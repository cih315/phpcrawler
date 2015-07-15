<?php 
class QQParse{

	public function parse($parser, $data, $ext = array()){
		$content = file_get_contents($data[0]);

		$base = preg_match('/[a-zA-Z0-9]+\.[a-zA-Z0-9\/\.]*/i', $data[0], $match);
		$base = explode('/', $match[0]);
		$total = count($base);
		if($total > 1){
			unset($base[count($base) - 1]);
		}
		switch($total){
			case 2:
				$th

		}

		
		
	}

	public function parse_cate($parser, $content){
		$dom = $parser->engine->init($content);
		$pattern = '/html/body/div[3]/div[2]/div/a';
		$res = $dom->find($pattern);
		$base = 'http://' . implode('/', $base);		
		if($res){
			$pattern = '/href=\"([^\"]+)\"/i';
			foreach($res as $value){
				preg_match($pattern, $value, $mat);	
				if(isset($mat[1])){
					$url = trim($base, '/') . '/' . trim($mat[1], '/');
				}
				echo $url . "\n";
				pushToCrawl($url);
			}
		}
	
	}

}
