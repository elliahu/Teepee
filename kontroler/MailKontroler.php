<?php
###########################
# Třída MailKontroler #
###########################
/*
  Třída má na starost odesílání emailů a správu odeslaných emailů
*/
class MailKontroler extends Kontroler{

  private $emailToShow;
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
    }
    else{
      $parametr = null;
    }
    //Řízení podle parametrů kontroleru
    switch ($parametr) {
      case "odeslat":
        $this->odeslat();
        break;

      default:
        $this->emailToShow = $parametry[0];
        $this->prehled();
        break;
    }
  }

  private function prehled(){
    $this->pohled = "mail";
    $this->data["vsechnyEmaily"] = DB::vsechnyEmaily();

    if(isset($this->emailToShow) && is_numeric(($this->emailToShow))){
      $this->data["showEmail"] = DB::getEmail($this->emailToShow);
      file_put_contents("model/emailViewerBuffer.html",$this->data["showEmail"]["zprava"]);
    }
  }

  private function odeslat(){
    $this->pohled="mail-odeslat";

    //Nacist emaily z databaze
    $vsechnyKmeny = DB::vsechnyKmeny();
    
    $vsechnyKontaktniEmaily = array();
    $i = 0;
    foreach($vsechnyKmeny as $kmen){
      $vsechnyKontaktniEmaily[$i] = array();
      $vsechnyKontaktniEmaily[$i]["nazev"] = $kmen["nazev"];
      $vsechnyKontaktniEmaily[$i]["id_kmenu"] = $kmen["id_kmenu"];
      $vsechnyKontaktniEmaily[$i]["emaily"] = DB::getEmailyPoldeKmenu($kmen["id_kmenu"]);
      $i++;
    }

    $this->data["vsechnyEmaily"] = $vsechnyKontaktniEmaily;

    //die("<pre>".print_r($vsechnyKontaktniEmaily)."</pre>");

    if(isset($_POST["odeslat"])){
      // Poslat na adresu
      if(isset($_POST["to"]) && !empty($_POST["to"])){
        $to = $_POST["to"];
        $to = str_replace(" ","",$to);
        if(!empty(explode(",",$to)))
          $sendToEmails = explode(",",$to);
        else 
          $sendToEmails = array();
      }

      //Checkboxy s kmeny
      $kmeny = $_POST["kmeny"];
      if(!empty($kmeny) && !isset($_POST["checkbox-vsichni"])){
        for($i = 0; $i < count($kmeny); $i++){
          $emaily = DB::getEmailyPoldeKmenu($kmeny[$i]);
          foreach($emaily as $email){
            $sendToEmails[] = $email["kontaktni_email"];
          }
        }
      }

      //Checkbox vedouci
      if(isset($_POST["checkbox-vedouci"]) && !isset($_POST["checkbox-vsichni"])){
        $emailyVedouci = DB::getPouzeEmailyVedoucich();
        foreach($emailyVedouci as $email){
          $sendToEmails[] = $email["email"];
        }
      }

      //Chceckbox vsichni
      if(isset($_POST["checkbox-vsichni"])){
        $emailyDeti = DB::getPouzeEmaily();
        $emailyVedouci = DB::getPouzeEmailyVedoucich();
        foreach($emailyDeti as $email){
          $sendToEmails[] = $email["kontaktni_email"];
        }
        foreach($emailyVedouci as $email){
          $sendToEmails[] = $email["email"];
        }
      }
      
      //Kontrola vyplneni prijemcu
      if(empty($sendToEmails)){
        $this->pridejZpravu("Vyplňte příjemce","danger");
        $this->presmeruj("mail/odeslat");
      }
      
      //die("<pre>".print_r($sendToEmails)."</pre>");

      //Predmet
      if(isset($_POST["sub"])){
        $subject = $_POST["sub"];
      }
      else{
        $this->pridejZpravu("Vyplňte předmět !","danger");
        $this->presmeruj("mail/odeslat");
      }
      
      //Tělo emailu
      if(isset($_POST["text"])){
        $body = $_POST["text"];
      }
      else{
        $this->pridejZpravu("Vyplňte tělo emailu !","danger");
        $this->presmeruj("mail/odeslat");
      }

      //Odeslat
      //MailManager::sendMail($sendToEmails,$subject,MailManager::intoFrame($body),$body);
      //MailManager::sendMail(array("elias.matysek@gmail.com"),$subject,MailManager::intoFrame($body),$body);
      DB::pridatEmail(implode(",",$sendToEmails),$subject,null,MailManager::intoFrame($body),$_SESSION["uzivatel"]->id_vedouciho);
      $this->pridejZpravu("Zpráva byla odeslána","success");
      $this->presmeruj("mail");
    }
    
  }
}
 ?>
