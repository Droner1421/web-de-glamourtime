document.addEventListener('DOMContentLoaded', function() {
    cargarClientes();
});

async function cargarClientes() {
    try {
        const response = await fetch('obtener-clientes.php');
        const data = await response.json();
        
        if (data.success) {
            mostrarClientes(data.clientes);
        } else {
            mostrarError('Error al cargar clientes: ' + data.error);
        }
    } catch (error) {
        console.error('Error:', error);
        mostrarError('Error al cargar clientes');
    }
}

function mostrarClientes(clientes) {
    const contenedor = document.querySelector('#tabla-clientes');
    
    if (!contenedor) {
        console.error('No se encontró el contenedor #tabla-clientes');
        return;
    }
    
    if (clientes.length === 0) {
        contenedor.innerHTML = '<p style="text-align: center; padding: 2rem; color: #999;">No hay clientes registrados</p>';
        return;
    }
    
    let html = `
        <table class="tabla-clientes">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Teléfono</th>
                    <th>Total Citas</th>
                    <th>Última Cita</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
    `;
    
    clientes.forEach(cliente => {
        const ultimaCita = cliente.ultima_cita ? new Date(cliente.ultima_cita).toLocaleDateString('es-ES') : 'N/A';
        
        html += `
            <tr>
                <td>${cliente.id}</td>
                <td>${escapeHtml(cliente.nombre)}</td>
                <td>${escapeHtml(cliente.email)}</td>
                <td>${escapeHtml(cliente.telefono || '-')}</td>
                <td><span class="badge-citas">${cliente.total_citas}</span></td>
                <td>${ultimaCita}</td>
                <td>
                    <button class="btn-accion btn-ver" onclick="verDetalleCliente(${cliente.id}, '${escapeHtml(cliente.nombre)}')">Ver Citas</button>
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
    const contador = document.getElementById('contador-clientes');
    if (contador) {
        contador.textContent = clientes.length;
    }
}

async function verDetalleCliente(clienteId, nombreCliente) {
    try {
        const response = await fetch(`obtener-citas.php?cliente_id=${clienteId}`);
        const data = await response.json();
        
        let citasHtml = '<p style="color: #999;">No tiene citas registradas</p>';
        
        if (data.success && data.citas && data.citas.length > 0) {
            citasHtml = '<ul style="list-style: none; padding: 0;">';
            data.citas.forEach(cita => {
                const fechaFormato = new Date(cita.fecha).toLocaleDateString('es-ES');
                citasHtml += `
                    <li style="padding: 0.5rem 0; border-bottom: 1px solid #eee;">
                        <strong>Fecha:</strong> ${fechaFormato} - <strong>Hora:</strong> ${cita.hora}
                        <br><strong>Servicios:</strong> ${cita.servicios}
                    </li>
                `;
            });
            citasHtml += '</ul>';
        }
        
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
            <div style="background: white; padding: 2rem; border-radius: 8px; max-width: 600px; width: 90%; max-height: 80vh; overflow-y: auto;">
                <h2 style="color: #333; margin-bottom: 1rem;">Citas de ${nombreCliente}</h2>
                <div>${citasHtml}</div>
                <button onclick="this.closest('div').parentElement.remove()" style="width: 100%; padding: 0.75rem; background: #d4af37; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: 600; margin-top: 1rem;">Cerrar</button>
            </div>
        `;
        
        document.body.appendChild(modal);
        
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                modal.remove();
            }
        });
    } catch (error) {
        console.error('Error:', error);
        mostrarError('Error al cargar citas del cliente');
    }
}

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = String(text);
    return div.innerHTML;
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
