<?php
require_once ('citro/DBTable.php');

class sys_trash extends DBTable {
	
	protected $_TableName = "sys_trash";
	
	protected $_primary = 'id';
	
	const SP_ID = "id";
	const SP_DATA_CREATE = "delete_date";
	const SP_USER_CREATE = "user_create";
	const SP_GROUPIDS = "groupids";
	const SP_TYPE = "type";
	const SP_ADRESS = "adress";
	const SP_DATA = "data";
	const SP_NAME = "name";

}

?>