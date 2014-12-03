<?php
require_once 'citro/DBTable.php';
/**
 * class resourt_orte
 *
 * Description for class resourt_orte
 *
 * @author:
*/
class ResortRegion extends DBTable {
	
	protected $_name = 'resort_region';
	

	public static function testNameUid($value){
		$value = (string)$value;
		if(strlen($value) > 150 &&  strlen($value) > 3) return NULL;
		return $value;
	}
	
	public static function testName($value){
		$value = (string)$value;
		if(strlen($value) > 150 &&  strlen($value) > 3) return NULL;
		return $value;
	}
	

	public function insertOver($uidNameExist,$uidName, $visitName, $data = array()){
		
	}
	
	public function insertUnder($uidNameExist,$uidName, $visitName, $data = array()){
		
	}
	

	
}

?>