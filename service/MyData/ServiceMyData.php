<?php

require_once 'citro/service-class/AService.php';

/**
 * Ist für die verwaltung der eigenen Daten
 *
 * @author Max Plank
 * @version 1.0
 *          @citro_isOn true
 *         
 */
class ServiceMyData extends AService {
	
	/**
	 * Der User construktor
	 */
	function __construct() {
		
		parent::__construct ();
	
	}
	

	
	
	
	
	
	
	/**
	 * so ist für das umschreiben der logindaten verantwortlich
	 *
	 * @param string $loginName
	 * @param string $loginPass Min 8 Zeichen Max 45 Zeichen. Als Rohdaten übersenden verschlüsselt wird in der Action
	 * @return boolean
	 * @throws Exception Username schon Vorhanden
	 * @throws Exception Username nicht korrekte Länge
	 * @throws Exception Password nicht korrekte Länge
	 * @citro_protocollExist AesKey|sha1hasch Die Geforderten Protocolle
	 */
	public function ActionUpdateLogindata($loginName = NULL, $loginPass = NULL) {
		
		if(empty($loginName))
			$loginName = NULL;
		
		if(empty($loginPass))
			$loginPass = NULL;
		
		require_once 'db/sys/access/sys_access.php';
		$userTab = new sys_access();
		$userSelect = $userTab->select ();
		$userSelect->where ( sys_access::SP_GUID . " = ?", $this->_rightsAcl->getAccess()->getGuId() );
		
		// die($this->_MainUser->getId());
		$userRow = $userTab->fetchRow ( $userSelect );
		
		if ($userRow !== NULL) {
			
			if ($loginName !== NULL) {
		
				if (!$loginName = $userTab->testLoginname ( $loginName ))
					throw new Exception("Der Username hat nicht die korrekte Länge",E_ERROR);
		
				if ($userTab->testExistLoginName ( $loginName ))
					throw new Exception("Der Username Ist schon vorhanden",E_ERROR);
				
				
				$userRow->offsetSet ( sys_access::SP_LOGINNAME, $userTab->mysql_prep ( $loginName ) );
			}

			if($loginPass !== NULL){
				$testloginPass = sys_access::testPassword($loginPass);
				if($testloginPass === FALSE ) 
					throw new Exception("Das Password hat nicht die korrekte Länge",E_ERROR);
				
				$userRow->offsetSet ( sys_access::SP_PASSWORD, $userTab->passwordCreateForDB ( trim( $loginPass) ) );
				$userRow->offsetSet ( sys_access::SP_PASSWORDBLANK,  trim( $loginPass)  );
			}
			
			

			if($loginName !== NULL || $loginPass !== NULL)
			$userRow->save ();
				
			// Löscht das Rechtemanagement für neu erstellung
			require_once 'citro/RightsManagement.php';	
			RightsManagement::deletetAccessCach($this->_rightsAcl->getAccess());
			
			return TRUE;
		}
		
		return FALSE;
	
	}
	
	
	/**
	 * Erstellt einen Neuen AesKey des acesses
	 *
	 * @citro_isOn true
	 */
	public function ActionCreateNewAESKey() {
		
		//todo Muss noch per Email an den User Gesendet werden
		require_once 'db/sys/access/sys_access.php';
		$newAESKey = sys_access::createAesKey();
		$accessTab = new sys_access();
		$accessTab->update(
				array(sys_access::SP_AESKEY=>$newAESKey), 
				$accessTab->getDefaultAdapter()->quoteInto(
						sys_access::SP_ID." = ?", 
						$this->_rightsAcl->getAccess()->getId()));   
		return $newAESKey;
	}
	
	
	/**
	 * Ist für das Update der eigenen Daten
	 *
	 * @param string $hashKey 
	 * @param array $data 
	 * @citro_isOn true
	 */
	public function ActionUpdateData($hashKey, $data) {
	
		$myContactId = $this->_rightsAcl->getAccess()->getContactId();
		$db = DBConnect::getConnect();
		$sqlContact = $db->select()->from("contacts",array("uid"))->where("id = ?",$myContactId) ;
		$myContactUID = $db->fetchOne($sqlContact);
		
		// Inizialiseren eines ServiceContactUpdates das die Schnitstelle DBIUpdate enthällt
		require_once 'service/Contact/ServiceContactUpdateHelper.php';
		$servContUpd = new ServiceContactUpdateHelper($myContactUID);

		require_once 'citro/update/ChronologicalFactory.php';
		$dbupdateReposetory = new ChronologicalFactory(
				"MainContact",
				$this->_rightsAcl->getAccess()->getId(),
				 DBConnect::getConnect(),$servContUpd );
				
		$isOk = $dbupdateReposetory->update($myContactUID, $hashKey, $data);
		return $isOk;
		
	}
	

	
	
	
	/**
	 * Giebt einen einzelnen User zurück
	 * 
	 * @return array boolean der rückgabe wert
	 * @citro_isOn true
	 */
	public function ActionGetToUpdate() {
		
		$myContactId = $this->_rightsAcl->getAccess()->getContactId();
		$db = DBConnect::getConnect();
		$sqlContact = $db->select()->from("contacts",array("uid"))->where("id = ?",$myContactId) ;
		$myContactUID = $db->fetchOne($sqlContact);

		
		// Inizialiseren eines ServiceContactUpdates das die Schnitstelle DBIUpdate enthällt
		require_once 'service/Contact/ServiceContactUpdateHelper.php';
		$servContUpd = new ServiceContactUpdateHelper($myContactUID);

		// Inizialisieren des Update Reposetorys
		require_once 'citro/update/SelectFactory.php';
		$dbupdateReposetory = new SelectFactory( DBConnect::getConnect(),$servContUpd );
		$dbupdateReposetory->toUpdate();
		
		
		require_once 'citro/update/UpdateHelpFunc.php';
		//$backArray = array("title","first_name","first_add_name","last_name","affix_name","uid","edata","vdata");
		//$backData = UpdateHelpFunc::getColumnToUpdate($dbupdateReposetory->getToUpdate(), $backArray);
		$backData = UpdateHelpFunc::insertHashKey($dbupdateReposetory);
	
		
		return $backData;
			
	}
	
	
	
	
	/**
	 * Liefert die Dockumentaion meiner Resourcen mit den Rechten einer bestimmten Gruppe
	 *
	 * @param bool $toList Ob die rückgabe in einer Flachen Array oder verschachtelten Array erfolgen soll
	 * @return array Das Gruppen array
	 * @citro_isOn true
	 * @citro_protocollExist AesKey|sha1hasch Die Geforderten Protocolle
	 */
	public function ActionGetMyRight($toList = FALSE) {
	

		// hollt die gruppen bis zur höchsten Gruppe in in einen Array mit key(roleId) und als daten die Datenbakzeile
		$myParentGroups = $this->_rightsAcl->getAccess()->getMyParentGroups();
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
			unset($data["pid"]);
			unset($data["user_create"]);
			unset($data["user_edit"]);
			unset($data["visibil"]);
			unset($data["deleted"]);
				
			$gRights [$Role] ["Data"] = $data;
		}
		return $gRights;
	}

}

?>