<?php
date_default_timezone_set('America/Bogota');

//Requerimos el archivo de configuración
require_once("Settings.php");

//Inicializamos las variables de sesión
session_start();

//Obtenemos los parametros inputs (JSON) de la petición recibido 
$_POST = json_decode(file_get_contents("php://input"), true);

//Verificamos que se haya recibido una petición
if (isset($_POST['method'])) {
    
    //Verificamos que la petición recibida no contenga inyección SQL
    require_once(PathServer."/InjectionSQL.php");

    //Requerimos el archivo que incluye las funciones publicas utilizadas en todo el código
    require_once(PathServer."/PublicFunctions.php");
    
    //Requerimos la clase para el manejo de la base de datos MySQL
    require_once(PathClass."/MySqlClass.php");
    $objMysql = new MySqlClass(ServerDB, UserDB, PasswordDB, NameDB);
    
    //Requerimos el archivo que verifica que la petición se pueda realizar, es decir el usuario tenga una sesion activa
    require_once(PathServer."/SessionControl.php");

    require_once(PathClass."/UserClass.php");
    new UserClass($objMysql, $_POST['method']);

}//Fin if (isset($_POST['method'])) {...}

http_response_code(404);

?>