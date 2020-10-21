<?php
class ProfilKontroler extends Kontroler{

  public function zpracuj($parametry){

    if($this->sessionCheck() === false){
      $this->presmeruj("login");
    }

    if(isset($parametry[0])){
      $IDprofilu = $parametry[0];
      $vedouci = DB::getVedouci($IDprofilu);
      $vedouci->kmen = DB::getKmen($vedouci->id_kmenu);
      $this->data["vedouci"] = $vedouci;
    }
    else{
      $this->presmeruj("vedouci");
    }
    $this->pohled = "profil";
    $this->upravProfil();
    $this->upravOpravneni();
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
            $this->data["vedouci"]->id_vedouciho
          );
          DB::upravProfil($poleHodnot);
          $this->pridejZpravu("Profil byl úspěšně upraven !","success");
          $this->aktualizujSession($_SESSION["uzivatel"]->jmeno);
          $this->presmeruj("profil/".$this->data["vedouci"]->id_vedouciho);
        }
        else{
          $this->pridejZpravu("Nikdo nemá rád vyplňování, ale musíme to znát všechno ... Vyplňte všchna pole !","danger");
          $this->presmeruj("profil/".$this->data["vedouci"]->id_vedouciho);
        }
      }
  }


  private function upravOpravneni(){
    if(isset($_POST["zmena-opravneni"])){
      DB::zmenOpravneni($this->data["vedouci"]->id_vedouciho,$_POST["opravneni"]);
      $this->pridejZpravu("Oprávnění bylo změněno","success");
      $this->presmeruj("profil/".$this->data["vedouci"]->id_vedouciho);
    }
  }
}
?>
