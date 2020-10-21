<?php
  class LoginKontroler extends Kontroler{
    //Atribut, jehož hodnota se nastaví v případě erroru
    protected $errorMsg = "";

    public function zpracuj($parametry){

      if($parametry[0] != "lost-password"){
         //Získáme hodnoty z přihlašovacího formuláře
        if($udaje = $this->ziskejData()){
          //Otestujeme získané hosnoty
          if($this->checkLogin($udaje["jmeno"],$udaje["heslo"])){
            $uzivatel = DB::getUzivatel($udaje["jmeno"]);
            //Vytvoření session
            $_SESSION["login"] = true;
            $_SESSION["uzivatel"] = $uzivatel;
            $_SESSION["uzivatel"]->kmen = DB::getKmen($_SESSION["uzivatel"]->id_kmenu);
            //Upraví last login na aktuální DATETIME
            DB::upravLastLogin($_SESSION["uzivatel"]->id_vedouciho);
            $_SESSION["historie_zprav"] = array();
            //Ověří, zda má uživatel vytvořený konfig, jinak vytvoří nový
            $this->overeniConfigu();
            $_SESSION["user_config"] = $this->getUserConfig();
            
            //Přesměrování
            //$this->pridejZpravu("Uživatel přihlášen jako <b> $uzivatel->jmeno </b>","success");
            $this->presmeruj("nastenka");
            }
            else{
              $this->pridejZpravu("Špatné uživatelské jméno nebo heslo !","danger");
              $this->presmeruj("login");
            }
          }
          $this->pohled = "login";
      }
      else{
        //Reset hesla
        if(isset($_GET["reset_token"])){
          if($this->overToken($_GET["reset_token"])){ // Ověří se platnost tokenu
            $this->pohled = "reset-password"; //Připojí se pohled
            $this->zmenaHesla($_GET["reset_token"]);  //Provede se změna hesla
          }
          else{
            $this->pridejZpravu("Neplatný nebo prošlý token","danger");
            $this->presmeruj("login/lost-password");
          }
        }
        else{
          // Zapomenuté heslo
          $this->pohled = "lost-password";
          if(isset($_POST["uz-jmeno"]) && isset($_POST["email"])){
            if($this->overEmail($_POST["uz-jmeno"],$_POST["email"])){
              $this->zaslatResetEmail($_POST["email"],$_POST["uz-jmeno"]);
              $this->pridejZpravu("<b>Úspešně jste zažádali o resetování hesla</b><br>Na zadanou adresu Vám byl zaslán email s dalšími instrukcemi. Pokud email neuvidíte do 5 minut, zkontrolujte spam a hromadnou poštu, pokud ani tam email nevidíte, kontaktujte správce","default");
              $this->presmeruj("login");
            }
            else{
              $this->pridejZpravu("Zadané údaje si neodpovídají","danger");
              $this->presmeruj("login/lost-password");
            }
          }
        }
      }
    }

    //Ověření, zda existuje takový uživatel
    private function checkLogin($jmeno,$heslo){
      return DB::overUzivatele($jmeno,$heslo);
    }

    //Metoda pro získání zadaných přihlašovacích údajů
    private function ziskejData(){
      if(isset($_POST["jmeno"]) && isset($_POST["heslo"])){
        if(!empty($_POST["jmeno"]) && !empty($_POST["heslo"])){
          return array("jmeno" => $_POST["jmeno"],"heslo" => $_POST["heslo"]);
        }
        else{
          $this->pridejZpravu("Nikdo nemá rád vyplňování, ale musíme to znát všechno ... Vyplňte všchna pole !","danger");
          $this->presmeruj("login");
        }
      }
      else{
        //Nebyly zadány hodnoty
        return false;
      }
    }

    //Metoda ověří, zda existuje uživateli config
    private function overeniConfigu(){
      if(!file_exists("cfg/user_cfg/user_cfg_".$_SESSION["uzivatel"]->id_vedouciho.".json")){
        file_put_contents("cfg/user_cfg/user_cfg_".$_SESSION["uzivatel"]->id_vedouciho.".json",file_get_contents("model/UserConfigClass.json"));
        $this->pridejZpravu("<b>Pozor !</b><br>Byl ti vytvořen uživatelský config. Teď si můžeš aplikaci nastavit podle sebe v uživatelském nastavení.<br><em>Ikona vpravo nahoře -> Uživatelské nastavení </em>","default");
        return false;
      }
      else
        return true;
    }

    //Metoda vrátí uživatelská config
    private function getUserConfig(){
      if($this->overeniConfigu()){
        $user_config = json_decode(file_get_contents("cfg/user_cfg/user_cfg_".$_SESSION["uzivatel"]->id_vedouciho.".json"));
        return $user_config;
      }
    }

    private function overEmail($login, $email){
      $navrat = DB::overEmailUzivatele($login,$email);
      if(isset($navrat["id_vedouciho"]) && ($navrat["id_vedouciho"] >= 1)){
        return true;
      }
      else{
        return false;
      }
    }

    private function zaslatResetEmail($email,$login){
      $mailManager = new MailManager();
      $resetManger = new PasswordResetManager();
      $token = $resetManger->addRequest($login,$email);
      global $config;
      $telo = "
        <h1>Resetování hesla</h1>
        Zažádali jste o resetování hesla pro účet <b>$login</b><br>
        <em>Pokud jste to nebyli vy, tak kontaktujte správce. Nemusíte se bát, heslo ještě nebylo resetováno.</em><br>
        <br>
        Pro resetování hesla klikněte <a href='".$config->aplikace->url."/login/lost-password/?reset_token=$token'>zde</a><br>

        Budete přesměrováni na stránku, kde si vytvoříte nové heslo.
      ";
      $mailManager->posliMail(array($email), "Resetování hesla", $mailManager->intoFrame($telo),strip_tags($telo));
    }

    private function zmenaHesla($token){
      $request = DB::getResetRequest($token);
      $this->data["uzivatlske_jmeno"] = $request["jmeno"];
      if(isset($_POST["reset_hesla"])){
        if(isset($_POST["nove_heslo"]) && isset($_POST["nove_heslo_znovu"])){
          if($_POST["nove_heslo"] == $_POST["nove_heslo_znovu"]){
            if (preg_match("#.*^(?=.{8,20})(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9]).*$#", $_POST["nove_heslo"])){
              DB::upravHeslo(array(password_hash($_POST["nove_heslo"],PASSWORD_DEFAULT),$request["id"]));
              $this->pridejZpravu("Úspěšně jste si změnili heslo. Nyní se přihlaste novým heslem.","default");
              $mailManager = new MailManager();
              $telo = "
                <h1>Resetovali jste si heslo</h1>
                Tímto potvrzujeme resetování hesla v aplikaci Teepee pro uživatele <b>".$request["jmeno"]."</b><br>
                Nyní se již přihlašujte pod novým heslem.<br><br>
                <em>Pokud jste to nebyli vy, kontaktujte správce </em>
              ";
              $mailManager->posliMail(array($request["email"]),"Resetovali jste si heslo",$mailManager->intoFrame($telo),strip_tags($telo));
              $resetManger = new PasswordResetManager();
              $resetManger->completeRequest($token);
              $this->presmeruj("login");
            }
            else{
              $this->pridejZpravu("Heslo musí obsahovat malé a velké písmeno, musí obsahovat číslici a musí být dlouhé alespoň 8 znaků (maximálně 20)","default");
              $this->presmeruj("login/lost-password/?reset_token=".$token);
            }
          }
          else{
            $this->pridejZpravu("Hesla nejsou stejná !","danger");
            $this->presmeruj("login/lost-password/?reset_token=".$token);
          }
        }
        else{
          $this->pridejZpravu("Vyplňte všechna pole!","danger");
          $this->presmeruj("login/lost-password/?reset_token=".$token);
        }
      }
    }

    private function overToken($token){
      $resetManger = new PasswordResetManager();
      return $resetManger->verifyToken($token);
    }
  }
 ?>
