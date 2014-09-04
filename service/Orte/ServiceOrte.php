<?php

require_once 'citro/service-class/AService.php';

/**
 * Dieser Serviece Verwaltet die orte der Resourts
 *
 * @author Max Plank
 * @version 1.0
 *         
 */
class ServiceOrte extends AService {
	

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
		require_once 'db/resort/resort_orte.php';
		$db = resort_orte::getDefaultAdapter();
	
		$sql = "SELECT count(id) FROM `".resort_orte::getTableNameStatic()."`; ";
		$allOrtCounts = $db->fetchOne($sql);
	
		return $allOrtCounts;
	}

	/**
	 * Giebt eine Liste von Orten zurück
	 * @param integer $count
	 * @param integer $offset
	 * @param string $areas
	 * @return array
	 */
	public function ActionGetOrtList($count, $offset){
			
		require_once 'db/resort/resort_orte.php';

		require_once 'db/apartment/apartment.php';

		$db = resort_orte::getDefaultAdapter();
		$ortListSel = $db->select ();
		$ortListSel->from(array('o' => resort_orte::getTableNameStatic()) ,array('ort_name' , 'counts' => 'count(a.id)'));
		$ortListSel->joinLeft(array('a'=>apartment::getTableNameStatic()), "o.ort_name = a.orts_name_key");
		

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
	 * @param string $name Der Ortsname nach dem gesucht werden soll
	 * @return multitype:
	 */
	public function ActionGetRegionen($name){
	
		require_once 'db/resort/resort_orte_region.php';
		require_once 'db/resort/resort_orte_match.php';
		require_once 'db/resort/resort_orte.php';
			
		$regionenTab = resort_orte::getDefaultAdapter();
		$regionSel = $regionenTab->select();
		$regionSel->from(array( "r" => resort_orte_region::getTableNameStatic() ),array("region") );
		$regionSel->joinLeft( array("m" => resort_orte_match::getTableNameStatic() ), "r.id = m.arrea_id",array());
		$regionSel->joinLeft(array("o" => resort_orte::getTableNameStatic() ), "m.ort_id = o.id" ,array());
			
		$regionSel->where("o.name=?",$name);
			
		$regionAll = $regionenTab->fetchCol($regionSel);
		if($regionAll === null)
			return NULL;

		return $regionAll;

	
	}
	
	
	/**
	 * Setzt die Position des ortes
	 * @param string $ortName
	 * @param string $lat
	 * @param string $lng
	 * @param string $zoom
	 */
	public function ActionSetPosition($ortName,$lat,$lng,$zoom){
		require_once 'db/resort/resort_orte.php';

		$ortTab = new resort_orte();
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
	
	
		require_once 'db/resort/resort_orte.php';
		$db = resort_orte::getDefaultAdapter();
	
		$searchListSel = $db->select ();
		$searchListSel->from( array('o' => resort_orte::getTableNameStatic() ), array( "o.name") );

		
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
	
	
		require_once 'db/resort/resort_orte.php';
		require_once 'db/sys/access/sys_access.php';
		require_once 'db/contact/contacts.php';
		

		$db = resort_orte::getDefaultAdapter();
	

		$ortListSel = $db->select ();
		$ortListSel->from(array('o' => resort_orte::getTableNameStatic()) );
		
		
		$ortListSel->joinLeft(array('u'=>sys_access::getTableNameStatic()), "o.usercreat = u.guid", array ('usercreate_guid' => 'u.guid'));
		$ortListSel->joinLeft(array('c'=>contacts::getTableNameStatic()), "u.contacts_id = c.id", array ('usercreate_name' => 'CONCAT(c.first_name," ",c.last_name )', 'usercreate_id' => 'c.id' ));
		
		$ortListSel->limit($count,$offset);
		$allOrts = $db->fetchAll( $ortListSel );
		return $allOrts;
	}
	

	
	/**
	 * Fügt ein Element hinzu
	 * @param string $name
	 * @param array $data
	 */
	public function ActionNewOrt($name, $data = array()){


		
		
		try {
			
			require_once 'db/resort/resort_orte.php';
			$orteTab = new resort_orte();
			
			$data = array();
			$data['name'] = $name;
			
			if(isset($data["desc"])){
				$data['text'] = $data["desc"];
			}
			
			$data['edata'] = DBTable::DateTime();
			$data['vdata'] = DBTable::DateTime();
			$data['usercreat'] = $this->_rightsAcl->getAccess()->getGuId();
			$data['useredit'] = $this->_rightsAcl->getAccess()->getGuId();
			$data['in_menue'] = 1;
			
			$newOrtId = $orteTab->insert($data);
			
		
			return TRUE;
			
			
		} catch (Exception $e) {
			return FALSE;
		}
				
	}
	
	
	/**
	 * Löscht einen Ort und seine Beziehungen
	 * @param string $ortname
	 */
	public function ActionDelete($ortname){
		
		
		require_once 'citro/DBTable.php'; 
		$db = DBTable::getDefaultAdapter();

		require_once 'db/resort/resort_orte.php';
		
		$ortSel = $db->select();
		$ortSel->from( resort_orte::getTableNameStatic() , '*');
		$ortSel->where("ort_name=?", $ortname);
		$ortRow = $db->fetchRow($ortSel);
		
		if( $ortRow === NULL )
			return FALSE;
	
		require_once 'db/resort/resort_orte_match.php';
		$db->delete(resort_orte_match::getTableNameStatic(),  $db->quoteInto("ort_id=?", $ortRow['id'] ) );
		$db->delete(resort_orte::getTableNameStatic(),  $db->quoteInto("id=?", $ortRow['id'] ) );
		return TRUE;
	}
	
	
	
	/**
	 * Umschreiben des Ortsnamens
	 * @param string $oldOrtName
	 * @param string $newOrtName
	 */
	public function ActionEdit($oldOrtName,$newOrtName){
		
		require_once 'db/resort/resort_orte.php';
		$orteTab = new resort_orte();
		$data = array();
		$data['ort_name'] = $newOrtName;
		
		
		$orteTab->update($data, $orteTab->getDefaultAdapter()->quoteInto("ort_name=?", $oldOrtName));
		
		require_once 'db/apartment/apartment.php';
		$appTab = new apartment();
		$dataApp = array();
		$dataApp['orts_name_key'] = $newOrtName;
		$appTab->update($dataApp, $appTab->getDefaultAdapter()->quoteInto("orts_name_key=?", $oldOrtName));
		
	}
	

	


	
	/**
	 * Giebt den angefragten ort zurück
	 * @param string $ortName
	 */
	public function ActionGetOrt($ortName){
	
	
		require_once 'db/resort/resort_orte.php';
		require_once 'db/contact/contact_access.php';
		require_once 'db/contact/contacts.php';
		
		
		$db = resort_orte::getDefaultAdapter();
		
		
		$ortListSel = $db->select ();

		$spA = array();
		$spA["o.name"];
		$spA["in_menue"];

		$spA["creat_date"] = "edata";
		$spA["edit_date"] = "vdata";

		$spA["create_guid"] = "usercreat";
		$spA["c.usercreate_name"];
		$spA["edit_guid"] = "useredit";
		$spA["useredit_name"];
		
		$spA["text"];
		$spA["gmaps_id"];
		$spA["gmap_karte_x"];
		$spA["gmap_karte_y"];
		$spA["gmap_zoom"];


		
		#$ortListSel->from(array('o' => resort_orte::getTableNameStatic()) ,$spA);
		$ortListSel->from(array('o' => resort_orte::getTableNameStatic()) );
				
		$ortListSel->joinLeft(array('u'=>contact_access::getTableNameStatic()), "o.usercreat = u.guid ",array() );
		$ortListSel->joinLeft(array('c'=>contacts::getTableNameStatic()), "u.contacts_id = c.id", array ('usercreate_name' => 'CONCAT(c.first_name," ",c.last_name )' ) );

		$ortListSel->joinLeft(array('u2'=>contact_access::getTableNameStatic()), "o.useredit = u2.guid " ,array() );
		$ortListSel->joinLeft(array('c2'=>contacts::getTableNameStatic()), "u2.contacts_id = c2.id", array ('useredit_name' => 'CONCAT(c2.first_name," ",c2.last_name )')  );
		
		
		$ortListSel->where("o.name=?", $ortName);
		$allOrts = $db->fetchRow( $ortListSel );

		return $allOrts;
		
	}
	

	/**
	 * Prüft ob es den Ort giebt falls ja giebt er diesen wieder
	 * @param string $name
	 * @return boolean|array
	 */
	public function ActionExist($name){
		require_once 'db/resort/resort_orte.php';
		$tab = new resort_orte();
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