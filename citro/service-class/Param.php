<?php

/**
 * class ParamFunc
 *
 * Enth�llt f�r die Function die Parameter
 *
 * @author:
*/
class Param {
	
	const TAG_PARAM = "param";
	
	private $name = NULL;
	private $value = NULL;
	private $type = NULL;
	
	// private $AllParam_A = array(); // Hier sind alle f�r die Function
	// enthaltennen Parameter aufgelestet die keys k�nnen als integer oder
	// string vorhanden sein
	
	/**
	 * ParamFunc constructor
	 *
	 * @param       	
	 *
	 */
	function __construct($Name, $Value) {
		
		$this->name = $Name;
		$this->setValue ( $Value );
	
	}
	
	public function getName() {
		return $this->name;
	}
	public function getValue() {
		return $this->value;
	}
	public function getType() {
		return $this->type;
	}
	
	public function setValue($Value) {
		$this->value = $Value;
		return $this;
		
		// if($Type === NULL){
		
		// if(is_int($Value)){
		// $this->type = self::TYPE_INT;
		// $this->value = $Value;
		
		// }
		// elseif(is_string($Value)){
		// $this->type = self::TYPE_STRING;
		// $this->value = $Value;
		// }
		// elseif(is_bool($Value)){
		// $this->type = self::TYPE_BOOL;
		// $this->value = $Value;
		// }
		// else{
		
		// $this->type = self::TYPE_STRING;
		// $this->value = $Value;
		// }
		
		// }else{
		
		// switch($Type){
		// case self::TYPE_INT:
		// $this->value =(int)$Value;
		// $this->type = self::TYPE_INT;
		// break;
		
		// case self::TYPE_STRING:
		// $this->value = (string)$Value;
		// $this->type = self::TYPE_STRING;
		// break;
		
		// // case "l": $Param = explode(",",$Param);
		// // if($Param == false || count($Param)<1 ) $Param = null;
		// // break;
		// // case "a": if($Param == false || count($Param)<1 ) $Param = null;
		// // break;
		
		// default:
		// $this->value =(string)$Value;
		// break;
		// }
		// }
	
	}
	
	// public static function getParam($Name, $Value, $Type){
	
	// if(is_a($Name, "Param")){
	// $param = $Name;
	// }
	// else{
	// $param = new Param($Name,$Value, $Type);
	// }
	
	// return $param;
	// }
	
	// public static function getParam(){
	
	// $param = new Param($Name,$Value, $Type);
	
	// return $param;
	// }
	
	/**
	 * Ist f�r die Abarbeitung aller Parameter einer Function zust�ndig
	 * bergibt die eizelnen parameter der Function setFuncParam
	 *
	 * @param $AllParams mixed
	 *       	 This is a description
	 *       	
	 */
	public function setAllParamForFunc($AllParams) {
		
		// als erstet Testen ob die variabel vom Type Array ist also was �ber
		// die Get kommt
		if (is_array ( $AllParams )) {
			
			if (count ( $AllParams ) > 0) {
				foreach ( $AllParams as $Key => $GetValue ) {
					if (is_numeric ( $GetValue )) {
						$GetValue = trim ( $GetValue );
						$this->setFuncParam ( $GetValue, $Key, "i" );
					} elseif (is_string ( $GetValue )) {
						$GetValue = trim ( $GetValue );
						$this->setFuncParam ( $GetValue, $Key, "s" );
					} elseif (is_array ( $GetValue )) {
						
						$this->setFuncParam ( $GetValue, $Key, "a" );
					} else {
						$GetValue = trim ( $GetValue );
						$this->setFuncParam ( $GetValue, $Key, "s" );
					}
				
				}
			}
		
		} elseif (get_class ( $AllParams ) == "SimpleXMLElement") {
			
			foreach ( $AllParams->children () as $Param ) {
				
				if ($Param->getName () == "PARAM") {
					isset ( $Param ['pos'] ) ? $Posi = $Param ['pos'] : $Posi = null;
					isset ( $Param ['typ'] ) ? $Type = $Param ['typ'] : $Type = null;
					isset ( $Param ['isserialize'] ) ? $Seril = $Param ['isserialize'] : $Seril = null;
					
					$this->setFuncParam ( ( string ) $Param, ( string ) $Posi, ( string ) $Type, $Seril );
				
				}
			
			}
		
		}
	
	}
	
	/**
	 * Ist f�r die Abarbeitung eines einzelnen Parameters zust�ndig
	 *
	 * @param $Param mixed
	 *       	 This is a description
	 * @param $Position mixed
	 *       	 This is a description
	 * @param $Type mixed
	 *       	 This is a description
	 * @param $IsSerialize mixed
	 *       	 This is a description
	 *       	
	 */
	public function setFuncParam($Param, $Position = null, $Type = null, $IsSerialize = false) {
		
		if ($IsSerialize === true) {
			$Param = unserialize ( $Param );
			if ($Param === false)
				$Param = null;
		} else {
			switch ($Type) {
				case "i" :
					is_numeric ( $Param ) ? $Param = ( int ) $Param : $Param = null;
					break;
				
				case "s" :
					$Param = ( string ) $Param;
					break;
				
				case "l" :
					$Param = explode ( ",", $Param );
					if ($Param == false || count ( $Param ) < 1)
						$Param = null;
					break;
				case "a" :
					if ($Param == false || count ( $Param ) < 1)
						$Param = null;
					break;
				
				default :
					$Param = ( string ) $Param;
					break;
			}
		
		}
		
		if ($Param != null) {
			
			if ($Position === null) {
				$this->AllParam_A [] = $Param;
			} else {
				$this->AllParam_A [$Position] = $Param;
			}
		}
	
	}
}

?>