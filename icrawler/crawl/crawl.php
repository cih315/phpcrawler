<?php 
/**
 * 抓取类 
 *
 **/ 

class Crawl{

	/**
	 * @protected static $_crawler
	 *
	 * 真正的抓取实例对象 
	 **/ 

	protected static $_driver = null;

	/**
	 * 初始化工作 
	 *
	 * @access public 
	 * @param  string $driver 抓取类的名字 
	 * @return void 
	 **/ 

	public static function init($driver){
		Loader::load('crawl.driver.' . strtolower($driver) . 'Driver');			
		$class   = ucfirst($driver)  . 'Driver';
		self::$_driver = new $class;
	}	


	/**
	 * 抓取数据
	 * swoole_server->task 函数中调取  
	 *
	 * @access public  static 
	 * @param  string  $url   要抓取的url 
	 * @param  array   $ext   扩展数据 
	 * @return void 
	 **/ 

	public static function fetch($url, $ext = array()){
		//默认抓取为snoopy
		$driver = isset($ext['driver']) && $ext['driver'] ? $ext['driver'] : 'snoopy';
		self::init($driver);
		self::$_driver->fetch($url, $ext);	
	} 
}
