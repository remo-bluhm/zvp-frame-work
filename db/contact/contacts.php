<?php
require_once 'citro/DBTable.php';

class Contacts extends DBTable {
	
	protected $_name = 'contacts';

	const GUEST = "GUEST";
	const HIRER = "HIRER";
	const ADJUSTOR = "ADJUSTOR";
	const NOTSET = "NOTSET";
	
	
	const SP_ID = "id";
	const SP_UNID = "uid";
	
	const SP_DATA_CREATE = "edata";
	const SP_DATA_EDIT = "vdata";
	const SP_ACCESS_CREATE = "access_create";
	const SP_ACCESS_EDIT = "access_edit";
	const SP_DELETE = "deleted";
	
	const SP_TYPE = "type";
	const SP_TITLE = "title_name";
	const SP_FIRST_NAME = "first_name";
	const SP_FIRST_ADD_NAME = "first_add_name";	
	const SP_LAST_NAME = "last_name";
	const SP_AFFIX_NAME = "affix_name";
	
	const SP_FIRMA = "firma";
	const SP_FIRMA_POSITION = "position";


	const SP_ADRESS_ID = "main_contact_address_id";
	const SP_EMAIL_ID = "main_contact_email_id";
	const SP_PHONE_ID = "main_contact_phone_id";


	private $_insertData = array();
	
	public function clearData(){
		$this->_insertData = array();
	}


	public function setType($value){
		$result = self::testType($value);
		if($result !== NULL)$this->_insertData[self::SP_TYPE] = $result;
		return $result;
	}
	public function setTitle($value){
		$result = self::testTitle($value);
		if($result !== NULL)$this->_insertData[self::SP_TITLE] = $result;
		return $result;
	}
	public function setFirstName($value){
		$result = self::testFirstName($value);
		if($result !== NULL)$this->_insertData[self::SP_FIRST_NAME] = $result;
		return $result;
	}
	public function setFirstAddName($value){
		$result = self::testFirstAddName($value);
		if($result !== NULL)$this->_insertData[self::SP_FIRST_ADD_NAME] = $result;
		return $result;
	}
	public function setLastName($value){
		$result = self::testLastName($value);
		if($result !== NULL)$this->_insertData[self::SP_LAST_NAME] = $result;
		return $result;
	}
	public function setAffixName($value){
		$result = self::testAffixName($value);
		if($result !== NULL)$this->_insertData[self::SP_AFFIX_NAME] = $result;
		return $result;
	}
	public function setAccessCreateId($id){
		$result = DBTable::testId($id);
		if($result !== FALSE)$this->_insertData[self::SP_ACCESS_CREATE] = $result;
		return $result;
	}
	public function setAccessEditId($id){
		$result = DBTable::testId($id);
		if($result !== FALSE)$this->_insertData[self::SP_ACCESS_EDIT] = $result;
		return $result;
	}
	public function setMainAdressId($id){
		$result = DBTable::testId($id);
		if($result !== FALSE)$this->_insertData[self::SP_ADRESS_ID] = $result;
		return $result;
	}
	public function setMainPhoneId($id){
		$result = DBTable::testId($id);
		if($result !== FALSE)$this->_insertData[self::SP_PHONE_ID] = $result;
		return $result;
	}
	public function setMainEmailId($id){
		$result = DBTable::testId($id);
		if($result !== FALSE)$this->_insertData[self::SP_EMAIL_ID] = $result;
		return $result;
	}
	
	
	/**
	 * Enthällt die Uid wenn ich vorher ein Insert oder Select aufgerufen habe
	 * @return string|NULL
	 */
	public function getUid(){
		if(array_key_exists(self::SP_UNID, $this->_insertData)) return $this->_insertData[self::SP_UNID];
		return NULL;
	}
	
	public static function testUID($value){
		$value = (string)$value;
		if(strlen($value) < 20 &&  strlen($value) > 12)return $value;
		return NULL;
	}
	
	public static function testTitle($value){
		if(strlen($value) > 50) return NULL;
		return $value;
	}
	public static function testFirstName($value){
		if(is_string($value) && strlen($value) < 200 &&  strlen($value) > 2)return $value;
		return NULL;
	}
	public static function testFirstAddName($value){
		if(is_string($value) && strlen($value) < 50 &&  strlen($value) > 0)return $value;
		return NULL;
	}
	/**
	 * Testet den Nachnamen
	 * @param string $value Nachname
	 * @return string|boolean Im Fehlerfall FALSE
	 */
	public static function testLastName($value){
		if(is_string($value) && strlen($value) < 150 &&  strlen($value) > 2)return $value;
		return NULL;
	}
	public static function testAffixName($value){
		if(is_string($value) && strlen($value) < 20 &&  strlen($value) > 0)return $value;
		return NULL;
	}

 	/**
 	 * Prüft auf den Type GUST,HIRER,ADJUSTOR
 	 * @param string $value
 	 * @return string falls dieser nicht richtig ist dann NOTSET
 	 */
 	public static function testType($value){

 		$value = strtoupper($value);
 		if($value === self::GUEST) return self::GUEST;
 		if($value === self::HIRER) return self::HIRER;
 		if($value === self::ADJUSTOR) return self::ADJUSTOR;
 		return self::NOTSET;
 	}
 	

	
 	
 	public function updateDataFull($uid, $data = array()){
 		if(!is_array($data))$data = array();
 		
 		if(self::testUID($uid) !== NULL){
 		
 			if(array_key_exists("name_title",$data)) 		$this->setTitle($data["name_title"]);
 			if(array_key_exists("name_first",$data)) 		$this->setFirstName($data["name_first"]);
 			if(array_key_exists("name_firstadd",$data)) 	$this->setFirstAddName($data["name_firstadd"]);
 			if(array_key_exists("name_last",$data)) 		$this->setLastName($data["name_last"]);
 			if(array_key_exists("name_affix",$data)) 		$this->setAffixName($data["name_affix"]);
 			
 			$where = $this->getAdapter()->quoteInto( self::SP_UNID."= ?", $uid);
 			$this->update($this->_insertData, $where);
 		}
 	}
 	
 	
	/**
	 * Einschreiben der gesetzten Daten mit prüfung der Daten die vorher mit den set Methoden gesetzt wurden
	 * @param integer $accessId
	 * @param string $lastName Der Nachname als String
	 * @return NULL|integer Bei erfolgreichen einschreiben die Id des Contactes ansonste NULL
	 */
	public function insertDataFull($accessId,$lastName,$data=array()){
	
		// Die Pflichtparameter
		$this->setAccessCreateId($accessId);
		$this->setAccessEditId($accessId);
		$lastName = $this->setLastName( $lastName);
		
		// testen auf die Pflichtparameter
		if($lastName !== NUll){
		
			// Setzen des Types
			array_key_exists("type",$data)?$this->setType($data["type"]):$this->setType(self::NOTSET);
						
			//name setzen
			if(array_key_exists("name_title",$data)) 		$this->setTitle($data["name_title"]);
			if(array_key_exists("name_first",$data)) 		$this->setFirstName($data["name_first"]);
			if(array_key_exists("name_firstadd",$data)) 		$this->setFirstAddName($data["name_firstadd"]);
			if(array_key_exists("name_affix",$data)) 		$this->setAffixName($data["name_affix"]);
				
			// setzen der UID
			$newUnId = NULL;
			$setInsert = TRUE;
			while ( $setInsert )
			{
				$newUnId = self::generateUnId("bt");
				$allTubles = $this->findUid($newUnId,FALSE);
			
				if($allTubles === NULL){
					$this->_insertData[self::SP_UNID] = $newUnId;
					$setInsert = FALSE;	break;
				}else{
					//@TODO hier kann noch ein logging eingebaut werden wenn die Id schon mal gefunden wurde um so ein überblic zu behalten
				}
			}

 			//########### Einschreiben der Daten
 			return $this->insert($this->_insertData);			
		}
		return NULL; // da Plichtparameter LastName nicht gesetzt wurde			
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	public function insert($data){
		$data[self::SP_DATA_CREATE] = DBTable::DateTime ();
		$data[self::SP_DATA_EDIT] = DBTable::DateTime ();
		return  parent::insert($data);
	}
	
	public function initialization($contactUid){
		
		$select = $this->select();
		
		
	}
	
	
	
	
	
	
	
	
	
	public function update($date,$where){
		$date[self::SP_DATA_EDIT] = DBTable::DateTime ();
		parent::update($date, $where);
	}
	
	
	/**
	 * Erstellt eine eindeutige Id des Contactes
	 * @param string $prefix eindeutigen Bezeichner der Firma oder der Datenbank
	 * @return string
	 */
	public static function generateUnId($prefix){
		return uniqid ($prefix."-");
	}
	
	/**
	 * Liefert die Uid des Contactes 
	 * Hierbei wird eine neue Datenbank abfrage gestellt
	 * @param integer $contactid
	 * @return string
	 */
	public function getUidFromDB($contactid){
		$value = DBConnect::getConnect()->fetchOne("select uid from contacts where id=$contactid");
		return $value;
	}
	
	/**
	 * Findet ein Contact anhand seiner Uid und liefertseine Id
	 * 
	 * @param string $uid
	 * @return string|FALSE
	 */
	public function exist($uid){
// 		$sel = $this->select();
// 		$sel
		$con = DBConnect::getConnect();
		$value = $con->fetchOne("select ".self::SP_ID." from ".$this->_name." where ".$con->quoteInto(self::SP_UNID."=?", $uid));    
		return $value;
	}

	
	/**
	 * Findet einen Contact anhand seiner UID
	 * @param string $uid
	 * @param bool $testDeletet
	 * @return Zend_Db_Table_Row_Abstract|NULL
	 */
	public function findUid($uid, $testDeletet = TRUE){
		$sel = $this->select();
		$sel->where( self::SP_UNID."= ?", $uid);
		if($testDeletet === TRUE){
			$sel->where(self::SP_DELETE."=?",0);
		}
		$contRow = $this->fetchRow($sel);
		return $contRow;
	}

}

?>