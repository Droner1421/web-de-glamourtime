<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
include('./config/conexion.php');

$accion = isset($_POST['accion']) ? $_POST['accion'] : '';

// LOGIN
if ($accion == 'login') {
    $correo = isset($_POST['correo']) ? trim($_POST['correo']) : '';
    $contrasena = isset($_POST['contrasena']) ? $_POST['contrasena'] : '';

    // Validar campos
    if (empty($correo) || empty($contrasena)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Correo y contraseña son requeridos']);
        $conexion->close();
        exit;
    }

    // preparara consulta sql
    $stmt = $conexion->prepare("SELECT id, nombres, apellidopat, apellidomat, correo, contrasena FROM usuarios WHERE correo = ?");
    
    if (!$stmt) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Error en la consulta: ' . $conexion->error]);
        $conexion->close();
        exit;
    }

    $stmt->bind_param('s', $correo);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $fila = $resultado->fetch_assoc();
        
        // Comparar contraseña directamente (sin MD5)
        if ($contrasena === $fila['contrasena']) {
            // Verificar si es admin (por correo)
            $es_admin = ($fila['correo'] === 'glamourtim3@gmail.com');
            
            // Guardar en sesión
            $_SESSION['usuario_id'] = $fila['id'];
            $_SESSION['usuario_nombre'] = $fila['nombres'] . ' ' . $fila['apellidopat'];
            $_SESSION['usuario_correo'] = $fila['correo'];
            $_SESSION['usuario_logueado'] = true;
            $_SESSION['es_admin'] = $es_admin;
            
            http_response_code(200);
            echo json_encode([
                'success' => true, 
                'mensaje' => 'Inicio de sesión exitoso'
            ]);
        } else {
            http_response_code(401);
            echo json_encode(['success' => false, 'error' => 'Contraseña incorrecta']);
        }
    } else {
        http_response_code(401);
        echo json_encode(['success' => false, 'error' => 'El correo no está registrado']);
    }

    $stmt->close();
}

// REGISTRO
elseif ($accion == 'register') {
    $nombres = isset($_POST['nombres']) ? trim($_POST['nombres']) : '';
    $apellidopat = isset($_POST['apellidopat']) ? trim($_POST['apellidopat']) : '';
    $apellidomat = isset($_POST['apellidomat']) ? trim($_POST['apellidomat']) : '';
    $telefono = isset($_POST['telefono']) ? trim($_POST['telefono']) : '';
    $correo = isset($_POST['correo']) ? trim($_POST['correo']) : '';
    $contrasena = isset($_POST['contrasena']) ? $_POST['contrasena'] : '';
    $confirmar_contrasena = isset($_POST['confirmar_contrasena']) ? $_POST['confirmar_contrasena'] : '';

    // Validar campos
    $errores = [];
    
    if (empty($nombres)) $errores[] = 'El nombre es requerido';
    if (empty($apellidopat)) $errores[] = 'El apellido paterno es requerido';
    if (empty($correo)) $errores[] = 'El correo es requerido';
    if (empty($telefono)) $errores[] = 'El teléfono es requerido';
    if (empty($contrasena)) $errores[] = 'La contraseña es requerida';
    if ($contrasena !== $confirmar_contrasena) $errores[] = 'Las contraseñas no coinciden';

    if (!empty($errores)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'errores' => $errores]);
        $conexion->close();
        exit;
    }

    // Verificar si el correo existye
    $stmt_check = $conexion->prepare("SELECT id FROM usuarios WHERE correo = ?");
    $stmt_check->bind_param('s', $correo);
    $stmt_check->execute();
    
    if ($stmt_check->get_result()->num_rows > 0) {
        $stmt_check->close();
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'El correo ya está registrado']);
        $conexion->close();
        exit;
    }
    $stmt_check->close();

    // Guardar contraseña tal como se ingresa
    $contrasena_guardada = $contrasena;

    // Insertar nuevo usuario
    $stmt = $conexion->prepare("INSERT INTO usuarios (nombres, apellidopat, apellidomat, telefono, correo, contrasena) VALUES (?, ?, ?, ?, ?, ?)");
    
    if (!$stmt) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Error en la inserción: ' . $conexion->error]);
        $conexion->close();
        exit;
    }

    $stmt->bind_param('ssssss', $nombres, $apellidopat, $apellidomat, $telefono, $correo, $contrasena_guardada);
    
    if ($stmt->execute()) {
        $usuario_id = $conexion->insert_id;
        
        // Crear sesión automáticamente
        $_SESSION['usuario_id'] = $usuario_id;
        $_SESSION['usuario_nombre'] = $nombres . ' ' . $apellidopat;
        $_SESSION['usuario_correo'] = $correo;
        $_SESSION['usuario_logueado'] = true;
        
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'mensaje' => 'Registro exitoso. ¡Bienvenido!'
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Error al registrar: ' . $stmt->error]);
    }

    $stmt->close();
}

$conexion->close();
?>






