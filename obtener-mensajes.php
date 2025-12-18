<?php
session_start();

header('Content-Type: application/json');

$respuesta = [];

if (isset($_SESSION['error'])) {
    $respuesta['error'] = $_SESSION['error'];
    unset($_SESSION['error']);
}

if (isset($_SESSION['exito'])) {
    $respuesta['exito'] = $_SESSION['exito'];
    unset($_SESSION['exito']);
}

echo json_encode($respuesta);
?>
