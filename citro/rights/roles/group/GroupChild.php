<?php
require_once 'citro/rightsmanagement/Group.php';

class GroupChild extends Group{
	
	
	
	private $_childHira = NULL;
	private $_childHireaIdsCach = NULL;
	
	
	
	
	
	function __construct($groupVal = NULL) {
		
		parent::__construct($groupVal);
		
		$this->_getGroupChild($this->getId());
	}
	
	
	private function _getGroupChild($Id){
		
		// Setze die Kindshira auf null zum neu erstellen
		$this->_childHira = NULL;
		
		require_once 'db/sys/access/groups/sys_access_groups.php';
		
		// Holle alle Gruppen die unter dieser Gruppe liegen aus der Datenbank
		$group = new sys_access_groups ();
		$groupSelect = $group->select ();
		
		$groupSelect->where ( sys_access_groups::SP_PID. " = ?", $Id );
		
		//$groupSelect->where ( contact_group::SP_VISIBIL . " = ?", 1 );
		$groupSelect->where ( sys_access_groups::SP_DELETE . " = ?", 0 );
		
		/* @var $RecGroupRowSet Zend_Db_Table_Rowset_Abstract */
		$RecGroupRowSet = $group->fetchAll ( $groupSelect );
		
		
		// falls keine gefunden wurden dann gieb null zurück
		if( $RecGroupRowSet->count() > 0 ){
		
			// ansonsten durchlaufe alle gruppen und schaue ob es da auch noch weiter untergruppen giebt
			$this->_childHira = array();
			/* @var $groupRow Zend_Db_Table_Row */
			foreach ($RecGroupRowSet as $groupRow){
		
		
				$groupChildId = $groupRow->offsetGet(sys_access_groups::SP_ID);
		
				$newGroup = new GroupChild($groupRow);
				$newGroup->setChildHira();
				$this->_childHira[$groupChildId] = $newGroup;
					
			}
		}
		
		return $this->_childHira;
	}
	
	
	
	
	
	/**
	 * Giebt alle GruppenIds zurück die dieser Gruppe unterliegen
	 * Dabei wird dieses Ergebnis gecachet um somit mehrere DB abfragen zu verhindern
	 * @param Bool $asString Wenn True(standard) übergeben wird dann bekommt man die ids als string zurück falls Fallse dann als Array
	 * @param string $string Separator falls die ausgabe als string erfolgen soll dann kann mann hier den Separator eingeeben standartmäsig ist dieser auf komma gesetzt
	 * @return array|string
	 */
	public function getChildIdsAsCache($asString = TRUE, $stringSeparator = ","){
	
	
		if($this->_childHireaIdsCach === NULL){
	
			$this->_childHireaIdsCach = $this->getChildIds();
				
	
		}
	
		// giebt diese falls gewünscht als String separiert zurück
		if($asString === TRUE){
			return implode(",", $this->_childHireaIdsCach);
		}
	
		return $this->_childHireaIdsCach;
	
	
	
	
	}
	
	/**
	 * Giebt alle Gruppenids die der Gruppe unterliegen zurück
	 * wird recursiv aufgerufen so werden die ids auch aller Untergruppen mit eingeschlossen
	 * @return array mit allen Gruppenids
	 */
	public function getChildIds(){
	
		$gruppenIds = array();
	
		if(is_array( $this->getChildHira() ) ){
				
			/* @var $groupe Group */
			foreach ($this->getChildHira() as $groupe){
	
	
				if(is_a($groupe, "Group")){
						
					$gruppenIds[] = $groupe->getId();
						
					$childArrys = $groupe->getChildIds();
						
					$gruppenIds = array_merge($gruppenIds,$childArrys);
						
						
				}
			}
		}
	
	
		return $gruppenIds;
	
	}
	
	
	public function isChildGroup(){
		if($this->_childHira !== NULL && is_array($this->_childHira)){
			return TRUE;
		}else{
			return FALSE;
		}
	}
	
	/**
	 * Liefert die KindsGruppen in Hirachie als Gruppenobjekt
	 * Diese abfrage cacht die ergebnisse
	 * @return array Die liste der Kindsgruppen dieser Gruppe
	 */
	public function getChildHira(){
	
	
		if($this->_childHira === NULL){
			$this->setChildHira();
		}
		return $this->_childHira;
	}
	
	/**
	 * Setzt Alle KindsGruppen Neu. Wenn diese nicht gelöscht oder die Sichtbarkeit gemarkert sind
	 * Diese abfrage wird nicht gecacht
	 * @return NULL|Group
	 */
	public function setChildHira(){
	
		// Setze die Kindshira auf null zum neu erstellen
		$this->_childHira = NULL;
	
		require_once 'db/sys/access/groups/sys_access_groups.php';
	
		// Holle alle Gruppen die unter dieser Gruppe liegen aus der Datenbank
		$group = new sys_access_groups ();
		$groupSelect = $group->select ();
	
		$groupSelect->where ( sys_access_groups::SP_PID. " = ?", $this->getId() );
	
		//$groupSelect->where ( contact_group::SP_VISIBIL . " = ?", 1 );
		$groupSelect->where ( sys_access_groups::SP_DELETE . " = ?", 0 );
	
		/* @var $RecGroupRowSet Zend_Db_Table_Rowset_Abstract */
		$RecGroupRowSet = $group->fetchAll ( $groupSelect );
	
	
		// falls keine gefunden wurden dann gieb null zurück
		if( $RecGroupRowSet->count() > 0 ){
	
			// ansonsten durchlaufe alle gruppen und schaue ob es da auch noch weiter untergruppen giebt
			$this->_childHira = array();
			/* @var $groupRow Zend_Db_Table_Row */
			foreach ($RecGroupRowSet as $groupRow){
	
	
				$groupChildId = $groupRow->offsetGet(sys_access_groups::SP_ID);
	
				$newGroup = new GroupChild($groupRow);
				$newGroup->setChildHira();
				$this->_childHira[$groupChildId] = $newGroup;
					
			}
		}
	
		return $this->_childHira;
	}
	
	
	
	
	
	
	
	
}

?>