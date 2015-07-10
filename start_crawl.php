<?php 
global $start;
$start = true;
include 'constants.php';
include CORE_PATH. 'common/loader.php';
Loader::load('helper.dom');
Loader::load('helper.dir');
Loader::load('common.monitor');
Loader::load('server.crawler_server');
