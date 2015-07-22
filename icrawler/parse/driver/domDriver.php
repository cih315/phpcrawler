<?php 
Loader::load('parse.driver.driver');
Loader::load('lib.parser.dom');
Loader::load('lib.storage.filestorage');

class domDriver extends Driver{

	public $engine = null;

	public function __construct(){
		parent::__construct(new fileStorage());
		$this->engine = new DOM();
	}

	public function fetch($data, $ext = array()){
		$hooks = Loader::load_config('hooks');
		if($hooks && $hooks['parse']){
			Loader::load('parse.' . strtolower($hooks['parse']['class']));
			$obj = new $hooks['parse']['class'];	
			$args = array($this, $data, $ext);
			call_user_func_array(array($obj, $hooks['parse']['method']), $args);
		}
	}

	public function find($data, $ext = array()){
	
	}
}
