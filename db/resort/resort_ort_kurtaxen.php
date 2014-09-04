<?php
require_once 'citro/DBTable.php';
/**
 * class objekt_orte
 *
 * Description for class zvp_zimmer
 *
 * @author:
*/
class resort_orte extends DBTable {
	
	protected $_TableName = "resort_orte";
	const SP_ID = "orte_id";
	
	const SP_BEZEICHNUNG = "bezeichnung";
	const SP_WERT = "wert";
	const SP_TEXT = "text";


}

?>