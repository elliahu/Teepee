<?php
#########################
# Třída RouterKontroler #
#########################
/*
  Převezme parametry url adresy a rozdělí je do pole
  1. prvek pole je požadovaný kontroler
  2. - n. prvek pole jsou parametry požadovaného kontroleru
*/
class RouterKontroler extends Kontroler{
  protected $kontroler;
  // předání parametrů odkazu
  public function zpracuj($parametry){
    $castiCesty = $this->parseURL($parametry[0]);

    //API request
    if($castiCesty[0] == "api"){
      $api = new API($castiCesty);
      $api->respond();
      exit();
    }

    // domovská stránka (načte se při zadání domény)
    if(empty($castiCesty[0])){
      $this->presmeruj("nastenka");
    }

    // Zjištění požadovaného kontroleru
    $castNazKont = $this->camelCase(array_shift($castiCesty));
    $tridaKontroleru = $castNazKont . "Kontroler";

    // Ověření existence požadovaného kontroleru
    if(file_exists("kontroler/$tridaKontroleru.php"))
      $this->kontroler = new $tridaKontroleru();
    else{
      $this->pridejZpravu("<b>404</b> <br> To, co hledáš, tady není","danger");
      $this->presmeruj("nastenka");
    }
    


    // Předání řízení příslušnému kontroleru
    $this->kontroler->zpracuj($castiCesty);
    $this->data["zpravy"] = $this->vratZpravy();
    $this->data["historie_zprav"] = $this->vratHistoriiZprav();
    $this->data["user_config"] = $this->nasctiUserConfig((isset($_SESSION["login"]) && $_SESSION["login"] === true)? true : false );
    $this->pohled = "template";
  }

  // Metoda převádí názvy url friendly kontrolerů na user friendly názvy kontrolerů
  //name-of-controler -> NameOfController
  private function camelCase($text) {
      $text = str_replace("-", " ", $text);
      $text = ucwords($text);
      $text = str_replace(" ", "", $text);
      return $text;
  }

  //Metoda pro parsování URL
  private function parseURL($url) {
      $parsedURL = parse_url($url);
      $path = $parsedURL["path"];
      $path = ltrim($path,"/");
      $path = trim($path);
      $cutPath = explode("/", $path);
      return $cutPath;
  }

  private function nasctiUserConfig($login = false){
    if($login){
      $user_config = json_decode(file_get_contents("cfg/user_cfg/user_cfg_".$_SESSION["uzivatel"]->id_vedouciho.".json"));
      return $user_config;
    }
    else{
      $user_config = json_decode(file_get_contents("model/UserConfigClass.json"));
      return $user_config;
    }
  }

}
