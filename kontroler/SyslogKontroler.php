<?php

/*
    Třída zobrazuje syslog
*/

class SyslogKontroler extends Kontroler{

    public function zpracuj($parametry){

        // Ověření přihlášení uživatele
        if ($this->sessionCheck() === false) {
            $this->presmeruj("login");
        }

        //Pouze pro adminy
        if ($_SESSION["uzivatel"]->uroven < ADMIN) {
            $this->pridejZpravu("Pro zobrazeni systémového logu nemáš dostatečné oprávnění", "danger");
            $this->presmeruj("nastenka");
        }

        $this->pohled = "syslog";
        //vrátí syslog do pohledu
        $this->data["syslog"] = DB::getSyslog();
    }
}
