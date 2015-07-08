<?php 
if(!function_exists('check_path')){
	function check_path($path, $mode = 0755){
		if(!is_dir($path)){
			check_path(dirname($path), $mode);
		}
		return @mkdir($path, $mode);
	}
}
