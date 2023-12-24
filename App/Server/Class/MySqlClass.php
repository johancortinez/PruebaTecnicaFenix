<?php
require_once(PathServer."/PublicFunctions.php");
require_once(PathServer."/RestrictionsMessages.php");

class MySqlClass {
    private $objMysql;
    private $TransactionInProcess = false;

    function __construct($servidor, $usuario, $clave, $baseDatos) {
        $this->objMysql = new mysqli($servidor, $usuario, $clave, $baseDatos);
        if ($this->objMysql->connect_errno) {
            $arrResponse["messages"][] = GetMessageResponse("MistakeConexionBD")." ".$this->objMysql->connect_error;
            die(SendClientResponse($arrResponse));
        }
        
        $this->objMysql->set_charset("utf8");
        
    }
    
    public function StartTransaction() {
        if (!$this->TransactionInProcess) {
            $this->objMysql->begin_transaction();
            $this->TransactionInProcess = true;
        }
        
    }//Fin StartTransaction(){...}
    
    public function CommitTransaction() {

        if ($this->TransactionInProcess) {
            if(!$this->objMysql->commit()) {
                $arrResponse["messages"][] = GetMessageResponse("MistakeAceptTransaction")." ".$this->objMysql->error;
                die(SendClientResponse($arrResponse));
            }
            $this->TransactionInProcess = false;
        }
        
    }//Fin CommitTransaction(){...}
    
    public function RollbackTransaction() {
        
        if ($this->TransactionInProcess) {
            if(!$this->objMysql->rollback()) {
                $arrResponse["messages"][] = GetMessageResponse("MistakeAceptTransaction")." ".$this->objMysql->error;
                die(SendClientResponse($arrResponse));
            }
            $this->TransactionInProcess = false;
        }
        
    }//Fin RollbackTransaction() {...}

    public function ExecuteSQL($querySQL) {
        $result = $this->objMysql->query($querySQL);
        
        if ($result === false) {
            global $arrMessagesRestrictions;
            $error = $this->objMysql->error;
            $this->RollbackTransaction();

            //Buscamos si el error que nos retorna Mysql esta contemplado en el listado de restricciones
            foreach ($arrMessagesRestrictions as $key => $value) {

                //Si encontramos la restricción devolvemos su mensaje
                if (strpos($error, $key) !== false) {                    
                    $error = (stripos(trim($querySQL), "DELETE") === 0) ? $value["Delete"] : $value["Insert"];
                    break;
                }
                
            }//Fin foreach ($arrMessagesRestrictions as $key => $value) {...}
            
            $arrResponse["messages"][] = GetMessageResponse("MistakeQuery"." ".$error);
            die(SendClientResponse($arrError));

        }//Fin if ($result === false){...}
        
        return $result;
        
    }//Fin ExecuteSQL(){...}
    
    public function InsertId() {
        return $this->objMysql->insert_id;

    }//Fin InsertId(){...}
    
    public function AffectedRows() {
        return $this->objMysql->affected_rows;

    }//Fin AffectedRows(){...}
    
    public function Close() {
        $this->objMysql->close();

    }//Fin Close(){...}

}//Fin class MySqlClass {...}

?>