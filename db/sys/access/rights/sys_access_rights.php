<?php
require_once ('citro/DBTable.php');
/**
 * class sys_acerigth
 *
 * Description for class sys_acerigth
 *
 * @author :
 *        
 */
class sys_access_rights extends DBTable {
	
	protected $_TableName = "access_rights";
	const SP_ID = "id";
	const SP_E_DATA = "edata";
	const SP_RESOURCE_NAME = "resource";
	const SP_ROLE_KEY = "role"; 
	const SP_RULE_TYPE = "rule_type";

	const RULETYPE_DENY = "DENY"; // in DB = 2
	const RULETYPE_ALLOW = "ALLOW"; // in DB = 1
	
	/**
	 * Schreibt die Resourcen in die Datenbank
	 *
	 * @param $Resource string 	 Die Resource
	 * @param $InquirerId Int 	 Die Id des Anfragenden
	 * @param $Inquirer Int    	 Die Anfragende Also Gruppe(GROUP) oder User(USER)
	 * @param $RuleType Int    	 Ob Service Erlaubt (ALLOW) oder Nicht erlaubt(DENY) ist
	 * @return int Die betroffene oder neu eingeschriebene Id;
	 *        
	 */
	public function set($resource, $role, $ruleType) {

		// Prüfen des ruleType
		$ruleTypeVal = NULL;
		
		if($ruleType === self::RULETYPE_ALLOW )	$ruleTypeVal = 1;
		
		if($ruleType === self::RULETYPE_DENY )	$ruleTypeVal = 2;
		
		if($ruleTypeVal === NULL)return FALSE;
		

	
		// Prüfen der Role
		if(($role = $this->testRole($role)) === FALSE)return FALSE;
		
		// Prüfen der Resource
		if(($resource = $this->testResource($resource)) === FALSE)return FALSE;

		// Löschen falls vorhanden
		$this->deleteResource($resource, $role);
		// Neu einschreiben
		$writeDate = array (
				self::SP_E_DATA => $this->getDateTime(), 
				self::SP_RESOURCE_NAME => trim ( $resource ),
				self::SP_ROLE_KEY => trim ( $role ), 					
				self::SP_RULE_TYPE => $ruleTypeVal
				 );
		

		$Id = $this->insert ( $writeDate );
	
		return TRUE;
	}
	
	public function testResource($res){
		// Prüfen der Resource
		if(!is_string($res))return FALSE;
		if(strlen ( $res ) > 255)return FALSE;
		$res = trim($res);
		return $res;
	}
	public function testRole($role){
		if(!is_string($role))return FALSE;
		if(strlen ( $role ) > 150)return FALSE;
		$role = trim($role);
		return $role;
	}
	
	public function deleteResource($resource, $role){
		
		// Prüfen der Role
		if(($role = $this->testRole($role)) === FALSE)return FALSE;
		
		// Prüfen der Resource
		if(($resource = $this->testResource($resource)) === FALSE)return FALSE;
				
 		$whereRes = $this->getAdapter()->quoteInto(self::SP_RESOURCE_NAME . " = ?", $resource);
 		$whereRol = $this->getAdapter()->quoteInto(self::SP_ROLE_KEY . " = ?", $role);

		parent::delete($whereRes." AND ".$whereRol);
	}

	/**
	 * Giebt eine einzelene zeile also ein Gesetztes Recht zurück
	 * 
	 * @param $Id unknown_type       	
	 * @return Ambigous <multitype:, mixed>
	 */
	public function getOne($Id) {
		$result = $this->_DBCon->fetchRow ( 'SELECT * FROM ' . $this->getDBTableName () . ' WHERE ' . self::SP_ID . ' = ' . $Id );
	
		return $result;
	
	}
	
	public function getResource($Resource, $Inquirer = NULL, $InquirerId = NULL, $RuleType = NULL) {
		
		$select = $this->_DBCon->select ();
		
		// Hinzuf�gen einer FROM Bedingung
		$select->FROM ( $this->getDBTableName (), "*" );
		
		$select->where ( self::SP_RESOURCE_NAME . " = ?", $Resource );
		$select->where ( self::SP_INQUIRER . " = ?", $this->getInquirerVal ( $Inquirer ) );
		$select->where ( self::SP_INQUIRER_ID . " = ?", $InquirerId );
		if ($RuleType !== NULL) {
			$select->where ( self::SP_RULE_TYPE . " = ?", $this->getRuleTypeVal ( $RuleType ) );
		}
		
		// echo $select->__toString();
		$back = $select->query ();
		$Data = $back->fetchAll ();
		
		return $Data;
	
	}
	
	private function getRuleTypeVal($RuleType) {
		
		switch ($RuleType) {
			case self::RULETYPE_DENY :
				return 2;
				break;
			case self::RULETYPE_ALLOW :
				return 1;
				break;
			default :
				throw new Exception ( "Probleme beim RuleTypes Übergeben werden muss '" . self::RULETYPE_DENY . "' oder '" . self::RULETYPE_ALLOW . "'", E_ERROR );
		
		}
	}
	

	
	/**
	 * Giebt die Resourcen Namen der Gruppe zurück
	 *
	 * @param $Id int Die GruppenId
	 * @param $AllSpalt bool wenn auf true gestellt dann werden alle Spalten mit ausgegeben	wie(Id,gruppe,...)
	 * @return array Array mit allen Resourcennamen
	 *        
	 */
	public function get($role, $RuleType = NULL, $AllSpalt = TRUE) {
		
		$select = $this->_db->select ();
		
		// Hinzufügen einer FROM Bedingung
		$select->FROM ( "sys_access_rights" );
		
// 		// abfrage der Id (gruppe oder user)
// 		if (is_array ( $InquirerId )) {
// 			$select->where ( self::SP_INQUIRER_ID . " IN (?)", $InquirerId );
// 		} else {
// 			$select->where ( self::SP_INQUIRER_ID . " = ?", $InquirerId );
// 		}
//		$select->where ( self::SP_INQUIRER . " = ?", $this->getInquirerVal ( $Inquirer ) );
		$select->where ( self::SP_ROLE_KEY . " IN (?)",  $role );
		
		if ($RuleType != NULL && ($RuleType === self::RULETYPE_ALLOW || $RuleType === self::RULETYPE_DENY)) {
			$select->where ( self::SP_RULE_TYPE . " = ?", $this->getRuleTypeVal ( $RuleType ) );
		}
		// echo "<pre>";
		// echo $select->__toString();
		$back = $select->query ();
		
		$Data = $back->fetchAll ();
		if ($AllSpalt === false) {
			$Data = $this->toList ( $Data, sys_access_rights::SP_RESOURCE_NAME, false, self::SP_ID );
		}
		// print_r($Data);
		return $Data;
	}
}

?>