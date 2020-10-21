<?php
#
# Třída AplikaceKontroler
# má na starosti zobrazení a ovládání stránky
# se stažením desktopové a android/ios aplikace
class AplikaceKontroler extends Kontroler{

  public function zpracuj($parametry){
    // Přístupná pouze přihlášeným uživatelům
    if($this->sessionCheck() === false){
      $this->presmeruj("login");
    }
    //Pohled
    $this->pohled = "aplikace";

  }




}
 ?>
