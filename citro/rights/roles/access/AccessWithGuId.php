<?php
require_once ('citro/rights/roles/access/Access.php');

class AccessWithGuId extends Access {
	
	public function __construct($guId, Zend_Config $config = NULL) {
		
		require_once 'db/sys/access/sys_access.php';
		$sysAccess = new sys_access();
		$row = $sysAccess->getAccessWithGuid($guId);
				
		if($row instanceof Zend_Db_Table_Row_Abstract){
			parent::__construct ($row);
		}

	}
	
	
}

?>