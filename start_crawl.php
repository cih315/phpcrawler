<?php 
global $start;
$start = true;
define('APP_PATH', __DIR__);
include APP_PATH . '/common/constants.php';
include APP_PATH . '/common/loader.php';
load_helper('dom');
load_helper('dir');
load('common.monitor');
load('server.crawler_server');
