<?php
session_start();
require_once 'config/conexion.php';
require_once 'config/categorias.php';

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

// Recibir datos del formulario
$id_producto = isset($_POST['id_producto']) ? intval($_POST['id_producto']) : 0;
$nombre = isset($_POST['nombre_producto']) ? trim($_POST['nombre_producto']) : '';
$descripcion = isset($_POST['descripcion']) ? trim($_POST['descripcion']) : '';
$precio = isset($_POST['precio']) ? floatval($_POST['precio']) : 0;
$categoria = isset($_POST['categoria']) ? strtolower(trim($_POST['categoria'])) : '';

// Validar ID del producto
if ($id_producto <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'ID de producto inválido']);
    exit;
}

// Validar datos
$errores = [];

if (empty($nombre)) {
    $errores[] = 'El nombre del producto es requerido';
}
if (empty($descripcion)) {
    $errores[] = 'La descripción es requerida';
}
if ($precio <= 0) {
    $errores[] = 'El precio debe ser mayor a 0';
}
if (empty($categoria)) {
    $errores[] = 'La categoría es requerida';
} elseif (!validarCategoria($categoria)) {
    $errores[] = 'La categoría seleccionada no es válida';
}

// Obtener datos actuales del producto
try {
    $sql_actual = "SELECT imagen_url FROM productos WHERE id_producto = ?";
    $stmt_actual = $conexion->prepare($sql_actual);
    
    if (!$stmt_actual) {
        throw new Exception('Error en la preparación: ' . $conexion->error);
    }
    
    $stmt_actual->bind_param('i', $id_producto);
    $stmt_actual->execute();
    $resultado_actual = $stmt_actual->get_result();
    
    if ($resultado_actual->num_rows === 0) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Producto no encontrado']);
        $stmt_actual->close();
        $conexion->close();
        exit;
    }
    
    $producto_actual = $resultado_actual->fetch_assoc();
    $imagen_url = $producto_actual['imagen_url'];
    $stmt_actual->close();
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    $conexion->close();
    exit;
}

// Procesar imagen si se proporciona una nueva
if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
    $archivo_tmp = $_FILES['imagen']['tmp_name'];
    $nombre_archivo = basename($_FILES['imagen']['name']);
    $extension = strtolower(pathinfo($nombre_archivo, PATHINFO_EXTENSION));
    $extensiones_permitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    
    if (!in_array($extension, $extensiones_permitidas)) {
        $errores[] = 'Solo se permiten imágenes (jpg, jpeg, png, gif, webp)';
    } else {
        // Eliminar imagen anterior si existe
        if (!empty($imagen_url) && file_exists($imagen_url)) {
            unlink($imagen_url);
        }
        
        $nombre_archivo_nuevo = date('YmdHis') . '_' . uniqid() . '.' . $extension;
        $ruta_destino = 'src/img/productos/' . $nombre_archivo_nuevo;
        
        if (!is_dir('src/img/productos/')) {
            mkdir('src/img/productos/', 0755, true);
        }
        
        if (move_uploaded_file($archivo_tmp, $ruta_destino)) {
            $imagen_url = $ruta_destino;
        } else {
            $errores[] = 'Error al subir la imagen';
        }
    }
}

if (!empty($errores)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'errores' => $errores]);
    exit;
}

try {
    $sql = "UPDATE productos SET nombre_producto = ?, descripcion = ?, precio = ?, imagen_url = ?, categoria = ? WHERE id_producto = ?";
    $stmt = $conexion->prepare($sql);
    
    if (!$stmt) {
        throw new Exception('Error en la preparación: ' . $conexion->error);
    }
    
    $stmt->bind_param('ssdssi', $nombre, $descripcion, $precio, $imagen_url, $categoria, $id_producto);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'mensaje' => 'Producto actualizado exitosamente',
            'id_producto' => $id_producto
        ]);
    } else {
        throw new Exception('Error al actualizar: ' . $stmt->error);
    }
    
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
