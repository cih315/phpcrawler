<?php 

/**
 * 检测目录是否存在，如不存在则创建
 *
 * @param  string $path 目录 
 * @param  int     $mode  目录权限
 * @return boolean
 **/ 

if(!function_exists('check_path')){
	function check_path($path, $mode = 0755){
		if(!is_dir($path)){
			check_path(dirname($path), $mode);
		}
		return @mkdir($path, $mode);
	}
}
