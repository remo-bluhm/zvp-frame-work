<?php

/** 
 * @author Remo Bluhm
 * 
 * so
 */

class Action {
	
	const TAG_ACTION = "action";
	const TAG_ALIAS = "alias";
	const TAG_NAME = "name";
	
	private $alias = NULL;
	private $name = NULL;
	private $params = array ();
	
	function __construct($ActionName, $Params = array(), $Alias = NULL) {
		$this->name = $ActionName;
		
		if ($Alias === NULL) {
			$this->alias = $ActionName;
		} else {
			$this->alias = $Alias;
		}
		
		if (is_array ( $Params ) && count ( $Params ) > 0) {
			foreach ( $Params as $Param ) {
				
				$this->setParams ( $Param );
			
			}
		}
	
	}
	
	public function setDataArray(array $action) {
		
		if (array_key_exists ( self::TAG_ALIAS, $action )) {
			$this->setAlias ( $action [self::TAG_ALIAS] );
		}
		
		require_once 'citro/service-class/Param.php';
		if (array_key_exists ( Param::TAG_PARAM, $action )) {
			
			$this->setParams ( $action [Param::TAG_PARAM] );
			
			// $this->actions[] = $action;
		}
	
	}
	
	public function setAlias($aliasName) {
		if (is_string ( $aliasName )) {
			// $pattern = '/^[[:alpha:]-]{4,20}$/';
			// $pattern = '/^[[:A-Za-z:]-]{4,20}$/';
			$this->alias = $aliasName;
			return TRUE;
		}
		
		return FALSE;
	}
	
	/**
	 * Der AliasName
	 * 
	 * @return string
	 */
	public function getAliasName() {
		return $this->alias;
	}
	
	/**
	 * Der Name der Action
	 * 
	 * @return string
	 */
	public function getName() {
		return  trim($this->name);
	}
	
	/**
	 * Schreibt ein Array vorn Parametern ein mögliche versionen sind
	 * 1.als Liste von ParameterOjbekten array(new Param($name,$Value), new
	 * Param($name,$Value))
	 * 2 oder als Dictionary array($name=>$Value, $name=>$Value)
	 *
	 * @param $params array       	
	 * @return Action ein verweiß auf sich selbst
	 */
	public function setParams(array $params) {
		
		require_once 'citro/service-class/Param.php';
		
		foreach ( $params as $paramName => $paramValue ) {
			
			/*
			 * @var $paramValue Param
			 */
			if (is_a ( $paramValue, "Param" )) {
				$this->params [$paramValue->getName ()] = $paramValue;
			} else {
				$param = new Param ( $paramName, $paramValue );
				$this->params [$param->getName ()] = $param;
			}
		
		}
		
		return $this;
	}
	
	public function setParam($paramName,$paramValue){
		$param = new Param ( $paramName, $paramValue );
		$this->params [$param->getName ()] = $param;
	}
	
	/**
	 * Giebt den Nachgefragten Parameter zurück
	 * 
	 * @param $name string
	 *       	 des Paramern in der
	 * @return Param NULL
	 */
	public function getParam($name) {
		
		if (array_key_exists ( $name, $this->params )) {
			/*
			 * @var $action Action
			 */
			$param = $this->params [$name];
			return $param;
		}
		
		return NULL;
	}
	
	/**
	 * Giebt alle Parameter zurück
	 * 
	 * @return array von Parameterobjekten
	 */
	public function getParams() {
		return $this->params;
	}

}

?>