<?php
require_once 'citro/error/ErrorCodes.php';

class LogException extends Exception {
	
	private $LogCode = 0;
	
	public function __construct(ErrorCodes $LogCode, $message, $code = 0, $previous = NULL) {
		
		$this->LogCode = $LogCode;
		// sicherstellen, dass alles korrekt zugewiesen wird
		parent::__construct ( $message, $code, $previous );
	}
	
	// ma�geschneiderte Stringdarstellung des Objektes
	public function __toString() {
		return __CLASS__ . ": [{$this->LogCode->getErrorCodeStr() }]: {$this->message}\n";
	}
	
	public function getLogCode() {
		// if(is_a($this->LogCode, "ErrorCodes")){
		// return $this->LogCode->getErrorCodeStr();
		// }
		return $this->LogCode;
	}

}

?>