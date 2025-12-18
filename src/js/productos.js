document.addEventListener('DOMContentLoaded', function() {
    cargarProductos('todos');
    
    // Filtros de categorías
    const botonesFiltro = document.querySelectorAll('.filtro-btn');
    botonesFiltro.forEach(btn => {
        btn.addEventListener('click', function() {
            // Remover clase active de todos
            botonesFiltro.forEach(b => b.classList.remove('active'));
            // Agregar clase active al clickeado
            this.classList.add('active');
            
            const categoria = this.getAttribute('data-categoria');
            cargarProductos(categoria);
        });
    });
});

function cargarProductos(categoria) {
    const url = `obtener-productos.php?categoria=${encodeURIComponent(categoria)}`;
    
    fetch(url)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                mostrarProductos(data.productos, categoria);
            } else {
                console.error('Error:', data.error);
                mostrarMensajeError('Error al cargar productos');
            }
        })
        .catch(error => {
            console.error('Error al cargar productos:', error);
            mostrarMensajeError('Error al cargar productos. Verifica la consola.');
        });
}

function mostrarProductos(productos, categoria) {
    const gridProductos = document.querySelector('.productos-grid');
    
    if (!gridProductos) {
        console.error('No se encontró el elemento .productos-grid');
        return;
    }
    
    if (productos.length === 0) {
        gridProductos.innerHTML = '<p style="grid-column: 1/-1; text-align: center; padding: 2rem; color: #999;">No hay productos en esta categoría</p>';
        return;
    }
    
    gridProductos.innerHTML = '';
    
    productos.forEach(producto => {
        const productoCard = document.createElement('article');
        productoCard.className = 'producto-card';
        productoCard.setAttribute('data-categoria', producto.categoria.toLowerCase());
        
        const imagenUrl = producto.imagen_url && producto.imagen_url.trim() ? producto.imagen_url : 'src/img/productos/placeholder.jpg';
        
        productoCard.innerHTML = `
            <div class="producto-imagen">
                <img src="${escapeHtml(imagenUrl)}" alt="${escapeHtml(producto.nombre_producto)}" onerror="this.src='src/img/productos/placeholder.jpg'">
                <div class="producto-overlay">
                    <button class="btn-detalles" onclick="verDetalles(${producto.id_producto}, '${escapeHtml(producto.nombre_producto)}', ${parseFloat(producto.precio).toFixed(2)}, '${escapeHtml(producto.descripcion)}', '${escapeHtml(imagenUrl)}')">Ver Detalles</button>
                </div>
            </div>
            <div class="producto-info">
                <h3>${escapeHtml(producto.nombre_producto)}</h3>
                <p class="categoria-etiqueta">${escapeHtml(producto.categoria)}</p>
                <p class="precio">$${parseFloat(producto.precio).toFixed(2)}</p>
                <button class="btn-comprar" onclick="agregarAlCarrito(${producto.id_producto}, '${escapeHtml(producto.nombre_producto)}', ${parseFloat(producto.precio).toFixed(2)})">Agregar al Carrito</button>
            </div>
        `;
        
        gridProductos.appendChild(productoCard);
    });
}

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = String(text);
    return div.innerHTML;
}

function verDetalles(id, nombre, precio, descripcion, imagen) {
    console.log('Detalles del producto:', { id, nombre, precio, descripcion });
    
    // Crear un modal simple
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
            <img src="${imagen}" alt="${nombre}" style="width: 100%; max-height: 300px; object-fit: cover; border-radius: 4px; margin-bottom: 1rem;">
            <h2 style="color: #333; margin-bottom: 0.5rem;">${nombre}</h2>
            <p style="color: #d4af37; font-size: 1.5rem; font-weight: bold; margin-bottom: 1rem;">$${precio}</p>
            <p style="color: #666; line-height: 1.6; margin-bottom: 1.5rem;">${descripcion}</p>
            <div style="display: flex; gap: 1rem;">
                <button onclick="this.closest('div').parentElement.remove()" style="flex: 1; padding: 0.75rem; background: #d4af37; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: 600;">Cerrar</button>
                <button onclick="agregarAlCarrito(${id}, '${nombre}', ${precio}); this.closest('div').parentElement.remove();" style="flex: 1; padding: 0.75rem; background: #667eea; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: 600;">Agregar al Carrito</button>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // Cerrar al hacer clic fuera del modal
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.remove();
        }
    });
}

function agregarAlCarrito(id, nombre, precio) {
    console.log('Agregado al carrito:', { id, nombre, precio });
    mostrarMensaje(`${nombre} agregado al carrito`);
    
    // Aquí puedes agregar la lógica del carrito real
    // Por ahora solo muestra un mensaje
}

function mostrarMensaje(mensaje) {
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
        animation: slideIn 0.3s ease;
    `;
    document.body.appendChild(notificacion);
    
    // Agregar animación
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideIn {
            from {
                transform: translateX(400px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
    `;
    document.head.appendChild(style);
    
    setTimeout(() => {
        notificacion.style.cssText += 'opacity: 0; transition: opacity 0.3s ease;';
        setTimeout(() => {
            notificacion.remove();
        }, 300);
    }, 3000);
}

function mostrarMensajeError(mensaje) {
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
