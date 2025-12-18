<?php
// conexion a la base de datos
define('LocalHost', 'db5018990367.hosting-data.io');
define("Port", 3306);
define('User', 'dbu286606');
define('Password', 'patadeperro21@glAmourTime');
define('DataBase', 'dbs14955801');


$conexion = new mysqli(LocalHost, User, Password, DataBase, Port);


if ($conexion->connect_error) {
    die("Error al conectar a la base de datos: " . $conexion->connect_error);
}


$conexion->set_charset("utf8mb4");
?>
