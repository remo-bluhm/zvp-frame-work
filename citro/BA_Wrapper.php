<?php

/**
 * class BA_Wrapper
 *
 * Description for class BA_Wrapper
 *
 * @author:
*/
class BA_Wrapper {
	
	private $BackArtWraper;
	private $ResponceArray;
	
	function __construct(Message $Message) {
		
		$Message->getBackart () === NULL ? $backArt = "json" : $backArt = $Message->getBackart ();
		$this->setBA_Wrapper ( $backArt );
	
	}
	
	private function setBA_Wrapper($BackArtStr) {
		
		$BackArtStr = strtolower ( $BackArtStr );
		
		switch ($BackArtStr) {
			
			case "seri" :
				require_once 'citro/backart/BA_serilisiert.php';
				$this->BackArtWraper = new BA_serilisiert ();
				break;
			
			case "xml" :
				require_once 'citro/backart/BA_xml.php';
				$this->BackArtWraper = new BA_xml ();
				break;
			
			case "html" :
				require_once 'citro/backart/BA_html.php';
				$this->BackArtWraper = new BA_html ();
				break;
			
			case "json" :
				require_once 'citro/backart/BA_json.php';
				$this->BackArtWraper = new BA_json ();
				break;
			
			default :
				require_once 'citro/backart/BA_json.php';
				$this->BackArtWraper = new BA_json ();
				break;
		}
	
	}
	
	public function Wrapp($RespArray) {
		
		if (is_array ( $RespArray )) {
			$this->ResponceArray = $RespArray;
		}
		
		$wrapper = $this->BackArtWraper;
		$Responce = $wrapper->getWrap ( $RespArray );
		
		return $Responce;
	}
	
	public function getWrapper() {
		return $this->BackArtWraper;
	}
	
	public static function getmicrotime() {
		list ( $usec, $sec ) = explode ( " ", microtime () );
		return (( float ) $usec + ( float ) $sec);
	}

}

?>