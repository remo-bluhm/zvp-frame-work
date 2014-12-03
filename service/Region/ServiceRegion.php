<?php

require_once 'citro/service-class/AService.php';

/**
 * Dieser Serviece Verwaltet die orte der Resourts
 *
 * @author Max Plank
 * @version 1.0
 *         
 */
class ServiceRegion extends AService {
	

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
	 * @param array $where
	 * @param array $spalten
	 * @return array
	 */
	public function ActionList($count, $offset = 0, $where = array(), $spalten = array()){
			
		require_once 'db/resort/ResortRegion.php';
		//require_once 'db/resort/resort_orte_match.php';

//		require_once 'db/apartment/Apartment.php';

		$db = ResortRegion::getDefaultAdapter();
		$regListSel = $db->select ();
		$regListSel->from(array('r' => "resort_region") ,array( 'name_uid'=>'r.name_uid' , 'name' => 'r.name'));
		
//		$ortListSel->joinLeft(array('a'=>Apartment::getTableNameStatic()), "o.id = a.orts_id",array());
		
		// suche nach Namen
		if(!empty($where["name_uid"])){
			$regListSel->joinLeft(array('rs'=>"resort_region"), "r.pid = rs.id",array());
			$regListSel->where("rs.name_uid LIKE ?", $where["name_uid"]);			
		}

		//$ortListSel->group("o.name");
		
		$regListSel->limit($count,$offset);
		
		
		//$ortListSel->order("apartment_count desc");
		
		$allOrts = $db->fetchAll($regListSel);
		return $allOrts;
	}
	
	
	
	
	
	
	
	/**
	 * Giebt die Contactdaten eines Contactes zurück
	 *
	 * @param string $nameUid Die Uid des Contacts als String
	 * @param array $spalten
	 * @return array
	 */
	public function ActionSingle($nameUid, $spalten = array()){
	
// 		if(is_string($spalten) && $spalten=="*"){
// 			$spReg = array();
// 			$spReg[] = "name_uid";
// 			$spReg[] = "name";

// 		}
// 		if(!is_array($spalten))$spalten = array();
	
		require_once 'db/resort/ResortRegion.php';
		$db = ResortRegion::getDefaultAdapter();
	
	
		$spReg = array();
		$spReg[] = "name_uid";
		$spReg[] = "name";
	
		// 		$spA["firma"] = "firma";
		// 		$spA["position"] = "position";
	
		$sel = $db->select ();
		$sel->from( array('r' => "resort_region" ), $spReg );
	
// 		if( in_array('usercreate_name',$spalten) ){
				
// 			$sel->joinLeft(array('a'=>"sys_access"), "c.access_create = a.id",array("create_access_guid"=>"guid") );
// 			$sel->joinLeft(array('c1'=>"contacts"), "a.contacts_id = c1.id", array ('create_access_name' => 'CONCAT(c1.first_name," ",c1.last_name )' ) );
// 		}
	
// 		if( in_array('useredit_name',$spalten) ){
// 			require_once 'db/sys/access/sys_access.php';
// 			$sel->joinLeft(array('a2'=>"sys_access"), "c.access_edit = a2.id" ,array("edit_access_guid"=>"guid") );
// 			$sel->joinLeft(array('c2'=>"contacts"), "a2.contacts_id = c2.id", array ('edit_access_name' => 'CONCAT(c2.first_name," ",c2.last_name )')  );
// 		}
	
	
		$adresSp = array();
		$adresSp["adr_id"] = "id";
		$adresSp["adr_art"] = "art";
		$adresSp["adr_nameline"] = "nameline";
		$adresSp["adr_street"] = "strasse";
		$adresSp["adr_ort"] = "ort";
		$adresSp["adr_zip"] = "plz";
		$adresSp["adr_land"] = "land";
		$adresSp["adr_landpart"] = "landpart";
		$adresSp["adr_infotext"] = "infotext";
	
		//$sel->joinLeft(array('ca'=>"contact_address"), "c.main_contact_address_id = ca.id" ,$adresSp);
	
	
		$phoneSp = array();
		$phoneSp["phone_id"] = "id";
		$phoneSp["phone_art"] = "art";
		$phoneSp["phone_number"] = "number";
		$phoneSp["phone_text"] = "text";
	
	
		//$sel->joinLeft(array('p'=>"contact_phone"), "c.main_contact_phone_id = p.id" ,$phoneSp);
	
	
		$mailSp = array();
		$mailSp["email_id"] = "id";
		$mailSp["email_adress"] = "mailadress";
		$mailSp["email_text"] = "text";
			
	
		//$sel->joinLeft(array('em'=>"contact_email"), "c.main_contact_email_id = em.id" ,$mailSp);
	
		$sel->where("r.name_uid = ?",$nameUid);

	
		$regionRetA = $db->fetchRow($sel);
	
		// falls nichts gefunden wurde dann abbruch
		if($regionRetA === FALSE) return FALSE;
			
// 		//////////////////////////////////////
// 		if( in_array('all_address',$spalten) ){
	
				
// 			$mainAddressId = (int)$contactA["address_id"];
// 			$adresSp["adr_is_main"] = "IF(`id` = ".$mainAddressId.", 'TRUE', 'FALSE' ) ";
				
// 			$selAdress = $db->select ();
// 			$selAdress->from( "contact_address" , $adresSp );
// 			$selAdress->where("contacts_id = ?",$contactA["id_name"]);
				
	
// 			$contactA["adresses"] = $db->fetchAll($selAdress);
// 		}
// 		//////////////////////////////////////////////////////////////////
// 		if( in_array('all_phone',$spalten) ){
				
// 			$mainPhoneId = (int)$contactA["phone_id"];
// 			$phoneSp["phone_is_main"] = "IF(`id` = ".$mainPhoneId.", 'TRUE', 'FALSE' ) ";
				
// 			$selPhone = $db->select ();
// 			$selPhone->from( "contact_phone" , $phoneSp );
// 			$selPhone->where("contacts_id = ?",$contactA["id_name"]);
				
// 			$contactA["numbers"] = $db->fetchAll($selPhone);
// 		}
// 		//////////////////////////////////////////////////////////////////
// 		if( in_array('all_email',$spalten) ){
	
// 			$mainEmailId = (int)$contactA["email_id"];
// 			$mailSp["email_is_main"] = "IF(`id` = ".$mainEmailId.", 'TRUE', 'FALSE' ) ";
	
// 			$selEmail = $db->select ();
// 			$selEmail->from( "contact_email" , $mailSp );
// 			$selEmail->where("contacts_id = ?",$contactA["id_name"]);
	
// 			$contactA["emails"] = $db->fetchAll($selEmail);
// 		}
	
	
// 		unset($contactA["id_name"]);
// 		unset($contactA["address_id"]);
// 		unset($contactA["phone_id"]);
// 		unset($contactA["email_id"]);
	
		FireBug::setDebug($regionRetA,"ServiceRegion Single");
		return $regionRetA;
	
	
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	/**
	 * Entfernt einen Ort aus einer Region
	 * @param string $regionName
	 * @param string $ortName
	 */
	public function ActionRemoveOrtOffRegion($regionName,$ortName){
		require_once 'db/resort/ResortCity.php';
		$db = ResortCity::getDefaultAdapter();
		
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

		require_once 'db/resort/ResortRegion.php';
		
		$ortSel = $db->select();
		$ortSel->from( ResortRegion::getTableNameStatic() , '*');
		$ortSel->where("region=?",$regionName);
		$regionA = $db->fetchRow($ortSel);

		if( $regionA === NULL )
			return FALSE;
		

		require_once 'db/resort/resort_orte_match.php';
		$db->delete(resort_orte_match::getTableNameStatic(),  $db->quoteInto("arrea_id=?", $regionA['id'] ) );
		$db->delete(ResortRegion::getTableNameStatic(),  $db->quoteInto("id=?", $regionA['id'] ) );
		

		return TRUE;		
	}

	
	

	
	/**
	 * Fügt ein Element hinzu
	 * @param string $overRegKey
	 * @param string $newRegKey der Regionname unter welche Area der Ort liegen soll
	 * @param string $newRegName
	 * @param array $fields
	 */
	public function ActionNew($overRegKey, $newRegKey,  $newRegName , $fields = array()  ) {

		
		require_once 'db/resort/ResortRegion.php';
		$overRegKey = ResortRegion::testNameUid($overRegKey);
		if($overRegKey === NULL)		
			throw new Exception("Over Region is not valide!",E_ERROR);
		
		
		$regTab = new ResortRegion();
		$arreaSel = $regTab->select();
		$arreaSel->where("name_uid=?",$overRegKey);
		$arreaRow = $regTab->fetchRow($arreaSel);
	
		if($arreaRow === NULL)
			throw new Exception("Over Region exist not!",E_ERROR);
		
		
		$existRegKey = DBConnect::getConnect()->fetchOne("select id from resort_region where name_uid = '$newRegKey'");
		if($existRegKey !== FALSE) throw new Exception("Region ($newRegKey) Exist!",E_ERROR);
		
		try {
			
			

			
			
			
			$data = array();
			$data['pid'] = $arreaRow->offsetGet("id");
			$data['name_uid'] = $newRegKey;
			$data['name'] = $newRegName;
			//$data['edata'] = DBTable::DateTime();
			//$data['vdata'] = DBTable::DateTime();
			//$data['access_create'] = $this->_rightsAcl->getAccess()->getId();
			//$data['access_edit'] = $this->_rightsAcl->getAccess()->getId();
			//$data['in_menue'] = 1;
			 FireBug::setDebug($data);
			$newOrtId = $regTab->insert($data);
			
			return $newOrtId;
			
			
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
		require_once 'db/resort/ResortRegion.php';
		$arreaTab = new ResortRegion();
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

		require_once 'db/resort/ResortRegion.php';
		
		$regionenTab = new ResortRegion();
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
	
		require_once 'db/resort/ResortRegion.php';
		require_once 'db/resort/resort_orte_match.php';
		require_once 'db/resort/ResortCity.php';
		require_once 'db/apartment/Apartment.php';
		
		$db = ResortRegion::getDefaultAdapter();
		
		
		
		$regionSel = $db->select();
		$regionSel->from( array("r" => ResortRegion::getTableNameStatic()), array('region' , 'counts_orte' => 'count(m.arrea_id)' ) );
		$regionSel->joinLeft(	array ("m" => resort_orte_match::getTableNameStatic()),	"r.id = m.arrea_id",array() );
		$regionSel->joinLeft(	array ("o" => ResortCity::getTableNameStatic()), "m.ort_id = o.id", array("ort_names" => "GROUP_CONCAT(o.name )" ) );
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
		
		
		require_once 'db/resort/ResortCity.php';
		$db = ResortCity::getDefaultAdapter();

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