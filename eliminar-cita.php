<?php
session_start();
include('./config/conexion.php');

header('Content-Type: application/json; charset=utf-8');

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_logueado']) || $_SESSION['usuario_logueado'] != true) {
    echo json_encode(['success' => false, 'error' => 'No estás logueado']);
    exit();
}

if (!isset($_POST['id_cita'])) {
    echo json_encode(['success' => false, 'error' => 'ID de cita no proporcionado']);
    exit();
}

$cita_id = intval($_POST['id_cita']);
$usuario_id = intval($_SESSION['usuario_id']);
$es_admin = isset($_SESSION['es_admin']) ? intval($_SESSION['es_admin']) : 0;

// Si es admin, puede eliminar cualquier cita
// Si no es admin, solo puede eliminar sus propias citas
if ($es_admin == 1) {
    // Admin puede eliminar cualquier cita
    $stmt_eliminar = $conexion->prepare("DELETE FROM citas WHERE id = ?");
    if (!$stmt_eliminar) {
        echo json_encode(['success' => false, 'error' => 'Error en la BD: ' . $conexion->error]);
        $conexion->close();
        exit();
    }
    $stmt_eliminar->bind_param('i', $cita_id);
} else {
    // Usuario normal solo puede eliminar sus propias citas
    $stmt_eliminar = $conexion->prepare("DELETE FROM citas WHERE id = ? AND usuario_id = ?");
    if (!$stmt_eliminar) {
        echo json_encode(['success' => false, 'error' => 'Error en la BD: ' . $conexion->error]);
        $conexion->close();
        exit();
    }
    $stmt_eliminar->bind_param('ii', $cita_id, $usuario_id);
}

if ($stmt_eliminar->execute()) {
    if ($stmt_eliminar->affected_rows > 0) {
        echo json_encode(['success' => true, 'message' => 'Cita eliminada correctamente']);
    } else {
        echo json_encode(['success' => false, 'error' => 'La cita no existe o no tienes permisos para eliminarla']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Error al eliminar: ' . $stmt_eliminar->error]);
}

$stmt_eliminar->close();
$conexion->close();
?>