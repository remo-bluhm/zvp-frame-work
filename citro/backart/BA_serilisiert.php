<?php

class BA_serilisiert {
	
	const TEXT_TYPE = "seri";
	private $SendString;
	
	function BA_serilisiert() {
	
	}
	public function getType() {
		return BA_serilisiert::TEXT_TYPE;
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
		$AllArryFunc ["allfunc"] = $ArrayFunc;
		$ResponceString = serialize ( $AllArryFunc );
		
		return $ResponceString;
	
	}

}
