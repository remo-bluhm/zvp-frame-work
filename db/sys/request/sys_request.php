<?php

require_once 'citro/DBTable.php';
/**
 * class sys_customer
 *
 * Description for class sys_customer
 *
 * @author :
 *        
 */
class sys_request extends DBTable {
	

	protected $_primary = 'id';
	
	const SP_ID = "id";
	const SP_DATA_CREATE = "time";
	const SP_IP = "ip";
	const SP_USERID = "userid";
	


	
	function __construct($config = array()) {
		parent::__construct ( $config );
	}
	
	/**
	 * @param integer $userId
	 * @param string $ip
	 * @return Ambigous <mixed, multitype:>
	 */
	public function set($userId,$ip){

		$data = array();
		$data[self::SP_DATA_CREATE] = self::DateTime();
		$data[self::SP_IP] = $ip;
		$data[self::SP_USERID] = $userId;
	
		$primaryId = parent::insert($data);
		return $primaryId;
	}
	
	
	
}

?>