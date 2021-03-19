<?php
// API kontroler
// 
class API{
    public $endpoint = "";
    public $token = "";
    public $request = ""; // Obsah reqestu -> JSON
    public $urlParams = array();
    public $response;

    //url:   domena.cz/api/endpoint{/parametr1/parametr2/...}?token=tokennumber

    public function __construct($parameters = array())
    {
        header('Content-Type: application/json');
        // [0] => api
        array_shift($parameters); // odebere 'api' z url paramtrů
        $this->endpoint = array_shift($parameters); // odebere endpoint z url
        $this->urlParams = $parameters;
        $this->response = new APIresponse();
        try{
            $this->request = json_decode(file_get_contents("php://input"));
        }
        catch(Exception $e){
           $this->response->badReqest();
        }
        if(isset($_GET["token"])){
            // Token se predava pres url parametry
            $this->token = $_GET["token"];
            // Odebereme token z pole $_GET 
            unset($_GET["token"]);
        }
        else{
            // Nepovedlo se ověřit autorizaci
            // špatný nebo žádný token
            $this->response->unauthorizedReqest();
        }
    }

    //Funkce vrací odpověď na request
    public function respond(){
        if($this->isTokenValid($this->token) === false){
            // Nevalidni token vraci neautorizovany request
            $this->response->unauthorizedReqest();
        }
        if($this->isRequestValid($this->request)){ // Overeni validity samotneho reqestu
            try{
                switch($_SERVER["REQUEST_METHOD"]){
                    case "GET": //GET request pro cteni dat
                        if(isset($_GET["condition"])){
                            $this->request = json_decode(stripslashes($_GET["condition"]));
                            if($this->request == null){
                                // Cteme obsah cele tabulky (nefiltrujeme)
                                $this->response->data = DB::APIget($this->endpoint,array());
                                $this->response->send();
                            }
                            else{
                                // Filtrujeme obsah podle $_GET["condition"]
                                $this->response->data = DB::APIget($this->endpoint,$this->request);
                                $this->response->send();
                            }
                        }
                        else{
                            $this->response->data = DB::APIget($this->endpoint,array());
                            $this->response->send();
                        }
                        break;

                    case "POST": // POST request pro cteni dat a aktualizaci existujicich dat
                        if(gettype($this->request[0]) == "array"){
                            // Cteme data
                            $this->response->data = DB::APIget($this->endpoint,$this->request);
                            $this->response->send();
                        }
                        else{
                            // aktualizaci existujicich dat
                            $this->response->data = DB::APIset($this->endpoint,$this->request);
                            $this->response->send();
                        }
                        break;

                    case "PUT": // PUT request
                        // Vkládání nových dat
                        $this->response->data = DB::APIput($this->endpoint,$this->request);
                        $this->response->send();
                        break;

                    case "DELETE": // Delete request
                        // Mazani existujicich dat
                        $this->response->data = DB::APIdelete($this->endpoint,$this->request);
                        $this->response->send();
                        break;

                    case "OPTIONS":
                        $this->response->responseCode = 200;
                        $this->response->responseMsg = "Podporované REQUEST METODY: GET pro získání dat ze serveru 
                        (možnost nastavit podmínku přidáním parametru 'condition' do url a vložením JSON objetku jako hodnotu tohot parametru)
                        POST pro aktualizování dat na serveru (do těla requestu vložte JSON ve formátu {[{},{}]} ) nebo pro získání dat ze serveru PUT pro vložení dat na server  (do těla requestu vložte JSON ve formátu {[{},{}]} )
                        DELETE pro odstranení dat.";
                        $this->response->send();
                        break;
                    default:
                        $this->response->badReqest();
                        break;
                }
            }
            catch(Exception $e){
                // Pri zpracovani pozadavku nastal error nebo vyjimka -> vratime bad request
                $this->response->responseCode = 400;
                $this->response->responseMsg = $e->getMessage();
                $this->response->send();
            }
        }
        else{
            // Nevalidni request vraci bad request response
            $this->response->badReqest();
        }
        
    }

    //Ověří validitu tokenu
    private function isTokenValid($token){
        // Overi existenci tokenu v tabulce autorizovanych aplikaci
        $app = DB::getAppByToken($token);
        if(isset($app["nazev_aplikace"])){
            return true;
        }
        else{
            return false;
        }
    }

    private function isRequestValid($request){
        if(($request == null) || ($_SERVER["REQUEST_METHOD"] == "OPTIONS")){
            // Prazdny request, nebo OPTIONS request je validni
            return true;
        }
        if(gettype($request) == "array"){ // Reuqest musi byt json array
            if($_SERVER["REQUEST_METHOD"] == "GET" || $_SERVER["REQUEST_METHOD"] == "DELETE" || ($_SERVER["REQUEST_METHOD"] == "POST" && gettype($request[0]) == "array"))
            {
                if(gettype($request[0]) == "array"){
                    //Request je json array of arrays
                    return true;
                }
                else {
                    //Request neni json array of arrays -> neni validni
                    return false;
                }
                    
            }
            if($_SERVER["REQUEST_METHOD"] == "POST" || $_SERVER["REQUEST_METHOD"] == "PUT"){
                if(gettype($request[0]) == "object"){
                    return true; // request je json array of objects
                }
                else{
                    return false;// request neni json array of objects -> neni validni
                }
            }
        }
        else{
            // Request neni json array -> neni validni
            return false;
        }
           
    }
}

?>