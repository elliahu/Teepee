<?php
class APIresponse {
    public $responseCode = 200;
    public $responseMsg = "OK";
    public $data = array();

    public function __construct($responseCode = null,$responseMsg = null,$data = array())
    {
        $this->responseCode = ($responseCode == null)? $this->responseCode : $responseCode;
        $this->responseMsg = ($responseMsg == null)? $this->responseMsg : $responseMsg;
        $this->data = $data;
    }

    //Pošle odpověď
    public function send(){
        echo json_encode($this);
        exit();
    }

    //Přidá data do pole dat
    public function addData($item){
        $this->data[] = $item;
    }

    //Pošle odpověď bad request
    public function badReqest(){
        $this->responseCode = BADREQUEST;
        $this->responseMsg = "Bad request";
        $this->send();
    }

    //Pošle odpověď unauthorized request
    public function unauthorizedReqest(){
        $this->responseCode = UNAUTHORIZEDREQUEST;
        $this->responseMsg = "Unauthorized request";
        $this->send();
    }

}
?>