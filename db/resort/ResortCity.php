<?php
require_once 'citro/DBTable.php';
/**
 * class resourt_orte
 *
 * Description for class resourt_orte
 *
 * @author:
*/
class ResortCity extends DBTable {
	
	protected $_name = 'resort_city';
	
	const SP_ID = "id";
	
	const SP_DATA_CREATE = "edata";
	const SP_DATA_EDIT = "vdata";

	
	const SP_PLZ = "plz";
	const SP_ORT_NAME = "ort";
	const SP_TEXT = "text";
	const SP_IN_MENUE = "in_menu";
	
	const SP_MAP_X = "map_lat";
	const SP_MAP_Y = "map_lng";
	const SP_MAP_ZOOM = "map_zoom";

	
	const MIN_SYSID = 100000;
	const MAX_SYSID = 999999;
	const MAX_WHILE_SYSID = 10;
	
	const MAX_NAME_LENGTH = 45;
	const MAX_DESC_LENGTH = 65000;
	
	

}

?>