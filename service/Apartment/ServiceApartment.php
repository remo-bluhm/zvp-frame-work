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
	
		FireBug::setDebug($fields,"ServiceApartment::ActionNew->fields");

		
		
		require_once 'db/apartment/Apartment.php';
		$apartmName = Apartment::testApartmName($apartmName);
		
		require_once 'db/contact/Contacts.php';
		$ownerUid = Contacts::testUID($ownerUid);
		if($ownerUid === NULL  )return FALSE;
		// falls keiner gefunden wurde dann false ansonsten die id
		$ownerId = DBConnect::getConnect()->fetchOne("select id from contacts where uid='$ownerUid' and type='HIRER';");
			
		
		if( $apartmName !== NULL && $ownerId !== FALSE  ){
			$apartmTab = new Apartment();
			$apartmId = $apartmTab->insertDataFull($this->_rightsAcl->getAccess()->getId(), $apartmName, $ownerId, $fields);
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
		$spA["name"] = "name";
		$spA["visibil"] = "visibil";
			
		$spA["creat_date"] = "date_create";
		$spA["edit_date"] = "date_edit";
		
		$spA["create_guid"] = "user_create";
		$spA["edit_guid"] = "user_edit";
		

		
		$sel->from(array('a' => "apartment"), $spA );
	
		$sel->joinLeft(array('g'=>"ggnr"), "g.zimmer_id = a.id ",array('ggnr' => 'gastgeber_nr','zimnr' => 'zimmer_nr') );
		
		$sel->limit($count,$offset);
		$result = $db->fetchAll($sel);
	

		return $result;
	}
	
	
// 	/**
// 	 * Giebt die anzahl der Apartments zurück
// 	 * @param array $where
// 	 * @return array
// 	 */
// 	public function ActionCount( $where = array()){
// 		require_once 'db/apartment/apartment.php';
// 		$db = apartment::getDefaultAdapter();
		
// 		$sel = $db->select();
// 		$sel->from(array('a' => apartment::getTableNameStatic()),"count(a.id)" );
		
// 		if(array_key_exists("ggnr_gg", $where) || array_key_exists("ggnr_nr", $where)){
// 			require_once 'db/ggnr/ggnr.php';
// 			$sel->joinLeft(array('g'=>ggnr::getTableNameStatic()), "g.zimmer_id = a.id ",array() );
// 		}
// 		if(array_key_exists("ort", $where) ){
// 			require_once 'db/resort/resort.php';
// 			require_once 'db/resort/resort_orte.php';
// 			//require_once 'db/contact/contacts.php';'resort_ortid' => 'ort_id',
// 			$sel->joinLeft(array('r'=>resort::getTableNameStatic()), "r.id = a.resort_id ",array() );
// 			$sel->joinLeft(array('o'=>resort_orte::getTableNameStatic()), "o.id = r.ort_id", array ()  );
// 		}
	
		
// 		if(array_key_exists("ggnr_gg", $where))
// 			$sel->where("g.gastgeber_nr = ?", $where["ggnr_gg"]);
		
// 		if(array_key_exists("ggnr_nr", $where))
// 			$sel->where("g.zimmer_nr = ?", $where["ggnr_nr"]);
		
// 		if(array_key_exists("name", $where))
// 			$sel->where("a.name LIKE ?", $where["name"]."%");
		
// 		if(array_key_exists("ort", $where))
// 			$sel->where("o.name LIKE ?", $where["ort"]."%");
	
// 		$allOrtCounts = $db->fetchOne($sel);
				
// 		return $allOrtCounts;
// 	}
	

	

	



	/**
	 * Giebt ein einzelnes Apartment zurück
	 * @param string $name nach dem Gesucht werden soll
	 * @return array|NULL
	 */
	public function ActionSingle($name){
	
	
	
		require_once 'db/ggnr/ggnr.php';
		require_once 'db/resort/Resort.php';
		require_once 'db/apartment/Apartment.php';
		require_once 'db/resort/ResortOrte.php';
		require_once 'db/contact/contact_access.php';
		require_once 'db/contact/Contacts.php';

	
		$db = Apartment::getDefaultAdapter();
	
	
		$resortSel = $db->select ();
	
		$spA = array();
		$spA["art"] = "art";
		$spA["name"] = "name";
		$spA["visibil"] = "visibil";
	
	
		$spA["creat_date"] = "date_create";
 		$spA["edit_date"] = "date_edit";
	
		$spA["create_guid"] = "user_create";
		$spA["edit_guid"] = "user_edit";
	
	

		$resortSel->from(array('a' => Apartment::getTableNameStatic()) ,$spA);
	
		$resortSel->joinLeft(array('g'=>ggnr::getTableNameStatic()), "g.zimmer_id = a.id ",array('ggnr' => 'gastgeber_nr','zimnr' => 'zimmer_nr') );
		
		$resortSel->joinLeft(array('r'=>Resort::getTableNameStatic()), "r.id = a.resort_id ",array('resort_name' => 'name','resort_strasse' => 'strasse') );
		$resortSel->joinLeft(array('o'=>ResortOrte::getTableNameStatic()), "o.id = r.ort_id", array ('ort_name' => 'name','ort_gmap_lat' => 'gmap_lat','ort_gmap_lng' => 'gmap_lng','ort_gmap_zoom' => 'gmap_zoom')  );
		
		$resortSel->joinLeft(array('c_o'=>Contacts::getTableNameStatic()), "a.contact_id = c_o.id ",array ('useroner_name' => 'CONCAT(c2.first_name," ",c2.last_name )')  );
		$resortSel->joinLeft(array('c_b'=>Contacts::getTableNameStatic()), "a.bookingcontact_id = c_b.id ",array ('userbooking_name' => 'CONCAT(c2.first_name," ",c2.last_name )')  );

		
		$resortSel->joinLeft(array('u'=>contact_access::getTableNameStatic()), "a.user_create = u.guid ",array() );
		$resortSel->joinLeft(array('c'=>Contacts::getTableNameStatic()), "u.contacts_id = c.id", array ('usercreate_name' => 'CONCAT(c.first_name," ",c.last_name )' ) );
	
		$resortSel->joinLeft(array('u2'=>contact_access::getTableNameStatic()), "a.user_edit = u2.guid " ,array() );
		$resortSel->joinLeft(array('c2'=>Contacts::getTableNameStatic()), "u2.contacts_id = c2.id", array ('useredit_name' => 'CONCAT(c2.first_name," ",c2.last_name )')  );
	
 		
	
	
		$resortSel->where("a.name=?", $name);
		$resortSel->where("'a.deleted'=?", 0);
	
		$resort = $db->fetchRow( $resortSel );
	
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