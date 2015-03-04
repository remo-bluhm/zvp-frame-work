<?php

require_once 'citro/service-class/AService.php';

/**
 * Dieser Serviece Verwaltet die orte der Resourts
 *
 * @author Max Plank
 * @version 1.0
 *         
 */
class ServiceApartment extends AService {
	

	private $_rootName = "Orte";
	private $_rootDescription = "Enthällt alle orte ";
	
	
	/**
	 * Der User construktor
	 */
	function __construct() {

		parent::__construct ();
	
	}

	
	
	/**
	 * Setzt einen neuenes Apartment
	 *
	 * @param string $apartmName  Name des Apartments
	 * @param string $ownerUid  uid des Besizers(vermieters) dieser muss ein vorher angelegter Vermieter sein
	 * @param array $fields Die Einzuschreibenden Felder
	 * @return citro_list 
	 *
	 */
	public function ActionNew($apartmName,$ownerUid, $fields = array()) {
	
		//FireBug::setDebug($fields,"ServiceApartment::ActionNew->fields");

		
		// Tesetet den Apartmentname
		require_once 'db/apartment/Apartment.php';
		$apartmName = Apartment::testApartmName($apartmName);
		
		// Testet den Contact
		require_once 'db/contact/Contacts.php';
		$ownerUid = Contacts::testUID($ownerUid);
		
		// falls keiner gefunden wurde dann false ansonsten die id
		if($ownerUid === NULL  )return FALSE;

		// hollt die Besizer id 
		$contactsTab = new Contacts();
		$ownerId = $contactsTab->exist($ownerUid);
		
	
		if( $apartmName !== NULL && $ownerId !== FALSE  ){
			
			
			
			$apartmTab = new Apartment();
			$apartmId = $apartmTab->insertDataFull($this->_rightsAcl->getAccess()->getId(), $apartmName, $ownerId, $fields);
			$apartmId = NULL;
			if($apartmId !== NULL){
				
				return TRUE;
				
			}
		}
		return FALSE;
		
	
	}
	
	
	
	
	
	
	
	
	
	
	

	/**
	 * Giebt eine Liste von Apartments zurück
	 * @param integer $count
	 * @param integer $offset
	 * @param array $where kann ein array mit abfragen übergeben werden RESORT_ID|ORT_ID
	 * @param array $spalten
	 * @return array
	 */
	public function ActionList($count, $offset, $where = array(), $spalten = array()){

		require_once 'db/apartment/Apartment.php';
		$db = Apartment::getDefaultAdapter();
		$sel = $db->select();
		
		$spA = array();
		$spA["apart_name_uid"] = "name_uid";
		$spA["apart_name"] = "name";
		$spA["apart_visibil"] = "visibil";
			
		$spA["creat_date"] = "date_create";
		$spA["edit_date"] = "date_edit";
		
		$spA["create_guid"] = "user_create";
		$spA["edit_guid"] = "user_edit";
		

		
		$sel->from(array('a' => "apartment"), $spA );
		
		$ggSp = array();
		$ggSp["ggnr_ga"] = "gastgeber_nr";
		$ggSp["ggnr_zi"] = "zimmer_nr";
		$sel->joinLeft(array('gg'=>"ggnr"), "a.id = gg.apartment_id ",$ggSp );
	
		//$sel->joinLeft(array('g'=>"ggnr"), "g.zimmer_id = a.id ",array('ggnr' => 'gastgeber_nr','zimnr' => 'zimmer_nr') );
		
		// suche nach Namen
		if(!empty($where["owner_uid"])){
			$sel->joinLeft(array('c'=>"contacts"),"a.owner_id = c.id", array() );
			$sel->where("c.uid = ?", $where["owner_uid"]);
		}
		
		
		$resortSp = array();
		$resortSp["resort_name"] = "name";
		$resortSp["resort_uid"] = "uid";
		$resortSp["resort_street"] = "strasse";
		$sel->joinLeft(array('r'=>"resort"),"a.resort_id = r.id", $resortSp );
		
		$resortCitySp = array();
		$resortCitySp["resort_city_name"] = "name";
		$resortCitySp["resort_city_uid"] = "name_uid";
		$resortCitySp["resort_city_zip"] = "zip";
		$sel->joinLeft(array('rc'=>"resort_city"),"r.city_id = rc.id", $resortCitySp );

		
		// suche nach ResortName
		if(!empty($where["resort_name"])){
			$sel->where("r.name LIKE ?", $where["resort_name"]."%" );
		}
		
		
		if(!empty($where["search_name"])){
			$sel->where("a.name LIKE ?", $where["search_name"]."%");
		}
		
		$sel->limit($count,$offset);
		
		$result = $db->fetchAll($sel);


		return $result;
	}
	
	/**
	 * Giebt eine Liste von Apartments zurück
	 * @param string $ownerUid
	 * @param integer $count
	 * @param integer $offset
	 * @param array $where kann ein array mit abfragen übergeben werden RESORT_ID|ORT_ID
	 * @param array $spalten
	 * @return array
	 */
	public function ActionListOwner($ownerUid, $count, $offset, $where = array(), $spalten = array()){
	
		require_once 'db/apartment/Apartment.php';
		$db = Apartment::getDefaultAdapter();
		$sel = $db->select();
	
		$sel->from(array('c' => "contacts"), array() );
		
		$spA = array();
		$spA["apart_name_uid"] = "name_uid";
		$spA["apart_name"] = "name";
		$spA["apart_visibil"] = "visibil";
			
		$spA["creat_date"] = "date_create";
		$spA["edit_date"] = "date_edit";
	
		$spA["create_guid"] = "user_create";
		$spA["edit_guid"] = "user_edit";
	

	
		$sel->joinLeft(array('a'=>"apartment"), "c.id = a.owner_id ",$spA );
		$ggSp = array();
		$ggSp["ggnr_ga"] = "gastgeber_nr";
		$ggSp["ggnr_zi"] = "zimmer_nr";
		$sel->joinLeft(array('gg'=>"ggnr"), "a.id = gg.apartment_id ",$ggSp );
	
		// suche nach Namen
		$sel->where("c.uid = ?", $ownerUid);
				
				
		
		if(!empty($where["search_name"])){
			$sel->where("a.name LIKE ?", $where["search_name"]."%");
				
				
		}
	
		$sel->limit($count,$offset);
	
		$result = $db->fetchAll($sel);
	
	
		return $result;
	}
	
	
	

	

	

	



	/**
	 * Giebt ein einzelnes Apartment zurück
	 * @param string $name nach dem Gesucht werden soll
	 * @param string $as kann genutzt werden um stadt nach den namenUid die "apartm_id" zu suchen. Standard ist nameId
	 * @return array|NULL
	 */
	public function ActionSingle($name,$as = NULL){

		$db = DBConnect::getConnect();
	
		$apartSel = $db->select ();
	
		$spA = array();
		$spA["apartm_uid"] = "name_uid";
		$spA["apartm_name"] = "name";
	
		$spA["create_date"] = "date_create";
 		$spA["edit_date"] = "date_edit";
 	
		$apartSel->from(array('a' => "apartment") ,$spA);
	
		$spOwner = array();
		$spOwner["owner_uid"] = "uid";
		$spOwner["owner_title"] = "title_name";
		$spOwner["owner_first"] = "first_name";
		$spOwner["owner_firstadd"] = "first_add_name";
		$spOwner["owner_last"] = "last_name";
		$spOwner["owner_affix"] = "affix_name";
		
		$apartSel->joinLeft(array('ow'=>"contacts"), "a.owner_id = ow.id ",$spOwner );
		
		$spResOrt = array();
		$spResOrt["resort_name"] = "name";
		$spResOrt["resort_uid"] = "uid";
		$spResOrt["resort_strasse"] = "strasse";
		
 		$apartSel->joinLeft(array('r'=>"resort"), "a.resort_id = r.id ",$spResOrt);
 		
 		
 		$spResOrt = array();
 		$spResOrt["city_name"] = "name";
 		$spResOrt["city_uid"] = "name_uid";
 		
 		$apartSel->joinLeft(array('ci'=>"resort_city"), "r.city_id = ci.id ",$spResOrt);
// 		$resortSel->joinLeft(array('o'=>ResortCity::getTableNameStatic()), "o.id = r.ort_id", array ('ort_name' => 'name','ort_gmap_lat' => 'gmap_lat','ort_gmap_lng' => 'gmap_lng','ort_gmap_zoom' => 'gmap_zoom')  );
		
// 		$resortSel->joinLeft(array('c_o'=>Contacts::getTableNameStatic()), "a.contact_id = c_o.id ",array ('useroner_name' => 'CONCAT(c2.first_name," ",c2.last_name )')  );
// 		$resortSel->joinLeft(array('c_b'=>Contacts::getTableNameStatic()), "a.bookingcontact_id = c_b.id ",array ('userbooking_name' => 'CONCAT(c2.first_name," ",c2.last_name )')  );

		
// 		$resortSel->joinLeft(array('u'=>contact_access::getTableNameStatic()), "a.user_create = u.guid ",array() );
// 		$resortSel->joinLeft(array('c'=>Contacts::getTableNameStatic()), "u.contacts_id = c.id", array ('usercreate_name' => 'CONCAT(c.first_name," ",c.last_name )' ) );
	
// 		$resortSel->joinLeft(array('u2'=>contact_access::getTableNameStatic()), "a.user_edit = u2.guid " ,array() );
// 		$resortSel->joinLeft(array('c2'=>Contacts::getTableNameStatic()), "u2.contacts_id = c2.id", array ('useredit_name' => 'CONCAT(c2.first_name," ",c2.last_name )')  );

		
 		if($as === "apartm_id" && ctype_digit($name)){
			$apartSel->where("a.id=?", $name,Zend_Db::INT_TYPE);
 		}else {
			$apartSel->where("a.name_uid=?", $name);
 			
 		}
// die();
	
		$resort = $db->fetchRow( $apartSel );
	
		return $resort;
	
	}
	
	
	
	/**
	 * Überschreibt die Daten
	 * @param string $name Eindeutiger Name des Zimmers
	 * @param array $data Daten name,strasse,resort_name...
	 */
	public function ActionUpdate($name,$data){

		require_once 'db/apartment/Apartment.php';
		$tab = new Apartment();
		$insData = array();
		
		if(isset($data["name"])){
			$insData["name"] = $data["name"] ;
		}
		
		
		if(isset($data["resort_name"])){
			require_once 'db/resort/Resort.php';
			$tabR = new Resort();
			$selectR = $tabR->select()->where($tabR->getAdapter()->quoteInto("name=?", $data["resort_name"]));
			$resortOrt = $tabR->fetchRow($selectR);
			if($resortOrt !== NULL){
				$insData["resort_id"] = $resortOrt->offsetGet("id") ;
			}
		}
	
		
		$tab->update($insData, $tab->getAdapter()->quoteInto("name=?", $name));
	
	
	}
	
	
	/**
	 * Überschreiben des Resorts
	 * @param string $name Der Apartmentname
	 * @param string $resortName Der Resortname
	 */
	public function ActionUpdateResort($name,$resortName){
	
		return  $this->ActionUpdate($name, array("resort_name"=>$resortName));
		
	}
	
	/**
	 * Giebt eine Liste von Apartments zurück
	 * @param string $searchname
	 * @param integer $max
	 * @param string $areas
	 * @return array
	 */
	public function ActionGetSearch($searchname, $max = 5, $areas = NULL){
	
	
		require_once 'db/apartment/Apartment.php';
		$db = Apartment::getDefaultAdapter();
	
		$searchListSel = $db->select ();
		$searchListSel->from( array('a' => Apartment::getTableNameStatic() ), array( "a.name") );
	
	
		$searchListSel->where("name LIKE ? " , $searchname."%");
		$searchListSel->limit($max);
	
		$allOrts = $db->fetchAll($searchListSel);
	
	
		return $allOrts;
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}

?>