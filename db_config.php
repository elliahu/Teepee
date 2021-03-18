<?php 
######## Databáze ########
  // toto přepsat
  $servername = "localhost"; // adresa databázového serveru
  $username = "root"; //uživatel
  $heslo = ""; // heslo uživatele
  $nazev_db = "royalrangers"; // název vytvořené databáze
  // // // // //
  try{
    DB::pripoj($servername,$username,$heslo,$nazev_db);
  }
  catch(Exception $e){
    die("
      <b>Nepodařilo se připojit k databázi !</b><br>
      To se mohlo stát z následujících důvodů:<br>
      1. Databázový server '$servername' je nedostupný<br>
      2. Databázový server '$servername' neexistuje<br>
      3. Na databázovém serveru '$servername' neexistuje databáze '$nazev_db'<br>
      4. Zadali jste špatného uživatele nebo jeho heslo<br><br><br>
      <b>ERROR MSG:</b><br>
      {$e->getMessage()}
    ");
  }
?>