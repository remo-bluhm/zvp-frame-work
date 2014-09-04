<?php

class MailNewUser {
	
	private $SmtpProvider = NULL;
	private $SendText = "no Text(Mail New User)";
	
	function __construct() {
		
		// Smtp Connexting
		// ist f�r das senden der Mail �ber den Smtp Provider
		$config = array ('auth' => 'login', 'username' => "p40180p3", 'password' => "doSGFHuJ" );
		
		require_once 'Zend/Mail/Transport/Smtp.php';
		$this->SmtpProvider = new Zend_Mail_Transport_Smtp ( "mail.remoseine.de", $config );
	
	}
	
	public function Send($addToMail, $addToName = "") {
		
		require_once 'Zend/Mail.php';
		$mail = new Zend_Mail ();
		$mail->setBodyHtml ( $this->SendText );
		// $mail->setBodyText("<b>Du wurdest Regestriert</b>");
		$mail->setFrom ( "remo@baeder-tourist.de", "Remo Bluhm (Baeder Tourist)" );
		$mail->addTo ( $addToMail, $addToName );
		$mail->setSubject ( "Anmeldung" );
		
		try {
			$mailBack = $mail->send ( $this->SmtpProvider );
			return TRUE;
		} catch ( Exception $e ) {
			return FALSE;
		
		}
	
	}
	
	/**
	 * Sendet eine Mail an den Sender
	 *
	 * @param $RealName unknown_type       	
	 * @param $LoginName unknown_type       	
	 * @param $PassWordBlank unknown_type       	
	 * @param $GuId unknown_type       	
	 */
	public function WriteText($VonUser, $RealName, $LoginName, $PassWordBlank, $GuId) {
		
		$this->SendText = "Sehr geehrter Herr/Frau " . $RealName . " <br />
		Sie wurden soeben von " . $VonUser . " beim Service Bäder Tourist regestriert. <br />
		<br />
		Ihr Loginname ist: " . $LoginName . " <br />
		Ihr Password ist: " . $PassWordBlank . " <br />
		Bitte Loggen Sie sich unter www.bt-phoenix.de ein und ändern Ihr Loginname und Ihr Password <br />
		<br />
		Ihre GuId ist: " . $GuId . " <br />
		<br />
		";
	
	}

}

?>