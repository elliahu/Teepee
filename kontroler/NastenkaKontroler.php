<?php
###########################
# Třída NastenkaKontroler #
###########################
/*
  Třída má na starost správu uživatelské nástěnky
  domain.cz/nastenka

  -> přechody mezi kmeny
  -> narozeniny
*/
class NastenkaKontroler extends Kontroler{

  // Metoda zpracuj
  public function zpracuj($parametry){
    // Ověření přihlášení uživatele
    if($this->sessionCheck()){
      $this->pohled = "nastenka";
    }
    else{
      $this->presmeruj("login");
    }

    global $config;
    $this->data["config"] = $config;

    //Vrátí do pohledu dnešní datum
    $this->data["datum"] = $this->dnesniDatum();
    $dny = array(
      "Monday" => "pondělí",
      "Tuesday" => "úterý",
      "Wednesday" => "středu",
      "Thursday" => "čtvrtek",
      "Friday" => "pátek",
      "Saturday" => "sobotu",
      "Sunday" => "neděli"
    );
    $this->data["denSchuzky"] = $dny[$config->denSchuzky];
    if(date("l",strtotime("Today")) != $config->denSchuzky){
      $this->data["den"] = DB::getSchuzkaPodleData(date("Y-m-d",strtotime("Next $config->denSchuzky")));
      $this->data["datumSchuzky"] = date("j.n.",strtotime("Next $config->denSchuzky"));
      }
    else{
      $this->data["den"] = DB::getSchuzkaPodleData(date("Y-m-d",strtotime("Today")));
      $this->data["datumSchuzky"] = date("j.n.",strtotime("Today"));
    }


    
    
    
    ## Přechody mezi kmeny
    /* 
     * APlikace bude upozorňvat na přechody mezi kmeny
     * Např dítě A má již věk na to, aby mohlo být součástí vyššího kmene
     * Aplikace na tuto skutečnost upozorní a nabídne možnost přechodu
    */
    $prechody = DB::overPrechody();
    
    if(isset($prechody[0]["jmeno"])){ // ověří zda existují přechody
      $this->data["zadnePrechody"] = true; // true = nalezeny navrhované přechody -> použito v podmínce pohledu
      $this->data["vsechnyKmeny"] = DB::vsechnyKmeny();
      $this->data["prechody"] = array();
      
      //Projde pole prechodů a předá do pohledu navrhované kmeny
      foreach($prechody as $prechod){
        $prechod["navrhovanyKmen"] = $this->navrhniKmen($prechod["id_kmenu"],$this->data["vsechnyKmeny"]);
        array_push($this->data["prechody"],$prechod);
      }
    }
    else {
      $this->data["zadnePrechody"] = false; 
    }
    ## Narozeniny
    /* 
     * APlikace bude upozorňvat na narozeniny vedoucích a dětí
    */
    $this->data["narozeniny"] = DB::narozeninyVTomtoTydnu();
    if(isset($this->data["narozeniny"][0]["jmeno"])){
      $this->data["zadneNarozeniny"] = true; // vychazi z logiky pohledu
    }
    else{
      $this->data["zadneNarozeniny"] = false; // vychazi z logiky pohledu
    }
    $this->data["nejblizsiSchuzka"] = DB::nejblizsiSchuzka();

  }


  public function dnesniDatum(){
    $mesice = array(
      "01" => "Ledna",
      "02" => "Února",
      "03" => "Března",
      "04" => "Dubna",
      "05" => "Května",
      "06" => "Června",
      "07" => "Července",
      "08" => "Srpna",
      "09" => "Září",
      "10" => "Října",
      "11" => "Listopadu",
      "12" => "Prosince"
    );
    $dny = array(
      0 => "Neděle",
      1 => "Pondělí",
      2 => "Úterý",
      3 => "Středa",
      4 => "Čtvrtek",
      5 => "Pátek",
      6 => "Sobota"
    );
    $denVTydnu = date("w",strtotime("Today"));
    $den = date("j",strtotime("Today"));
    $mesic= date("m",strtotime("Today"));

    return array($dny[$denVTydnu],$den.". ".$mesice[$mesic]);
  }

  private function navrhniKmen($soucasnyKmen,$vsechnyKmeny){
    /* In progress */
    $i = 0;
    foreach($vsechnyKmeny as $kmen){
      if($kmen["id_kmenu"] == $soucasnyKmen)
        break;
      $i++;
    }
    if(isset($vsechnyKmeny[$i+1]))
      return $vsechnyKmeny[$i+1];
    else 
      return false;
  }

  public function acronym($text){
    /* Vytvoří acronym vety 'Abc Def' -> 'AD' */
    $words = explode(" ", $text);
    $acronym = "";

    foreach ($words as $w) {
      $acronym .= $w[0];
    }
    return $acronym;
  }

  private function prechodVsichni($kdo = array()){
    /* Work in progress */
    foreach ($kdo as $x){
      DB::prechodMeziKmeny($x["id_rangera"],$x["navrhovanyKmen"]["id_kmenu"]);
    }
  }

  private function prechodJeden($kdo = array(),$id){
    /* Work in progress */
    foreach($kdo as $x){
      if($x["id_rangera"] == $id){
        DB::prechodMeziKmeny($x["id_rangera"],$x["navrhovanyKmen"]["id_kmenu"]);
        break;
      }
    }
  }
}
 ?>
