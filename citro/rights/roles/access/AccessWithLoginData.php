<?php
require_once ('citro/rights/roles/access/Access.php');


class AccessWithLoginData extends Access {
	
	public function __construct( $Loginname, $Password ,Zend_Config $config = NULL) {
		
		require_once 'db/sys/access/sys_access.php';
		
		// Hollen des Users aus der DatenClasse
		$sysAccess = new sys_access();
		$row = $sysAccess->getAccessWithLoginData ( $Loginname, $Password );
		
		if($row instanceof Zend_Db_Table_Row_Abstract){			
			parent::__construct ($row );
		}
		
	
	
	}
}

?>