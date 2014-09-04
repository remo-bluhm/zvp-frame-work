<?php
require_once 'Zend/Json.php';
/**
 * class BA_json
 *
 * Description for class BA_json
 *
 * @author :
 *        
 */
class BA_json {
	
	const TEXT_TYPE = "json";
	private $SendString;
	
	/**
	 * BA_json constructor
	 *
	 * @param       	
	 *
	 */
	function BA_json() {
	
	}
	
	public function getType() {
		return BA_json::TEXT_TYPE;
	}
	public function getString() {
		
		return $this->SendString;
	
	}
	public function getWrap($ListFunc) {
		
		$AllArryFunc = array ();
		
		if (! empty ( $ListFunc ) && count ( $ListFunc ) > 0) {
			
			$ArrayFunc = array ();
			
			foreach ( $ListFunc as $FuncName => $Func ) {
				
				$ArrayFunc [$FuncName] = $Func;
			
			}
		
		}
		$AllArryFunc ["ALLSERVICE"] = $ArrayFunc;
		$ResponceString = Zend_Json::encode ( $AllArryFunc );
		
		return $ResponceString;
	
	}

}

?>