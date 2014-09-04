<?php
require_once ('citro/DBTable.php');

/**
 * class sys_group
 *
 * Description for class sys_group
 *
 * @author :
 *        
 */
class sys_access_groups extends DBTable {
	
	protected $_primary = 'id';
	
	const SP_ID = "id";
	const SP_NAME = "name";
	
	const SP_LFT = "lft";
	const SP_RGT = "rgt";
	
	const SP_DATA_EDIT = "date_edit";
	const SP_DATA_CREATE = "date_create";
	
	const SP_ACCESS_CREATE = "access_owner";
	const SP_ACCESS_EDIT = "access_edit";

	
	const SP_VISIBIL = "visibil";
	const SP_TEXT = "text";
	


	
	/**
	 * Erstellt die Rootgruppe als Administratorgruppe
	 * @param string $name
	 * @param string $description
	 * @param integer $accessIdCreate
	 * @return integer
	 */
	public function setRoot($name,$description,$accessIdCreate){
		$db = $this->getAdapter();
		
		$data = array();
		$data[self::SP_NAME] = $name;
		
		$data[self::SP_LFT] = 1;
		$data[self::SP_RGT] = 2;
		
		$data[self::SP_DATA_EDIT] = self::getDateTime();
		$data[self::SP_DATA_CREATE] = self::getDateTime();
		
		$data[self::SP_ACCESS_CREATE] = $accessIdCreate;
		$data[self::SP_ACCESS_EDIT] = $accessIdCreate;
		
		$data[self::SP_VISIBIL] = 1;
		$data[self::SP_TEXT] = $description;
		
		$db->insert($this->getTableName(), $data);
		$insertId = $db->lastInsertId($this->getTableName());
		return $insertId;
		
// 		$data['id'] = $insertId;
// 		return $data;
	}
	
	
	public function visitAllow($id,$isAdmin = FALSE,$accessGuId){
		
		$groupSelect = $this->select ();
		$groupSelect->where ( self::SP_ID . "= ?", $id );
		$groupSelect->where ( self::SP_DELETE . " = ?", 0 );
		if (! $isAdmin) {
			$groupSelect->where ( self::SP_USER_CREATE . " = ?", $this->_MainUser->getId () );
		}
		
		/* @var $groups Zend_Db_Table_Rowset */
		$group = $this->fetchRow( $groupSelect );
		$group->offsetSet ( self::SP_VISIBIL, 1 );
		$group->save ();
	}
	
	
	public function visitDeny(){
		
	}
	
	/**
	 * Giebt die Gruppenhierachie einer gruppenid zurück
	 * @param integer $id Die GruppenId des aufrufenden Accesses
	 * @return array Die Gruppenhierachie die der Access unterligt Key "0" = Root
	 */
	public function getParent($id){
		$tab = $this->getTableName();
		$id = (int) $id;
		
 		$sel = $this->select();
 		$sel->from("sys_access_groups AS n",array());
 		$sel->from("sys_access_groups AS p");
 		
 		$sel->where("n.lft BETWEEN p.lft AND p.rgt ");
 		$sel->where("n.id = ?",$id);
 		$sel->order("n.lft");

		$allRow = $this->fetchAll($sel);
		return $allRow;

	}
	
	/**
	 * Giebt die Gruppenhierachie eines Gruppnnamens zurück
	 * @param integer $id Die GruppenId des aufrufenden Accesses
	 * @return array Die Gruppenhierachie die der Access unterligt Key "0" = Root
	 */
	public function getParentWithName($groupname){
		$tab = $this->getTableName();
	
		$sel = $this->select();
		$sel->from("sys_access_groups AS n",array());
		$sel->from("sys_access_groups AS p");
			
		$sel->where("n.lft BETWEEN p.lft AND p.rgt ");
		
		$sel->where("n.name = ?",$groupname);
		$sel->order("n.lft");
	
		$allRow = $this->fetchAll($sel);
		return $allRow;
	
	}
	
	
// 	/**
// 	 * Giebt Alle Kindsgruppen die unter der Id Unterliegen 
// 	 * @param integer $id Die Elterngruppen Id
// 	 * @param integer $owner Der Besitzer
// 	 * @return array
// 	 */
// 	public function getChild($id,$owner = NULL){
	
// 		if(!is_int($id))
// 			return array();
		
// 		$db = $this->getAdapter();
		
// 		$ownerSel = "";
// 		if(is_numeric($owner))
// 			$ownerSel = $db->quoteInto("and t1.access_owner = ?", $owner);
		
// 		$select = "
// 		select 	t1.".self::SP_NAME.", 
// 				t1.id as sys_id, 
// 				t1.".self::SP_DATA_CREATE." as created, 
// 				t1.".self::SP_DATA_EDIT." as edit, 
// 				t1.".self::SP_VISIBIL.", 
// 				t1.".self::SP_TEXT.",
// 				ROUND ((t1.rgt - t1.lft - 1) / 2) AS offspring
// 		from sys_access_groups AS t1 
// 		join sys_access_groups AS t2 
// 		left join sys_access_groups AS t3 on t3.lft < t1.lft
// 			and t3.rgt > t1.rgt
// 			and t3.lft > t2.lft
// 			and t3.rgt < t2.rgt
// 		where t2.id = ".$id."
// 			and t3.id is null
// 			and t1.lft > t2.lft
// 			and t1.rgt < t2.rgt
// 			".$ownerSel."
// 		order by t1.lft";
		
		
// 		$elemente = $db->fetchAll($select);
		
// 		return $elemente;

// 	}
	
	public function getRoot(){
		
		
		$groupSelect = $this->select ();
		$groupSelect->where ( sys_access_groups::SP_LFT . " = 1" );
		$groupRow = $this->fetchRow ( $groupSelect );
		return $groupRow;
	}
	
	/**
	 * Giebt Alle Kindsgruppen die unter der Id Unterliegen
	 * @param string $id Die Elterngruppen Id
	 * @param integer $owner Der Besitzer
	 * @return array
	 */
	public function getList($groupName,$owner = NULL){
	
		if(!is_string($groupName))
			return array();
	
		$db = $this->getAdapter();
	
		$ownerSel = "";
		if(is_numeric($owner))
			$ownerSel = $db->quoteInto("and t1.access_owner = ?", $owner);
	
		$select = "
		select 	t1.".self::SP_NAME.",
		t1.".self::SP_DATA_CREATE." as created,
		t1.".self::SP_DATA_EDIT." as edit,
		t1.".self::SP_VISIBIL.",
		t1.".self::SP_TEXT.",
		ROUND ((t1.rgt - t1.lft - 1) / 2) AS offspring
		from sys_access_groups AS t1
		join sys_access_groups AS t2
		left join sys_access_groups AS t3 on t3.lft < t1.lft
		and t3.rgt > t1.rgt
		and t3.lft > t2.lft
		and t3.rgt < t2.rgt
		where t2.name = '".$groupName."'
		and t3.id is null
		and t1.lft > t2.lft
		and t1.rgt < t2.rgt
		".$ownerSel."
		order by t1.lft";
	
	
		$elemente = $db->fetchAll($select);
	
		return $elemente;
	
	}
	
	public function getTree($fromId){
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
	
	public function getTreeName($fromId){
		$select =" SELECT o.name
		FROM ".$this->getTableName()." AS n,
		".$this->getTableName()." AS p,
		".$this->getTableName()." AS o
		WHERE o.lft BETWEEN p.lft AND p.rgt
		AND o.lft BETWEEN n.lft AND n.rgt
		AND n.id = ".$fromId."
		GROUP BY o.lft
		ORDER BY o.lft;";
	
		$db = $this->getAdapter();
		$elemente = $db->fetchCol($select);
		return $elemente;
	}
	
	public function getGroupAsId($id){
		$groupSelect = $this->select ();
		$groupSelect->where ( sys_access_groups::SP_ID . " = ?", $id );
		$groupRow = $this->fetchRow ( $groupSelect );
		return $groupRow;
	}
	
	

	
	/**
	 * Giebt eine Gruppe zurück auf grund Ihres Namens
	 * @param string $name
	 * @return Ambigous <Zend_Db_Table_Row_Abstract, NULL, unknown>
	 */
	public function getGroupAsName($name){
		$groupSelect = $this->select ();
		$groupSelect->where ( sys_access_groups::SP_NAME . " = ?", $name );
		$groupRow = $this->fetchRow ( $groupSelect );
		return $groupRow;
	}
	
	
	
	
	/**
	 * @param integer $lft
	 * @param integer $rgt
	 * @param integer $owner_id
	 * @param string $name
	 * @param string $text
	 * @param integer $visibil
	 * @return Ambigous <NULL, multitype:number unknown >
	 */
	public function newMainGroup($lft,$rgt,$owner_id,$name,$text,$visibil){
		$db = $this->getAdapter();
		$db->beginTransaction();
		try{
			
			$dataBack = NULL;
			if($lft+1 == $rgt ){
				// ist element
				$db->query("UPDATE sys_access_groups SET rgt = rgt+2 WHERE rgt > ".$rgt);
				$db->query("UPDATE sys_access_groups SET lft = lft+2 WHERE lft > ".$rgt);
			
				$data = array();
		
				$data["lft"] = $rgt+1;
				$data["rgt"] = $rgt+2;
				$data["date_create"] = self::getDateTime();
				$data["date_edit"] = self::getDateTime();
				$data["access_owner"] = $owner_id;
				$data["access_edit"] = $owner_id;
				$data["name"] = $name;
				$data["text"] = $text;
				$data["visibil"] = $visibil;
			
				$insertId = $db->insert("sys_access_groups", $data);
			
				$data['id'] = $insertId;
				$dataBack = $data;
			}else {
				// ist Knoten
				$db->query("UPDATE sys_access_groups SET rgt = rgt+2 WHERE rgt >=".$rgt);
				$db->query("UPDATE sys_access_groups SET lft = lft+2 WHERE lft > ".$rgt);
				$data = array();
	
				$data["lft"] = $rgt;
				$data["rgt"] = $rgt+1;
				$data["date_create"] = self::getDateTime();
				$data["date_edit"] = self::getDateTime();
				$data["access_owner"] = $owner_id;
				$data["access_edit"] = $owner_id;
				$data["name"] = $name;
				$data["text"] = $text;
				$data["visibil"] = $visibil;
			
				$insertId = $db->insert("sys_access_groups", $data);
			
				$data['id'] = $insertId;
				$dataBack = $data;
			}
			$db->commit();
			return $dataBack;
		}catch (Exception $eTrans){
			$db->rollBack();
			return FALSE;
		}
		
	}
	
	public function updateMainGroup($nameOld, $nameNew = NULL, $text=NULL){
		$data = array();
		if($nameNew !== NULL) $data[self::SP_NAME] = $nameNew;
		if($text !== NULL) $data[self::SP_TEXT] = $text;
		$data[self::SP_DATA_EDIT] = self::DateTime();
	
		$numberRowsUpdate = $this->update($data, $this->_db->quoteInto("name = ?", $nameOld) );

		return $numberRowsUpdate;
	}

	

}
?>