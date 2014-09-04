<?php

require_once 'Zend/Acl.php';
require_once 'Zend/Acl/Role.php';
require_once 'Zend/Acl/Resource.php';



/**
 * class UrserRightsAcl
 *
 * Erstellt das Rechte system
 *
 * @author :
 *        
 */
class RightsAcl extends Zend_Acl {

	

	const SHORTCUT_ACCESS = "A_";
	const SHORTCUT_GROUP = "G_";
	
	private $_confServName = null;
	private $_ResourcenA = null;
	private $_lastCreateDate = NULL;


	
	/**
	 * @var Access
	 */
	private $_access = NULL;
	
	/**
	 * @var Group
	 */
	private $_groupParent = NULL;
	
	/**
	 * @var ServiceResource
	 */
	private $_resource = NULL;
	

	
	/**
	 * @var Zend_Config
	 */
	private $_config = NULL; 
	
	public function setConfig(Zend_Config $conf) {
		self::$_config = $conf;
	}
	

	/**
	 * @return ServiceResource
	 */
	public function getServiceResource(){
		return $this->_resource;
	}
	
	
	/**
	 * @return Access der User
	 */
	public function getAccess(){
		return $this->_access;
	}
	
	

	/**
	 * @return GroupParent die Gruppe Und Ihre ElternGruppen
	 */
	public function getGroupParent(){
		return $this->_groupParent;
	}

	/**
	 * @return Group die Gruppe 
	 */
	public function getGroup(){
		return $this->_groupParent;
	}

	/**
	 * Liefert die Zeit wann das letzte mal die Reche erstellt wurden
	 * Dieses ist notwendig wenn das Objekt gecacht wurde
	 * @return string DateTime
	 */
	public function getLastCreateDate(){
		return $this->_lastCreateDate;
	}

	
	
	/**
	 * Inizialisiert das Rechtesystem
	 * 
	 * @param ServiceResource $resourc 
	 * @param Access $access
	 * @param GroupParent $group
	 */
	function __construct(ServiceResource $resourc, Access $access, GroupParent $groupParent) {
		
		// Setzt den User
		$this->_access = $access;
		$this->_groupParent = $groupParent;
		$this->_resource = $resourc;
	
	
	
		// Die gesamten Services(resourcen) mit ihren Actionen(methoden) als zweigimensionales Array
		$this->_setResourcen ( $resourc->getResourcenArray() );
			
		// setzt die Rollen
		$this->_setRoles ( $this->_access, $groupParent );

			
		// setzt die Rechte der Gruppen und seiner Unterliegenten gruppen
		if($groupParent !== NULL){
			$this->_setRechtGroup ( $groupParent );
		}
		// Setzt die Rechte des Accesses
		$this->_setRechte (  $access->getRoleId() );
		
		require_once 'citro/DBTable.php';
		$this->_lastCreateDate = DBTable::getDateTime();
		
	}
	

	
	/**
	 * Giebt alle meine Resourcen zurück die Ich besitze
	 *
	 * @return multitype:unknown
	 */
	public function getMyResources($toList = FALSE) {
	
		// Prüfen
		if(!is_array($this->_resource->getResourcenArray()))
			return NULL;

		
		$AllowResList = array ();
		$AllowRes = array ();
		// Durchläuft alle Services aus den Resourcen Objekt
		foreach ($this->_resource->getResourcenArray() as $servName => $actionList){
			
			if($this->isResourceAllowed ( $servName )){
				$AllowResList[$servName] = array();
				$AllowRes[] = $servName;
			}
			// Prüfen ob Actionen vorhanden sind
			if(is_array($actionList)){
				// Durchläuft alle Actionen aus den Service Resourcen Objekt
				foreach ($actionList as $action){
					
					if($this->isResourceAllowed ( $servName."_".$action )){
						$AllowResList[$servName][] = $action;
						$AllowRes[] = $servName."_".$action;
					}
				}
			}
			
		}
		
		
		if($toList){
			return $AllowResList;
		}else {
			return $AllowRes;
		}
	}

	

	
	/**
	 * Teste ob die Resource des angemelteten Users erlaubt ist
	 *
	 * @param $resourceName string Den Sourcename
	 * @param $delimiter string Der Trenner der Resourcen
	 * @return bool Ob erlaubt true/false
	 *        
	 */
	public function isResourceAllowed($resourceName, $delimiter = "_") {
		
		// Prüfen auf vorhandensein des Users
		if(!$this->_access instanceof Access)
			return FALSE;
	
		// Bei Admins ist immer Alles erlaubt
		if ($this->_access->isAdmin ()) 
			return TRUE;

		// Testen auf Rolle Allow
		if( !$this->hasRole ( $this->_access->getRoleId() ) )
			return FALSE;
		
		
		

		
		// zerlegen der Resource da sich die Resource unterteilen kann in Service_Action_Parmeter(eventuell)	
		$resSplitt = explode($delimiter, $resourceName);
		
		$addRes = "";
		foreach ($resSplitt as $res){
			
			// Zusammenschreiben des Resoursenteils mit den falls schon vorhandenen Reseoursenteil der am ende der schleife hinzugefügt wurde
			$resName = $addRes.$res;

			// Prüfung auf vorhanden sein der Resource
			if ($this->has ( $resName ) ) {
				
				// Ist Erlaubt Resource nachfragen bei Zend_Acl
				$Allowed = $this->isAllowed ($this->_access->getRoleId(), $resName );
	
				// Falls Nicht erlaubt sofort Abbruch zb wenn Service nicht erlaubt dann auch keine Actionen erlauben
				if($Allowed === FALSE)
					return FALSE;
				// Test ob es die gesamte Resourcen anfrage war dann die erlaubniss zurückgeben
				if($resName === $resourceName)
					return $Allowed;
				
				
			}else{
				// Da Resource nicht vorhanden 
				return FALSE;
			}
		
			// hinzufügen des Resourcen Teils 
			$addRes = $addRes.$res.$delimiter ;
			
		}		
		return FALSE;
	}
	
	

	
	/**
	 * Setzt der Resourcen
	 *
	 * @param array $resourcenA  Ein zweidimensionales Array mit seinen Services und darunder liegenden Actionen
	 *       	
	 */
	private function _setResourcen($resourcenA) {

		// Testen auf array
		if(!is_array($resourcenA))
			return FALSE;
		
		foreach ( $resourcenA as $ServKey => $ActionKeyArray ) {
		
			// Setzten der Serviceresource
			$ServResource = new Zend_Acl_Resource($ServKey);
 			$this->addResource($ServResource);
 			
 			// Prüfen auf Actionsresourcen
 			if(is_array($ActionKeyArray) ){
	
 				foreach ($ActionKeyArray as $action){
 					// erstellen des Actionsresourcennamens
					$resActionKey = $ServKey."_".$action;
					
					// Setzen der Actionsresource
					$actionResource = new Zend_Acl_Resource($resActionKey);
					
					// nicht als unterliegende Resource hinzufügen da dies dazu führt das wenn der Service erlaubt ist auch alle seine Actionen eraubt sind
					//$this->addResource($actionResource,$ServResource);
					$this->addResource($actionResource);
 				}
			}
	
		}

			
	
	}
	
	/**
	 * Setzt die Rollen User,Gruppe und seiner Elterngruppen
	 * 
	 * @param $UserId integer       	
	 * @param $GroupenHira Group       	
	 */
	private function _setRoles(Access $access, GroupParent $GroupenHira = NULL) {
	
		// Löscht erst mal alle Rollen falls noch welche vorhanden waren
		$this->_roleRegistry = NULL;
		
		if($GroupenHira !== NULL){
			// setzt die Rolle der Gruppen
			$this->_setRoleGroup ( $GroupenHira );
			
			// Setzt die Rolle des Accesses
			$this->addRole ( new Zend_Acl_Role ( $access->getRoleId() ), $GroupenHira->getRoleId ()   );
		}else{
			// Setzt die Rolle des Accesses
			$this->addRole ( new Zend_Acl_Role ( $access->getRoleId() ) );
		}
		
	
	
	}
	
	/**
	 * setzt die Rollen der Gruppe und Ihrer Eltern gruppen
	 * Diese Mehtode wird Recursiv aufgerufen wenn es eine Elterngruppe in dieser Gruppe giebt
	 * 
	 * @param Group $Group        	
	 */
	private function _setRoleGroup(GroupParent $Group) {
		
		// Prüfen ob eine Elterngruppe vorhanden ist
		if ($Group->isParents ()) {
			
			// Recursiv die Elterngruppe holen und setzen
			$partentGroup = $Group->getParents ();

			$this->_setRoleGroup ( $partentGroup );
			
			if (! $this->hasRole (  $Group->getRoleId()  )) {
			
				$this->addRole ( new Zend_Acl_Role ( $Group->getRoleId()  ),$partentGroup->getRoleId()  );
			}
		} else {
			
			if (! $this->hasRole ( $Group->getRoleId() )) {
			
				$this->addRole ( new Zend_Acl_Role ( $Group->getRoleId()  ) );
			}
		}
	}
	
	
	/**
	 * Setzt die Rechte der Gruppe und Ihrer Eltern gruppen
	 * Diese Mehtode wird Recursiv aufgerufen wenn es eine Elterngruppe in dieser Gruppe giebt
	 * 
	 * @param Group $group
	 */
	private function _setRechtGroup(GroupParent $group) {
		
		// Prüfen ob die Gruppe noch eine Darüberliegende Gruppe besitzt
		if ($group->isParents ()) {	
			$this->_setRechtGroup ( $group->getParents () );
		}
		
		// Prüfen ob die Gruppe eingeschaltet(sichtbar gemarkert)
		if ($group->getIsOn ()) {
		
			$this->_setRechte ($group->getRoleId() );
			
			$this->_counter++;
		
		}
		
	}


	/**
	 * Setzen der Rechte in der Acl
	 * @param unknown_type $ruleType 1 erlaubt 2 verboten
	 * @param unknown_type $resource Die Resource Service_Action
	 * @param unknown_type $role Rolle zb. G_23
	 */
	private function _setRechte($role) {
		
		require_once 'db/sys/access/rights/sys_access_rights.php';
		$rightsTab = new sys_access_rights ();
		
		$servSel = $rightsTab->select ();
		$servSel->where ( sys_access_rights::SP_ROLE_KEY . " = ?", $role );// RollenId
		$servSel->order(sys_access_rights::SP_E_DATA." ASC"); // DESC
		$rightsRowSet = $rightsTab->fetchAll ( $servSel );
	
		
		/* @var $tubleRow Zend_Db_Table_Rowset_Abstract */
		foreach ($rightsRowSet  as $tubleRow){
		
			$serviceName = $tubleRow->offsetGet(sys_access_rights::SP_RESOURCE_NAME);
			$ruleType = $tubleRow->offsetGet(sys_access_rights::SP_RULE_TYPE);
		
			// prüft noch mal ob die Resource überhaupt exestiert in den Php Dateien
			// der serviceName kann ja vorher schon mal in die Datenbank eingeschrieben sein und dann nachher wurde er aus den Dateien entfernt 
			if($this->_resource->ResourceExist($serviceName)){
			
				if($ruleType === "1"){
			
					$this->allow($role,$serviceName);
				}else {
					$this->deny($role,$serviceName);
				}
			}else {
				$rightsTab->delete( sys_access_rights::getDefaultAdapter()->quoteInto( sys_access_rights::SP_RESOURCE_NAME." = ?", $serviceName) );
				require_once 'citro/error/LogException.php';
				require_once 'citro/error/ErrorCodes.php';
				new LogException(new ErrorCodes(ErrorCodes::APP_ACL, "NOT", 1), "Der Service (".$serviceName.") wurde aus der Datenbank entfernt da er in den Service-php-dateien nicht exestiert!",E_NOTICE);
				//throw new Exception("Service (".$serviceName.") muss auch aus der Datenbank entfernt werden",E_ERROR);
			}
		
		
		}

		

	}

	

	


}

?>