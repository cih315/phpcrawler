<?php 
$redis = new Redis();
$redis->connect('127.0.0.1', '6379');
$k = $redis->rpush('key1', 1);
$k = $redis->rpush('key1', 2);
var_dump($v);
