<?php
#
# Třída kontroler
# Určuje jak vypadají ostatní kontrolery, které z ní dědí
#
  abstract class Kontroler{
    //abstraktní třída kontroler
    protected $pohled = "";
    protected $data = array();
    protected $colorGenerator;
    protected $mailManager;


    //abstraktní metoda zpracuj
    abstract function zpracuj($parametry);


    //metoda pro vypsání pohledu
    public function vypisPohled(){
      extract($this->data);
      if($this->pohled)
        require_once("view/{$this->pohled}.phtml");
    }
    //metoda pro přesměrování
    public function presmeruj($target){
      header("Location: /$target");
      exit();
    }
    //metoda která testuje zda je uživatel přihlášen
    public function sessionCheck(){
      if(isset($_SESSION["login"]) && $_SESSION["login"] == true){
        return true;
      }
      else{
        return false;
      }
    }
    //Metoda přidává zprávy o historie zprav, kterou si může uživatel zobrazit
    public function pridejZpravuDoHistorie($txt,$typ){
      $_SESSION["historie_zprav"][] = array(
        "zprava" => $txt,
        "typ" => $typ,
        "cas" => date('H:i:s', time())
      );
    }
    //Metoda vrací pole obsahující historii zprav
    public function vratHistoriiZprav(){
      if (!empty($_SESSION["historie_zprav"])){
        $zpravy = $_SESSION["historie_zprav"];
        return $zpravy;
      }
      else
        return array();
    }
    //metoda pro pridání správy do pole Zpravy
    public function pridejZpravu($txt,$typ){
      $_SESSION["zpravy"][] = array(
        "zprava" => $txt,
        "typ" => $typ
      );
      $this->pridejZpravuDoHistorie($txt,$typ);
      $this->syslog($txt,$typ);
    }
    //metoda vypíše zprývy z pole "zpravy" a smaže je
    public function vratZpravy() {
      if (!empty($_SESSION["zpravy"])){
        $zpravy = $_SESSION["zpravy"];
        unset($_SESSION["zpravy"]);
        return $zpravy;
      }
      else
        return array();
    }

    public function aktualizujSession($jmeno_uzivatele){
      /*
      * Využívá se pro rychlé aktualizování informací uložené v poli _SESSION
      * npř. Uživatel si změní jméno -> Jeho nové jméno se aktualizuje v DB ale i v _SESSION
      */
      $_SESSION["uzivatel"] = DB::getVedouci($_SESSION["uzivatel"]->id_vedouciho);
      $_SESSION["uzivatel"]->kmen = DB::getKmen($_SESSION["uzivatel"]->id_kmenu);
    }

    public function syslog($log,$typ){
      DB::syslog($log,$typ,(isset($_SESSION["uzivatel"]->id_vedouciho)) ? $_SESSION["uzivatel"]->id_vedouciho : 0);
    }
  }
