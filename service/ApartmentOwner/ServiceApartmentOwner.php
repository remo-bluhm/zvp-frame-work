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
		
		$resortSel->where("c.deleted = ?", 0);
		$resortSel->where("c.type = ?", "HIRER");
		
		// suche nach Namen
		if(!empty($where["uid"]))
			$resortSel->where("c.uid LIKE ?", $where["uid"]);
			
		// suche nach Namen
		if(!empty($where["last_name"]))
			$resortSel->where("c.last_name LIKE ?", $where["last_name"]."%");
		if(!empty($where["first_name"]))
			$resortSel->where("c.first_name LIKE ?", $where["first_name"]."%");
		
		
		if(!empty($where["zip"])){
			$groupIsOn = TRUE;
			$resortSel->where("a.plz LIKE ?", $where["zip"]."%");
		}
		if(!empty($where["ort"])){
			$groupIsOn = TRUE;
			$resortSel->where("a.ort LIKE ?", $where["ort"]."%");
		}
		if(!empty($where["street"])){
			$groupIsOn = TRUE;
			$resortSel->where("a.strasse LIKE ?", $where["street"]."%");
		}
		
		if(!empty($where["email"])){
			$groupIsOn = TRUE;
			$resortSel->where("m.mailadress LIKE ?", $where["email"]."%");
		}
			
			
		$resortSel->limit($count,$offset);
		$resortSel->group("c.id");
		
		$resort = $db->fetchAll( $resortSel );
		
		return $resort;
	
	
	}
	
	/**
	 * Arbeitet die Suche für die Listmethode ab
	 * @param where
	 * @param resortSel
	 */
	private function listSearch($where, $resortSel) {

		if(!is_array($where))$where = array();
		if(array_key_exists("last_name", $where))
			$resortSel->where("c.last_name LIKE ?", $where["last_name"]);
		if(array_key_exists("first_name", $where))
			$resortSel->where("c.first_name LIKE ?", $where["first_name"]);
	
		if(array_key_exists("adr_plz", $where)){
			$resortSel->where("a.plz LIKE ?", $where["adr_plz"]);
		}
		
		if(array_key_exists("adr_ort", $where)){
			$resortSel->where("a.ort LIKE ?", $where["adr_ort"]);
		}
		
		if(array_key_exists("adr_strasse", $where)){
			$resortSel->where("a.strasse LIKE ?", $where["adr_strasse"]);
		}
	}

	
	
	
	
	
	
	/**
	 * Giebt die Contactdaten eines Contactes zurück
	 *
	 * @param string $contactuid Die Uid des Contacts als String
	 * @param array $spalten
	 * @return array|bool FALSE
	 */
	public function ActionSingle($contactuid, $spalten = array()){

			
		if(is_string($spalten) && $spalten=="*"){
			$spalten = array();
			$spalten[] = "usercreate_name";
			$spalten[] = "useredit_name";
			$spalten[] = "all_address";
			$spalten[] = "all_phone";
			$spalten[] = "all_email";
		}
		if(!is_array($spalten))$spalten = array();
	
		require_once 'db/contact/Contacts.php';
		$db = Contacts::getDefaultAdapter();

		
		$spA = array();
		$spA["uid"] = "uid";
		//$spA["type"] = "type";
		$spA["id_name"] = "id";
		$spA["create_date"] = "edata";
		$spA["edit_date"] = "vdata";
		$spA["name_title"] = "title_name";
		$spA["name_first"] = "first_name";
		$spA["name_firstadd"] = "first_add_name";
		$spA["name_last"] = "last_name";
		$spA["name_affix"] = "affix_name";
		
		$spA["address_id"] = "main_contact_address_id";
		$spA["phone_id"] = "main_contact_phone_id";
		$spA["email_id"] = "main_contact_email_id";

// 		$spA["firma"] = "firma";
// 		$spA["position"] = "position";
		
		$sel = $db->select ();
		$sel->from( array('c' => "contacts" ), $spA );
		
		if( in_array('usercreate_name',$spalten) ){

			$sel->joinLeft(array('a'=>"sys_access"), "c.access_create = a.id",array("create_access_guid"=>"guid") );
			$sel->joinLeft(array('c1'=>"contacts"), "a.contacts_id = c1.id", array ('create_access_name' => 'CONCAT(c1.first_name," ",c1.last_name )' ) );
		}
		
		if( in_array('useredit_name',$spalten) ){
			require_once 'db/sys/access/sys_access.php';
			$sel->joinLeft(array('a2'=>"sys_access"), "c.access_edit = a2.id" ,array("edit_access_guid"=>"guid") );
			$sel->joinLeft(array('c2'=>"contacts"), "a2.contacts_id = c2.id", array ('edit_access_name' => 'CONCAT(c2.first_name," ",c2.last_name )')  );
		}

		
		$adresSp = array();
		$adresSp["adr_id"] = "id";
		$adresSp["adr_art"] = "art";
		$adresSp["adr_nameline"] = "nameline";
		$adresSp["adr_street"] = "strasse";
		$adresSp["adr_ort"] = "ort";
		$adresSp["adr_zip"] = "plz";
		$adresSp["adr_land"] = "land";
		$adresSp["adr_landpart"] = "landpart";
		$adresSp["adr_infotext"] = "infotext";
		
		//$sel->joinLeft(array('ca'=>"contact_address"), "c.main_contact_address_id = ca.id" ,$adresSp);

		
 		$phoneSp = array();
 		$phoneSp["phone_id"] = "id";
 		$phoneSp["phone_art"] = "art";
 		$phoneSp["phone_number"] = "number";
 		$phoneSp["phone_text"] = "text";
		
 
 		//$sel->joinLeft(array('p'=>"contact_phone"), "c.main_contact_phone_id = p.id" ,$phoneSp);

		
 		$mailSp = array();
 		$mailSp["email_id"] = "id";
 		$mailSp["email_adress"] = "mailadress";
 		$mailSp["email_text"] = "text";
 		

 		//$sel->joinLeft(array('em'=>"contact_email"), "c.main_contact_email_id = em.id" ,$mailSp);
		
		$sel->where("c.uid = ?",$contactuid);
		$sel->where("c.deleted = ?", "0");
		$sel->where("c.type = ?", "HIRER");
		

		
		$contactA = $db->fetchRow($sel);
		
		// falls nichts gefunden wurde dann abbruch
		if($contactA === FALSE) return FALSE;

			
			//////////////////////////////////////
		if( in_array('all_address',$spalten) ){
	
			
			$mainAddressId = (int)$contactA["address_id"];
			$adresSp["adr_is_main"] = "IF(`id` = ".$mainAddressId.", 'TRUE', 'FALSE' ) ";
			
			$selAdress = $db->select ();
			$selAdress->from( "contact_address" , $adresSp );
			$selAdress->where("contacts_id = ?",$contactA["id_name"]);
			

			$contactA["adresses"] = $db->fetchAll($selAdress);
		}
		//////////////////////////////////////////////////////////////////
		if( in_array('all_phone',$spalten) ){
			
			$mainPhoneId = (int)$contactA["phone_id"];
			$phoneSp["phone_is_main"] = "IF(`id` = ".$mainPhoneId.", 'TRUE', 'FALSE' ) ";
					
			$selPhone = $db->select ();
			$selPhone->from( "contact_phone" , $phoneSp );
			$selPhone->where("contacts_id = ?",$contactA["id_name"]);
			
			$contactA["numbers"] = $db->fetchAll($selPhone);
		}
		//////////////////////////////////////////////////////////////////
		if( in_array('all_email',$spalten) ){
				
			$mainEmailId = (int)$contactA["email_id"];
			$mailSp["email_is_main"] = "IF(`id` = ".$mainEmailId.", 'TRUE', 'FALSE' ) ";
				
			$selEmail = $db->select ();
			$selEmail->from( "contact_email" , $mailSp );
			$selEmail->where("contacts_id = ?",$contactA["id_name"]);
				
			$contactA["emails"] = $db->fetchAll($selEmail);
		}


		unset($contactA["id_name"]);
		unset($contactA["address_id"]);
		unset($contactA["phone_id"]);
		unset($contactA["email_id"]);
	
		//FireBug::setDebug($contactA,"ServiceContact Single");
		return $contactA;
	
	
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
	
		// Prüfen des lastName
		require_once 'db/contact/Contacts.php';
		$lastName = Contacts::testLastName($lastName);
		if( $lastName !== NULL ){
	
	
			$contTab = new Contacts();
			$contTab->getDefaultAdapter()->beginTransaction();
			try {
	
				$fields["cont_type"] = "HIRER";
				$contactUid = $contTab->insertData( $this->getAccess()->getId() , $lastName,$fields);
					
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
	
	
	
	
	

	
	

}

?>