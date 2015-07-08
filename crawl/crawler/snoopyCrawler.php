<?php 

load('crawl.crawler.crawler');
load('lib.crawler.snoopy');
load('lib.storage.filestorage');
class SnoopyCrawler extends Crawler{

	private $engine = null;
	public function __construct(){
		parent::__construct(new FileStorage());
		$this->engine = new Snoopy(); 
	}

	public function fetch($urls, $option = array(), $ext = array()){
		foreach($urls as $url){
			if(!$url || !preg_match('/http[s]?:\/\/[[A-Za-z0-9_?.%&=\/#@!]*/i', $url)){
				continue;
			}	
			$this->engine->fetch($url);	
			if($this->engine && $this->engine->status == 200){
				$this->storage->save($url, $this->engine);	
			}
		}
	}
}
