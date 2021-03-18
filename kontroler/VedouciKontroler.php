<?php
class VedouciKontroler extends Kontroler{

  public function zpracuj($parametry){

    if($this->sessionCheck() === false){
      $this->presmeruj("login");
    }

    if(isset($parametry[0])){
      $parametr = $parametry[0];
    }
    else{
      $parametr = null;
    }

    switch ($parametr) {
      case "novy":
        $this->novy();
        break;

      case "smazat":
        if(isset($parametry[1])){
          $this->smazatVedouciho($parametry[1]);
        }
        else{
          $this->prehled("");
        }
        break;

      case "sort":
        if(isset($parametry[1])){
          $this->prehled($parametry[1]);
        }
        else{
          $this->prehled("");
        }
        break;

      default:
        $this->prehled("");
        break;
    }
  }
  // Přehled všech vedoucích
  private function prehled($sortBy){
    if($sortBy == ""){
      $vedouci = DB::vsichniVedouci();
      $this->data["vedouci"]=$vedouci;
      $this->pohled = "vedouci-prehled";
    }
    else{
      $sortBy = $this->camelCase($sortBy);
      $vedouci = DB::vsichniVedouci($sortBy);
      $this->data["vedouci"]=$vedouci;
      $this->pohled = "vedouci-prehled";
    }
  }
  //Vytvoření nového vedoucího
  private function novy(){
    if($_SESSION["uzivatel"]->uroven < SUPERUSER){
      $this->pridejZpravu("Pro vytváření nových uživatelů nemáš dostatečná oprávnění !","danger");
      $this->presmeruj("vedouci");
    }
    else{
      if(!empty($_POST)){
      
        if(!empty($_POST["jmeno"]) && !empty($_POST["prijmeni"])
        && !empty($_POST["datum_narozeni"]) && !empty($_POST["email"]) && !empty($_POST["tel"])
        && !empty($_POST["uz_jmeno"]) && !empty($_POST["heslo"]) && !empty($_POST["conf_heslo"])
        && !empty($_POST["kmen"])){
          if(empty($_POST["prezdivka"])){
            $prezdivka = "";
          }
          else{
            $prezdivka = $_POST["prezdivka"];
          }
          if($_POST["heslo"] == $_POST["conf_heslo"]){
            $vedouciArr = array(
              $_POST["jmeno"],
              $prezdivka,
              $_POST["prijmeni"],
              $_POST["datum_narozeni"],
              $_POST["email"],
              $_POST["tel"],
              $_POST["uz_jmeno"],
              password_hash($_POST["heslo"],PASSWORD_DEFAULT),
              $_POST["kmen"]
            );
            if(DB::rowCountUzivatel($_POST["uz_jmeno"]) < 1){
              $rows = DB::novyVedouci($vedouciArr,$_POST["uz_jmeno"],$_POST["heslo"]);
              $this->pridejZpravu("Byl přidán nový vedoucí !","success");
              $this->presmeruj("vedouci");
            }
            else{
              $this->pridejZpravu("Již existuje uživatel se stejným uživatelským jménem","danger");
              $this->presmeruj("vedouci/novy");
            }

          }
          else{
            $this->pridejZpravu("Tady někdo neumí psát ... Hesla nejsou stejná !","danger");
            $this->presmeruj("vedouci/novy");
          }
        }
        else{
          $this->pridejZpravu("Nikdo nemá rád vyplňování, ale musíme to znát všechno ... Vyplň všchna pole !","danger");
          $this->presmeruj("vedouci/novy");
        }
      }
      $this->pohled = "vedouci-novy";
    }
  }

  public function camelCase($text) {
      $text = str_replace("-", " ", $text);
      $text = ucwords($text);
      return $text;
  }

  public function reverseCamelCase($text) {
      $text = str_replace(" ", "-", $text);
      $text = strtolower($text);
      return $text;
  }

  private function smazatVedouciho($id){
    if($id != 1){
      if($_SESSION["uzivatel"]->uroven >= ADMIN){
        DB::smazatVedouciho($id);
        unlink("/cfg/user_cfg/user_cfg_$id.json");
        $this->pridejZpravu("Vedoucí s id = $id byl úspěšně smazán !","success");
        $this->presmeruj("vedouci");
      }
      else{
        $this->pridejZpravu("Pro mazání uživatelů nemáš dostatečné opávnění ","danger");
        $this->presmeruj("vedouci");
      }
    }
    else{
      $this->pridejZpravu("Tohoto vedouciho nelze smazat !","danger");
      $this->presmeruj("vedouci");
    }
  }

}
 ?>
