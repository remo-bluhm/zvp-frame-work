<?php

require_once 'citro/service-class/AService.php';

/**
 * Dieser Serviece verwaltet alle Contacte
 *
 * @author Max Plank
 * @version 1.0
 *         
 */
class ServiceContact extends AService {
	
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
	
		require_once 'db/contact/contacts.php';
	
		$db = contacts::getDefaultAdapter();

		$resortSel = $db->select ();
	
		$spA = array();
		$spA["uid"] = "uid";
		//$spA["anzahl"] = "count(uid)";
		$spA["title_name"] = "title_name";
		$spA["last_name"] = "last_name";
		$spA["first_name"] = "first_name";
		$spA["first_add_name"] = "first_add_name";
		$spA["affix_name"] = "affix_name";
		
		$spA["firma"] = "firma";
		$spA["position"] = "position";
		
		

		
		
// 		$spA["visibil"] = "visibil";
			
// 		$spA["creat_date"] = "edata";
// 		$spA["edit_date"] = "vdata";
	
// 		$spA["create_guid"] = "usercreate";
// 		$spA["edit_guid"] = "useredit";
	
// 		$spA["strasse"]="strasse";
	

	
// 		if( in_array('apartment_count',$spalten) )
// 			$spA['apartment_count'] = 'IFNULL(count(a.id),0)';
	
		// muss nur auf True gestellt werden wenn die hinzugefügten where abfragen gestellt werden 	
		//$groupIsOn = FALSE;
	
		$resortSel->from(array('c' => contacts::getTableNameStatic()) ,$spA);

		
 		
 			
 		require_once 'db/contact/contact_address.php';
 		$adressSpaltenA = array();
 		$adressSpaltenA['a_plz'] = "plz";
 		$adressSpaltenA['a_ort'] = "ort";
 		$adressSpaltenA['a_strasse'] = "strasse";
 		if( array_key_exists('adr_plz',$where) || array_key_exists("adr_ort", $where) || array_key_exists("adr_strasse", $where) ){	
 			$resortSel->joinLeft(array('a'=>contact_address::getTableNameStatic()), "c.id = a.contacts_id", $adressSpaltenA );
 		}else {
 			$resortSel->joinLeft(array('a'=>contact_address::getTableNameStatic()), "c.main_contact_address_id = a.id", $adressSpaltenA );
 		}
 		
 		
 		require_once 'db/contact/contact_phone.php';
 		$phoneSpaltenA = array();
 		$phoneSpaltenA['p_art'] = "art";
 		$phoneSpaltenA['p_number'] = "number";
 		$phoneSpaltenA['p_text'] = "text";
 		if( array_key_exists('phone_art',$where) || array_key_exists("phone_number", $where) || array_key_exists("phone_text", $where) ){
 			$resortSel->joinLeft(array('p'=>contact_phone::getTableNameStatic()), "c.id = p.contacts_id", $phoneSpaltenA );
 		}else {
 			$resortSel->joinLeft(array('p'=>contact_phone::getTableNameStatic()), "c.main_contact_phone_id = p.id", $phoneSpaltenA );
 		}
		
// 		if( in_array('ort_name',$spalten) || array_key_exists("ort", $where)  ){
// 			require_once 'db/resort/resort_orte.php';
// 			$resortSel->joinLeft(array('o'=>resort_orte::getTableNameStatic()), "o.id = r.ort_id", array ( 'ort_name'=>'o.name') );
// 		}
	
// 		if( in_array('usercreate_name',$spalten) ){
// 			require_once 'db/contact/contact_access.php';
// 			require_once 'db/contact/contacts.php';
// 			$resortSel->joinLeft(array('u'=>contact_access::getTableNameStatic()), "r.usercreate = u.guid ",array() );
// 			$resortSel->joinLeft(array('c'=>contacts::getTableNameStatic()), "u.contacts_id = c.id", array ('usercreate_name' => 'CONCAT(c.first_name," ",c.last_name )' ) );
// 		}
	
// 		if( in_array('useredit_name',$spalten) ){
// 			require_once 'db/contact/contact_access.php';
// 			require_once 'db/contact/contacts.php';
// 			$resortSel->joinLeft(array('u2'=>contact_access::getTableNameStatic()), "r.useredit = u2.guid " ,array() );
// 			$resortSel->joinLeft(array('c2'=>contacts::getTableNameStatic()), "u2.contacts_id = c2.id", array ('useredit_name' => 'CONCAT(c2.first_name," ",c2.last_name )')  );
// 		}
	
// 		$spaltenInApartment = array();
// 		if( in_array('apartment_namen',$spalten) ){
// 			$spaltenInApartment['apartment_namen'] = 'GROUP_CONCAT(a.name )';
// 		}
	
// 		if( in_array('apartment_count',$spalten) ){
// 			require_once 'db/apartment/apartment.php';
// 			$resortSel->joinLeft(array('a'=>apartment::getTableNameStatic()), "a.resort_id = r.id"  , $spaltenInApartment);
// 			$resortSel->group("r.id");
// 		}
	
 		// 		$resortSel->where("r.name=?", $name);
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
		//$resortSel->union(array("SELECT FOUND_ROWS()"));
// 		if($groupIsOn)
// 			$resortSel->group("c.id");
	
 		//$selectStr = $resortSel->__toString();
 //	$selectStr = $selectStr." UNION SELECT FOUND_ROWS();";
 		//echo $selectStr;
		$resort = $db->fetchAll( $resortSel );
	
		return $resort;
	
	
	}
	
	
	/**
	 * Giebt die anzahl der Apartments zurück
	 * @param array $where
	 * @return array
	 */
	public function ActionCount( $where = array()){
	
		require_once 'db/contact/contacts.php';
		$db = contacts::getDefaultAdapter();
	
		$searchListSel = $db->select ();
		$searchListSel->from( array('c' => contacts::getTableNameStatic() ), array( "count(c.id)") );
	
	
	
// 		// Suche nach Ort
// 		if(array_key_exists("ort", $where)){
// 			require_once 'db/resort/resort_orte.php';
// 			$searchListSel->joinLeft(array('o'=>resort_orte::getTableNameStatic()), "o.id = r.ort_id", array ('ort_name' => 'name')  );
// 		}
		
		$searchListSel->where("c.deleted = ?", 0);
		
		if(array_key_exists("last_name", $where))
			$searchListSel->where("c.last_name LIKE ?", $where["last_name"]);
		if(array_key_exists("first_name", $where))
			$searchListSel->where("c.first_name LIKE ?", $where["first_name"]);
			
// 		// Suche nach Strasse
// 		if(array_key_exists("strasse", $where))
// 			$searchListSel->where("r.strasse LIKE ?", $where["strasse"]."%");
	
// 		if(array_key_exists("ort", $where))
// 			$searchListSel->where("o.name LIKE ?", $where["ort"]."%");
	
		$allOrtCounts = $db->fetchOne($searchListSel);
		return $allOrtCounts;
	
	
	}
	
	/**
	 * Giebt die Contactdaten eines Contactes zurück
	 *
	 * @param string $contactuid Die Uid des Contacts als String
	 * @param array $spalten
	 * @return array
	 */
	public function ActionGetSingle($contactuid, $spalten = array()){
	
	
		require_once 'db/contact/contacts.php';
		$db = contacts::getDefaultAdapter();

		
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
		$sel->from( array('c' => contacts::getTableNameStatic() ), $spA );
		
		if( in_array('usercreate_name',$spalten) ){
			require_once 'db/sys/access/sys_access.php';
			$sel->joinLeft(array('a'=>sys_access::getTableNameStatic()), "c.access_create = a.id",array() );
			$sel->joinLeft(array('c1'=>contacts::getTableNameStatic()), "a.contacts_id = c1.id", array ('usercreate_name' => 'CONCAT(c1.first_name," ",c1.last_name )' ) );
		}
		
		if( in_array('useredit_name',$spalten) ){
			require_once 'db/sys/access/sys_access.php';
			$sel->joinLeft(array('a2'=>sys_access::getTableNameStatic()), "c.access_edit = a2.id" ,array() );
			$sel->joinLeft(array('c2'=>contacts::getTableNameStatic()), "a2.contacts_id = c2.id", array ('useredit_name' => 'CONCAT(c2.first_name," ",c2.last_name )')  );
		}

		
		$adresSp = array();
		$adresSp["adr_art"] = "art";
		$adresSp["adr_land"] = "land";
		$adresSp["adr_landpart"] = "landpart";
		$adresSp["adr_plz"] = "plz";
		$adresSp["adr_ort"] = "ort";
		$adresSp["adr_strasse"] = "strasse";
		$adresSp["adr_infotext"] = "info_text";
		
		require_once 'db/contact/contact_address.php';
		$sel->joinLeft(array('ca'=>contact_address::getTableNameStatic()), "c.main_contact_address_id = ca.id" ,$adresSp);

		
 		$phoneSp = array();
 		$phoneSp["phone_art"] = "art";
 		$phoneSp["phone_number"] = "number";
 		$phoneSp["phone_text"] = "text";
		
 		require_once 'db/contact/contact_phone.php';
 		$sel->joinLeft(array('p'=>contact_phone::getTableNameStatic()), "c.main_contact_phone_id = p.id" ,$phoneSp);

		
 		$mailSp = array();
 		$mailSp["mail_adress"] = "mailadress";
 		$mailSp["mail_text"] = "text";
 		
 		require_once 'db/contact/contact_email.php';
 		$sel->joinLeft(array('em'=>contact_email::getTableNameStatic()), "c.main_contact_email = em.id" ,$mailSp);
		
		$sel->where("c.uid = ?",$contactuid);
		$sel->where("c.deleted = ?", "0");
		
		$contactA = $db->fetchRow($sel);
		
		// falls nichts gefunden wurde dann abbruch
		if($contactA === FALSE) return FALSE;
			
		//////////////////////////////////////
		if( in_array('all_address',$spalten) ){
			require_once 'db/contact/contact_address.php';
					
			$selAdress = $db->select ();
			$selAdress->from( array('c' => contact_address::getTableNameStatic() ), $adresSp );
			$selAdress->where("contacts_id = ?",$contactA["id_name"]);
			
			$contactA["adresses"] = $db->fetchAll($selAdress);
		}
		//////////////////////////////////////////////////////////////////
		if( in_array('all_phone',$spalten) ){
			require_once 'db/contact/contact_phone.php';
					
			$selPhone = $db->select ();
			$selPhone->from( contact_phone::getTableNameStatic() , $phoneSp );
			$selPhone->where("contacts_id = ?",$contactA["id_name"]);
			
			$contactA["phones"] = $db->fetchAll($selPhone);
		}
		unset($contactA["id_name"]);
		return $contactA;
	
	
	}
	
	/**
	 * Giebt meinene Contactdaten zurück
	 * 
	 * @return citro_list Die contact daten
	 */
	public function ActionContactList(){
		
		
		require_once 'citro/db/contact/contacts.php';
		$contactTab = new contacts();
		$contactSel = $contactTab->select();
		//$contactSel->where("category = ?", "REN");
		
		
		$limit = 10;
		if(isset($this->limit) && $this->limit < 101){
			$limit = $this->limit;
		}
		
		$contactSel->limit($limit,0);
		
		$contactArry = $contactTab->fetchAll($contactSel);
		
		return $contactArry->toArray();
	}
	
	/**
	 * Giebt meinene Contactdaten zurück
	 * @return Zend_Db_Table_Row meine Contactdaten
	 */
	public function ActionGetMyContact(){

		$contactId = $this->_user->getContactId();

		require_once 'db/contact/contacts.php';
		$kontaktTab = new contacts();
		$contactSel = $kontaktTab->select();
		$contactSel->where(contacts::SP_ID . " = ?", $contactId);
		$contactRow = $kontaktTab->fetchRow($contactSel);
		
		if($contactRow == NULL)
			return array();
		
		return $contactRow->toArray();
	}
	
	/**
	 * Giebt einen einzelnen contact zurück
	 *
	 * @param string $contactId  Die Guid des Users als Strüng
	 * @return array rückgabe wert
	 * @citro_isOn true
	 */
	public function ActionGetToUpdate($contactId) {

	
		
		// Inizialiseren eines ServiceContactUpdates das die Schnitstelle DBIUpdate enthällt
		require_once 'service/Contact/ServiceContactUpdate.php';
		$servContUpd = new ServiceContactUpdate($contactId);
		
		// Inizialisieren des Update Reposetorys
		require_once 'citro/update/ToUpdateFactory.php';
		$dbupdateReposetory = new ToUpdateFactory( DBConnect::getConnect(),$servContUpd );
		
		// Hollen der UpdateDaten
		//$updateData = $dbupdateReposetory->getToUpdate($servContUpd);
		// Erstellen einses Hashkeys aus den updateDaten
		//$updateHashKey = $dbupdateReposetory->generateeHashKey($updateData);
		
		// verbinden der Daten zum verschicken
		//$updateHashKeyArray = array (DBupdateRepository::UPDATE_HASH_KEY => $updateHashKey );
		//$updateData = array_merge ( ( array ) $updateData, ( array ) $updateHashKeyArray );
		
		return $updateData;
		
		
	
	}
	
	/**
	 * Ist für das Update der eigenen Daten
	 *
	 * @param integer $contactId
	 * @param array $userDataArray
	 * @citro_isOn true
	 */
	public function ActionUpdateData($contactId, $userDataArray) {
	

		
		require_once 'citro/db/contact/contacts.php';
		require_once 'citro/DBupdateRepository.php';
		

		$userDataArray [contacts::SP_DATA_EDIT] = DBTable::DateTime ();
		$userDataArray [contacts::SP_USER_EDIT] = $this->_MainUser->getGUID();
		
		$updateColumn = array (
				contacts::SP_DATA_EDIT, 
				contacts::SP_USER_EDIT, 
				contacts::SP_FIRST_NAME, 
				contacts::SP_LAST_NAME,
				contacts::SP_FIRMA,
				contacts::SP_POSITION,
				contacts::SP_EMAIL,
				contacts::SP_TELEFON,
				contacts::SP_TELEFON_FAX,
				);
		
				$repos = new DBupdateRepository ( $this->_MainUser->getGUID(), new contacts (), $contactId );

				$isUpdateUser = $repos->setUpdate ( $userDataArray, $updateColumn );
		
				return $isUpdateUser;
	}

	
	
	/**
	 * Setzt einen neuen User
	 *
	 * @param string $LastName  Name des Users
	 * @param array $fields Die Gruppe die der Users unterliegt
	 * @return citro_list Den eingetragenen User mit  guid|name|password|aeskey|visibil|date_create|admin Der Parameter "admin" bekommt man nur wenn man selber Admin ist
	 *
	 */
	public function ActionNew($LastName, $fields = array()) {
				
		
		require_once 'db/contact/contacts.php';
		$kData = array();

		// Setzen der Standartfelder
		$kData[contacts::SP_DATA_CREATE] = DBTable::DateTime ();
		$kData[contacts::SP_DATA_EDIT] = DBTable::DateTime ();
		$kData[contacts::SP_ACCESS_CREATE] = $this->_rightsAcl->getAccess()->getId();
		$kData[contacts::SP_ACCESS_EDIT] = $this->_rightsAcl->getAccess()->getId();
		
		if(!empty($fields["title_name"])){
			$title = contacts::testTitle($fields["title_name"]);
			if($title !== FALSE) $kData[contacts::SP_TITLE] = $title;
		}
		
		if(!empty($fields["first_add_name"])){
			$addFirstName = contacts::testFirstAddName($fields["first_add_name"]);
			if($addFirstName !== FALSE) $kData[contacts::SP_FIRST_ADD_NAME] = $addFirstName;
		}
		
		if(!empty($fields["first_name"])){
			$firstName = contacts::testFirstName($fields["first_name"]);
			if($firstName !== FALSE) $kData[contacts::SP_FIRST_NAME] = $firstName;
		}
		
		if(!empty($fields["affixname"])){
			$affixName = contacts::testAffixName($fields["affix_name"]);
			if($affixName !== FALSE) $kData[contacts::SP_AFFIX_NAME] = $affixName;
		}

		
		if(!empty($fields["firma"])){
			$firma = contacts::testAffixName($fields["firma"]);
			if($firma !== FALSE) $kData[contacts::SP_FIRMA] = $firma;
		}
		if(!empty($fields["position"])){
			$firma = contacts::testAffixName($fields["position"]);
			if($firma !== FALSE) $kData[contacts::SP_FIRMA_POSITION] = $firma;
		}
		if(!empty($fields["infotext"])){
			$info = contacts::testAffixName($fields["infotext"]);
			if($info !== FALSE) $kData[contacts::SP_KURZINFO] = $info;
		}
		

		// Testen des Pflichtfeldes Last Name oder abbruch
		$lastName = contacts::testLastName($LastName);
		if($lastName === FALSE)return FALSE;
		$kData[contacts::SP_LAST_NAME] = $LastName;
		
		
		
		
		// Adresss
		$adressData = NULL;
		
		if( !empty($fields["adr_ort"]) ){

			require_once 'db/contact/contact_address.php';
			$adressOrt = contact_address::testOrt($fields["adr_ort"]);
			if($adressOrt !== FALSE){
				
				$adressData = array();
				$adressData[contact_address::SP_ORT] = $adressOrt;
	
				$adressPlz = contact_address::testPLZ($fields["adr_plz"]);
				if($adressPlz !== FALSE)$adressData[contact_address::SP_PLZ] = $adressPlz;	
				
				$adressStreet = contact_address::testStreet($fields["adr_strasse"]);
				if($adressStreet !== FALSE)$adressData[contact_address::SP_STRASSE] = $adressStreet;	
								
				$adressLand = contact_address::testLand($fields["adr_land"]);
				if($adressLand !== FALSE)$adressData[contact_address::SP_LAND] = $adressLand;	
								
				$adressLandPart = contact_address::testLandPart($fields["adr_landpart"]);
				if($adressLandPart !== FALSE)$adressData[contact_address::SP_LAND_PART] = $adressLandPart;	
			
			}
			
		}

		
		// Telefon
		$phoneData = NULL;
		if(!empty($fields["phone_number"])){
			require_once 'db/contact/contact_phone.php';
			
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
			require_once 'db/contact/contact_email.php';
				
			$emailAdresss = contact_email::testEmail($fields["mail_adress"]);
			if($emailAdresss !== FALSE){
				$emailData = array();
				$emailData[contact_email::SP_ADRESS] = $emailAdresss;		
			}
		}
		
		
		
		// Daten einschreiben
		$db = contacts::getDefaultAdapter();
	
		$setInsert = TRUE;
		while ( $setInsert )
		{
			$newUnId = contacts::generateUnId("bt");
			$selTuble = $db->select();
			$selTuble->from(contacts::getTableNameStatic());
			$selTuble->where(contacts::SP_UNID." = ? ",$newUnId);
				
			$allTubles = $db->fetchAll($selTuble);

			if(count($allTubles) == 0){
				$kData[contacts::SP_UNID] = $newUnId;
				$setInsert = FALSE;
				break;
			}
						
		}
		
		
		
		$db->beginTransaction();
		
		
		try {
			$contactTab = new contacts();
			$contactsId = $contactTab->insert($kData);

			$updateData = array();
			
			if(is_array($adressData)){
				$adressData[contact_address::SP_CONTACT_ID] = $contactsId;
				$adressTab = new contact_address();
				$adressId = $adressTab->insert( $adressData);
				$updateData[contacts::SP_ADRESS_ID] = $adressId;
				
			}
			if(is_array($emailData)){
				$emailData[contact_email::SP_CONTACT_ID] = $contactsId;
				$mailTab = new contact_email();
				$emailId = $mailTab->insert( $emailData);
				
				$updateData[contacts::SP_EMAIL_ID] = $emailId;
			}
			if(is_array($phoneData )){
				$phoneData[contact_phone::SP_CONTACT_ID] = $contactsId;
				$phoneTab = new contact_phone();
				$phoneId = $phoneTab->insert( $phoneData);
				$updateData[contacts::SP_PHONE_ID] = $phoneId;
			}

			if(count($updateData) > 0){
				$db->update(contacts::getTableNameStatic(), $updateData, contacts::SP_ID."=".$contactsId);
			}
			
			
			
			$db->commit();
			
			return $newUnId;
		}catch (Exception $eTrans){
			$db->rollBack();
			throw new Exception($eTrans->getMessage(),E_ERROR);
			return FALSE;
		}
		

	}
	
	private function _setSearchData($kontaktId, $kontaktCategory, $lastName, $firstName = NULL, $ort= NULL, $firma = NULL){

// 		require_once 'citro/db/contact/contacts_search.php';
// 		$kSearchTab = new kontakt_search();
		
// 		$value = $lastName;
		
		
// 		if($firstName !== NULL){
// 			$value = $value."_".$firstName;
// 		}
// 		if($ort !== NULL){
// 			$value = $value."_".$ort;
// 		}
// 		if($firma !== NULL){
// 			$value = $value."_".$firma;
// 		}
	
		
// 		$kSearchTab->setNew($kontaktId,$value, $kontaktCategory);
		
	}
	
	/**
	 * Giebt daten des Kontaktes zurück wenn dieser gefunden wurde
	 * 
	 * @param unknown_type $Value
	 * @param integer $MaxBack
	 */
	public function ActionSearch($Value,$MaxBack = 5){
		
	}
	
	

}

?>