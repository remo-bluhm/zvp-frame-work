<?php
require_once 'citro/DBTable.php';
/**
 * class zvp_zimmer
 *
 * Description for class zvp_zimmer
 *
 * @author:
*/
class Resort extends DBTable {
	
	protected $_name = 'resort';

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


	private $_insertData = array();
	
	public function clearData(){
		$this->_insertData = array();
	}
	
	
	public function setName($value){
		$result = self::testType($value);
		if($result !== NULL)$this->_insertData[self::SP_TYPE] = $result;
		return $result;
	}
	
	public static function testName($value){
		if(is_string($value) && strlen($value) < 200 &&  strlen($value) > 2)return $value;
		return NULL;
	}
	
	
}

?>