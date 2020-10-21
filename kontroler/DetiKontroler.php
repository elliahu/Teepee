<?php
#######################
# Třída Detikontroler #
#######################
/*
  Třída má na starost správu dětí -> přidání, mazání, přechod na profil
  editaci údajů zajišťuje třída ProfilDiteKontroler "kontroler\ProfilDiteKontrole.php"
*/
class DetiKontroler extends Kontroler{

  public function zpracuj($parametry){
    // Ověření přihlášení uživatele
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

    // Řízení podle parametrů kontroleru
    switch ($parametr) {
      // Přidat dítě
      case "novy":
        $this->novy();
        break;
      //smazat záznamy dítěte
      case "smazat":
        if(isset($parametry[1])){
          $this->smazatDite($parametry[1]);
        }
        else{
          $this->prehled("");
        }
        break;
      // Zobrazení záznamů podle kmene
      case "sort":
        if(isset($parametry[1])){
          $this->prehled($parametry[1]);
        }
        else{
          $this->prehled("");
        }
        break;
      // celkový přehled
      default:
        $this->prehled("");
        break;
    }
  }
  // Přehled všech dětí
  private function prehled($sortBy){
    if($sortBy == ""){
      $deti = DB::vsechnyDeti();
      $this->data["deti"]=$deti;
      $this->pohled = "deti";
    }
    else{
      $sortBy = $this->camelCase($sortBy);
      $deti = DB::vsechnyDeti($sortBy);
      $this->data["deti"]=$deti;
      $this->pohled = "deti";
    }
  }
  //Přidání nového dítěte
  private function novy(){
    if($_SESSION["uzivatel"]->uroven < SUPERUSER){
      $this->pridejZpravu("Pro přidání dětí nemáš dostatečná oprávnění !","danger");
      $this->presmeruj("deti");
    }
    else{
      if(isset($_POST["pridat-dite"])){
        if(!empty($_POST["jmeno"]) && !empty($_POST["prijmeni"])
        && !empty($_POST["datum_narozeni"]) && !empty($_POST["email"]) && !empty($_POST["tel"])
        && !empty($_POST["kmen"])){
          if(empty($_POST["prezdivka"])){
            $prezdivka = "";
          }
          else{
            $prezdivka = $_POST["prezdivka"];
          }
          DB::pridatDite(array($_POST["jmeno"],$prezdivka,$_POST["prijmeni"],$_POST["datum_narozeni"],$_POST["email"],
                                $_POST["tel"],$_POST["kmen"]));

          $this->pridejZpravu($_POST["jmeno"]." ".$_POST["prijmeni"]." úspěšně přidán/a","success");
          $this->presmeruj("deti");
        }
        else{
          $this->pridejZpravu("Nikdo nemá rád vyplňování, ale musíme to znát všechno ... Vyplň všchna pole !","danger");
          $this->presmeruj("deti/novy");
        }
      }
      $this->pohled = "deti-novy";
    }
  }
// Smazání záznamu dítěte
  private function smazatDite($id){
      if($_SESSION["uzivatel"]->uroven >= ADMIN){
        DB::smazatDite($id);
        $this->pridejZpravu("Dítě s id = $id bylo úspěšně smazáno !","success");
        $this->presmeruj("deti");
      }
      else{
        $this->pridejZpravu("Pro mazání dětí nemáš dostatečné opávnění ","danger");
        $this->presmeruj("deti");
      }
  }
// camel case
  public function camelCase($text) {
      $text = str_replace("-", " ", $text);
      $text = ucwords($text);
      return $text;
  }
// reverse camel case
  public function reverseCamelCase($text) {
      $text = str_replace(" ", "-", $text);
      $text = strtolower($text);
      return $text;
  }

}
 ?>
