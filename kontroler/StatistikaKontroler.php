<?php
class StatistikaKontroler extends Kontroler{

  public function zpracuj($parametry){

    /*if($this->sessionCheck() === false){
      $this->presmeruj("login");
    }*/
    if(isset($parametry[0])){
      //Jednotlivé kmeny
      $id_kmenu = $parametry[0];
      $kmen = DB::getKmen($id_kmenu);
      $nazevKmene = DB::getNazevKmene($id_kmenu)["nazev"];
      $this->pohled = "statistika-graf";
      $this->data["celkovyPrehled"]["btn"] = "Celkový přehled";
      $this->data["celkovyPrehled"]["kmen"] = $nazevKmene;
      $this->data["celkovyPrehled"]["data"] = DB::dotazVsechny("SELECT jmeno,prezdivka,prijmeni,pocet_bodu from ranger where id_kmenu = ? order by pocet_bodu desc",array($id_kmenu));
      //Statistika po tydnech
      $this->data["poTydnech"] = DB::statistikaPrubehPoTydnech($id_kmenu);
      $this->data["kmeny"] = DB::vsechnyKmeny();
      $this->data["akt_kmen"] = (array) DB::getKmen($id_kmenu);
    }
    else{
      //Celkové statistiky
      $parametr = null;
      $this->pohled = "statistika";
      //Všechny kmeny
      $this->data["kmeny"] = DB::vsechnyKmeny();
      //Jména
      $this->data["statistika"]["jmena"] = DB::dotazVsechny("SELECT jmeno,prezdivka,prijmeni,pocet_bodu from ranger order by pocet_bodu desc");
      //Statistika účast na schůzkách
      $this->data["schuzky"] = DB::statistikaSchuzky();
      //Statistika účasti na akcích
      $this->data["akce"] = DB::statistikaAkce();
      //Celkový počet dětí
      $this->data["pocetDeti"] = DB::statistikaPocetDeti();
      //Celkový počet schůzek
      $this->data["pocetSchuzek"] = DB::statistikaPocetSchuzek();
      //Celkový počet akcí
      $this->data["pocetAkci"] = DB::statistikaPocetAkci();
      // Průměrná účast na schůzce
      $this->data["prumUcastSchuzka"] = DB::statistikaPrumernaUcastSchuzka();
      // Průměrná účast na akci
      $this->data["prumUcastAkce"] = DB::statistikaPrumernaUcastAkce();
    }


  }


}
 ?>
