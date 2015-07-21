<?php 

//抓取server 

Loader::load('crawl.snoopyCrawl');

//初始化一些全局变量 
global $redis, $crawler_server, $crawler_monitor;

$config         = Loader::load_config('server');

$crawler_server = new swoole_server($config['crawler']['host'], $config['crawler']['port']);

if(!$crawler_server){
	exit('crawler_server connect failed');	
}
$crawler_server->set($config['crawler']['options']);

//绑定一些事件及相应的回调函数

$crawler_server->on('start', function(swoole_server $crawler_server){
	echo 'crawler_server start_time--' . date('Y-m-d H:i:s') . "\n";
	echo "master_pid:{$crawler_server->master_pid}--manager_pid:{$crawler_server->manager_pid}\n";	
	echo 'version--[' . SWOOLE_VERSION . "]\n";	
});

$crawler_server->on('workerStart', function(swoole_server $crawler_server, $worker_id){
	global $argv;
	if($worker_id >= $crawler_server->setting['worker_num']) {
		swoole_set_process_name("php {$argv[0]} task worker");
	} else {
		swoole_set_process_name("php {$argv[0]} event worker");
	}
	echo 'workerStart time--' . date('Y-m-d H:i:s') . "\n";
	echo 'worker_id:' . $worker_id . "\n";
});

$crawler_server->on('connect', function(swoole_server $crawler_server, $fd, $from_id){
	echo 'client connect time--' . date('Y-m-d H:i:s') . "\n";
	echo 'client fd:' . $fd . "\n";
	echo 'client from_id:' . $from_id . "\n";
});


$crawler_server->on('finish', function(swoole_server $crawler_server, $data){
	echo 'async_task finish time--' . date('Y-m-d H:i:s') . "\n";
	echo 'connect_pid:' . posix_getpid().".\n";
});

$crawler_server->on('close', function(swoole_server $crawler_server, $fd, $from_id){
	echo 'client close time--' . date('Y-m-d H:i:s') . "\n";
	echo 'client fd:' . $fd . "\n";
	echo 'client from_id:' . $from_id . "\n";
});

$crawler_server->on('workerStop', function(swoole_server $crawler_server, $worker_id){
	echo 'workerStop time--' . date('Y-m-d H:i:s') . "\n";
	echo 'worker_id:' . $worker_id . "\n";
	echo 'pid:' . posix_getpid().".\n";
});

$crawler_server->on('workerError', function(swoole_server $crawler_server, $data){
	echo 'workerError time--' . date('Y-m-d H:i:s') . "\n";
	echo 'pid:' . posix_getpid().".\n";
});

$crawler_server->on('shutdown', function(swoole_server $crawler_server){
	echo 'server shutdown time--' . date('Y-m-d H:i:s') . "\n";
	echo 'server_pid:' . posix_getpid().".\n";
});

$crawler_server->on('receive', function(swoole_server $crawler_server, $fd, $from_id, $data){
	echo 'server receive time--' . date('Y-m-d H:i:s') . "\n";
	echo 'client fd:' . $fd . "\n";
	echo 'client--from_id:' . $from_id . "\n";
	$data = json_decode($data, true);
	$cmd  = $data['cmd'];
	unset($data['cmd']);
	switch($cmd){
	case 'fetch':
		$crawler_server->task($data, 0);
		$crawler_server->send($fd, "OK\n");
		break;
	default:
		echo "error cmd \n";
	}
});

$crawler_server->on('task', function(swoole_server $crawler_server, $task_id, $from_id, $data){
	echo 'task start time--' . date('Y-m-d H:i:s') . "\n";
	echo 'tast_id :' . $task_id. "\n";
	echo 'client--from_id:' . $from_id . "\n";
	$class  = isset($data['class'])   ?  $data['class'] : '';
	$method = isset($data['method'])  ?  $data['method'] : '';
	$object = isset($data['object'])  ?  unserialize($data['object']) : '';
	if((!$class && !$object) || !$method){
		$crawler_server->finish("error callback\n");	
	}
	if($object){
		call_user_func_array(array($object, $method), array($data['data']));
	}else{
		call_user_func_array("{$class}::{$method}", array($data['data']));
	}
	$crawler_server->finish("OK\n");	
});

//添加监控进程 

$crawler_server->addProcess($crawler_monitor);

//server开始
$crawler_server->start();
