<?php 
class QQParse{

	public function parse($parser, $data, $ext = array()){
		$content = file_get_contents($data[0]);
		$base    = preg_match('/[a-zA-Z0-9]+\.[a-zA-Z0-9\/\.]*/i', $data[0], $match);
		$base    = explode('/', $match[0]);
		$total   = count($base);
		if($total > 1){
			unset($base[$total - 1]);
		}
		switch($total){
			case 2:
				$this->parse_index($parser, $content, $base);
                break;
            case 3:
                $this->parse_cate($parser, $content, $base);
                break;
            default :
                $this->parse_cate($parser, $content, $base);
                return;
		}
        return ;
	}

	public function parse_index($parser, $content, $base){
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
				//echo $url . "\n";
				pushToCrawl($url);
			}
		}
	
	}


    public function parse_cate($parser, $content, $base){
		$dom = $parser->engine->init($content);
        $xpath = "/html/body/div[3]/div[3]/div[1]/div[2]/a"; 
        $res = $dom->find($xpath);
        $base_url = 'http://' . implode('/', $base);
        $base_url_1 = 'http://' . $base[0];        
        $xpath_1 = "//*[@id=\"list1\"]/li";
        $content = $dom->find($xpath_1);
		if($res){
			$pattern = '/href=\"([^\"]+)\"/i';
			foreach($res as $value){
				preg_match($pattern, $value, $mat);	
				if(isset($mat[1])){
                    if(strpos($mat[1], '/') === 0){
                        $url =  trim($base_url_1, '/') . '/' . trim($mat[1], '/');
                    }else{
					    $url = trim($base_url, '/') . '/' . trim($mat[1], '/');
                    }
				}
                if(strpos($url, '.htm')){
                    $end = strripos($url, '/');
                    $int = substr($url, $end+1, -4);
                    if(intval($int) > 5){
                        break;
                    }
                }
				pushToCrawl($url);
			}
		}

        if($content){
            foreach($content as $value){
                $value = trim(strip_tags($value));
                $time = time();
                $value = mb_substr($value, 0, strlen($value), 'utf-8');

                $cate_id = 0;
                $sql = "select id from qq_cates where cate_mark = '{$base[1]}'";
                $cate = my_mysql_query($sql);
                if($cate){
                    $cate_id = $cate[0]['id']; 
                }
                $sub_sql = $cate_id ? ' and cate_id = ' . $cate_id : '';
                $sql = "select id from qq_sign where sign_title = '{$value}'" . $sub_sql;
                $res = my_mysql_query($sql);
                if($res){
                    return; 
                }
                $sql = "insert into qq_sign (sign_title, cate_id, created_time, updated_time) values('{$value}', {$cate_id}, {$time}, {$time})";
                $id = my_mysql_insert($sql);
                echo $id . "\n";
            }
        
        }

    }

}
