<?php 
$redis = new Redis();
$redis->connect('127.0.0.1', '6379');
$k = $redis->rpush(md5('http://www.smartlei.com'), 'http://www.smartlei.com/article/list/5');
var_dump($v);
