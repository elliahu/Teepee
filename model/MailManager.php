<?php
#
#   Třída spravuje odesílání emailů
#
class MailManager{
    
    // Pošle email protokolem SMTP, možnost poslat kopie a prilohy
    public function posliMail($komu = array(), $predmet, $telo, $teloAlt, $prilohy = array(), $CC = null){
        //SMTP nastavení -> zmeny v init.php
        $mail = new PHPMailer(true);
        //$mail->SMTPDebug = SMTPDebug;                     
        $mail->isSMTP();                                            
        $mail->Host       = HOST;                    
        $mail->SMTPAuth   = SMTPAuth;                                   
        $mail->Username   = NOREPLY;                     
        $mail->Password   = NOREPLYPSSWD;                             
        $mail->SMTPSecure = SMTPSecure;         
        $mail->Port       = PORT;                                    
        
        //From
        $mail->setFrom(NOREPLY, 'Teepee');
        $mail->CharSet = 'UTF-8';

        //Email
        if(!empty($komu)){
            //Opakuje pro každého příjemce
            foreach($komu as $adresa){
                //Komu odeslat
                $mail->addAddress($adresa);
                $mail->addReplyTo(NOREPLY, 'Teepee');

                //Kopie
                if($CC != null)
                    $mail->addCC($CC);

                //Přílohy    
                if(!empty($komu)){
                    foreach($prilohy as $priloha){
                        $mail->addAttachment($priloha);
                    }
                }

                $mail->isHTML(true); // Jedná se o HTML email
                $mail->Subject = $predmet; // Předmět
                $mail->Body    = $telo; // Tělo (text) emailu
                $mail->AltBody = $teloAlt; // Tělo (text) emailu pro email klienty, které neumí zobrazit
                                           // HTML emaily

                // odeslat
                $mail->send();
            }
        }
    }

    //Metoda se používá pro obalení těla emailu do šablony
    public function intoFrame($obsah){
        //šablona uložená v view/mail_frame/
        return file_get_contents("view/mail_frame/app-top.html").$obsah.file_get_contents("view/mail_frame/app-bottom.html");
    }
}

?>