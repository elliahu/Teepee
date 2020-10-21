<?php
class DochazkaKontroler extends Kontroler{
  public function zpracuj($parametry){
    if($this->sessionCheck() === false){
      $this->presmeruj("login");
    }
    $this->pohled = "dochazka";
    //Data v pohledu
    $this->data["vsechnyKmeny"] = DB::nazvyVsechKmenu();
    $this->data["vsechnySchuzky"] = DB::nejblizsiSchuzky();
    $this->data["upravy"] = false;
    global $config;
    $this->data["config"] = $config;
    //Logika
    if(isset($parametry[0]) && ($parametry[0] != "")){
      $kmen = $parametry[0];
      $this->data["IDkmenu"] = $kmen;
      if(isset($parametry[1]) && ($parametry[1] != "")){
        $schuzka = $parametry[1];
        $this->data["IDschuzky"] = $schuzka;
        $this->data["deti"] = DB::detiPodleKmene($kmen);
        $this->data["vybranyKmen"] = DB::nazevKmenu($kmen);
        $this->data["vybranaSchuzka"] = DB::getSchuzka($schuzka);

        $zapsaneBody = DB::hledejZapsaneBody($schuzka,$kmen);
        if(isset($zapsaneBody[0]["id_rangera"])){
            $this->data["upravy"] = true;
            $this->data["zapsaneBody"] = $zapsaneBody;
            $this->zapisBody();
        }
        else{
          $this->data["zapsaneBody"] = 0;
          $this->zapisBody();
        }
      }
      else{
        $schuzka = null;
        $this->data["IDschuzky"] = $schuzka;
        $this->data["vybranyKmen"] = DB::nazevKmenu($kmen);
        $this->data["vybranaSchuzka"] = DB::getSchuzka($schuzka);
      }
    }
    else{
      $kmen = null;
      $this->data["IDkmenu"] = $kmen;
      $schuzka = null;
      $this->data["IDschuzky"] = $schuzka;
      $this->data["vybranyKmen"] = DB::nazevKmenu($kmen);
      $this->data["vybranaSchuzka"] = DB::getSchuzka($schuzka);
    }
    $this->data["deti"] = DB::detiPodleKmene($kmen);
  }

  private function zapisBody(){
    if(isset($_POST["body"])){
      if(!empty($_POST["schuzka"])){
        //Projde vsechny děti v kmenu a pokud jim byly uděleny body, vloží zánam, pokud ne vloží záznam s 0 body
        foreach($this->data["deti"] as $dite){
          if(isset($_POST["d-".$dite["id_rangera"]])){
            if(isset($_POST["o-".$dite["id_rangera"]])){
              switch ($_POST["o-".$dite["id_rangera"]]) {
                case 'pritomen':
                  DB::pridejZaznamDochazky($dite["id_rangera"],$this->data["IDschuzky"],$_POST["d-".$dite["id_rangera"]],"pritomen");
                  DB::pridejBody($dite["id_rangera"],$_POST["d-".$dite["id_rangera"]]);
                  break;
                case 'pritomen-pozde':
                  DB::pridejZaznamDochazky($dite["id_rangera"],$this->data["IDschuzky"],$_POST["d-".$dite["id_rangera"]],"pritomen-pozde");
                  DB::pridejBody($dite["id_rangera"],$_POST["d-".$dite["id_rangera"]]);
                  break;
                case 'omluven':
                  DB::pridejZaznamDochazky($dite["id_rangera"],$this->data["IDschuzky"],$_POST["d-".$dite["id_rangera"]],"omluven");
                  DB::pridejBody($dite["id_rangera"],$_POST["d-".$dite["id_rangera"]]);
                  break;
                case 'neomluven':
                  DB::pridejZaznamDochazky($dite["id_rangera"],$this->data["IDschuzky"],$_POST["d-".$dite["id_rangera"]],"neomluven");
                  break;
                default:
                  DB::pridejZaznamDochazky($dite["id_rangera"],$this->data["IDschuzky"],$_POST["d-".$dite["id_rangera"]],"neomluven");
                  break;
              }

            }
          }
        }
        $this->pridejZpravu("Uloženo","success");
        $this->presmeruj("dochazka/".$this->data["IDkmenu"]."/".$this->data["IDschuzky"]);
      }
      else{
        $this->pridejZpravu("Vyberte schůzku", "primary");
        $this->presmeruj("dochazka/".$_SESSION["uzivatel"]->id_kmenu);
      }
    }
    // Uprava jiz existujicich zaznamu
    if(isset($_POST["uprav-body"])){
      if(!empty($_POST["schuzka"])){
        //Projde vsechny děti v kmenu a pokud jim byly uděleny body, vloží zánam, pokud ne vloží záznam s 0 body
        foreach($this->data["zapsaneBody"] as $dite){
          if(isset($_POST["d-".$dite["id_rangera"]])){
            if(isset($_POST["o-".$dite["id_rangera"]])){
              switch ($_POST["o-".$dite["id_rangera"]]) {
                case 'pritomen':
                  DB::upravZaznamDochazky($dite["id_rangera"],$this->data["IDschuzky"],$_POST["d-".$dite["id_rangera"]],"pritomen");
                  DB::pridejBody($dite["id_rangera"],(-1)*$dite["body"]);
                  DB::pridejBody($dite["id_rangera"],$_POST["d-".$dite["id_rangera"]]);
                  break;
                case 'pritomen-pozde':
                  DB::upravZaznamDochazky($dite["id_rangera"],$this->data["IDschuzky"],$_POST["d-".$dite["id_rangera"]],"pritomen-pozde");
                  DB::pridejBody($dite["id_rangera"],(-1)*$dite["body"]);
                  DB::pridejBody($dite["id_rangera"],$_POST["d-".$dite["id_rangera"]]);
                  break;
                case 'omluven':
                  DB::upravZaznamDochazky($dite["id_rangera"],$this->data["IDschuzky"],$_POST["d-".$dite["id_rangera"]],"omluven");
                  DB::pridejBody($dite["id_rangera"],(-1)*$dite["body"]);
                  DB::pridejBody($dite["id_rangera"],$_POST["d-".$dite["id_rangera"]]);
                  break;
                case 'neomluven':
                  DB::upravZaznamDochazky($dite["id_rangera"],$this->data["IDschuzky"],$_POST["d-".$dite["id_rangera"]],"neomluven");
                  break;
                default:
                  DB::upravZaznamDochazky($dite["id_rangera"],$this->data["IDschuzky"],$_POST["d-".$dite["id_rangera"]],"neomluven");
                  break;
              }

            }
          }
        }
        $this->pridejZpravu("Uloženo","success");
        $this->presmeruj("dochazka/".$this->data["IDkmenu"]."/".$this->data["IDschuzky"]);
      }
      else{
        $this->pridejZpravu("Vyberte schůzku", "primary");
        $this->presmeruj("dochazka/".$_SESSION["uzivatel"]->id_kmenu);
      }
    }
  }

}
 ?>
