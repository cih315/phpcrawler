<?php 
/**
 *
 * 加载文件类 以用户自定义优先加载 
 *
 * @author wangleiming<ray_phper@63.com>
 **/ 

class Loader{

	/**
	 * 加载配置
	 *
	 * @access publis static 
	 * @param  string   $name server 或者 subpath.server
	 * @param  string   $ext  扩展名
	 * @return array
	 **/ 

	static function load_config($name, $ext = 'php'){
		static $_config = array();	
		if(!isset($_config[$name])){
			$sub = str_replace('.', '/', $name) . '.' . $ext;
			if(file_exists(($file = CLIENT_PATH . 'config/' . $sub))){
				$config = include($file);
				$_config[$name] = $config;
			}elseif(file_exists(($file = CONFIG_PATH . $sub))){
				$config = include($file);
				$_config[$name] = $config;
			}else{
				exit("load config file {$name} failed");
			}
		}
		return $_config[$name];
	}

	/**
	 * 加载文件 
	 *
	 * @access public static 
	 * @param  string $name  server.crawer_server 
	 * @param  string $ext   扩展名 
	 * @return void 
	 **/ 

	static function load($name, $ext  = 'php'){
		$sub = str_replace('.', '/', $name) . '.' . $ext;
		if(file_exists(($file = CLIENT_PATH . $sub))){
			include_once($file);
		}elseif(file_exists(($file = CORE_PATH . $sub))){
			include_once($file);
		}else{
			exit("load config file {$name} failed");
		}
	}	

}
