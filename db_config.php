<?php 
######## Databáze ########
  // toto přepsat
  $servername = "localhost"; // adresa databázového serveru
  $username = "root"; //uživatel
  $heslo = ""; // heslo uživatele
  $nazev_db = "royalrangers"; // název vytvořené databáze
  // // // // //
  DB::pripoj($servername,
  $username,$heslo,$nazev_db);
?>