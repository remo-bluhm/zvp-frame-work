<?php

require_once 'citro/service-class/AService.php';

/**
 * Dieser Serviece verwaltet alle Contacte
 *
 * @author Max Plank
 * @version 1.0
 *         
 */
class ServiceGuest extends AService {
	
	private $_categoryStand = "NOT";
	public $limit = 10;
	
	
	/**
	 * Der User construktor
	 */
	function __construct() {
		
		parent::__construct ();
	
	}
	
	
	/**
	 * Giebt eine Liste von Contacten zurück
	 * @param integer $count
	 * @param integer $offset
	 * @param array $where name|ort
	 * @param array $spalten
	 * @return array
	 */
	public function ActionGetList($count, $offset = 0, $where = array(), $spalten = array()){
	
		require_once 'db/contact/Contacts.php';
	
		$db = Contacts::getDefaultAdapter();
	
	
		$resortSel = $db->select ();
	
		$spGuest = array();
		$spGuest["guest_id"] = "id";
		$spGuest["sys_text"] = "systext";
		
	
	
		$resortSel->from(array('gu' => "guest") ,$spGuest);
		
		$spContacts = array();
		$spContacts["uid"] = "uid";
		$spContacts["title_name"] = "title_name";
		$spContacts["last_name"] = "last_name";
		$spContacts["first_name"] = "first_name";
		$spContacts["first_add_name"] = "first_add_name";
		$spContacts["affix_name"] = "affix_name";
		
		$spContacts["firma"] = "firma";
		$spContacts["position"] = "position";
		
		require_once 'db/contact/Contacts.php';
		
		$resortSel->joinLeft(array('c'=>Contacts::getTableNameStatic()), "gu.contacts_id = c.id", $spContacts );
 			
 		require_once 'db/contact/address/Address.php';
 		$adressSpaltenA = array();
 		$adressSpaltenA['a_plz'] = "plz";
 		$adressSpaltenA['a_ort'] = "ort";
 		$adressSpaltenA['a_strasse'] = "strasse";
 		if( array_key_exists('adr_plz',$where) || array_key_exists("adr_ort", $where) || array_key_exists("adr_strasse", $where) ){	
 			$resortSel->joinLeft(array('a'=>Address::getTableNameStatic()), "c.id = a.contacts_id", $adressSpaltenA );
 		}else {
 			$resortSel->joinLeft(array('a'=>Address::getTableNameStatic()), "c.main_contact_address_id = a.id", $adressSpaltenA );
 		}
 		
 		
 		require_once 'db/contact/phone/contact_phone.php';
 		$phoneSpaltenA = array();
 		$phoneSpaltenA['p_art'] = "art";
 		$phoneSpaltenA['p_number'] = "number";
 		$phoneSpaltenA['p_text'] = "text";
 		if( array_key_exists('phone_art',$where) || array_key_exists("phone_number", $where) || array_key_exists("phone_text", $where) ){
 			$resortSel->joinLeft(array('p'=>contact_phone::getTableNameStatic()), "c.id = p.contacts_id", $phoneSpaltenA );
 		}else {
 			$resortSel->joinLeft(array('p'=>contact_phone::getTableNameStatic()), "c.main_contact_phone_id = p.id", $phoneSpaltenA );
 		}

		$resortSel->where("c.deleted = ?", 0);
			
		// suche nach Namen
		if(array_key_exists("last_name", $where))
			$resortSel->where("c.last_name LIKE ?", $where["last_name"]);
		if(array_key_exists("first_name", $where))
			$resortSel->where("c.first_name LIKE ?", $where["first_name"]);
	
		if(array_key_exists("adr_plz", $where)){
			$groupIsOn = TRUE;
			$resortSel->where("a.plz LIKE ?", $where["adr_plz"]);
		}
		if(array_key_exists("adr_ort", $where)){
			$groupIsOn = TRUE;
			$resortSel->where("a.ort LIKE ?", $where["adr_ort"]);
		}
		if(array_key_exists("adr_strasse", $where)){
			$groupIsOn = TRUE;
			$resortSel->where("a.strasse LIKE ?", $where["adr_strasse"]);
		}
			
			
		$resortSel->limit($count,$offset);

		$resort = $db->fetchAll( $resortSel );
	
		return $resort;
	
	
	}
	

	
	
	
	
	/**
	 * Setzt einen neuen Gast
	 *
	 * @param string $LastName  Name des Users
	 * @param array $fields Die Gruppe die der Users unterliegt
	 * @return citro_list Den eingetragenen User mit  guid|name|password|aeskey|visibil|date_create|admin Der Parameter "admin" bekommt man nur wenn man selber Admin ist
	 *
	 */
	public function ActionNew($LastName, $fields = array()) {
	
	
		$gastData = array();
		$gastData["edata"] = DBTable::DateTime ();
		$gastData["vdata"] = DBTable::DateTime ();
		$gastData["access_create"] = $this->_rightsAcl->getAccess()->getId();
		$gastData["access_edit"] = $this->_rightsAcl->getAccess()->getId();
		if(!empty($fields["intern_info"])){
			$gastData["systext"] = $fields["intern_info"];
		}
		
		require_once 'db/contact/Contacts.php';
		$kData = array();
	
		// Setzen der Standartfelder
		$kData[Contacts::SP_DATA_CREATE] = DBTable::DateTime ();
		$kData[Contacts::SP_DATA_EDIT] = DBTable::DateTime ();
		$kData[Contacts::SP_ACCESS_CREATE] = $this->_rightsAcl->getAccess()->getId();
		$kData[Contacts::SP_ACCESS_EDIT] = $this->_rightsAcl->getAccess()->getId();
	
		// Setzt den Namen für das Adressfeld zusammen
		$fullName = "";
		
		if(!empty($fields["title_name"])){
			$title = Contacts::testTitle($fields["title_name"]);
			if($title !== FALSE){
				$kData[Contacts::SP_TITLE] = $title;
				$fullName.= $title;
			}
		}
	
		if(!empty($fields["first_add_name"])){
			$addFirstName = Contacts::testFirstAddName($fields["first_add_name"]);
			if($addFirstName !== FALSE){
				$kData[Contacts::SP_FIRST_ADD_NAME] = $addFirstName;
				$fullName.= $addFirstName;
			}
		}
	
		if(!empty($fields["first_name"])){
			$firstName = Contacts::testFirstName($fields["first_name"]);
			if($firstName !== FALSE){
				$kData[Contacts::SP_FIRST_NAME] = $firstName;
				$fullName.= $firstName;
			}
		}
		
		// Testen des Pflichtfeldes Last Name oder abbruch
		$lastName = Contacts::testLastName($LastName);
		if($lastName === FALSE)return FALSE;
		$kData[Contacts::SP_LAST_NAME] = $lastName;
		$fullName.= $lastName;
	
		
		if(!empty($fields["affixname"])){
			$affixName = Contacts::testAffixName($fields["affix_name"]);
			if($affixName !== FALSE){
				$kData[Contacts::SP_AFFIX_NAME] = $affixName;
				$fullName.= $affixName;
			}
		}
	
	
	
		if(!empty($fields["firma"])){
			$firma = Contacts::testAffixName($fields["firma"]);
			if($firma !== FALSE) $kData[Contacts::SP_FIRMA] = $firma;
		}
		if(!empty($fields["position"])){
			$firma = Contacts::testAffixName($fields["position"]);
			if($firma !== FALSE) $kData[Contacts::SP_FIRMA_POSITION] = $firma;
		}
		if(!empty($fields["infotext"])){
			$info = Contacts::testAffixName($fields["infotext"]);
			if($info !== FALSE) $kData[Contacts::SP_KURZINFO] = $info;
		}
	
	

	
	
	
	
		// Adresss
		$adressData = NULL;
	
		if( !empty($fields["adr_ort"]) ){
	
			require_once '../../db/contact/address/Address.php';
			$adressOrt = Address::testOrt($fields["adr_ort"]);
			if($adressOrt !== FALSE){
	
				$adressData = array();
				$adressData['name_row_1'] = $fullName;
				$adressData[Address::SP_ORT] = $adressOrt;
	
				$adressPlz = Address::testPLZ($fields["adr_plz"]);
				if($adressPlz !== FALSE)$adressData[Address::SP_PLZ] = $adressPlz;
	
				$adressStreet = Address::testStreet($fields["adr_strasse"]);
				if($adressStreet !== FALSE)$adressData[Address::SP_STRASSE] = $adressStreet;
	
				$adressLand = Address::testLand($fields["adr_land"]);
				if($adressLand !== FALSE)$adressData[Address::SP_LAND] = $adressLand;
	
				$adressLandPart = Address::testLandPart($fields["adr_landpart"]);
				if($adressLandPart !== FALSE)$adressData[Address::SP_LAND_PART] = $adressLandPart;
					
			}
				
		}
	
	
		// Telefon
		$phoneData = NULL;
		if(!empty($fields["phone_number"])){
			require_once '../../db/contact/phone/contact_phone.php';
				
			$phoneNumber = contact_phone::testPhoneNumber($fields["phone_number"]);
			if($phoneNumber !== FALSE){
				$phoneData = array();
				$phoneData[contact_phone::SP_NUMBER] = $phoneNumber;
				$phoneData[contact_phone::SP_ART] = "Telefon 1";
	
				$phoneText = contact_phone::testText($fields["phone_text"]);
				if($phoneText !== FALSE)$phoneData[contact_phone::SP_TEXT] = $phoneText;
	
			}
		}
	
		// Email
		$emailData = NULL;
		if(!empty($fields["mail_adress"])){
			require_once '../../db/contact/email/contact_email.php';
	
			$emailAdresss = contact_email::testEmail($fields["mail_adress"]);
			if($emailAdresss !== FALSE){
				$emailData = array();
				$emailData[contact_email::SP_ADRESS] = $emailAdresss;
			}
		}
	
	
	
		// Daten einschreiben
		$db = Contacts::getDefaultAdapter();
	
		$setInsert = TRUE;
		while ( $setInsert )
		{
			$newUnId = Contacts::generateUnId("bt");
			$selTuble = $db->select();
			$selTuble->from(Contacts::getTableNameStatic());
			$selTuble->where(Contacts::SP_UNID." = ? ",$newUnId);
	
			$allTubles = $db->fetchAll($selTuble);
	
			if(count($allTubles) == 0){
				$kData[Contacts::SP_UNID] = $newUnId;
				$setInsert = FALSE;
				break;
			}
	
		}
	

		$db->beginTransaction();

	
		try {
			
			
			
			
			$contactTab = new Contacts();
			$contactsId = $contactTab->insert($kData);
	
			
			$gastData['contacts_id'] = $contactsId;
			$db->insert("guest", $gastData);
			
			
			$updateData = array();
				
			if(is_array($adressData)){
				$adressData[Address::SP_CONTACT_ID] = $contactsId;
				$adressTab = new Address();
				$adressId = $adressTab->insert( $adressData);
				$updateData[Contacts::SP_ADRESS_ID] = $adressId;
	
			}
			if(is_array($emailData)){
				$emailData[contact_email::SP_CONTACT_ID] = $contactsId;
				$mailTab = new contact_email();
				$emailId = $mailTab->insert( $emailData);
	
				$updateData[Contacts::SP_EMAIL_ID] = $emailId;
			}
			if(is_array($phoneData )){
				$phoneData[contact_phone::SP_CONTACT_ID] = $contactsId;
				$phoneTab = new contact_phone();
				$phoneId = $phoneTab->insert( $phoneData);
				$updateData[Contacts::SP_PHONE_ID] = $phoneId;
			}
	
			if(count($updateData) > 0){
				$db->update(Contacts::getTableNameStatic(), $updateData, Contacts::SP_ID."=".$contactsId);
			}
				
				
				
			$db->commit();
				
			return $newUnId;
		}catch (Exception $eTrans){
			$db->rollBack();
			throw new Exception($eTrans->getMessage(),E_ERROR);
			return FALSE;
		}
	
	
	}
	
	
	
	
	/**
	 * Giebt die Contactdaten eines Contactes zurück
	 *
	 * @param string $contactuid Die Uid des Contacts als String
	 * @param array $spalten
	 * @return array
	 */
	public function ActionGetSingle($contactuid, $spalten = array()){
	
	
		require_once 'db/contact/Contacts.php';
		$db = Contacts::getDefaultAdapter();
	
	
		$spA = array();
		$spA["uid"] = "uid";
		$spA["id_name"] = "id";
		$spA["create_date"] = "edata";
		$spA["edit_date"] = "vdata";
		$spA["title_name"] = "title_name";
		$spA["last_name"] = "last_name";
		$spA["first_name"] = "first_name";
		$spA["first_add_name"] = "first_add_name";
		$spA["affix_name"] = "affix_name";
	
		$spA["firma"] = "firma";
		$spA["position"] = "position";
	
	
	
		$sel = $db->select ();
		$sel->from( array('c' => Contacts::getTableNameStatic() ), $spA );
		
		$sel->joinRight(array('g'=>'guest'), "c.id = g.contacts_id",array('g_systext' =>"systext") );
	
		if( in_array('usercreate_name',$spalten) ){
			require_once 'db/sys/access/sys_access.php';
			$sel->joinLeft(array('a'=>sys_access::getTableNameStatic()), "c.access_create = a.id",array() );
			$sel->joinLeft(array('c1'=>Contacts::getTableNameStatic()), "a.contacts_id = c1.id", array ('usercreate_name' => 'CONCAT(c1.first_name," ",c1.last_name )' ) );
		}
	
		if( in_array('useredit_name',$spalten) ){
			require_once 'db/sys/access/sys_access.php';
			$sel->joinLeft(array('a2'=>sys_access::getTableNameStatic()), "c.access_edit = a2.id" ,array() );
			$sel->joinLeft(array('c2'=>Contacts::getTableNameStatic()), "a2.contacts_id = c2.id", array ('useredit_name' => 'CONCAT(c2.first_name," ",c2.last_name )')  );
		}
	
	
		$adresSp = array();
		$adresSp["adr_art"] = "art";
		$adresSp["adr_land"] = "land";
		$adresSp["adr_landpart"] = "landpart";
		$adresSp["adr_plz"] = "plz";
		$adresSp["adr_ort"] = "ort";
		$adresSp["adr_strasse"] = "strasse";
		$adresSp["adr_infotext"] = "info_text";
	
		require_once '../../db/contact/address/Address.php';
		$sel->joinLeft(array('ca'=>Address::getTableNameStatic()), "c.main_contact_address_id = ca.id" ,$adresSp);
	
	
		$phoneSp = array();
		$phoneSp["phone_art"] = "art";
		$phoneSp["phone_number"] = "number";
		$phoneSp["phone_text"] = "text";
	
		require_once '../../db/contact/phone/contact_phone.php';
		$sel->joinLeft(array('p'=>contact_phone::getTableNameStatic()), "c.main_contact_phone_id = p.id" ,$phoneSp);
	
	
		$mailSp = array();
		$mailSp["mail_adress"] = "mailadress";
		$mailSp["mail_text"] = "text";
			
		require_once '../../db/contact/email/contact_email.php';
		$sel->joinLeft(array('em'=>contact_email::getTableNameStatic()), "c.main_contact_email = em.id" ,$mailSp);
	
		$sel->where("c.uid = ?",$contactuid);
		$sel->where("c.deleted = ?", "0");
	
		$contactA = $db->fetchRow($sel);
	
		// falls nichts gefunden wurde dann abbruch
		if($contactA === FALSE) return FALSE;
			
		//////////////////////////////////////
		if( in_array('all_address',$spalten) ){
			require_once '../../db/contact/address/Address.php';
				
			$selAdress = $db->select ();
			$selAdress->from( array('c' => Address::getTableNameStatic() ), $adresSp );
			$selAdress->where("contacts_id = ?",$contactA["id_name"]);
				
			$contactA["adresses"] = $db->fetchAll($selAdress);
		}
		//////////////////////////////////////////////////////////////////
		if( in_array('all_phone',$spalten) ){
			require_once '../../db/contact/phone/contact_phone.php';
				
			$selPhone = $db->select ();
			$selPhone->from( contact_phone::getTableNameStatic() , $phoneSp );
			$selPhone->where("contacts_id = ?",$contactA["id_name"]);
				
			$contactA["phones"] = $db->fetchAll($selPhone);
		}
		unset($contactA["id_name"]);
		return $contactA;
	
	
	}

	
	
	/**
	 * Giebt die Liste der Adressen eines Gastes zurück
	 * @param string $contactUid
	 */
	public function ActionAddressList($contactUid){
		
		
		require_once 'db/contact/Contacts.php';
		
		$db = Contacts::getDefaultAdapter();
		
		
		$addressSel = $db->select ();
		
		$spA = array();
		$spA["uid"] = "uid";
		$spA["title_name"] = "title_name";
		$spA["last_name"] = "last_name";
		$spA["first_name"] = "first_name";
		$spA["first_add_name"] = "first_add_name";
		$spA["affix_name"] = "affix_name";
		
		$spA["firma"] = "firma";
		$spA["position"] = "position";
		
		
		
		$addressSel->from(array('c' => Contacts::getTableNameStatic()) ,$spA);
		
		$guestSpA = array();
		
		$addressSel->joinRight(array('g'=>'guest'), "c.id = g.contacts_id", $guestSpA );
			
		
		require_once '../../db/contact/address/Address.php';
		$adressSpaltenA = array();
		$adressSpaltenA['a_id'] = "id";
		$adressSpaltenA['a_land'] = "land";
		$adressSpaltenA['a_landpart'] = "landpart";
		$adressSpaltenA['a_name_row_1'] = "name_row_1";
		$adressSpaltenA['a_name_row_2'] = "name_row_2";
		$adressSpaltenA['a_art'] = "art";
		$adressSpaltenA['a_plz'] = "plz";
		$adressSpaltenA['a_ort'] = "ort";
		$adressSpaltenA['a_strasse'] = "strasse";
		$adressSpaltenA['a_info_text'] = "info_text";
		
		$addressSel->joinRight(array('a'=>Address::getTableNameStatic()), "c.id = a.contacts_id", $adressSpaltenA );
		
		
		$addressSel->where("c.deleted = ?", 0);
		$addressSel->where("c.uid = ?", $contactUid);
			
		
			
		//$resortSel->limit($count,$offset);
		$resort = $db->fetchAll( $addressSel );
		
		return $resort;
	}
}

?>