<?php

$arrMessagesRestrictions = array();
$arrMessagesRestrictions["uk_user_email"] = array("Insert" => "El email ya ha sido registrado previamente", "Delete" => "");
$arrMessagesRestrictions["fk_user_id_type_document"] = array("Insert" => "El tipo de documento no existe", "Delete" => "");


ksort($arrMessagesRestrictions);

?>