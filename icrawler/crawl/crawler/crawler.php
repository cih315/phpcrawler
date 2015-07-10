<?php 
abstract class Crawler{

	protected $storage = null;

	public function __construct(Storage $storage){
		$this->storage = $storage;	
	}

	abstract public function fetch($url, $options = array(), $ext = array());

}
