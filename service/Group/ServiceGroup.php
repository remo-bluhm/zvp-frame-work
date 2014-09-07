<?php

require_once 'citro/service-class/AService.php';

/**
 * Der Service ist für die verwaltung der Gruppen verantwortlich
 *
 * @author Max Plank
 * @version 1.0
 *         
 */
class ServiceGroup extends AService {
	
	/**
	 * Der Rechemanagement construktor
	 */
	function __construct() {
		
		parent::__construct ();
	
	}
	
	/**
	 * Hollt Alle gruppen die Ich erstellt habe 
	 * 
	 * @return array Das Gruppen array
	 * @param bool $flat ob als hirachie oder Flach
	 * @citro_isOn true
	 * @citro_protocollExist AesKey|sha1hasch Die Geforderten Protocolle
	 */
	public function ActionGetMyGroups($flat = TRUE) {
		
		$myGroups = $this->_rightsAcl->getAccess()->getMyGroups($flat);
		return $myGroups;
		
		

		
	}
	
	/**
	 * Hollt Alle gruppen die Ich erstellt habe
	 *
	 * @return array Das Gruppen array
	 * @citro_isOn true
	 * @citro_protocollExist AesKey|sha1hasch Die Geforderten Protocolle
	 */
	public function ActionGetRoot() {
	
		if($this->_rightsAcl->getAccess()->isAdmin()){
			require_once 'db/sys/access/groups/sys_access_groups.php';
			$group = new sys_access_groups ();
			$root = $group->getRoot();
			return $root->toArray();
		}
		
		return NULL;
	
	}
	
	
	/**
	 * Liefert eine Liste Meiner gruppen die unter einer meiner Gruppen unterliegt 
	 * Wenn groupName Null dann die Main gruppen
	 *  
	 * @param string $groupName
	 */
	public function ActionGetGroupList($groupName = NULL){
		$myGroups = $this->_rightsAcl->getAccess()->getMyGroupsAsArray();
		require_once 'db/sys/access/groups/sys_access_groups.php';
		
		if(is_array($myGroups) && in_array($groupName, $myGroups)){
			
			$group = new sys_access_groups ();
			$myGroup = $group->getList($groupName);
			return $myGroup;
			
		}else {

			$group = new sys_access_groups ();
			$myGroup = $group->getList($this->_rightsAcl->getGroup()->getName(),$this->_rightsAcl->getAccess()->getId());
			return $myGroup;
		}
		

	}

// 	/**
// 	 * Hollt Alle Gruppen die Mir Unterliegen und von meinen Administrator mir zugewiesen wurden
// 	 *
// 	 * @return array Das Gruppen array
// 	 * @param bool $flat ob als hirachie oder Flach
// 	 * @citro_isOn true
// 	 * @citro_protocollExist AesKey|sha1hasch Die Geforderten Protocolle
// 	 */
// 	public function ActionGetMyChildGroups($flat = TRUE) {
	
// 		$myGroups =  $this->_rightsAcl->getAccess()->getMySubGroups($flat);
// 		$groupA = $this->_getChildGroupAsData($myGroups, FALSE);
// 		return $groupA;
// 	}
	
	/**
	 * Giebt die Anzahl der User zurück die einer Gruppe unterliegen
	 *
	 * @param integer $groupId  falls nur die User zurückgegben werden sollen die unter einer Gruppe liegen dann muss hier die GruppenId übergeben werden
	 * @return integer Die Anzahl der User
	 * @citro_isOn true
	 */
	public function ActionCountUserInGroup($groupId = 0) {
		require_once 'db/sys/access/sys_access.php';
		$contactTab = new sys_access();
		$cotactsSel = $contactTab->select();
		$cotactsSel->from(sys_access::getTableNameStatic(),array("COUNT(*)"));
		$cotactsSel->where ( sys_access::SP_DELETE . " = ?", 0 );
		$cotactsSel->where(sys_access::SP_GROUPID." = ?", $groupId);
		//		echo $cotactsSel->__toString();
		// 		echo "<br>";
		$row = $contactTab->fetchRow($cotactsSel);
	
		$anzahl = $row->offsetGet("COUNT(*)");
		return $anzahl;
	
	}
		
	/**
	 * Giebt alle Kinds Gruppen als Arrays zurück
	 * wird recursiv aufgerufen so werden die ids auch aller Untergruppen mit eingeschlossen
	 * @param array $myGroups ein Array mit Gruppenobjekten
	 * @param bool $flat Ob es einen Flache ausgebe oder hirachiche ausgabe geben soll 
	 * @return array mit allen Gruppenids 
	 */
	private function _getChildGroupAsData( $myGroups){
	
		$gruppenData = array();
	
		if(is_array( $myGroups) ){
		
			
			/* @var $groupe Group */
			foreach ($myGroups as $groupe){
				
				// Hollen der Daten
				$groupData = $groupe->getData();
				unset($groupData['deleted']);
				
				// Prüfe auf vorhandensein von KinsGruppen
				if($groupe->isChildGroup()){
					
					$groupDataChild = $this->getChildGroupAsData($groupe->getChildHira());
					$groupData["parents"] = $groupDataChild;
								
				}
				$gruppenData[] = $groupData;		
			} // endforeach
		}
		return $gruppenData;
	}
	
	
	
// 	/**
// 	 * Hollt eine meiner Gruppen
// 	 *
// 	 * @param integer $id
// 	 * @return array NULL Userliste NULL wenn gruppe nicht gefunden wurde oder user nicht besitzer
// 	 * @citro_isOn true
// 	 * @citro_protocollExist aeskey|sha1hasch Die Geforderten Protocolle
// 	 */
// 	public function ActionGetSingle($id) {
	
// 		require_once 'db/sys/access/groups/sys_access_groups.php';
	
// 		$groupTab = new sys_access_groups ();
// 		$groupSelect = $groupTab->select ();
// 		$groupSelect->where ( sys_access_groups::SP_ID . " = ?", $id );
// 		$groupSelect->where ( sys_access_groups::SP_DELETE . " = ?", 0 );
// 		if (! $this->_MainUser->isAdmin ()) {
// 			$groupSelect->where ( sys_access_groups::SP_ACCESS_CREATE . " = ?", $this->_rightsAcl->getAccess()->getGuId() );
// 		}
// 		$group = $groupTab->fetchRow ( $groupSelect );
	
// 		if ($group === NULL){
				
// 			return FALSE;
// 		}
// 		require_once 'citro/rightsmanagement/Group.php';
// 		$groupObj = new Group($group);
// 		$groupObj->getData();
	
// 		return $groupObj->getData();
	
	
// 	}
	
	/**
	 * Hollt eine meiner Gruppen aufgrund vom Gruppennamen
	 *
	 * @param string $groupname
	 * @param array $spalten
	 * @return array|bool FALSE wenn gruppe nicht gefunden wurde oder user nicht besitzer
	 * @citro_isOn true
	 * @citro_protocollExist aeskey|sha1hasch Die Geforderten Protocolle
	 */
	public function ActionGetSingle($groupname ,$spalten = array()) {
		
		
		$myGroups = $this->_rightsAcl->getAccess()->getMyGroupsAsArray();
		
		require_once 'db/sys/access/groups/sys_access_groups.php';
		
		
			
		if(is_array($myGroups) && in_array($groupname, $myGroups)){
	
			$db = sys_access_groups::getDefaultAdapter();
			
			$spA = array();
			$spA["name"] = "name";
			$spA["short_text"] = "text";
			$spA["create_date"] = "date_create";
			$spA["edit_date"] = "date_edit";

			
			
			
			$sel = $db->select ();
			$sel->from( array('g' => sys_access_groups::getTableNameStatic() ), $spA );
			
			if( in_array('usercreate_name',$spalten) ){
				require_once 'db/sys/access/sys_access.php';
				require_once 'db/contact/contacts.php';
				$sel->joinLeft(array('a'=>sys_access::getTableNameStatic()), "g.access_owner = a.id",array('create_access_guid' => 'a.guid' )  );
				$sel->joinLeft(array('c1'=>contacts::getTableNameStatic()), "a.contacts_id = c1.id", array ('create_user_name' => 'CONCAT(c1.first_name," ",c1.last_name )' ) );
			}
			
			if( in_array('useredit_name',$spalten) ){
				require_once 'db/sys/access/sys_access.php';
				$sel->joinLeft(array('a2'=>sys_access::getTableNameStatic()), "g.access_edit = a2.id" ,array('edit_access_guid' => 'a2.guid' ) );
				$sel->joinLeft(array('c2'=>contacts::getTableNameStatic()), "a2.contacts_id = c2.id", array ('edit_user_name' => 'CONCAT(c2.first_name," ",c2.last_name )')  );
			}
			
			
			
			$sel->where ( sys_access_groups::SP_NAME . " = ?", $groupname );
			
			$group = $db->fetchRow ( $sel );
		
			
			if (!is_array($group))return FALSE;
			return $group;
		}

		return FALSE;
	}
	
	
	/**
	 * Hollt eine meiner Gruppen aufgrund vom Gruppennamen
	 *
	 * @param string $groupname
	 * @return array|bool FALSE wenn gruppe nicht gefunden wurde oder user nicht besitzer
	 * @citro_isOn true
	 * @citro_protocollExist aeskey|sha1hasch Die Geforderten Protocolle
	 */
	public function ActionGetRollId($groupname) {
	
	
		$myGroups = $this->_rightsAcl->getAccess()->getMyGroupsAsArray();
	
		require_once 'db/sys/access/groups/sys_access_groups.php';
	
	
			
		if(is_array($myGroups) && in_array($groupname, $myGroups)){
	
			$db = sys_access_groups::getDefaultAdapter();
				
			$spA = array();
			$spA["id"] = "id";
			$spA["role_id"] = " CONCAT( 'G_',id)";
			$spA["name"] = "name";
			$spA["short_text"] = "text";
			$spA["create_date"] = "date_create";
			$spA["edit_date"] = "date_edit";
			$sel = $db->select ();
			$sel->from(  sys_access_groups::getTableNameStatic() , $spA );	
			$sel->where ( sys_access_groups::SP_NAME . " = ?", $groupname );
			$group = $db->fetchRow ( $sel );
			
				
			if (!is_array($group))return FALSE;
			return $group;
		}
	
		return FALSE;
	}
	
	/**
	 * Stellt ein ob die gruppe und Ihrer darunderliegenden User ausgeschaltet sind
	 *
	 * @param integer $id
	 * @return bool 
	 * @citro_isOn true
	 */
	public function ActionSetUnVisit($id) {
	

		require_once 'citro/rightsmanagement/GroupChild.php';	
		$groupMain = new GroupChild($id);


		$underGroupsIds = $groupMain->getChildIdsAsCache(FALSE);
		$underGroupsIds[] = $id;
	
		
		$groupTab = new groups ();
		$groupSelect = $groupTab->select ();
		$groupSelect->where ( groups::SP_ID . " IN (?)", $underGroupsIds );
		$groupSelect->where ( groups::SP_DELETE . " = ?", 0 );
		if (! $this->_MainUser->isAdmin ()) {
			$groupSelect->where ( groups::SP_USER_CREATE . " = ?", $this->_MainUser->getId () );
		}
		
		//echo $groupSelect->__toString();
		/* @var $groups Zend_Db_Table_Rowset */
		$groups = $groupTab->fetchAll( $groupSelect );
		
		
		/* @var $group Zend_Db_Table_Row */
		foreach ($groups as $group){
			$group->offsetSet ( groups::SP_VISIBIL, 0 );
			$group->save ();
		}
		
		return TRUE;
	}	
	
	
	/**
	 * Stellt ein ob die gruppe und Ihrer darunderliegenden User ausgeschaltet sind
	 * 
	 * @param integer $id        	      	
	 * @return array NULL Userliste NULL wenn gruppe nicht gefunden wurde oder user nicht besitzer
	 * @citro_isOn true
	 */
	public function ActionSetVisit($id) {

		$groupMain = new GroupParent($id);
		$parentGroup = $groupMain->getParents();

		$partentVisit = TRUE;
		if($parentGroup instanceof GroupParent && $parentGroup->getVisit() === FALSE)$partentVisit = FALSE;
		
		if($partentVisit === FALSE) return FALSE;
		
		$groupTab = new groups ();
		$groupSelect = $groupTab->select ();
		$groupSelect->where ( groups::SP_ID . "= ?", $id );
		$groupSelect->where ( groups::SP_DELETE . " = ?", 0 );
		if (! $this->_MainUser->isAdmin ()) {
			$groupSelect->where ( groups::SP_ACCESS_CREATE . " = ?", $this->_MainUser->getId () );
		}
		
		/* @var $groups Zend_Db_Table_Rowset */
		$group = $groupTab->fetchRow( $groupSelect );
		$group->offsetSet ( groups::SP_VISIBIL, 1 );
		$group->save ();

	
		return TRUE;
	}

	
	/**
	 * Hollt die Kinds Gruppen
	 *    
	 * @param integer $id 	
	 * @return array NULL Userliste NULL wenn gruppe nicht gefunden wurde oder user nicht besitzer
	 * @citro_isOn true
	 */
	public function ActionGetChildGroups($id) {
		
		// Prüft auf die Gruppen Id
		$myGroups1 = $this->_MainUser->getMyGroupsIds();
		$myGroups2 = $this->_MainGroup->getChildIdsAsCache(FALSE);
		$myGroups =  array_unique(array_merge( $myGroups1, $myGroups2));
		if( !in_array($id, $myGroups)){
			throw new Exception("User besitzt keine Rechte über diese Gruppe", E_ERROR);
		}

		
		require_once 'db/contact/contact_group.php';
		
		$groupTab = new groups ();
		$groupSelect = $groupTab->select ();
		$groupSelect->where ( groups::SP_PID . " = ?", $id );
		$groupSelect->where ( groups::SP_DELETE . " = ?", 0 );
		if (! $this->_MainUser->isAdmin ()) {
			$groupSelect->where ( groups::SP_USER_CREATE . " = ?", $this->_MainUser->getId () );
		}
		$group = $groupTab->fetchAll ( $groupSelect );
		
		// if($group === NULL)return FALSE;
		
		return $group->toArray ();
	}
	

	/**
	 * Erstellt eine Neue Gruppe
	 * @param string $Name
	 * @param string $Description
	 * @param bool $Visit
	 * @return boolean|Ambigous <multitype:, array>
	 */
	public function ActionNewMainGroup($Name, $Description, $Visit = FALSE) {
		
		
		require_once 'db/sys/access/groups/sys_access_groups.php';
		$groupTab = new sys_access_groups ();
		$groupRow = $groupTab->getGroupAsId($this->_rightsAcl->getGroup()->getId());
		if ($groupRow === NULL) 
			return FALSE;
			
		$mLft = $groupRow->offsetGet("lft");
		$mRgt = $groupRow->offsetGet("rgt");

		$db = sys_access_groups::getDefaultAdapter();
		$db->beginTransaction();
		
		try{
			$data = $groupTab->newMainGroup($mLft,$mRgt,$this->_rightsAcl->getAccess()->getId(), $Name,$Description,1 );
			$db->commit();
			return $data;
		}catch (Exception $eTrans){
			$db->rollBack();
			return FALSE;
		}
		
	}
	
	/**
	 * Erstellt eine Neue Gruppe
	 * @param string $groupName
	 * @param string $parentGroupName
	 * @param string $Description
	 * @param bool $Visit
	 * @return boolean|Ambigous <multitype:, array>
	 */
	public function ActionNewGroup( $groupName, $parentGroupName = NULL, $Description = NULL, $Visit = FALSE) {
	
		//Inizialisieren der Gruppentabelle
		require_once 'db/sys/access/groups/sys_access_groups.php';
		$groupTab = new sys_access_groups ();
		
		
		// Prüfen auf übergebene Eltern gruppe 
		// ansonsten unter der Gruppe der Accessgruppe einschreiben
		if( !empty($parentGroupName) && in_array( $parentGroupName, $this->_rightsAcl->getAccess()->getMyGroupsAsName() ) ){
			$groupRow = $groupTab->getGroupAsName($parentGroupName);
		}else {
			$groupRow = $groupTab->getGroupAsName($this->_rightsAcl->getGroup()->getName());			
		}
		
		
		// Prüfen auf Rückgabetype
		if(!is_a($groupRow, Zend_Db_Table_Row_Abstract))
			return FALSE;

		// einschreiben
		$data = $groupTab->newMainGroup($groupRow->offsetGet("lft"),
										$groupRow->offsetGet("rgt"),
										$this->_rightsAcl->getAccess()->getId(), 
										$groupName,
										$Description,
										1 );
		
		return $data;
	}
	
	/**
	 * Bearbeitet eine Gruppe
	 * @param string $oldName
	 * @param string $newName
	 * @param string $text
	 * @return boolean|Ambigous <multitype:, array>
	 */
	public function ActionUpdateMainGroup($oldName, $newName=NULL, $text=NULL) {
	
	
		require_once 'db/sys/access/groups/sys_access_groups.php';
		$groupTab = new sys_access_groups ();
		$numberRowsUpdate = $groupTab->updateMainGroup($oldName, $newName, $text);

		if($numberRowsUpdate > 0)return TRUE;
		return FALSE;
	}
	
	
	public function ActionDeleteMainGroup(){
		
		$myMainGroups = $this->_rightsAcl->getAccess()->getMyMainGroups();
		
		$mainGroupIdA = array();
		if(is_array($myMainGroups)){
			foreach ($myMainGroups as $mainGroup){
				$mainGroupId[] = $mainGroup["sys_id"];
			}
		}
		///    uniqid()
		if(in_array($id, $mainGroupId))
		
		require_once 'db/sys/access/groups/sys_access_groups.php';
		
		$db = sys_access_groups::getDefaultAdapter();
		$db->beginTransaction();
		
		try{
				
			$sysAccessGroupsTab = new sys_access_groups();
			// holen des Darüberliegenden elementes
			$tabRow = $sysAccessGroupsTab->getElement(amenities::getRootKey($elementId),amenities::getSysId($elementId),"SYSID");
				
			$rgt = (integer)$tabRow['rgt'];
			$lft = (integer)$tabRow['lft'];
			$rootKey = (string)$tabRow['rootkey'];
		
			// Prüfen ob das zu löschende Element die wurzel ist wenn ja dann abbruch
			if($lft == 1)
				throw new Exception("Element darf kein Rootelement sein", E_ERROR);
				
			// sichern des kompletten baumes
			$this->ActionSaveTree($rootKey,"Not save for delete Elementtree(".$elementId.")");
				
			// Lösche das übergebene Elemente mit all seinen unterelementen
			$sysAccessGroupsTab->deleteElementWithUnderElement($lft,$rgt,$rootKey);
				
			$db->commit();
			return TRUE;
		
		}catch (Exception $eTrans){
			$db->rollBack();
			die($eTrans->getMessage());
			return FALSE;
		
		}
	}
	
	
	
	/**
	 * Erstellt eine Gruppe die über einer anderen liegt
	 * 
	 * @param integer	$ChildGruppeId  die gruppenid der gruppe die dann als Kindsgruppe gelten soll
	 * @param string 	$Name  Der Darüberligenden Neuen Gruppe
	 * @param string 	$Description        	
	 * @param bool 		$Visit        	
	 * @param integer	$ParentGruppe        	
	 * @return array
	 */
	public function ActionNewParentGroup($ChildGruppeId, $Name, $Description, $Visit = FALSE) {
		
		
		// Prüft auf die Gruppen Id
		$myGroups1 = $this->_MainUser->getMyGroupsIds();
		$myGroups2 = $this->_MainGroup->getChildIdsAsCache(FALSE);
		$myGroups =  array_unique(array_merge( $myGroups1, $myGroups2));
		if( !in_array($id, $myGroups)){
			throw new Exception("User besitzt keine Rechte über diese Gruppe", E_ERROR);
		}
		
		
		
		
		require_once 'citro/db/contact/contact_group.php';
		// $group = new sys_group();
		
		// $childGroup = $group->getGroup($ChildGruppeId,
		// $this->_MainUser->getId());
		
		// $groupTab = new sys_group();
		// $groupSelect = $groupTab->select();
		// $groupSelect->where( sys_group::SP_ID." = ?",$ChildGruppeId);
		// $groupSelect->where( sys_group::SP_DELETE." = ?",0);
		// $groupSelect->where( sys_group::SP_USER_CREATE." =
		// ?",$this->_MainUser->getId());
		// $childGroup = $groupTab->fetchRow($groupSelect);
		
		$childGroupTab = new groups ();
		$childGroupSelect = $childGroupTab->select ();
		$childGroupSelect->where ( groups::SP_ID . " = ?", $ChildGruppeId );
		$childGroupSelect->where ( groups::SP_DELETE . " = ?", 0 );
		$childGroupSelect->where ( groups::SP_USER_CREATE . " = ?", $this->_MainUser->getId () );
		
		$childGroupRow = $childGroupTab->fetchRow ( $childGroupSelect );
		
		if ($childGroupRow !== NULL) {
			
			$tabData = array ();
			
			$tabData [groups::SP_PID] = $childGroupRow->offsetGet ( groups::SP_PID );
			$tabData [groups::SP_NAME] = $Name;
			$tabData [groups::SP_TEXT] = $Description;
			$tabData [groups::SP_DATA_CREATE] = DBTable::DateTime ();
			$tabData [groups::SP_DATA_EDIT] = DBTable::DateTime ();
			$tabData [groups::SP_USER_CREATE] = $this->_MainUser->getId ();
			$tabData [groups::SP_USER_EDIT] = $this->_MainUser->getId ();
			$tabData [groups::SP_DELETE] = 0;
			if ($Visit === TRUE) {
				$tabData [groups::SP_VISIBIL] = 1;
			} else {
				$tabData [groups::SP_VISIBIL] = 0;
			}
			
			$newGroupTab = new groups ();
			$newGroup = $newGroupTab->createRow ( $tabData );
			$newGroup->save ();
			
			// $NewGroupId =
			// $group->setNew($this->_MainUser->getId(),$Name,$Description,$childGroup[sys_group::SP_PID],$Visit);
			
			$childGroupRow->offsetSet ( groups::SP_PID, $newGroup->offsetGet ( groups::SP_ID ) );
			$childGroupRow->save ();
			
			$NewGroupA = $newGroup->toArray ();
			return $NewGroupA;
		
		}
		
		return FALSE;
	
	}
	
	public static function RecycleGroup($data) {
		
		require_once 'citro/db/contact/contact_group.php';
		require_once 'citro/db/contact/contacts.php';
		
		if (is_array ( $data )) {
			
			if (array_key_exists ( "group", $data )) {
				
				/*
				 * @var $groupRow Zend_Db_Table_Row
				 */
				$groupRow = $data ["group"];
				$pid = $groupRow->offsetGet ( contact_group::SP_PID );
				
				$isTrue = TRUE;
				
				while ( $isTrue ) {
					if ($pid !== "0") {
						
						$group = new groups ();
						$groupSelect = $group->select ();
						$groupSelect->where ( groups::SP_ID . " = ?", $pid );
						$groupRow = $group->fetchRow ( $groupSelect );
						if ($groupRow !== NULL) {
							
							$isDelete = $groupRow->offsetGet ( groups::SP_DELETE );
							if ($isDelete == "1") {
								$groupRow->offsetSet ( groups::SP_DELETE, 0 );
								$groupRow->save ();
								$pid = $groupRow->offsetGet ( groups::SP_PID );
							
							} else {
								$isTrue = FALSE;
							}
						
						} else {
							$isTrue = FALSE;
						}
					} else {
						$isTrue = FALSE;
					}
				
				}
				
				$groupBackData = self::_recycleGroupRecursive ( $data );
				
				return TRUE;
			} else {
				return FALSE;
			}
		
		} else {
			return FALSE;
		}
	
	}
	
	private function _recycleGroupRecursive($data) {
		require_once 'db/groups/groups.php';
		require_once 'db/contact/contacts.php';
		
		if (is_array ( $data )) {
			
			if (array_key_exists ( "group", $data )) {
				$group = new groups ();
				
				/*
				 * @var $groupRow Zend_Db_Table_Row
				 */
				$groupRow = $data ["group"];
				$groupRow->setTable ( $group );
				$groupRow->offsetSet ( groups::SP_DELETE, 0 );
				$groupRow->save ();
				
				if (array_key_exists ( "user", $data )) {
					
					if (is_array ( $data ["user"] )) {
						
						$user = new contacts ();
						/*
						 * @var $userRow Zend_Db_Table_Row
						 */
						foreach ( $data ["user"] as $userRow ) {
							
							$userRow->setTable ( $user );
							$userRow->offsetSet ( contacts::SP_DELETE, 0 );
							$userRow->save ();
						
						}
					}
				}
				
				if (array_key_exists ( "partentgroup", $data )) {
					
					if (is_array ( $data ["partentgroup"] )) {
						
						foreach ( $data ["partentgroup"] as $partentData ) {
							
							$parentBackData = self::_recycleGroupRecursive ( $partentData );
						}
					}
				}
			
			} // ende der MainGruppe
			
			return "ok";
		
		} else {
			return FALSE;
		}
	}
	
	/**
	 * Löscht die Übergebenen Gruppe und seine user
	 * Intern werden sie nur als Deletet Markiert
	 * 
	 * @param integer $groupId  die Gruppe oder Gruppen(als Kommaseparierte Liste) die gelöscht werden sollen
	 * @return ArrayObject Die Userliste
	 * @citro_isOn true
	 */
	public function ActionDelete($groupId) {
		
		$AllRow = $this->_getAllRowsDeleteRecursive ( $groupId );
	
	
		
		if (is_array ( $AllRow ) && count ( $AllRow ) > 0) {
			require_once 'citro/Trash.php';
			$Trash = new Trash ( $this->_MainUser, "GRUPPE" );
			$tableRowsAll = $Trash->setDelete ( $AllRow ["group"]->offsetGet ( groups::SP_NAME ), $AllRow, __FILE__, "ServiceGroup", "RecycleGroup" );
			return TRUE;
		} else {
			return FALSE;
		}
	}

	private function _getAllRowsDeleteRecursive($groupId) {
		$tableRowsAll = array ();
		
		$groupTab = new groups();
		$groupSelect = $groupTab->select ();
		$groupSelect->where ( groups::SP_ID . " = ?", $groupId );
		$groupSelect->where ( groups::SP_DELETE . " = ?", 0 );
		$groupRow = $groupTab->fetchRow ( $groupSelect );
		
		if ($groupRow !== NULL) {
			
			// DeletetMarket
			$groupRow->offsetSet ( groups::SP_DELETE, 1 );
			$groupRow->save ();
			
			$tableRowsAll ["group"] = $groupRow;
			
			$userTab = new contacts();
			$userSelect = $userTab->select ();
			$userSelect->where ( contacts::SP_GROUP_ID . " = ?", $groupRow->offsetGet ( "id" ) );
			$userSelect->where ( contacts::SP_DELETE . " = ?", 0 );
			$userRowSet = $userTab->fetchAll ( $userSelect );
			
			/*
			 * @var $pGroupRow Zend_Db_Table_Row
			 */
			foreach ( $userRowSet as $userRow ) {
				$userRow->offsetSet ( contacts::SP_DELETE, 1 );
				$userRow->save ();
				
				$tableRowsAll ["user"] [] = $userRow;
			}
			
			// ///////////////////////////// Ab hier aufruf wegen Recursive
			$pGroupTab = new groups ();
			$pGroupSelect = $groupTab->select ();
			$pGroupSelect->where ( groups::SP_PID . " = ?", $groupRow->offsetGet ( "id" ) );
			$pGroupSelect->where ( groups::SP_DELETE . " = ?", 0 );
			$pGroupRowSet = $groupTab->fetchAll ( $pGroupSelect );
			
			/*
			 * @var $pGroupRow Zend_Db_Table_Row
			 */
			foreach ( $pGroupRowSet as $pGroupRow ) {
				
				$pGroupRow_id = $pGroupRow->offsetGet ( contact_group::SP_ID );
				$pRowsAll = $this->_getAllRowsDeleteRecursive ( $pGroupRow_id );
				$tableRowsAll ["partentgroup"] [] = $pRowsAll;
			
			}
		
		}
		
		return $tableRowsAll;
	}

	
	/**
	 * Giebt die User Liste einer Gruppe zurück
	 * @param string $groupName
	 * @return array Liste der Accounts
	 */
	public function ActionGetUserFromGroup($groupName){

		
		require_once 'db/sys/access/sys_access.php';
		require_once 'db/sys/access/groups/sys_access_groups.php';
		require_once 'db/contact/contacts.php';
	
		

		require_once 'citro/DBConnect.php';
		$DB = DBConnect::getConnect ();
		$groupSelect = $DB->select ();
		

		$groupSelect->from ( array ('g' => sys_access_groups::getTableNameStatic() ), array("id") );
		$groupSelect->where ( 'g.' . sys_access_groups::SP_NAME. " = ?", $groupName );
		
		$groupId = $DB->fetchOne($groupSelect);

		// abbruch da keine gruppe gefunden wurde
		if($groupId === FALSE)return array();
		
		
		// die Accesses hollen
		$accessSp = array();
		$accessSp['guid'] = sys_access::SP_GUID;
		$accessSp['loginname'] = sys_access::SP_LOGINNAME;
	
		$accessSelect = $DB->select ();
		$accessSelect->from ( array ('a' => sys_access::getTableNameStatic() ),  $accessSp );
		
		$contactsSp = array();
		$contactsSp['firstname'] = contacts::SP_FIRST_NAME;
		$contactsSp['lastname'] = contacts::SP_LAST_NAME;
		$contactsSp['uid'] = contacts::SP_UNID;
		
		//array ('ownername' => sys_user::SP_NAME, 'ownerguid' => sys_user::SP_GUID ) 
		// den Account Contact hollen
		$accessSelect->joinLeft ( array ('uc' => contacts::getTableNameStatic() ), 'a.' . sys_access::SP_CONTACT_ID . ' = uc.' . contacts::SP_ID, $contactsSp);
		
		$accessSelect->where ( 'a.' . sys_access::SP_GROUPID . " = ?", $groupId );
		
		$accesses = $DB->fetchAll( $accessSelect );
		return $accesses;
		
		
	}
	
	
	
	/**
	 * erstellt in der Gruppe einen Account für einen bestimmten contact
	 * @param string $groupName
	 * @param string $contactuid
	 * @param string $loginmail
	 * @param string $password
	 * @return array Liste der Accounts
	 */
	public function ActionNewAccount($groupName, $contactuid, $loginmail, $password ){
	
		require_once 'db/contact/contacts.php';
		$contactsTab = new contacts();
		$contactsRow = $contactsTab->fetchRow($contactsTab->getDefaultAdapter()->quoteInto(contacts::SP_UNID."= ?", $contactuid));
		
		if($contactsRow !== NULL){
			$contactsId = $contactsRow->offsetGet(contacts::SP_ID);
			$contactsName = $contactsRow->offsetGet(contacts::SP_FIRST_NAME);
			$contactsLastName = $contactsRow->offsetGet(contacts::SP_LAST_NAME);
		
			
			//return $contactsId;
			
			require_once 'db/sys/access/groups/sys_access_groups.php';
			$groupsTab = new sys_access_groups();
			$groupRow = $groupsTab->fetchRow($groupsTab->getDefaultAdapter()->quoteInto(sys_access_groups::SP_NAME."= ?", $groupName));
			
			if($groupRow !== NULL){
				$groupId = $groupRow->offsetGet(sys_access_groups::SP_ID);
				$groupName = $groupRow->offsetGet(sys_access_groups::SP_NAME);
				//return $groupId."-".$groupName;
			}
			
		}
		

		
		
		require_once 'db/sys/access/sys_access.php';
		
		$accessTab = new sys_access();
		$accessRow = $accessTab->createAccessRow($this->_rightsAcl->getAccess()->getId(), $contactsId, $groupId, $loginmail, $password, array());
		$accessRow->save();
		return $accessRow->toArray();
	
	
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}

?>