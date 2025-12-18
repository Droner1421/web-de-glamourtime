<?php
session_start();
require_once 'config/conexion.php';

header('Content-Type: application/json; charset=utf-8');

// Verificar sesion del usuario
if (!isset($_SESSION['usuario_logueado']) || $_SESSION['usuario_logueado'] != true) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Debes estar autenticado']);
    exit;
}

// Verificar la sesion como administrador
if (!isset($_SESSION['es_admin']) || $_SESSION['es_admin'] != 1) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'No tienes permisos de administrador']);
    exit;
}

// Obtener filtros (opcionales)
$fecha_desde = isset($_GET['fecha_desde']) ? trim($_GET['fecha_desde']) : '';
$fecha_hasta = isset($_GET['fecha_hasta']) ? trim($_GET['fecha_hasta']) : '';
$estado = isset($_GET['estado']) ? trim($_GET['estado']) : '';

try {
    // Construir consulta dinámica
    $sql = "SELECT id, usuario_id, nombre, telefono, servicios, estilista, tipo_servicio, fecha, hora, notas, fecha_creacion, estado FROM citas WHERE 1=1";
    $params = [];
    $tipos = '';
    
    if (!empty($fecha_desde)) {
        $sql .= " AND fecha >= ?";
        $params[] = $fecha_desde;
        $tipos .= 's';
    }
    
    if (!empty($fecha_hasta)) {
        $sql .= " AND fecha <= ?";
        $params[] = $fecha_hasta;
        $tipos .= 's';
    }
    
    if (!empty($estado)) {
        $sql .= " AND estado = ?";
        $params[] = $estado;
        $tipos .= 's';
    }
    
    $sql .= " ORDER BY fecha DESC, hora DESC";
    
    $stmt = $conexion->prepare($sql);
    
    if (!$stmt) {
        throw new Exception('Error en la preparación: ' . $conexion->error);
    }
    
    if (!empty($params)) {
        $stmt->bind_param($tipos, ...$params);
    }
    
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    $citas = [];
    while ($row = $resultado->fetch_assoc()) {
        $citas[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'citas' => $citas,
        'total' => count($citas)
    ]);
    
    $stmt->close();
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

$conexion->close();
?>
