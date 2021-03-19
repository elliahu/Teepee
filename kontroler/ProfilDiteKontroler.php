<?php
class ProfilDiteKontroler extends Kontroler{

  public function zpracuj($parametry){

    if($this->sessionCheck() === false){
      $this->presmeruj("login");
    }

    if(isset($parametry[0])){
      $IDprofilu = $parametry[0];
      $dite = DB::getDite($IDprofilu);
      $dite->kmen = DB::getKmen($dite->id_kmenu);
      $this->data["dite"] = $dite;
      // statistika ditete
      $this->statistika($IDprofilu);
    }
    else{
      $this->presmeruj("deti");
    }
    $this->pohled = "profil-dite";
    $this->upravProfil();
    
    
    
  }

  private function upravProfil(){
      if(isset($_POST["profil"])){
        if(!empty($_POST["jmeno"]) && !empty($_POST["prijmeni"])
        && !empty($_POST["datum_narozeni"]) && !empty($_POST["email"])
        && !empty($_POST["tel"]) && !empty($_POST["id_kmenu"])){
          if(empty($_POST["prezdivka"])){
            $prezdivka = null;
          }
          else{
            $prezdivka = $_POST["prezdivka"];
          }
          $poleHodnot = array(
            $_POST["jmeno"],
            $prezdivka,
            $_POST["prijmeni"],
            $_POST["datum_narozeni"],
            $_POST["email"],
            $_POST["tel"],
            $_POST["id_kmenu"],
            $this->data["dite"]->id_rangera
          );
          DB::upravProfilDitete($poleHodnot);
          $this->pridejZpravu("Profil byl úspěšně upraven !","success");
          $this->presmeruj("profil-dite/".$this->data["dite"]->id_rangera);
        }
        else{
          $this->pridejZpravu("Nikdo nemá rád vyplňování, ale musíme to znát všechno ... Vyplňte všchna pole !","danger");
          $this->presmeruj("profil-dite/".$this->data["dite"]->id_rangera);
        }
      }
  }

  private function statistika($IDprofilu){
    $schuzky = DB::vsechnySchuzky();
    $prubeh = DB::statistikaDetiPrubehPoTydnech($IDprofilu);
    $this->data["schuzky"] = $schuzky;
    $this->data["prubeh"] = $prubeh;
    $this->data["prumernaUcastSchuzky"] =round( DB::statistikaDetiPrumernaUcastNaSchuzkach($IDprofilu),0);
    $this->data["pritomen"] = DB::statistikaDetiPritomen($IDprofilu);
    $this->data["nepritomen"] = DB::statistikaDetiNepritomen($IDprofilu);
    $this->data["omluvenaAbsence"] = round( DB::statistikaDetiOmluvenaAbsence($IDprofilu),0);
  }

}
?>
