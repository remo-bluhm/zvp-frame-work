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
	public static function getDateTime($timestamp = NULL){
		if($timestamp === NULL)$timestamp = time();
		$DataTime = date ( "Y-m-d H:i:s", $timestamp );
		return $DataTime;
	}
	
	public static function DateTime() {
		
		$DataTime = date ( "Y-m-d H:i:s", $GLOBALS ["_SERVER"] ["REQUEST_TIME"] );
		return $DataTime;
	}
	
	
	protected function _setupTableName() {
		$this->_name = self::getTableNameStatic();
		parent::_setupTableName();
// 		if (self::$DbPrafix != NULL) {
// 			//$this->_name = self::getDBPrafix () . $this->_TableName;
// 			$className = get_called_class();
// 			$this->_name = self::getDBPrafix () . $className;
// 		}
// 		parent::_setupTableName ();
	
	}
	
	
	public static function getTableNameStatic(){
		$className = get_called_class();
		if (self::$DbPrafix != NULL) {
			return  self::getDBPrafix () . $className ;
		}
		return $className;
	}
	
	public function getTableName() {
		
		return $this->_name;
	}
	
	
	

	
	/**
	 * Setzt ein Datenbank präfix
	 * 
	 * @param $PraefixStr string       	
	 */
	public static function setDBPrafix($PraefixStr) {
		if (is_string ( $PraefixStr ) && strlen ( $PraefixStr ) < self::$DbPrafixMaxLang) {
			self::$DbPrafix = $PraefixStr;
		}
		if ($PraefixStr === NULL)
			self::$DbPrafix = NULL;
	}
	/**
	 * giebt fals gesetzt den Datenbank präfix zurück
	 * 
	 * @return string NULL Datenbank präfix falls nicht gesetzt dann NULL
	 */
	public static function getDBPrafix() {
		return self::$DbPrafix;
	}
	
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
	 * @param $Liste array Die liste die durchlaufen werden soll
	 * @param $SpalteName string Der Spaltennamen der liste
	 * @param $toLower bool	 ob der valuewert kleingeschriebben werden soll // veraltet
	 * @param $KeyAsId FALSE|SpaltenName(id) Falls ein wert gesetzt ist wird dieser als Key verwendet
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
	public static function createNew_UnId(){
		return  uniqid ();
	}
	
	
	/**
	 * Testet die Id auf Integer und länge
	 * 
	 * @param $id integer       	
	 * @return integer boolean Fehlerfalle False
	 */
	public static function testId($id) {
		if (is_int ( $id )) {
			if ($id < 99999999999) {
				return $id;
			}
		
		}
		return FALSE;
	}

}
