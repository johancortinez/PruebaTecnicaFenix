<?php
require_once(PathServer."/SessionControl.php");
require_once(PathServer."/PublicFunctions.php");

class UserClass {

    private $objMysql;

    public function __construct($objMysql, $method="") {

        $this->objMysql = $objMysql;
        $response = null;
        
        switch ($method) {
            case "Login":
                $email = isset($_POST["email"]) ? $_POST["email"] : "";
                $password = isset($_POST["password"]) ? $_POST["password"] : "";

                $response = $this->Login($email, $password);
                break;

            case "ListTypesDocument":
                $response = $this->ListTypesDocument();
                break;

            case "Search":
                $value = isset($_POST["value"]) ? $_POST["value"] : "";

                $response = $this->Search($value);
                break;
            
            case "Save":
                $idUser = isset($_POST["id_user"]) ? $_POST["id_user"] : 0;
                $password = isset($_POST["password"]) ? $_POST["password"] : "";
                $email = isset($_POST["email"]) ? $_POST["email"] : "";
                $firstName = isset($_POST["first_name"]) ? $_POST["first_name"] : "";
                $lastName = isset($_POST["last_name"]) ? $_POST["last_name"] : "";
                $typeDocument = isset($_POST["type_document"]) ? $_POST["type_document"] : 0;
                $document = isset($_POST["document"]) ? $_POST["document"] : "";
                $phone = isset($_POST["phone"]) ? $_POST["phone"] : "";
                $address = isset($_POST["address"]) ? $_POST["address"] : "";
                $birthdate = isset($_POST["birthdate"]) ? $_POST["birthdate"] : "";
                $isAdmin = isset($_POST["is_admin"]) ? $_POST["is_admin"] : 0;

                $response = $this->Save($idUser, $email, $password, $firstName, $lastName, 
                    $typeDocument, $document, $phone, $address, $birthdate, $isAdmin
                );
                break;

            case "Delete":
                $idUser = isset($_POST["id_user"]) ? $_POST["id_user"] : 0;

                $response = $this->Delete($idUser);
                break;

        }//Fin switch ($method){...}

        if ($response != null) {
            die($response);
        }//Fin if ($response != null){...}
        
    }//Fin __construct(){...}

    private function Login($email, $password) {

        $arrResponse = array();

        $SQL_VerifyUser = "
            SELECT 
                id_user,
                email,
                first_name,
                last_name,
                id_type_document,
                document,
                phone,
                address,
                birthdate,
                is_admin
            FROM user
            WHERE email = '$email'
                AND password = '".EncryptString($password)."'";

        $RS_User = $this->objMysql->ExecuteSQL($SQL_VerifyUser);

        if (mysqli_num_rows($RS_User) > 0) {
            $rowUser = mysqli_fetch_array($RS_User);
            do {
                $token = RandomString(15);
                $SQL_VerifyToken = "
                    SELECT 
                        token
                    FROM z_log_session
                    WHERE z_log_session.token = '$token'";

                $RS_Token = $this->objMysql->ExecuteSQL($SQL_VerifyToken);
                $tokenOk = mysqli_num_rows($RS_Token) == 0;
                
            } while (!$tokenOk);

            $currentDate = new DateTime();
            $expirationDate = (new DateTime())->modify("+".SessionExpirationDays." day");

            $SQL_InsertSession = "
                INSERT INTO z_log_session (
                    id_user,
                    token,
                    ip,
                    start_date,
                    expiration_date,
                    active
                ) VALUES (
                    '".$rowUser["id_user"]."',
                    '$token',
                    '".GetClientIP()."',
                    '".$currentDate->format("Y-m-d H:i:s")."',
                    '".$expirationDate->format("Y-m-d H:i:s")."',
                    1
                )";

            $this->objMysql->ExecuteSQL($SQL_InsertSession);

            $arrResponse["session"] = array(
                "id_session"    => (int)$this->objMysql->InsertId(),
                "token"         => (string)$token,
                "expirationDate"=> (string)$expirationDate->format("Y-m-d H:i:s")
            );

            $arrResponse["user"] = array(
                "id_user"       => (int)$rowUser["id_user"],
                "email"         => (string)$rowUser["email"],
                "first_name"    => (string)$rowUser["first_name"],
                "last_name"     => (string)$rowUser["last_name"],
                "type_document" => (int)$rowUser["id_type_document"],
                "document"      => (string)$rowUser["document"],
                "phone"         => (string)$rowUser["phone"],
                "address"       => (string)$rowUser["address"],
                "birthdate"     => (string)$rowUser["birthdate"],
                "is_admin"      => (bool)$rowUser["is_admin"],
            );

            //Establecemos el token de la sesión
            $_SESSION["token"] = $token;
            
        } else {
            $arrResponse["messages"][] = GetMessageResponse("IncorrectUser");
        }//Fin if (mysqli_num_rows($RS_User) > 0){...} else {...}

        return SendClientResponse($arrResponse);

    }//Fin Login(){...}

    private function ListTypesDocument() {

        $arrResponse = array();

        $SQL_typesDocuments = "
            SELECT
                id_type_document,
                name
            FROM type_document
            ORDER BY name
        ";

        $RS_TypesDocuments = $this->objMysql->ExecuteSQL($SQL_typesDocuments);

        if (mysqli_num_rows($RS_TypesDocuments) > 0) {
            while ($rowTypeDocument = mysqli_fetch_array($RS_TypesDocuments)) {

                $arrResponse["types_document"][] = array(
                    "id_type_document" => (int)$rowTypeDocument["id_type_document"],
                    "name" => (string)$rowTypeDocument["name"],
                );

            }//Fin while ($rowTypeDocument = mysqli_fetch_array()) {...}

        } else {
            $arrResponse["messages"][] = GetMessageResponse("NotFoundTypesDocument");

        }//Fin if (mysqli_num_rows($RS_TypesDocuments) > 0) {...} else {...}

        return SendClientResponse($arrResponse);

    }

    private function Search($value) {

        $arrResponse = array();

        $SQL_SearchUser = "
            SELECT
                id_user,
                email,
                first_name,
                last_name,
                id_type_document,
                document,
                phone,
                address,
                birthdate,
                is_admin
            FROM user
            WHERE email LIKE '%$value%'
                OR first_name LIKE '%$value%'
                OR last_name LIKE '%$value%'
                OR document LIKE '%$value%'
                OR phone LIKE '%$value%'
            ORDER BY first_name, last_name, email
        ";

        $RS_Users = $this->objMysql->ExecuteSQL($SQL_SearchUser);
        if (mysqli_num_rows($RS_Users) > 0) {

            while ($rowUser = mysqli_fetch_array($RS_Users)) {
                $arrResponse["users"][] = array(
                    "id_user"       => (int)$rowUser["id_user"],
                    "email"         => (string)$rowUser["email"],
                    "first_name"    => (string)$rowUser["first_name"],
                    "last_name"     => (string)$rowUser["last_name"],
                    "type_document" => (int)$rowUser["id_type_document"],
                    "document"      => (string)$rowUser["document"],
                    "phone"         => (string)$rowUser["phone"],
                    "address"       => (string)$rowUser["address"],
                    "birthdate"     => (string)$rowUser["birthdate"],
                    "is_admin"      => (bool)$rowUser["is_admin"],
                );

            }//Fin while ($rowUser = mysqli_fetch_array($RS_Users)) {...}

        } else {
            $arrResponse["messages"][] = GetMessageResponse("NotFoundUser");

        }//Fin if (mysqli_num_rows($RS_Users) > 0) {...}

        return SendClientResponse($arrResponse);

    }//Fin Search() {...}

    private function Save($idUser, $email, $password, $firstName, $lastName, 
        $typeDocument, $document, $phone, $address, $birthdate, $isAdmin
    ) {
        $arrResponse = array();

        //Verificamos si el email ingresado ya se encuentra registrado
        $SQL_VerifyEmail = "
            SELECT
                id_user
            FROM user
            WHERE email = '$email'
                AND id_user != '$idUser'
        ";

        $RS_Email = $this->objMysql->ExecuteSQL($SQL_VerifyEmail);
        if (mysqli_num_rows($RS_Email) == 0) {

            if ($idUser == 0) {
                //Nuevo usuario
                $SQL_InsertUser = "
                    INSERT INTO user (
                        email,
                        password,
                        first_name,
                        last_name,
                        id_type_document,
                        document,
                        phone,
                        address,
                        birthdate,
                        is_admin
                    ) VALUES (
                        '$email',
                        '".EncryptString($password)."',
                        '$firstName',
                        '$lastName',
                        '$typeDocument',
                        '$document',
                        '$phone',
                        '$address',
                        '$birthdate',
                        '$isAdmin'
                    )
                ";
                
                $this->objMysql->ExecuteSQL($SQL_InsertUser);
                $idUser = $this->objMysql->InsertId();

            } else {
                //usuario existente
                $SQL_UpdateUser = "
                    UPDATE user SET
                        email = '$email',
                        ".($password != "" ? "password = '".EncryptString($password)."'," : "")."
                        first_name = '$firstName',
                        last_name = '$lastName',
                        id_type_document = '$typeDocument',
                        document = '$document',
                        phone = '$phone',
                        address = '$address',
                        birthdate = '$birthdate',
                        is_admin = '$isAdmin'
                    WHERE id_user = '$idUser'
                ";

                $this->objMysql->ExecuteSQL($SQL_UpdateUser);

            }//Fin if ($idUser == 0) {...} else {...}

            $arrResponse["user"] = array(
                "id_user" => $idUser,
            );

        } else {
            $arrResponse["messages"][] = GetMessageResponse("EmailUsedUser");

        }//Fin if (mysqli_num_rows($RS_Email) == 0) {...} else {...}

        return SendClientResponse($arrResponse);

    }//Fin Save() {...}

    private function Delete($idUser) {

        $arrResponse = array();

        $SQL_DeleteUser = "DELETE FROM user WHERE id_user = '$idUser'";
        $this->objMysql->ExecuteSQL($SQL_DeleteUser);

        $arrResponse["user"] = array(
            "id_user" => $idUser,
        );

        return SendClientResponse($arrResponse);

    }//Fin Delete() {...}



}//Fin UserClass

?>