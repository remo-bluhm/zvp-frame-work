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
	public function ActionList($count, $offset = 0, $where = array(), $spalten = array()){
	
		require_once 'db/contact/Contacts.php';
	
		$db = Contacts::getDefaultAdapter();
	
		$resortSel = $db->select ();
	
		$spA = array();
		$spA["uid"] = "uid";
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
		if( array_key_exists('adr_plz',$where) || array_key_exists("adr_ort", $where) || array_key_exists("adr_strasse", $where) ){
			$resortSel->joinLeft(array('a'=>"contact_address"), "c.id = a.contacts_id", $adressSpaltenA );
		}else {
			$resortSel->joinLeft(array('a'=>"contact_address"), "c.main_contact_address_id = a.id", $adressSpaltenA );
		}
			
			
	
		$phoneSpaltenA = array();
		$phoneSpaltenA['p_art'] = "art";
		$phoneSpaltenA['p_number'] = "number";
		$phoneSpaltenA['p_text'] = "text";
		if( array_key_exists('phone_art',$where) || array_key_exists("phone_number", $where) || array_key_exists("phone_text", $where) ){
			$resortSel->joinLeft(array('p'=>"contact_phone"), "c.id = p.contacts_id", $phoneSpaltenA );
		}else {
			$resortSel->joinLeft(array('p'=>"contact_phone"), "c.main_contact_phone_id = p.id", $phoneSpaltenA );
		}
	
	
		$resortSel->where("c.deleted = ?", 0);
		$resortSel->where("c.type = ?", "GUEST");

		// Suche
		$this->listSearch ( $where, $resortSel );

			
			
		$resortSel->limit($count,$offset);
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
	 * Setzt einen neuen Gast
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
		
				$fields["type"] = "GUEST";
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
	
		require_once '../../db/contact/phone/Phone.php';
		$sel->joinLeft(array('p'=>Phone::getTableNameStatic()), "c.main_contact_phone_id = p.id" ,$phoneSp);
	
	
		$mailSp = array();
		$mailSp["mail_adress"] = "mailadress";
		$mailSp["mail_text"] = "text";
			
		require_once '../../db/contact/email/Email.php';
		$sel->joinLeft(array('em'=>Email::getTableNameStatic()), "c.main_contact_email = em.id" ,$mailSp);
	
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
			require_once '../../db/contact/phone/Phone.php';
				
			$selPhone = $db->select ();
			$selPhone->from( Phone::getTableNameStatic() , $phoneSp );
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