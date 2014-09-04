<?php
require_once 'citro/DBTable.php';
/**
 * class resourt_orte
 *
 * Description for class resourt_orte
 *
 * @author:
*/
class resort_orte extends DBTable {
	
	protected $_TableName = "resort_orte";
	const SP_ID = "id";
	
	const SP_DATA_CREATE = "edata";
	const SP_DATA_EDIT = "vdata";
	const SP_USER_CREAT = "usercreat";
	const SP_USER_EDIT = "useredit";
	
	const SP_PLZ = "plz";
	const SP_ORT_NAME = "ort";
	const SP_TEXT = "text";
	const SP_IN_MENUE = "in_menu";
	
	const SP_GMAP_KARTE_X = "gmap_karte_x";
	const SP_GMAP_KARTE_Y = "gmap_karte_y";
	const SP_GMAP_ZOOM = "gmap_zoom";

	
	const MIN_SYSID = 100000;
	const MAX_SYSID = 999999;
	const MAX_WHILE_SYSID = 10;
	
	const MAX_NAME_LENGTH = 45;
	const MAX_DESC_LENGTH = 65000;
	
	
// 	public function ortExist($ortName){
// 		$tabSelect = $this->select();
// 		$tabSelect->where(self::SP_ORT_NAME." = ?", $ortName);
		
// 		$tabRow = $this->fetchRow($tabSelect);
// 		if($tabRow !== NULL){
// 			return $tabRow->toArray();
// 		}else{
// 			return FALSE;
// 		}
		
// 	}
	
	

	
	
	/**
	 * Püfft den Eingehenden Namen
	 * @param unknown_type $name
	 * @return boolean|Ambiguous
	 */
	public static function checkName($name){
	
		if(!is_string($name))
			return NULL;
		if(count($name) > self::MAX_NAME_LENGTH)
			return NULL;
	
		return $name;
	}
	/**
	 * Püfft den Eingehenden Valuewet
	 * @param unknown_type $name
	 * @return boolean|Ambiguous
	 */
	public static function checkDescription($value){
	
		if(!is_string($value))
			return NULL;
		if(count($value) > self::MAX_DESC_LENGTH)
			return NULL;
	
		return $value;
	}
	
	
	/**
	 * Erstellt ein neues Rootelement
	 * Achtung nur zur erstmaligen Inizialisierung aufrufen sonst kann es zu problemen führen
	 * @param string $rootKey ein eindeutiger RootKey mit max 3 Zeichen
	 * @param string $name
	 * @param string $value
	 * @return array Der erstellten Daten
	 */
	public function setRoot($accessGuId, $name,$desc = NULL){
	
		$db = $this->getAdapter();
	
		// Suchen eines elementes mit einen keynamen
		$select = $db->select();
		$select->from($this->getTableName());
		$elements = $db->fetchAll($select);
		
		if(count($elements) > 0)
			throw new Exception("Ein Rootelement ist schon vorhanden",E_ERROR);
		
		$sysId = mt_rand(self::MIN_SYSID, self::MAX_SYSID);
	
		$data = array();
		$data['sysid'] = $sysId;
		$data['lft'] = 1;
		$data['rgt'] = 2;
		$data['edata'] = DBTable::DateTime();
		$data['vdata'] = DBTable::DateTime();
		$data['usercreat'] = $accessGuId;
		$data['useredit'] = $accessGuId;
		$data['ort_name'] = $name;
		$data['text'] = $desc;

	
		$db->insert($this->getTableName(), $data);	
		return $data;
	
	}
	
	/**
	 * Giebt eine noch nicht vorhandene SysId innerhalb eines rootKeys zurück
	 * @return integer|NULL Die SysId wenn NULL dann kann es ein fehler sein und der counter ist mehr als 10 mal durchlaufen
	 */
	public function getNewSysId(){
		$db = $this->getAdapter();
	
		$mtRand = NULL;
		$whileCounter = 0;
	
		while ($whileCounter < self::MAX_WHILE_SYSID){
	
			$mtRandTest = mt_rand(self::MIN_SYSID, self::MAX_SYSID);
	
			$select = $this->select();
				
			$select->where("sysid = ?",$mtRandTest);
	
			$elemRow = $this->fetchRow($select);
	
			// prüfen auf wurde nicht gefunden
			if($elemRow === NULL){
				$mtRand = $mtRandTest;
				break 1;
			}
	
			$whileCounter++;
	
		}
	
		return $mtRand;
	
	}
	
	
	/**
	 * Erstellt ein neues Element
	 *
	 * Wenn ein Element übergeben wird dann wird das neue genau darunder angelegt
	 * Wenn ein Knoten übergeben wird dann wird es am ende innerhalb des Knotens angelegt
	 * @param integer $lft
	 * @param integer $rgt
	 * @param integer $sysId
	 * @param string $name
	 * @param string $text
	 * @return array|NULL Die eingeschrieben Daten mit der Id. Bei NULL konnte es nicht eingeschrieben werden warscheinlich wurde ein Knoten übergeben
	 */
	public function addElement($accessGuId,$lft,$rgt,$sysId,$name,$text){
	
	
		$db = $this->getAdapter();
		$data = NULL;
		
		
		if($lft+1 == $rgt ){
			// ist element
			$db->query("UPDATE ".$this->getTableName()." SET rgt = rgt+2 WHERE rgt > ".$rgt);
			$db->query("UPDATE ".$this->getTableName()." SET lft = lft+2 WHERE lft > ".$rgt);
	
			$data = array();
			$data["sysid"] = $sysId;
			$data["lft"] = $rgt+1;
			$data["rgt"] = $rgt+2;
			$data['edata'] = DBTable::DateTime();
			$data['vdata'] = DBTable::DateTime();
			$data['usercreat'] = $accessGuId;
			$data['useredit'] = $accessGuId;
			$data["ort_name"] = $name;
			$data["text"] = $text;
	
			$db->insert($this->getTableName(), $data);
	
		}else {
			// ist Knoten
			$db->query("UPDATE ".$this->getTableName()." SET rgt = rgt+2 WHERE rgt >=".$rgt);
			$db->query("UPDATE ".$this->getTableName()." SET lft = lft+2 WHERE lft > ".$rgt);
			$data = array();
			$data["sysid"] = $sysId;
			$data["lft"] = $rgt;
			$data["rgt"] = $rgt+1;
			$data['edata'] = DBTable::DateTime();
			$data['vdata'] = DBTable::DateTime();
			$data['usercreat'] = $accessGuId;
			$data['useredit'] = $accessGuId;
			$data["ort_name"] = $name;
			$data["text"] = $text;
	
			$db->insert($this->getTableName(), $data);		
		}
		return $data;
	}
	
	/**
	 * Erstellt aus den Übergebenen Elemen einen Knoten und erstellt darunder ein neues Element
	 *
	 * @param string $accessGuId
	 * @param integer $lft
	 * @param integer $rgt
	 * @param integer $sysId
	 * @param string $name
	 * @param string $text
	 * @return array|NULL Die eingeschrieben Daten mit der Id. Bei NULL konnte es nicht eingeschrieben werden warscheinlich wurde ein Knoten übergeben
	 */
	public function addKnotElement($accessGuId,$lft,$rgt,$sysId,$name,$text){
		$db = $this->getAdapter();
		$data = NULL;
		if($lft+1 == $rgt ){
			
			$db->query("UPDATE ".$this->getTableName()." SET rgt = rgt+2 WHERE rgt >=".$rgt);
			$db->query("UPDATE ".$this->getTableName()." SET lft = lft+2 WHERE lft > ".$rgt);
			$data = array();
			$data["sysid"] = $sysId;
			$data["lft"] = $rgt;
			$data["rgt"] = $rgt+1;
			$data['edata'] = DBTable::DateTime();
			$data['vdata'] = DBTable::DateTime();
			$data['usercreat'] = $accessGuId;
			$data['useredit'] = $accessGuId;
			$data["ort_name"] = $name;
			$data["text"] = $text;
	
			$db->insert($this->getTableName(), $data);

		}
		return $data;
	}
	
}

?>