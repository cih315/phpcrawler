<?php 

//抓取server 

load('crawl.snoopyCrawl');

global $redis, $server;
$config = load_config('server');
$server = new swoole_server($config['crawler']['host'], $config['crawler']['port']);
if(!$server){
	exit('crawler_server connect failed');	
}
$server->set($config['crawler']['options']);

$server->on('start', function(swoole_server $server){
	echo 'crawler_server start_time--' . date('Y-m-d H:i:s') . "\n";
	echo "master_pid:{$server->master_pid}--manager_pid:{$server->manager_pid}\n";	
	echo 'version--[' . SWOOLE_VERSION . "]\n";	
});

$server->on('workerStart', function(swoole_server $server, $worker_id){
	global $argv;
	if($worker_id >= $server->setting['worker_num']) {
		swoole_set_process_name("php {$argv[0]} task worker");
	} else {
		swoole_set_process_name("php {$argv[0]} event worker");
	}
	echo 'workerStart time--' . date('Y-m-d H:i:s') . "\n";
	echo 'worker_id:' . $worker_id . "\n";
});

$server->on('connect', function(swoole_server $server, $fd, $from_id){
	echo 'client connect time--' . date('Y-m-d H:i:s') . "\n";
	echo 'client fd:' . $fd . "\n";
	echo 'client from_id:' . $from_id . "\n";
});


$server->on('finish', function(swoole_server $server, $data){
	echo 'async_task finish time--' . date('Y-m-d H:i:s') . "\n";
	echo 'connect_pid:' . posix_getpid().".\n";
});

$server->on('close', function(swoole_server $server, $fd, $from_id){
	echo 'client close time--' . date('Y-m-d H:i:s') . "\n";
	echo 'client fd:' . $fd . "\n";
	echo 'client from_id:' . $from_id . "\n";
});

$server->on('workerStop', function(swoole_server $server, $worker_id){
	echo 'workerStop time--' . date('Y-m-d H:i:s') . "\n";
	echo 'worker_id:' . $worker_id . "\n";
	echo 'pid:' . posix_getpid().".\n";
});

$server->on('workerError', function(swoole_server $server, $data){
	echo 'workerError time--' . date('Y-m-d H:i:s') . "\n";
	echo 'pid:' . posix_getpid().".\n";
});

$server->on('shutdown', function(swoole_server $server){
	echo 'server shutdown time--' . date('Y-m-d H:i:s') . "\n";
	echo 'server_pid:' . posix_getpid().".\n";
});

$server->on('receive', function(swoole_server $server, $fd, $from_id, $data){
	echo 'server receive time--' . date('Y-m-d H:i:s') . "\n";
	echo 'client fd:' . $fd . "\n";
	echo 'client--from_id:' . $from_id . "\n";
	$data = json_decode($data, true);
	$cmd = $data['cmd'];
	unset($data['cmd']);
	switch($cmd){
		case 'fetch':
			$server->task($data, 0);
			$server->send($fd, "OK\n");
			break;
		default:
			echo "error cmd \n";
	}
});

$server->on('task', function(swoole_server $server, $task_id, $from_id, $data){
	echo 'task start time--' . date('Y-m-d H:i:s') . "\n";
	echo 'tast_id :' . $task_id. "\n";
	echo 'client--from_id:' . $from_id . "\n";
	$class  = isset($data['class'])   ?  $data['class'] : '';
	$method = isset($data['method'])  ?  $data['method'] : '';
	$object = isset($data['object'])  ?  unserialize($data['object']) : '';
	if((!$class && !$object) || !$method){
		$server->finish("error callback\n");	
	}
	if($object){
		call_user_func_array(array($object, $method), array($data['data']));
	}else{
		call_user_func_array("{$class}::{$method}", array($data['data']));
	}
	$server->finish("OK\n");	
});
$process = new swoole_process('monitor', false);
$redis = new Redis();
$redis->connect('127.0.0.1', '6379');
function monitor(swoole_process $worker){		
	global $redis, $server;
	while(true){	
		$value = $redis->lpop('key1');
		if($value){
			$data = array(
				'class' => 'snoopyCrawl',	
				'method' => 'callback',	
				'data'   => array('http://www.tuicool.com/articles/a2EJ3iQ'),
			);
			$server->task($data);
			echo $value . "\n";
		}
		sleep(5);
	}
}
$server->addProcess($process);
$server->start();
