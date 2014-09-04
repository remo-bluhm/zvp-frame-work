<?php

class ServiceResponce {
	
	protected $ResponceActionsA; // enth�llt die Zur�ckzugebende Array kann auch
	                             // ein Dictionery sein
	protected $ResponceParamA;
	protected $ResponceErrorA;
	
	/**
	 *
	 * @return the $ResponceActionsA
	 */
	public function getResponceActionsA() {
		return $this->ResponceActionsA;
	}
	
	/**
	 *
	 * @return the $ResponceParamA
	 */
	public function getResponceParamA() {
		return $this->ResponceParamA;
	}
	
	/**
	 *
	 * @return the $ResponceErrorA
	 */
	public function getResponceErrorA() {
		return $this->ResponceErrorA;
	}
	
	/**
	 *
	 * @param $ResponceActionsA field_type       	
	 */
	public function setResponceActionsA($ResponceActionsA) {
		$this->ResponceActionsA = $ResponceActionsA;
	}
	
	/**
	 *
	 * @param $ResponceParamA field_type       	
	 */
	public function setResponceParamA($ResponceParamA) {
		$this->ResponceParamA = $ResponceParamA;
	}
	
	/**
	 *
	 * @param $ResponceErrorA field_type       	
	 */
	public function setResponceErrorA($ResponceErrorA) {
		$this->ResponceErrorA = $ResponceErrorA;
	}

}

?>