<?php 
abstract class Driver{

	protected $storage = null;

	public function __construct(Storage $storage){
		$this->storage = $storage;
	}

	abstract function fetch($data, $ext = array());
}
