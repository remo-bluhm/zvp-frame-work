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
	 * @return array
	 */
	public function ActionSingle($contactuid, $spalten = array()){
	
		$conn = DBConnect::getConnect();
		$value = $conn->fetchOne( "SELECT id FROM contacts where (".$conn->quoteInto("uid = ?", $contactuid).") AND (type='HIRER')"  );
		if($value == FALSE)return FALSE;
		
		$service = $this->_serviceFabric->getService(new Service("Contact"), $this->_resource);
		$action =  new Action("Single");
		$action->setParam("contactuid", $contactuid);
		//kann noch hinzugefügt werden
		//$action->setParam("spalten", $spalten);
		
	
		$actionBack = $this->_serviceFabric->getAction($this->_resource, $service, $action);
		
		return $actionBack;	
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