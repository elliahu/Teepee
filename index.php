<?php
# **********************************************************************
###########################
# Evidenční systém oddílu #
#    Royal Rangers        #
#  42. p.h. Ostrava       #
###########################
/*
@author 
Matěj Eliáš
2020
Šíření pouze se souhlasem majitele

*/

  
  try{ // Nastane li v aplikaci výjimka, zobrazí se uživateli formou zprávy

  // Init.php
  require_once("init.php");

  // Router
  $router = new RouterKontroler();
  $router->zpracuj(array($_SERVER["REQUEST_URI"]));
  $router->vypisPohled();
  }
  
  // Exception Handler
  catch(Exception $e){
    //Vytvoří zprávu o výjimce
    $exception = "
    <h3><b>Ooops!</b> Něco se pokazilo</h3>
    Kód výjimky:<br>".
    $e->getMessage();
    //Přidá zprávu do pole zpráv
    $router->pridejZpravu($exception,"error");
    //Zaloguje informaci o výjimce do syslogu
    DB::syslog($exception,"exception",(isset($_SESSION["uzivatel"]->id_vedouciho)) ? $_SESSION["uzivatel"]->id_vedouciho : 0);
    //Přesměruje na nástěnku
    die("<script>window.location.href = '/nastenka';</script>");
  }
  

 ?>
