<?php 
abstract class Parser{

	protected $storage = null;

	public function __construct(Storage $storage){
		$this->storage = $storage;
	}

	abstract function find($data, $ext = array());
}
