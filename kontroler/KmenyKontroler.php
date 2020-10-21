<?php
/*
    Třída KmenyKontroler
    Má nastarost spárvu kmenů
*/
class KmenyKontroler extends Kontroler{
  public function zpracuj($parametry){
    //Přístupné pouze přihlášeným uživatelům
    if($this->sessionCheck() === false){
      $this->presmeruj("login");
    }
    // Dostaneme parametry z odkazu
    if(isset($parametry[0])){
      $parametr = $parametry[0];
    }
    else{
      $parametr = null;
    }
    // Řízení na základě parametrů z odkazu
    switch ($parametr) {
      case "smazat":
        if(isset($parametry[1])){
          $this->smazatKmen($parametry[1]);
        }
        else{
          $this->prehled();
        }
        break;

      case "upravit":
        if(isset($parametry[1])){
          $this->upravitKmen($parametry[1]);
        }
        else{
          $this->prehled();
        }
        break;

      default:
        $this->prehled();
        break;
    }
  }
  // metoda zobrazuje přehled
  private function prehled(){
    $this->data["kmeny"] = DB::vsechnyKmeny();
    $this->pohled="kmeny";
    $this->novyKmen();
  }

  //Metoda zobrazuje vytvoření nového kmene
  private function novyKmen(){
    if(isset($_POST["pridat-kmen"])){
      if((!empty($_POST["nazev"])) && (isset($_POST["min_vek"])) && (!empty($_POST["max_vek"])) && (!empty($_POST["popis"]))){
        if(($_POST["min_vek"] > 0) && ($_POST["max_vek"] < 100)){
          if($_POST["min_vek"] < $_POST["max_vek"]){
            DB::pridatKmen(array($_POST["nazev"],$_POST["min_vek"],$_POST["max_vek"],$_POST["popis"]));
            $this->pridejZpravu("Kmen byl přidán","success");
            $this->presmeruj("kmeny");
          }
          else{
            $this->pridejZpravu("Minimální věk musí být menší než maximální věk","danger");
            $this->presmeruj("kmeny");
          }
        }
        else{
          $this->pridejZpravu("Věkový rozsah 1 - 100 let","danger");
          $this->presmeruj("kmeny");
        }
      }
      else{
        $this->pridejZpravu("Nikdo nemá rád vyplňování, ale musíme to znát všechno ... Vyplňte všechna pole !","danger");
        $this->presmeruj("kmeny");
      }
    }
  }

  private function smazatKmen($id){
    if(($_SESSION["uzivatel"]->uroven >= ADMIN)){
      DB::smazatKmen($id);
      $this->pridejZpravu("Kmen byl smazán","success");
      $this->presmeruj("kmeny");
    }
    else{
      $this->pridejZpravu("Pro mazání kmenů nemáš dostatečné oprávnění","danger");
      $this->presmeruj("kmeny");
    }
  }

  private function upravitKmen($id){
    $kmen = DB::getKmen($id);
    if(($_SESSION["uzivatel"]->uroven >= EDITOR)){
      $this->pohled = "kmeny_uprav";
      $this->data["kmen"] = $kmen;
      if(isset($_POST["upravit-kmen"])){
        if((!empty($_POST["nazev"])) && (!empty($_POST["min_vek"])) &&(!empty($_POST["max_vek"])) && (!empty($_POST["popis"]))){
          DB::upravitKmen(array($_POST["nazev"],$_POST["min_vek"],$_POST["max_vek"],$_POST["popis"],$id));
          $this->pridejZpravu("Kmen s id = $id byl upraven","success");
          $this->presmeruj("kmeny");
        }
      }
    }
    else{
      $this->pridejZpravu("Pro úpravu kmenů nemáš dostatečné oprávnění","danger");
      $this->presmeruj("kmeny");
    }
  }
}
 ?>
