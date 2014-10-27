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
	 * Giebt eine Liste von Apartments zurück
	 * @param integer $count
	 * @param integer $offset
	 * @param array $where kann ein array mit abfragen übergeben werden RESORT_ID|ORT_ID
	 * @param array $spalten
	 * @return array
	 */
	public function ActionGetList($count, $offset, $where = array(), $spalten = array()){


		require_once 'db/apartment/apartment.php';
		require_once 'db/ggnr/ggnr.php';
		
		$db = apartment::getDefaultAdapter();
		
		$sel = $db->select();
		
		$spA = array();
		$spA["name"] = "name";
		$spA["visibil"] = "visibil";
			
		$spA["creat_date"] = "date_create";
		$spA["edit_date"] = "date_edit";
		
		$spA["create_guid"] = "user_create";
		$spA["edit_guid"] = "user_edit";
		

		
		$sel->from(array('a' => apartment::getTableNameStatic()), $spA );
		
		if( in_array('usercreate_name',$spalten) ){
			require_once 'db/sys/access/sys_access.php';
			require_once 'db/contact/Contacts.php';
			$sel->joinLeft(array('u'=>sys_access::getTableNameStatic()), "a.user_create = u.guid ",array() );
			$sel->joinLeft(array('c'=>Contacts::getTableNameStatic()), "u.contacts_id = c.id", array ('usercreate_name' => 'CONCAT(c.first_name," ",c.last_name )' ) );
		}
		
		if( in_array('useredit_name',$spalten) ){
			require_once 'db/sys/access/sys_access.php';
			require_once 'db/contact/Contacts.php';
			$sel->joinLeft(array('u2'=>sys_access::getTableNameStatic()), "a.user_edit = u2.guid " ,array() );
			$sel->joinLeft(array('c2'=>Contacts::getTableNameStatic()), "u2.contacts_id = c2.id", array ('useredit_name' => 'CONCAT(c2.first_name," ",c2.last_name )')  );
		}
		
		if( in_array('resort',$spalten)  || array_key_exists("ort", $where) ){
			
			require_once 'db/resort/resort.php';
			$resortSpalten = array();
			$resortSpalten["resort_name"] = "name";
			$resortSpalten["resort_strasse"] = "strasse";
			$sel->joinLeft(array('r'=>resort::getTableNameStatic()), "r.id = a.resort_id ",$resortSpalten );
			
			require_once 'db/resort/resort_orte.php';
			$ortSpalten = array();
			$ortSpalten["ort_name"] = "name";
			$sel->joinLeft(array('o'=>resort_orte::getTableNameStatic()), "o.id = r.ort_id", $ortSpalten );
		
	
		}
	
		
		$sel->joinLeft(array('g'=>ggnr::getTableNameStatic()), "g.zimmer_id = a.id ",array('ggnr' => 'gastgeber_nr','zimnr' => 'zimmer_nr') );
		

		
		
		if(array_key_exists("ggnr_gg", $where))
			$sel->where("g.gastgeber_nr = ?", $where["ggnr_gg"]);
		
		if(array_key_exists("ggnr_nr", $where))
			$sel->where("g.zimmer_nr = ?", $where["ggnr_nr"]);
		
		if(array_key_exists("name", $where))
			$sel->where("a.name LIKE ?", $where["name"]."%");
		
		if(array_key_exists("ort", $where))
			$sel->where("o.name LIKE ?", $where["ort"]."%");
	

		$sel->limit($count,$offset);
		$result = $db->fetchAll($sel);
	

		return $result;
	}
	
	
	/**
	 * Giebt die anzahl der Apartments zurück
	 * @param array $where
	 * @return array
	 */
	public function ActionCount( $where = array()){
		require_once 'db/apartment/apartment.php';
		$db = apartment::getDefaultAdapter();
		
		$sel = $db->select();
		$sel->from(array('a' => apartment::getTableNameStatic()),"count(a.id)" );
		
		if(array_key_exists("ggnr_gg", $where) || array_key_exists("ggnr_nr", $where)){
			require_once 'db/ggnr/ggnr.php';
			$sel->joinLeft(array('g'=>ggnr::getTableNameStatic()), "g.zimmer_id = a.id ",array() );
		}
		if(array_key_exists("ort", $where) ){
			require_once 'db/resort/resort.php';
			require_once 'db/resort/resort_orte.php';
			//require_once 'db/contact/contacts.php';'resort_ortid' => 'ort_id',
			$sel->joinLeft(array('r'=>resort::getTableNameStatic()), "r.id = a.resort_id ",array() );
			$sel->joinLeft(array('o'=>resort_orte::getTableNameStatic()), "o.id = r.ort_id", array ()  );
		}
	
		
		if(array_key_exists("ggnr_gg", $where))
			$sel->where("g.gastgeber_nr = ?", $where["ggnr_gg"]);
		
		if(array_key_exists("ggnr_nr", $where))
			$sel->where("g.zimmer_nr = ?", $where["ggnr_nr"]);
		
		if(array_key_exists("name", $where))
			$sel->where("a.name LIKE ?", $where["name"]."%");
		
		if(array_key_exists("ort", $where))
			$sel->where("o.name LIKE ?", $where["ort"]."%");
	
		$allOrtCounts = $db->fetchOne($sel);
				
		return $allOrtCounts;
	}
	

	

	



	/**
	 * Giebt ein einzelnes Apartment zurück
	 * @param string $name nach dem Gesucht werden soll
	 * @return array|NULL
	 */
	public function ActionGetOne($name){
	
	
	
		require_once 'db/ggnr/ggnr.php';
		require_once 'db/resort/resort.php';
		require_once 'db/apartment/apartment.php';
		require_once 'db/resort/resort_orte.php';
		require_once 'db/contact/contact_access.php';
		require_once 'db/contact/Contacts.php';

	
		$db = apartment::getDefaultAdapter();
	
	
		$resortSel = $db->select ();
	
		$spA = array();
		$spA["art"] = "art";
		$spA["name"] = "name";
		$spA["visibil"] = "visibil";
	
	
		$spA["creat_date"] = "date_create";
 		$spA["edit_date"] = "date_edit";
	
		$spA["create_guid"] = "user_create";
		$spA["edit_guid"] = "user_edit";
	
	

		$resortSel->from(array('a' => apartment::getTableNameStatic()) ,$spA);
	
		$resortSel->joinLeft(array('g'=>ggnr::getTableNameStatic()), "g.zimmer_id = a.id ",array('ggnr' => 'gastgeber_nr','zimnr' => 'zimmer_nr') );
		
		$resortSel->joinLeft(array('r'=>resort::getTableNameStatic()), "r.id = a.resort_id ",array('resort_name' => 'name','resort_strasse' => 'strasse') );
		$resortSel->joinLeft(array('o'=>resort_orte::getTableNameStatic()), "o.id = r.ort_id", array ('ort_name' => 'name','ort_gmap_lat' => 'gmap_lat','ort_gmap_lng' => 'gmap_lng','ort_gmap_zoom' => 'gmap_zoom')  );
		
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

		require_once 'db/apartment/apartment.php';
		$tab = new apartment();
		$insData = array();
		
		if(isset($data["name"])){
			$insData["name"] = $data["name"] ;
		}
		
		
		if(isset($data["resort_name"])){
			require_once 'db/resort/resort.php';
			$tabR = new resort();
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
	
	
		require_once 'db/apartment/apartment.php';
		$db = apartment::getDefaultAdapter();
	
		$searchListSel = $db->select ();
		$searchListSel->from( array('a' => apartment::getTableNameStatic() ), array( "a.name") );
	
	
		$searchListSel->where("name LIKE ? " , $searchname."%");
		$searchListSel->limit($max);
	
		$allOrts = $db->fetchAll($searchListSel);
	
	
		return $allOrts;
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}

?>