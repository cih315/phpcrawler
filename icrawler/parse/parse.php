<?php 
class Parse{

	protected static $_driver = null;

	public static function init($driver){
		Loader::load('parse.driver.' . strtolower($driver) . 'Driver');			
		$class   = ucfirst($driver)  . 'Driver';
		self::$_driver = new $class;
	}	

	public static function fetch($path, $ext = array()){
		$driver = isset($ext['driver']) && $ext['driver'] ? $ext['driver'] : 'dom';
		self::init($driver);
		self::$_driver->fetch($path, $ext);	
	} 

}
