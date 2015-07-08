<?php 
function load_config($name){
	static $_loaded = array();
	if(!isset($_loaded[$name])){
		include(APP_PATH . '/config/' . $name . '.php');
		$_loaded[$name] = $config;
	}
	return $_loaded[$name];
}

function load_server($name){
	include(APP_PATH . '/server/' . $name . '.php');
}

function load($file, $ext = '.php'){
	$file = APP_PATH . '/' . str_replace('.', '/', $file) . $ext;		
	if(file_exists($file)){
		include($file);	
	}else{
		exit($file . 'not found');
	}
}
