 
//<?php

// require_once 'citro/xml/XDataXml.php';
// require_once 'citro/xml/XElement.php';
// require_once 'citro/xml/XAtribute.php';

// /**
//  * class BA_xml
//  *
//  * Description for class BA_xml
//  *
//  * @author :
//  *        
//  */
// class BA_xml {
	
// 	const TEXT_TYPE = "xml";
// 	private $SendString;
// 	/**
// 	 * BA_xml constructor
// 	 *
// 	 * @param       	
// 	 *
// 	 */
// 	function BA_xml() {
		
// 		/*
// 		 * $DataTabel = new XDataXml(); $DataTabel->DataArray = $AllFetchArray;
// 		 * $this->SendString = $DataTabel->write();
// 		 */
// 	}
// 	public function getType() {
// 		return BA_xml::TEXT_TYPE;
// 	}
// 	public function getString() {
		
// 		return $this->SendString;
	
// 	}
	
// 	public function getWrap($ListFunc) {
		
// 		$FetchArray = "";
// 		$AllServ = array ();
// 		if (! empty ( $ListFunc ) && count ( $ListFunc ) > 0) {
			
// 			foreach ( $ListFunc as $FuncName => $Func ) {
// 				$AllServ [$FuncName] = $Func;
// 			}
// 			$FetchArray = $this->toXml ( $AllServ, "ALLSERVICE" );
// 		}
// 		$FetchArray = $this->toXml ( $AllServ, "ALLSERVICE" );
		
// 		return $FetchArray;
// 	}
	
// 	/**
// 	 * The main function for converting to an XML document.
// 	 * Pass in a multi dimensional array and this recrusively loops through and
// 	 * builds up an XML document.
// 	 *
// 	 * @param $data array       	
// 	 * @param $rootNodeName string
// 	 *       	 - what you want the root node to be - defaultsto data.
// 	 * @param $xml SimpleXMLElement
// 	 *       	 - should only be used recursively
// 	 * @return string XML
// 	 */
// 	public static function toXml($data, $rootNodeName = 'data', $xml = null) {
// 		// turn off compatibility mode as simple xml throws a wobbly if you
// 		// don't.
// 		if (ini_get ( 'zend.ze1_compatibility_mode' ) == 1) {
// 			ini_set ( 'zend.ze1_compatibility_mode', 0 );
// 		}
		
// 		if ($xml == null) {
// 			$xml = simplexml_load_string ( '<?xml version="1.0" encoding="utf-8" 

// <$rootNodeName />' );
// 		}
		
// 		// loop through the data passed in.
// 		foreach ( $data as $key => $value ) {
// 			// no numeric keys in our xml please!
// 			if (is_numeric ( $key )) {
// 				// make string key...
// 				$key = "Node_" . ( string ) $key;
// 			}
			
// 			// replace anything not alpha numeric
// 			// $key = preg_replace('/[^a-z]/i', '', $key);
			
// 			// if there is another array found recrusively call this function
// 			if (is_array ( $value )) {
// 				$node = $xml->addChild ( $key );
// 				// recrusive call.
// 				self::toXml ( $value, $rootNodeName, $node );
// 			} else {
// 				// add single node.
// 				$value = htmlentities ( $value );
// 				$xml->addChild ( $key, $value );
// 			}
		
// 		}
// 		// pass back as string. or simple xml object if you want!
// 		return $xml->asXML ();
// 	}
	
// 	private function WrapTable($FetchArray) {
		
// 		// TODO: $FetchArray auf Inhalt pr�fen
// 		$DataTabel = new XDataXml ();
// 		$DataTabel->DataArray = $FetchArray;
// 		return $DataTabel->write ();
// 	}
// }

// 