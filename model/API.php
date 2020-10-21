<?php
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
        array_shift($parameters); // odebere api z url paramtrů
        $this->endpoint = array_shift($parameters);
        $this->urlParams = $parameters;
        $this->response = new APIresponse();
        try{
            $this->request = json_decode(file_get_contents("php://input"));
        }
        catch(Exception $e){
           $this->response->badReqest();
        }
        if(isset($_GET["token"])){
            $this->token = $_GET["token"];
            unset($_GET["token"]);
        }
        else{
            $this->response->unauthorizedReqest();
        }
    }

    ##########################################################
    //Funkce vrací odpověď na request
    public function respond(){
        if($this->isTokenValid($this->token) === false){
            $this->response->unauthorizedReqest();
        }
        if($this->isRequestValid($this->request)){
            try{
                switch($_SERVER["REQUEST_METHOD"]){
                    case "GET":
                        if(isset($_GET["condition"])){
                            $this->request = json_decode(stripslashes($_GET["condition"]));
                            if($this->request == null){
                                $this->response->data = DB::APIget($this->endpoint,array());
                                $this->response->send();
                            }
                            else{
                                $this->response->data = DB::APIget($this->endpoint,$this->request);
                                $this->response->send();
                            }
                        }
                        else{
                            $this->response->data = DB::APIget($this->endpoint,array());
                            $this->response->send();
                        }
                        break;

                    case "POST":
                        if(gettype($this->request[0]) == "array"){
                            $this->response->data = DB::APIget($this->endpoint,$this->request);
                            $this->response->send();
                        }
                        else{
                            $this->response->data = DB::APIset($this->endpoint,$this->request);
                            $this->response->send();
                        }
                        break;

                    case "PUT":
                        $this->response->data = DB::APIput($this->endpoint,$this->request);
                        $this->response->send();
                        break;

                    case "DELETE":
                        $this->response->data = DB::APIdelete($this->endpoint,$this->request);
                        $this->response->send();
                        break;

                    case "OPTIONS":
                        $this->response->responseCode = 200;
                        $this->response->responseMsg = "Podporované REQUEST METODY: GET pro získání dat ze servru 
                        (možnost nastavit odmínku přidáním parametru 'condition' do url a vložením JSON objetku jako hodnotu tohot parametru)
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
                $this->response->badReqest();
            }
        }
        else{
            $this->response->badReqest();
        }
        
    }
    ###########################################################

    //Ověří validitu tokenu
    private function isTokenValid($token){
        $app = DB::getAppByToken($token);
        if(isset($app["nazev"])){
            return true;
        }
        else{
            return false;
        }
    }

    private function isRequestValid($request){
        if(($request == null) || ($_SERVER["REQUEST_METHOD"] == "OPTIONS"))
            return true;
        if(gettype($request) == "array"){
            if($_SERVER["REQUEST_METHOD"] == "GET" || $_SERVER["REQUEST_METHOD"] == "DELETE" || $_SERVER["REQUEST_METHOD"] == "POST")
            {
                if(gettype($request[0]) == "array")
                    return true;
                else 
                    return false;
            }
            if($_SERVER["REQUEST_METHOD"] == "POST" || $_SERVER["REQUEST_METHOD"] == "PUT"){
                if(gettype($request[0]) == "object"){
                    return true;
                }
                else
                    return false;
            }
        }
        else
            return false;
    }
}

?>