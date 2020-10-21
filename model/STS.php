<?php
class STS {
  public $kmen;
  public $statistika;

  function __construct($kmen,$statistika){
    $this->kmen = $kmen;
    $this->statistika = $statistika;
  }

  public function vratGraf(){

  }

  public function ziskejData(){
    $data = DB::Statistika.$statistika($this->camelCase($this->kmen));
  }

  // camel case
    private function camelCase($text) {
        $text = str_replace("-", " ", $text);
        $text = ucwords($text);
        return $text;
    }
}
 ?>
