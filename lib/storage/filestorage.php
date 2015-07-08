<?php 
load('lib.storage.storage');
load('common.function');
class FileStorage extends Storage{

	public function save($url, $data, $level = 1){
		if(!$url || !$data){
			return false;
		}
		$store = load_config('store');
		preg_match('/http:\/\/[^\/]+[\/]?/i', $url, $match);
		if(!$match){
			return false;	
		}
		$sub   = intval($level) <= 1 ? 'v1' : 'v' . intval($level);
		$path  = $store['save_path'] . md5($match[0]) . '/' . $sub . '/';	
		$file  = md5($url); 
		$content = is_object($data) ? $data->results : $data;
		check_path($path, 0777);
		@file_put_contents($path . $file, serialize($content));
	}

}
