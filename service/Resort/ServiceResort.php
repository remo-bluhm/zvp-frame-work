<?php

require_once 'citro/service-class/AService.php';

/**
 * Dieser Serviece Verwaltet die orte der Resourts
 *
 * @author Max Plank
 * @version 1.0
 *         
 */
class ServiceResort extends AService {
	

	private $_rootName = "Orte";
	private $_rootDescription = "Enthällt alle orte ";
	
	
	/**
	 * Der User construktor
	 */
	function __construct() {
		
		parent::__construct ();
	
	}

	

	/**
	 * Giebt eine Liste von Apartments zurück
	 * @param integer $count
	 * @param integer $offset
	 * @param array $where name|ort
	 * @param array $spalten
	 * @return array
	 */
	public function ActionGetList($count, $offset, $where = array(), $spalten = array()){

 		require_once 'db/resort/Resort.php';

		$db = Resort::getDefaultAdapter();
		
		
		$resortSel = $db->select ();
		
		$spA = array();
		$spA["name"] = "name";
		$spA["visibil"] = "visibil";
			
		$spA["creat_date"] = "edata";
		$spA["edit_date"] = "vdata";
		
		$spA["create_guid"] = "usercreate";
		$spA["edit_guid"] = "useredit";
		
		$spA["strasse"]="strasse";
		
		$spA["gmap_lat"]= "gmap_lat";
		$spA["gmap_lng"]= "gmap_lng";
		$spA["gmap_zoom"]= "gmap_zoom";
		
		if( in_array('apartment_count',$spalten) )
			$spA['apartment_count'] = 'IFNULL(count(a.id),0)';
		
		 

		
		$resortSel->from(array('r' => Resort::getTableNameStatic()) ,$spA);
		
		if( in_array('ort_name',$spalten) || array_key_exists("ort", $where)  ){		
			require_once 'db/resort/ResortOrte.php';
			$resortSel->joinLeft(array('o'=>ResortOrte::getTableNameStatic()), "o.id = r.ort_id", array ( 'ort_name'=>'o.name') );
		}

		if( in_array('usercreate_name',$spalten) ){
			require_once 'db/sys/access/sys_access.php';
			require_once 'db/contact/Contacts.php';
			$resortSel->joinLeft(array('u'=>sys_access::getTableNameStatic()), "r.usercreate = u.guid ",array() );
			$resortSel->joinLeft(array('c'=>Contacts::getTableNameStatic()), "u.contacts_id = c.id", array ('usercreate_name' => 'CONCAT(c.first_name," ",c.last_name )' ) );
		}
		
		if( in_array('useredit_name',$spalten) ){
			require_once 'db/sys/access/sys_access.php';
			require_once 'db/contact/Contacts.php';
			$resortSel->joinLeft(array('u2'=>sys_access::getTableNameStatic()), "r.useredit = u2.guid " ,array() );
			$resortSel->joinLeft(array('c2'=>Contacts::getTableNameStatic()), "u2.contacts_id = c2.id", array ('useredit_name' => 'CONCAT(c2.first_name," ",c2.last_name )')  );
		}
		
		$spaltenInApartment = array();
		if( in_array('apartment_namen',$spalten) ){
			$spaltenInApartment['apartment_namen'] = 'GROUP_CONCAT(a.name )';
		}
		
		if( in_array('apartment_count',$spalten) ){
			require_once 'db/apartment/Apartment.php';
			$resortSel->joinLeft(array('a'=>Apartment::getTableNameStatic()), "a.resort_id = r.id"  , $spaltenInApartment);
			$resortSel->group("r.id");
		}
		
// 		$resortSel->where("r.name=?", $name);
 		$resortSel->where("r.deleted = ?", 0);
 		
 		// suche nach Namen
 		if(array_key_exists("name", $where))
 			$resortSel->where("r.name LIKE ?", $where["name"]."%");
 			
 		// Suche nach Ort
 		if(array_key_exists("ort", $where))
 			$resortSel->where("o.name LIKE ?", $where["ort"]."%");
 		
 		// Suche nach Strasse
 		if(array_key_exists("strasse", $where))
 			$resortSel->where("r.strasse LIKE ?", $where["strasse"]."%"); 
 		
 		
		$resortSel->limit($count,$offset);
		
		
 		//echo $resortSel->__toString();
		
		//$resort = $db->fetchAll( $stringSel );
		$resort = $db->fetchAll( $resortSel );
		
		return $resort;
		
		
	}
	
	
	/**
	 * Giebt die anzahl der Apartments zurück
	 * @param array $where
	 * @return array
	 */
	public function ActionCount( $where = array()){
		
		require_once 'db/resort/Resort.php';
		$db = Resort::getDefaultAdapter();
		
		$searchListSel = $db->select ();
		$searchListSel->from( array('r' => Resort::getTableNameStatic() ), array( "count(r.id)") );
		

		
		// Suche nach Ort
		if(array_key_exists("ort", $where)){
			require_once 'db/resort/ResortOrte.php';
			$searchListSel->joinLeft(array('o'=>ResortOrte::getTableNameStatic()), "o.id = r.ort_id", array ('ort_name' => 'name')  );
		}
			
		if(array_key_exists("name", $where))
			$searchListSel->where("r.name LIKE ?", $where["name"]."%");
			
		// Suche nach Strasse
		if(array_key_exists("strasse", $where))
			$searchListSel->where("r.strasse LIKE ?", $where["strasse"]."%");
		
		if(array_key_exists("ort", $where))
			$searchListSel->where("o.name LIKE ?", $where["ort"]."%");
		
		$allOrtCounts = $db->fetchOne($searchListSel);
		return $allOrtCounts;

		
	}
	
	
	/**
	 * Überschreibt die Daten
	 * @param string $name Der Resortname
	 * @param array $data Daten name,strasse
	 */
	public function ActionUpdate($name,$data){
		
		require_once 'db/resort/Resort.php';
		$tab = new Resort();
		$insData = array();
		if(isset($data["name"])){
			$insData["name"] = $data["name"] ;
		}
		if(isset($data["strasse"])){
			$insData["strasse"] = $data["strasse"] ;
		}
		$tab->update($insData, $tab->getAdapter()->quoteInto("name=?", $name));
		
		
	}
	
	/**
	 * Giebt ein einzelnes Resort zurück
	 * @param string $name nach dem Gesucht werden soll 
	 * @return array|NULL
	 */
	public function ActionGetResort($name){

		
	
		require_once 'db/resort/Resort.php';
		require_once 'db/resort/ResortOrte.php';
		require_once 'db/contact/contact_access.php';
		require_once 'db/contact/Contacts.php';
		
		
		$db = Resort::getDefaultAdapter();
		
		
		$resortSel = $db->select ();
		
		$spA = array();
		$spA["name"] = "name";
		$spA["visibil"] = "visibil";

		
		$spA["creat_date"] = "edata";
		$spA["edit_date"] = "vdata";
		
 		$spA["create_guid"] = "usercreate";
 		$spA["edit_guid"] = "useredit";

		
		$spA["strasse"]="strasse";

		$spA["gmap_lat"]= "gmap_lat";
		$spA["gmap_lng"]= "gmap_lng";
		$spA["gmap_zoom"]= "gmap_zoom";
	
		
		
		$resortSel->from(array('r' => Resort::getTableNameStatic()) ,$spA);
		
		$resortSel->joinLeft(array('u'=>contact_access::getTableNameStatic()), "r.usercreate = u.guid ",array() );
		$resortSel->joinLeft(array('c'=>Contacts::getTableNameStatic()), "u.contacts_id = c.id", array ('usercreate_name' => 'CONCAT(c.first_name," ",c.last_name )' ) );
		
		$resortSel->joinLeft(array('u2'=>contact_access::getTableNameStatic()), "r.useredit = u2.guid " ,array() );
		$resortSel->joinLeft(array('c2'=>Contacts::getTableNameStatic()), "u2.contacts_id = c2.id", array ('useredit_name' => 'CONCAT(c2.first_name," ",c2.last_name )')  );

		$resortSel->joinLeft(array('o'=>ResortOrte::getTableNameStatic()), "o.id = r.ort_id", array ('ort_name' => 'name')  );
		
		
		$resortSel->where("r.name=?", $name);
		$resortSel->where("'r.deleted'=?", 0);
		
		$resort = $db->fetchRow( $resortSel );
		
		return $resort;

	}

	/**
	 * Erstellt ein Neues Resourt
	 * @param string $name
	 * @param array $newData
	 */
	public function ActionNew($name, $newData = array()){
		
		try {
				
			require_once 'db/resort/Resort.php';
			$orteTab = new Resort();
				
			$data = array();
			$data['name'] = $name;
				
			if(isset($data["strasse"])){
				$data['strasse'] = $newData["strasse"];
			}
				
			$data['edata'] = DBTable::DateTime();
			$data['vdata'] = DBTable::DateTime();
			$data['usercreate'] = $this->_rightsAcl->getAccess()->getGuId();
			$data['useredit'] = $this->_rightsAcl->getAccess()->getGuId();
			$data['visibil'] = 1;
			$data['deleted'] = 0;
				
			
			$newOrtId = $orteTab->insert($data);
				
			return TRUE;
				
				
		} catch (Exception $errorDB) {
			
			return FALSE;
		}
	}
	
	/**
	 * Erstellt ein Neues Resourt
	 * @param string $name
	 * @param array $newData
	 */
	public function ActionNewOrt($name, $newData = array()){
	
		try {
	
			require_once 'db/resort/Resort.php';
			$orteTab = new Resort();
	
			$data = array();
			$data['name'] = $name;
	
			if(isset($data["strasse"])){
				$data['strasse'] = $newData["strasse"];
			}
	
			$data['edata'] = DBTable::DateTime();
			$data['vdata'] = DBTable::DateTime();
			$data['usercreate'] = $this->_rightsAcl->getAccess()->getGuId();
			$data['useredit'] = $this->_rightsAcl->getAccess()->getGuId();
			$data['visibil'] = 1;
			$data['deleted'] = 0;
	
				
			$newOrtId = $orteTab->insert($data);
	
			return TRUE;
	
	
		} catch (Exception $errorDB) {
				
			return FALSE;
		}
	}
	
	/**
	 * Giebt eine Liste von Resorts zurück
	 * @param string $searchname
	 * @param integer $max
	 * @param string $areas
	 * @return array
	 */
	public function ActionGetSearch($searchname, $max = 5, $areas = NULL){
	
	
		require_once 'db/resort/Resort.php';
		$db = Resort::getDefaultAdapter();
	
		$searchListSel = $db->select ();
		$searchListSel->from( array('r' => Resort::getTableNameStatic() ), array( "r.name") );
	
	
		$searchListSel->where("name LIKE ? " , $searchname."%");
		$searchListSel->limit($max);
	
		$all = $db->fetchAll($searchListSel);
	
	
		return $all;
	}
	
	
	
}

?>