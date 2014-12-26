<?php

/**
 * class Message
 * 
 * Beinhaltet die Abarbeitung der Kompletten anfrage
 * Beinhaltet Functionen zur abarbeitung
 *
 * @author:
*/
class Message {
	
	const TAG_GUID = "code"; // der KeyName der Responce
	
	private $_guid = NULL;

	
	
	
	/**
	 * Enthällt die anfragenden Services
	 *
	 * @var array Service
	 */
	private $_services = array ();
	

	
	
	
	/**
	 * Inizialisieren
	 * @param Zend_Config $config 
	 * @param array $data Die Anfrage Daten die in das Message convertiert werden sollen
	 */
	function __construct(Zend_Config $config, $data = NULL){
	
		if( is_array($data) ){
			$this->setDataArray($data);
		}
			
		
	}
	


	
	
	/**
	 * Giebt alle services zurück
	 * 
	 * @return array:
	 */
	public function getServices() {
		return $this->_services;
	}
	
	

	
	/**
	 * Giebt die UserGuid zurück
	 * @return string|NULL
	 */
	public function getAccessGuid() {
		return $this->_guid;
	}
	
	
	
	
	/**
	 * Giebt die Backart zurück
	 * 
	 */
	public function getBackArt(){
		return $this->_backart;
	}
	
	
	
	public function setDataArray(array $request) {
		
		if (array_key_exists ( self::TAG_GUID, $request )) {
			$this->_guid = $request [self::TAG_GUID];
		}
	
		
		require_once 'citro/service-class/Service.php';
		
		if (array_key_exists ( Service::TAG_SERVICE, $request )) {
			
			$serviceOne = $request [Service::TAG_SERVICE];
			
			if (array_key_exists ( Service::TAG_NAME, $serviceOne )) {
				$service = new Service ( $serviceOne [Service::TAG_NAME] );
				$service->setDataArray ( $serviceOne );
				$this->_services [] = $service;
			}
		}
		
		if (array_key_exists ( Service::TAG_SERVICE . "s", $request )) {
			foreach ( $request [Service::TAG_SERVICE . "s"] as $Key => $serv ) {
				
				$service = new Service ( $serv [Service::TAG_NAME] );
				
				$service->setDataArray ( $serv );
				// $service->setName($Key);
				// $service->setService($serv);
				$this->_services [] = $service;
			}
		
		}
	
	}
	

	
	

}

?>