<?php
#######################
# Třída BodyKontroler #
#######################
/*
  Třída má na starost správu bodů -> mazání, přidávání, síň slávy
*/
class BodyKontroler extends Kontroler{

  public function zpracuj($parametry){
    // Ověření přihlášení uživatele
    if($this->sessionCheck() === false){
      $this->presmeruj("login");
    }

    //Předání parametrů kontroleru
    if(isset($parametry[0])){
      $parametr = $parametry[0];
    }
    else{
      $parametr = null;
    }

    //Řízení podle parametrů konroleru
    switch ($parametr) {
      // Přidání bodů
      // V případě přidání záorného počtu bodů dojde k odečtu bodů
      case "pridat":
        if(isset($parametry[1])){
          $id = $parametry[1];
          $this->data["dite"] = DB::getDite($id);
          $this->pridatBody($id);
        }
        else{
          $this->pridatBody(null);
        }
        break;

      // Přehled bodů
      default:
        $this->prehled();
        break;
    }

  }

  // Přehled bodů
  private function prehled(){
    $prehled = DB::getBody();
    $this->data["prehled"] = $prehled;
    $this->pohled = "body-prehled";
  }

  //Přidání bodů
  private function pridatBody($id){
    if($_SESSION["uzivatel"]->uroven >= USER){
      if($id !== null){
        $this->data["id_ditete"] = $id;
      }
      if(isset($_POST["pridat"])){
        if(!empty($_POST["id"]) && !empty($_POST["pocet_bodu"]) && !empty($_POST["duvod"])){
          DB::pridejBody($_POST["id"],$_POST["pocet_bodu"]);
          DB::zaznamHistorieBodu($_POST["id"],$_SESSION["uzivatel"]->id_vedouciho,$_POST["pocet_bodu"],$_POST["duvod"]);
          $this->pridejZpravu("Přidáno ".$_POST["pocet_bodu"]." bodů. Důvod: ".$_POST["duvod"],"success");
          $this->presmeruj("body");
        }
        else{
          $this->pridejZpravu("Nikdo nemá rád vyplňování, ale bez toho to fungovat nebude !","danger");
          $this->presmeruj("body/pridat/$id");
        }
      }
      $this->pohled = "body-pridat";
    }
    else{
      $this->pridejZpravu("Nemáte dostatečné oprávnění pro přidávání bodů","danger");
      $this->presmeruj("body");
    }
  }

}
 ?>
