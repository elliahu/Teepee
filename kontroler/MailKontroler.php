<?php
###########################
# Třída MailKontroler #
###########################
/*
  Třída má na starost odesílání emailů a správu odeslaných emailů
*/
class MailKontroler extends Kontroler{

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
        $this->prehled();
        break;
    }
  }

  private function prehled(){
    $this->pohled = "under_construction";
    $this->data["vsechnyEmaily"] = DB::vsechnyEmaily();
  }

  private function odeslat(){
    $this->pohled="under_construction";
    $emaily = DB::getPouzeEmaily();
    foreach($emaily as $e){
      $emailyPouze[] = $e["kontaktni_email"];
    }
    $emailyPouze = implode(",",$emailyPouze);
    $this->data["emailyAll"] = $emailyPouze;
    if(isset($_POST["odeslat"])){
      if(!empty($_POST["to"]) && !empty($_POST["sub"]) && !empty($_POST["text"])){
        $prijemce = $_POST["to"];
        $prijmece = str_replace(" ","",$prijemce);
        $prijemcePole = explode(",",$prijemce);
        $predmet = $_POST["sub"];
        $text = $_POST["text"];

        $mail = new PHPMailer();
        $mail->CharSet = 'UTF-8';
        $mail->Encoding = 'base64';

        $mail->setFrom("info@matejelias.cz",$_SESSION["uzivatel"]->jmeno." ".$_SESSION["uzivatel"]->prijmeni);
        $mail->addAddress(array_shift($prijemcePole));
        foreach($prijemcePole as $email)
        {
           $mail->addBCC($email);
        }
        //$mail->addBCC("ostrava@royalranger.cz");
        $mail->Subject = $predmet;
        $mail->isHTML(TRUE);
        $mail->Body = '
        <html style="box-sizing:border-box;padding:15px; width:100%;">
        <div style="background-color:#fff;box-shadow: 0px 0px 23px -5px rgba(0,0,0,0.75);max-width:900px;">
          <div style="width:100%;padding:15px;background: #159858;background: linear-gradient(30deg, #093637, #159957);">
            <h1 style="color:#fff">Royal Rangers Ostrava</h1>
          </div>
          <div style="width:100%; padding:15px;">
            <p>
              '.$text.'
            </p>
          </div>
          <div style="background-color:#f5f5f5;width:100%;padding:15px;>
            <p>
            Tato zpráva byla odeslána z evidenčního systému 42. p.h. Royal Rangers Ostrava
            </p>
          </div>
        </div>
        </html>
        ';
        $textDB = $mail->Body;
        $mail->AltBody = $text;
        $mail->addAttachment('/view/img/logo_dark.png', 'Royal_Rangers_logo.png');
        $mail->addReplyTo("elias.matej@seznam.cz",$_SESSION["uzivatel"]->jmeno." ".$_SESSION["uzivatel"]->prijmeni);
        if (!$mail->send())
        {
           /* PHPMailer error. */
           $this->pridejZpravu("Email se nepodařilo odeslat - PHPMailer error: ".$mail->ErrorInfo, "danger");
           $this->presmeruj("mail/odeslat");
        }
        else{
          DB::pridatEmail($prijemce,$predmet,null,$textDB,$_SESSION["uzivatel"]->id_vedouciho);
          $this->pridejZpravu("Email byl odeslán", "success");
          $this->presmeruj("mail/odeslat");
        }
      }
      else{
        $this->pridejZpravu("Nikdo nemá rád vyplňování, ale jinak to fungovat nebude !", "danger");
        $this->presmeruj("mail/odeslat");
      }
    }
  }
}
 ?>
