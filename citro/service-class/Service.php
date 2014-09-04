<?php

// Das ist der Service auf versionn 2

class Service {
	
	const TAG_SERVICE = "service"; // der KeyName der Responce
	const TAG_ACTIONS = "actions";
	const TAG_NAME = "name";
	const TAG_ALIAS = "alias";
	
	private $alias = NULL;
	private $name = NULL;
	private $params = array ();
	private $actions = array ();
	
	function __construct($ServiceName, $Params = array(), $Alias = NULL) {
		
		$this->name = $ServiceName;
		
		if (is_array ( $Params ) && count ( $Params ) > 0) {
			foreach ( $Params as $Param ) {
				
				$this->setParam ( $Param );
			
			}
		}
		
		if ($Alias === NULL) {
			$this->alias = $ServiceName;
		} else {
			$this->alias = $Alias;
		}
	
	}
	
	public function getName() {
		return $this->name;
	}
	
	public function setDataArray(array $service) {
		
		if (array_key_exists ( self::TAG_ALIAS, $service )) {
			$this->setAlias ( $service [self::TAG_ALIAS] );
		}
		
		$this->actions = array ();
		
		require_once 'citro/service-class/Action.php';
		
		if (array_key_exists ( Action::TAG_ACTION, $service )) {
			
			$actionOne = $service [Action::TAG_ACTION];
			
			$action = new Action ( $actionOne [Action::TAG_NAME] );
			$action->setDataArray ( $actionOne );
			// $service->setService($request[Service::TAG_SERVICE]);
			$this->actions [] = $action;
		}
		
		if (array_key_exists ( Action::TAG_ACTION . "s", $service )) {
			foreach ( $service [Action::TAG_ACTION . "s"] as $Key => $act ) {
				
				$action = new Action ( $act [Action::TAG_NAME] );
				
				$action->setDataArray ( $act );
				$this->actions [] = $action;
			}
		
		}
	
	}
	
	public function getAlias() {
		return $this->alias;
	}
	
	public function setAlias($aliasName) {
		if (is_string ( $aliasName )) {
			// $pattern = '/^[[:alpha:]-]{4,20}$/';
			// $pattern = '/^[[:A-Za-z:]-]{4,20}$/';
			$this->alias = $aliasName;
		}
	}
	
	/**
	 * Setzt ein Parameterobjekt
	 * 
	 * @param $Name string       	
	 * @param $Value unknown_type       	
	 * @param $Type string       	
	 */
	public function setParam($Name, $Value) {
		require_once 'citro/Param.php';
		$param = new Param ( $Name, $Value );
		$this->params [$Name] = $param;
	
	}
	
	/**
	 * Setzt ein Array von ParamObjekten für den Service
	 * 
	 * @return Params
	 */
	public function setParams(array $paramObj) {
		
		/*
		 * @var $paramObjekt Param
		 */
		foreach ( $paramObj as $paramObjekt ) {
			if (is_a ( $paramObjekt, "Param" )) {
				$this->params [$paramObjekt->getName ()] = $paramObjekt;
			}
		}
	}
	
	/**
	 * giebt alle Paramobjekte des Services zurück
	 * 
	 * @return multitype:
	 */
	public function getParams() {
		return $this->params;
	}
	
	/**
	 * Setzen einer Action mit Parametern
	 * 
	 * @param $action String       	
	 * @param $AliasName String       	
	 * @param $Params array       	
	 * @return Action FALSE es nicht zur einschreibung gekommmen ist
	 */
	public function setActionWithParams($action, $AliasName = NUll, $Params = array()) {
		
		require_once 'citro/Action.php';
		
		if (is_string ( $action )) {
			$actionObj = new Action ( $action, $Params, $AliasName );
			
			$action = $this->setActionObjekt ( $actionObj );
			
			return $action;
		
		}
		
		return FALSE;
	}
	/**
	 * Setzen einer Action
	 * 
	 * @param $action String       	
	 * @param $AliasName String       	
	 * @return Action FALSE es nicht zur einschreibung gekommmen ist
	 */
	public function setAction($action, $AliasName = NUll) {
		
		require_once 'citro/Action.php';
		
		if (is_string ( $action )) {
			$actionObj = new Action ( $action, array (), $AliasName );
			
			$action = $this->setActionObjekt ( $actionObj );
			
			return $action;
		
		}
		
		return FALSE;
	}
	
	/**
	 * Setzen einer Action
	 * 
	 * @param $action Action       	
	 * @return Action
	 */
	public function setActionObjekt(Action $action) {
		
		require_once 'citro/Action.php';
		
		$PostName = "";
		$i = 1;
		while ( array_key_exists ( $action->getName () . $PostName, $this->actions ) ) {
			
			$PostName = "_" . $i;
			$i ++;
			if ($i > 1000)
				break;
		
		}
		
		$this->actions [$action->getName () . $PostName] = $action;
		
		return $action;
	
	}
	
	/**
	 *
	 * @param $actions array       	
	 * @return Service
	 */
	public function setActions(array $actions) {
		
		if (is_array ( $actions )) {
			foreach ( $actions as $action ) {
				/*
				 * @var $action Action
				 */
				if (is_a ( $action, "Action" )) {
					
					$this->setActionObjekt ( $action );
				
				}
			}
		}
		return $this;
	}
	
	/**
	 *
	 * @param $name string       	
	 * @return Action NULL
	 */
	public function getAction($key) {
		
		if (is_string ( $key )) {
			
			if (array_key_exists ( $key, $this->actions )) {
				
				return $this->actions [$key];
			}
		
		}
		return NULL;
	}
	
	/**
	 *
	 * @return array Actionsliste
	 */
	public function getActions() {
		return $this->actions;
	}

}

?>