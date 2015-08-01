<?php

// get mi

// und zum zweiten

include_once ("Zend/Db/Table/Abstract.php");

abstract class DBTable extends Zend_Db_Table_Abstract {
	
	private static $DbPrafixMaxLang = 5;
	private static $DbPrafix = NULL;
	
	function __construct($config = array()) {
		parent::__construct ( $config );
	
	}
	
	
	public function init() 

	{
		
		// $this->_observer = new MyObserverClass();
		parent::init ();
	}
	
	/**
	 * Giebt ein DataTime String zurück der RequestTime
	 *
	 * @return string DataTime string
	 *        
	 */
	public static function getDateTime($timestamp = NULL) {
		if ($timestamp === NULL)
			$timestamp = time ();
		$DataTime = date ( "Y-m-d H:i:s", $timestamp );
		return $DataTime;
	}
	
	public static function DateTime() {
		
		$DataTime = date ( "Y-m-d H:i:s", $GLOBALS ["_SERVER"] ["REQUEST_TIME"] );
		return $DataTime;
	}
	
	protected function _setupTableName() {
		// $this->_name = self::getTableNameStatic();
		parent::_setupTableName ();
		// if (self::$DbPrafix != NULL) {
		// //$this->_name = self::getDBPrafix () . $this->_TableName;
		// $className = get_called_class();
		// $this->_name = self::getDBPrafix () . $className;
		// }
		// parent::_setupTableName ();
	
	}
	
	public static function getTableNameStatic() {
		$className = get_called_class ();
		if (self::$DbPrafix != NULL) {
			return self::getDBPrafix () . $className;
		}
		return $className;
	}
	
	public function getTableName() {
		
		return $this->_name;
	}
	
	public function testColum($method_name){
		return  method_exists($this, $method_name);
	}
	
	/**
	 * Bereinigt das Array $fields mit den mitgegebenen Feldern $cols
	 * @param array $fields Das zu bereinigende Array
	 * @param array $cols mit einer liste der Cols die zurückgegeben werden sollen 
	 */
	public static function colsCleanArray(array $fields,array $cols) {
		
		$cols = array_flip ( $cols );
		
		$cleanCols = array_intersect_key ( $fields, $cols );
	}
	

	/**
	 * Ersetzt alle sonder Buchstaben 
	 * @param string $str
	 * @return sring Der Bereinigte String
	 */
	function remove_accent($str)
	{
	    $a = array('À', 'Á', 'Â', 'Ã', 'Ä',  'Å', 'Æ',  'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö',  'Ø', 'Ù', 'Ú', 'Û', 'Ü',  'Ý', 'ß',  'à', 'á', 'â', 'ã', 'ä',  'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö',  'ø', 'ù', 'ú', 'û', 'ü',  'ý', 'ÿ', 'Ā', 'ā', 'Ă', 'ă', 'Ą', 'ą', 'Ć', 'ć', 'Ĉ', 'ĉ', 'Ċ', 'ċ', 'Č', 'č', 'Ď', 'ď', 'Đ', 'đ', 'Ē', 'ē', 'Ĕ', 'ĕ', 'Ė', 'ė', 'Ę', 'ę', 'Ě', 'ě', 'Ĝ', 'ĝ', 'Ğ', 'ğ', 'Ġ', 'ġ', 'Ģ', 'ģ', 'Ĥ', 'ĥ', 'Ħ', 'ħ', 'Ĩ', 'ĩ', 'Ī', 'ī', 'Ĭ', 'ĭ', 'Į', 'į', 'İ', 'ı', 'Ĳ',  'ĳ',  'Ĵ', 'ĵ', 'Ķ', 'ķ', 'Ĺ', 'ĺ', 'Ļ', 'ļ', 'Ľ', 'ľ', 'Ŀ', 'ŀ', 'Ł', 'ł', 'Ń', 'ń', 'Ņ', 'ņ', 'Ň', 'ň', 'ŉ', 'Ō', 'ō', 'Ŏ', 'ŏ', 'Ő', 'ő', 'Œ',  'œ',  'Ŕ', 'ŕ', 'Ŗ', 'ŗ', 'Ř', 'ř', 'Ś', 'ś', 'Ŝ', 'ŝ', 'Ş', 'ş', 'Š', 'š', 'Ţ', 'ţ', 'Ť', 'ť', 'Ŧ', 'ŧ', 'Ũ', 'ũ', 'Ū', 'ū', 'Ŭ', 'ŭ', 'Ů', 'ů', 'Ű', 'ű', 'Ų', 'ų', 'Ŵ', 'ŵ', 'Ŷ', 'ŷ', 'Ÿ', 'Ź', 'ź', 'Ż', 'ż', 'Ž', 'ž', 'ſ', 'ƒ', 'Ơ', 'ơ', 'Ư', 'ư', 'Ǎ', 'ǎ', 'Ǐ', 'ǐ', 'Ǒ', 'ǒ', 'Ǔ', 'ǔ', 'Ǖ', 'ǖ', 'Ǘ', 'ǘ', 'Ǚ', 'ǚ', 'Ǜ', 'ǜ', 'Ǻ', 'ǻ', 'Ǽ',  'ǽ',  'Ǿ', 'ǿ');
	    $b = array('A', 'A', 'A', 'A', 'Ae', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'Oe', 'O', 'U', 'U', 'U', 'Ue', 'Y', 'ss', 'a', 'a', 'a', 'a', 'ae', 'a', 'a', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'oe', 'o', 'u', 'u', 'u', 'ue', 'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'l', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o');
	    return str_replace($a, $b, $str);
	}
	
	function post_slug($str)
	{
	    return strtolower( preg_replace( array('/[^a-zA-Z0-9 -]/', '/[ -]+/', '/^-|-$/'), array('', '-', ''), $this->remove_accent($str)));
	}
	
	// /**
	// * Setzt ein Datenbank präfix
	// *
	// * @param $PraefixStr string
	// */
	// public static function setDBPrafix($PraefixStr) {
	// if (is_string ( $PraefixStr ) && strlen ( $PraefixStr ) <
	// self::$DbPrafixMaxLang) {
	// self::$DbPrafix = $PraefixStr;
	// }
	// if ($PraefixStr === NULL)
	// self::$DbPrafix = NULL;
	// }
	// /**
	// * giebt fals gesetzt den Datenbank präfix zurück
	// *
	// * @return string NULL Datenbank präfix falls nicht gesetzt dann NULL
	// */
	// public static function getDBPrafix() {
	// return self::$DbPrafix;
	// }
	
	/**
	 * Daten die in die Datenbank eingeschrieben werden sollen sollten diese
	 * function durchlaufen damit es keine SQLInjektion entsteht
	 *
	 * @param $value string
	 *       	 Der zu durchlaufende string
	 * @return string Bereinigter string
	 *        
	 */
	public static function mysql_prep($value) {
		if (get_magic_quotes_gpc ()) {
			$value = stripslashes ( $value );
		} else {
			$value = addslashes ( $value );
		}
		return $value;
	}
	
	/**
	 * Generiert eine Liste aus einen DBRequest Array
	 *
	 * @param $Liste array
	 *       	 Die liste die durchlaufen werden soll
	 * @param $SpalteName string
	 *       	 Der Spaltennamen der liste
	 * @param $toLower bool
	 *       	 ob der valuewert kleingeschriebben werden soll // veraltet
	 * @param $KeyAsId FALSE|SpaltenName(id)
	 *       	 Falls ein wert gesetzt ist wird dieser als Key verwendet
	 * @return array liste
	 *        
	 */
	public function toList($Liste, $SpalteName, $toLower = true, $KeyAsId = FALSE) {
		$BackArray = array ();
		foreach ( $Liste as $Elm ) {
			
			if ($toLower) {
				
				$ElemVal = strtolower ( $Elm [$SpalteName] );
			} else {
				$ElemVal = $Elm [$SpalteName];
			
			}
			
			if ($KeyAsId === FALSE) {
				$BackArray [] = $ElemVal;
			} else {
				$BackArray [$Elm [$KeyAsId]] = $ElemVal;
			}
		
		}
		
		return $BackArray;
	}
	
	/**
	 * erstellt ein eindeutigen UnId
	 */
	public static function createNew_UnId() {
		return uniqid ();
	}
	
	/**
	 * Testet die Id auf Integer und länge
	 *
	 * @param $id integer       	
	 * @return integer boolean Fehlerfalle False
	 */
	public static function testId($value) {
		if (empty ( $value ))
			return FALSE;
		if (! is_numeric ( $value ))
			return FALSE;
		if (is_float ( $value ))
			return FALSE;
		if ($value < 1)
			return FALSE;
		if ($value > 99999999999)
			return FALSE;
		return ( int ) $value;
	}

}
