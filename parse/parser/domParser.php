<?php 
load('parse.parser.parser');
load('lib.parser.dom');
load('lib.storage.mysqlStorage');

class domParser extends Parser{

	private $engine = null;

	public function __construct(){
		parent::__construct(new MysqlStorage());
		$this->engine = new DOM();
	}

	public function run($data, $pattern, $ext = array()){
		$dom = $this->engine->init($tmp);	
		$res = $dom->find($pattern);
		if($res){
			foreach($res as $value){
					
			}
		}	
	}
}
