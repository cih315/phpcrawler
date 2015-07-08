<?php 
load('crawl.crawl');
class snoopyCrawl extends crawl{

	protected static $_crawlerName = null;
	public static function setCrawler($crawlerName = 'snoopy'){
		self::$_crawlerName = $crawlerName;	
	}

	public static function getCrawler(){
		return self::$_crawlerName ? self::$_crawlerName : 'snoopy';
	}

	public static function fetch($data, $ext = array()){
		$ext['class'] = 'snoopyCrawl';
		foreach($data as $value){
			self::asyncFetch('fetch', array($value), $ext);	
		}
	}

	public static function callbackFetch($data, $ext = array()){
		parent::fetch($data, $ext);		
	}
}
