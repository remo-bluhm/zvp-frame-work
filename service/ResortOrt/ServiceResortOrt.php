<?php

require_once 'citro/service-class/AService.php';

/**
 * Dieser Serviece Verwaltet die orte der Resourts
 *
 * @author Max Plank
 * @version 1.0
 *         
 */
class ServiceResortOrt extends AService {
	

	private $_rootName = "Orte";
	private $_rootDescription = "Enthällt alle orte ";
	
	
	/**
	 * Der User construktor
	 */
	function __construct() {
		
		parent::__construct ();
	
	}



	/**
	 * Giebt eine Liste von Orten zurück
	 * @param integer $count
	 * @param integer $offset
	 * @param string $areas
	 * @return array
	 */
	public function ActionGetOrtList($count, $offset, $areas = NULL){
			
		require_once 'db/resort/resort_orte.php';
		require_once 'db/resort/resort_orte_match.php';
		require_once 'db/resort/resort_orte_region.php';
		require_once 'db/apartment/Apartment.php';

		$db = resort_orte::getDefaultAdapter();
		$ortListSel = $db->select ();
		$ortListSel->from(array('o' => resort_orte::getTableNameStatic()) ,array( 'ort_name'=>'o.name' , 'apartment_count' => 'count(a.id)'));
		$ortListSel->joinLeft(array('m'=>resort_orte_match::getTableNameStatic()), "o.id = m.ort_id",array());
		$ortListSel->joinLeft(array('r'=>resort_orte_region::getTableNameStatic()), "m.arrea_id = r.id",array('region_name'=>'region'));
		$ortListSel->joinLeft(array('a'=>Apartment::getTableNameStatic()), "o.id = a.orts_id",array());
		
		if(!empty($areas) && strlen($areas) < 150 ){
			$ortListSel->where("r.region = ?",$areas );
		}

		$ortListSel->group("o.name");
		
		if(0 < $count && $count < 100){
			$ortListSel->limit($count);
		}else{
			$ortListSel->limit(10);
		}
		
		$ortListSel->order("apartment_count desc");
		
		$allOrts = $db->fetchAll($ortListSel);
		return $allOrts;
	}
	
	/**
	 * Entfernt einen Ort aus einer Region
	 * @param string $regionName
	 * @param string $ortName
	 */
	public function ActionRemoveOrtOffRegion($regionName,$ortName){
		require_once 'db/resort/resort_orte.php';
		$db = resort_orte::getDefaultAdapter();
		
		$select = "DELETE from usr_p41239_3.bt_resort_orte_match 
		where ort_id = (select  id from usr_p41239_3.bt_resort_orte where name = '$ortName')
		and arrea_id = (select  id from usr_p41239_3.bt_resort_orte_region where region = '$regionName')
		;";
		
		$rootOrt = $db->query($select);
	}
	
	
	/**
	 * Löscht die Region
	 * @param string $regionName
	 */
	public function ActionDelete($regionName){ 
		


		
		require_once 'citro/DBTable.php';
		$db = DBTable::getDefaultAdapter();

		require_once 'db/resort/resort_orte_region.php';
		
		$ortSel = $db->select();
		$ortSel->from( resort_orte_region::getTableNameStatic() , '*');
		$ortSel->where("region=?",$regionName);
		$regionA = $db->fetchRow($ortSel);

		if( $regionA === NULL )
			return FALSE;
		

		require_once 'db/resort/resort_orte_match.php';
		$db->delete(resort_orte_match::getTableNameStatic(),  $db->quoteInto("arrea_id=?", $regionA['id'] ) );
		$db->delete(resort_orte_region::getTableNameStatic(),  $db->quoteInto("id=?", $regionA['id'] ) );
		

		return TRUE;		
	}

	
	

	
	/**
	 * Fügt ein Element hinzu
	 * @param string $name
	 * @param string $regionName der Regionname unter welche Area der Ort liegen soll
	 * @param string $desc
	 */
	public function ActionNew($name,$regionName = "", $desc = ""){

		if( !empty( $regionName) ){
			require_once 'db/resort/resort_orte_region.php';
			$arreaTab = new resort_orte_region();
			$arreaSel = $arreaTab->select();
			$arreaSel->where("region=?",$regionName);
			$arreaRow = $arreaTab->fetchRow($arreaSel);
		
			if($arreaRow === NULL)
				throw new Exception("");
		}
		
		
		try {
			
			require_once 'db/resort/resort_orte.php';
			$orteTab = new resort_orte();
			
			$data = array();
			$data['name'] = $name;
			$data['text'] = $desc;
			$data['edata'] = DBTable::DateTime();
			$data['vdata'] = DBTable::DateTime();
			$data['access_create'] = $this->_rightsAcl->getAccess()->getId();
			$data['access_edit'] = $this->_rightsAcl->getAccess()->getId();
			$data['in_menue'] = 1;
			 FireBug::setDebug($data);
			$newOrtId = $orteTab->insert($data);
			
			
			
			if(!empty( $regionName) ){
				require_once 'db/resort/resort_orte_match.php';
				$matchTab = new resort_orte_match();
					
				$data = array();
				$data['ort_id'] = $newOrtId;
				$data['arrea_id'] = $arreaRow->offsetGet('id');
				$newMatchId = $matchTab->insert($data);
			}
			return TRUE;
			
			
		} catch (Exception $e) {
			FireBug::setDebug($e->getMessage());
			return FALSE;
		}
				
	}
	
	

	
	

	

	/**
	 * Erstellt eine neue Region in der Datenbank
	 * @param string $name
	 */
	public function ActionNewRegion($name){
		require_once 'db/resort/resort_orte_region.php';
		$arreaTab = new resort_orte_region();
		$data = array();
		$data['region'] = $name;
		try {
			$regionId = $arreaTab->insert($data);
			
			return $regionId;
		} catch (Exception $e) {
			return FALSE;
		}

		
		
	}
	
	

	
	/**
	 * Giebt eine einzelne Region zurück
	 * @param string $name Der Regionname nach dem Gesucht werden soll 
	 * @return multitype:
	 */
	public function ActionGetRegion($name){

		require_once 'db/resort/resort_orte_region.php';
		
		$regionenTab = new resort_orte_region();
		$regionSel = $regionenTab->select();
		$regionSel->where("region=?",$name);
		
		
		$regionRow = $regionenTab->fetchRow($regionSel);
		if($regionRow === null)
			return NULL;
		
		return $regionRow->toArray();


	}
	

	

	/**
	 * Giebt alle Regionen zurück
	 * @return multitype:
	 */
	public function ActionGetRegionen(){
	
		require_once 'db/resort/resort_orte_region.php';
		require_once 'db/resort/resort_orte_match.php';
		require_once 'db/resort/resort_orte.php';
		require_once 'db/apartment/Apartment.php';
		
		$db = resort_orte_region::getDefaultAdapter();
		
		
		
		$regionSel = $db->select();
		$regionSel->from( array("r" => resort_orte_region::getTableNameStatic()), array('region' , 'counts_orte' => 'count(m.arrea_id)' ) );
		$regionSel->joinLeft(	array ("m" => resort_orte_match::getTableNameStatic()),	"r.id = m.arrea_id",array() );
		$regionSel->joinLeft(	array ("o" => resort_orte::getTableNameStatic()), "m.ort_id = o.id", array("ort_names" => "GROUP_CONCAT(o.name )" ) );
		$regionSel->group("r.region");

		$regFetchAll = $db->fetchAll($regionSel);
	
		return $regFetchAll;
	}
	
	
	
	/**
	 * Fügt ein Ort zu einer Region hinzu
	 * @param string $regionname
	 * @param string $ortname
	 */
	public function ActionSetOrtAddRegion($regionname,$ortname){
		
		
		require_once 'db/resort/resort_orte.php';
		$db = resort_orte::getDefaultAdapter();

		$select = "	INSERT INTO bt_resort_orte_match (ort_id, arrea_id) 
					select  o.id, r.id from bt_resort_orte as o, bt_resort_orte_region as r
					where o.name = '$ortname' 
					and  r.region = '$regionname'
					and not exists(
						select id from bt_resort_orte_match as m2 
						where m2.ort_id = (select  o2.id from bt_resort_orte as o2	where name = '$ortname')   
						and m2.arrea_id = (select  r2.id from bt_resort_orte_region as r2 where region = '$regionname')
					 ) 
					;";

		$rootOrt = $db->query($select);
	}
	
	
	
	
	
	
	
	
	
}

?>