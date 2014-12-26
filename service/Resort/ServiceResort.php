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
	public function ActionList($count, $offset, $where = array(), $spalten = array()){

 		require_once 'db/resort/resort.php';

		$db = Resort::getDefaultAdapter();
		
		
		$resortSel = $db->select ();
		
		$spA = array();
		$spA["resort_name"] = "name";
		$spA["resort_visibil"] = "visibil";
			
		$spA["creat_date"] = "edata";
		$spA["edit_date"] = "vdata";
		
// 		$spA["resort_create_guid"] = "usercreate";
// 		$spA["resort_edit_guid"] = "useredit";
		
		$spA["resort_strasse"]="strasse";
		
		$spA["gmap_lat"]= "gmap_lat";
		$spA["gmap_lng"]= "gmap_lng";
		$spA["gmap_zoom"]= "gmap_zoom";
		
		if( in_array('apartment_count',$spalten) )
			$spA['apartment_count'] = 'IFNULL(count(a.id),0)';
		
		 

		
		$resortSel->from(array('r' => "resort") ,$spA);
		
		if( in_array('ort_name',$spalten) || array_key_exists("ort", $where)  ){		
			require_once 'db/resort/ResortCity.php';
			$citySp =  array ( 'city_name'=>'o.name','city_zip'=>'o.zip') ;
			
			$resortSel->joinLeft(array('o'=>"resort_city"), "o.id = r.city_id", $citySp);
		}

		
		$spaltenInApartment = array();
		if( in_array('apartment_namen',$spalten) ){
			$spaltenInApartment['apartment_namen'] = 'GROUP_CONCAT(a.name )';
		}
		
		if( in_array('apartment_count',$spalten) || in_array('apartment_namen',$spalten) ){
			require_once 'db/apartment/Apartment.php';
			$resortSel->joinLeft(array('a'=>"apartment"), "a.resort_id = r.id"  , $spaltenInApartment);
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
			require_once 'db/resort/ResortCity.php';
			$searchListSel->joinLeft(array('o'=>ResortCity::getTableNameStatic()), "o.id = r.ort_id", array ('ort_name' => 'name')  );
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
		require_once 'db/resort/ResortCity.php';
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

		$resortSel->joinLeft(array('o'=>ResortCity::getTableNameStatic()), "o.id = r.ort_id", array ('ort_name' => 'name')  );
		
		
		$resortSel->where("r.name=?", $name);
		$resortSel->where("'r.deleted'=?", 0);
		
		$resort = $db->fetchRow( $resortSel );
		
		return $resort;

	}

	/**
	 * Erstellt ein Neues Resourt
	 * @param string $resortUid
	 * @param string $name
	 * @param string $cityUid 
	 * @param array $data
	 */
	public function ActionNew($resortUid,$name,$cityUid, $data = array()){
		
	    if(!is_array($data))$data = array();
		    
		require_once 'db/resort/Resort.php';
		$resortTab = new Resort();
			
		// setzen des Accesses
		$accessId = $this->_rightsAcl->getAccess()->getId();
	
		// hollen der OrtsId
		$citySql = $resortTab->getAdapter()->select()->from("resort_city")->where("name_uid=?",$cityUid);
		$cityId = (integer) $resortTab->getAdapter()->fetchOne($citySql);

	
	    $sendUid = $resortTab->post_slug($resortUid);

	    $sendUid = Resort::testUid($sendUid);
	    if($sendUid === NULL) throw new Exception("Resort Uid ist nicht Valiede", E_ERROR);
	    
	    $existUid = $resortTab->existUid($sendUid);
	    if($existUid !== FALSE) throw new Exception("Resort Existiert", E_ERROR);
		
		$newOrtId = $resortTab->insertDataFull($accessId,$cityId,$sendUid,$name, $data);
			
		return TRUE;
				
				
	
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