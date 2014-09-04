<?php

/**
 * class zvp_zimmer
 *
 * Description for class zvp_zimmer
 *
 * @author:
*/
class amenities  extends DBTable {
	
	protected $_TableName = "amenities";
	
	const MAX_NAME_LENGTH = 45;
	const MAX_VALUE_LENGTH = 65000;
	const MAX_ROOTKEY_LENGTH = 3;
	const MIN_SYSID = 100000;
	const MAX_SYSID = 999999;
	const MAX_WHILE_SYSID = 10;
	
	
	

	
	
	/**
	 * Erstellt ein neues Rootelement
	 * @param string $rootKey ein eindeutiger RootKey mit max 3 Zeichen
	 * @param string $name
	 * @param string $value
	 * @return array Der erstellten Daten
	 */
	public function setRoot($rootKey,$name,$value = NULL){
		
		$db = $this->getAdapter();
		
		$sysId = mt_rand(self::MIN_SYSID, self::MAX_SYSID);

		$data = array();
		$data['sys_id'] = $sysId;
		$data['lft'] = 1;
		$data['rgt'] = 2;
		$data['name'] = $name;
		$data['value'] = $value;
		$data['rootkey'] = $rootKey;
				
		$db->insert($this->getTableName(), $data);
		$insertId = $db->lastInsertId($this->getTableName());

		$data['id'] = $insertId; 
		
		return $data;
		
	}

	
	
	/**
	 * Giebt eine noch nicht vorhandene SysId innerhalb eines rootKeys zurück
	 * @param string $rootKey
	 * @return integer|NULL Die SysId wenn NULL dann kann es ein fehler sein und der counter ist mehr als 10 mal durchlaufen
	 */
	public function getNewSysId($rootKey){
		$db = $this->getAdapter();
		
		$mtRand = NULL;
		$whileCounter = 0;
		
		while ($whileCounter < self::MAX_WHILE_SYSID){
				
			$mtRandTest = mt_rand(self::MIN_SYSID, self::MAX_SYSID);
				
			$select = $this->select();
			
			$select->where("sys_id = ?",$mtRandTest);
			
			$select->where("rootkey = ?",$rootKey);

			//$select->where("deleted = ?",0);
			
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
	 * @param unknown_type $lft
	 * @param unknown_type $rgt
	 * @param unknown_type $sysId
	 * @param unknown_type $rootKey
	 * @param unknown_type $name
	 * @param unknown_type $text
	 * @return array|NULL Die eingeschrieben Daten mit der Id. Bei NULL konnte es nicht eingeschrieben werden warscheinlich wurde ein Knoten übergeben
	 */
	public function addElement($lft,$rgt,$sysId,$rootKey,$name,$text){
		
	
			$db = $this->getAdapter();
			$dataBack = NULL;
			if($lft+1 == $rgt ){
				// ist element
				$db->query("UPDATE ".$this->getTableName()." SET rgt = rgt+2 WHERE rgt > ".$rgt." AND rootkey = '".$rootKey."'");
				$db->query("UPDATE ".$this->getTableName()." SET lft = lft+2 WHERE lft > ".$rgt." AND rootkey = '".$rootKey."'");
				
				$data = array();
				$data["sys_id"] = $sysId;
				$data["lft"] = $rgt+1;
				$data["rgt"] = $rgt+2;
				$data["rootkey"] = $rootKey;
				$data["name"] = $name;
				$data["value"] = $text;
				
				$insertId = $db->insert($this->getTableName(), $data);
				
				$data['id'] = $insertId;
				$dataBack = $data;
			}else {
				// ist Knoten
				$db->query("UPDATE ".$this->getTableName()." SET rgt = rgt+2 WHERE rgt >=".$rgt." AND rootkey = '".$rootKey."'");
				$db->query("UPDATE ".$this->getTableName()." SET lft = lft+2 WHERE lft > ".$rgt." AND rootkey = '".$rootKey."'");
				$data = array();
				$data["sys_id"] = $sysId;
				$data["lft"] = $rgt;
				$data["rgt"] = $rgt+1;
				$data["rootkey"] = $rootKey;
				$data["name"] = $name;
				$data["value"] = $text;
				
				$insertId = $db->insert($this->getTableName(), $data);
				
				$data['id'] = $insertId;
				$dataBack = $data;
			}
			return $dataBack;
	}
	
	/**
	 * Erstellt aus den Übergebenen Elemen einen Knoten und erstellt darunder ein neues Element
	 * 
	 * @param unknown_type $lft
	 * @param unknown_type $rgt
	 * @param unknown_type $sysId
	 * @param unknown_type $rootKey
	 * @param unknown_type $name
	 * @param unknown_type $text
	 * @return array|NULL Die eingeschrieben Daten mit der Id. Bei NULL konnte es nicht eingeschrieben werden warscheinlich wurde ein Knoten übergeben
	 */
	public function addKnotElement($lft,$rgt,$sysId,$rootKey,$name,$text){
		$db = $this->getAdapter();
		$dataBack = NULL;
		if($lft+1 == $rgt ){
			
				$db->query("UPDATE ".$this->getTableName()." SET rgt = rgt+2 WHERE rgt >=".$rgt." AND rootkey = '".$rootKey."'");
				$db->query("UPDATE ".$this->getTableName()." SET lft = lft+2 WHERE lft > ".$rgt." AND rootkey = '".$rootKey."'");
				$data = array();
				$data["sys_id"] = $sysId;
				$data["lft"] = $rgt;
				$data["rgt"] = $rgt+1;
				$data["rootkey"] = $rootKey;
				$data["name"] = $name;
				$data["value"] = $text;
				
				$insertId = $db->insert($this->getTableName(), $data);
				
				$data['id'] = $insertId;
				$dataBack = $data;
		}
		return $dataBack;
	}

	
	
	
	
	
	
	
	
	/**
	 * Bearbeitet ein bestimmtes Element
	 * @param unknown_type $elementId
	 * @param unknown_type $name
	 * @param unknown_type $value
	 */
	public function editElement($rootKey,$sysId, $name = NULL, $value = NULL){
	
		$data = array();
		
		if($name !== NULL)
			$data['name'] = $name;
		
		if($value !== NULL)
			$data['value'] = $value;
		

		$where = array();
		$where[] =$this->getAdapter()->quoteInto("sys_id = ? ", $sysId);
		$where[] =$this->getAdapter()->quoteInto("rootkey = ? ",$rootKey);
		
		$this->update($data, $where);

	}
	
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
	public static function checkValue($value){
	
		if(!is_string($value))
			return NULL;
		if(count($value) > self::MAX_VALUE_LENGTH)
			return NULL;
	
		return $value;
	}
	/**
	 * Püfft den Eingehenden Valuewet
	 * @param unknown_type $name
	 * @return boolean|Ambiguous
	 */
	public static function checkRootKey($value){
	
		if(!is_string($value))
			return NULL;
	
		if(count($value) > self::MAX_ROOTKEY_LENGTH)
			return NULL;

		if(strlen($value) < self::MAX_ROOTKEY_LENGTH)
			return NULL;
		
	
		$value = strtoupper ($value);
		return $value;
	}
	
	
	/**
	 * Extrahiert aus einer ElementId [APP-213652] den Rootkey
	 * @param string $eId
	 * @return string 
	 */
	public static function getRootKey($eId){
		$idA = explode("-", $eId);
		return $idA[0];
	}
	/**
	 * Extrahiert aus einer ElementId [APP-213652] die SysId
	 * @param string $eId
	 * @return string
	 */
	public static function getSysId($eId){
		$idA = explode("-", $eId);
		return $idA[1];
	}
	
	/**
	 * Hollt das gesuchte Element anhand seiner id in der angefragten Spalte
	 * @param integer $id Die Id die gesucht werden soll
	 * @param string $sp Die spalte in der gesucht werden soll [ID,LFT,RGT,SYSID]
	 * @return array|bool FALSE wenn die zeile nicht gefunden wurde.
	 */
	public function getElement($rootKey,$id,$sp = "ID"){
		
		$db = $this->getAdapter();
		
		$sel = $this->select();

		switch ($sp){
			case "ID":
				$sel->where("rootkey = ?",$rootKey);
				$sel->where("id=?",$id);
			break;
			case "LFT":
				$sel->where("rootkey = ?",$rootKey);
				$sel->where("lft=?",$id);
			break;
			case "RGT":
				$sel->where("rootkey = ?",$rootKey);
				$sel->where("rgt=?",$id);
			break;
			case "SYSID":
				$sel->where("rootkey = ?",$rootKey);
				$sel->where("sys_id = ?",$id);
				break;
			default:
				$sel->where("rootkey = ?",$rootKey);
				$sel->where("id=?",$id);
			break;
		}
		//echo  $sel->__toString();
		$tabRow = $db->fetchAll($sel);
	
		if(count($tabRow) !== 1)
			return NULL;
		
		
		if( !is_array($tabRow[0]) )
			return NULL;

		return $tabRow[0];
	}
	


	public function deleteRootElement($rootKey){
		$db = $this->getAdapter();
		
		$delWhereA = array();
		$delWhereA[] = $db->quoteInto("rootkey=?", $rootKey);
		
		// Lösche Alle Elemente 
		$zeilen = $db->delete($this->getTableName(), $delWhereA);
		return $zeilen;
	}

	
	
	/**
	 * Löscht ein Element 
	 * Wenn ein Knoten übergeben wird dann löscht er das Element und seine darunderliegenden Elemente
	 * werden einen level höher eingeschrieben
	 * @param integer $fromLft
	 * @param integer $formRgt
	 * @param string $rootKey
	 */
	public function deleteElement($fromLft,$formRgt,$rootKey){
		
		$db = $this->getAdapter();

		$delWhereA = array();
		$delWhereA[] = $db->quoteInto("lft=?", $fromLft);
		$delWhereA[] = $db->quoteInto("rootkey=?", $rootKey);
		
		// Lösche Element
		$db->delete($this->getTableName(), $delWhereA);
		
		// Umschreiben der Darunderliegenden
		if($$fromLft+1 == $formRgt){
			// Blatt
			$db->query("UPDATE '".$this->getTableName()."' SET lft=lft-2 WHERE lft > ".$formRgt." AND rootkey = '".$rootKey."'");
			$db->query("UPDATE '".$this->getTableName()."' SET rgt=rgt-2 WHERE rgt > ".$formRgt." AND rootkey = '".$rootKey."'");
			
		}else {
			// delete Knoten und verschiebe seine unterelemente in die obere ebene
			$db->query("UPDATE `".$this->getTableName()."` SET lft=lft-1, rgt=rgt-1 WHERE lft BETWEEN ".$fromLft." AND ".$formRgt." AND rootkey = '".$rootKey."'" );
			
			$db->query("UPDATE ".$this->getTableName()." SET lft=lft-2  WHERE lft > ".$formRgt." AND rootkey = '".$rootKey."'");
			$db->query("UPDATE ".$this->getTableName()." SET rgt=rgt-2  WHERE rgt > ".$formRgt." AND rootkey = '".$rootKey."'");

		}

	}
	
	/**
	 * Löscht einen Knoten mit all seinen unterelementen
	 * @param integer $fromLft
	 * @param integer $fromRgt
	 * @param string $rootKey
	 */
	public function deleteElementWithUnderElement($fromLft,$fromRgt,$rootKey){
	
		$db = $this->getAdapter();
		
		if($$fromLft+1 < $fromRgt){
			// Lösche Element
			$move = floor(($fromRgt-$fromLft)/2);
			$move = 2*(1+$move);
			$moveS = (string)$move;
		
			$delWhereA = array();
			$delWhereA[] = $db->quoteInto("rootkey=?", $rootKey);
			$delWhereA[] = "lft BETWEEN ".$fromLft." AND ".$fromRgt;
			
		
			$db->delete($this->getTableName(), $delWhereA);
			$db->query("UPDATE ".$this->getTableName()." SET lft = lft-".$move." WHERE lft > ".$fromRgt." AND rootkey = '".$rootKey."'");
			$db->query("UPDATE ".$this->getTableName()." SET rgt = rgt-".$move." WHERE rgt > ".$fromRgt." AND rootkey = '".$rootKey."'");	
		}
	
	}
	
	
// 	/**
// 	 * Löscht einen Knoten mit all seinen unterelementen
// 	 * @param integer $fromLft
// 	 * @param integer $fromRgt
// 	 * @param string $rootKey
// 	 */
// 	public function deleteElementUnderElement($fromLft,$fromRgt,$rootKey){
	
// 		$db = $this->getAdapter();
	
// 		if($$fromLft+1 < $fromRgt){
// 			// Lösche Element
// 			$move = floor(($fromRgt-$fromLft)/2);
// 			$move = 2*(1+$move);
// 			$moveS = (string)$move;
	
// 			$delWhereA = array();
// 			$delWhereA[] = $db->quoteInto("rootkey=?", $rootKey);
// 			$delWhereA[] = "lft BETWEEN ".$fromLft." AND ".$fromRgt;
				
	
// 			$db->update($this->getTableName(), $delWhereA);
// 			$db->query("UPDATE ".$this->getTableName()." SET lft = lft-".$move." WHERE lft > ".$fromRgt." AND rootkey = '".$rootKey."'");
// 			$db->query("UPDATE ".$this->getTableName()." SET rgt = rgt-".$move." WHERE rgt > ".$fromRgt." AND rootkey = '".$rootKey."'");
// 		}
	
// 	}
	
	
	public function getNestedTree($rootKey = 0){
	
		$tab = $this->getTableName();
		
		$select = "SELECT  CONCAT( n.rootkey, '-', n.sys_id ) AS element_id, n.lft,n.rgt,n.name,n.value,n.rootkey,n.sys_id,  COUNT(*)-1 AS level 
		FROM `$tab` AS n , `$tab` AS p
   		WHERE n.rootkey='$rootKey' AND p.rootkey='$rootKey' AND n.lft BETWEEN p.lft AND p.rgt
		GROUP BY n.lft
		ORDER BY n.lft";
		
		$db = $this->getAdapter();
		$elemente = $db->fetchAll($select);
		return $elemente;
		
	
	}
	
	public function getDBTree($fromId){
		$select =" SELECT o.*,
		COUNT(p.id)-1 AS level
		FROM ".$this->getTableName()." AS n,
		".$this->getTableName()." AS p,
		".$this->getTableName()." AS o
		WHERE o.lft BETWEEN p.lft AND p.rgt
		AND o.lft BETWEEN n.lft AND n.rgt
		AND n.id = ".$fromId."
		GROUP BY o.lft
		ORDER BY o.lft;";
	
		$db = $this->getAdapter();
		$elemente = $db->fetchAll($select);
		return $elemente;
	}
	
// 	public function getNestedTreeOffspring($rootKey){
	
// 		$select = "SELECT n.*, 
// 			COUNT(*)-1 AS level,
// 			ROUND ((n.rgt - n.lft - 1) / 2) AS offspring 
// 		FROM `bt_amenities` AS n,	`bt_amenities` AS p
// 		WHERE n.rootkey='$rootKey' AND p.rootkey='$rootKey' AND n.lft BETWEEN p.lft AND p.rgt
// 		GROUP BY n.lft
// 		ORDER BY n.lft";
	
// 		$db = $this->getAdapter();
// 		$elemente = $db->fetchAll($select);
// 		return $elemente;
	
	
// 	}
	

	

	
	public function getChildTree($id,$root_id = 0){
	
		$select = "SELECT o.name,COUNT(p.id)-1 AS level
		FROM 	`bt_amenities_group` AS n,	
				`bt_amenities_group` AS p,
				`bt_amenities_group` AS o
		WHERE 	o.root_id=$root_id AND o.root_id=$root_id 
			AND o.lft BETWEEN p.lft AND p.rgt 
			AND o.lft BETWEEN n.lft AND n.rgt 
			AND n.id = $id
		GROUP BY o.lft
		ORDER BY o.lft";
	
		$db = $this->getAdapter();
		$elemente = $db->fetchAll($select);
		return $elemente;
	}
	public function getParentTree($id,$root_id = 0){
	
		$select = "SELECT p.name
		FROM `bt_amenities_group` AS n,	`bt_amenities_group` AS p
		WHERE n.root_id=$root_id AND p.root_id=$root_id
		AND n.lft BETWEEN p.lft AND p.rgt
		AND n.id = $id
		ORDER BY p.lft";
	
		$db = $this->getAdapter();
		$elemente = $db->fetchAll($select);
		return $elemente;
	}
	
	
// 	private function _getElementTree($elementId, $depth = null)
// 	{
// 		// @TODO: test -> if multiple elements with depth 1 are found -> error
// 		$db = $this->_db;
// 		$elementId = (int) $elementId;

	
// 		// Get main element left and right
// 		$select = $db
// 		->select()
// 		->from($this->getTableName(), array('lft','rgt'))
// 		->where('id' . ' = ?', $elementId);
	
// 		$stmt = $db->query($select);
// 		$element = $stmt->fetch();

// 		if($element === FALSE)
// 			throw new Exception("Das element mit der Id($elementId) konnte nicht gefunden werden",E_ERROR);
		
// 		// Get the tree
// 		$query = "
// 		SELECT
// 		node.id,
// 		node.name,
// 		node.lft,
// 		node.rgt,
// 		COUNT(parent.name) - 1 AS depth
// 		FROM 
// 		{$this->getTableName()} AS node,
// 		{$this->getTableName()} AS parent
// 		WHERE node.lft BETWEEN parent.lft AND parent.rgt
// 		AND   node.lft BETWEEN {$element['lft']} AND {$element['rgt']}
// 		GROUP BY  node.lft
// 		ORDER BY node.lft
// 		";
	
// 		$stmt = $this->_db->query($query);
// 		$nodes = $stmt->fetchAll();
	
// 		return $nodes;
// 	}
	
	public function toUp(){
		
	}
	public function tuDown(){
		
	}
	
	public function move($rootKey, $lft,$rgt,$refRgtOrLft)
	{
		

		
  		$db = $this->getAdapter();
			
		$l = $lft;
		$r = $rgt;
		$p = $refRgtOrLft;
		
			
		$query = "UPDATE  ".$this->getTableName()." SET
			    lft = lft + IF ($p > $r,
				        IF ($r < lft and lft < $p,  		$l - $r - 1,
				            IF ($l <= lft and lft < $r, 	$p - $r - 1, 0 )  ),
							        IF ($p <= lft and lft < $l,  		$r - $l + 1,
							            IF ($l <= lft and lft < $r, 	$p - $l, 0 ) )
			    ),
			    rgt = rgt + IF ($p > $r,
			        IF ($r < rgt and rgt < $p, 						$l - $r - 1,
			            IF ($l < rgt and rgt <= $r, 				$p - $r - 1, 0 ) ),
			        IF ($p <= rgt and rgt < $l, 					$r - $l + 1,
			            IF ($l < rgt and rgt <= $r, 				$p - $l, 0 ) )
				    )
				WHERE rootkey = '$rootKey' AND ( $r < $p OR $p < $l )   ;  ";
		
		$db->query($query);

	}
	
	
	
		
	/**
	 * Giebt die Anzahl der darunderliegenden Elemente Zurück
	 *
	 * @params $elementId|int ID of the element
	 *
	 * @return int
	 */
	public function numberOfDescendant($rgt,$lft)
	{
		$width = $rgt-$lft+1;
		$result = ($width - 2) / 2;
	
		return $result;
	}
	

}

?>