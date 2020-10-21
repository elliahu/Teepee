<?php
###########################
# Třída UzivatelskeNastaveniKontroler #
###########################
/*
  Třída má na starost správu uzivatelskoho nastavení 
*/
class UzivatelskeNastaveniKontroler extends Kontroler{

  // Metoda zpracuj
  public function zpracuj($parametry){
    // Ověření přihlášení uživatele
    if($this->sessionCheck() === false){
        $this->presmeruj("login");
    }
    $this->data["user_config"] = $_SESSION["user_config"];

    $this->pohled = "uzivatelske_nastaveni";

    //nastveni
    $this->zmenUzivatelskeJmeno();
    $this->zmenHeslo();
    $this->zmenNastaveni();
  }

  private function zmenNastaveni(){
    if(isset($_POST["zmenit_nastaveni"])){
      if(!isset($_POST["cas"]))
        $_POST["cas"] = 2;
      $usrCfg = $this->data["user_config"];
      $usrCfg->upozorneni->skrytPo = $_POST["cas"];
      file_put_contents("cfg/user_cfg/user_cfg_".$_SESSION["uzivatel"]->id_vedouciho.".json",json_encode($usrCfg));
      $this->pridejZpravu("Uživatelské nastavení změněno","success");
      $this->presmeruj("uzivatelske-nastaveni");
    }
  }
  
 //Metoda pro změnu uživatelského jména
  private function zmenUzivatelskeJmeno(){
    if(isset($_POST["zmenit-jmeno"])){
      if(isset($_POST["jmeno"])){
        if(preg_match('/^[a-zA-Z0-9]{3,20}$/', $_POST["jmeno"])) { 
          if($this->overUnikatniUzivatelskeJmeno($_POST["jmeno"])){
            $this->aktualizujSession($_POST["jmeno"]);
            DB::upravUzJmeno(array($_POST["jmeno"],$_SESSION["uzivatel"]->id_vedouciho));
            $this->pridejZpravu("Uživatelské jméno změněno na ".$_POST["jmeno"],"success");
            $this->presmeruj("uzivatelske-nastaveni");
          }
          else{
            $this->pridejZpravu("Uživatelské jméno je již zabrané","danger");
            $this->presmeruj("uzivatelske-nastaveni");
          }
        }
        else{
          $this->pridejZpravu("Uživatelské jméno obsahuje nepovolené znaky, nebo je příliš dlouhé","danger");
          $this->presmeruj("uzivatelske-nastaveni");
        }
      }
      else{
        $this->pridejZpravu("Vaše uživatelské jméno se nezměnilo","danger");
        $this->presmeruj("uzivatelske-nastaveni");
      }
    }
  }

  private function overUnikatniUzivatelskeJmeno($jmeno){
    $uzivatel = DB::getUzivatel($jmeno);
    if(isset($uzivatel->id_vedouciho)){
      return false;
    }
    else{
      return true;
    }
  }

  private function zmenHeslo(){
    if(isset($_POST["zmenit-heslo"])){
      if(isset($_POST["stare_heslo"]) && isset($_POST["nove_heslo"]) && isset($_POST["nove_heslo_znovu"])){
        if($this->overStareHeslo($_POST["stare_heslo"],$_SESSION["uzivatel"]->id_vedouciho)){
          if($_POST["nove_heslo"] == $_POST["nove_heslo_znovu"]){
            if(preg_match("#.*^(?=.{8,20})(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9]).*$#", $_POST["nove_heslo"])){
              DB::upravHeslo(array(password_hash($_POST["nove_heslo"],PASSWORD_DEFAULT),$_SESSION["uzivatel"]->id_vedouciho));
              $this->pridejZpravu("Heslo bylo upraveno","success");
              $this->presmeruj("uzivatelske-nastaveni");
            }
            else{
              $this->pridejZpravu("Heslo není dostatečně silné","danger");
              $this->presmeruj("uzivatelske-nastaveni");
            }
          }
          else{
            $this->pridejZpravu("Hesla nejsou stejná","danger");
            $this->presmeruj("uzivatelske-nastaveni");
          }
        }
        else{
          $this->pridejZpravu("Staré heslo je špatně","danger");
          $this->presmeruj("uzivatelske-nastaveni");
        }
      }
      else{
        $this->pridejZpravu("Heslo nebylo změněno. Vyplňte všechna pole !","danger");
        $this->presmeruj("uzivatelske-nastaveni");
      }
    }
  }

  private function overStareHeslo($heslo,$id_vedouciho){
    $hesloTest = DB::getHeslo($id_vedouciho);
    if(password_verify($heslo,$hesloTest["heslo"])){
      return true;
    }
    return false;
  }


}
 ?>
