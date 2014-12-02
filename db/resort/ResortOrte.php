<?php
require_once 'citro/DBTable.php';
/**
 * class resourt_orte
 *
 * Description for class resourt_orte
 *
 * @author:
*/
class ResortOrte extends DBTable {
	
	protected $_name = 'contacts';
	
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
	
	

}

?>