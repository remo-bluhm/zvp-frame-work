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
 	

	
 	
 	
 	
 	
	/**
	 * Einschreiben der gesetzten Daten mit prüfung der Daten die vorher mit den set Methoden gesetzt wurden
	 * @param integer $accessId
	 * @param string $lastName Der Nachname als String
	 * @return NULL|integer Bei erfolgreichen einschreiben die Id des Contactes ansonste NULL
	 */
	public function insertData($accessId,$lastName,$data=array()){
	
		// Die Pflichtparameter
		$lastName = self::testLastName( $lastName);
		
		// testen auf die Pflichtparameter
		if($lastName !== NUll){
			$fields = array();	
			
			// setzen der Pflichtparameters Nachname
			$fields[self::SP_LAST_NAME] = $lastName;
			
			// setzen der Standartfelder
			$fields[self::SP_ACCESS_CREATE] = $accessId;
			$fields[self::SP_ACCESS_EDIT]=$accessId;	
			$fields[Contacts::SP_DATA_CREATE] = DBTable::DateTime ();
			$fields[Contacts::SP_DATA_EDIT] = DBTable::DateTime ();

			
			
			
	
			
			// Setzen des Types
			if(!empty( $data["cont_type"])){
				$fields["type"] = self::testType($data["cont_type"]);
			}
			
			//Titel setzen
			if(!empty( $data["title_name"])){
				$title = self::testTitle ( $data["title_name"]);
				if($title !== NULL )	$fields[self::SP_TITLE] = $title ;
			}
			
			//$firstName setzen
			if(!empty( $data["first_name"])){
				$firstName = self::testFirstName ( $data["first_name"]);
				if($firstName !== NULL )	$fields[self::SP_FIRST_NAME] = $firstName ;
			}
			
			//addFirstName setzen
			if(!empty( $data["first_add_name"])){
				$addFirstName = self::testFirstAddName( $data["first_add_name"]);
				if($addFirstName !== NULL )	$fields[self::SP_FIRST_ADD_NAME] = $addFirstName ;
			}
			
			//affixName setzen
			if(!empty( $data["affix_name"])){
				$affixName = self::testAffixName( $data["affix_name"]);
				if($affixName !== NULL )	$fields[self::SP_AFFIX_NAME] = $affixName;
			}
			

			
			// setzen der UID
			$newUnId = NULL;
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
			$fields[Contacts::SP_UNID] = $newUnId;
			
		
			//########### Einschreiben der Daten

			$contactId = (integer)$this->insert($fields);
			
			
			// setzen von Adressen
			$mainAdressId = NULL;
			if(array_key_exists("adresses",$data) && is_array($data["adresses"]) ){
				require_once 'db/contact/address/Address.php';
				$adrTab = new Address();
				foreach ($data["adresses"] as $adrFields){
					
					
					
					if(is_array($adrFields) && !empty($adrFields["ort"]) ){
						$adrTab->clearData();
												
						if(array_key_exists("art",$adrFields)) 		$adrTab->setArt($adrFields["art"]);
						if(array_key_exists("adrline",$adrFields)) 	$adrTab->setNameLine($adrFields["adrline"]);
						if(array_key_exists("strasse",$adrFields)) 	$adrTab->setStreet($adrFields["strasse"]);
						if(array_key_exists("ort",$adrFields)) 		$adrTab->setOrt($adrFields["ort"]);
						if(array_key_exists("plz",$adrFields)) 		$adrTab->setZip($adrFields["plz"]);
						if(array_key_exists("land",$adrFields)) 	$adrTab->setLand($adrFields["land"]);
						if(array_key_exists("landiade",$adrFields)) $adrTab->setLand($adrFields["landiade"]);
						if(array_key_exists("landpart",$adrFields)) $adrTab->setLandpart($adrFields["landpart"]);
						
						$adressId = $adrTab->insertDataFull($contactId);
						
						if (!empty($adrFields["is_main"]) && $adressId !== NULL ){
							if( strtoupper( $adrFields["is_main"]) == "TRUE" ) $mainAdressId = $adressId;
						}
					}
				}
			}
				
				
				
			// setzen der Telefonnummern
			$mainPhoneId = NULL;
			if(array_key_exists("numbers",$data) && is_array($data["numbers"]) ){
				require_once 'db/contact/phone/Phone.php';
				$phoneTab = new Phone();
				foreach ($data["numbers"] as $phoneFields){
					if(is_array($phoneFields) && !empty($phoneFields["number"])){
						$phoneTab->setArt($phoneFields["art"]);
						$phoneTab->setNumber($phoneFields["number"]);
						$phoneTab->setText($phoneFields["text"]);
						
						$phoneId = $phoneTab->insertDataFull( $contactId);
						
						if (!empty($phoneFields["is_main"]) && $phoneId !== NULL ){
							if(strtoupper ( $phoneFields["is_main"]) == "TRUE") $mainPhoneId = $phoneId;
						}
					}
				}
			}
				
				
				
			// setzen der Mails
			$mainMailId = NULL;
			if(array_key_exists("emails",$data) && is_array($data["emails"]) ){
				require_once 'db/contact/email/Email.php';
				$mailTab = new Email();
				foreach ($data["emails"] as $mailFields){
					if(is_array($mailFields) && !empty($mailFields["adress"])){
						$mailTab->setText($mailFields["text"]);
						$mailTab->setEmail($mailFields["adress"]);
						$mailId = $mailTab->insertDataFull( $contactId);
						if (!empty($mailFields["is_main"])){
							if(strtoupper ( $mailFields["is_main"]) == "TRUE") $mainMailId = $mailId;
						}
					}
				}
			}
				
				
			// Setzen der Haupt Adressen, Mails oder Telefonnummern
			$updateData = array();
			if($mainAdressId !== NULL) $updateData[Contacts::SP_ADRESS_ID] = $mainAdressId;
			if($mainPhoneId !== NULL) $updateData[Contacts::SP_PHONE_ID] = $mainPhoneId;
			if($mainMailId !== NULL) $updateData[Contacts::SP_EMAIL_ID] = $mainMailId;
			if(count($updateData) > 0 )$this->update($updateData, "id = ".$contactId);
				
			return $newUnId;
			
		}
		
		return NULL;
						
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
	
	
	public function exist($uid){
		$value = DBConnect::getConnect()->fetchOne("select 'id' from contacts where uid=$uid");
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