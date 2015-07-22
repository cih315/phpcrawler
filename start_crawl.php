<?php 
global $start;
$start = true;
include 'constants.php';
include CORE_PATH. 'Loader.php';
Loader::load('helper.Dom');
Loader::load('helper.Dir');
Loader::load('Monitor');
Loader::load('server.CrawlerServer');
