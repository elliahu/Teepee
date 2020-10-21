<?php
  class LogoutKontroler extends Kontroler{
    public function zpracuj($parametry){
      if($this->sessionCheck() === false){
        $this->presmeruj("login");
      }
      $_SESSION["login"] = false;
      $_SESSION["uzivatel"] = null;
      $_SESSION["vedouci"] = null;
      session_destroy();
      $this->presmeruj("login");
    }
  }
 ?>
