<?php
header('Content-Type: application/json; charset=utf-8');
require_once 'config/conexion.php';

try {
    $sql = "SELECT id_producto, nombre_producto, descripcion, precio, imagen_url, categoria FROM productos ORDER BY nombre_producto";
    $stmt = $conexion->prepare($sql);
    
    if (!$stmt) {
        throw new Exception('Error en la preparación: ' . $conexion->error);
    }
    
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    $productos = [];
    while ($row = $resultado->fetch_assoc()) {
        $productos[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'productos' => $productos
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
