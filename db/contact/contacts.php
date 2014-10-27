<?php
require_once 'citro/DBTable.php';

class Contacts extends DBTable {
	
	protected $_name = 'contacts';
	
	protected $apartmetOwner = NULL;
	protected $guest = NULL;
	protected $adresses = NULL;
	protected $mainAdressId = NULL;
	
	const SP_ID = "id";
	const SP_UNID = "uid";
	
	const SP_DATA_CREATE = "edata";
	const SP_DATA_EDIT = "vdata";
	const SP_ACCESS_CREATE = "access_create";
	const SP_ACCESS_EDIT = "access_edit";
	const SP_DELETE = "deleted";
	
	const SP_TITLE = "title_name";
	const SP_FIRST_NAME = "first_name";
	const SP_FIRST_ADD_NAME = "first_add_name";	
	const SP_LAST_NAME = "last_name";
	const SP_AFFIX_NAME = "affix_name";
	
	const SP_FIRMA = "firma";
	const SP_FIRMA_POSITION = "position";
	
	const SP_KURZINFO = "kurz_info";

	const SP_ADRESS_ID = "main_contact_address_id";
	const SP_EMAIL_ID = "main_contact_email_id";
	const SP_PHONE_ID = "main_contact_phone_id";
	
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

	
	

	/**
	 * Erstellt eine eindeutige Id des Contactes
	 * @param string $prefix eindeutigen Bezeichner der Firma oder der Datenbank
	 * @return string
	 */
	public static function generateUnId($prefix){
		return uniqid ($prefix."-");
	}
	
	
	
	public static function testTitle($value){
		if(strlen($value) > 50) return NULL;
		return $value;
	}
	public static function testFirstName($value){
		if(is_string($value) && strlen($value) < 200 &&  strlen($value) > 2){
			return $value;
		}
		return NULL;
	}
	public static function testFirstAddName($value){
		return $value;
	}
	/**
	 * Testet den Nachnamen
	 * @param string $value Nachname
	 * @return string|boolean Im Fehlerfall FALSE
	 */
	public static function testLastName($value){
		if(is_string($value) && strlen($value) < 200 &&  strlen($value) > 2){
			return $value;
		}
		return NULL;
	}
	public static function testAffixName($value){
		return $value;
	}

	
	public static function testFirma($value){
		return $value;
	}

	public static function testPosition($value){
		return $value;
	}
	
	public static function testKurzInfo($value){
		return $value;
	}
	

 	private $_title = NULL;
 	private $_firstName = NULL;
 	private $_firstAddName = NULL;
 	private $_lastName = NULL;
 	private $_affix = NULL;
 	
 	public function setTitle($valueOrData){
		$this->_title = $this->testTitle($valueOrData);
 	}
 	public function setFirstName($valueOrData){
 		$this->_firstName = $this->testFirstName($valueOrData);
 	}
 	public function setFirstAddName($valueOrData){
		$this->_firstAddName = $this->testFirstAddName($valueOrData);
 	}
 	public function setLastName($valueOrData){
 		$this->_lastName = $this->testLastName($valueOrData);
 	}
 	public function setAffix($valueOrData){
 		$this->_affix = $this->testAffixName($valueOrData);
 	}
	private function generateDate(){
		$data = array();
		if($this->_title !== NULL) $data[self::SP_TITLE] = $this->_title;
		if($this->_firstName !== NULL) $data[self::SP_FIRST_NAME] = $this->_firstName;
		if($this->_firstAddName !== NULL) $data[self::SP_FIRST_ADD_NAME] = $this->_firstAddName;
		if($this->_lastName !== NULL) $data[self::SP_LAST_NAME] = $this->_lastName;
		if($this->_affix !== NULL) $data[self::SP_AFFIX_NAME] = $this->_affix;
			
		return $data;
	}
	
	/**
	 * Einschreiben der gesetzten Daten mit prüfung der Daten die vorher mit den set Methoden gesetzt wurden
	 * @param integer $accessId
	 * @param string $lastName Der Nachname als String
	 * @return NULL|integer Bei erfolgreichen einschreiben die Id des Contactes ansonste NULL
	 */
	public function insertSetData($accessId, $lastName){
		
		$this->setLastName($lastName);
		
		// Testen des Pflichtfeldes Last Name oder abbruch
		if($this->_lastName === NULL)return NULL;
		
		$fields = $this->generateDate();		
		$fields[self::SP_ACCESS_CREATE] = $accessId;
		$fields[self::SP_ACCESS_EDIT]=$accessId;	
		
		return $this->insert($fields);
		
	}
	
	public function insert($fields){
	
		// Setzen der Standartfelder
		$fields[Contacts::SP_DATA_CREATE] = DBTable::DateTime ();
		$fields[Contacts::SP_DATA_EDIT] = DBTable::DateTime ();
		
		
		
		
		$setInsert = TRUE;
		while ( $setInsert )
		{
			$newUnId = self::generateUnId("bt");
			$allTubles = $this->findUid($newUnId,FALSE);
		
			if($allTubles === NULL){
				$fields[self::SP_UNID] = $newUnId;
				$setInsert = FALSE;
				break;
			}else{
				//@TODO hier muss noch ein logging eingebaut werden wenn die Id schon mal gefunden wurde um so ein überblic zu behalten
			}
		
		}
		
		
		
		return  parent::insert($fields);
		
	}
}

?>