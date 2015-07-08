<?php 
define('APP_PATH', __DIR__);
include APP_PATH . '/constants.php';
include APP_PATH . '/loader.php';
load('crawl.snoopyCrawl');
snoopyCrawl::fetch(array('http://www.tuicool.com/articles/FJV3uyz', 'http://www.tuicool.com/a/', 'http://www.tuicool.com/articles/NFru2u'));
