<?php

//require_once 'citro/Trash/TrashAdressAbstract';

class TrashAdress { //extends TrashAdressAbstract {
	
	private $_objekt = NULL;
	private $_method = NULL;
	
	private $_userId = NULL;
	private $_groupIds = NULL;
	private $_type = "NO_SET";
	
	function __construct($objekt, $method) {
		$this->_objekt = $objekt;
		$this->_method = $method;
	
	}
	
	public function getType() {
		return $this->_type;
	}
	
	public function setType($type) {
		$this->_type = $type;
	}
	
	public function getObjekt() {
		return $this->_objekt;
	}
	
	public function getMethode() {
		return $this->_method;
	}
}

?>