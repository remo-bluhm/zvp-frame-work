<?php

/**
 * class zvp_orte
 *
 * Description for class zvp_orte
 *
 * @author:
*/
class zvp_orte extends DBTable {
	
	protected $_TableName = "orte";
	const SP_ID = "id";
	
	const SP_E_DATA = "edatum";
	const SP_V_DATA = "vdatum";
	const SP_USER_CREAT = "usercreat";
	const SP_USER_EDIT = "useredit";
	
	const SP_PLZ = "plz";
	const SP_ORT = "ort";
	const SP_IS_KURTAXE = "is_kurtaxt";
	const SP_GMAP_X = "gmap_x";
	const SP_GMAP_Y = "gmap_y";
	const SP_GMAP_ZOOM = "gmap_x_zoom";
	
	const SP_TEXT = "text";
	
	const SP_IN_MENUE = "in_menue";
	
	// public function newOrt($Ort, $PLZ ){
	
	// $OrtId = $this->OrtExist($Ort);
	// if($OrtId != false){
	
	// return $OrtId;
	
	// }
	// $data = array(
	
	// zvp_orte::SP_E_DATA => $this->getDateTime(),
	// zvp_orte::SP_V_DATA => $this->getDateTime(),
	
	// zvp_orte::SP_USER_CREAT => VAR_USER_ID,
	// zvp_orte::SP_USER_EDIT => VAR_USER_ID,
	
	// zvp_orte::SP_ORT => $Ort,
	// zvp_orte::SP_PLZ => $PLZ,
	
	// );
	
	// $id = 0;
	// try{
	// $this->_DBCon->insert($this->getDBTableName(), $data);
	// $id = $this->_DBCon->lastInsertId();
	// }catch(exception $insertEx){
	// echo $insertEx;
	// }
	
	// return $id;
	
	// }
	
	// public function getAllOrte(){
	
	// $select = $this->_db->select();
	
	// // Hinzuf�gen einer FROM Bedingung
	// $select->FROM( $this->_name );
	// //$select->where( sys_ws_rechte::SP_GROUPID." = ?",$GroupId,'INTEGER');
	
	// //$select->where( "id = 2");
	
	// //$select->where( $this->_SpVisible." = ?",$visible);
	// //$select->where( $this->_SPDelete." = ?",$delete);
	// //echo $select->__toString();
	// $back = $select->query();
	
	// $All = $back->fetchAll();
	
	// //print_r($send);
	// return $All;
	
	// }
	
	// public function OrtExist($Ort){
	
	// $select = $this->_DBCon->select();
	// $select->FROM( $this->getDBTableName(), array(zvp_orte::SP_ID,
// zvp_orte::SP_ORT) );
	
	// $select->where( zvp_orte::SP_ORT." = ?",$Ort,"STRING");
	// //echo $select->__toString();
	
	// $back = $select->query();
	
	// $Zeile = $back->fetch();
	
	// if($Zeile != null) {
	
	// return $Zeile[zvp_orte::SP_ID];
	// }
	
	// return false;
	
	// }
}

?>