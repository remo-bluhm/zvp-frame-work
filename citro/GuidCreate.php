<?php

class GuidCreate {
	
	const GUID_LANG = 39;
	
	/**
	 * Erstellt eine neue GuId
	 *
	 * @return string Die Guid mit einer l�nge von 39 Zeichen.
	 */
	public static function generateGUID() {
		
		$ip_pop = explode ( '.', $GLOBALS ["_SERVER"] ["REMOTE_ADDR"] );
		
		$uid = uniqid ( "", TRUE );
		$uidex = explode ( ".", $uid ); // der Erste wert ist 14 Zeichen Lang
		
		$guid = "";
		$guid .= $uidex [0]; // 14
		$guid .= "-"; // 1
		$guid .= dechex ( mt_rand ( 16, 255 ) ); // 2
		$guid .= "-"; // 1
		$guid .= self::toHex ( $ip_pop [0] ); // 2
		$guid .= self::toHex ( $ip_pop [1] ); // 2
		$guid .= "-"; // 1
		$guid .= self::toHex ( $ip_pop [2] ); // 2
		$guid .= self::toHex ( $ip_pop [3] ); // 2
		$guid .= "-"; // 1
		$guid .= dechex ( mt_rand ( 16, 255 ) ); // 2
		$guid .= "-"; // 1
		$guid .= $uidex [1]; // 8
		
		return strtoupper ( $guid );
	
	}
	
	private static function toHex($number) {
		
		$hex = dechex ( $number );
		if (strlen ( $hex ) == 1) {
			return "0" . $hex;
		} else {
			return $hex;
		}
	}
	
	/**
	 * Testet ob der �bergebene String eine Guid sein kann gepr�ft wird dabei
	 * die Strucktur und die l�ngen
	 * 
	 * @param $guid string       	
	 * @return boolean TRUE|FALSE
	 */
	public static function isProbablyGUID($guid) {
		
		if($guid === NULL)
			return FALSE;
		
		if (is_string ( $guid ) && strlen ( $guid ) == self::GUID_LANG) {
			
			$elem = explode ( "-", $guid );
			if ($elem != FALSE && count ( $elem ) == 6) {
				if (strlen ( $elem [0] ) != 14)
					return FALSE;
				if (strlen ( $elem [1] ) != 2)
					return FALSE;
				if (strlen ( $elem [2] ) != 4)
					return FALSE;
				if (strlen ( $elem [3] ) != 4)
					return FALSE;
				if (strlen ( $elem [4] ) != 2)
					return FALSE;
				if (strlen ( $elem [5] ) != 8)
					return FALSE;
					
					// alle Pr�fungen bestanden
				return TRUE;
			
			}
		}
		
		return FALSE;
	
	}
}

?>