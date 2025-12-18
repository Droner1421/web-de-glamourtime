<?php
session_start();

header('Content-Type: application/json');

$respuesta = array();

if (isset($_SESSION['usuario_logueado']) && $_SESSION['usuario_logueado'] == true) {
    $respuesta['logueado'] = true;
    $respuesta['usuario_nombre'] = $_SESSION['usuario_nombre'];
    $respuesta['usuario_correo'] = $_SESSION['usuario_correo'];
    $respuesta['usuario_id'] = $_SESSION['usuario_id'];
    $respuesta['es_admin'] = isset($_SESSION['es_admin']) ? $_SESSION['es_admin'] : false;
} else {
    $respuesta['logueado'] = false;
    $respuesta['es_admin'] = false;
}

echo json_encode($respuesta);
?>
