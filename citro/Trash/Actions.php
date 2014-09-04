<?php

/** 
 * @author Remo Bluhm
 * 
 * 
 */

class Actions {
	const TAG_ACTIONS = "actions";
	
	private $actions = array ();
	
	public function setActions(array $actions) {
		foreach ( $actions as $actionName => $action ) {
			require_once 'citro/Action.php';
			$Action = new Action ();
			$Action->setAction ( $action );
			$this->actions [$actionName] = $Action;
		}
	}
	
	public function workActions(Service $service) {
		require_once 'citro/Action.php';
		/*
		 * @var $Action Action
		 */
		foreach ( $this->actions as $actionName => $Action ) {
			
			$Action->workAction ( $service );
		
		}
	
	}

}

?>