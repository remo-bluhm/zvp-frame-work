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
	public function ActionList($count, $offset = 0, $where = array(), $spalten = array()){
	
		require_once 'db/contact/Contacts.php';
	
		$db = Contacts::getDefaultAdapter();

		$resortSel = $db->select ();
	
		$spA = array();
		$spA["uid"] = "uid";
		//$spA["anzahl"] = "count(uid)";
		$spA["type"] = "type";
		$spA["title_name"] = "title_name";
		$spA["last_name"] = "last_name";
		$spA["first_name"] = "first_name";
		$spA["first_add_name"] = "first_add_name";
		$spA["affix_name"] = "affix_name";
		
		$spA["firma"] = "firma";
		$spA["position"] = "position";
		

		$resortSel->from(array('c' => "contacts") ,$spA);

	
 		
 			
 		require_once 'db/contact/address/Address.php';
 		$adressSpaltenA = array();
 		$adressSpaltenA['a_plz'] = "plz";
 		$adressSpaltenA['a_ort'] = "ort";
 		$adressSpaltenA['a_strasse'] = "strasse";
 		if( !empty($where["zip"]) || !empty($where["ort"]) || !empty($where["street"]) ){	
 			$adressJoin = "c.id = a.contacts_id";
 		}else {
 			$adressJoin ="c.main_contact_address_id = a.id";
 		}
		$resortSel->joinLeft(array('a'=>"contact_address"),$adressJoin , $adressSpaltenA );
 		
 		// Join der Email
 		$mailSpaltenA = array("m_adress" => "mailadress");
 		!empty($where['email'])  ? $emailJoin = "c.id = m.contacts_id": $emailJoin = "c.main_contact_email_id = m.id";
		$resortSel->joinLeft(array('m'=>"contact_email"),$emailJoin, $mailSpaltenA );
		
		// Join der Email
		$mailSpaltenA = array("p_number" => "number");
		!empty($where['phonenumber'])  ? $phoneJoin = "c.id = p.contacts_id": $phoneJoin = "c.main_contact_email_id = p.id";
		$resortSel->joinLeft(array('p'=>"contact_phone"),$phoneJoin, $mailSpaltenA );
		

		
// 		if( in_array('ort_name',$spalten) || array_key_exists("ort", $where)  ){
// 			require_once 'db/resort/resort_orte.php';
// 			$resortSel->joinLeft(array('o'=>resort_orte::getTableNameStatic()), "o.id = r.ort_id", array ( 'ort_name'=>'o.name') );
// 		}
	
// 		if( in_array('usercreate_name',$spalten) ){
// 			$resortSel->joinLeft(array('u'=>"sys_access"), "c.access_create = u.guid",array("access_create_guid"=>"u.guid") );
// 			//$resortSel->joinLeft(array('c2'=>"contacts"), "u.contacts_id = c.id", array ('usercreate_name' => 'CONCAT(c2.first_name," ",c2.last_name )' ) );
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
 		if(!empty($where["uid"]))
 			$resortSel->where("c.uid LIKE ?", $where["uid"]);
 		
		// suche nach Namen
		if(!empty($where["last_name"]))
			$resortSel->where("c.last_name LIKE ?", $where["last_name"]);
		if(!empty($where["first_name"]))
			$resortSel->where("c.first_name LIKE ?", $where["first_name"]);
	
		
		if(!empty($where["zip"])){
			$groupIsOn = TRUE;
			$resortSel->where("a.plz LIKE ?", $where["zip"]);
		}
		if(!empty($where["ort"])){
			$groupIsOn = TRUE;
			$resortSel->where("a.ort LIKE ?", $where["ort"]);
		}
		if(!empty($where["street"])){
			$groupIsOn = TRUE;
			$resortSel->where("a.strasse LIKE ?", $where["street"]);
		}
		
		if(!empty($where["email"])){
			$groupIsOn = TRUE;
			$resortSel->where("m.mailadress LIKE ?", $where["email"]);
		}
			
			
		$resortSel->limit($count,$offset);
		$resortSel->group("c.id");
//  		$selectStr = $resortSel->__toString();
//  		echo $selectStr;
		$resort = $db->fetchAll( $resortSel );
	
		return $resort;
	
	
	}
	
	

	
	/**
	 * Giebt die Contactdaten eines Contactes zurück
	 *
	 * @param string $contactuid Die Uid des Contacts als String
	 * @param array $spalten
	 * @return array
	 */
	public function ActionSingle($contactuid, $spalten = array()){
	
		
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

		
		$spA["address_id"] = "main_contact_address_id";
		$spA["phone_id"] = "main_contact_phone_id";
		$spA["email_id"] = "main_contact_email_id";
		
		
		
		$spA["firma"] = "firma";
		$spA["position"] = "position";
		

				
		$sel = $db->select ();
		$sel->from( array('c' => "contacts" ), $spA );
		
		if( in_array('usercreate_name',$spalten) ){
			
			$sel->joinLeft(array('a'=>"sys_access"), "c.access_create = a.id",array() );
			$sel->joinLeft(array('c1'=>"contacts"), "a.contacts_id = c1.id", array ('usercreate_name' => 'CONCAT(c1.first_name," ",c1.last_name )' ) );
		}
		
		if( in_array('useredit_name',$spalten) ){
			require_once 'db/sys/access/sys_access.php';
			$sel->joinLeft(array('a2'=>"sys_access"), "c.access_edit = a2.id" ,array() );
			$sel->joinLeft(array('c2'=>"contacts"), "a2.contacts_id = c2.id", array ('useredit_name' => 'CONCAT(c2.first_name," ",c2.last_name )')  );
		}

		
		$adresSp = array();
		$adresSp["adr_art"] = "art";
		$adresSp["adr_land"] = "land";
		$adresSp["adr_landpart"] = "landpart";
		$adresSp["adr_plz"] = "plz";
		$adresSp["adr_ort"] = "ort";
		$adresSp["adr_strasse"] = "strasse";
		$adresSp["adr_infotext"] = "info_text";
		
		$sel->joinLeft(array('ca'=>"contact_address"), "c.main_contact_address_id = ca.id" ,$adresSp);

		
 		$phoneSp = array();
 		$phoneSp["phone_art"] = "art";
 		$phoneSp["phone_number"] = "number";
 		$phoneSp["phone_text"] = "text";
		
 
 		$sel->joinLeft(array('p'=>"contact_phone"), "c.main_contact_phone_id = p.id" ,$phoneSp);

		
 		$mailSp = array();
 		$mailSp["mail_adress"] = "mailadress";
 		$mailSp["mail_text"] = "text";
 		

 		$sel->joinLeft(array('em'=>"contact_email"), "c.main_contact_email_id = em.id" ,$mailSp);
		
		$sel->where("c.uid = ?",$contactuid);
		$sel->where("c.deleted = ?", "0");
		
		$contactA = $db->fetchRow($sel);
		
		// falls nichts gefunden wurde dann abbruch
		if($contactA === FALSE) return FALSE;
			
		//////////////////////////////////////
		if( in_array('all_address',$spalten) ){
	
			
			$mainAddressId = (int)$contactA["address_id"];
			$adresSp["is_main"] = "IF(`id` = ".$mainAddressId.", 'TRUE', 'FALSE' ) ";
			
			$selAdress = $db->select ();
			$selAdress->from( "contact_address" , $adresSp );
			$selAdress->where("contacts_id = ?",$contactA["id_name"]);

			$contactA["adresses"] = $db->fetchAll($selAdress);
		}
		//////////////////////////////////////////////////////////////////
		if( in_array('all_phone',$spalten) ){
			
			$mainPhoneId = (int)$contactA["phone_id"];
			$phoneSp["is_main"] = "IF(`id` = ".$mainPhoneId.", 'TRUE', 'FALSE' ) ";
					
			$selPhone = $db->select ();
			$selPhone->from( "contact_phone" , $phoneSp );
			$selPhone->where("contacts_id = ?",$contactA["id_name"]);
			
			$contactA["phones"] = $db->fetchAll($selPhone);
		}
		//////////////////////////////////////////////////////////////////
		if( in_array('all_email',$spalten) ){
				
			$mainEmailId = (int)$contactA["email_id"];
			$mailSp["is_main"] = "IF(`id` = ".$mainEmailId.", 'TRUE', 'FALSE' ) ";
				
			$selEmail = $db->select ();
			$selEmail->from( "contact_email" , $mailSp );
			$selEmail->where("contacts_id = ?",$contactA["id_name"]);
				
			$contactA["emails"] = $db->fetchAll($selEmail);
		}


		unset($contactA["id_name"]);
		unset($contactA["address_id"]);
		unset($contactA["phone_id"]);
		unset($contactA["email_id"]);
		return $contactA;
	
	
	}
	

	
	/**
	 * Giebt meinene Contactdaten zurück
	 *
	 * @return citro_list Die contact daten
	 */
	public function ActionContactList(){
	
	
		require_once 'citro/db/contact/contacts.php';
		$contactTab = new Contacts();
		$contactSel = $contactTab->select();
	
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

		require_once 'db/contact/Contacts.php';
		$kontaktTab = new Contacts();
		$contactSel = $kontaktTab->select();
		$contactSel->where(Contacts::SP_ID . " = ?", $contactId);
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
	//$this->getResource()->exist("Group")
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
		
		// Setzen der $fieldsvariabel auf array
		if(!is_array($fields))$fields = array();
		//FireBug::setDebug($fields);
		// Prüfen des lastName
		require_once 'db/contact/Contacts.php';
		$lastName = Contacts::testLastName($lastName);
		if( $lastName !== NULL ){
		
		
			$contTab = new Contacts();
			$contTab->getDefaultAdapter()->beginTransaction();
			try {
		
				
				$contactId =  $contTab->insertDataFull( $this->getAccess()->getId(), $lastName, $fields);
				$contactUid = $contTab->getUid();
				
				// setzen von Adressen
				$mainAdressId = NULL;
				if(array_key_exists("adresses",$fields) && is_array($fields["adresses"]) ){
					require_once 'db/contact/address/Address.php';
					$adrTab = new Address();
					foreach ($fields["adresses"] as $adrFields){
						$adrTab->clearData();	
						if(is_array($adrFields) && !empty($adrFields["ort"]) ){
							$adressId = $adrTab->insertDataFull($this->getAccess()->getId(), $contactId, $adrFields);
							if (!empty($adrFields["is_main"]) && $adressId !== NULL ){
								if( strtoupper( $adrFields["is_main"]) == "TRUE" ) $mainAdressId = $adressId;
							}
						}
					}
				}
				
				// setzen der Telefonnummern
				$mainPhoneId = NULL;
				if(array_key_exists("numbers",$fields) && is_array($fields["numbers"]) ){
					require_once 'db/contact/phone/Phone.php';
					$phoneTab = new Phone();
					foreach ($fields["numbers"] as $phoneFields){
						if(is_array($phoneFields) && !empty($phoneFields["number"])){
							$phoneId = $phoneTab->insertDataFull($this->getAccess()->getId(), $contactId, $phoneFields);
							if (!empty($phoneFields["is_main"]) && $phoneId !== NULL ){
								if(strtoupper ( $phoneFields["is_main"]) == "TRUE") $mainPhoneId = $phoneId;
							}
						}
					}
				}
				
				// setzen der Mails
				$mainMailId = NULL;
				if(array_key_exists("emails",$fields) && is_array($fields["emails"]) ){
					require_once 'db/contact/email/Email.php';
					$mailTab = new Email();
					foreach ($fields["emails"] as $mailFields){
						if(is_array($mailFields) && !empty($mailFields["adress"])){
							$mailTab->setText($mailFields["text"]);
							$mailTab->setEmail($mailFields["adress"]);
							$mailId = $mailTab->insertDataFull($this->getAccess()->getId(), $contactId,$mailFields);
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
				if(count($updateData) > 0 )$contTab->update($updateData, "id = ".$contactId);
	
				
				//$contactUid = NULL;
				
				// Nochmaliges Prüfen auf contactid
				if($contactUid === NULL){
					$contTab->getAdapter()->rollBack();
					return FALSE;
				}
				$contTab->getAdapter()->commit();		
				return $contactUid;
		
			} catch (Exception $e) {
				$contTab->getAdapter()->rollBack();
				return FALSE;
			}
		
		}else{
			// Fehler da der Lastname nicht valiede ist
			return FALSE;
		}
		return FALSE;
		// FireBug::setDebug($newUnId);
		

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