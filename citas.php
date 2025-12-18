<?php
session_start();
include('./config/conexion.php');
header('Content-Type: application/json; charset=utf-8');

// Verificar sesion del usuario
if (!isset($_SESSION['usuario_logueado']) || $_SESSION['usuario_logueado'] != true) {
    echo json_encode(['success' => false, 'error' => 'Debes iniciar sesión para agendar una cita.']);
    exit();
}

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['success' => false, 'error' => 'ID de usuario no disponible']);
    exit();
}

$nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
$telefono = isset($_POST['telefono']) ? trim($_POST['telefono']) : '';
$servicios_array = isset($_POST['servicio']) ? $_POST['servicio'] : array();
$estilista = isset($_POST['estilista']) ? trim($_POST['estilista']) : '';
$tipo_servicio = isset($_POST['edad']) ? trim($_POST['edad']) : '';
$fecha = isset($_POST['fecha']) ? trim($_POST['fecha']) : '';
$hora = isset($_POST['hora']) ? trim($_POST['hora']) : '';
$notas = isset($_POST['notas']) ? trim($_POST['notas']) : '';
$usuario_id = intval($_SESSION['usuario_id']);

// Validar campos requeridos
if (empty($nombre) || empty($telefono) || empty($fecha) || empty($hora) || empty($servicios_array)) {
    echo json_encode(['success' => false, 'error' => 'Faltan campos requeridos']);
    exit();
}

// Validar que la hora esté en el rango permitido (10:00 - 19:00)
$hora_partes = explode(':', $hora);
$hora_num = intval($hora_partes[0]);
if ($hora_num < 10 || $hora_num >= 19) {
    echo json_encode(['success' => false, 'error' => 'Las citas están disponibles de 10:00 a 19:00']);
    exit();
}

// Convertir array a string
if (is_array($servicios_array)) {
    $servicios = implode(", ", $servicios_array);
} else {
    $servicios = $servicios_array;
}

// Verificar que solo tenga una cita por día
$stmt_verificar = $conexion->prepare("SELECT id FROM citas WHERE usuario_id = ? AND fecha = ?");
if (!$stmt_verificar) {
    echo json_encode(['success' => false, 'error' => 'Error en la BD: ' . $conexion->error]);
    exit();
}

$stmt_verificar->bind_param('is', $usuario_id, $fecha);
$stmt_verificar->execute();
$resultado_verificar = $stmt_verificar->get_result();

if ($resultado_verificar->num_rows > 0) {
    echo json_encode(['success' => false, 'error' => 'Solo puedes tener una cita por día.']);
    $stmt_verificar->close();
    exit();
}
$stmt_verificar->close();

// Insertar cita con Prepared Statements
$stmt = $conexion->prepare("INSERT INTO citas (usuario_id, nombre, telefono, servicios, estilista, tipo_servicio, fecha, hora, notas) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
if (!$stmt) {
    echo json_encode(['success' => false, 'error' => 'Error en la BD: ' . $conexion->error]);
    exit();
}

$stmt->bind_param('issssssss', $usuario_id, $nombre, $telefono, $servicios, $estilista, $tipo_servicio, $fecha, $hora, $notas);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Cita agendada correctamente']);
} else {
    echo json_encode(['success' => false, 'error' => 'Error al agendar la cita: ' . $stmt->error]);
}

$stmt->close();

$conexion->close();
?>
