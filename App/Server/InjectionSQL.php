<?php

$arrNotAllowed = array(
    "&lt;",
    "&gt",
    "&quot",
    "&#x27",
    "&#x2F",
    "/*",
    "*/",
    "request",
    "select",
    "declare",
    "insert",
    "update",
    "delete ",
    "drop ",
    "exec(",
    "execute(",
    "cast(",
    "nchar",
    "varchar",
    "nvarchar", 
    "substring",
    "sysobject",
    "syscolumns"
);

foreach ($_GET as $key => $value) {
    $_GET[$key] =  str_ireplace($arrNotAllowed, "", $value);
}//foreach ($_GET as $key => $value) {...}

foreach ($_POST as $key => $value) {
    $_POST[$key] = str_ireplace($arrNotAllowed, "", $value);
}//foreach ($_POST as $key => $value) {...}

?>
