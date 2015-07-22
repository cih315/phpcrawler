<?php 
Loader::load('lib.storage.storage');
class MysqlStorage extends Storage{

	protected $db = null;

	public function __construct(){
		$db = Loader::load_config('db');	
		$this->db = $this->init($db);
	}

	public function init($config = array()){
		$config = empty($config) ? Loader::load_config('db') : $config;
		if(empty($config)){
			exit('db config is empty');
		}
		static $_db = null;
		if($_db){
			return $_db;
		}
		($_db = mysql_connect($config['host'], $config['port'])) or die('Could not connect to mysql server.');
		mysql_select_db($config['dbname'], $_db) or die('Could not select database.');
		mysql_query("SET NAMES {$config['charset']}");
		return $_db;
	}

	public function query($sql){
		if(!$this->db){
			$this->init();
		}
		$sql = mysql_real_escape_string($sql);
		return  $this->db->query($sql);
	}

}
