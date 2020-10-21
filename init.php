<?php

 /*   Soubor init slouží pro inicializaci protřebných údajů
  *     -> globální proměnné a funkce
  *     -> autoloader
  *     -> error handler
  *     -> shutdown handler
 */  

  // inicializace session
  session_start();
  ####### Autoloader ########
  function autoladClass($trida) {
    // Zjsití, zda jde o kontroler nebo model
    if (preg_match("/Kontroler/", $trida))
      //kontroler
      require("kontroler/$trida.php");
    elseif(preg_match("/API/", $trida))
      if($trida == "API")
        require("model/API.php");
      else
        require("model/api/$trida.php");
    else
      //model
      require("model/$trida.php");
  }
  //registrace autoloaderu
  spl_autoload_register("autoladClass");

  ########## Databáze ##############
  require_once("db_config.php");
  
  ########## ERROR HANDLER ##################
  function errorHandler($error_level,$error_message,$error_file,$error_line){
    //Ingorujeme NOTICE
    if($error_level != 8){
      //Vytvoří zprávu o erroru
      $error = array(
        "zprava" => "
        <h3><b>Ooops!</b> Něco se pokazilo</h3>
        Kód chyby:<br>
        <b>ERROR :</b> [$error_level], $error_message <b>IN FILE:</b> $error_file <b>ON LINE</b> $error_line<br>
        <br>
        Kontaktujte správce
        ",
        "typ" => "error",
        "cas" => date('H:i:s', time())
      );
      /* Vloží zprávu do pole s upozorněním
      -> Není možné použít Kontroler->pridejZpravu(), protože kontroler, ještě nebyl inicializován
      */
      $_SESSION["zpravy"][] = $error; 
      //Vložení záznamu do historie zprav
      array_push($_SESSION["historie_zprav"],$error);
      //Zaloguje informaci o erroru do syslogu
      DB::syslog($error["zprava"],$error["typ"],(isset($_SESSION["uzivatel"]->id_vedouciho)) ? $_SESSION["uzivatel"]->id_vedouciho : 0);
      //Redirect na nástěnku
      die("<script>window.location.href = '/nastenka';</script>");
    }
  }
  // Registrace error handleru
  set_error_handler("errorHandler");

  ############ Shut down function ###############
  //Shutdown handler 
  // V případě, že dojde k shutdown erroru spustí se shutDownFunction()
  function shutDownFunction() {
    $error = error_get_last();
     // Fatal error, E_ERROR === 1
    if ($error['type'] === E_ERROR) {
      $msg = "<br>{{{<br>
      <b>SHUTDOWN ERROR :</b> ".$error['type'].", ".$error['message']." <b>IN FILE:</b> ".$error['file']." <b>ON LINE</b> ".$error['line']."<br>}}}<br>
      ";
      trigger_error($msg,E_USER_ERROR);
    }
  }
  //registrace  shutdownfunkce
  register_shutdown_function('shutDownFunction');


  ####### Role vedoucích #######
  //Podle databáze
  //Možnost editovat (změnit, přidat, ubrat atd.)
  define("ADMIN", 9); // Nejvyšší oprávnění
  define("SUPERUSER", 8);
  define("EDITOR", 4);
  define("USER", 0);

  ####### Config ##############
  //Config nastavení aplikace (cfg/config.json)
  $config = json_decode(file_get_contents("cfg/config.json"));

  ####### Mail Nastavení #######
  //PHPMailer 
  //Dostupné z https://github.com/PHPMailer/PHPMailer
  // K odesílání používá SMTP -> lze změnit na jiný protokol 
    //->V tom případě nutnost upravit MailManager() třídu v model/MailManager.php
  define("SMTPDebug", SMTP::DEBUG_SERVER);
  define("HOST", 'smtp-220831.m31.wedos.net');
  define("SMTPAuth",true);
  define("NOREPLY","no-reply@matejelias.cz");
  define("INFO","info@matejelias.cz");
  define("NOREPLYPSSWD",'^v1U0/Hc7-ku\0UQ3');
  define("INFOPSSWD",'^v1U0/Hc7-ku\0UQ3');
  define("SMTPSecure", PHPMailer::ENCRYPTION_STARTTLS);
  define("PORT", 587);


  //HTPP response codes
  define("OKREQUEST",200);
  define("BADREQUEST",400);
  define("FORBIDDENREQUEST",403);
  define("UNAUTHORIZEDREQUEST",401);
  define("NOTFOUND",404);
 ?>
