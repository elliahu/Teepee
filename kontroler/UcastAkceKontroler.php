<?php
class UcastAkceKontroler extends Kontroler{

  // Main
  public function zpracuj($parametry){

    //Dostupne pouze přihlášeným uživatelum
    if ($this->sessionCheck() === false) {
      $this->presmeruj("login");
    }

    $this->data["upravy"] = false;
    global $config;
    $this->data["config"] = $config;

    // $parametry[0] = id akce  
    if (isset($parametry[0])) {
      $id_akce = $parametry[0];
      $this->data["vybranaAkce"] = DB::getAkce($id_akce);
      $this->data["deti"] = DB::vsechnyDeti();
      //Rozhoduje zda vybrane akci již byla zapsaná učat
      if($this->akceJeZapsana($id_akce)){ // Ucast byla zapsana -> upravy
        $this->data["upravy"] = true;
        $this->data["ucast"] = DB::getZaznamUcastAkce($id_akce);
        $this->zapisBody();
      }
      else{ // Ucast jeste nebyla zasana -> novy zapis
        $this->zapisBody();
      }
    } else {
      $id_akce = null;
    }
    $this->data["vsechnyAkce"] = DB::vsechnyAkce();
    $this->pohled = "ucast-akce";
  }

  //Metoda pro zapis bodů  
  private function zapisBody()  {
    if (isset($_POST["body"])) {
      // o-* stav, d-* body
      if (!empty($_POST["schuzka"])) {
        //Projde deti a přidá záznam o účasti na akci
        foreach ($this->data["deti"] as $dite) {
          if (isset($_POST["d-" . $dite["id_rangera"]])) {
            if (isset($_POST["o-" . $dite["id_rangera"]])) {
              switch ($_POST["o-" . $dite["id_rangera"]]) {
                case 'pritomen':
                  $this->pridejZaznam($dite["id_rangera"],$_POST["schuzka"],$_POST["d-".$dite["id_rangera"]],"pritomen");
                  break;
                case 'omluven':
                  $this->pridejZaznam($dite["id_rangera"],$_POST["schuzka"],$_POST["d-".$dite["id_rangera"]],"omluven");
                  break;
                case 'neomluven':
                  $this->pridejZaznam($dite["id_rangera"],$_POST["schuzka"],$_POST["d-".$dite["id_rangera"]],"neomluven");
                  break;
                default:
                  $this->pridejZaznam($dite["id_rangera"],$_POST["schuzka"],$_POST["d-".$dite["id_rangera"]],"neomluven");
                  break;
              }
            }
          }
        }
        $this->pridejZpravu("Záznam byl přidán","success");
            $this->presmeruj("ucast-akce/".$_POST["schuzka"]);
      } else {
        $this->pridejZpravu("Vyberte schůzku", "primary");
        $this->presmeruj("ucast-akce");
      }
    }
    //Upravy existujicích záznamů
    if(isset($_POST["body-upravit"])){
      if (!empty($_POST["schuzka"])) {
        //Projde deti a přidá záznam o účasti na akci
        foreach ($this->data["deti"] as $dite) {
          if (isset($_POST["d-" . $dite["id_rangera"]])) {
            if (isset($_POST["o-" . $dite["id_rangera"]])) {
              switch ($_POST["o-" . $dite["id_rangera"]]) {
                case 'pritomen':
                  $this->upravZaznam($dite["id_rangera"],$_POST["schuzka"],$_POST["d-".$dite["id_rangera"]],$dite["body"],"pritomen");
                  break;
                case 'omluven':
                  $this->upravZaznam($dite["id_rangera"],$_POST["schuzka"],$_POST["d-".$dite["id_rangera"]],$dite["body"],"omluven");
                  break;
                case 'neomluven':
                  $this->upravZaznam($dite["id_rangera"],$_POST["schuzka"],$_POST["d-".$dite["id_rangera"]],$dite["body"],"neomluven");
                  break;
                default:
                  $this->upravZaznam($dite["id_rangera"],$_POST["schuzka"],$_POST["d-".$dite["id_rangera"]],$dite["body"],"neomluven");
                  break;
              }
            }
          }
        }
        $this->pridejZpravu("Záznam byl upraven","success");
            $this->presmeruj("ucast-akce/".$_POST["schuzka"]);
      } else {
        $this->pridejZpravu("Vyberte schůzku", "primary");
        $this->presmeruj("ucast-akce");
      }
    }
  }

  //Metoda prida zaznam o ucasti na akci do databaze
  private function pridejZaznam($id_rangera, $id_schuzky, $pocetBodu, $stav)
  {
    DB::pridejZanznamUcastiNaAkci($id_schuzky,$id_rangera,$pocetBodu,$stav);
    DB::pridejBody($id_rangera,$pocetBodu);
  }

  private function upravZaznam($id_rangera, $id_schuzky, $pocetBodu,$pocetBoduPred, $stav)
  {
    DB::upravZaznamUcastiNaAkci($id_schuzky,$id_rangera,$pocetBodu,$stav);
    DB::pridejBody($id_rangera,$pocetBoduPred * (-1)); // Odecte pedchozi body
    DB::pridejBody($id_rangera,$pocetBodu); // prida novy pocet bodu
  }

   // testuje zda byla ucast na akci jiz zapsana 
  private function akceJeZapsana($id_akce){
    if(DB::getZaznamUcastAkce($id_akce) != null)
      return true;
    else 
      return false;
  }

  //Najde zaznam rangera v poli všech zaznamu jinak vrati false
  public function najdiZaznam($id_rangera,$zaznamy = array(array())){
    foreach($zaznamy as $zaznam){
      if(in_array($id_rangera,$zaznam))
        return $zaznam;
    }
    return false;
  }
}
