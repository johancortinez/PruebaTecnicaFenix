<?php
require_once(PathServer."/PublicFunctions.php");

/* Este archivo se encarga de verificar si una petición se puede o no realizar,
 * verifica que las peticiones tengan los parametros adecuados
 */

$idSession = 0;
$RequestOk = false;
$arrResponseMenssage = array();

if (isset($objMysql)) {
    //Cerramos las sesiones por fecha de expiración
    $SQL_CloseSession = "
        UPDATE z_log_session SET 
            z_log_session.active = 0,
            z_log_session.end_date = '".date("Y-m-d H:i:s")."'
        WHERE z_log_session.expiration_date <= '".date("Y-m-d H:i:s")."'";
        
    $objMysql->ExecuteSQL($SQL_CloseSession);
    if (isset($_POST['method'])) {

        if (isset($_POST['token']) && trim($_POST['token']) != "") {
            //Verificamos si la sesión continua activa
            $SQL_Session = "
                SELECT
                    z_log_session.id_log_session,
                    z_log_session.active
                FROM z_log_session
                WHERE z_log_session.token = '".$_POST['token']."'";

            $RS_Session = $objMysql->ExecuteSQL($SQL_Session);
            if ($rowSession = mysqli_fetch_array($RS_Session)) {

                //Verificamos si la sesión se encuentra activa
                switch ((int)$rowSession['active']) {
                    case 1://Activa
                        $idSession = $rowSession['id_log_session'];
                        $RequestOk = true;
                        break;

                    default://Inactiva (Cerrada)
                        $arrResponseMenssage['messages'][] = GetMessageResponse("InactiveSession");
                        break;

                }//Fin switch ((int)$rowSession['active']) {...}

            }//Fin if ($rowSession = mysqli_fetch_array($RS_Session)) {...}

        }//Fin if (isset($_POST['token']) && trim($_POST['token']) != "") {...}
        
        //Login no se valida llave 
        if ($_POST['method'] == "Login") {
            $RequestOk = true;
        }//Fin if ($_POST['method'] == "Login" ) ...}
        
    }//Fin if (isset($_POST['method'])) {...}

}//Fin if (isset($objMysql)) {...}

if (!$RequestOk) {
    if (count($arrResponseMenssage) > 0) {
        die(SendClientResponse($arrResponseMenssage));  
    } else {
        die(http_response_code(404));
    }
}//Fin if (!$RequestOk) {...}

?>