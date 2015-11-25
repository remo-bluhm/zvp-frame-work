<?php

// anfrage Objekt an den Service

class Request {
	
	
	
	
	/**
	 * @var Zend_Config
	 */
	private $_config = NULL;
	
	
	
	/**
	 * Der anfragende User falls nicht gesetzt dann NULL
	 * @var User NULL
	 */
	private $_userMain = NULL;
	
	
	
	/**
	 * Die anzufragende Ip Adresse
	 * @var string
	 */
	private $_Ip = null;
	
	
	
	/**
	 * Enthällt die gesendeten Daten Raw (array)
	 * @var array
	 */
	private $_requestData = NULL;
	
	

	/**
	 * Enthällt die Rohdaten die angefragt wurden 
	 * kann auch die protokolle enthalten
	 * @var unknown_type
	 */
	private $_sendDataRaw = NULL;
	
		
	
	
	/**
	 * Enthällt die Anfrageart 
	 * @var string POST|GET|RAWPOST
	 */
	private $_sendDataArt = NULL;
	
	
	
	/**
	 * Enthält nach der abarbeitung des Requeststrings den Type Massage
	 * @var Massage
	 */
	private $_message = NULL;
	
	
	
	/**
	 * Enthällt das Protocol wenn gesetzt
	 * @var Protocol
	 */
	private $_protocol = NULL;
	
	
	
	/**
	 * Inizialisirt das Message Objekt es erwartet einen Messagearray
	 * falls keiner angegegeben wird hier ein Error ausgel�st
	 *
	 * @param $config Zend_Config       	
	 * @param $dataRaw array
	 *       	 Anfrage Array
	 */
	function __construct(Zend_Config $config, $dataRaw = NULL) {
		
		$this->_config = $config;
		
		// Setzen der AnfrageIp
		require_once 'citro/IpTooling.php';
		$this->_Ip = IpTooling::getIp ();
		
		// Setzen der Gesendeten Daten
		$this->setSendDataRaw ($dataRaw);
		
		// private $AnfrageTime = null;
		// ermitteln der AnfrageTime
	
	}
	

	
	public function setProtocol(Protocol $protocol){
		$this->_protocol = $protocol;
	}


	
	/**
	 * Giebt das Protocol wiedre
	 * @return Protocol|NULL wenn nicht gesetzt dann null
	 */
	public function getProtocol() {
		return $this->_protocol;
	}
	
	
	
	/**
	 * Giebt die IP zurück
	 * @return string
	 */
	public function getIp() {
		return $this->_Ip;
	}
	
	
	
	/**
	 * Giebt die Rohdaden zurück von der anfrage
	 * @return unknown_type
	 */
	public function getSendDataRaw() {
		return $this->_sendDataRaw;
	}
	

	
	/**
	 * Giebt die Bereinigten Daten der Anfrage zurück nach der bearbeitung von workRequestString
	 * @return array|NUll 
	 */
	public function getRequestData(){
		return $this->_requestData;
	}
	
	
	/**
	 * Liefert anhand der übergebenen Daten ein Message Objekt zurück
	 */
	public function getMessage(){
		return $this->_message;
	}
	
	
	
	/**
	 * Hollt die Gesendeten Daten und schreibt das Protokoll davor
	 * @return Ambigous <string, unknown>|NULL
	 */
	public function setSendDataRaw($Data = NULL) {
		if($Data !== NULL){
			$this->_sendDataRaw = $Data;
			return ;
		}
		echo "<pre>";
		print_r($GLOBALS);
		$SendDataString = NULL;
		// Fals Daten über die Url gesendet wurden dann diese nehmen
		if (! empty ( $GLOBALS ["_GET"] ["vars"] )) {
			//echo "asdfasdfasdf";
			// entfernt vorm einschreiben die sleches
			$SendDataString = stripslashes ( $GLOBALS ["_GET"] ["vars"] ); 
			
			$SendDataString = $GLOBALS ["_GET"] ["vars"];;
			$this->_sendDataArt = "GET";
		
		}elseif (! empty ( $GLOBALS ['HTTP_RAW_POST_DATA'] )) {
			// Fals die Daten über die RawPostData kommen
			$SendDataString = $GLOBALS ['HTTP_RAW_POST_DATA'];
			$this->_sendDataArt = "RAWPOST";
		
		} elseif (! empty ( $GLOBALS ['_POST'] ['_DATA'] )) {
			// todo: muss noch gelöst werden da der wert gequotet wird das heist
			// alle ' bekommen ein \' davor
			$SendDataString = $GLOBALS ['_POST'] ['_DATA'];
			$this->_sendDataArt = "POST";
		
		} elseif (defined ( "VAR_SEND_DATA_TEST" )) {
			$SendDataString = VAR_SEND_DATA_TEST;
			$this->_sendDataArt = "TEST";
		}
// 		echo $SendDataString;
// 		echo $this->_sendDataArt;
		$this->_sendDataRaw = $SendDataString;
		echo "<pre>";
		print_r($SendDataString);
	}
	
	


	public function getAccess(){

		if($this->_message instanceof Message){
			$accessGuid = $this->_message->getAccessGuid();
			require_once 'citro/protocol/AProtocol.php';
			$access = AProtocol::getAccess($accessGuid);
			return $access;
		}
		return NULL;

	
	}
	

	
	
	/**
	 * Arbeitet den eingehenden Requeststring ab.
	 * Dabei werden wenn vorhanden alle Protocolle abgearbeitet
	 * und der Jsonstring decodiert($requestData als array);
	 * @return Message 
	 * @throws Exception falls der Jsonstring fehlerhaft war
	 */
	public function workRequestToMessage(){
	
	
		$ProtTree = NULL;
		
		//Falls ein ProtocollObjekt übrgeben wurde diese abarbeiten
		if($this->_protocol instanceof Protocol){
			
			// Abarbeiten des Protokolls und den Protokoll Baum zurückgeben
			$this->_protocol->setRequest( $this->getSendDataRaw() );
			
			/* @var $ProtTree AProtocol */
			$ProtTree = $this->_protocol->getProtocollTree ();
			
		}

		// falls $ProtTree Null wurde die anfrage nicht in einen Protocoll eingepackt und müsste ein JsonString sein
		if($ProtTree === NULL || !is_subclass_of($ProtTree, "AProtocol") ){
			$decodeData = $this->getSendDataRaw();
			
			

		}else{
			$decodeData = $ProtTree->getData();
		
		}
			


		require_once 'Zend/Json.php';
		$this->_requestData = Zend_Json::decode( $decodeData );
		
	
		require_once 'citro/Message.php';
		$this->_message = new Message($this->_config,$this->_requestData);
		
		return $this->_message;
	}

}

?>