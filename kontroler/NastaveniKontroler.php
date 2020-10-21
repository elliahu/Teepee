<?php
###########################
# Třída NastaveniKontroler #
###########################
/*
  Třída má na starost správu globálního nastavení aplikace
*/
class NastaveniKontroler extends Kontroler{

  // Metoda zpracuj
  public function zpracuj($parametry){
    // Ověření přihlášení uživatele
    if($this->sessionCheck() === false){
        $this->presmeruj("login");
    }
    
    global $config;
    $this->data["config"] = $config;
    $this->pohled = "nastaveni";

    $this->ulozNastaveni();
  }
  private function ulozNastaveni(){
    if(isset($_POST["ulozit-nastaveni"])){
      unset($_POST["ulozit-nastaveni"]);
      global $config;
      $newConfig = (object) array_merge((array)$config,$_POST);

      //Zapíše hondoty do configu*/
      file_put_contents("cfg/config.json",json_encode($newConfig));
      $this->pridejZpravu("Nastavení aplikace uloženo","success");
      $this->presmeruj("nastaveni");
    }
  }

}
 ?>
