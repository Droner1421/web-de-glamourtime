<?php
session_start();
include('./config/conexion.php');

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_logueado']) || $_SESSION['usuario_logueado'] != true) {
    header('Location: index.html');
    exit();
}

if (!isset($_SESSION['usuario_id'])) {
    header('Location: index.html');
    exit();
}

if (!isset($_GET['id'])) {
    header('Location: mis-citas.php');
    exit();
}

$cita_id = intval($_GET['id']);
$usuario_id = intval($_SESSION['usuario_id']);

// Obtener la cita usando Prepared Statements
$stmt = $conexion->prepare("SELECT id, usuario_id, nombre, telefono, servicios, estilista, tipo_servicio, fecha, hora, notas FROM citas WHERE id = ? AND usuario_id = ?");
if (!$stmt) {
    header('Location: mis-citas.php');
    exit();
}

$stmt->bind_param('ii', $cita_id, $usuario_id);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows == 0) {
    $stmt->close();
    header('Location: mis-citas.php');
    exit();
}

$cita = $resultado->fetch_assoc();
$stmt->close();

// Parsear servicios para poder marcar checkboxes
$servicios_actuales = array_map('trim', explode(',', $cita['servicios']));

$error = '';
$exito = '';

// validar si hay que actualizar
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
    $telefono = isset($_POST['telefono']) ? trim($_POST['telefono']) : '';
    $servicios_array = isset($_POST['servicio']) ? $_POST['servicio'] : array();
    $servicios = is_array($servicios_array) ? implode(", ", $servicios_array) : trim($servicios_array);
    $estilista = isset($_POST['estilista']) ? trim($_POST['estilista']) : '';
    $tipo_servicio = isset($_POST['edad']) ? trim($_POST['edad']) : '';
    $fecha = isset($_POST['fecha']) ? trim($_POST['fecha']) : '';
    $hora = isset($_POST['hora']) ? trim($_POST['hora']) : '';
    $notas = isset($_POST['notas']) ? trim($_POST['notas']) : '';

    // Validar campos requeridos
    if (empty($nombre) || empty($telefono) || empty($fecha) || empty($hora) || empty($servicios)) {
        $error = "Faltan campos requeridos";
    } else {
        // Validar que solo tenga una cita por día
        $stmt_verificar = $conexion->prepare("SELECT id FROM citas WHERE usuario_id = ? AND fecha = ? AND id != ?");
        if (!$stmt_verificar) {
            $error = "Error en la BD: " . $conexion->error;
        } else {
            $stmt_verificar->bind_param('isi', $usuario_id, $fecha, $cita_id);
            $stmt_verificar->execute();
            $resultado_verificar = $stmt_verificar->get_result();

            if ($resultado_verificar->num_rows > 0) {
                $error = "Solo puedes tener una cita por día.";
            } else {
                // Actualizar la cita con Prepared Statements
                $stmt_update = $conexion->prepare("UPDATE citas SET nombre=?, telefono=?, servicios=?, estilista=?, tipo_servicio=?, fecha=?, hora=?, notas=? WHERE id=? AND usuario_id=?");
                if (!$stmt_update) {
                    $error = "Error en la BD: " . $conexion->error;
                } else {
                    $stmt_update->bind_param('ssssssssii', $nombre, $telefono, $servicios, $estilista, $tipo_servicio, $fecha, $hora, $notas, $cita_id, $usuario_id);

                    if ($stmt_update->execute()) {
                        $exito = "Cita actualizada correctamente. Redirigiendo...";
                        
                        // Recargar la cita
                        $stmt_reload = $conexion->prepare("SELECT id, usuario_id, nombre, telefono, servicios, estilista, tipo_servicio, fecha, hora, notas FROM citas WHERE id = ? AND usuario_id = ?");
                        $stmt_reload->bind_param('ii', $cita_id, $usuario_id);
                        $stmt_reload->execute();
                        $resultado_reload = $stmt_reload->get_result();
                        $cita = $resultado_reload->fetch_assoc();
                        $servicios_actuales = array_map('trim', explode(',', $cita['servicios']));
                        $stmt_reload->close();
                        
                        // Redirigir después de 2 segundos con parámetro para forzar recarga
                        echo "<script>setTimeout(function() { window.location.href = 'mis-citas.html?refresh=' + Date.now(); }, 2000);</script>";
                    } else {
                        $error = "Error al actualizar: " . $stmt_update->error;
                    }
                    $stmt_update->close();
                }
            }
            $stmt_verificar->close();
        }
    }
}

// Calcular fecha mínima (mañana)
// Ya no es necesaria
// $fecha_minima = new DateTime();
// $fecha_minima->add(new DateInterval('P1D'));
// $fecha_minima_str = $fecha_minima->format('Y-m-d');

$conexion->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="src/css/estilos.css">
    <link rel="stylesheet" href="src/css/responsive.css">
    <link rel="stylesheet" href="src/css/user-menu.css">
    <link rel="stylesheet" href="src/css/editar-cita.css">
    <title>Editar Cita - Glamour Time</title>
</head>
<body>
    <div class="barra_superior">
        <div class="info-contacto">
            <a href="tel:123-456-7890" class="contacto-item">
                <i class="icon">📞</i>
                <span>123-456-7890</span>
            </a>
            <a href="mailto:glamourtim3@gmail.com" class="contacto-item">
                <i class="icon">📧</i>
                <span>glamourtim3@gmail.com</span>
            </a>
        </div>
    </div>

    <div class="barra_navegacion">
        <div class="nav-container">
            <div class="logo-container">
                <div class="logo-icon">💎</div>
                <div class="titulo">Glamour Time</div>
            </div>
            <div class="alternar-menu" onclick="alternarMenu()">
                <span></span>
                <span></span>
                <span></span>
            </div>
            <nav class="enlaces-nav">
                <a href="index.html" class="item-nav">Inicio</a>
                <a href="conocenos.html" class="item-nav">Conócenos</a>
                <a href="productos.html" class="item-nav">Productos</a>
                <a href="servicios.html" class="item-nav">Servicios</a>
                <a href="mis-citas.html" class="item-nav active">Mis Citas</a>
                <a href="#" class="item-nav usuario abrir-auth">
                    <img class="avatar" src="/src/img/avatar.png" alt="usuario">
                </a>
            </nav>
        </div>
    </div>

    <main>
        <div class="editar-cita-contenedor">
            <h1>✏️ Editar Cita</h1>
            <p class="editar-subtitulo">Modifica los detalles de tu cita</p>

            <?php if ($error): ?>
                <div class="error-message show">❌ <?php echo $error; ?></div>
            <?php endif; ?>

            <?php if ($exito): ?>
                <div class="exito-message show">✅ <?php echo $exito; ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label for="nombre">👤 Nombre</label>
                    <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($cita['nombre']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="telefono">📞 Teléfono</label>
                    <input type="tel" id="telefono" name="telefono" value="<?php echo htmlspecialchars($cita['telefono']); ?>" required>
                </div>

                <div class="seleccion-servicios"> 
            <label class="etiqueta-servicios">Selecciona los servicios</label>
            <div class="grupo-checkbox">
              <label class="etiqueta-checkbox">
                <input type="checkbox" id="manicure" name="servicio" value="manicure" <?php echo in_array('manicure', $servicios_actuales) ? 'checked' : ''; ?>>
                <span class="checkbox-personalizado"></span>
                <span class="texto-checkbox">Manicura & Pedicura</span>
              </label>
              <label class="etiqueta-checkbox">
                <input type="checkbox" id="corte" name="servicio" value="corte" <?php echo in_array('corte', $servicios_actuales) ? 'checked' : ''; ?>>
                <span class="checkbox-personalizado"></span>
                <span class="texto-checkbox">Corte & Peinado</span>
              </label>
              <label class="etiqueta-checkbox">
                <input type="checkbox" id="tintes" name="servicio" value="tintes" <?php echo in_array('tintes', $servicios_actuales) ? 'checked' : ''; ?>>
                <span class="checkbox-personalizado"></span>
                <span class="texto-checkbox">Tintes & Mechas</span>
              </label>
              <label class="etiqueta-checkbox">
                <input type="checkbox" id="faciales" name="servicio" value="faciales" <?php echo in_array('faciales', $servicios_actuales) ? 'checked' : ''; ?>>
                <span class="checkbox-personalizado"></span>
                <span class="texto-checkbox">Faciales & Tratamientos</span>
              </label>
            </div>
          </div>

                <div class="form-group">
            <label for="estilista">👗 Estilista</label>
            <select id="estilista" name="estilista" required>
              <option value="">Selecciona un estilista</option>
              <option value="ana" <?php echo $cita['estilista'] === 'ana' ? 'selected' : ''; ?>>Ana Martínez</option>
              <option value="luis" <?php echo $cita['estilista'] === 'luis' ? 'selected' : ''; ?>>Luis Fernández</option>
              <option value="maria" <?php echo $cita['estilista'] === 'maria' ? 'selected' : ''; ?>>María Gómez</option>
              <option value="carlos" <?php echo $cita['estilista'] === 'carlos' ? 'selected' : ''; ?>>Carlos Rodríguez</option>
            </select>
          </div>

                <div class="seleccion-edad">  
            <label class="etiqueta-edad">Selecciona tipo de servicio</label>
            <div class="grupo-radio">
              <label class="etiqueta-radio">
                <input type="radio" name="edad" value="adulto" <?php echo $cita['tipo_servicio'] === 'adulto' ? 'checked' : ''; ?> required>
                <span class="radio-personalizado"></span>
                <span class="texto-radio">Adulto</span>
              </label>
              <label class="etiqueta-radio">
                <input type="radio" name="edad" value="nino" <?php echo $cita['tipo_servicio'] === 'nino' ? 'checked' : ''; ?> required>
                <span class="radio-personalizado"></span>
                <span class="texto-radio">Niño</span>
              </label>          
            </div>
          </div>

                <div class="form-group">
                    <label for="fecha">📅 Fecha</label>
                    <input type="date" id="fecha" name="fecha" value="<?php echo $cita['fecha']; ?>" required>
                </div>

                <div class="form-group">
                    <label for="hora">🕐 Hora</label>
                    <select id="hora" name="hora" required>
                        <option value="">Selecciona una hora</option>
                        <?php
                        for ($h = 10; $h < 19; $h++) {
                            $hora_str = str_pad($h, 2, '0', STR_PAD_LEFT) . ':00:00';
                            $hora_display = str_pad($h, 2, '0', STR_PAD_LEFT) . ':00';
                            $selected = $cita['hora'] === $hora_str ? 'selected' : '';
                            echo "<option value='$hora_str' $selected>$hora_display</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="notas">📝 Notas Adicionales</label>
                    <textarea id="notas" name="notas"><?php echo htmlspecialchars($cita['notas']); ?></textarea>
                </div>

                <div class="form-actions">
                    <input type="submit" class="btn-guardar" value="💾 Guardar Cambios">
                    <a href="mis-citas.html" class="btn-cancelar">← Volver</a>
                    <input type="button" class="btn-eliminar" value="🗑️ Eliminar" onclick="abrirModalEliminar()">
                </div>
            </form>
        </div>
    </main>

    <!-- Modal de confirmación de eliminación -->
    <div id="modal-eliminar" class="modal">
        <div class="modal-content">
            <div class="modal-header">⚠️ Confirmar Eliminación</div>
            <div class="modal-body">
                ¿Estás seguro de que deseas eliminar esta cita? Esta acción no se puede deshacer.
            </div>
            <div class="modal-footer">
                <input type="button" class="btn-modal btn-cancelar-modal" value="Cancelar" onclick="cerrarModal()">
                <input type="button" class="btn-modal btn-confirmar" value="Sí, Eliminar" onclick="confirmarEliminacion()">
            </div>
        </div>
    </div>

    <script src="src/js/menu.js"></script>
    <script>
        function abrirModalEliminar() {
            document.getElementById('modal-eliminar').style.display = 'block';
        }

        function cerrarModal() {
            document.getElementById('modal-eliminar').style.display = 'none';
        }

        function confirmarEliminacion() {
            const citaId = <?php echo $cita_id; ?>;
            
            fetch('eliminar-cita.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'id=' + citaId
            })
            .then(response => response.json())
            .then(data => {
                cerrarModal();
                if (data.success) {
                    alert('✅ Cita eliminada correctamente');
                    window.location.href = 'mis-citas.html';
                } else {
                    alert('❌ ' + (data.message || 'Error al eliminar la cita'));
                }
            })
            .catch(error => {
                cerrarModal();
                console.error('Error:', error);
                alert('❌ Error al conectar con el servidor');
            });
        }

        // Cerrar modal al hacer click fuera
        window.onclick = function(event) {
            const modal = document.getElementById('modal-eliminar');
            if (event.target === modal) {
                cerrarModal();
            }
        }
    </script>
</body>
</html>
