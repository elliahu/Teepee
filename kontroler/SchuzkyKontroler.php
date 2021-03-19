<?php
class SchuzkyKontroler extends Kontroler{

  public function zpracuj($parametry){

    if($this->sessionCheck() === false){
      $this->presmeruj("login");
    }
    global $config;
    $this->data["config"] = $config;

    if(isset($parametry[0])){
      $parametr = $parametry[0];
    }
    else{
      $parametr = null;
    }

    switch ($parametr) {
      case "smazat":
        if(isset($parametry[1])){
          $this->smazatSchuzku($parametry[1]);
        }
        else{
          $this->prehled();
        }
        break;

      case "quick":
        $this->rychlaSchuzka();
        break;

      case "upravit":
        if(isset($parametry[1])){
          $this->upravitSchuzku($parametry[1]);
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

  private function prehled(){
    $vsechnySchuzky = DB::vsechnySchuzky();
    if(!empty($vsechnySchuzky)){
      $this->data["vsechnySchuzky"] = $vsechnySchuzky;
    }
    else{
      $this->data["vsechnySchuzky"] = array("Žádné schuzky k vypsání");
    }
    $this->pridatSchuzku();
    //$this->pridatViceSchuzek();
    $this->pohled = "schuzky";
  }

// metoda pro přidání jedné schůzky
  private function pridatSchuzku(){
    if(isset($_POST["pridat-schuzku"])){
      if(!empty($_POST["datum"])){
        if(!empty($_POST["popis"]))
          $popis = $_POST["popis"];
        else
          $popis = "Běžná schůzka";
        if(DB::pridejSchuzku(array($_POST["datum"],$popis,$_SESSION["uzivatel"]->id_vedouciho))){
          $this->pridejZpravu("Schůzka byla přidána !","success");
          $this->presmeruj("dochazka/".$_SESSION["uzivatel"]->kmen->id_kmenu."/".$_SESSION["uzivatel"]->id_vedouciho);
        }
        else{
          $this->pridejZpravu("Schůzka s tímto datem již byla vytvořena ! ","danger");
          $this->presmeruj("schuzky");
        }
      }
      else{
        $this->pridejZpravu("Nikdo nemá rád vyplňování, ale aspoň to datum by to chtělo ...","danger");
        $this->presmeruj("schuzky");
      }
    }
  }


  private function smazatSchuzku($id){
    $schuzka = DB::getSchuzka($id);
    if(($_SESSION["uzivatel"]->id_vedouciho == $schuzka["pridal"]) || ($_SESSION["uzivatel"]->uroven >= SUPERUSER)){
      DB::smazatSchuzku($id);
      $this->pridejZpravu("Schůzka byla smazána !","success");
      $this->presmeruj("schuzky");
    }
    else{
      $this->pridejZpravu("Pro mazání schůzek nemáte dostatečné oprávnění :(","danger");
      $this->presmeruj("schuzky");
    }
  }

  private function upravitSchuzku($id){
    $schuzka = DB::getSchuzka($id);
    if(($_SESSION["uzivatel"]->id_vedouciho == $schuzka["pridal"]) || ($_SESSION["uzivatel"]->uroven >= EDITOR)){
      $this->pohled = "schuzky_uprav";
      $this->data["schuzka"] = $schuzka;
      if(isset($_POST["upravit-schuzku"])){
        if(!empty($_POST["datum"]) && !empty($_POST["popis"])){
          $pridal = $_SESSION["uzivatel"]->id_vedouciho;
          DB::upravSchuzku(array($_POST["datum"],$_POST["popis"],$pridal,$id));
          $this->pridejZpravu("Upraveno","success");
          $this->presmeruj("schuzky");
        }
        else{
          $this->pridejZpravu("Nikdo nemá rád vyplňování, ale musíme to znát všechno ... Vyplňte všechna pole !","danger");
          $this->presmeruj("schuzky");
        }
      }
    }
    else{
      $this->pridejZpravu("Pro editaci schůzek nemáte dostatečné oprávnění :(","danger");
      $this->presmeruj("schuzky");
    }
  }

  private function rychlaSchuzka(){
    global $config;
    if(date('l', strtotime('Today')) != $config->denSchuzky){
      $datum = date('Y-m-d', strtotime('next '.$config->denSchuzky))."";
    }
    else{
      $datum = date('Y-m-d', strtotime('Today'))."";
    }
    if(DB::pridejSchuzkuRychle($datum,$_SESSION["uzivatel"]->id_vedouciho) != false){
      $this->pridejZpravu("Schůzka $datum byla přidána !","success");
      $this->presmeruj("nastenka");
    }
    else{
      $this->pridejZpravu("Schůzka s tímto datem již byla vytvořena ! ","danger");
      $this->presmeruj("schuzky");
    }
  }



}
 ?>
