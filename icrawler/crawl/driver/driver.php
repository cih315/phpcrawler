<?php 

/**
 * 规范抓取类,抓取驱动 
 *
 **/ 

abstract class Driver{

	protected $storage = null;

	public function __construct(Storage $storage){
		$this->storage = $storage;	
	}

	abstract public function fetch($url, $options = array(), $ext = array());

}
