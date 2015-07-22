<?php 

Loader::load('crawl.driver.Driver');
Loader::load('lib.crawler.Snoopy');
Loader::load('lib.storage.FileStorage');
class SnoopyDriver extends Driver{

	private $engine = null;
	public function __construct(){
		parent::__construct(new FileStorage());
		$this->engine = new Snoopy(); 
	}

	public function fetch($url, $option = array(), $ext = array()){
		if(!$url || !preg_match('/http[s]?:\/\/[[A-Za-z0-9_?.%&=\/#@!]*/i', $url)){
			continue;
		}	
		$this->engine->fetch($url);	
		if($this->engine && $this->engine->status == 200){
			$this->storage->save($url, $this->engine);	
		}
	}
}
