<?php 
abstract class Crawl{

	protected static $_crawler = null;

	public static function init(){
		$crawler = static::getCrawler();
		Loader::load('crawl.crawler.' . $crawler . 'Crawler');			
		$class   = ucfirst($crawler)  . 'Crawler';
		self::$_crawler = new $class;
	}	

	public static function fetch($data, $ext = array()){
		self::init();
		self::$_crawler->fetch($data, $ext);	
	} 

	public static function asyncFetch($cmd = 'fetch', $data, $ext = array()){
		$config = Loader::load_config('server');
		$client = new swoole_client(SWOOLE_SOCK_TCP);
		$client->connect($config['crawler']['host'], $config['crawler']['port']);
		$send = array(
			'cmd'      => $cmd ? $cmd : 'fetch',
			'class'    => isset($ext['class'])  ? $ext['class'] : get_class($this), 
			'method'   => isset($ext['method']) ? $ext['method'] : 'callback',
			'data'     => $data,
		);
		$client->send(json_encode($send));
		var_dump($client->recv());
	}
	
	public static function callback($data, $ext = array()){
		static::callbackFetch($data, $ext);
	}
	abstract static function setCrawler($crawler = '');
	abstract static function getCrawler();
	abstract static function callbackFetch($data, $ext = array());
}
