<?php 
Loader::load('parse.parser.parser');
Loader::load('lib.parser.dom');
Loader::load('lib.storage.filestorage');

class domParser extends Parser{

	private $engine = null;

	public function __construct(){
		parent::__construct(new fileStorage());
		$this->engine = new DOM();
	}

	public function run($data, $ext = array()){
		$content = file_get_contents($data[0]);
		$dom = $this->engine->init($content);	
		$pattern = '//*[@id="c_left"]/div[2]/div[2]/h3/a';
		$res = $dom->find($pattern);
		if($res){
			pushToCrawl('http://www.smartlei.com/article/17');
		}
		if($res){
			foreach($res as $value){
					
			}
		}	
	}

	public function find($data, $ext = array()){
	
	}
}
