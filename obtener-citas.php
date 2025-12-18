<?php
session_start();
include('./config/conexion.php');

header('Content-Type: application/json; charset=utf-8');

// Verificar sesion del usuario
if (!isset($_SESSION['usuario_logueado']) || $_SESSION['usuario_logueado'] != true) {
    echo json_encode(['success' => false, 'message' => 'No estás logueado']);
    exit();
}

// Verificar si es un request del admin o del usuario
$es_admin = isset($_SESSION['es_admin']) && $_SESSION['es_admin'] == 1;
$cliente_id = isset($_GET['cliente_id']) ? intval($_GET['cliente_id']) : 0;

if ($es_admin && $cliente_id > 0) {
    // Request del admin para ver citas de un cliente específico
    $usuario_id = $cliente_id;
} else {
    // Request del usuario para ver sus propias citas
    if (!isset($_SESSION['usuario_id'])) {
        echo json_encode(['success' => false, 'message' => 'ID de usuario no disponible']);
        exit();
    }
    $usuario_id = intval($_SESSION['usuario_id']);
}

// Obtener las citas del usuario usando Prepared Statements
$stmt = $conexion->prepare("SELECT id, usuario_id, nombre, telefono, servicios, estilista, tipo_servicio, fecha, hora, notas, fecha_creacion FROM citas WHERE usuario_id = ? ORDER BY fecha DESC, hora DESC");
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Error en la BD: ' . $conexion->error]);
    exit();
}

$stmt->bind_param('i', $usuario_id);
$stmt->execute();
$resultado = $stmt->get_result();

$citas = [];
if ($resultado && $resultado->num_rows > 0) {
    while ($fila = $resultado->fetch_assoc()) {
        $citas[] = $fila;
    }
}

$stmt->close();
$conexion->close();

echo json_encode([
    'success' => true,
    'citas' => $citas
]);
?>
