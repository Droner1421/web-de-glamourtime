document.addEventListener('DOMContentLoaded', function() {
    cargarTodasCitas();
    
    // Event listeners para filtros
    const btnFiltrar = document.getElementById('btn-filtrar');
    if (btnFiltrar) {
        btnFiltrar.addEventListener('click', aplicarFiltros);
    }
    
    const btnLimpiar = document.getElementById('btn-limpiar-filtros');
    if (btnLimpiar) {
        btnLimpiar.addEventListener('click', limpiarFiltros);
    }
});

async function cargarTodasCitas() {
    try {
        const response = await fetch('obtener-todas-citas.php');
        const data = await response.json();
        
        if (data.success) {
            mostrarCitasAdmin(data.citas);
        } else {
            mostrarError('Error al cargar citas: ' + data.error);
        }
    } catch (error) {
        console.error('Error:', error);
        mostrarError('Error al cargar citas');
    }
}

function mostrarCitasAdmin(citas) {
    const contenedor = document.querySelector('#tabla-citas');
    
    if (!contenedor) {
        console.error('No se encontró el contenedor #tabla-citas');
        return;
    }
    
    if (citas.length === 0) {
        contenedor.innerHTML = '<p style="text-align: center; padding: 2rem; color: #999;">No hay citas registradas</p>';
        return;
    }
    
    let html = `
        <table class="tabla-citas">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Cliente</th>
                    <th>Teléfono</th>
                    <th>Servicios</th>
                    <th>Estilista</th>
                    <th>Fecha</th>
                    <th>Hora</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
    `;
    
    citas.forEach(cita => {
        const fechaFormato = new Date(cita.fecha).toLocaleDateString('es-ES');
        const estado = cita.estado || 'pendiente';
        const claseEstado = `estado-${estado}`;
        
        html += `
            <tr>
                <td>${cita.id}</td>
                <td>${escapeHtml(cita.nombre)}</td>
                <td>${escapeHtml(cita.telefono)}</td>
                <td>${escapeHtml(cita.servicios)}</td>
                <td>${escapeHtml(cita.estilista || '-')}</td>
                <td>${fechaFormato}</td>
                <td>${cita.hora}</td>
                <td><span class="badge ${claseEstado}">${estado}</span></td>
                <td>
                    <button class="btn-accion btn-ver" onclick="verDetalleCita(${cita.id})">Ver</button>
                    <button class="btn-accion btn-eliminar" onclick="eliminarCita(${cita.id}, '${escapeHtml(cita.nombre)}')">Eliminar</button>
                </td>
            </tr>
        `;
    });
    
    html += `
            </tbody>
        </table>
    `;
    
    contenedor.innerHTML = html;
    
    // Actualizar contador
    const contador = document.getElementById('contador-citas');
    if (contador) {
        contador.textContent = citas.length;
    }
}

function verDetalleCita(id) {
    // Obtener datos de la cita de la tabla
    const filas = document.querySelectorAll('table tbody tr');
    let citaData = null;
    
    filas.forEach(fila => {
        const celdas = fila.querySelectorAll('td');
        if (celdas[0].textContent == id) {
            citaData = {
                id: celdas[0].textContent,
                cliente: celdas[1].textContent,
                telefono: celdas[2].textContent,
                servicios: celdas[3].textContent,
                estilista: celdas[4].textContent,
                fecha: celdas[5].textContent,
                hora: celdas[6].textContent,
                estado: celdas[7].textContent
            };
        }
    });
    
    if (!citaData) return;
    
    const modal = document.createElement('div');
    modal.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.7);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1000;
    `;
    
    modal.innerHTML = `
        <div style="background: white; padding: 2rem; border-radius: 8px; max-width: 500px; width: 90%; max-height: 80vh; overflow-y: auto;">
            <h2 style="color: #333; margin-bottom: 1.5rem;">Detalles de la Cita</h2>
            
            <div style="margin-bottom: 1rem;">
                <strong>ID:</strong> ${citaData.id}
            </div>
            <div style="margin-bottom: 1rem;">
                <strong>Cliente:</strong> ${citaData.cliente}
            </div>
            <div style="margin-bottom: 1rem;">
                <strong>Teléfono:</strong> ${citaData.telefono}
            </div>
            <div style="margin-bottom: 1rem;">
                <strong>Servicios:</strong> ${citaData.servicios}
            </div>
            <div style="margin-bottom: 1rem;">
                <strong>Estilista:</strong> ${citaData.estilista}
            </div>
            <div style="margin-bottom: 1rem;">
                <strong>Fecha:</strong> ${citaData.fecha}
            </div>
            <div style="margin-bottom: 1rem;">
                <strong>Hora:</strong> ${citaData.hora}
            </div>
            <div style="margin-bottom: 1.5rem;">
                <strong>Estado:</strong> <span class="badge estado-${citaData.estado.toLowerCase()}">${citaData.estado}</span>
            </div>
            
            <button onclick="this.closest('div').parentElement.remove()" style="width: 100%; padding: 0.75rem; background: #d4af37; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: 600;">Cerrar</button>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.remove();
        }
    });
}

async function eliminarCita(id, nombre) {
    if (!confirm(`¿Estás seguro de que deseas eliminar la cita de ${nombre}?`)) {
        return;
    }
    
    const formData = new FormData();
    formData.append('id_cita', id);
    
    try {
        const response = await fetch('eliminar-cita.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            mostrarExito('Cita eliminada exitosamente');
            setTimeout(() => {
                cargarTodasCitas();
            }, 1000);
        } else {
            mostrarError('Error: ' + data.error);
        }
    } catch (error) {
        console.error('Error:', error);
        mostrarError('Error al eliminar cita');
    }
}

function aplicarFiltros() {
    const fechaDesde = document.getElementById('filtro-fecha-desde')?.value || '';
    const fechaHasta = document.getElementById('filtro-fecha-hasta')?.value || '';
    const estado = document.getElementById('filtro-estado')?.value || '';
    
    let url = 'obtener-todas-citas.php?';
    const params = [];
    
    if (fechaDesde) params.push(`fecha_desde=${encodeURIComponent(fechaDesde)}`);
    if (fechaHasta) params.push(`fecha_hasta=${encodeURIComponent(fechaHasta)}`);
    if (estado) params.push(`estado=${encodeURIComponent(estado)}`);
    
    if (params.length > 0) {
        url += params.join('&');
    }
    
    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                mostrarCitasAdmin(data.citas);
            } else {
                mostrarError('Error al filtrar citas');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            mostrarError('Error al filtrar citas');
        });
}

function limpiarFiltros() {
    document.getElementById('filtro-fecha-desde').value = '';
    document.getElementById('filtro-fecha-hasta').value = '';
    document.getElementById('filtro-estado').value = '';
    
    cargarTodasCitas();
}

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = String(text);
    return div.innerHTML;
}

function mostrarExito(mensaje) {
    const notificacion = document.createElement('div');
    notificacion.style.cssText = `
        position: fixed;
        bottom: 20px;
        right: 20px;
        background: #4caf50;
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 4px;
        z-index: 1000;
        box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        font-weight: 600;
    `;
    notificacion.textContent = mensaje;
    document.body.appendChild(notificacion);
    
    setTimeout(() => {
        notificacion.remove();
    }, 3000);
}

function mostrarError(mensaje) {
    const notificacion = document.createElement('div');
    notificacion.style.cssText = `
        position: fixed;
        bottom: 20px;
        right: 20px;
        background: #f44336;
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 4px;
        z-index: 1000;
        box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        font-weight: 600;
    `;
    notificacion.textContent = mensaje;
    document.body.appendChild(notificacion);
    
    setTimeout(() => {
        notificacion.remove();
    }, 5000);
}
