<?php

require_once 'citro/service-class/AService.php';

/**
 * Der Service ist für die verwaltung der Gruppen verantwortlich
 *
 * @author Max Plank
 * @version 1.0
 *         
 */
class ServiceSysUser extends AService {
	
	/**
	 * Der Rechemanagement construktor
	 */
	function __construct() {
		
		parent::__construct ();
	
	}
	

	
	/**
	 * Erstellen eines Neuen Systemusers
	 * @param array $data
	 */
	public function ActionNew($data){
		
	}
	
	/**
	 * Erstellen eines Neuen System Administrator
	 * @param array $data
	 */
	public function ActionNewAdmin($data){
	
	}
	
	/**
	 * @param integer $count
	 * @param integer $offset
	 * @param array $where
	 * @param array $spalten
	 */
	public function ActionGetList($count, $offset, $where = array(), $spalten = array()){
		
		if(isset($where["groupid"])){
			
		}
		
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}

?>