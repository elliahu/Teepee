<?php

class PasswordResetManager{

    //Vrací náhodný řetězec ze znaků 0-9 a-z A-Z
    private function generateToken($length = 40) {
        do{
            $token = substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
        }
        while(DB::uniqueToken($token) === false);
        return $token;
    }

    //Přidá do databáze uáznam o žádosti o změnu hesla a vytvoří příslušný token
    public function addRequest($login,$email){
        $id_vedouciho =  DB::overEmailUzivatele($login,$email);
        $token = $this->generateToken();
        DB::addPasswordResetRequest($id_vedouciho["id_vedouciho"],$token);
        return $token;
    }

    //Ověří platnost tokenu
    public function verifyToken($token){
        return DB::verifyRequestToken($token);
    }

    //Nastaví příslušný request jako dokončený
    public function completeRequest($token){
        DB::completeRequest($token);
        return true;
    }
}

?>