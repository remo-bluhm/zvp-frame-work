<?php

class Responce {
	

	private static $_instance = NULL;
	
	public  $error = array ();
	public  $services = array();
	
	private static $_protocol = NULL;
	
	function __construct(){
		
	}
	

	
	
	
	
	/**
	 * @return Responce
	 */
	public static function getInstance(){
		if(self::$_instance === NULL)
			self::$_instance = new Responce();
		
		return self::$_instance;
	}
	
	
	public static function setProtocol(Protocol $protocol){
		self::$_protocol = $protocol;
	}
	
	
	public function responceString(){
	
		require_once 'Zend/Json.php';
		$ResponceString = Zend_Json::encode ( $this );
		return $ResponceString;
		
	}
	
	public static function responceOutAndExit(){
	
		
			
		if(self::$_protocol === NULL){
			echo self::$_instance->responceString();
			exit();
		}else{
			$protocolTree = self::$_protocol->getProtocollTree();
			if ($protocolTree === NULL){
				echo self::$_instance->responceString();
				exit();
			}else{
				echo self::$_protocol->getProtocollTree()->setResponce( self::$_instance->responceString() );
				exit();
			}
		}
			
		
	}
	
	
	
	
	public function setError($text) {
	
		$this->error[] = $text;
	}
	
	public function setServices(array $services){

		$this->services = $services;
	}
	

}

?>