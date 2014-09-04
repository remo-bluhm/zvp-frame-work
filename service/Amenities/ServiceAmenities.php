<?php

require_once 'citro/service-class/AService.php';

/**
 * Dieser Serviece verwaltet alle Contacte
 *
 * @author Max Plank
 * @version 1.0
 *         
 */
class ServiceAmenities extends AService {
	

	
	
	/**
	 * Der User construktor
	 */
	function __construct() {
		
		parent::__construct ();
	
	}

	
	/**
	 * Giebt alle Rootelemente der Amenities zurück
	 * @param string $rootKey 
	 * @return array
	 */
	public function ActionGetRootElement($rootKey){
	
		require_once 'db/amenities/amenities.php';
		$amTab = new amenities();

		$spalten = array();
		$spalten['element_id'] = "CONCAT( rootkey, '-', sys_id )" ;
		$spalten[] = "name" ;
		
		$amSel = $amTab->select();
		$amSel->from(amenities::getTableNameStatic(),$spalten);
		$amSel->where("lft = ?",1);
		$amSel->where("rootkey = ?",$rootKey);

		
		$allAm = $amTab->fetchRow($amSel);
	
		if($allAm !== NULL)
			
		return $allAm;
	}
	
	
	/**
	 * Giebt alle Rootelemente der Amenities zurück
	 * @return array
	 */
	public function ActionGetAllRootElement(){
	
		require_once 'db/amenities/amenities.php';
		$amTab = new amenities();
		$amSel = $amTab->select();
		$amSel->where("lft = ?",1);
		$amSel->order('rootkey');
		$allAm = $amTab->fetchAll($amSel)->toArray();
	
		return $allAm;
	}
	
	/**
	 * Erstellt ein neues Rootelement
	 * @param string $rootKey
	 * @param string $name
	 * @param string $desc
	 * @throws Exception
	 * @return boolean
	 */
	public function ActionNewRoot($rootKey,$name,$desc = NULL){
		
		require_once 'db/amenities/amenities.php';
		$db = amenities::getDefaultAdapter();
	
		$rootKey = amenities::checkRootKey($rootKey);
		if($rootKey === NULL)
			return FALSE;
		$name = amenities::checkName($name);
		$desc = amenities::checkValue($desc);
		
		$db->beginTransaction();
		
		try{
			
		
			// Suchen eines elementes mit einen keynamen
			$select = $db->select();
			$select->from(amenities::getTableNameStatic());
			$select->where('rootkey = ? ',$rootKey);
			$elements = $db->fetchRow($select);
			
			// prüfen auf vorhandensein eines Rootkeys mit dem gleichen key
		 	if ($elements !== FALSE)
				throw new Exception("Rootkey ist schon vorhanden",E_ERROR);
						
			$amenitiesTab = new amenities();
			$amenitiesTab->setRoot($rootKey,$name,$desc);
			
			$db->commit();
		}catch (Exception $eTrans){
			$db->rollBack();
			//die($eTrans->getMessage());
			return FALSE;
		}
		return TRUE;
		
	}
	
	/**
	 * Löscht ein Root element mit all seinen unterelementen
	 * @param string $rootKey
	 */
	public function ActionDeleteRoot($rootKey){
		
		require_once 'db/amenities/amenities.php';
		$rootKey = amenities::checkRootKey($rootKey);
		
		if($rootKey === NULL)
			return NULL;
		
		$amTab = new amenities();
		$amZeilen = $amTab->deleteRootElement($rootKey);
		
		require_once 'db/amenities/amenities_version.php';
		$avTab = new amenities_version();
		$avZeilen = $avTab->deleteRootElement($rootKey);
		
		
		
	}
	
	
	
	
	/**
	 * Giebt eine Liste von Amenities zurück
	 *
	 * @param string $rootKey
	 * @param string $addColumn [level,offspring] ist noch nich fertiggestellt
	 * @return array
	 */
	public function ActionGetElementList($rootKey,$addColumn = ""){
	
		require_once 'db/amenities/amenities.php';
	
		$amenitiesTab = new amenities();
		$elements = $amenitiesTab->getNestedTree($rootKey);
	
		return $elements;
	}
	

	

	
	
	
	
	public function ActionExistElementName($rootKey, $name){
		
		require_once 'db/amenities/amenities.php';
		$amenitiesTab = new amenities();
		
		
		// Prüfen des Namens
		$name = $amenitiesTab->checkName($name);
		if($name === NULL)
			return FALSE;
		
		
		
		$select = $amenitiesTab->select();
		$select->where("rootkey=?",$rootKey);
		$select->where("name=?",$name);
		
		$amRow = $amenitiesTab->fetchRow($select);
		if($amRow === null)
			return FALSE;
		
		return $amRow->toArray();
		
	}
	
	
	
	/**
	 * Fügt ein Element hinzu
	 * @param string $elementId das zu unterliegende Element
	 * @param string $name
	 * @param string $text
	 * @param bool $sendElementToKnot
	 */
	public function ActionNewElement($elementId,$name,$text = "",$sendElementToKnot = FALSE){
		
		require_once 'db/amenities/amenities.php';
		$amenitiesTab = new amenities();
		
		
		// Prüfen des Namens
		$name = $amenitiesTab->checkName($name);
		if($name === NULL)
			return FALSE;

				
		// holen des Darüberliegenden elementes
		$tabRow = $amenitiesTab->getElement(amenities::getRootKey($elementId),amenities::getSysId($elementId),"SYSID");
		if($tabRow === FALSE)
			throw new Exception("Vorherliegendes Element konnte nicht gefunden werden!", E_ERROR);
		
	
		
		$lft = (integer)$tabRow['lft'];
		$rgt = (integer)$tabRow['rgt'];
		$rootKey = amenities::getRootKey($elementId);

		$db = amenities::getDefaultAdapter();
		$db->beginTransaction();
		try{

			// erstellen der neuen RandId
			$mtRand = $amenitiesTab->getNewSysId($rootKey);
			if($mtRand === NULL)
				throw new Exception("Es konnt keine neue SystemId erzeugt werden!", E_ERROR);

			// Element muss ein Knoten sein also auf jedenfall addElement
 			if( $lft+1 < $rgt )
 				$sendElementToKnot = FALSE;
 			
			// prüfen ob das element ein rootelemente ist dann daraus auf jedenfalle ein knoten machen
			if($lft == 1 && 2 == $rgt )
				$sendElementToKnot = TRUE;
			
			if($sendElementToKnot === FALSE){
				$data = $amenitiesTab->addElement($lft,$rgt,$mtRand,$rootKey ,$name,$text);
			}else {
				$data = $amenitiesTab->addKnotElement($lft,$rgt,$mtRand,$rootKey ,$name,$text);
			}
			
			$db->commit();
			
			$data['element_id'] = $data['rootkey']."-".$data['sys_id'];
			
			return $data;
			
		}catch (Exception $eTrans){
			$db->rollBack();
			
		}
		
	}

	
	/**
	 * Bearbeitet das Element
	 * @param string $elementId Eindeutiger Bezeichner für das Element bestehend aus RootKey und SysId zb [APP-542698]
	 * @param string $name
	 * @param string $value
	 */
	public function ActionEditElement($elementId,$name = NULL,$value = NULL){
				
		require_once 'db/amenities/amenities.php';	
		$amenitiesTab = new amenities();
		
		// Prüfen des Namens
		$name = $amenitiesTab->checkName($name);
		$value = $amenitiesTab->checkName($value);
		
		if($name === NULL || $value === NULL)
			return FALSE;
		
		try {
						
			$isEdit = $amenitiesTab->editElement(amenities::getRootKey($elementId) , amenities::getSysId($elementId), $name, $value);
	
			return $isEdit;
		} catch (Exception $e) {
			return FALSE;
		}
		
	}
	
	/**
	 * Löscht ein Element oder einen Knoten
	 * Bei einen Knoten werden all seine unterelemente ein Level nach oben verschoben
	 * @param string $elementId
	 */
	public function ActionDeleted($elementId){
		
		
		
		require_once 'db/amenities/amenities.php';
		$db = amenities::getDefaultAdapter();
		$db->beginTransaction();
		
		try{
			
			$amenitiesTab = new amenities();
			// holen des Darüberliegenden elementes
			$tabRow = $amenitiesTab->getElement(amenities::getRootKey($elementId),amenities::getSysId($elementId),"SYSID");
			
			$rgt = (integer)$tabRow['rgt'];
			$lft = (integer)$tabRow['lft'];
			$rootKey = (string)$tabRow['rootkey'];
		
			// Prüfen ob das zu löschende Element die wurzel ist wenn ja dann abbruch
			if($lft == 1)
				throw new Exception("Element darf kein Rootelement sein", E_ERROR);
			
			// sichern des kompletten baumes
			$this->ActionSaveTree($rootKey,"Not save for delete Element(".$elementId.")");
			
			$amenitiesTab->deleteElement($lft,$rgt,$rootKey);
			$db->commit();
			return TRUE;
		
		}catch (Exception $eTrans){
			$db->rollBack();
			die($eTrans->getMessage());
			return FALSE;
			
		}
		
	}
		

	
	
	/**
	 * Löscht einen Knoten mit all seinen unterelementen
	 * @param string $elementId
	 */
	public function ActionDeleteWithUnderElements($elementId){
		require_once 'db/amenities/amenities.php';
		$db = amenities::getDefaultAdapter();
		$db->beginTransaction();
		
		try{
			
			$amenitiesTab = new amenities();
			// holen des Darüberliegenden elementes
			$tabRow = $amenitiesTab->getElement(amenities::getRootKey($elementId),amenities::getSysId($elementId),"SYSID");
			
			$rgt = (integer)$tabRow['rgt'];
			$lft = (integer)$tabRow['lft'];
			$rootKey = (string)$tabRow['rootkey'];

			// Prüfen ob das zu löschende Element die wurzel ist wenn ja dann abbruch
			if($lft == 1)
				throw new Exception("Element darf kein Rootelement sein", E_ERROR);
			
			// sichern des kompletten baumes
			$this->ActionSaveTree($rootKey,"Not save for delete Elementtree(".$elementId.")");
			
			// Lösche das übergebene Elemente mit all seinen unterelementen
			$amenitiesTab->deleteElementWithUnderElement($lft,$rgt,$rootKey);
			
			$db->commit();
			return TRUE;
		
		}catch (Exception $eTrans){
			$db->rollBack();
			die($eTrans->getMessage());
			return FALSE;
				
		}
	}
	
	
	
	
	/**
	 * Verschiebt ein Element oder Gruppe eins nach oben 
	 * 
	 * aber nur innerhalb eines levels
	 * @param string $elementId
	 */
	public function ActionUp($elementId){
		require_once 'db/amenities/amenities.php';
		$db = amenities::getDefaultAdapter();
		$db->beginTransaction();
		try{
				
			$amenitiesTab = new amenities();

			// hollen des Hauptelementes
			$fromElem = $amenitiesTab->getElement(amenities::getRootKey($elementId),amenities::getSysId($elementId), "SYSID");
			$fromRgt = (integer)$fromElem["rgt"];
			$fromLft = (integer)$fromElem["lft"];
			
			$rootKey = (string)$fromElem["rootkey"];
	
			
			// hollen des Elementes das getauscht werden soll
			$toElememts = $amenitiesTab->getElement($rootKey,$fromLft-1, "RGT");
			//prüfen ob Element gefunden wurde
			if($toElememts === NULL)
				return FALSE;
			
			
			$toLft = (integer)$toElememts["lft"];
			$toRgt = (integer)$toElememts["rgt"];

			$amenitiesTab->move($rootKey,$fromLft,$fromRgt,$toLft);

			$db->commit();
			return TRUE;
				
				
		}catch (Exception $eTrans){
			//die($eTrans->getMessage());
				
			$db->rollBack();
		return FALSE;
		}
	}
	
	/**
	 * Verschiebt ein Element oder Gruppe eins nach unten
	 *
	 * aber nur innerhalb eines levels
	 * @param string $elementId
	 */
	public function ActionDown($elementId){
	
		require_once 'db/amenities/amenities.php';
	
		$db = amenities::getDefaultAdapter();
		$db->beginTransaction();
	
		try{
				
			$amenitiesTab = new amenities();
	
			$fromElem = $amenitiesTab->getElement(amenities::getRootKey($elementId),amenities::getSysId($elementId),"SYSID");
				
			$fromRgt = (integer)$fromElem["rgt"];
			$fromLft = (integer)$fromElem["lft"];
			$rootKey = (string)$fromElem["rootkey"];
	
	
			$toElememts = $amenitiesTab->getElement($rootKey,$fromRgt+1,"LFT");
			// prüfen ob toElement gefunden wurde
			if($toElememts === NULL)
				return FALSE;
			
			$toRgt = (integer)$toElememts["rgt"];
			$toLft = (integer)$toElememts["lft"];
				
	
	
				
			$amenitiesTab->move($rootKey,$toLft,$toRgt,$fromLft);
	
			$db->commit();
			return TRUE;
				
				
		}catch (Exception $eTrans){
			$db->rollBack();
			return FALSE;
		}
	}
		
	/**
	 * Verschiebt ein Element in ein Andere Gruppe
	 * @param string $fromElementId
	 * @param string $toElementId
	 * @param bool $into
	 */
	public function ActionMove($fromElementId,$toElementId, $into = TRUE){
		require_once 'db/amenities/amenities.php';
		$db = amenities::getDefaultAdapter();
		$db->beginTransaction();
		try{
			$amenitiesTab = new amenities();
	
			$fromElem = $amenitiesTab->getElement(amenities::getRootKey($fromElementId),amenities::getSysId($fromElementId),"SYSID");
	
			$fromRgt = (integer)$fromElem["rgt"];
			$fromLft = (integer)$fromElem["lft"];
			$rootKey = (string)$fromElem["rootkey"];
				
				
			$toElememts = $amenitiesTab->getElement(amenities::getRootKey($toElementId),amenities::getSysId($toElementId),"SYSID");
			$toRgt = (integer)$toElememts["rgt"];
			$toLft = (integer)$toElememts["lft"];
	
			// Das Rootelement darf nicht verschoben werden
			if($fromLft == 1 )
				return FALSE;
				
			$anzahlElemente = $amenitiesTab->move($rootKey,$fromLft,$fromRgt,$toRgt);
			$db->commit();
			return TRUE;
				
		}catch (Exception $eTrans){
			$db->rollBack();
			return FALSE;
				
		}
	}
	
	
	
	
	/**
	 * Sichert einen Kompletten Baum die unter einen Rootkey liegen
	 * @param string $rootKey
	 * @param string $description
	 */
	public function ActionSaveTree($rootKey,$description = NULL){
		
		if(!is_string($description))
			$description = "not set";
		

		
		if(strlen($rootKey) > 3)
			return FALSE;
		
		require_once 'db/amenities/amenities.php';
		$amTab = new amenities();
		$amSel = $amTab->select();
		$amSel->where("rootkey = ?",$rootKey);
		$amAll = $amTab->fetchAll($amSel)->toArray();
	
		if(count($amAll) > 0){

			require_once 'db/amenities/amenities_version.php';
			$amVersTab = new amenities_version();
			
			$data = array();
			$data['value'] = serialize($amAll);
			$data['edata'] = DBTable::getDateTime();
			$data['rootkey'] = $rootKey;
			$data['description'] = $description;
		
			$amVersTab->insert($data);
				
		}
		
		
		
		
		
		
	}
	
	/**
	 * Giebt alle sicheungen zurück
	 * die unter einen Rootkey liegen
	 * @param string $rootKey
	 */
	public function ActionGetAllSecuritys($rootKey){
	
		require_once 'db/amenities/amenities.php';
		$rootKey = amenities::checkRootKey($rootKey);
		
		require_once 'db/amenities/amenities_version.php';
		$amTab = new amenities_version();
		$amSel = $amTab->select();
		$amSel->where("rootkey = ?",$rootKey);
		$amSel->order('edata DESC');
		$allAm = $amTab->fetchAll($amSel)->toArray();
		
		return $allAm;
	}
	
	/**
	 * Stellt eine Sicherung wieder her
	 * @param integer $resourtId
	 */
	public function ActionToRestoreTree($resourtId){
	
		require_once 'db/amenities/amenities_version.php';
		$amTab = new amenities_version();
		$amSel = $amTab->select();
		$amSel->where("id = ?",$resourtId);
		$tree = $amTab->fetchRow();
		
	
		if($tree !== NULL ){
			$resourtId = $tree->offsetGet('id');
			$value = $tree->offsetGet('value');
			$rootkey = $tree->offsetGet('rootkey');
			$value = unserialize($value);
		
			require_once 'db/amenities/amenities.php';
			$amenitiesTab = new amenities();
			
			if(is_array($value)){
				
		
				
				$this->ActionSaveTree($rootkey,"Save by restore. Restoreid delete: ".$resourtId);
				
				$where = array();
				$where[] = "rootkey='".$rootkey."'";
				$amenitiesTab->delete($where);

				
				foreach ($value as $element){
					unset($element['id']);
					$amenitiesTab->insert($element);
				}
				
				$this->ActionDeleteRestoreTree($resourtId, $rootkey);
	
					
				
			}
			
			
			
			
		}
	
		return TRUE;
	}
		
	/**
	 * Löscht eine Sicherung
	 * @param integer $restoreId
	 * @param string $rootkey
	 */
	public function ActionDeleteRestoreTree($restoreId,$rootkey=NULL){
		
		require_once 'db/amenities/amenities_version.php';
		
		if($rootkey === NULL || count($rootkey) !== 3 ){
			
			$amTab = new amenities_version();
			$amSel = $amTab->select();
			$amSel->where("id = ?",$restoreId);
			$tree = $amTab->fetchRow();
			
			$rootkey = $tree->offsetGet('rootkey');
			
		}

		$amDel = new amenities_version();
		
		$whereDel = array();
		$whereDel[] = "rootkey = '".$rootkey."'";
		$whereDel[] = "id = ".$restoreId;
		$amDel->delete($whereDel);
		
		
	}
	
	
	

	
	
	

	
	
	
	
	
	
	
	
	
	
}

?>