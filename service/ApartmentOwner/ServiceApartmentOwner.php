<?php

require_once 'citro/service-class/AService.php';

/**
 * Dieser Serviece verwaltet alle Contacte
 *
 * @author Max Plank
 * @version 1.0
 *         
 */
class ServiceApartmentOwner extends AService {
	
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
		
		

	
		$resortSel->from(array('c' => contacts::getTableNameStatic()) ,$spA);

		
 		
 			
 		require_once 'db/contact/address/contact_address.php';
 		$adressSpaltenA = array();
 		$adressSpaltenA['a_plz'] = "plz";
 		$adressSpaltenA['a_ort'] = "ort";
 		$adressSpaltenA['a_strasse'] = "strasse";
 		if( array_key_exists('adr_plz',$where) || array_key_exists("adr_ort", $where) || array_key_exists("adr_strasse", $where) ){	
 			$resortSel->joinLeft(array('a'=>contact_address::getTableNameStatic()), "c.id = a.contacts_id", $adressSpaltenA );
 		}else {
 			$resortSel->joinLeft(array('a'=>contact_address::getTableNameStatic()), "c.main_contact_address_id = a.id", $adressSpaltenA );
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

		
		$spA["address_id"] = "main_contact_address_id";
		
		
		
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
		
		require_once 'db/contact/address/contact_address.php';
		$sel->joinLeft(array('ca'=>contact_address::getTableNameStatic()), "c.main_contact_address_id = ca.id" ,$adresSp);

		
 		$phoneSp = array();
 		$phoneSp["phone_art"] = "art";
 		$phoneSp["phone_number"] = "number";
 		$phoneSp["phone_text"] = "text";
		
 		require_once 'db/contact/phone/contact_phone.php';
 		$sel->joinLeft(array('p'=>contact_phone::getTableNameStatic()), "c.main_contact_phone_id = p.id" ,$phoneSp);

		
 		$mailSp = array();
 		$mailSp["mail_adress"] = "mailadress";
 		$mailSp["mail_text"] = "text";
 		
 		require_once 'db/contact/email/contact_email.php';
 		$sel->joinLeft(array('em'=>contact_email::getTableNameStatic()), "c.main_contact_email_id = em.id" ,$mailSp);
		
		$sel->where("c.uid = ?",$contactuid);
		$sel->where("c.deleted = ?", "0");
		
		$contactA = $db->fetchRow($sel);
		
		// falls nichts gefunden wurde dann abbruch
		if($contactA === FALSE) return FALSE;
			
		//////////////////////////////////////
		if( in_array('all_address',$spalten) ){
			require_once 'db/contact/address/contact_address.php';
			
			$mainAddressId = (int)$contactA["address_id"];
			$adresSp["is_main"] = "IF(`id` = ".$mainAddressId.", '1', '0' ) ";
			
			$selAdress = $db->select ();
			$selAdress->from( contact_address::getTableNameStatic() , $adresSp );
			$selAdress->where("contacts_id = ?",$contactA["id_name"]);

			$contactA["adresses"] = $db->fetchAll($selAdress);
		}
		//////////////////////////////////////////////////////////////////
		if( in_array('all_phone',$spalten) ){
			require_once 'db/contact/phone/contact_phone.php';
					
			$selPhone = $db->select ();
			$selPhone->from( contact_phone::getTableNameStatic() , $phoneSp );
			$selPhone->where("contacts_id = ?",$contactA["id_name"]);
			
			$contactA["phones"] = $db->fetchAll($selPhone);
		}
		unset($contactA["id_name"]);
		unset($contactA["address_id"]);
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
	 * Giebt Die Daten zurück für den Update
	 *
	 * @param string $contactUId  Die Guid des Users als Strüng
	 * @return array rückgabe wert
	 * @citro_isOn true
	 */
	public function ActionGetToUpdate($contactUId) {

	
		// Inizialiseren eines ServiceContactUpdates das die Schnitstelle DBIUpdate enthällt
		require_once 'service/Contact/ServiceContactUpdateHelper.php';
		$servContUpd = new ServiceContactUpdateHelper($contactUId);
		
		// Inizialisieren des Update Reposetorys
		require_once 'citro/update/SelectFactory.php';
		$dbupdateReposetory = new SelectFactory( DBConnect::getConnect(),$servContUpd );
		//$daten = $dbupdateReposetory->toUpdate();
	

		require_once 'citro/update/UpdateHelpFunc.php';
		//$backArray = array("title","first_name","first_add_name","last_name","affix_name","uid","edata","vdata");
		//$backData = UpdateHelpFunc::getColumnToUpdate($dbupdateReposetory->getToUpdate(), $backArray);
		$backData = UpdateHelpFunc::insertHashKey($dbupdateReposetory);
		
		
		return $backData;
	
	}
	
	/**
	 * Ist für das Update der eigenen Daten
	 *
	 * @param string $contactUid
	 * @param string $hashKey
	 * @param array $data
	 * @citro_isOn true
	 */
	public function ActionUpdateData($contactUid,$hashKey, $data) {
	
		// Inizialiseren eines ServiceContactUpdates das die Schnitstelle DBIUpdate enthällt
		require_once 'service/Contact/ServiceContactUpdateHelper.php';
		$servContUpd = new ServiceContactUpdateHelper($contactUid);

		require_once 'citro/update/ChronologicalFactory.php';
		$myId = $this->_rightsAcl->getAccess()->getId(); // ist für die Personaliesierung der veränderung
		$dbCon = DBConnect::getConnect();
		$dbupdateReposetory = new ChronologicalFactory(	"MainContact",$myId,$dbCon,$servContUpd );
				
		$isOk = $dbupdateReposetory->update($contactUid, $hashKey, $data);
		return $isOk;
	}

	
	
	/**
	 * Setzt einen neuen User
	 *
	 * @param string $lastName  Name des Users
	 * @param array $fields Die Gruppe die der Users unterliegt
	 * @return citro_list Den eingetragenen User mit  guid|name|password|aeskey|visibil|date_create|admin Der Parameter "admin" bekommt man nur wenn man selber Admin ist
	 *
	 */
	public function ActionNew($lastName, $fields = array()) {
				
		if($this->getResource()->exist("Contact")){
			
			
			require_once 'citro/service-class/Service.php';
			$Service = new Service("Contact");
			
			$ServFab = $this->getServiceFabric();
			$contServ = $ServFab->getService($Service, $this->getResource(), $this->_rightsAcl);
			
			require_once 'citro/service-class/Action.php';
			$action = new Action("New");
			$action->setParam("lastName", $lastName);
			$action->setParam("fields", $fields);
		
			$backAction = $ServFab->getAction($this->getResource(), $contServ, $action);
			
			
			
			FireBug::setDebug($backAction);
			
			
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