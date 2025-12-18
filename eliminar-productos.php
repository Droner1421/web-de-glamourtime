<?php
session_start();
require_once 'config/conexion.php';

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

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Método no permitido']);
    exit;
}

header('Content-Type: application/json; charset=utf-8');

// Recibir ID del producto
$id_producto = isset($_POST['id_producto']) ? intval($_POST['id_producto']) : 0;

if ($id_producto <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'ID de producto inválido']);
    exit;
}

try {
    // Obtener datos del producto
    $sql_select = "SELECT imagen_url FROM productos WHERE id_producto = ?";
    $stmt_select = $conexion->prepare($sql_select);
    
    if (!$stmt_select) {
        throw new Exception('Error en la preparación: ' . $conexion->error);
    }
    
    $stmt_select->bind_param('i', $id_producto);
    $stmt_select->execute();
    $resultado = $stmt_select->get_result();
    
    if ($resultado->num_rows === 0) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Producto no encontrado']);
        $stmt_select->close();
        $conexion->close();
        exit;
    }
    
    $producto = $resultado->fetch_assoc();
    $stmt_select->close();
    
    // Eliminar imagen si existe
    if (!empty($producto['imagen_url']) && file_exists($producto['imagen_url'])) {
        unlink($producto['imagen_url']);
    }
    
    // Eliminar producto de la base de datos
    $sql_delete = "DELETE FROM productos WHERE id_producto = ?";
    $stmt_delete = $conexion->prepare($sql_delete);
    
    if (!$stmt_delete) {
        throw new Exception('Error en la preparación: ' . $conexion->error);
    }
    
    $stmt_delete->bind_param('i', $id_producto);
    
    if ($stmt_delete->execute()) {
        echo json_encode([
            'success' => true,
            'mensaje' => 'Producto eliminado exitosamente'
        ]);
    } else {
        throw new Exception('Error al eliminar: ' . $stmt_delete->error);
    }
    
    $stmt_delete->close();
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

$conexion->close();
?>
