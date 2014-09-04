<?php

class UpdateHelpFunc {
	
	const UPDATE_HASH_KEY = "UpdateRepositoryHashKey";
	
	/**
	 * giebt die zu updatente Spalte mit dem HashKey zurück
	 * zurückgegeben wird ein Array mit Spaltenname(key)=>Wert(value)
	 *
	 * @param $columnArray array Eine Liste aller Spalten die zurückgegeben werden sollen falls	null dan werden alle zurückgegeben
	 * @param $withHashKey array Wenn True dan ist in dem Zurüggegebenen array schon der	HashKey mit enthalten
	 * @return string
	 */
	public static function getColumnToUpdate(array $data, array $columnArray) {
			return array_intersect_key ( $data, array_flip ( $columnArray ) );
	}
	
	
	
	
	/**
	 * Fügt in den Übergebenen Daten einen Hashekey ein
	 * @param array $toUpdateData
	 * @param array $data
	 * @return string
	 */
	public static function insertHashKey(SelectFactory $toUpdateFactory){
		$data = $toUpdateFactory->toUpdate();
		$data [self::UPDATE_HASH_KEY] = $toUpdateFactory->getHashKey();
		return $data;
	}
	
	
	/**
	 * ist für die Errstellung eines HashKeys verantwortlich
	 *
	 * @param $tupelArray array
	 * @return string
	 */
	public static function generateeHashKey($updateData) {
	
		$tupelsSeri = serialize ( $updateData );
		$tupelsSha1 = sha1 ( $tupelsSeri );
		return $tupelsSha1;
	
	}
	
	
}

?>