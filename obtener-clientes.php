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

try {
    $sql = "SELECT u.id, CONCAT(u.nombres, ' ', u.apellidopat, ' ', u.apellidomat) as nombre, u.correo as email, u.telefono, COUNT(c.id) as total_citas, MAX(c.fecha) as ultima_cita FROM usuarios u LEFT JOIN citas c ON u.id = c.usuario_id GROUP BY u.id ORDER BY u.nombres";
    
    $stmt = $conexion->prepare($sql);
    
    if (!$stmt) {
        throw new Exception('Error en la preparación: ' . $conexion->error);
    }
    
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    $clientes = [];
    while ($row = $resultado->fetch_assoc()) {
        $clientes[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'clientes' => $clientes,
        'total' => count($clientes)
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
