document.addEventListener('DOMContentLoaded', function() {
    cargarProductosAdmin();
});

async function cargarProductosAdmin() {
    try {
        const response = await fetch('obtener-todos-productos.php');
        const data = await response.json();
        
        if (data.success) {
            mostrarProductosAdmin(data.productos);
        } else {
            mostrarError('Error al cargar productos: ' + data.error);
        }
    } catch (error) {
        console.error('Error:', error);
        mostrarError('Error al cargar productos');
    }
}

function mostrarProductosAdmin(productos) {
    const contenedor = document.querySelector('#productos-tabla');
    
    if (!contenedor) {
        console.error('No se encontró el contenedor #productos-tabla');
        return;
    }
    
    if (productos.length === 0) {
        contenedor.innerHTML = '<p style="text-align: center; padding: 2rem; color: #999;">No hay productos</p>';
        return;
    }
    
    let html = `
        <table class="tabla-productos">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Categoría</th>
                    <th>Precio</th>
                    <th>Descripción</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
    `;
    
    productos.forEach(producto => {
        html += `
            <tr>
                <td>${producto.id_producto}</td>
                <td>${escapeHtml(producto.nombre_producto)}</td>
                <td>${escapeHtml(producto.categoria)}</td>
                <td>$${parseFloat(producto.precio).toFixed(2)}</td>
                <td>${escapeHtml(producto.descripcion.substring(0, 50))}...</td>
                <td>
                    <button class="btn-accion btn-editar" onclick="editarProducto(${producto.id_producto})">Editar</button>
                    <button class="btn-accion btn-eliminar" onclick="eliminarProducto(${producto.id_producto}, '${escapeHtml(producto.nombre_producto)}')">Eliminar</button>
                </td>
            </tr>
        `;
    });
    
    html += `
            </tbody>
        </table>
    `;
    
    contenedor.innerHTML = html;
}

function editarProducto(id) {
    window.location.href = `editar-productos.html?id=${id}`;
}

async function eliminarProducto(id, nombre) {
    if (!confirm(`¿Estás seguro de que deseas eliminar el producto "${nombre}"?`)) {
        return;
    }
    
    const formData = new FormData();
    formData.append('id_producto', id);
    
    try {
        const response = await fetch('eliminar-productos.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            mostrarExito('Producto eliminado exitosamente');
            cargarProductosAdmin();
        } else {
            mostrarError('Error: ' + data.error);
        }
    } catch (error) {
        console.error('Error:', error);
        mostrarError('Error al eliminar producto');
    }
}

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = String(text);
    return div.innerHTML;
}

function mostrarExito(mensaje) {
    const notificacion = document.createElement('div');
    notificacion.className = 'notificacion-exito';
    notificacion.textContent = mensaje;
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
    document.body.appendChild(notificacion);
    
    setTimeout(() => {
        notificacion.remove();
    }, 3000);
}

function mostrarError(mensaje) {
    const notificacion = document.createElement('div');
    notificacion.className = 'notificacion-error';
    notificacion.textContent = mensaje;
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
    document.body.appendChild(notificacion);
    
    setTimeout(() => {
        notificacion.remove();
    }, 5000);
}
