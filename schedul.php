<?php 
global $scheduler;
$scheduler = new swoole_process('schedul', false);

function crawler(){
	global $scheduler;
	$scheduler->write('parser');
	echo 22222 . "\n";
}
function parser(){
	echo 3333 . "\n";
}
function schedul(swoole_process $worker){
	$single = $worker->read();
	if($single == 'crawler'){
		crawler();
	}
	if($single == 'parser'){
		$work->write('crawler');
		parser();
	}
}
$scheduler->write('crawler');
$pid = $scheduler->start();
$scheduler->wait();


