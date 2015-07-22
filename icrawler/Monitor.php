<?php 
/**
 * 监控或者调度程序
 **/ 

global $crawler_monitor, $parser_monitor, $redis, $crawler_server, $parser_server, 
	   $crawler_topic, $parser_topic, $sites, $start;

Loader::load_config('site');
if(!$sites){
	return false;
}

//用户定义的要抓取的网站地址
//	config/site.php

$sites = is_array($sites) ? $sites : array($sites);

//初始化redis 
init_redis();


$all_1 = $all_2 = array();

//把要抓取的网站添加的要监控的队列
foreach($sites as $site){
	preg_match('/http:\/\/[^\/]+[\/]?/i', $site, $match);
	if(!$match){
		return false;	
	}
	$key = trim($match[0], '/');
	$key = md5($key);
	if($start){
		$redis->lpush($key, $site);
	}
	array_push($all_1, $key);
	array_push($all_2, md5($key));
}
$crawler_topic = 'phpcrawlerkeys';
$parser_topic  = 'phpparserkeys';
$redis->set($crawler_topic, serialize($all_1), 7*24*3600);
$redis->set($parser_topic, serialize($all_2), 7*24*3600);

function init_redis(){
	global $redis;
	$config = Loader::load_config('redis');
	$redis  = new Redis();
	$redis->connect($config['host'], $config['port']);
}

/**
 * 抓取服务的监控进程的回调函数
 **/ 

function crawler_monitor(swoole_process $worker){
	global $redis, $crawler_server, $crawler_topic;

	//无线循环，如果在相应的队列拿到数据就执行抓取工作
	while(true){
		$keys = $redis->get($crawler_topic);
		if(!$keys){
			continue;
		}
		$keys = unserialize($keys);
		foreach($keys as $key){
			if($url = $redis->rpop($key)){
				echo 'crawler-----' . $url . "\n";
				$config = Loader::load_config('site');
				$data = array(
					'driver' => $config[$key]['crawler'],
					'data'   => $url, 
				);
				$crawler_server->task($data, 0);
			}
		}
		sleep(5);
	}
}

/**
 * 解析服务的监控进程的回调函数 
 **/ 

function parser_monitor(swoole_process $worker){
	global $redis, $parser_server, $parser_topic;

	//无线循环，如果在相应的队列拿到数据就执行分析工作
	while(true){
		$keys = $redis->get($parser_topic);
		if(!$keys){
			continue;
		}
		$keys = unserialize($keys);
		foreach($keys as $key){
			if($path = $redis->rpop($key)){
				echo 'parser-----------' . $path. "\n";
				$config = Loader::load_config('site');
				$data = array(
					'driver' => $config[$key]['parser'],
					'data'   => array($path), 
				);
				$parser_server->task($data, 0);
            }else{
                var_dump($path);
            }
		}
		sleep(5);
	}
}

//创建监控程序
for($i = 0; $i < 2; $i++){
	$crawler_monitor  = new swoole_process('crawler_monitor', false);    
	$parser_monitor = new swoole_process('parser_monitor', false);    
}

/**
 * 把生成的文件信息push到parser中相应的队列，触发解析程序 
 *
 * @param $url  抓取到的url 
 * @param $path 文件存储的位置 
 **/
function pushToParse($url, $path){
	global $redis; 
	preg_match('/http:\/\/[^\/]+[\/]?/i', $url, $match);
	$key = trim($match[0], '/'); 
	$key = md5(md5($key));

    $key_exist = 'hsetparse';
    if($redis->hget($key_exist, md5($url))){
        return; 
    }
    $redis->hset($key_exist, md5($url), 1);
	$redis->lpush($key, $path);
}

/**
 * 把从文件中解析出来的url放到抓取队列中
 *
 * @param $key 解析出的的url 
 **/
function pushToCrawl($url){
	global $redis; 
	preg_match('/http:\/\/[^\/]+[\/]?/i', $url, $match);
	$key = trim($match[0], '/');
	$key = md5($key);
    $key_exist = 'hsetcrawl';
    if($redis->hget($key_exist, md5($url))){
        return; 
    }
	$redis->lpush($key, $url);
    $redis->hset($key_exist, md5($url), 1);
}
