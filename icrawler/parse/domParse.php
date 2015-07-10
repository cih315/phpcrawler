<?php 
Loader::load('parse.parse');
class domParse extends Parse{
	protected static $_parserName = null;

	public static function setParser($parserName = 'dom'){
		self::$_parserName = $parserName;
	}

	public static function getParser(){
		return self::$_parserName ? self::$_parserName : 'dom';
	}

	public static function run($data, $ext = array()){
		$ext['class'] = 'domParse';
		return self::asyncParse('parse', $data, $ext);		
	}

	public static function callbackParse($data, $ext = array()){
		return parent::run($data, $ext);	
	}

}
