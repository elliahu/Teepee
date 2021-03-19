<?php

class DB {

	// Databázové spojení
  private static $spojeni;

	// Výchozí nastavení ovladače
  private static $nastaveni = array(
		PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_EMULATE_PREPARES => false,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
	);

	// Připojí se k databázi pomocí daných údajů
    public static function pripoj($server, $uzivatel, $heslo, $databaze)
    {
	  if (!isset(self::$spojeni))
      {
        $dsn = "mysql:host=$server;dbname=$databaze;charset=utf8";
			 self::$spojeni = new PDO(
				$dsn,
				$uzivatel,
				$heslo,
				self::$nastaveni
       );
	  }
	}

  //Ověří existenci uživatele a správnost údajů
  public static function overUzivatele($jmeno,$heslo){
    $sql = "SELECT * FROM vedouci WHERE login LIKE ?";
    $uzivatel = self::dotazJeden($sql,array($jmeno));
    if(password_verify($heslo,$uzivatel["heslo"]))
      return true;
    else
      return false;
  }

  public static function getUzivatel($jmeno){
    $sql = "SELECT * FROM vedouci WHERE login LIKE ?";
    $navrat = self::$spojeni->prepare($sql);
    $navrat->execute(array($jmeno));
    return (object) $navrat->fetch();
  }

  public static function getVedouci($id){
    $sql = "SELECT * FROM vedouci WHERE id_vedouciho = ?";
    $vedouci = self::$spojeni->prepare($sql);
    $vedouci->execute(array($id));
    return (object) $vedouci->fetch();
  }

  public static function getDite($id){
    $sql = "SELECT * FROM ranger WHERE id_rangera = ?";
    $navrat = self::$spojeni->prepare($sql);
    $navrat->execute(array($id));
    return (object) $navrat->fetch();
  }

  public static function getBody(){
    $sql = "SELECT r.id_rangera,r.jmeno,r.prezdivka,r.prijmeni,r.pocet_bodu,k.nazev FROM ranger r, kmen k WHERE r.id_kmenu = k.id_kmenu ORDER BY r.pocet_bodu DESC";
    $navrat = self::$spojeni->prepare($sql);
    $navrat->execute();
    return $navrat->fetchAll();
  }

  public static function hledejZapsaneBody($id_schuzky,$id_kmenu){
    $sql = "SELECT u.pocet_bodu as body,u.*, r.* FROM ucast_schuzka u, ranger r WHERE id_schuzky = ? AND u.id_rangera = r.id_rangera AND r.id_kmenu = ?";
    $navrat = self::$spojeni->prepare($sql);
    $navrat->execute(array($id_schuzky,$id_kmenu));
    return $navrat->fetchAll();
  }

  public static function pridejZaznamDochazky($id_rangera,$id_schuzky,$pocetBodu,$stav){
    $sql = "INSERT INTO ucast_schuzka values(?,?,?,?)";
    $navrat = self::$spojeni->prepare($sql);
    $navrat->execute(array($id_schuzky,$id_rangera,$pocetBodu,$stav));
  }

  public static function getUrovenUzivatele($id){
    $sql = "SELECT uroven FROM vedouci WHERE id_vedouciho = ?";
    $navrat = self::$spojeni->prepare($sql);
    $navrat->execute(array($id));
    return $navrat->fetch();
  }

  public static function zmenOpravneni($id,$opravneni){
    $sql = "UPDATE vedouci SET uroven = ? WHERE id_vedouciho = ?";
    $navrat = self::$spojeni->prepare($sql);
    $navrat->execute(array($opravneni,$id));
  }

  public static function upravZaznamDochazky($id_rangera,$id_schuzky,$pocetBodu,$stav){
    $sql = "UPDATE ucast_schuzka SET pocet_bodu = ?, stav = ? WHERE id_rangera = ? AND id_schuzky = ?";
    $navrat = self::$spojeni->prepare($sql);
    $navrat->execute(array($pocetBodu,$stav,$id_rangera,$id_schuzky));
  }

  public static function pridejBody($id,$pocet){
    $sql = "UPDATE ranger SET pocet_bodu = pocet_bodu + ? WHERE id_rangera = ?";
    $navrat = self::$spojeni->prepare($sql);
    $navrat->execute(array($pocet,$id));
  }

  public static function zaznamHistorieBodu($komu,$kdo,$pocet_bodu,$duvod){
    $sql = "INSERT INTO historie_bodu VALUES (null,?,NOW(),?,?,?)";
    $navrat = self::$spojeni->prepare($sql);
    $navrat->execute(array($komu,$pocet_bodu,$duvod,$kdo));
  }

  public static function smazatAkci($id){
    $sql = "DELETE FROM akce WHERE id_akce = ?";
    $navrat = self::$spojeni->prepare($sql);
    $navrat->execute(array($id));
  }

  public static function getKmen($id){
    $sql = "SELECT * FROM kmen WHERE id_kmenu = ?";
    $navrat = self::$spojeni->prepare($sql);
    $navrat->execute(array($id));
    return (object) $navrat->fetch();
  }

  public static function getNazevKmene($id){
    $sql = "SELECT nazev FROM kmen WHERE id_kmenu = ?";
    $navrat = self::$spojeni->prepare($sql);
    $navrat->execute(array($id));
    return $navrat->fetch();
  }

  public static function rowCountUzivatel($uz_jmeno){
    $sql = "SELECT * FROM vedouci WHERE login LIKE ?";
    $navrat = self::$spojeni->prepare($sql);
    $navrat->execute(array($uz_jmeno));
    return $navrat->rowCount();
  }

  public static function vsechnyKmeny(){
    $sql = "SELECT * FROM kmen ORDER BY min_vek ASC";
    $navrat = self::$spojeni->prepare($sql);
    $navrat->execute();
    return $navrat->fetchAll();
  }

  public static function vsechnyZaznamyDochazky($schuzka){
    $sql = "SELECT ucast_schuzka.*, ranger.* FROM ucast_schuzka join ranger using(id_rangera) WHERE id_schuzky = ?";
    $navrat = self::$spojeni->prepare($sql);
    $navrat->execute(array($schuzka));
    return $navrat->fetchAll();
  }

  public static function vsechnySchuzky(){
    $sql = "SELECT * FROM schuzka ORDER BY datum DESC";
    $navrat = self::$spojeni->prepare($sql);
    $navrat->execute();
    return $navrat->fetchAll();
  }

  public static function getSchuzkaPodleData($datum){
    $sql = "SELECT * from schuzka where datum = ?";
    $navrat = self::$spojeni->prepare($sql);
    $navrat->execute(array($datum));
    if($navrat->rowCount() != 0)
      return $navrat->fetch();
    else
      return false;
  }

  public static function getAkce($id){
    $sql = "SELECT * FROM akce WHERE id_akce = ?";
    $navrat = self::$spojeni->prepare($sql);
    $navrat->execute(array($id));
    return $navrat->fetch();
  }

  public static function upravAkci($id,$zacatek,$konec,$nazev,$popis,$vedouci_akce,$pridal){
    $sql = "UPDATE akce SET zacatek = ?, konec=?,nazev=?,popis=?,vedouci_akce=?,pridal=? WHERE id_akce = ?";
    $navrat = self::$spojeni->prepare($sql);
    $navrat->execute(array($zacatek,$konec,$nazev,$popis,$vedouci_akce,$pridal,$id));
  }

  public static function vsechnyAkce(){
    $sql = "SELECT * FROM akce ORDER BY zacatek DESC";
    $navrat = self::$spojeni->prepare($sql);
    $navrat->execute();
    return $navrat->fetchAll();
  }

  public static function getSchuzka($id){
    if($id === null){
      return null;
    }
    else{
      $sql = "SELECT * FROM schuzka WHERE id_schuzky = ?";
      $navrat = self::$spojeni->prepare($sql);
      $navrat->execute(array($id));
      return $navrat->fetch();
    }
  }

  public static function upravSchuzku($data = array()){
    $sql = "UPDATE schuzka SET datum = ?, popis = ?, pridal = ? WHERE id_schuzky = ? ";
    $navrat = self::$spojeni->prepare($sql);
    $navrat->execute($data);
  }

  public static function pridatKmen($data = array()){
    $sql = "INSERT INTO kmen VALUES(null,?,?,?,?)";
    $navrat = self::$spojeni->prepare($sql);
    $navrat->execute($data);
  }

  public static function getEmail($id){
    $sql = "SELECT * FROM odeslana_posta WHERE id_posty = ?";
    $navrat = self::$spojeni->prepare($sql);
    $navrat->execute(array($id));
    return $navrat->fetch();
  }

  public static function pridatDite($data = array()){
    $sql = "INSERT INTO ranger VALUES(null,?,?,?,?,?,?,0,?)";
    $navrat = self::$spojeni->prepare($sql);
    $navrat->execute($data);
  }

  public static function pridatEmail($prijemce,$predmet,$hlavicka = null,$zprava,$odeslal){
    $sql = "INSERT INTO odeslana_posta VALUES(null,?,?,?,?,now(),?)";
    $navrat = self::$spojeni->prepare($sql);
    $navrat->execute(array($prijemce,$predmet,$hlavicka,$zprava,$odeslal));
  }

  public static function vsechnyEmaily(){
    $sql = "SELECT * FROM odeslana_posta";
    $navrat = self::$spojeni->prepare($sql);
    $navrat->execute();
    return $navrat->fetchAll();
  }

  public static function getPouzeEmaily(){
    $sql = "SELECT kontaktni_email FROM ranger";
    $navrat = self::$spojeni->prepare($sql);
    $navrat->execute();
    return $navrat->fetchAll();
  }

  public static function getPouzeEmailyVedoucich(){
    $sql = "SELECT email FROM vedouci";
    $navrat = self::$spojeni->prepare($sql);
    $navrat->execute();
    return $navrat->fetchAll();
  }

  public static function getEmailyPoldeKmenu($IdKmenu = null){
    if($IdKmenu != null){
      $sql = "SELECT kontaktni_email,jmeno,prijmeni FROM ranger JOIN kmen USING(id_kmenu) WHERE id_kmenu = ?";
      $navrat = self::$spojeni->prepare($sql);
      $navrat->execute(array($IdKmenu));  
    }  
    else {
      $sql = "SELECT kontaktni_email FROM ranger";
      $navrat = self::$spojeni->prepare($sql);
      $navrat->execute(); 
    }
    return $navrat->fetchAll();
  }

  public static function smazatKmen($id){
    $sql = "DELETE FROM kmen WHERE id_kmenu = ?";
    $navrat = self::$spojeni->prepare($sql);
    $navrat->execute(array($id));
  }

  public static function upravitKmen($data = array()){
    $sql = "UPDATE kmen SET nazev = ?, min_vek = ?, max_vek = ?, popis = ? WHERE id_kmenu = ?";
    $navrat = self::$spojeni->prepare($sql);
    $navrat->execute($data);
  }

  public static function urovenUzivatele($id){
    $sql = "SELECT uroven FROM uzivatel WHERE id_vedouciho = ?";
    $navrat = self::$spojeni->prepare($sql);
    $navrat->execute(array($id));
    return $navrat->fetch();
  }

  public static function vsichniVedouci($kmen = null){
    if($kmen !== null){
      $sql = "SELECT vedouci.*, kmen.nazev FROM vedouci,kmen WHERE vedouci.id_kmenu = kmen.id_kmenu AND kmen.nazev LIKE ? ";
    }
    else{
      $sql = "SELECT vedouci.*, kmen.nazev FROM vedouci,kmen WHERE vedouci.id_kmenu = kmen.id_kmenu";
    }
    $navrat = self::$spojeni->prepare($sql);
    if(isset($kmen)){
      $navrat->execute(array($kmen));
    }
    else {
      $navrat->execute();
    }
    return $navrat->fetchAll();
  }
  public static function vsechnyDeti($kmen = null){
    if($kmen !== null){
      $sql = "SELECT ranger.*, kmen.nazev FROM ranger,kmen WHERE ranger.id_kmenu = kmen.id_kmenu AND kmen.nazev LIKE ? ";
    }
    else{
      $sql = "SELECT ranger.*, kmen.nazev FROM ranger,kmen WHERE ranger.id_kmenu = kmen.id_kmenu";
    }
    $navrat = self::$spojeni->prepare($sql);
    if(isset($kmen)){
      $navrat->execute(array($kmen));
    }
    else {
      $navrat->execute();
    }
    return $navrat->fetchAll();
  }

  public static function upravProfil($parametry = array()){
    $sql = "UPDATE vedouci SET jmeno = ?,prezdivka = ?,prijmeni = ?,datum_narozeni = ?,
    email = ?, tel = ?,id_kmenu = ? WHERE id_vedouciho = ?";
    $navrat = self::$spojeni->prepare($sql);
    $navrat->execute($parametry);
  }

  public static function upravProfilDitete($parametry = array()){
    $sql = "UPDATE ranger SET jmeno = ?,prezdivka = ?,prijmeni = ?,datum_narozeni = ?,
    kontaktni_email = ?, kontaktni_tel = ?,id_kmenu = ? WHERE id_rangera = ?";
    $navrat = self::$spojeni->prepare($sql);
    $navrat->execute($parametry);
  }

  public static function upravUzJmeno($parametry = array()){
    $sql = "UPDATE vedouci SET login = ? WHERE id_vedouciho = ?";
    $navrat = self::$spojeni->prepare($sql);
    $navrat->execute($parametry);
  }

  public static function upravHeslo($parametry = array()){
    $sql = "UPDATE vedouci SET heslo = ? WHERE id_vedouciho = ?";
    $navrat = self::$spojeni->prepare($sql);
    $navrat->execute($parametry);
  }

  public static function getHeslo($id_vedouciho){
    $sql = "SELECT heslo FROM vedouci WHERE id_vedouciho = ?";
    $navrat = self::$spojeni->prepare($sql);
    $navrat->execute(array($id_vedouciho));
    return $navrat->fetch();
  }

  public static function detiPodleKmene($id_kmenu){
    if($id_kmenu === null){
      return null;
    }
    else{
      $sql = "SELECT * FROM ranger WHERE id_kmenu = ?";
      $navrat = self::$spojeni->prepare($sql);
      $navrat->execute(array($id_kmenu));
      return $navrat->fetchAll();
    }
  }

  public static function detiPodleKmeneUcast($id_kmenu){
    if($id_kmenu === null){
      return null;
    }
    else{
      $sql = "SELECT ranger.*, kmen.* FROM ranger JOIN kmen USING(id_kmenu) WHERE ranger.id_kmenu = ?";
      $navrat = self::$spojeni->prepare($sql);
      $navrat->execute(array($id_kmenu));
      return $navrat->fetchAll();
    }
  }


  public static function ucastNaSchuzce($id_schuzky,$id_kmenu){
      $sql = "SELECT ranger.*,ucast_schuzka.* FROM ranger join ucast_schuzka using(id_rangera) WHERE ucast_schuzka.id_schuzky = ? AND ranger.id_kmenu = ?";
      $navrat = self::$spojeni->prepare($sql);
      $navrat->execute(array($id_schuzky,$id_kmenu));
      return $navrat->fetchAll();
  }

  public static function nazevKmenu($id_kmenu){
    if($id_kmenu === null){
      return NULL;
    }
    else{
      $sql = "SELECT nazev,id_kmenu FROM kmen WHERE id_kmenu = ?";
      $navrat = self::$spojeni->prepare($sql);
      $navrat->execute(array($id_kmenu));
      return $navrat->fetch();
    }
  }

  public static function nazvyVsechKmenu(){
    $sql = "SELECT nazev,id_kmenu FROM kmen";
    $navrat = self::$spojeni->prepare($sql);
    $navrat->execute();
    return $navrat->fetchAll();
  }

  public static function nejblizsiSchuzky(){
    $sql = "SELECT *,ABS(DATEDIFF(now(),datum)) as dny FROM schuzka ORDER BY dny ASC";
    $navrat = self::$spojeni->prepare($sql);
    $navrat->execute();
    return $navrat->fetchAll();
  }
  public static function nejblizsiSchuzka(){
    $sql = "SELECT *,ABS(DATEDIFF(now(),datum)) as dny FROM schuzka ORDER BY dny ASC LIMIT 2";
    $navrat = self::$spojeni->prepare($sql);
    $navrat->execute();
    return $navrat->fetchAll();
  }

  public static function upravLastLogin($id_uz){
    $sql = "UPDATE vedouci SET posledni_prihlaseni = now() WHERE id_vedouciho = ?";
    $navrat = self::$spojeni->prepare($sql);
    $navrat->execute(array($id_uz));
  }

  public static function pridejSchuzku($parametry = array()){
    $sql1 = "SELECT * FROM schuzka WHERE datum = ?";
    $navrat1 = self::$spojeni->prepare($sql1);
    $navrat1->execute(array($parametry[0]));
    if($navrat1->rowCount() == 0){
      $sql = "INSERT INTO schuzka values(NULL,?,?,?)";
      $navrat = self::$spojeni->prepare($sql);
      $navrat->execute($parametry);
      return true;
    }
    else{
      return false;
    }
  }

  public static function pridejSchuzkuRychle($datum,$id_uzivatele){
    $sql1 = "SELECT * FROM schuzka WHERE datum = ?";
    $navrat1 = self::$spojeni->prepare($sql1);
    $navrat1->execute(array($datum));
    if($navrat1->rowCount() == 0){
      $sql = "INSERT INTO schuzka VALUES(null, ?, 'Běžná schůzka', ?)";
      $navrat = self::$spojeni->prepare($sql);
      $navrat->execute(array($datum,$id_uzivatele));
      return true;
    }
    else{
      return false;
    }
  }

  public static function pridejAkci($parametry = array()){
    $sql = "INSERT INTO akce VALUES(null,?,?,?,?,?,?)";
    $navrat = self::$spojeni->prepare($sql);
    $navrat->execute($parametry);
  }

	// Spustí dotaz a vrátí z něj první řádek
  public static function dotazJeden($dotaz, $parametry = array()) {
		  $navrat = self::$spojeni->prepare($dotaz);
		  $navrat->execute($parametry);
	  return $navrat->fetch();
	}

	// Spustí dotaz a vrátí všechny jeho řádky jako pole asociativních polí
  public static function dotazVsechny($dotaz, $parametry = array()) {
		$navrat = self::$spojeni->prepare($dotaz);
		$navrat->execute($parametry);
		return $navrat->fetchAll();
	}

	// Spustí dotaz a vrátí z něj první sloupec prvního řádku
  public static function dotazSamotny($dotaz, $parametry = array()) {
		$vysledek = self::dotazJeden($dotaz, $parametry);
		return $vysledek[0];
	}

	// Spustí dotaz a vrátí počet ovlivněných řádků
	public static function dotaz($dotaz, $parametry = array()) {
		$navrat = self::$spojeni->prepare($dotaz);
		$navrat->execute($parametry);
		return $navrat->rowCount();
	}

  //vrati zaznamy ucasti na akci, pokud existuji, jinak vrati null
  public static function getZaznamUcastAkce($id_akce){
    $sql = "SELECT * FROM ucast_akce WHERE id_akce = ?";
    $navrat = self::$spojeni->prepare($sql);
    $navrat->execute(array($id_akce));
    $test = $navrat->fetchAll();
    if(isset($test[0]["id_rangera"])){
      return $test;
    }
    else{
      return null;
    }
  }

  public static function upravZaznamUcastiNaAkci($id_schuzky,$id_rangera,$pocetBodu,$stav){
    $sql = "UPDATE ucast_akce SET pocet_bodu = ?, stav = ? WHERE id_akce = ? AND id_rangera = ?";
    $navrat = self::$spojeni->prepare($sql);
    $navrat->execute(array($pocetBodu,$stav,$id_schuzky,$id_rangera));
  }

	// Vloží do tabulky nový řádek jako data z asociativního pole
	public static function vloz($tabulka, $parametry = array()) {
		return self::dotaz("INSERT INTO $tabulka (".
		implode(', ', array_keys($parametry)).
		") VALUES (".str_repeat('?,', sizeOf($parametry)-1)."?)",
			array_values($parametry));
	}
  //přidání nového vedoucího
  public static function novyVedouci($parametryVedouci = array()){
    $sql = "INSERT INTO vedouci (jmeno,prezdivka,prijmeni,datum_narozeni,email,tel,login,heslo,posledni_prihlaseni,uroven,id_kmenu)
    VALUES(?,?,?,?,?,?,?,?,now(),4,?)";
    $navrat = self::$spojeni->prepare($sql);
    $navrat->execute($parametryVedouci);
    $id = self::idPoslednihoVlozeneho();
    return $id;
  }
	// Změní řádek v tabulce tak, aby obsahoval data z asociativního pole
	public static function zmen($tabulka, $hodnoty = array(), $podminka, $parametry = array()) {
		return self::dotaz("UPDATE $tabulka SET ".
		implode(' = ?, ', array_keys($hodnoty)).
		" = ? " . $podminka,
		array_merge(array_values($hodnoty), $parametry));
	}

	// Vrací ID posledně vloženého záznamu
	public static function idPoslednihoVlozeneho()
	{
		return self::$spojeni->lastInsertId();
	}

  public static function smazatVedouciho($id){
    //vedouci
    $sql = "DELETE FROM vedouci WHERE id_vedouciho = ?";
    $navrat = self::$spojeni->prepare($sql);
    $navrat->execute(array($id));
  }

  public static function smazatDite($id){
    $sql = "DELETE FROM ranger WHERE id_rangera = ?";
    $navrat = self::$spojeni->prepare($sql);
    $navrat->execute(array($id));
  }

  public static function smazatSchuzku($id){
    $sql = "DELETE FROM schuzka WHERE id_schuzky = ?";
    $navrat = self::$spojeni->prepare($sql);
    $navrat->execute(array($id));
  }

  public static function pridejZanznamUcastiNaAkci($id_akce,$id_rangera,$pocet_bodu,$stav){
    $sql = "INSERT INTO ucast_akce VALUES (?,?,?,?)";
    $navrat = self::$spojeni->prepare($sql);
    $navrat->execute(array($id_akce,$id_rangera,$pocet_bodu,$stav));
  }

  public static function overEmailUzivatele($login,$email){
    $sql = "SELECT id_vedouciho FROM vedouci WHERE login = ? AND email = ?";
    $navrat = self::$spojeni->prepare($sql);
    $navrat->execute(array($login,$email));
    return $navrat->fetch();
  }

  public static function overPrechody(){
    $sql = "SELECT ranger.id_rangera,ranger.jmeno, ranger.prezdivka, ranger.prijmeni, TIMESTAMPDIFF(YEAR, ranger.datum_narozeni, CURDATE()) AS vek,kmen.id_kmenu, kmen.nazev, kmen.max_vek
    FROM ranger JOIN kmen USING (id_kmenu) WHERE TIMESTAMPDIFF(YEAR, datum_narozeni, CURDATE()) >= kmen.max_vek";
    $navrat = self::$spojeni->prepare($sql);
    $navrat->execute();
    return $navrat->fetchAll();
  }

  public static function prechodMeziKmeny($kdo,$kam){
    $sql = "UPDATE ranger SET id_kmenu = ? WHERE id_rangera = ?";
    $navrat = self::$spojeni->prepare($sql);
    $navrat->execute(array($kam,$kdo));
  }

  public static function narozeninyVTomtoTydnu(){
    $sql = "SELECT datum_narozeni,jmeno,prezdivka,prijmeni,TIMESTAMPDIFF(YEAR, ranger.datum_narozeni, CURDATE()) as vek FROM ranger WHERE DATE(datum_narozeni + INTERVAL (YEAR(NOW()) - YEAR(datum_narozeni)) YEAR)
    BETWEEN
    DATE(NOW() - INTERVAL WEEKDAY(NOW()) DAY)
    AND
    DATE(NOW() + INTERVAL 6 - WEEKDAY(NOW()) DAY)";
    $navrat = self::$spojeni->prepare($sql);
    $navrat->execute();
    return $navrat->fetchAll();
  }

  ####################################################
  ######### PASSWORD RESET ###########################
  ####################################################

  public static function addPasswordResetRequest($id_vedouciho,$token){
    $sql = "INSERT INTO password_reset_request_cache(id_vedouciho,token) VALUES (?,?)";
    $navrat = self::$spojeni->prepare($sql);
    $navrat->execute(array($id_vedouciho,$token));
  }

  public static function getResetRequest($token){
    $sql = "SELECT password_reset_request_cache.*, vedouci.login as jmeno , vedouci.id_vedouciho as id, vedouci.email 
    FROM password_reset_request_cache JOIN vedouci USING (id_vedouciho) 
    WHERE token LIKE ?";
    $navrat = self::$spojeni->prepare($sql);
    $navrat->execute(array($token));
    $navrat = $navrat->fetch();
    return $navrat;
  }

  public static function verifyRequestToken($token){
    $sql = "SELECT reset_no,hotovo FROM password_reset_request_cache WHERE token LIKE ?";
    $navrat = self::$spojeni->prepare($sql);
    $navrat->execute(array($token));
    $navrat = $navrat->fetch();
    if(isset($navrat["reset_no"]) && ($navrat["reset_no"] > 0) && $navrat["hotovo"] == 0)
      return true;
    else
      return false;
  }

  public static function completeRequest($token){
    $sql = "UPDATE password_reset_request_cache SET hotovo = true WHERE token LIKE ?";
    $navrat = self::$spojeni->prepare($sql);
    $navrat->execute(array($token));
  }

  public static function uniqueToken($token){
    $sql = "SELECT reset_no FROM password_reset_request_cache WHERE token LIKE ?";
    $navrat = self::$spojeni->prepare($sql);
    $navrat->execute(array($token));
    $navrat = $navrat->fetch();
    if(isset($navrat["reset_no"]) && ($navrat["reset_no"] > 0))
      return false;
    else 
      return true;
  }

  ####################################################
  ######## SYSLOG ####################################
  ####################################################

  public static function syslog($log,$typ,$id_vedouciho){
    $sql = "INSERT INTO syslog (id_vedouciho,log,typ) VALUES (?,?,?)";
    $navrat = self::$spojeni->prepare($sql);
    $navrat->execute(array($id_vedouciho,strip_tags($log,"<b>"),$typ));
    return true;
  }

  public static function getSyslog(){
    $sql = "SELECT * FROM syslog  ORDER BY id_logu DESC LIMIT 500";
    $navrat = self::$spojeni->prepare($sql);
    $navrat->execute();
    return $navrat->fetchAll();
  }

  ###################################################
  #########   STATISTIKA ############################
  ###################################################

  public static function StatistikaNejviceBodu($kmen){
    if($kmen != "Vsichni"){
      $sql = "SELECT id_kmenu FROM kmen WHERE nazev LIKE ? ";
      $navrat = self::$spojeni->prepare($sql);
      $navrat->execute(array($kmen));
      $navrat->fetch();
      $id_kmenu = $navrat["id_kmenu"];
      $sql = "SELECT * FROM ranger WHERE id_kmenu = ? ORDER BY";
    }

  }

  public static function statistikaNejmensiAbsence(){
    $sql = "SELECT r.jmeno,r.prijmeni,k.nazev,count(u.id_rangera) as pocet
    from ucast_schuzka u join ranger r using(id_rangera) join kmen k using(id_kmenu)
    where stav like 'pritomen'
    group by id_rangera
    ORDER BY pocet DESC LIMIT 3
    ";
    $navrat = self::$spojeni->prepare($sql);
    $navrat->execute();
    return $navrat->fetchAll();
  }

  public static function statistikaPrubehPoTydnech($kmen = null){
    if($kmen == null)
      $sql = "";
    else
      $sql_dataset = "SELECT jmeno,prezdivka,prijmeni,ucast_schuzka.id_rangera as id,ucast_schuzka.pocet_bodu as body,datum from ucast_schuzka join ranger using(id_rangera) join schuzka using(id_schuzky) WHERE ranger.id_kmenu = ?";
      $sql_datum = "SELECT datum from ucast_schuzka join ranger using(id_rangera) join schuzka using(id_schuzky) WHERE ranger.id_kmenu = ? GROUP BY datum";
      $sql_ranger = "SELECT * FROM ranger WHERE id_kmenu = ?";
      $navrat = self::$spojeni->prepare($sql_dataset);
      $navrat->execute(array($kmen));
      $dataset = $navrat->fetchAll();
      $navrat = self::$spojeni->prepare($sql_datum);
      $navrat->execute(array($kmen));
      $datum = $navrat->fetchAll();
      $navrat = self::$spojeni->prepare($sql_ranger);
      $navrat->execute(array($kmen));
      $ranger = $navrat->fetchAll();

      return array("dataset" => $dataset, "datum" => $datum, "ranger" => $ranger);
    }

    public static function statistikaSchuzky(){
      $sql = "SELECT schuzka.datum AS datum,  count(ucast_schuzka.id_rangera) AS pocet
      FROM schuzka JOIN ucast_schuzka USING (id_schuzky) JOIN ranger USING (id_rangera) 
      WHERE ucast_schuzka.stav LIKE 'pritomen' OR ucast_schuzka.stav LIKE 'pritomen-pozde'
      GROUP BY schuzka.id_schuzky
      ORDER BY schuzka.datum ASC";
      $navrat = self::$spojeni->prepare($sql);
      $navrat->execute();
      return $navrat->fetchAll();
    }

    public static function statistikaAkce(){
      $sql = "SELECT akce.nazev AS datum,  count(ucast_akce.id_rangera) AS pocet
      FROM akce JOIN ucast_akce USING (id_akce) JOIN ranger USING (id_rangera) 
      WHERE ucast_akce.stav LIKE 'pritomen'
      GROUP BY akce.id_akce
      ORDER BY akce.zacatek ASC";
      $navrat = self::$spojeni->prepare($sql);
      $navrat->execute();
      return $navrat->fetchAll();
    }

    public static function statistikaPocetDeti(){
      $sql = "SELECT count(id_rangera) as pocet FROM ranger";
      $navrat = self::$spojeni->prepare($sql);
      $navrat->execute();
      $navrat =  $navrat->fetchAll();
      return $navrat[0]["pocet"];
    }

    public static function statistikaPocetSchuzek(){
      $sql = "SELECT count(id_schuzky) as pocet FROM schuzka";
      $navrat = self::$spojeni->prepare($sql);
      $navrat->execute();
      $navrat =  $navrat->fetchAll();
      return $navrat[0]["pocet"];
    }

    public static function statistikaPocetAkci(){
      $sql = "SELECT count(id_akce) as pocet FROM akce";
      $navrat = self::$spojeni->prepare($sql);
      $navrat->execute();
      $navrat =  $navrat->fetchAll();
      return $navrat[0]["pocet"];
    }

    public static function statistikaPrumernaUcastSchuzka(){
      $sql = "SELECT AVG(pocet) as prumer 
      FROM ( 
        SELECT schuzka.datum AS datum, count(ucast_schuzka.id_rangera) AS pocet 
        FROM schuzka JOIN ucast_schuzka USING (id_schuzky) JOIN ranger USING (id_rangera) 
        WHERE ucast_schuzka.stav LIKE 'pritomen' OR ucast_schuzka.stav LIKE 'pritomen-pozde' 
        GROUP BY schuzka.id_schuzky 
        ORDER BY schuzka.datum ASC
      ) sub";
      $navrat = self::$spojeni->prepare($sql);
      $navrat->execute();
      $navrat =  $navrat->fetch();
      return $navrat["prumer"];
    }

    public static function statistikaPrumernaUcastAkce(){
      $sql = "SELECT avg(pocet) as prumer from (
        SELECT akce.nazev AS datum,  count(ucast_akce.id_rangera) AS pocet
              FROM akce JOIN ucast_akce USING (id_akce) JOIN ranger USING (id_rangera) 
              WHERE ucast_akce.stav LIKE 'pritomen'
              GROUP BY akce.id_akce
              ORDER BY akce.zacatek ASC) sub";
      $navrat = self::$spojeni->prepare($sql);
      $navrat->execute();
      $navrat =  $navrat->fetch();
      return $navrat["prumer"];
    }

    public static function statistikaDetiPrubehPoTydnech($id_rangera){
      /*
        Vrací statistiku rangera po tydnech
      */
      $sql_dataset = "SELECT ucast_schuzka.id_rangera as id,ucast_schuzka.pocet_bodu as body,datum from ucast_schuzka join ranger using(id_rangera) join schuzka using(id_schuzky) WHERE ranger.id_rangera = ?";
      $navrat = self::$spojeni->prepare($sql_dataset);
      $navrat->execute(array($id_rangera));
      return $navrat->fetchAll();
    }

    public static function statistikaDetiPrumernaUcastNaSchuzkach($id_rangera){
      $sql = "SELECT COUNT(id_rangera) as pritomen FROM ucast_schuzka WHERE id_rangera = ? AND stav LIKE 'pritomen'";
      $ucastPritomen = self::$spojeni->prepare($sql);
      $ucastPritomen->execute(array($id_rangera));
      $ucastPritomen = $ucastPritomen->fetch();
      $sql = "SELECT COUNT(id_rangera) as nepritomen FROM ucast_schuzka WHERE id_rangera = ? AND (stav LIKE 'neomluven' OR stav LIKE 'omluven')";
      $ucastNepritomen = self::$spojeni->prepare($sql);
      $ucastNepritomen->execute(array($id_rangera));
      $ucastNepritomen = $ucastNepritomen->fetch();

      $a = $ucastPritomen["pritomen"];
      $b = $ucastNepritomen["nepritomen"];
      if($a == 0 ){
        return 0;
      }
      return ( 100 * ($a / ( $a + $b)));

    }

    public static function statistikaDetiPritomen($id_rangera){
      /*
        Vrati pocet kolikrat bylo dite pritomno (int)
      */
      $sql = "SELECT COUNT(id_rangera) as pritomen FROM ucast_schuzka WHERE id_rangera = ? AND stav LIKE 'pritomen'";
      $ucastPritomen = self::$spojeni->prepare($sql);
      $ucastPritomen->execute(array($id_rangera));
      $ucastPritomen = $ucastPritomen->fetch();
      return $ucastPritomen["pritomen"];
    }

    public static function statistikaDetiNepritomen($id_rangera){
      /*
        Vrati pocet kolikrat bylo dite nepritomno (int)
      */
      $sql = "SELECT COUNT(id_rangera) as nepritomen FROM ucast_schuzka WHERE id_rangera = ? AND (stav LIKE 'neomluven' OR stav LIKE 'omluven')";
      $ucastNepritomen = self::$spojeni->prepare($sql);
      $ucastNepritomen->execute(array($id_rangera));
      $ucastNepritomen = $ucastNepritomen->fetch();
      return $ucastNepritomen["nepritomen"];
    }

    public static function statistikaDetiOmluvenaAbsence($id_rangera){
      /*
        Vrati procentualni omluvenou absenci ditete
      */
      $sql = "SELECT COUNT(id_rangera) as pritomen FROM ucast_schuzka WHERE id_rangera = ? AND stav LIKE 'omluven'";
      $ucastPritomen = self::$spojeni->prepare($sql);
      $ucastPritomen->execute(array($id_rangera));
      $ucastPritomen = $ucastPritomen->fetch();
      $sql = "SELECT COUNT(id_rangera) as nepritomen FROM ucast_schuzka WHERE id_rangera = ? AND stav LIKE 'neomluven'";
      $ucastNepritomen = self::$spojeni->prepare($sql);
      $ucastNepritomen->execute(array($id_rangera));
      $ucastNepritomen = $ucastNepritomen->fetch();

      $a = $ucastPritomen["pritomen"];
      $b = $ucastNepritomen["nepritomen"];
      if($a == 0 ){
        return 0;
      }
      return ( 100 * ($a / ( $a + $b)));
    }



    #########################################
    ###########     API #####################
    #########################################

    public static function getAppByToken($token){
      $sql = "SELECT * from autorizovane_aplikace WHERE token LIKE ?";
      $navrat = self::$spojeni->prepare($sql);
      $navrat->execute(array($token));
      return $navrat->fetch();
    }

    /*
    [["sloupec" , "hodnota"],["sloupec2" , "hodnota2"]]
    */
    public static function APIget($tabulka,array $parametry){
      $sql = "SELECT * FROM $tabulka";
      if(!empty($parametry)){
        $sql.=" WHERE ";
        $i=0;
        foreach($parametry as $condition){
          if(is_string($condition[1])){
            $sql.= " ".$condition[0]." LIKE '".$condition[1]."'";
          }
          else{
            $sql.= " ".$condition[0]."=".$condition[1];
          }
          if(isset($parametry[$i+1]))
            $sql.=" AND ";
          $i++;
        }
      }
      $navrat = self::$spojeni->prepare($sql);
      $navrat->execute();
      return $navrat->fetchAll();
    }

    /*
    [{"sloupec":"hodnota","sloupec":"hodnota","sloupec":"hodnota"},{"sloupec":"hodnota","sloupec":"hodnota"}]
    */

    //pokud prvni sloupec je null, tak vkládám nová data
    //pokud prvni sloupec neni null, tak data updatuju
    public static function APIput($tabulka,array $parametry){
      foreach($parametry as $obj){
        $data = (array) $obj;
        $dataKeys = array_keys($data);
          //Vkládáme
          $sql = "INSERT INTO $tabulka (";
          for($i = 0;$i<count($data);$i++){
            $sql.=$dataKeys[$i];
            if(isset($dataKeys[$i+1]))
              $sql.=",";
          }
          $sql.=") VALUES(";
          //sloupce
          for($i = 0;$i<count($data);$i++){
            if(is_string($data[$dataKeys[$i]]))
              $sql.="'".$data[$dataKeys[$i]]."'";
            else 
             {
               if($data[$dataKeys[$i]] == null)
                $sql.="null";
               else 
                $sql.=$data[$dataKeys[$i]];
             }
            if(isset($dataKeys[$i+1]))
              $sql.=",";
          }

          $sql.=")";
          
          $navrat = self::$spojeni->prepare($sql);
          $navrat->execute();
        
      }
    }

    public static function APIset($tabulka,array $parametry){
      foreach($parametry as $obj){
        $data = (array) $obj;
        $dataKeys = array_keys($data);
          //aktualizujeme
          $sql = "UPDATE $tabulka SET ";
          //sloupce
          for($i = 0;$i<count($data);$i++){
            if(is_string($data[$dataKeys[$i]]))
              $sql.=$dataKeys[$i]."='".$data[$dataKeys[$i]]."'";
            else 
            $sql.=$dataKeys[$i]."=".$data[$dataKeys[$i]];
            if(isset($dataKeys[$i+1]))
              $sql.=",";
          }
          if(is_string($data[$dataKeys[0]]))
            $sql .= " WHERE ".$dataKeys[0]." LIKE '".$data[$dataKeys[0]]."'";
          else
            $sql .= " WHERE ".$dataKeys[0]." = ".$data[$dataKeys[0]];
          echo $sql;
          $navrat = self::$spojeni->prepare($sql);
          $navrat->execute();
      }
    }

    public static function APIdelete($tabulka,array $parametry){
      $sql = "DELETE FROM $tabulka";
      if(!empty($parametry)){
        $sql.=" WHERE ";
        $i=0;
        foreach($parametry as $condition){
          if(is_string($condition[1])){
            $sql.= " ".$condition[0]." LIKE '".$condition[1]."'";
          }
          else{
            $sql.= " ".$condition[0]."=".$condition[1];
          }
          if(isset($parametry[$i+1]))
            $sql.=" AND ";
          $i++;
        }
      }
      $navrat = self::$spojeni->prepare($sql);
      $navrat->execute();
    }
}
