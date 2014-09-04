<?php


require_once 'citro/rights/roles/Role.php';
require_once 'db/sys/access/groups/sys_access_groups.php';

/**
 * Repräsentiret als objekt eine Gruppe
 * In Ihr sind auch alle ElterGruppen in einer Hirachie enthalten
 * 
 * @author Max Plank
 *        
 */
class Group extends Role {
	
	private $id = NULL;
	private $pid = NULL;
	private $name = NULL;
	private $_userCreate = NULL;
	private $isOn = TRUE;

	/**
	 * @var Zend_Db_Table_Rowset_Abstract
	 */
	private $_groupRow = NULL;
	

	
	
	public function getData(){
		$groupA = $this->_groupRow->toArray();
		$groupA["role_id"] = $this->getRoleId();
		
		return $groupA;
		
	}
	### Inizialisierung ################################################
	
	function __construct($groupVal = NULL){
		
		if(is_integer($groupVal) && $groupVal > 0){
			$dataRow = $this->setDateFromDatabase($groupVal);
			$groupVal = $dataRow;
		}
		
// 		$data = new Zend_Db_Table_Row();
// 		$d
		
		if($groupVal instanceof Zend_Db_Table_Row_Abstract){
			$this->_groupRow = $groupVal;	
			parent::__construct("G_".$this->_groupRow->offsetGet("id"));
		}else{
			throw new Exception("Gruppe konnte nicht Inizialisiert werden!",E_ERROR);
		}
		

		
	}
	

	
// 	/**
// 	 * Giebt alle meine Gruppen die ich besitze als namensArray (Alle Ebenen)
// 	 * @return array
// 	 */
// 	public function getMyGroupsAsArray() {
// 		require_once 'db/sys/access/groups/sys_access_groups.php';
// 		$group = new sys_access_groups ();
// 		$myGroup = $group->getTreeName($this->getId());
// 		return $myGroup;
// 	}
	
	
	/**
	 * Inizialisiert das Gruppenobjekt in dem es die Daten aus der Datenbank hollt
	 *
	 * @param Integer $groupId
	 * @return boolean Ob die Daten gesetzt wurden (TRUE) oder nicht (FALSE)
	 */
	private function setDateFromDatabase($groupId){
		require_once 'db/sys/access/groups/sys_access_groups.php';
	
		$group = new sys_access_groups ();
		$groupSelect = $group->select ();
		$groupSelect->where ( sys_access_groups::SP_ID. " = ?", $groupId );
		$groupSelect->where ( sys_access_groups::SP_DELETE . " = ?", 0 );
	
		$groupRow = $group->fetchRow ( $groupSelect );
		return $groupRow;
	}
	

	
	/**
	 * Prüft ob die Daten Der Gruppe gesetzt sind
	 * @return boolean
	 */
	public function isDataSet(){
		if($this->_groupRow === NULL) {
			return  FALSE;
		}
		return TRUE;
	}
	
	

	
	

	
	
	/**
	 * Giebt die Id der Gruppe Zurück
	 * @throws Exception Wenn keine gruppe gesetzt werden konnte
	 * @return NULL
	 */
	public function getId() {
		if($this->_groupRow !== NULL){
			return (integer)$this->_groupRow->offsetGet(sys_access_groups::SP_ID, NULL );
		}
		return NULL;
	}
	
	/**
	 * Giebt die Id der Eltern(partent) Gruppe zurück
	 * @throws Exception Wenn keine gruppe gesetzt werden konnte
	 * @return NULL integer Id der darüberligenden gruppe oder Null wenn diese
	 *         gruppe keine Elterngruppe besitzt
	 */
	public function getPid(){
		if($this->_groupRow !== NULL){
			return $this->_groupRow->offsetGet(sys_access_groups::SP_PID, 0);
		}
		return NULL;
	}
	
	/**
	 * Giebt die Access Id die die Gruppe erstellt hat zurück
	 * @return number|NULL
	 */
	public function getCreateGuId(){
		
		if($this->_groupRow instanceof  Zend_Db_Table_Row_Abstract){
			return (integer) $this->_groupRow->offsetGet(sys_access_groups::SP_ACCESS_CREATE, NULL);
		}
		return NULL;
	}
	
	
	/**
	 * Giebt den User zurück der die Gruppe erstellt hat
	 * @return User
	 */
	public function getUserCreate(){
	
	}
	
	
	public function getDateCreate(){
		if($this->_groupRow !== NULL){
			return $this->_groupRow->offsetGet(sys_access_groups::SP_DATA_CREATE, 0);
		}
	}
	public function getDateEdit(){
		if($this->_groupRow !== NULL){
			return $this->_groupRow->offsetGet(sys_access_groups::SP_DATA_EDIT, 0);
		}
	}
	public function getText(){
		if($this->_groupRow !== NULL){
			return $this->_groupRow->offsetGet(sys_access_groups::SP_TEXT, 0);
		}
	}

	
	/**
	 * Giebt die Sichtbarkeit der Gruppe zurück
	 * @throws Exception Wenn keine gruppe gesetzt werden konnte
	 * @return boolean
	 */
	public function getVisit(){
		if($this->_groupRow !== NULL){
			$visit = $this->_groupRow->offsetGet(sys_access_groups::SP_VISIBIL, 0);
			if($visit == 1){
				return TRUE;
			}
			return FALSE;
		}
		throw new Exception("Gruppe wurde nicht gesetzt", E_ERROR);
	}
	
	/**
	 * Giebt den Namen der gruppe zurück
	 * 
	 * @return NULL string Name der gruppe oder Null wenn er nicht gesetzt ist
	 */
	public function getName() {
		if($this->_groupRow !== NULL){
			$name = $this->_groupRow->offsetGet(sys_access_groups::SP_NAME, "no set");
			return $name;
		}
		throw new Exception("Gruppe wurde nicht gesetzt", E_ERROR);
	}
	

	
	/**
	 * Ob die Gruppe on ist also eingeschaltet
	 * hiermit können zb.
	 * die User ein oder ausgeschalten sein
	 * 
	 * @return boolean
	 */
	public function getIsOn() {
		return $this->isOn;
	}
	
	
	
	

	
	
	/**
	 * Zerlegt die RolenId in die GruppenId
	 * @param string $roleId
	 * @return number GruppenId
	 */
	public static function getIdFromRoleId($roleId){
		$groupId = (integer)substr($roleId, 2);
		return $groupId;
	}


	

	

	


	
	
	
	


}

?>