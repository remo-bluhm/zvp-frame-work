<?php
require_once 'citro/DBTable.php';
/**
 * class zvp_zimmer
 *
 * Description for class zvp_zimmer
 *
 * @author:
*/
class resort extends DBTable {
	
	protected $_TableName = "resort";
	const SP_ID = "id";
	
	const SP_DATA_CREATE = "edata";
	const SP_DATA_EDIT = "vdata";
	const SP_USER_CREAT = "usercreat";
	const SP_USER_EDIT = "useredit";
	
	const SP_VISIBIL = "visibil";
	const SP_DELETED = "deleted";
	
	const SP_ORT_ID = "resort_orte_id";
	const SP_STRASSE = "strasse";
	const SP_GMAPS_ID = "gmaps_id";
	const SP_KURZTEXT = "kurztext";


}

?>