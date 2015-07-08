<?php 
$config['crawler'] = array(
	'host'    => '127.0.0.1',
	'port'    => '9501',
	'options' => array(
		'worker_num'      => 4,	
		'task_worker_num' => 4,
		'dispatch_mode' => 3,
		'daemonize '    => 0,
		'log_file'      => '',
	),
);


$config['parser'] = array(
	'host'    => '127.0.0.1',
	'port'    => '9502',
	'options' => array(
		'worker_num'      => 4,	
		'task_worker_num' => 4,
		'dispatch_mode' => 3,
		'daemonize '    => 0,
		'log_file'      => '',
	),
);

