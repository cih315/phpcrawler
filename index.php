<?php 
define('APP_PATH', __DIR__);
include APP_PATH . '/constants.php';
include APP_PATH . '/loader.php';
load_config('site');
include APP_PATH . '/monitor.php';
load_server('crawler_server');
