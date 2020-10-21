<?php
/*
 * Třída pracuje s texty a řetězci
 *
*/

class TextManager{

    // name-of-something -> nameOfSomething
    // name of something -> nameOfSomething
    public function camelCase($text){
        $text = str_replace("-", " ", $text);
        $text = ucwords($text);
        $data = explode(" ",$text);
        $data[0] = strtolower($data[0]);
        return implode($data);
    }

    // nameOfSomething -> Name of something
    public function reverseCamelCase($text){
        $data = preg_split('/(?=[A-Z])/', $text);
        $string = implode(' ', $data);
        $string = strtolower($string);
        $data = explode(" ",$string);
        $data[0] = ucfirst($data[0]);
        return implode(" ",$data);
    }
}
?>