<?php

/**
 * class zvp_orte
 *
 * Description for class zvp_orte
 *
 * @author:
*/
class zvp_zim_art extends DBTable {
	
	protected $_TableName = "zimmer_art";
	const SP_ID = "id";
	
	const SP_E_DATA = "edatum";
	const SP_V_DATA = "vdatum";
	const SP_USER_CREAT = "usercreat";
	const SP_USER_EDIT = "useredit";
	
	const SP_SYS_NAME = "sys_name";
	const SP_MENUE_NAME = "menu_name";
	const SP_KURZTEXT = "kurztext";
	const SP_IN_MENUE = "in_menu";
	
	// public function newArt($SysName,$MenuName = "", $KurtzText= "" ,$inMenue
// = false ){
	
	// $ArtId = $this->ArtExist($SysName);
	// if($ArtId != false){
	
	// return $ArtId;
	
	// }
	// if($inMenue == false){
	// $inMenue = 0;
	// }else{
	// $inMenue = 1;
	// }
	
	// $data = array(
	
	// zvp_zim_art::SP_E_DATA => $this->getDateTime(),
	// zvp_zim_art::SP_V_DATA => $this->getDateTime(),
	
	// zvp_zim_art::SP_USER_CREAT => VAR_USER_ID,
	// zvp_zim_art::SP_USER_EDIT => VAR_USER_ID,
	
	// zvp_zim_art::SP_SYS_NAME => $SysName,
	// zvp_zim_art::SP_MENUE_NAME => $MenuName,
	// zvp_zim_art::SP_KURZTEXT => $KurtzText,
	// zvp_zim_art::SP_IN_MENUE => $inMenue,
	
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
	
	// $select = $this->_DBCon->select();
	
	// // Hinzuf�gen einer FROM Bedingung
	// $select->FROM( $this->getDBTableName() );
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
	
	// public function ArtExist($Art){
	
	// $select = $this->_DBCon->select();
	// $select->FROM( $this->getDBTableName(), array(zvp_zim_art::SP_ID,
// zvp_zim_art::SP_SYS_NAME) );
	
	// $select->where( zvp_zim_art::SP_SYS_NAME." = ?",$Art,"STRING");
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