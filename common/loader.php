<?php 
function load_config($name){
	static $_loaded = array();
	if(!isset($_loaded[$name])){
		include_once(APP_PATH . '/config/' . $name . '.php');
		$_loaded[$name] = $config;
	}
	return $_loaded[$name];
}

function load_helper($name){
	$file = APP_PATH . '/helper/' . $name . '_helper.php';		
	if(file_exists($file)){
		include_once($file);	
	}else{
		exit($file . 'not found');
	}
}

function load($file, $ext = '.php'){
	$file = APP_PATH . '/' . str_replace('.', '/', $file) . $ext;		
	if(file_exists($file)){
		include_once($file);	
	}else{
		exit($file . 'not found');
	}
}
