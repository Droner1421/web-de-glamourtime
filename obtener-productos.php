<?php
header('Content-Type: application/json; charset=utf-8');
require_once 'config/conexion.php';

$categoria = isset($_GET['categoria']) ? strtolower(trim($_GET['categoria'])) : 'todos';

try {
    if ($categoria === 'todos') {
        $sql = "SELECT id_producto, nombre_producto, descripcion, precio, imagen_url, categoria FROM productos ORDER BY nombre_producto";
    } else {
        $sql = "SELECT id_producto, nombre_producto, descripcion, precio, imagen_url, categoria FROM productos WHERE LOWER(categoria) = ? ORDER BY nombre_producto";
    }
    
    $stmt = $conexion->prepare($sql);
    
    if ($categoria !== 'todos') {
        $stmt->bind_param('s', $categoria);
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
