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
	public function ActionList($count = 10, $offset = 0, $where = array(), $spalten = array()){

 		require_once 'db/resort/resort.php';

		$db = Resort::getDefaultAdapter();
		
		
		$resortSel = $db->select ();
		
		$spA = array();
		$spA["resort_uid"] = "uid";
		$spA["resort_name"] = "name";
		$spA["resort_visibil"] = "visibil";
			
		$spA["creat_date"] = "edata";
		$spA["edit_date"] = "vdata";
		
// 		$spA["resort_create_guid"] = "usercreate";
// 		$spA["resort_edit_guid"] = "useredit";
		
		$spA["resort_strasse"]="street";
		
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
 			$resortSel->where("r.street LIKE ?", $where["street"]."%"); 
 		
 		
		$resortSel->limit($count,$offset);
		
		
 		//echo $resortSel->__toString();
		
		//$resort = $db->fetchAll( $stringSel );
		$resort = $db->fetchAll( $resortSel );
	
		return $resort;
		
		
	}
	
	
	
	
	
	/**
	 * Sucht ein Resort
	 * @param string $searchStr
	 * @param integer $count
	 * @return array
	 */
	public function ActionSearch( $searchStr , $count = 10) {
	
		$db = DBConnect::getConnect();
	
		$spA = array();
		$spA["resort_uid"] = "uid";
		$spA["resort_name"] = "name";
		$spA["resort_street"]="street";
	
		$resortSel = $db->select();
		$resortSel->from( array('r' => "resort") ,$spA);
	
				
		$citySp =  array('city_name'=>'c.name');
		$resortSel->joinLeft(array('c'=>"resort_city"), "c.id = r.city_id", $citySp);
		
		$searchA = explode(" ", $searchStr);
		$cleanSearA = array();
		foreach ($searchA as $searchElem){
			if(!empty($searchElem)){
				$elemClean = trim($searchElem);
				$cleanSearA[] = $elemClean;
			}
		}
		$numberSearchElem = count($cleanSearA);
		if($numberSearchElem < 1) return array();
			
		$searchstring = "(r.name LIKE '".$cleanSearA[0]."%' OR r.street LIKE '".$cleanSearA[0]."%' OR c.name LIKE '".$cleanSearA[0]."%') ";
		 
		for($i=1; $i<count($cleanSearA); $i++) //bei mehr als einem Suchbegriff, weitere zur Abfrage hinzufügen
		{
			$searchstring .= " AND (r.name LIKE '".$cleanSearA[$i]."%' OR r.street LIKE '".$cleanSearA[$i]."%' OR c.name LIKE '".$cleanSearA[$i]."%') ";
		}
		
		$resortSel->where($searchstring);
		$resortSel->limit($count);
		//echo $resortSel->__toString();

		$resort = $db->fetchAll( $resortSel );
		if(!is_array($resort))$resort = array();	
		return $resort;
	
	
	}
	
	/**
	 * Sucht ein Resort
	 * @param string $searchStr
	 * @param integer $count
	 * @return array
	 */
	public function ActionSearchName( $searchStr , $count = 10) {
	
		$db = DBConnect::getConnect();
	
		$spA = array();
		$spA["resort_uid"] = "uid";
		$spA["resort_name"] = "name";
		$spA["resort_street"]="street";
	
		$resortSel = $db->select();
		$resortSel->from( array('r' => "resort") ,$spA);
	
	
		$citySp =  array('city_name'=>'c.name');
		$resortSel->joinLeft(array('c'=>"resort_city"), "c.id = r.city_id", $citySp);
		
		$resortSel->where($db->quoteInto("r.name LIKE ?", $searchStr."%"));
		$resortSel->limit($count);
		//echo $resortSel->__toString();
	
		$resort = $db->fetchAll( $resortSel );
		if(!is_array($resort))$resort = array();

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
	 * Prüft ob eine Resortuid existiert
	 * @param string $uidname Der Resortname
	 * @return int 1 resource existiert 0 exestiert nicht
	 */
	public function ActionExist($uidname){
	
		if(empty($uidname))return 1; // falls eine lehre anfrage kommt diese als standart mit existiert zurückgeben
		require_once 'db/resort/Resort.php';
		$tab = new Resort();
		$id = $tab->existUid($uidname);
		if(is_int($id)&&$id > 0){
			//Gefunden
			return 1;
		}else {
			//nicht gefunden
			return 0;
		}
	
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
	 * @param string $resortUid nach dem Gesucht werden soll 	 
	 * @param array $spalten
	 * @return array|NULL
	 */
	public function ActionSingle($resortUid , $spalten = array()){

	    if(is_string($spalten) && $spalten=="*"){
	        $spalten = array();
	        $spalten[] = "usercreate_name";
	        $spalten[] = "useredit_name";
	        $spalten[] = "all_address";
	        $spalten[] = "all_phone";
	        $spalten[] = "all_email";
	    }
	    if(!is_array($spalten))$spalten = array();
	    
		require_once 'db/resort/Resort.php';
// 		require_once 'db/resort/ResortCity.php';
// 		require_once 'db/contact/contact_access.php';
// 		require_once 'db/contact/Contacts.php';
		
		
		$db = Resort::getDefaultAdapter();
		
		
		
		$spA = array();
		$spA["uid"] = "uid";
		$spA["name"] = "name";
		$spA["visibil"] = "visibil";

		$spA["create_date"] = "edata";
		$spA["edit_date"] = "vdata";
		
//  		$spA["create_guid"] = "access_create";
//  		$spA["edit_guid"] = "access_edit";

		$spA["street"]="strasse";

		$spA["gmap_lat"]= "gmap_lat";
		$spA["gmap_lng"]= "gmap_lng";
		$spA["gmap_zoom"]= "gmap_zoom";
	
		
		$sel = $db->select ();
		$sel->from(array('r' => "resort") ,$spA);
		
		if( in_array('usercreate_name',$spalten) ){
			$sel->joinLeft(array('a'=>"sys_access"), "r.access_create = a.id",array("create_access_guid"=>"guid") );
			$sel->joinLeft(array('c1'=>"contacts"), "a.contacts_id = c1.id", array ('create_access_name' => 'CONCAT(c1.first_name," ",c1.last_name )' ) );
		}
		
		if( in_array('useredit_name',$spalten) ){
			$sel->joinLeft(array('a2'=>"sys_access"), "r.access_edit = a2.id" ,array("edit_access_guid"=>"guid") );
			$sel->joinLeft(array('c2'=>"contacts"), "a2.contacts_id = c2.id", array ('edit_access_name' => 'CONCAT(c2.first_name," ",c2.last_name )')  );
		}
		
		
		
		$sel->joinLeft(array('o'=>'resort_city'), "o.id = r.city_id", array ('ort_name' => 'name')  );
		
		
		$sel->where("r.uid=?", $resortUid);
		$sel->where("'r.deleted'=?", 0);
		
		$resort = $db->fetchRow( $sel );
		
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
	    if($existUid !== FALSE) throw new Exception("Resort UID Existiert schon", E_ERROR);
		
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