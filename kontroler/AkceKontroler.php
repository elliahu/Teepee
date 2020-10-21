<?php
#######################
# Třída AkceKontroler #
#######################
/*
  Třída má na starost správu akcí tzn. Přidávání, odstraňování,
  editace
*/
class AkceKontroler extends Kontroler{

  public function zpracuj($parametry){
    //Ověření přihlášení uživatele
    if($this->sessionCheck() === false){
      $this->presmeruj("login");
    }

    // Předání parametrů kontroleru
    if(isset($parametry[0])){
      $parametr = $parametry[0];
    }
    else{
      $parametr = null;
    }

    //Řízení na základě parametrů kontroleru
    switch ($parametr) {
      // Mazání akcí
      case "smazat":
        // Kontroler očekává další parametr tj. id akce, kterou má smazat
        if(isset($parametry[1])){
          // smazání příslušné akce
          $this->smazatAkci($parametry[1]);
        }
        else{
          // nebyl předán očekávaný parametr tj. id akce
          // Mazání neproběhlo
          $this->prehled();
        }
        break;

      // Úpravy akcí
      case "upravit":
        // Kontroler očekává další parametr tj. id akce, kterou má upravit
        if(isset($parametry[1])){
          // upravy příslušné akce
          $this->upravitAkci($parametry[1]);
        }
        else{
          // Nebyl předán očekávaný parametr upravy neproběhly
          $this->prehled();
        }
        break;

      // Přehled všech akcí
      default:
        $this->prehled();
        break;
    }

  }

  // Metoda zobrazuje přehled všech akcí
  private function prehled(){
    $vsechnyAkce = DB::vsechnyAkce();
    if(!empty($vsechnyAkce)){
      $this->data["vsechnyAkce"] = $vsechnyAkce;
    }
    else{
      $this->data["vsechnyAkce"] = array();
    }
    $this->pridatAkci();
    $this->pohled = "akce";
  }

// metoda pro přifání jedné akce
  private function pridatAkci(){
    if(isset($_POST["pridat-akci"])){
      if(!empty($_POST["zacatek"]) && !empty($_POST["konec"]) && !empty($_POST["nazev"]) && !empty($_POST["vedouci_akce"])){
        if(empty($_POST["popis"]) )
          $popis = "Víkendová akce";
        else
          $popis = $_POST["popis"];
        DB::pridejAkci(array($_POST["zacatek"],$_POST["konec"],$_POST["nazev"],$popis,$_POST["vedouci_akce"],$_SESSION["uzivatel"]->id_vedouciho));
        $this->pridejZpravu("Akce ".$_POST["nazev"]." byla přidána !","success");
        $this->presmeruj("ucast-akce/".DB::idPoslednihoVlozeneho());
      }
      else{
        $this->pridejZpravu("Nikdo nemá rád vyplňování, ale bez toho to fungovat nebude !","danger");
        $this->presmeruj("akce");
      }
    }
  }

  // Mazání akcí
  private function smazatAkci($id){
    $akce = DB::getAkce($id);
    if(($_SESSION["uzivatel"]->id_vedouciho == $akce["pridal"]) || ($_SESSION["uzivatel"]->uroven >= SUPERUSER)){
      DB::smazatAkci($id);
      $this->pridejZpravu("Akce byla smazána !","success");
      $this->presmeruj("akce");
    }
    else{
      $this->pridejZpravu("Pro mazání akcí nemáte dostatečné oprávnění","danger");
      $this->presmeruj("akce");
    }
  }

  // Upravy akcí
  private function upravitAkci($id){
    $akce = DB::getAkce($id);
    if(($_SESSION["uzivatel"]->id_vedouciho == $akce["pridal"]) || ($_SESSION["uzivatel"]->uroven >= EDITOR)){
      $this->pohled = "akce_uprav";
      $this->data["akce"] = $akce;
      if(isset($_POST["upravit-akci"])){
        if(!empty($_POST["zacatek"]) && !empty($_POST["konec"]) && !empty($_POST["nazev"]) && !empty($_POST["popis"]) && !empty($_POST["vedouci_akce"])){
          $pridal = $_SESSION["uzivatel"]->id_vedouciho;
          DB::upravAkci($id,$_POST["zacatek"],$_POST["konec"],$_POST["nazev"],$_POST["popis"],$_POST["vedouci_akce"],$_SESSION["uzivatel"]->id_vedouciho);
          $this->pridejZpravu("Upraveno","success");
          $this->presmeruj("akce");
        }
        else{
          $this->pridejZpravu("Nikdo nemá rád vyplňování, ale musíme to znát všechno ... Vyplňte všechna pole !","danger");
          $this->presmeruj("akce/uprav/$id");
        }
      }
    }
    else{
      $this->pridejZpravu("Pro editaci akcí nemáte dostatečné oprávnění :(","danger");
      $this->presmeruj("akce");
    }
  }

}
 ?>
