<?php

/** 
 * @author Remo Bluhm
 * 
 * 
 */

class ParamConverts {
	
	const TYPE_INT = "integer";
	const TYPE_STRING = "string";
	const TYPE_BOOL = "boolean";
	const TYPE_ARRAY = "array";
	
	public static function Convert($Value, $Type) {
		// echo "<pre>";
		// var_dump($Type);
		// var_dump($Value);
		
		$value = NULL;
		
		switch ($Type) {
			
			case self::TYPE_ARRAY :
				$value = $Value;
				break;
			
			case self::TYPE_BOOL :
				$value = self::getBool ( $Value );
				break;
			
			case self::TYPE_INT :
				$value = self::getInteger ( $Value );
				break;
			
			case self::TYPE_STRING :
				$value = self::getString ( $Value );
				
				break;
			
			// case "l": $Param = explode(",",$Param);
			// if($Param == false || count($Param)<1 ) $Param = null;
			// break;
			// case "a": if($Param == false || count($Param)<1 ) $Param = null;
			// break;
			
			default :
				$value = ( string ) $Value;
				break;
		}
		
		return $value;
	
	}
	
	public static function isValueType($Value, $Type){
		$value = NULL;
		
		switch ($Type) {
				
			case self::TYPE_ARRAY :
				$value = is_array($Value);
				break;
					
			case self::TYPE_BOOL :
				$value = is_bool( $Value );
				break;
					
			case self::TYPE_INT :
				$value = is_numeric( $Value );
				break;
					
			case self::TYPE_STRING :
				$value = is_string( $Value );
				break;
				
			default :
				$value = NULL;
				break;
		}
		
		return $value;
	}
	
	public static function getInteger($Value, $Stand = 0, $NumMin = 0, $NumMax = 1000000) {
		
		// Testen der Werte
		if (! is_integer ( $Value )) {
			$Value = ( integer ) $Value;
		
		}
		
		if ($Value > $NumMin && $Value < $NumMax) {
			return $Value;
		}
		
		return $Stand;
	
	}
	
	public static function getString($Value, $Stand = NULL, $maxLang = 255, $htmlspecialchars = TRUE) {
		
		$KeyStr = ( string ) $Value;
		
		if (strlen ( $KeyStr ) < $maxLang) {
			if ($htmlspecialchars) {
				$KeyStr = htmlspecialchars ( $KeyStr );
			}
			return $KeyStr;
		}
		
		return $Stand;
	
	}
	
	/**
	 * Hollt den Parameter und Pr�ft ob dieser vom type "true" = 1 oder "false"
	 * = 0 ist
	 * 
	 * @param
	 *       	 string Valuer Key des Parameters
	 * @param
	 *       	 bool ValueDen Standartwert(False) wenn dieser vom user nicht
	 *        	gesetzt wurde
	 * @return boolean
	 */
	public static function getBool($Value) {
		
		if ($Value === "1" || $Value === "true" || $Value === "TRUE" || $Value === 1 || $Value === TRUE) {
			return TRUE;
		} else {
			return FALSE;
		}
	
	}
	
	public static function getInList($Key, $Stand = NULL, $List = array()) {
		
		// der Gesendete Wert
		$KeyVal = $this->getParam ( $Key, $Stand );
		
		// Die �bergebene Liste worin gesucht werden soll ob der wert exestiert
		$Liste = array ();
		if (is_array ( $List ) && count ( $List ) > 0) {
			$Liste = $List;
		}
		if (is_string ( $List ) && strlen ( $List ) > 2) {
			$Liste = explode ( ",", $List );
		}
		
		// Pr�fen ob der �bergebene wert in der $List exestiert
		if (in_array ( $KeyVal, $Liste )) {
			return $KeyVal;
		}
		
		return $Stand;
	
	}
	
	public static function getGUID($KeyGuId, $Stand = NULL) {
		
		$KeyGuId = $this->getParam ( $KeyGuId );
		
		if ($KeyGuId === NULL) {
			// nicht gesetzt
			return $Stand;
		} else {
			require_once 'citro/GuidCreate.php';
			
			if (GuidCreate::isProbablyGUID ( $KeyGuId )) {
				return $KeyGuId;
			}
			
			return $Stand;
		
		}
	
	}

}

?>