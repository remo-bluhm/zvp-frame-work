<?php
require_once 'citro/service-class/AService.php';
require_once 'citro/DBTable.php';


/**
 * class systemTrans
 *
 * Achtung schreibt das Alte system in das neue
 *
 * @author :
 *        
 */
class ServiceTrans extends AService {
	
	/**
	 * systemTrans constructor
	 *
	 * @param       	
	 *
	 */
	function __construct() {
		
		parent::__construct ();
	
	}
	
	
	
	

	
	/**
	 * Arbeitet die Transaction ab 
	 * @return bool ob die Transaction erfolgreich war
	 */
	 public function ActionTrans() {
		
	 	require_once 'citro/DBConnect.php';
	 	$DBCon = DBConnect::getConnect ();
		 	
	 	// Leehre alle Tabellen
	 	$this->ActionSetTableClean();
		
	 	//################################################
	 	// Erstelle BuchungsStelle bust
	 	require_once 'service/Contact/ServiceContact.php';
	 	$servCon = new ServiceContact();
	 	// 1 Zinnowitz
	 	$dataZinno = array();
	 	$dataZinno[contacts::SP_FIRMA] =  "Bäder Tourist" ;
	 	$dataZinno[contacts::SP_FIRST_NAME] =  "Ester" ;
	 	$dataZinno[contacts::SP_LAST_NAME] =  "Schulze" ;
	 	$dataZinno[contacts::SP_PLZ] =  "17454" ;
	 	$dataZinno[contacts::SP_ORT] =  "Zinnowitz" ;
	 	$dataZinno[contacts::SP_LAND] =  "Deutschland" ;
	 	$dataZinno[contacts::SP_TELEFON] =  "038377/40807";
	 	$kontaktRow = $kontaktTab->newKontaktRow($this->_MainUser->getId(), contacts::CATECORY_BOOKINGKONTAKT, $dataZinno);
	 	$gastgeberId = $servCon->ActionNew($gastgeberNummer."-".$zimmerNummer,  array());
	 	
	 	$zinno_id = $kontaktRow->offsetGet(contacts::SP_ID);
	 	
	 	// 2 Koserow
	 	$dataKose = array();
	 	$dataKose[contacts::SP_FIRMA] =  "Bäder Tourist" ;
	 	$dataKose[contacts::SP_FIRST_NAME] =  "Kristina" ;
	 	$dataKose[contacts::SP_LAST_NAME] =  "Bluhm" ;
	 	$dataKose[contacts::SP_PLZ] =  "17454" ;
	 	$dataKose[contacts::SP_ORT] =  "Koserow" ;
	 	$dataKose[contacts::SP_LAND] =  "Deutschland" ;
	 	$dataKose[contacts::SP_TELEFON] =  "038375/21062";
	 	$kontaktRow = $kontaktTab->newKontaktRow($this->_MainUser->getId(), contacts::CATECORY_BOOKINGKONTAKT, $dataKose);
	 	$kose_id = $kontaktRow->offsetGet(contacts::SP_ID);
	 	
	 	// 3 Karlshagen
	 	$dataKarlsh = array();
	 	$dataKarlsh[contacts::SP_FIRMA] =  "Bäder Tourist";
	 	$dataKarlsh[contacts::SP_FIRST_NAME] =  "Wilfried";
	 	$dataKarlsh[contacts::SP_LAST_NAME] =  "Schulze" ;
	 	$dataKarlsh[contacts::SP_PLZ] =  "17449";
	 	$dataKarlsh[contacts::SP_ORT] =  "Karlshagen" ;
	 	$dataKarlsh[contacts::SP_LAND] =  "Deutschland" ;
	 	$dataKarlsh[contacts::SP_TELEFON] =  "038371/20815";
	 	$kontaktRow = $kontaktTab->newKontaktRow($this->_MainUser->getId(), contacts::CATECORY_BOOKINGKONTAKT, $dataKarlsh);
	 	$karlsh_id = $kontaktRow->offsetGet(contacts::SP_ID);
	 	
	 	
	 	
	 	$select = $DBCon->select ();
	 	$select->FROM ( 'haupttabelle' );
	 	$select->where ( 'Verfg = ?', 1 );
	 	$stmt = $select->query ();
	 	$allHaupttabelle = $stmt->fetchAll();
	 		 	

	 	
	 	
	 	//require_once 'citro/db/zimmer/zimmer_art.php';
	 	foreach ($allHaupttabelle as $HTZeile){
	 		
	 		// Die Buchungsnummern
	 		$gastgeberNummer = $HTZeile["BuNr"];
	 		$zimmerNummer = $HTZeile["BuNr2"];
	 		
	
	 		// Appartement Insert Array
	 		$appInsert = array();
	
	 		
	 		/// Erstellen des kontaktes (Gastgeber)
// 	 		require_once "db/contact/contacts.php";
	 		
	 		
	 		
// 	 		$kontaktTab = new contacts();
// 	 		$kontaktRow = $kontaktTab->newContact($this->_MainUser->getId(), contacts::CATECORY_RENTER, $kdata);
// 	 		$gastgeberId = $kontaktRow->offsetGet(contacts::SP_ID);
	 		

	 		$gastgeberId = $servCon->ActionNew($gastgeberNummer."-".$zimmerNummer,  array());
	 		
	 		
	 		$appInsert['contact_id'] = $gastgeberId;
	 		
	 		
	 		
// 	 		//### contact_id #################################### 
// 	 		// erstellen des Gastgeberst
// 	 		require_once 'db/ggnr/ggnr.php';
// 	 		$ggTab = new ggnr();
// 	 		$ggSel = $ggTab->select();
// 	 		$ggSel->where(ggnr::SP_CONTACT_NR." = ? ",$gastgeberNummer);
	 		
// 	 		$ggOne = $ggTab->fetchRow($ggSel);
	 	
// 	 		if($ggOne === NULL){
	 		
// 	 			/// Erstellen des kontaktes (Gastgeber)
// 	 			$kdata = array();
// 	 			$kdata[contacts::SP_LAST_NAME] =  $gastgeberNummer."-".$zimmerNummer ;
	 				
// 	 			$kontaktTab = new contacts();
// 	 			$kontaktRow = $kontaktTab->newKontaktRow($this->_MainUser->getId(), contacts::CATECORY_RENTER, $kdata);
// 	 			$gastgeberId = $kontaktRow->offsetGet(contacts::SP_ID);
	 		
// 	 		}else {
// 	 			$gastgeberId = $ggOne->offsetGet(contacts::SP_ID);
// 	 		}
// 	 		$appInsert['contact_id'] = $gastgeberId;
	 		
	 		
	 	
	 		
	 		
	 		//#### art ###############################################
	 		// erstellen oder hollen der zimmer art
	 		require_once 'service/Amenities/ServiceAmenities.php';
	 		$servAm = new ServiceAmenities();	
	 		$appArt = $servAm->ActionExistElementName("APT", $HTZeile["Art"]);
	 		
	 		if($appArt === FALSE){
	 			$appArt = $servAm->ActionGetRootElement("APT");
	 		
	 			$appArt =	$servAm->ActionNewElement ( $appArt["element_id"] , $HTZeile["Art"]  );
	 		}
	 		$appInsert['art'] = $appArt['name'];
	 
	 		
	 		//### bookingcontact_id ################################
	 		// BuchungsstellenId
	 		$buchStelleId = $zinno_id;
	 		if($HTZeile["bust"] == 1) $buchStelleId = $zinno_id;
	 		if($HTZeile["bust"] == 2) $buchStelleId = $kose_id;
	 		if($HTZeile["bust"] == 3) $buchStelleId = $karlsh_id;
	 		$appInsert['bookingcontact_id'] = $buchStelleId;
	 		
	 		
	 		//#### Orts Key ###############################################
	 		// erstellen oder hollen des Ortskeys
	 		
	 		
	 		require_once 'service/Orte/ServiceOrte.php';
	 		$servOrt = new ServiceOrte();
	 		$servOrt->_setRightsAcl($this->_rightsAcl);
	 		
	 		$ort = $servOrt->ActionExist( $HTZeile["Ort"] );
	 		
	 		if($ort === FALSE){
	 			$isNewOrt = $servOrt->ActionNewOrt($HTZeile["Ort"]);
	 			$ort = $servOrt->ActionExist( $HTZeile["Ort"] );
	 	
	 		}
	 		$appInsert['orts_id'] = $ort["id"];
	 		
	 		
	 		//### resourt_id #######################################
	 		require_once 'db/resort/resort.php';
	 		$resourtData = array();
	 		$resourtData['name'] = $gastgeberNummer."-".$zimmerNummer;
	 		$resourtData['edata'] = DBTable::DateTime();
	 		$resourtData['vdata'] = DBTable::DateTime();
	 		$resourtData['usercreate'] = $this->_MainUser->getGuId();
	 		$resourtData['useredit'] = $this->_MainUser->getGuId();
	 		$resourtData['deleted'] = 0;
	 		$resourtData['ort_id'] = $ort["id"];
	 		
	 		$DBCon->insert("bt_resort", $resourtData);
	 		$appInsert['resort_id'] = $DBCon->lastInsertId("bt_resort","id");
	 	
	 		
	 		
	 		//## date_create ######################################
	 		$appInsert['date_create'] = DBTable::DateTime();
	 		
	 		//## date_edit ######################################
	 		$appInsert['date_edit'] = DBTable::DateTime();
	 			 		
	 		//## user_create ######################################
	 		$appInsert['user_create'] = $this->_MainUser->getGuId();
	 		
	 		//## user_create ######################################
	 		$appInsert['user_edit'] = $this->_MainUser->getGuId();
	 		
	 		//## user_create ######################################
	 		$appInsert['sperre'] = 0;
	 		
	 		//## user_create ######################################
	 		$appInsert['visibil'] = 1;
	 		
	 		//## user_create ######################################
	 		$appInsert['deleted'] = 0;
	 		
	 		//## user_create ######################################
	 		$appInsert['gg_type'] = "VM";
	 		
	 		//## Adresszusatz ######################################
	 		$appInsert['adr_zusatz'] = "";
	 		
	 		//## Name ######################################
	 		$appInsert['name'] = $gastgeberNummer."-".$zimmerNummer;
	 		
	 		//## Mindestmitzeit ######################################
	 		$appInsert['mi_mzeit'] = 2;
	 		
	 		//## Anreise Tage ######################################
	 		$appInsert['anr_tage'] = "123456";
	 		
	 		//## Personen Maximal ######################################
	 		$appInsert['person_max'] = $HTZeile["Personenbis"];
	 		
	 		//## Personen Optimal ######################################
	 		$appInsert['person_opt'] = $HTZeile["Personenvon"];
	 		
	 		//## Schlafzimmer ######################################
	 		$appInsert['bedroom'] = $HTZeile["Schlaf"];
	 		
	 		
	 		if($HTZeile['Verpfleg'] == 'mit Frühstück'){
	 			$appInsert['verpfl_art'] = 'B';
	 		}else{
	 			$appInsert['verpfl_art'] = 'S';
	 		}
	 			 		
	 		//## Entfernung Strand ######################################
	 		$appInsert['entf_main'] = $HTZeile["Entfernung"];
	 		
	 		//## Wohnungsgröße ######################################
	 		$appInsert['qm'] = 0;
	 		
	 		//## Haustier erlaubt ######################################
	 		if($HTZeile['Haustier'] == 'ja'){
	 			$appInsert['pet_allow'] = 1;
	 		}else{
	 			$appInsert['pet_allow'] = 0;
	 		}
	 		
	 		//## Barriere-free ######################################
	 		//$appInsert['barriere-free'] = NULL;
	 		
	 		//## Etagen ######################################
	 		//$appInsert['ground_level'] = NULL;
	 		
	 		//## Etagen ######################################
	 		$appInsert['kurztext'] = "";
	 		
	 	
	 
	 		
	 		//## Anzahl der Zimmer #######################################################
	 		$appInsert['number_of_rooms'] = (integer) $HTZeile["Wohnung"] +  (integer) $HTZeile["Kombi"] + (integer) $HTZeile["Schlaf"];
	 		
// 	 		echo "<pre>";
// 	 		print_r($appInsert);
	 		
// 	 		die();
	 		
	 		$DBCon->insert("bt_apartment", $appInsert);
	 		$appId = $DBCon->lastInsertId("bt_apartment","id");
	 		
	 		//######################################
	 		// einschreiben der Gastgebernummer
	 		$ggNrBind = array();
	 		$ggNrBind['gastgeber_nr'] = $gastgeberNummer;
	 		$ggNrBind['zimmer_nr'] = $zimmerNummer;
	 		$ggNrBind['zimmer_id'] = $appId;
	 		$ggNrBind['bt_kontakt_id'] = $gastgeberId;
	 		
	 		require_once 'db/ggnr/ggnr.php';
	 		$ggTab = new ggnr();
	 		$ggTab->insert($ggNrBind);
	 		
	 		
	 		
// 	 		//## Zimmerzusätze #######################################################
// 	 		if($HTZeile['Koch'] === 'Kochnische' || $HTZeile['Koch'] === 'Küche' ){
	 			
// 	 			$apKueche = $servAm->ActionExistElementName("APP", "Küche");
	 			
// 	 			if($apKueche === FALSE){
// 	 				$appOrtRoot = $servAm->ActionGetRootElement("ORT");
// 	 				$apKueche =	$servAm->ActionNewElement ( $appOrtRoot["element_id"] , "Küche" );
// 	 			}
	 			
	 			
// 	 			$appInsert['orts_name_key'] = $apKueche['element_id'];
	 			
// 	 		}
	 		

	 		
	 		
// 	 		// erstellen Resourt Ort
// 	 		$orte = new resort_orte();
// 	 		$ort = $orte->ortExist($HTZeile["Ort"]);
// 	 		if($ort !== FALSE){
// 	 			$ortsId = $ort[resort_orte::SP_ID];	
// 	 		}else{
// 	 			$data = array();
// 	 			$data[resort_orte::SP_ORT_NAME] = $HTZeile["Ort"];
// 	 			$data[resort_orte::SP_DATA_CREATE] = DBTable::getDateTime($HTZeile["Time"]);
// 	 			$data[resort_orte::SP_DATA_EDIT] = DBTable::getDateTime() ;
// 	 			$data[resort_orte::SP_USER_CREAT] = $this->_MainUser->getId() ;
// 	 			$data[resort_orte::SP_USER_EDIT] =  $this->_MainUser->getId() ;
	 		
// 	 			$ortRow =  $orte->createRow($data);
// 	 			$ortRow->save();
// 	 			$ortsId = $ortRow->offsetGet(resort_orte::SP_ID);
// 	 		}
	 		
	 		
	 		
	 		
	 		
// 	 		// Erstellen des Main Resourt
// 	 		$rdata = array();
// 	 		$rdata[resort::SP_ORT_ID] = $ortsId;
// 	 		$rdata[resort::SP_DATA_CREATE] = DBTable::getDateTime($HTZeile["Time"]);
// 	 		$rdata[resort::SP_DATA_EDIT] = DBTable::getDateTime() ;
// 	 		$rdata[resort::SP_USER_CREAT] = $this->_MainUser->getId() ;
// 	 		$rdata[resort::SP_USER_EDIT] =  $this->_MainUser->getId() ;
	 		
// 	 		$resortTab = new resort();
// 	 		$resortRow =  $resortTab->createRow($rdata);
// 	 		$resortRow->save();
// 	 		$resortId = $resortRow->offsetGet(resort::SP_ID);
	 			 		
	 			 		
	 		
	 		
	
	 		
			

			//mit Frühstück

			

				
			
			

//			
// 			require_once 'db/apartment/apartment.php';
// 	 		// Erstellen Zimmer
// 			$zimmerData = array();
// 			$zimmerData[apartment::SP_SPERR] = 0 ;
// 			$zimmerData[apartment::SP_VISIBIL] = 1 ;
// 			$zimmerData[apartment::SP_DELETED] = 0 ;
			
			
// 			$zimmerData[apartment::SP_ENTF_MAIN] = $HTZeile["Entfernung"] ;
// 			$zimmerData[apartment::SP_PERSON_MAX] = $HTZeile["Personenbis"] ;
// 			if($HTZeile["Haustier"] == "ja"){
// 				$zimmerData[apartment::SP_PET_ALLOW] = 1 ;
// 			}
			
// 			//if($HTZeile["Verpfleg"] ==  "mit Frühstück") $zimmerData[apartment::SP_VERPF_ART] = "B" ;  // Breakfest mit Frühstück
// 			if($HTZeile["Verpfleg"] ==  "mit Frühstück") {$zimmerData[apartment::SP_VERPF_ART] = "B" ;  // Breakfest mit Frühstück
// 			}else{$zimmerData[apartment::SP_VERPF_ART] = "S" ; }
// 			if($HTZeile["Bad"] ==  "Du/WC") {
// 				//$ausstattungen["Dusche"]
// 				$zimmerData[zimmer::SP_BA_DUSCHE] = "B" ;
				
				
// 			}
// 			if($HTZeile["Bad"] ==  "Bad/WC"){
// 				$zimmerData[zimmer::SP_BA_WANNE] = "B" ;
// 			}
// 			if($HTZeile["Bad"] ==  "Bad/Du/WC"){
// 				$zimmerData[zimmer::SP_BA_WC] = "B" ;
// 			}
			
// 			if($HTZeile["Kinderbett"] ==  "ja"){
// 				$zimmerData[zimmer::SP_BA_WANNE] = "B" ;
// 			}
// 			if($HTZeile["Kinderbett"] ==  "nein"){
// 				$zimmerData[zimmer::SP_BA_WC] = "B" ;
// 			}
			
			
// 			if($HTZeile["Koch"] ==  "Kochnische"){
// 				$zimmerData[zimmer::SP_BA_WANNE] = "B" ;
// 			}
// 			if($HTZeile["Koch"] ==  "Küche"){
// 				$zimmerData[zimmer::SP_BA_WC] = "B" ;
// 			}

			
// 			if($HTZeile["Koch"] ==  "keine Küche"){
// 				$zimmerData[zimmer::SP_BA_WANNE] = "B" ;
// 			}
	
// 			if($HTZeile["Wohnung"] ==  1){
// 				$zimmerData[zimmer::SP_BA_WANNE] = "B" ;
// 			}
// 			if($HTZeile["Kombi"] ==  1){
// 				$zimmerData[zimmer::SP_BA_WC] = "B" ;
// 			}
// 			if($HTZeile["Schlaf"] == 1){
// 				$zimmerData[zimmer::SP_BA_WANNE] = "B" ;
// 			}
// 			if($HTZeile["Schlaf"] == 1){
// 				$zimmerData[zimmer::SP_BA_WANNE] = "B" ;
// 			}
			
// // 			Hauptsaison 21.Juni - 31.August
// // 			Nebensaison	1.Mai - 20.Juni
// // 						1.September - 31.Oktober
// // 			Saisonfern	1.November - 30.April
			
// 			if($HTZeile["PreisHS"] ==  1){
// 				$zimmerData[zimmer::SP_BA_WANNE] = "B" ;
// 			}
// 			if($HTZeile["PreisNS"] ==  1){
// 				$zimmerData[zimmer::SP_BA_WC] = "B" ;
// 			}
// 			if($HTZeile["PreisSF"] == 1){
// 				$zimmerData[zimmer::SP_BA_WANNE] = "B" ;
// 			}
// 			if($HTZeile["zuPreis"] == 1){
// 				$zimmerData[zimmer::SP_BA_WANNE] = "B" ;
// 			}
			
// 			// Bettwariationen Bbwz  Bbws  Bbsz
			
// 			// Haupt Text Text
// 	 		$zimmerTab = new zimmer();
// 	 		$zimmerRow = $zimmerTab->setNewZimmer($this->_MainUser->getId() ,$kontaktId,$resortId,$zimmerArtId,$buchStelleId,TRUE ,$zimmerData	);
	 		 
	 
	 		
// 	 		// Erstellen GGNR
	 		
	 
	 		
	 	
	 	}
	 	
	 	
	 	return  array("isok"=>"1" );
	 	
	 	
	 	
// 		$KochArt = $this->getArtenSystem ( $DBCon, "Koch", "kocharten" );
// 		$BadArt = $this->getArtenSystem ( $DBCon, "Bad", "badarten" );
// 		$VerpflArt = $this->getArtenSystem ( $DBCon, "Verpfleg", "verpflarten" );
		
// 		$HT = $this->getHaupttabelle ( $DBCon );
		
// 		$this->SetTabelle ( $HT, $KochArt, $BadArt, $VerpflArt );
		
 		return TRUE;
	
	}
	
	/**
	 * Leeren der Tabellen
	 * @return boolean
	 */
	function ActionSetTableClean() {
		
		require_once 'citro/DBConnect.php';
		$DBCon = DBConnect::getConnect ();
		
		require_once 'service/Amenities/ServiceAmenities.php';
		$servAm = new ServiceAmenities();
		$servAm->ActionDeleteRoot("APT");
		$servAm->ActionNewRoot("APT", "Appartment Type", "stellt die Appartmenttypen dar");
	
		$servAm->ActionDeleteRoot("APG");
		$rootok =  $servAm->ActionNewRoot("APG", "Appartment Gastgebertypen", "Setllt die Vertragsart der gastgeber dar");
		
		$servAm->ActionDeleteRoot("APP");
		$rootok =  $servAm->ActionNewRoot("APP", "Appartment Zusätze", "Beinhaltet alle Zusätze des Zimmers aufgeteil in Räumen");
		
	
		
		// löschen der User
		$contDelWhere = array();
		$contDelWhere[] = "category = 'REN' OR category = 'BOK' ";
		$DBCon->delete("bt_contacts",$contDelWhere);
				
		
		// löschen der Buchungsnummer
		$DBCon->query ( "TRUNCATE TABLE usr_p41239_3.bt_ggnr" );
		

		

		
		// ist verantwortlich für den cach der Desctopapplication	
		$DBCon->query ( "TRUNCATE TABLE usr_p41239_3.bt_desktop_app_cach" );
		// die zimmer	
		$DBCon->query ( "TRUNCATE TABLE usr_p41239_3.bt_apartment" );
		//$DBCon->query ( "TRUNCATE TABLE usr_p41239_3.bt_apartment_art" );
		$DBCon->query ( "TRUNCATE TABLE usr_p41239_3.bt_apartment_prices" );
		$DBCon->query ( "TRUNCATE TABLE usr_p41239_3.bt_apartment_entries" );
		
		
		// die Objekte
		$DBCon->query ( "TRUNCATE TABLE usr_p41239_3.bt_resort" );
		$DBCon->query ( "TRUNCATE TABLE usr_p41239_3.bt_resort_orte" );
		$DBCon->query ( "TRUNCATE TABLE usr_p41239_3.bt_resort_orte_match" );
		$DBCon->query ( "TRUNCATE TABLE usr_p41239_3.bt_resort_orte_region" );
		
	
		

		
		// löschen aller User die Touristen oder vermieter sind
		$DBCon->query ( "DELETE FROM usr_p41239_3.bt_contacts WHERE category='TOU' or category ='REN' " );
			
			
			//$DBCon->query ( "TRUNCATE TABLE usr_p41239_3.bt_zimmer_ausst" );
			
			//$DBCon->query ( "TRUNCATE TABLE usr_p41239_3.bt_zimmer_ausst_tab" );
			//$DBCon->query ( "TRUNCATE TABLE usr_p41239_3.bt_zimmer_bettvariation" );
			//$DBCon->query ( "TRUNCATE TABLE usr_p41239_3.bt_zimmer_buchstellen" );
			
			//$DBCon->query ( "TRUNCATE TABLE usr_p41239_3.bt_zimmer_geeignet" );
			//$DBCon->query ( "TRUNCATE TABLE usr_p41239_3.bt_zimmer_geeignet_tab" );
			
			//$DBCon->query ( "TRUNCATE TABLE usr_p41239_3.bt_zimmer_preis_zusatz" );
			//$DBCon->query ( "TRUNCATE TABLE usr_p41239_3.bt_zimmer_raumvariation" );
			//$DBCon->query ( "TRUNCATE TABLE usr_p41239_3.bt_zimmer_text" );
			


			
			
// 			$DBCon->query ( "TRUNCATE TABLE usr_p41239_3.bt_kontakt" );
// 			$DBCon->query ( "TRUNCATE TABLE usr_p41239_3.bt_kontakt_adress" );
// 			$DBCon->query ( "TRUNCATE TABLE usr_p41239_3.bt_kontakt_internet" );
// 			$DBCon->query ( "TRUNCATE TABLE usr_p41239_3.bt_kontakt_telefon" );
// 			$DBCon->query ( "TRUNCATE TABLE usr_p41239_3.bt_kontakt_search" );

			
			return TRUE;
		
	}
	
	
	

	
	
	
	
	
	
	function getArtenSystem($DBCon, $Spalte, $bezeichner) {
		$Art = array ();
		$sart = new systemArten ( $DBCon ); // Zimmerarten
		$HtSpal = $this->gethaupttabellearten ( $Spalte, $DBCon );
		
		foreach ( $HtSpal as $HTElm ) {
			if (! empty ( $HTElm [$Spalte] )) {
				$idArt = $sart->newInsert ( $bezeichner, $HTElm [$Spalte] );
				$Art [$HTElm [$Spalte]] = $idArt;
			}
		}
		return $Art;
	}
	
	function getHaupttabelle() {
		
		$DBCon = Zend_Registry::get ( VAR_REG_DB );
		
		$select = $DBCon->select ();
		// Hinzuf�gen einer FROM Bedingung
		$select->FROM ( 'haupttabelle' );
		// Hinzuf�gen einer WHERE Bedingung
		$select->where ( 'Verfg = ?', 1 );
		$back = $select->query ();
		return $back->fetchAll ();
	}
	
	function SetTabelle($HT, $KochArt, $BadArt, $VerpflArt) {
		
		$DBCon = Zend_Registry::get ( VAR_REG_DB );
		$DateString = "Y-m-d H:i:s";
		
		if (is_array ( $HT )) {
			
			// $zaeler = 1;
			foreach ( $HT as $Elem ) {
				
				$expNow = new Zend_Db_Expr ( 'NOW()' );
				
				$fewoKochart = $KochArt [$Elem ['Koch']];
				$fewoBad = $BadArt [$Elem ['Bad']];
				$fewoVerpfl = $VerpflArt [$Elem ['Verpfleg']];
				
				$ZimmerData = array (

				'obj_nr' => $Elem ['BuNr'], 'zim_nr' => $Elem ['BuNr2'], 'edatum' => date ( $DateString, $Elem ['Time'] ), 'vdatum' => date ( $DateString, $Elem ['Updat'] ), 'sperre' => $Elem ['Verfg'] == 1 ? 0 : 1, 'man_sperr' => 0, 'usercreat' => 1, 'useredit' => 1 )

				;
				
				$DBZimmer = new Zimmer ( $DBCon );
				$DBZimmer->insert ( $ZimmerData );
				$ZimmerDataId = $DBCon->lastInsertId ();
				
				// kann als pr�fung mit aufgenommen werden
				/*
				 * $Achtung = ""; if($id !=
				 * $zaeler){$Achtung="############Achtung############";
				 * $zaeler++;
				 */
				
				$Elem ['Haustier'] == 'ja' ? $haustier = 1 : $haustier = 0;
				$Elem ['Kinderbett'] == 'ja' ? $geeigkinder = 1 : $geeigkinder = 0;
				
				$ZimmerStandData = array (

				'bt_zimmer_id' => $ZimmerDataId, 'kurztext' => "", 'verpfl_art' => $fewoVerpfl, 'entf_strand' => $Elem ['Entfernung'], 

				'koch_art' => $fewoKochart, 'bad_art' => $fewoBad, 

				'zimanz_wohn' => $Elem ['Wohnung'], 'zimanz_komb' => $Elem ['Kombi'], 'zimanz_schlaf' => $Elem ['Schlaf'], 'zimanz_bad' => 0, 

				'geeig_kinder' => $geeigkinder, 'geeig_behind' => 0, 

				'geeig_haustier' => $haustier, 'geeig_raucher' => 0, 'geeig_mehrblick' => 0, 'geeig_senj' => 0, 

				'auss_balkon' => 0, 'auss_terrasse' => 0, 'auss_garten' => 0, 'auss_waschm' => 0, 'auss_spuelm' => 0, 'auss_internet' => 0, 'auss_sauna' => 0, 'auss_kamin' => 0, 'auss_sattv' => 0, 'auss_grill' => 0, 'auss_fusheiz' => 0, 

				'wohgroese' => 0 );
				$DBZimmerStand = new ZimmerStand ( $DBCon );
				$DBZimmerStand->insert ( $ZimmerStandData );
				
				$ZimmerFullData = array ('bt_zimmer_id' => $ZimmerDataId, 'buchungsstelle' => $Elem ['bust'], 'preis_endreinigung' => null, 'preis_zusatz' => $Elem ['zuPreis'], 'betten_wohnraum' => $Elem ['Bbwz'], 'bettem_kombi' => $Elem ['Bbws'], 'betten_schlafraum' => $Elem ['Bbsz'], 'text' => $Elem ['Text'] );
				
				$DBZimmerFull = new ZimmerFull ( $DBCon );
				$DBZimmerFull->insert ( $ZimmerFullData );
			}
		}
	
	}
}

class Zimmer extends DBTable {
	protected $_name = 'bt_zimmer';
}
class ZimmerStand extends DBTable {
	protected $_name = 'bt_zimmerstand';
}
class ZimmerFull extends DBTable {
	protected $_name = 'bt_zimmerfull';
}
class systemArten {
	
	/**
	 * //	* @var Zend_Db_Adapter_Pdo_Mysql $DBcon Enth�llt die Connectionsclasse
	 * //
	 */
	private $DBcon;
	private $TabName = "bt_zimmer_arten";
	private $MaxItemSort = 250;
	private $langSpaltName = 45;
	private $langKurztext = 100;
	
	function systemArten($DB) {
		$this->DBcon = $DB;
	}
	
	public function newInsert($sysName, $sysText, $sysInMenu = true) {
		
		if ($sysInMenu == true) {
			$sysInMenu = 1;
		} else {
			$sysInMenu = 0;
		}
		
		if (strlen ( $sysName ) > $this->langSpaltName) {
			$sysName = substr ( $sysName, 0, $this->langSpaltName );
		}
		
		if (strlen ( $sysText ) > $this->langKurztext) {
			$sysText = substr ( $sysText, 0, $this->langKurztext );
		}
		
		$queryMaxItem = "SELECT COUNT(*) FROM " . $this->TabName . " WHERE tab_spalt_name='$sysName' ";
		$MaxItem = $this->DBcon->fetchOne ( $queryMaxItem );
		
		if ($MaxItem > $this->MaxItemSort)
			return $id = null;
		
		$queryTest = "SELECT id FROM " . $this->TabName . " WHERE tab_spalt_name='$sysName' AND kuztext='$sysText' ";
		$testvalue = $this->DBcon->fetchOne ( $queryTest );
		
		if ($testvalue > 0) {
			
			return $testvalue;
		} else {
			$query = "INSERT INTO " . $this->TabName . "( tab_spalt_name, kuztext, menue_on ) VALUES ('" . $sysName . "', '" . $sysText . "', '" . $sysInMenu . "' )";
			$this->DBcon->query ( $query );
			$id = $this->DBcon->lastInsertId ();
			return $id;
		
		}
	}
	
	
	
	// 	public function main() {
	// 		global $STCONFIG;
	
	// 		$this->FetchArray = array ();
	
	// 		switch ($this->ParamObjekt->getParam ( "action" )) {
		
	// 			case "tabletruncate" :
	// 				$this->FetchArray [] ["tabletruncate"] = $this->setTableTruncate ();
	// 				break;
		
	// 			case "systemtrans" :
	// 				$this->FetchArray [] ["systemtrans"] = $this->setSystemTransStart ();
	// 				break;
		
	// 			case "gethaupttabelle" :
	// 				$this->FetchArray = $this->getHaupttabelle ();
	// 				break;
		
	// 			default :
	// 				break;
	
	// 		}
	
	// 		// $DateString = "Y-m-d H:i:s";
	
	// 	}

}
?>