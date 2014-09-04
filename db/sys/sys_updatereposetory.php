<?php
require_once ('citro/DBTable.php');

class sys_updatereposetory extends DBTable {
	
	protected $_TableName = "sys_updatereposetory";
	
	protected $_primary = 'id';
	
	const SP_ID = "id";
	const SP_DATA_CREATE = "date";
	const SP_USER_CREATE = "user_id";
	const SP_KEY = "key";
	const SP_TYPE = "type";
	const SP_DATA = "data";

}

?>