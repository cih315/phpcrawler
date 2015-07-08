<?php 
global $crawler_monitor, $parser_monitor, $redis, $crawler_server, $parser_server, $crawler_topic, $parser_topic;
if(!$sites){
    return false;
}

$sites = is_array($sites) ? $sites : array($sites);
init_redis();
$all = array();
foreach($sites as $site){
    preg_match('/http:\/\/[^\/]+[\/]?/i', $site, $match);
    if(!$match){
        return false;	
    }
    $key = md5($match[0]);
    $redis->lpush($key, $site);
    array_push($all, $key);
}
$crawler_topic = 'phpcrawlerkeys';
$parser_topic  = 'phpparserkeys';
$redis->set($crawler_topic, serialize($all), 7*24*3600);
$redis->set($parser_topic, serialize($all), 7*24*3600);

function init_redis(){
    global $redis;
    $config = load_config('redis');
    $redis  = new Redis();
    $redis->connect($config['host'], $config['port']);
}

init_monitor();
function crawler_monitor(swoole_process $worker){
    global $redis, $crawler_server, $crawler_topic;
    while(true){
        $keys = $redis->get($crawler_topic);
        $keys = unserialize($keys);
        if(!$keys){
            break;
        }
        foreach($keys as $key){
            if($url = $redis->rpop($key)){
                $config = load_config('site');
                $data = array(
                    'class' => $config[$key]['crawler'] . 'Crawl',
                    'method' => 'callback',
                    'data'   => array($url), 
                );
                $crawler_server->task($data, 0);
            }
        }
        sleep(5);
    }
}

function parser_monitor(swoole_process $worker){
    global $redis, $parser_server, $parser_topic;
    while(true){
        $keys = $redis->get($parser_topic);
        foreach($keys as $key){
            if($url = $redis->rpop($key)){
                $config = load_config('site');
                $data = array(
                    'class' => $config[$key]['parser'] . 'Parse',
                    'method' => 'callback',
                    'data'   => $url, 
                );
                $parser_server->task($data, 0);
            }
        }
        sleep(5);
    }
}
function init_monitor(){
    global $crawler_monitor, $parser_monitor;
    for($i = 0; $i < 2; $i++){
        $crawler_monitor  = new swoole_process('crawler_monitor', false);    
        $parser_monitor = new swoole_process('parser_monitor', false);    
    }
}
