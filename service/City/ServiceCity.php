<?php

require_once 'citro/service-class/AService.php';

/**
 * Dieser Serviece Verwaltet die orte der Resourts
 *
 * @author Max Plank
 * @version 1.0
 *         
 */
class ServiceCity extends AService {
	

	private $_rootName = "Orte";
	private $_rootDescription = "Enthällt alle orte ";
	
	
	/**
	 * Der User construktor
	 */
	function __construct() {
		
		parent::__construct ();
	
	}

	
	/**
	 * Giebt die anzahl der Orte an
	 * @param string $areas
	 * @return array
	 */
	public function ActionCountOrte( $areas = NULL){
		require_once 'db/resort/ResortCity.php';
		$db = ResortCity::getDefaultAdapter();
	
		$sql = "SELECT count(id) FROM `".ResortCity::getTableNameStatic()."`; ";
		$allOrtCounts = $db->fetchOne($sql);
	
		return $allOrtCounts;
	}
	
	/**
	 * Sucht ein Ort
	 * @param string $searchStr
	 * @param integer $count
	 * @return array
	 */
	public function ActionSearch( $searchStr , $count = 10) {
	
		$db = DBConnect::getConnect();
	
		$spA = array();
		$spA["city_uid"] = "uid";
		$spA["city_name"] = "name";
		$spA["city_zip"] = "zip";
		$spA["city_land"] = "land";
		$spA["city_land_part"] = "land_part";
		$spA["city_country"] = "land_country";
	
		$resortSel = $db->select();
		$resortSel->from( array('c' => "resort_city") ,$spA);
	
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
			
		$searchstring = "(c.name LIKE '".$cleanSearA[0]."%' OR c.zip LIKE '".$cleanSearA[0]."%' ) ";
			
		
		$resortSel->where($searchstring);
		$resortSel->limit($count);
		//echo $resortSel->__toString();
	
		$resort = $db->fetchAll( $resortSel );
		if(!is_array($resort))$resort = array();
		return $resort;
	
	
	}

	/**
	 * Giebt eine Liste von Orten zurück
	 * @param integer $count
	 * @param integer $offset
	 * @param string $areas
	 * @return array
	 */
	public function ActionGetOrtList($count, $offset){
			
		require_once 'db/resort/ResortCity.php';

		require_once 'db/apartment/Apartment.php';

		$db = ResortCity::getDefaultAdapter();
		$ortListSel = $db->select ();
		$ortListSel->from(array('o' => ResortCity::getTableNameStatic()) ,array('ort_name' , 'counts' => 'count(a.id)'));
		$ortListSel->joinLeft(array('a'=>Apartment::getTableNameStatic()), "o.ort_name = a.orts_name_key");
		

		$ortListSel->group("o.ort_name");
		
		if(0 < $count && $count < 100){
			$ortListSel->limit($count);
		}else{
			$ortListSel->limit(10);
		}
		
		$ortListSel->order("counts desc");
		
		$allOrts = $db->fetchAll($ortListSel);
		return $allOrts;
	}
	

	/**
	 * Giebt die Regionen zurück die dem Ort zugehörig sind
	 * @param string $searchName Der Ortsname nach dem gesucht werden soll
	 * @return multitype:
	 */
	public function ActionGetRegionen($searchName = NULL){

		require_once 'db/resort/ResortRegion.php';
			
		$regTab = new ResortRegion();
		$regionSel = $regTab->select();
		
		$spR = array();
		$spR["name_uid"] = "name_uid";
		$spR["name"] = "name";
		
		
		$regionSel->from(array( "r" => "resort_region" ),$spR );
			//$string = $regionSel->__toString();
	
		//$regionSel->limit(10);
		//$regionSel->where("o.name=?",$name);
			
		$regionAll = $regTab->fetchAll($regionSel);
		if($regionAll === NULL)
			return NULL;

		return $regionAll->toArray();

	
	}
	
	
	/**
	 * Setzt die Position des ortes
	 * @param string $ortName
	 * @param string $lat
	 * @param string $lng
	 * @param string $zoom
	 */
	public function ActionSetPosition($ortName,$lat,$lng,$zoom){
		require_once 'db/resort/ResortCity.php';

		$ortTab = new ResortCity();
		$ortSel = $ortTab->select();
		$ortSel->where("name=?", trim($ortName));

		$ortRow = $ortTab->fetchRow($ortSel);
		
		if($ortRow !== NULL){
			
			$ortRow->offsetSet("gmap_lat", $lat);
			$ortRow->offsetSet("gmap_lng", $lng);
			$ortRow->offsetSet("gmap_zoom", $zoom);
			
			$ortRow->save();
			return TRUE;
			
		}
		
		return FALSE;		
	}
	
	
	
	/**
	 * Giebt eine Liste von Orten zurück
	 * @param string $searchname
	 * @param integer $max
	 * @param string $areas
	 * @return array
	 */
	public function ActionGetOrtSearch($searchname, $max = 5, $areas = NULL){
	
	
		require_once 'db/resort/ResortCity.php';
		$db = ResortCity::getDefaultAdapter();
	
		$searchListSel = $db->select ();
		$searchListSel->from( array('o' => ResortCity::getTableNameStatic() ), array( "o.name") );

		
		$searchListSel->where("name LIKE ? " , $searchname."%");
		$searchListSel->limit($max);
	
		$allOrts = $db->fetchAll($searchListSel);	
		
	
		return $allOrts;
	}
	
	/**
	 * Giebt eine Liste von Orten zurück
	 * @param integer $count
	 * @param integer $offset
	 * @param string $areas
	 * @return array
	 */
	public function ActionGetOrtListFull($count, $offset, $areas = NULL){
	
	
		require_once 'db/resort/ResortCity.php';
		require_once 'db/sys/access/sys_access.php';
		require_once 'db/contact/Contacts.php';
		

		$db = ResortCity::getDefaultAdapter();
	

		$ortListSel = $db->select ();
		$ortListSel->from(array('o' => ResortCity::getTableNameStatic()) );
		
		
 		$ortListSel->joinLeft(array('u'=>"sys_access"), "o.access_create = u.id", array ('usercreate_guid' => 'u.id'));
 		$ortListSel->joinLeft(array('c'=>'contacts'), "u.contacts_id = c.id", array ('usercreate_name' => 'CONCAT(c.first_name," ",c.last_name )', 'usercreate_id' => 'c.id' ));
		
		$ortListSel->limit($count,$offset);
		$allOrts = $db->fetchAll( $ortListSel );
		return $allOrts;
	}
	
	
	/**
	 * Giebt eine Liste von Orten zurück
	 * @param integer $count
	 * @param integer $offset
	 * @param array $where name|ort
	 * @param array $spalten
	 * @return array
	 */
	public function ActionList($count, $offset = 0, $where = array(), $spalten = array()){
	
	
		require_once 'db/resort/ResortCity.php';
		require_once 'db/sys/access/sys_access.php';
		require_once 'db/contact/Contacts.php';
	
	
		$db = ResortCity::getDefaultAdapter();
	
		$spA = array();
		$spA["id"] = "id";
		//$spA["anzahl"] = "count(uid)";
		$spA["name_uid"] = "name_uid";
		$spA["name"] = "name";
		$spA["create"] = "edata";
		$spA["edit"] = "vdata";
	
// 		$spA["zip"] = "zip";
// 		$spA["land"] = "land";
// 		$spA["landpart"] = "landpart";
		
		$ortListSel = $db->select ();
		$ortListSel->from(array('o' => "resort_city"), $spA );
	

		$ortListSel->limit($count,$offset);
		$allOrts = $db->fetchAll( $ortListSel );
		return $allOrts;
	}
	

	
	/**
	 * Fügt ein Element hinzu
	 * @param string $name
	 * @param string $zip
	 * @param string $landkey
	 * @param array $fields
	 * @return integer|bool
	 */
	public function ActionNew($name,$zip,$landkey, $fields = array()){

		// Setzen der $fieldsvariabel auf array
		if(!is_array($fields))$fields = array();

		require_once 'db/resort/ResortCity.php';

		
		$name = ResortCity::testName($name);
		
		
	
		$zip = ResortCity::testZip($zip);
		
		$landKey = 1; // "germany" key = 1
		

		if($name !== NULL && $zip !== NULL){
			$cityTab = new ResortCity();
			$resultUid = $cityTab->exist($name, $zip, $landKey);
	
			if($resultUid !== FALSE){
				// Achtung eigentlich erst mal die LandId hollen
				// einschreiben
				//$cityTab->insertDataFull($this->_rightsAcl->getAccess()->getId(), $name, $zip, $land, $data);
				
			}else{
				return $resultUid;
			}
			
			
		}
		
// 		try {
			
			
			
			
			
// 			$data = array();
// 			$data['name'] = $name;
			
// 			if(isset($fields["ort_name_uid"])){
// 				$data['name_uid'] = $fields["ort_name_uid"];
// 			}
			

// 			$newOrtId = $cityTab->insertDataFull($this->_rightsAcl->getAccess()->getId(), $name, $zip, $data);

			
			
	
// 			//$newOrtId = $orteTab->insert($data);
			
// 			return $newOrtId;
		
			
			
// 		} catch (Exception $e) {
// 			return FALSE;
// 		}
				
	}
	
	
	/**
	 * Löscht einen Ort und seine Beziehungen
	 * @param string $ortname
	 */
	public function ActionDelete($ortname){
		
		
		require_once 'citro/DBTable.php'; 
		$db = DBTable::getDefaultAdapter();

		require_once 'db/resort/ResortCity.php';
		
		$ortSel = $db->select();
		$ortSel->from( ResortCity::getTableNameStatic() , '*');
		$ortSel->where("ort_name=?", $ortname);
		$ortRow = $db->fetchRow($ortSel);
		
		if( $ortRow === NULL )
			return FALSE;
	
		require_once 'db/resort/resort_orte_match.php';
		$db->delete(resort_orte_match::getTableNameStatic(),  $db->quoteInto("ort_id=?", $ortRow['id'] ) );
		$db->delete(ResortCity::getTableNameStatic(),  $db->quoteInto("id=?", $ortRow['id'] ) );
		return TRUE;
	}
	
	
	
	/**
	 * Umschreiben des Ortsnamens
	 * @param string $oldOrtName
	 * @param string $newOrtName
	 */
	public function ActionEdit($oldOrtName,$newOrtName){
		
		require_once 'db/resort/ResortCity.php';
		$orteTab = new ResortCity();
		$data = array();
		$data['ort_name'] = $newOrtName;
		
		
		$orteTab->update($data, $orteTab->getDefaultAdapter()->quoteInto("ort_name=?", $oldOrtName));
		
		require_once 'db/apartment/Apartment.php';
		$appTab = new Apartment();
		$dataApp = array();
		$dataApp['orts_name_key'] = $newOrtName;
		$appTab->update($dataApp, $appTab->getDefaultAdapter()->quoteInto("orts_name_key=?", $oldOrtName));
		
	}
	

	

	/**
	 * Giebt den Ort Datensatz zurück
	 * @param string $ortName
	 * @return array
	 */
	public function ActionSingle($ortName){
		$db = DBConnect::getConnect();
		$ortListSel = $db->select ();
		
		$ortListSel->from(array("resort_city") );
	
		$ortListSel->where("name=?", $ortName);
		$allOrts = $db->fetchRow( $ortListSel );
		
		return $allOrts;
	}

	
	/**
	 * Giebt den angefragten ort zurück
	 * @param string $ortName
	 */
	public function ActionGetOrt($ortName){
	
	
	//	require_once 'db/resort/resort_orte.php';
	//	require_once 'db/contact/contact_access.php';
		require_once 'db/contact/Contacts.php';
		
		
		$db = DBConnect::getConnect();
		
		
		$ortListSel = $db->select ();

// 		$spA = array();
// 		$spA["o.name"];
// 		$spA["in_menue"];

// 		$spA["creat_date"] = "edata";
// 		$spA["edit_date"] = "vdata";

// 		$spA["create_guid"] = "usercreat";
// 		$spA["c.usercreate_name"];
// 		$spA["edit_guid"] = "useredit";
// 		$spA["useredit_name"];
		
// 		$spA["text"];
// 		$spA["gmaps_id"];
// 		$spA["gmap_karte_x"];
// 		$spA["gmap_karte_y"];
// 		$spA["gmap_zoom"];


		
		#$ortListSel->from(array('o' => resort_orte::getTableNameStatic()) ,$spA);
		$ortListSel->from(array("resort_orte") );
				
//		$ortListSel->joinLeft(array('u'=>contact_access::getTableNameStatic()), "o.usercreat = u.guid ",array() );
	//	$ortListSel->joinLeft(array('c'=>Contacts::getTableNameStatic()), "u.contacts_id = c.id", array ('usercreate_name' => 'CONCAT(c.first_name," ",c.last_name )' ) );

//		$ortListSel->joinLeft(array('u2'=>contact_access::getTableNameStatic()), "o.useredit = u2.guid " ,array() );
	//	$ortListSel->joinLeft(array('c2'=>Contacts::getTableNameStatic()), "u2.contacts_id = c2.id", array ('useredit_name' => 'CONCAT(c2.first_name," ",c2.last_name )')  );
		
		
		$ortListSel->where("name=?", $ortName);
		$allOrts = $db->fetchRow( $ortListSel );

		return $allOrts;
		
	}
	
	/**
	 * Prüft ob es den Ort giebt falls ja giebt er diesen wieder
	 * @param string $uid
	 * @return boolean|array
	 */
	public function ActionExistUid($uid){
		if(empty($uid))return 1; // falls eine lehre anfrage kommt diese als standart mit existiert zurückgeben
		require_once 'db/resort/ResortCity.php';
		$tab = new ResortCity();
		$id = $tab->existUid($uid);
		if(is_int($id)&&$id > 0){
			//Gefunden
			return 1;
		}else {
			//nicht gefunden
			return 0;
		}
	
	}
	/**
	 * Prüft ob es den Ort giebt falls ja giebt er diesen wieder
	 * @param string $name
	 * @return boolean|array
	 */
	public function ActionExist($name){
		require_once 'db/resort/ResortCity.php';
		$tab = new ResortCity();
		$sel = $tab->select();
		$sel->where("name=?",$name);
		$row = $tab->fetchRow($sel);
		
		if($row === NULL){
			return FALSE;
		}
		return $row->toArray();
		
	}
	
}












?>