<?php 
Loader::load('lib.storage.storage');

class FileStorage extends Storage{

	public function save($url, $data, $level = 1){
		if(!$url || !$data){
			return false;
		}
		preg_match('/http:\/\/([^\/]+)[\/]?/i', $url, $match);
		if(!$match){
			return false;	
		}
		$store = Loader::load_config('store');
		$sub   = intval($level) <= 1 ? 'v1' : 'v' . intval($level);
		$path  = $store['save_path'] . trim($match[1], '/') . '/' . $sub . '/';	
		$file  = $path . md5($url); 
		$content = is_object($data) ? $data->results : $data;
		check_path($path, 0777);
		@file_put_contents($file, serialize($content));

		//务必调用否则不能触发 解析程序
		echo 'push -------parse' . "\n";
		pushToParse($url, $file);
	}

}
