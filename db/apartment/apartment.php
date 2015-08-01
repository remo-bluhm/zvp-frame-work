<?php

/**
 * class zvp_zimmer
 *
 * Description for class zvp_zimmer
 *
 * @author:
*/
class Apartment extends DBTable {
	protected $_name = 'apartment';
	
	const SP_ID = "id";
	
	const SP_OWNER_ID = "owner_id";
	const SP_APARTM_ART_ID = "art_id";
	const SP_BOOKINGKONTAKT_ID = "bookingcontact_id";
	const SP_RESORT_ID = "resort_id";
	const SP_APARTM_NAME_UID = "name_uid";// ist ein einmaliger Wer für das zimmer der keine lerrzeichen enthalten darf oder sonderzeichen auser minus
	const SP_APARTM_NAME = "name";// ist ein einmaliger Wer für das zimmer der keine lerrzeichen enthalten darf oder sonderzeichen auser minus
	
	
	
	const SP_DATA_CREATE = "date_create";
	const SP_DATA_EDIT = "date_edit";
	const SP_ACCESS_CREAT = "user_creat";
	const SP_ACCESS_EDIT = "user_edit";
	
	
	const SP_SPERR = "sperre";
	const SP_VISIBIL = "visibil";
	const SP_DELETED = "deleted";
	
	const SP_GG_TYPE = "gg_typ"; // VertragsArt
	const SP_ADR_ZUSATZ = "adr_zusatz";// ist für zb. woh1 oder...
	//const SP_ADR_NAME = "name"; 
	
	const SP_ADR_ORT = "orts_id";
	
	const SP_MIN_MZEIT = "mi_mzeit";
	const SP_ANR_TAGE = "anr_tage";
	const SP_BETTEN = "bett";
	const SP_ZUS_BETTEN = "zubett";
	
	const SP_PERSON_MAX = "max_person";
	const SP_VERPF_ART = "verpf_art";
	const SP_KURZTEXT = "kurztext";
	const SP_PREIS_ENDREINIGUNG = "preis_endreinigung"; // sollte hier auch raus 
	const SP_KIND_BETT = "kindbett"; // Ist nicht mehr vorhanen

	const SP_PET_ALLOW = "pet_allow";
	const SP_PET_ALLOW_TEXT = "pet_text";
		
	const SP_BA_DUSCHE = "dusche";
	const SP_BA_WANNE = "bad";
	const SP_BA_WC = "wc";
	
	const SP_ENTF_MAIN = "entf_main"; // sollte auch in resourt verschoben werden
	
	
	private $_insertData = array();
	
	public function clearData(){
		$this->_insertData = array();
	}
	
	public function setNameUid($value){
		$result = self::testApartmName($value);
		if($result !== NULL)$this->_insertData[self::SP_APARTM_NAME_UID] = $result;
		return $result;
	}
	public function setName($value){
		$result = self::testApartmName($value);
		if($result !== NULL)$this->_insertData[self::SP_APARTM_NAME] = $result;
		return $result;
	}
	public function setArt($value){
		$result = self::testApartmentArt($value);
		if($result !== NULL)$this->_insertData[self::SP_APARTM_ART_ID] = $result;
		return $result;
	}
	public function setOwnerId($value){
		$result = DBTable::testId($value);
		if($result !== NULL)$this->_insertData[self::SP_OWNER_ID] = $result;
		return $result;
	}
	public function setBookingontId($value){
		$result = DBTable::testId($value);
		if($result !== FALSE)$this->_insertData[self::SP_TYPE] = $result;
		return $result;
	}
	public function setResourtId($value){
		$result = DBTable::testId($value);
		if($result !== FALSE)$this->_insertData[self::SP_TYPE] = $result;
		return $result;
	}
	public function setOrtId($value){
		$result = DBTable::testId($value);
		if($result !== FALSE)$this->_insertData[self::SP_] = $result;
		return $result;
	}
	public function setAccessCreateId($value){
		$result = DBTable::testId($value);
		if($result !== FALSE)$this->_insertData[self::SP_DATA_CREATE] = $result;
		return $result;
	}
	public function setAccessEditId($value){
		$result = DBTable::testId($value);
		if($result !== FALSE)$this->_insertData[self::SP_DATA_EDIT] = $result;
		return $result;
	}
	
	

	

	
	public function insertDataFull($accessId,$nameUid,$ownerId, $data=array()){
		// Die Pflichtparameter
		$this->setAccessCreateId($accessId);
		
		$apartmName = $this->setNameUid($nameUid);
		
		$resultFind = $this->exist($nameUid);
		
		$ownerIdValue = $this->setOwnerId($ownerId);
		
	
		if($apartmName !== NULL  && $resultFind === FALSE ){
			if(array_key_exists("name",$data)) 		$this->setName($data["name"]);
			
			if(array_key_exists("art_sys_name",$data)){

				require_once 'db/apartment/ApartmentArt.php';
				$apartmArtTab = new ApartmentArt($data["art_sys_name"]);
				
				
				$id = $apartmArtTab->exist($sysName);
				
				var_dump($id);
				
				
				$this->setArt($data["art_sys_name"]); // Die art des Quartieres FZ2,FW1
			}
			if(array_key_exists("bookingcontact_id",$data)) 		$this->setName($data["bookingcontact_id"]); 
			if(array_key_exists("bookingcontact_id",$data)) 		$this->setName($data["bookingcontact_id"]); 
			
			
			
			
			//return $this->insert($this->_insertData);
		}else{
			return NULL;
			//@TODO hier kann noch ein Fehler gesetzt werden
		}
	}
	
	public function insert($data){
		$data[self::SP_DATA_CREATE] = DBTable::DateTime ();
		$data[self::SP_DATA_EDIT] = DBTable::DateTime ();
		return  parent::insert($data);
	}
	
	/**
	 * Prüfft ob ein Apartment existiert
	 * @param string $name
	 * @return bool 
	 */
	public function exist($nameUid){
		
		if($this->getId($nameUid) !== FALSE){
			//Gefunden
			return TRUE;
		}else {
			//nicht gefunden
			return FALSE;
		}
	}
	/**
	 * Findet die Id anhand ihrer Uid
	 * @param string $nameUid
	 * @return int|FALSE
	 */
	public function getId($nameUid){
		$conn = DBConnect::getConnect();
		$value = $conn->fetchOne("select id from apartment where ".$conn->quoteInto("name_uid = ?", $nameUid) );
		if($value === FALSE) return FALSE;
		return (int) $value;
	}
	
 	
 	public function setNewZimmer($userId, $ownerId, $resortId, $artId, $bookingId, $visibil = TRUE, $data = array() ) {
		

		$dataInsert = array();
		
		
		if( self::testGastgeberType($data[self::SP_GG_TYPE]) 	!== NULL) 		$dataInsert[self::SP_GG_TYPE] 		= $data[self::SP_GG_TYPE];
		if( self::testAdressZusatz($data[self::SP_ADR_ZUSATZ]) 	!== NULL) 		$dataInsert[self::SP_ADR_ZUSATZ] 	= $data[self::SP_ADR_ZUSATZ];
		if( self::testAdressName($data[self::SP_ADR_NAME]) 		!== NULL) 		$dataInsert[self::SP_ADR_NAME] 		= $data[self::SP_ADR_NAME];
		if( self::testMinMietZeit($data[self::SP_MIN_MZEIT]) 	!== NULL) 		$dataInsert[self::SP_MIN_MZEIT] 	= $data[self::SP_MIN_MZEIT];
		if( self::testAnreiseTage($data[self::SP_ANR_TAGE]) 	!== NULL)		$dataInsert[self::SP_ANR_TAGE] 		= $data[self::SP_ANR_TAGE];
		
		if( self::testBetten($data[self::SP_BETTEN]) 			!== NULL)		$dataInsert[self::SP_BETTEN] 		= $data[self::SP_BETTEN];
		if( self::testZustellBett($data[self::SP_ZUS_BETTEN]) 	!== NULL)		$dataInsert[self::SP_ZUS_BETTEN] 	= $data[self::SP_ZUS_BETTEN];
		if( self::testKinderBett($data[self::SP_KIND_BETT]) 	!== NULL)		$dataInsert[self::SP_KIND_BETT] 	= $data[self::SP_KIND_BETT];
		
		$dataInsert[self::SP_BA_DUSCHE] 	= self::testDuscheOrWanneOrWc($data[self::SP_BA_DUSCHE]); 
		$dataInsert[self::SP_BA_WANNE] 		= self::testDuscheOrWanneOrWc($data[self::SP_BA_WANNE]); 
		$dataInsert[self::SP_BA_WC] 		= self::testDuscheOrWanneOrWc($data[self::SP_BA_WC]); 

		if( self::testEndreinigungPreis($data[self::SP_PREIS_ENDREINIGUNG]) !== NULL)	$dataInsert[self::SP_PREIS_ENDREINIGUNG]= $data[self::SP_PREIS_ENDREINIGUNG];
		if( self::testVerpfleArt($data[self::SP_VERPF_ART]) 				!== NULL)	$dataInsert[self::SP_VERPF_ART] 		= $data[self::SP_VERPF_ART];
		if( self::testEntfernungMain($data[self::SP_ENTF_MAIN]) 			!== NULL)	$dataInsert[self::SP_ENTF_MAIN] 		= $data[self::SP_ENTF_MAIN];
		if( self::testKurtztext($data[self::SP_KURZTEXT]) 					!== NULL)	$dataInsert[self::SP_KURZTEXT] 			= $data[self::SP_KURZTEXT];
		
		
		
		
		$dataInsert[self::SP_DELETED] = 0;
		$dataInsert[self::SP_SPERR] = 0;
		
		$visibil === FALSE ? $dataInsert[self::SP_VISIBIL] = 0: $dataInsert[self::SP_VISIBIL] = 1;
		

		
		$dataInsert[self::SP_OWNER_ID] = $ownerId;
		$dataInsert[self::SP_RESORT_ID] = $resortId;
		$dataInsert[self::SP_ZIMMER_ART_ID] = $artId;
		$dataInsert[self::SP_BOOKINGKONTAKT_ID] = $bookingId;
		
		
		$dataInsert[self::SP_DATA_CREATE] = DBTable::getDateTime();
		$dataInsert[self::SP_DATA_EDIT] = DBTable::getDateTime() ;
		$dataInsert[self::SP_USER_CREAT] = $userId;
		$dataInsert[self::SP_USER_EDIT] =  $userId ;
	
		$zimmerRow = $this->createRow($dataInsert);
		$zimmerRow->save();

		return $zimmerRow;
	}
	
	/**
	 * Testet die Art des Apartments
	 * @param unknown $value
	 * @return unknown|NULL
	 */
	public static function testApartmentArt($value){
		if(is_int($value) && $value < 100 ){
			return $value;
		}
		return NULL;
	}
	
	public static function testApartmNameUid($value){
		if(is_string($value)&& strlen($value) < 150 ){
			return $value;
		}
		return NULL;
	}
	
	public static function testApartmName($value){
		if(is_string($value)&& strlen($value) < 150 ){
			return $value;
		}
		return NULL;
	}
	
	public static function testGastgeberType($value){
		if(is_string($value)&& strlen($value) < 3 ){
			return $value;
		}
		return NULL;
	}
	
	public static function testAdressZusatz($value){
		if(is_string($value)&& strlen($value) < 12 ){
			return $value;
		}
		return NULL;
	}
	

	public static function testMinMietZeit($value){
		if(is_int($value) && $value < 100 ){
			return $value;
		}
		return NULL;
	}
	public static function testAnreiseTage($value){
		if(is_string($value) && strlen($value) < 8 ){
			return $value;
		}
		return NULL;
	}
	
	
	
	public static function testBetten($value){
		if(is_int($value) && $value < 1000 ){
			return $value;
		}
		return NULL;
	}
	public static function testZustellBett($value){
		if(is_int($value) && $value < 1000 ){
			return $value;
		}
		return NULL;
	}
	public static function testKinderBett($value){
		if(is_int($value) && $value < 1000 ){
			return $value;
		}
		return NULL;
	}
	
	
	public static function testDuscheOrWanneOrWc($value){
		if($value === 1 ){
			return $value;
		}
		return 0;
	}
	
	
	public static function testEndreinigungPreis($value){
		if(is_double($value)){
			return $value;
		}
		return NULL;
	}
	public static function testPreisText($value){
		if(is_string($value) && strlen($value) < 151 ){
			return $value;
		}
		return NULL;
	}	
	public static function testVerpfleArt($value){
		if(is_string($value) && strlen($value) < 4 ){
			return $value;
		}
		return NULL;
	}	
	public static function testEntfernungMain($value){
			return $value;
	
	}
	public static function testKurtztext($value){
		if(is_string($value) && strlen($value) < 255 ){
			return $value;
		}
		return NULL;
	}
	
	
	/**
	 * This is method setUpdateZimmer
	 *
	 * @param $ZimmId mixed
	 *       	 This is a description
	 * @param $ZimType mixed
	 *       	 This is a description
	 * @param $ZimmerArtId mixed
	 *       	 This is a description
	 * @param $OrtId mixed
	 *       	 This is a description
	 * @param $Strasse mixed
	 *       	 This is a description
	 * @param $AdrZus mixed
	 *       	 This is a description
	 * @param $AdrName mixed
	 *       	 This is a description
	 * @param $MinMitZeit mixed
	 *       	 This is a description
	 * @param $AnrTage mixed
	 *       	 This is a description
	 * @param $Bett mixed
	 *       	 This is a description
	 * @param $ZuBett mixed
	 *       	 This is a description
	 * @param $KindBett mixed
	 *       	 This is a description
	 * @param $Dusche mixed
	 *       	 This is a description
	 * @param $Wanne mixed
	 *       	 This is a description
	 * @param $Wc mixed
	 *       	 This is a description
	 * @param $Sperr mixed
	 *       	 This is a description
	 * @param $manSperr mixed
	 *       	 This is a description
	 * @return mixed This is the return value description
	 *        
	 */
// 	public function setUpdateZimmer($ZimmId, $GastgeberType, $ZimmerArtId, $OrtId, $Strasse, $AdrZus, $AdrName, $MinMitZeit, $AnrTage, $Bett, $ZuBett, $KindBett, $Dusche, $Wanne, $Wc, $Sperr = true, $manSperr = true) {
		
// 		$manSperr == "true" ? $SetManSperr = 1 : $SetManSperr = 0;
// 		$Sperr == "true" ? $SetSperr = 1 : $SetSperr = 0;
		
// 		$data = array (

// 		zvp_zimmer::SP_V_DATA => $this->getDateTime (), zvp_zimmer::SP_SPERR => $Sperr, zvp_zimmer::SP_SPERR_MAN => $manSperr, zvp_zimmer::SP_USER_EDIT => VAR_USER_ID, zvp_zimmer::SP_GG_TYPE => $GastgeberType, zvp_zimmer::SP_ZIM_ART_ID => $ZimmerArtId, zvp_zimmer::SP_ZIM_ORT_ID => $OrtId, 

// 		zvp_zimmer::SP_ADR_STRASSE => $Strasse, zvp_zimmer::SP_ADR_ZUSATZ => $AdrZus, zvp_zimmer::SP_ADR_NAME => $AdrName, 

// 		zvp_zimmer::SP_MIN_MZEIT => $MinMitZeit, zvp_zimmer::SP_ANR_TAGE => $AnrTage, zvp_zimmer::SP_BETTEN => $Bett, zvp_zimmer::SP_ZUS_BETTEN => $ZuBett, zvp_zimmer::SP_KIND_BETT => $KindBett, 

// 		zvp_zimmer::SP_BA_DUSCHE => $Dusche, zvp_zimmer::SP_BA_WANNE => $Wanne, zvp_zimmer::SP_BA_WC => $Wc )

// 		;
		
// 		try {
// 			$this->_DBCon->update ( $this->getDBTableName (), $data, zvp_zimmer::SP_ID . "=" . $ZimmId );
// 		} catch ( exception $EXUpdate ) {
// 			throw new exception ( "Der Updatevorgang des zimmers ist Fehlgeschlagen ZimmerId: " . $ZimmId );
// 		}
	
// 	}
	
// 	/**
// 	 * Giebt die Functionsliste zur�ck die der Gruppe zugeh�rt
// 	 *
// 	 * @param $GroupId int
// 	 *       	 die Gruppenid des users
// 	 * @return array() Eine Liste der Functionen Gro�geschrieben zur�ck
// 	 *        
// 	 */
// 	public function getAllZimmer($GroupId) {
		
// 		$select = $this->_DBCon->select ();
		
// 		// Hinzuf�gen einer FROM Bedingung
// 		$select->FROM ( $this->getDBTableName () );
// 		// $select->where( sys_ws_rechte::SP_GROUPID." = ?",$GroupId,'INTEGER');
		
// 		// $select->where( "hauta = 2");
		
// 		// $select->where( $this->_SpVisible." = ?",$visible);
// 		// $select->where( $this->_SPDelete." = ?",$delete);
// 		// echo $select->__toString();
// 		$back = $select->query ();
		
// 		$All = $back->fetchAll ();
		
// 		return $All;
	
// 	}
	
// 	public function sperrManZimmer($id, $isSperr = true) {
// 		if ($isSperr == true) {
// 			$mansperr = 1;
// 		} else {
// 			$mansperr = 0;
// 		}
// 		$idArray = explode ( ",", $id );
		
// 		$data = array (

// 		zvp_zimmer::SP_SPERR_MAN => $mansperr )

// 		;
		
// 		$n = 0;
// 		if (is_array ( $idArray )) {
// 			foreach ( $idArray as $Key => $Value ) {
// 				$where [zvp_zimmer::SP_ID . ' = ?'] = $Value;
// 				$BackZeilenAnz = $this->_DBCon->update ( $this->getDBTableName (), $data, $where );
// 				$n = $n + $BackZeilenAnz;
// 			}
// 		}
		
// 		// zur�ckgeben der Anzahl der betroffenen zeilen
// 		return $n;
// 		// Hinzuf�gen einer FROM Bedingung
	
// 	}
	
// 	/**
// 	 * Pr�ft ob ein Zimmer Existiert nach der Objektnummer und der zimmernummer
// 	 * 
// 	 * @param $ObjNr int
// 	 *       	 Die Objektnummer
// 	 * @param $ZimNr int
// 	 *       	 Die Zimmernummer
// 	 * @return Ambiguous boolean
// 	 */
// 	public function Exist($ObjNr, $ZimNr) {
// 		$select = $this->_DBCon->select ();
// 		$select->FROM ( $this->getDBTableName (), array (zvp_zimmer::SP_ID, zvp_zimmer::SP_OBJ_NR, zvp_zimmer::SP_ZIM_NR ) );
		
// 		$select->where ( zvp_zimmer::SP_OBJ_NR . " = ?", $ObjNr, "INTEGER" );
// 		$select->where ( zvp_zimmer::SP_ZIM_NR . " = ?", $ZimNr, "INTEGER" );
		
// 		// echo $select->__toString();
		
// 		$back = $select->query ();
		
// 		$Zeile = $back->fetch ();
		
// 		if ($Zeile != null) {
			
// 			return $Zeile [zvp_zimmer::SP_ID];
// 		}
		
// 		return false;
// 	}

	//const SP_SPEC_ KURZTEXT = "kurztext";
	
	
	
	
	// 	const VERPFART_VULL = "V";
	// 	const VERPFART_HALV = "H";
	// 	const VERPFART_BREAKFAST = "B";
	
	// 	const OUTSITEAREA_BALCONY = "BAL";
	// 	const OUTSITEAREA_TERRACE = "TER";
	// 	const OUTSITEAREA_GARDEN = "GAR";
	
	
	
	// 	public function getZimmerBuNr($ObjNr, $ZimNr = null) {
	
	// 		$select = $this->_DBCon->select ();
	
	// 		// Hinzuf�gen einer FROM Bedingung
	// 		$select->FROM ( $this->getDBTableName () );
	// 		$select->where ( zvp_zimmer::SP_OBJ_NR . " = ?", $ObjNr, 'INTEGER' );
	// 		if ($ZimNr != null) {
	// 			$select->where ( zvp_zimmer::SP_ZIM_NR . " = ?", $ZimNr, 'INTEGER' );
	// 		}
	
	// 		// echo $select->__toString();
	// 		$back = $select->query ();
	
	// 		$All = $back->fetchAll ();
	
	// 		return $All;
	// 	}
	
	
}

?>