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
		require_once 'db/resort/ResortOrte.php';
		$db = ResortOrte::getDefaultAdapter();
	
		$sql = "SELECT count(id) FROM `".ResortOrte::getTableNameStatic()."`; ";
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
			
		require_once 'db/resort/ResortOrte.php';

		require_once 'db/apartment/Apartment.php';

		$db = ResortOrte::getDefaultAdapter();
		$ortListSel = $db->select ();
		$ortListSel->from(array('o' => ResortOrte::getTableNameStatic()) ,array('ort_name' , 'counts' => 'count(a.id)'));
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
		$spR["name_id"] = "name";
		$spR["name"] = "name";
		
		
		$regionSel->from(array( "r" => "resort_orte_region" ),$spR );
			//$string = $regionSel->__toString();
	
		//$regionSel->where("o.name=?",$name);
			
		$regionAll = $regTab->fetchRow($regionSel);
		if($regionAll === NULL)
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
		require_once 'db/resort/ResortOrte.php';

		$ortTab = new ResortOrte();
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
	
	
		require_once 'db/resort/ResortOrte.php';
		$db = ResortOrte::getDefaultAdapter();
	
		$searchListSel = $db->select ();
		$searchListSel->from( array('o' => ResortOrte::getTableNameStatic() ), array( "o.name") );

		
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
	
	
		require_once 'db/resort/ResortOrte.php';
		require_once 'db/sys/access/sys_access.php';
		require_once 'db/contact/Contacts.php';
		

		$db = ResortOrte::getDefaultAdapter();
	

		$ortListSel = $db->select ();
		$ortListSel->from(array('o' => ResortOrte::getTableNameStatic()) );
		
		
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
	
	
		require_once 'db/resort/ResortOrte.php';
		require_once 'db/sys/access/sys_access.php';
		require_once 'db/contact/Contacts.php';
	
	
		$db = ResortOrte::getDefaultAdapter();
	
		$spA = array();
		$spA["id"] = "id";
		//$spA["anzahl"] = "count(uid)";
		$spA["name"] = "name";
		$spA["create"] = "edata";
		$spA["edit"] = "vdata";
		$spA["name"] = "name";
		$spA["zip"] = "zip";
		$spA["land"] = "land";
		$spA["landpart"] = "landpart";
		
		$ortListSel = $db->select ();
		$ortListSel->from(array('o' => ResortOrte::getTableNameStatic()), $spA );
	

		$ortListSel->limit($count,$offset);
		$allOrts = $db->fetchAll( $ortListSel );
		return $allOrts;
	}
	

	
	/**
	 * Fügt ein Element hinzu
	 * @param string $region
	 * @param string $name
	 * @param array $fields
	 * @return integer|bool
	 */
	public function ActionNew($region, $name, $fields = array()){

		// Setzen der $fieldsvariabel auf array
		if(!is_array($fields))$fields = array();
		//FireBug::setDebug($fields,"ServContact New Fields");
		// Prüfen des lastName
		require_once 'db/contact/Contacts.php';
		$lastName = Contacts::testLastName($lastName);
		if( $lastName !== NULL ){
		
		
			$contTab = new Contacts();
			$contTab->getDefaultAdapter()->beginTransaction();
			try {
		
		
				$contactId =  $contTab->insertDataFull( $this->getAccess()->getId(), $lastName, $fields);
				$contactUid = $contTab->getUid();
		
				// setzen von Adressen
				$mainAdressId = NULL;
				if(array_key_exists("adresses",$fields) && is_array($fields["adresses"]) ){
					require_once 'db/contact/address/Address.php';
					$adrTab = new Address();
					foreach ($fields["adresses"] as $adrFields){
						$adrTab->clearData();
						if(is_array($adrFields) && !empty($adrFields["adr_ort"]) ){
							$adressId = $adrTab->insertDataFull($this->getAccess()->getId(), $contactId, $adrFields);
							if (!empty($adrFields["adr_is_main"]) && $adressId !== NULL ){
								if( strtoupper( $adrFields["adr_is_main"]) == "TRUE" ) $mainAdressId = $adressId;
							}
						}
					}
				}
		
				// setzen der Telefonnummern
				$mainPhoneId = NULL;
				if(array_key_exists("numbers",$fields) && is_array($fields["numbers"]) ){
					require_once 'db/contact/phone/Phone.php';
					$phoneTab = new Phone();
					foreach ($fields["numbers"] as $phoneFields){
						$phoneTab->clearData();
						if(is_array($phoneFields) && !empty($phoneFields["phone_number"])){
							$phoneId = $phoneTab->insertDataFull($this->getAccess()->getId(), $contactId, $phoneFields);
							if (!empty($phoneFields["phone_is_main"]) && $phoneId !== NULL ){
								if(strtoupper ( $phoneFields["phone_is_main"]) == "TRUE") $mainPhoneId = $phoneId;
							}
						}
					}
				}
		
				// setzen der Mails
				$mainMailId = NULL;
				if(array_key_exists("emails",$fields) && is_array($fields["emails"]) ){
					require_once 'db/contact/email/Email.php';
					$mailTab = new Email();
					foreach ($fields["emails"] as $mailFields){
						$mailTab->clearData();
						if(is_array($mailFields) && !empty($mailFields["email_adress"])){
							$mailId = $mailTab->insertDataFull($this->getAccess()->getId(), $contactId,$mailFields);
							if (!empty($mailFields["email_is_main"])){
								if(strtoupper ( $mailFields["email_is_main"]) == "TRUE"){
									$mainMailId = $mailId;
		
								}
							}
						}
					}
				}
		
				// Setzen der Haupt Adressen, Mails oder Telefonnummern
				$updateData = array();
				if($mainAdressId !== NULL) $updateData[Contacts::SP_ADRESS_ID] = $mainAdressId;
				if($mainPhoneId !== NULL) $updateData[Contacts::SP_PHONE_ID] = $mainPhoneId;
				if($mainMailId !== NULL) $updateData[Contacts::SP_EMAIL_ID] = $mainMailId;
				if(count($updateData) > 0 )$contTab->update($updateData, "id = ".$contactId);
		
		
				//$contactUid = NULL;
		
				// Nochmaliges Prüfen auf contactid
				if($contactUid === NULL){
					$contTab->getAdapter()->rollBack();
					return FALSE;
				}
				$contTab->getAdapter()->commit();
				return $contactUid;
		
			} catch (Exception $e) {
				$contTab->getAdapter()->rollBack();
				return FALSE;
			}
		
		}else{
			// Fehler da der Lastname nicht valiede ist
			return FALSE;
		}
		return FALSE;
		// FireBug::setDebug($newUnId);
		
		
		try {
			
			require_once 'db/resort/ResortOrte.php';
			$orteTab = new ResortOrte();
			
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

		require_once 'db/resort/ResortOrte.php';
		
		$ortSel = $db->select();
		$ortSel->from( ResortOrte::getTableNameStatic() , '*');
		$ortSel->where("ort_name=?", $ortname);
		$ortRow = $db->fetchRow($ortSel);
		
		if( $ortRow === NULL )
			return FALSE;
	
		require_once 'db/resort/resort_orte_match.php';
		$db->delete(resort_orte_match::getTableNameStatic(),  $db->quoteInto("ort_id=?", $ortRow['id'] ) );
		$db->delete(ResortOrte::getTableNameStatic(),  $db->quoteInto("id=?", $ortRow['id'] ) );
		return TRUE;
	}
	
	
	
	/**
	 * Umschreiben des Ortsnamens
	 * @param string $oldOrtName
	 * @param string $newOrtName
	 */
	public function ActionEdit($oldOrtName,$newOrtName){
		
		require_once 'db/resort/ResortOrte.php';
		$orteTab = new ResortOrte();
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
		
		$ortListSel->from(array("resort_orte") );
	
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
	 * @param string $name
	 * @return boolean|array
	 */
	public function ActionExist($name){
		require_once 'db/resort/ResortOrte.php';
		$tab = new ResortOrte();
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