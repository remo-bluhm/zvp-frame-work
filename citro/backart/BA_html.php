// <?php

// require_once 'citro/xml/XDataTable.php';

// /**
//  * class BA_html
//  *
//  * Description for class BA_html
//  *
//  * @author :
//  *        
//  */
// class BA_html {
	
// 	const TEXT_TYPE = "html";
// 	private $BackType;
// 	private $SendString;
// 	/**
// 	 * BA_html constructor
// 	 *
// 	 * @param       	
// 	 *
// 	 */
// 	function BA_html() {
	
// 	}
	
// 	public function getType() {
// 		return BA_html::TEXT_TYPE;
// 	}
// 	public function getString() {
		
// 		return $this->SendString;
	
// 	}
	
// 	public function getWrap($ListFunc) {
// 		// header('content-type:text/html; charset=utf-8;');
		
// 		$FuncBackStr = "";
		
// 		if (! empty ( $ListFunc ) && count ( $ListFunc ) > 0) {
			
// 			foreach ( $ListFunc as $FuncName => $Func ) {
				
// 				$FetchArray = $Func;
				
// 				$TableBackStr = $this->WrapTable ( $FetchArray );
				
// 				$FuncBackStr .= '<div id="' . $FuncName . '" >' . $TableBackStr . "</div>";
			
// 			}
		
// 		}
// 		$FuncBackStr = '<div id="AllService" >' . $FuncBackStr . "</div>";
// 		$ResponceString = $FuncBackStr;
		
// 		return $ResponceString;
// 	}
	
// 	private function WrapTable($FetchArray) {
		
// 		// TODO: $FetchArray auf Inhalt pr�fen
// 		if (is_string ( $FetchArray )) {
// 			$FetchArray = array ($FetchArray );
// 		}
// 		$DataTabel = new XDataTable ();
// 		return $DataTabel->write ( $FetchArray );
// 	}

// }

// ?>