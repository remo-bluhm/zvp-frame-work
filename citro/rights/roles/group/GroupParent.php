<?php

require_once 'citro/rights/roles/group/Group.php';

class GroupParent extends Group{
	
	private $_partent = NULL;
	
	
	function __construct(Zend_Db_Table_Row_Abstract $groupVal) {
		parent::__construct($groupVal);
	
	}
	

	
	public function setParent($group){
		$this->_partent = $group;
	}
	
 	public function getParents() {
		return $this->_partent;
 	
 	}

	
	public function getGroupPartent(){
		
		$SelfObj = array( $this->getRoleId() => $this );
		
		if ($this->isParents ()) {
			$subj = $this->getParents ();		
			$SelfObj =  $subj->getGroupPartent ();
			$SelfObj[ $this->getRoleId()] = $subj;
		}
		
		return $SelfObj;
	}

	
	/**
	 * Giebt alle Gruppen Ids von der eigenen und aller Eltern Gruppen
	 *
	 * @return array
	 */
	public function getGroupParentRoleIds() {

		// hollt die eigene Rollen Id des Accesses
		$SelfId = array ($this->getRoleId() );

		
		if ($this->isParents ()) {
			$subj = $this->getParents ();
			$subjIdArray = $subj->getGroupParentRoleIds ();

			$SelfId = array_merge ( $SelfId, $subjIdArray );
		}

		return $SelfId;

	}
	
	
	
	public function getPartentGroupsAsArray(){
	
		$selfArray = array();
	
		if ($this->isParents ()) {
			$subj = $this->getParents ();
			$subjArray = $subj->getGroupAsArray ();
	
			$selfArray = array_merge ( $selfArray, $subjArray );
		}
	
		return $selfArray;
	}
	
	public function getGroupAsArray() {
	
		$selfArray [$this->getId ()] = $this->getData();
	
		if ($this->isParents ()) {
			$subj = $this->getParents ();
			$subjArray = $subj->getGroupAsArray ();
	
			$selfArray = array_merge ( $selfArray, $subjArray );
		}
	
		return $selfArray;
	}
	
	
	

	
	
	/**
	 * prüft ob die Gruppe eine Darüberliegende Gruppe besitzt
	 *
	 * @return boolean
	 */
	public function isParents() {
		
		if (!$this->_partent instanceof GroupParent)
			return FALSE;
		return TRUE;
	}
	
	
	// 	/**
	// 	 * Giebt die Eltern Gruppen zurück
	// 	 * Wenn diese noch nicht exestiert dann wird sie erstellt
	// 	 * @return Group|NULL null wenn es keine Elterngruppe giebt
	// 	 */
	// 	public function getParents() {
	
	
	// 		if(!$this->isParents()){
	// 			if((integer)$this->getPid() != 0){
	// 				$newGroup = new GroupParent((integer)$this->getPid());
	// 				if($newGroup->isDataSet()){
	// 					$this->_partent = $newGroup;
	// 				}
	// 			}
	// 		}
	
	
	// 		return $this->_partent;
	// 	}
	// 	/**
	// 	 * Giebt die Angefragte Gruppe mit Ihren Elterngruppen zurück
	// 	 */
	// 	public function setAllParents() {
	
	// 		$parentG = $this->getParents();
	// 		if($parentG instanceof GroupParent){
	
	
	// 			$parentG->setAllParents();
	// 		}
	
	// 	}
	

	
	
	
}

?>