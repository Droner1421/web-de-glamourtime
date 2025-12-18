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

// Verificar la sesion como administradorr
if (!isset($_SESSION['es_admin']) || $_SESSION['es_admin'] != 1) {
    
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Método no permitido']);
    exit;
}

header('Content-Type: application/json; charset=utf-8');

// Recibir datos del formulario
$nombre = isset($_POST['nombre_producto']) ? trim($_POST['nombre_producto']) : '';
$descripcion = isset($_POST['descripcion']) ? trim($_POST['descripcion']) : '';
$precio = isset($_POST['precio']) ? floatval($_POST['precio']) : 0;
$categoria = isset($_POST['categoria']) ? strtolower(trim($_POST['categoria'])) : '';

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

//  imagen
$imagen_url = '';
if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
    $archivo_tmp = $_FILES['imagen']['tmp_name'];
    $nombre_archivo = basename($_FILES['imagen']['name']);
    $extension = strtolower(pathinfo($nombre_archivo, PATHINFO_EXTENSION));
    $extensiones_permitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    
    if (!in_array($extension, $extensiones_permitidas)) {
        $errores[] = 'Solo se permiten imágenes (jpg, jpeg, png, gif, webp)';
    } else {
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
} else {
    $errores[] = 'La imagen es requerida';
}

if (!empty($errores)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'errores' => $errores]);
    exit;
}

try {
    $sql = "INSERT INTO productos (nombre_producto, descripcion, precio, imagen_url, categoria) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conexion->prepare($sql);
    
    if (!$stmt) {
        throw new Exception('Error en la preparación: ' . $conexion->error);
    }
    
    $stmt->bind_param('ssdss', $nombre, $descripcion, $precio, $imagen_url, $categoria);
    
    if ($stmt->execute()) {
        $id_nuevo = $conexion->insert_id;
        echo json_encode([
            'success' => true,
            'mensaje' => 'Producto agregado exitosamente',
            'id_producto' => $id_nuevo
        ]);
    } else {
        throw new Exception('Error al insertar: ' . $stmt->error);
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
