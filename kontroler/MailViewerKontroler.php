<?php
###########################
# Třída MailViewerKontroler #
###########################
/*
  Třída má na starost zobrazení obsahu odeslaných emailů
*/
class MailViewerKontroler extends Kontroler{

  // Metoda zpracuj
  public function zpracuj($parametry){
    // Ověření přihlášení uživatele
    if($this->sessionCheck()){
      $this->pohled = "nastenka";
    }
    else{
      $this->presmeruj("login");
    }

    // Parametry kontroleru
    if(isset($parametry[0])){
      $parametr = $parametry[0];
      $email = DB::getEmail($parametr);
      if(isset($email["zprava"])){
        $this->pohled="mail-viewer";
        $this->data["email"] = $email["zprava"];
      }
      else{
        $this->presmeruj("mail");
      }
    }
    else{
      $this->presmeruj("mail");
    }

  }
}
 ?>
