<?php
require_once (PathServer.'/ResponseMessages.php');

function EncryptString($string) {
    return hash_hmac('sha1', $string, AppPassword);

}//Fin EncryptString(){...}

/*
 * En esta función se tienen en cuenta las posibles direcciones de los proxys por los que pasa la petición. 
 * Para localizar la ip real del usuario se comienza a mirar por el principio hasta encontrar una dirección ip 
 * que no sea del rango privado. En caso de no encontrarse ninguna se toma como valor el REMOTE_ADDR.
 */
function GetClientIP() {
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) ) {
        
        $client_ip = !empty($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] 
            : ((!empty($_ENV['REMOTE_ADDR'])) ? $_ENV['REMOTE_ADDR'] : "unknown");

        // los proxys van añadiendo al final de esta cabecera
        // las direcciones ip que van "ocultando". Para localizar la ip real
        // del usuario se comienza a mirar por el principio hasta encontrar
        // una dirección ip que no sea del rango privado. En caso de no
        // encontrarse ninguna se toma como valor el REMOTE_ADDR

        $entries = preg_split('/[, ]/', $_SERVER['HTTP_X_FORWARDED_FOR']);
        reset($entries);
        
        while (list(, $entry) = each($entries)) {
            $entry = trim($entry);
            if (preg_match("/^([0-9]+\.[0-9]+\.[0-9]+\.[0-9]+)/", $entry, $ip_list)) {
                $private_ip = array(
                    '/^0\./',
                    '/^127\.0\.0\.1/',
                    '/^192\.168\..*/',
                    '/^172\.((1[6-9])|(2[0-9])|(3[0-1]))\..*/',
                    '/^10\..*/'
                );

                $found_ip = preg_replace($private_ip, $client_ip, $ip_list[1]);

                if ($client_ip != $found_ip) {
                    $client_ip = $found_ip;
                    break;
                }
            }

        }//Fin while (list(, $entry) = each($entries)){...}
        
    } else {
        $client_ip = !empty($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] :
            ((!empty($_ENV['REMOTE_ADDR'])) ? $_ENV['REMOTE_ADDR'] : "unknown");
    }

    return $client_ip;
        
}// Fin GetClientIP()

function GetMessageResponse($keyMessage, $message="") {    
    global $arrMessagesResponse;
    
    if (isset($arrMessagesResponse[$keyMessage])) {
        return array('message' => $arrMessagesResponse[$keyMessage].$message );
    } else {
        return array();
    }
    
}//Fin GetMessageResponse(){...}

function RandomString($length) {
    $string = "";
    
    if ($length > 0) {
        $arrCharacters = array(
            "a", "b", "c", "d", "e", 
            "f", "g", "h", "i", "j", 
            "k", "l", "m", "n", "ñ", 
            "o", "p", "q", "r", "s", 
            "t", "u", "v", "w", "x", 
            "y", "z", "1", "2", "3",
            "4", "5", "6", "7", "8",
            "9", "0"
        );
        
        while (strlen($string) < $length) {
            $character = $arrCharacters[rand(0, (count($arrCharacters)-1))];
            $capitalLetter = rand(0, 1);

            if ($capitalLetter == 1) 
            {
                $character = strtoupper($character);
            }
            
            $string .= $character;
            
        }//Fin while (strlen($string) < $length) {...}
        
    }//Fin if ($length > 0) {...}
    
    return $string;
    
}//Fin RandomString(){...}

function SendClientResponse($arrResponse) {
    return json_encode($arrResponse);
    
}//Fin SendClientResponse(){...}

?>