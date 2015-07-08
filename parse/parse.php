<?php 
abstract class Parse{

	protected static $_parser = null;

	public static function init(){
		$parser = static::getParser();
		load('crawl.parser.' . $parser . 'parser');			
		$class   = ucfirst($parser)  . 'parser';
		self::$_parser = new $class;
	}	

	public static function run($data, $ext = array()){
		self::init();
		self::$_parser->run($data, $ext);	
	} 

	public static function asyncParse($cmd = 'fetch', $data, $ext = array()){
		$config = load_config('server');
		$client = new swoole_client(SWOOLE_SOCK_TCP);
		$client->connect($config['parser']['host'], $config['parser']['port']);
		$send = array(
			'cmd'      => $cmd ? $cmd : 'parse',
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
	abstract static function setParser($parser = '');
	abstract static function getParser();
	abstract static function callbackParse($data, $ext = array());
}
