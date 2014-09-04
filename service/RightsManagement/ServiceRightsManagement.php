<?php

require_once 'citro/service-class/AService.php';

/**
 * die Rechte management klasse
 * Ist der Standart Service für das Management der Services
 *
 * @author Max Plank
 * @version 1.0
 *         
 */
class ServiceRightsManagement extends AService {
	
	/**
	 * Der Rechemanagement construktor
	 */
	function __construct() {
		
		parent::__construct ();
	
	}
	
	/**
	 * Liefert Alle meine Resourcen die Ich besitze und Ihrer Documentaionen
	 * 
	 * @return ArrayObject Die Userliste
	 * @param bool $onlyIsOn bool wenn auf False gesetzt giebt diese auch die zurück die im Quellcode auf false oder in der config auf false gestellt sind
	 * @citro_isOn true
	 */
	public function ActionGetMyResource($onlyIsOn = TRUE) {
			
		$ACL = $this->_MainRightManagement;
		
		$myResourcesOfAcl = $this->_rightsAcl->getMyResources ();
 	
 		require_once 'citro/ServiceResourceInstance.php';
		$ResInst = ServiceResourceInstance::getMainInstance ();
		$Docu = $ResInst->getMyDocu ( $myResourcesOfAcl, $onlyIsOn );
		
		return $Docu;
	
	}
	
	
	
	/**
	 * Liefert die Dockumentaion meiner Resourchen mit den Rechten einer bestimmten Gruppe
	 * 
	 * @param string $groupName       
	 * @return array Das Gruppen array
	 * @citro_isOn true
	 * @citro_protocollExist AesKey|sha1hasch Die Geforderten Protocolle	
	 */
	public function ActionGetMyRight($groupName) {
// 		echo "<pre>";
// 		print_r( $this->_rightsAcl->getServiceResource()->getDocu());
// 		die();
		// Setzt die Anfragende Gruppe
		//require_once 'citro/rightsmanagement/GroupParent.php';

		//$Group = new GroupParent( $groupId );
		//$Group->setAllParents();
	
// 		if(!$this->_rightsAcl->getAccess()->isAdmin()){
// 			if($Group->getCreateGuId() != $this->_rightsAcl->getAccess()->getGuId()){
// 				return FALSE;
// 			}
// 		}

		require_once 'db/sys/access/groups/sys_access_groups.php';
		$groupTab = new sys_access_groups();
		$groupSel = $groupTab->select();
		$groupSel->where("name = ?",$groupName);
		$groupRow = $groupTab->fetchRow($groupSel);
		if($groupRow === NULL) throw new Exception("Gruppe konnte nicht verifiziert werden;",E_ERROR);
		$Group = new Group($groupRow);


		// hollt die gruppen bis zur höchsten Gruppe in in einen Array mit key(roleId) und als daten die Datenbakzeile 
		//$gliste = $Group->getGroupPartent ();
		//$myParentGroups = $this->_rightsAcl->getAccess()->getMyParentGroups();
		
		require_once 'db/sys/access/groups/sys_access_groups.php';
		$group = new sys_access_groups ();
		$myParentGroups = $group->getParentWithName($groupName);

		
		$gliste = $myParentGroups->toArray();
		// Das Rechte Array hat eine liste von Rollen und jete Rolle einen Erlaubt und verboten key die dann die Resourcen beinahlten
		$gRights = array (); 
		foreach ( $gliste as  $data ) {
	
			$Role = "G_".$data['id'];
			require_once 'db/sys/access/rights/sys_access_rights.php';
			$setData = new sys_access_rights ();
				
			$RightsAllow = $setData->get ( $Role, sys_access_rights::RULETYPE_ALLOW ,FALSE);
			//print_r($RightsAllow);
			$gRights [$Role] ["Allow"] = $RightsAllow;
				
			$RightsDeny = $setData->get (  $Role, sys_access_rights::RULETYPE_DENY,FALSE );
			$gRights [$Role] ["Deny"] = $RightsDeny;
				
			unset($data["id"]);
			unset($data["user_create"]);
			unset($data["user_edit"]);
			unset($data["visibil"]);
			unset($data["deleted"]);
			unset($data["lft"]);
			unset($data["rgt"]);
				
			$gRights [$Role] ["Data"] = $data;
		}
// 		echo "<pre>";
// 		print_r($gRights);		
		
		
		foreach ( $this->_rightsAcl->getServiceResource()->getMyDocu( $this->_rightsAcl->getMyResources (),TRUE) as $servName => $servData ) {
			
			// abarbeitung der Serviceresource 
			$myDocu [$servName] ["rights"] = $this->getServiceReigthData ( $servName, $Group, $gRights );
			
			foreach ( $servData ["ACTIONS"] as $actName => $actData ) {
				// Abarbeiten der Actionsresource
				$actionName = $servName . ServiceResource::SERVICEACTIONSEPARATOR . $actName;
				$myDocu [$servName] ["ACTIONS"] [$actName] ["rights"] = $this->getServiceReigthData ( $actionName, $Group, $gRights );
			}
		}
		
		return $myDocu;
	}
	
	
	
	/**
	 * Liefert ein Array in dem die Rechte für eine Resource Beschrieben werden
	 * @param string $name der Resource
	 * @param Group $group Die angefragte Gruppe
	 * @param array $gRights rechte aus der Datenbank und deren daten
	 * @return Ambigous <string, multitype:multitype: string number unknown Ambigous <NULL, number> Ambigous <> >
	 */
	private function getServiceReigthData($name,Group $group, $gRights) {
	
		// Setzt die Standartdaten
		$Data = array ();
		$Data ['isSet'] = "NOT_SET";
		$Data ['isSetInGroup'] = "NOT_SET";
		$Data ['deleteId'] = 0;
		// setzt die Gruppen Id
		$Data ['mainGroupId'] = $group->getId();
		
		
	
		if (array_key_exists ( $group->getRoleId(), $gRights )) {
				
			if (in_array ( $name, $gRights [$group->getRoleId()] ["Allow"] )) {
				$Data ['isSetInGroup'] = "ALLOW_SET";
				$rightsIdsWithName = array_flip ( $gRights [$group->getRoleId()] ["Allow"] );
				$Data ['deleteId'] = $rightsIdsWithName [$name];
					
			}
	
			if (in_array ( $name, $gRights [$group->getRoleId()] ["Deny"] )) {
	
				$Data ['isSetInGroup'] = "DENY_SET";
				$rightsIdsWithName = array_flip ( $gRights [$group->getRoleId()] ["Deny"] );
				$Data ['deleteId'] = $rightsIdsWithName [$name];
			}
	
		}
	
		$Data ['servName'] = $name;
		$Data ['trend'] = array ();
	
		foreach ( $gRights as $groupIs => $resousList ) {
				
				
			// Wenn nicht dann Prüfen ob erlaubt
			if (in_array ( $name, $resousList ["Allow"] )) {
	
				$Data ['isSet'] = "ALLOW_SET";
	
				$Data ['trend'] [$groupIs] = $resousList ["Data"];
				$Data ['trend'] [$groupIs] ['isSet'] = "ALLOW_SET";
					
			}
				
			// Zuerst Prüfen ob es in dieser Gruppe verboten ist
			if (in_array ( $name, $resousList ["Deny"] )) {
				$Data ['isSet'] = "DENY_SET";
				$Data ['trend'] [$groupIs] =  $resousList ["Data"];
				$Data ['trend'] [$groupIs] ['isSet'] = "DENY_SET";
					
			}
		}
	
		return $Data;
	}
	
	


	
	/**
	 * Setzt Rechte für eine Rolle
	 *
	 * @return ArrayObject
	 * @param string $art DELETE|ALLOW|DENY
	 * @param string $resource Die die Rechte bekommen soll
	 * @param string $role Die Gruppenrolle
	 * @citro_isOn true
	 */
	public function ActionSetRightGroup($art,$resource,$role) {
	
	
		$erg = FALSE;
	
		require_once 'db/sys/access/rights/sys_access_rights.php';
		$rightTab = new sys_access_rights ();
// 	var_dump($art);
// 	var_dump($resource);
// 	var_dump($groupname);die();
		switch ($art){
			case "ALLOW" :	$erg = $rightTab->set($resource, $role, "ALLOW");
			break;
			case "DENY" :	$erg = $rightTab->set($resource, $role, "DENY");
			break;
			case "DELETE" :	$erg = $rightTab->deleteResource($resource, $role);
			break;
			default: break;
				
		}
		return $erg;
	}


}

?>