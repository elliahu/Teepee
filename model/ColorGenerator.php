<?php
#
#  Třída je určená ke generování barev a porvádění operací s barvami
#   
#

class ColorGenerator{
    
    //Sub-metoda volaná ostatníma metodama
    private function randomColor_part(){
        //Vrací 0 - 255
        return str_pad( dechex( mt_rand( 0, 255 ) ), 2, '0', STR_PAD_LEFT);
    }
  
    //metoda vrátí náhodnou barvu v hexadecimalnim tvaru
    public function randomColorHex(){   
        // Poslkládá rgb kód barvy z čísel 0-255 0-255 0-255
        return $this->randomColor_part() . $this->randomColor_part() . $this->randomColor_part();
    }
  
    //vrátí náhodnou barvu v rgba tvaru
    public function randomColorRgba($opacity){
        //Možnost přidat průhlednost
        return "rgba(".mt_rand( 0, 255 ).",".mt_rand( 0, 255 ).",".mt_rand( 0, 255 ).",$opacity)";
    }
  
    //vrátí náhodnou barvu v rgb tvaru
    public function randomColorRgb(){
        return "rgb(".mt_rand( 0, 255 ).",".mt_rand( 0, 255 ).",".mt_rand( 0, 255 ).")";
    }
    
}
?>