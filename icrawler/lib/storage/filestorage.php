<?php 
Loader::load('lib.storage.storage');

class FileStorage extends Storage{

	public function save($url, $data, $level = 1){
		if(!$url || !$data){
			return false;
		}
        $url = trim($url, '/');
        $first = stripos($url, '/');
        $end   = strripos($url, '/'); 
        $tmp_url = $url;
        $last = 'index';
        if(!($first == $end || $first == ($end-1))){
            $last = strrchr($url, '/');
            if($last && strpos($last, '.')){
                $tmp_url = substr($url, 0, $end);
            }
        }
		preg_match('/http:\/\/([^\/]+)[\/]?([a-zA-Z0-9\/]*)/i', $tmp_url, $match);
		if(!$match){
			return false;	
		}
		$store = Loader::load_config('store');
		$sub   = isset($match[2]) && $match[2] ? $match[2] : '';
		$sub   = $sub ? trim($sub, '/') . '/' : '';
		$path  = $store['save_path'] . trim($match[1], '/') . '/' . $sub;	
		$file  = $path . trim($last, '/'); 
		$content = is_object($data) ? $data->results : $data;
		check_path($path, 0777);
		@file_put_contents($file, serialize($content));

		//务必调用否则不能触发 解析程序
		
		echo 'push -------parse---'  .$file . "\n";
		pushToParse($url, $file);
	}

}
