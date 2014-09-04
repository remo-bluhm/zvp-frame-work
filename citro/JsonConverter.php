<?php


/**
 * class JsonConverter
 *
 * Description for class Protocol_json
 *
 * @author :
 *        
 */
class JsonConverter {
	
	private $_config = NULL;
	/**
	 * Protocol_json constructor
	 *
	 * @param       	
	 *
	 *
	 */
	function __construct($config = NULL) {
		if ($config !== NULL)	$this->_config = $config;
	}
	
	/**
	 * Decoding Json
	 * @param string $SendString
	 * @return Ambigous <unknown, multitype:string unknown >|boolean
	 */
	public function json_decode( $SendString) {

		try {
			
// 			$ND_Utf8n = utf8_decode ( $SendString );
// 			$ND = utf8_encode ( $ND_Utf8n );
// 			$jsonValue = json_decode ( $ND, true );
// 			$jsonData = $this->ArrayUtf8Decode ( $jsonValue );
			require_once 'Zend/Json.php';
			$jsonData = Zend_Json::decode( $SendString );
		
			return $jsonData;
		} catch ( Exception $eJsonDecode ) {
			return FALSE;
			//throw new LogException ( new ErrorCodes ( ErrorCodes::APP_SYS, "PRO", 501 ), "Der Json String ist Fehlerhaft", E_ERROR, $eJsonDecode );
		}
	
	}
	
	/**
	 * Encoding Json
	 * @param array $ArrayFunc
	 * @return Ambigous <string, mixed>
	 */
	public function json_encode($array) {
		
		require_once 'Zend/Json.php';
		$ResponceString = Zend_Json::encode ( $array );

	return $ResponceString;
	}
	


// 	// wird intern genuzt um zu testen wie tief die verschachtelung im augenblick ist
// 	private $VT = 0; 
// 	// bis zu welcher Tiefe der Decoder angewendet werden soll Ab 4 Ebene sind die Parameterdaten
// 	private $VTDecode = 100; 
	
// 	private function ArrayUtf8Decode($A) {
// 		$BA = array ();
// 		$this->VT ++;
// 		if ($this->VT <= $this->VTDecode) {
// 			if (is_array ( $A ) && count ( $A ) > 0) {
				
// 				foreach ( $A as $Key => $Value ) {
					
// 					if (is_array ( $Value )) {
						
// 						$BackA = $this->ArrayUtf8Decode ( $Value );
// 						if ($BackA != false) {
// 							$BA [utf8_decode ( $Key )] = $BackA;
// 						}
					
// 					} else {
// 						$BA [utf8_decode ( $Key )] = utf8_decode ( $Value );
// 					}
// 				}
			
// 			} else {
// 				$BA = false;
// 			}
// 		} else {
// 			$BA = $A;
// 		}
// 		$this->VT --;
// 		return $BA;
// 	}

}

?>