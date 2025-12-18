document.addEventListener('DOMContentLoaded', function() {

    if (!document.getElementById('user-menu')) {
        const menuHtml = `
            <div id="user-menu" class="user-menu" style="display:none;">
                <div class="user-menu-header">
                    <span id="menu-usuario-nombre"></span>
                </div>
                <a href="admin.html" id="admin-btn" class="user-menu-item admin" style="display:none;">Panel Admin</a>
                <a href="#" id="logout-btn" class="user-menu-item logout">Cerrar sesión</a>
            </div>
        `;
        document.body.insertAdjacentHTML('beforeend', menuHtml);
    }
    

    fetch('verificar-sesion.php')
        .then(response => response.json())
        .then(data => {
            const avatarBtn = document.querySelector('.usuario.abrir-auth');
            const userMenu = document.getElementById('user-menu');
            const menuNombreTxt = document.getElementById('menu-usuario-nombre');
            const logoutBtn = document.getElementById('logout-btn');
            const adminBtn = document.getElementById('admin-btn');
            
            if (data.logueado) {
              
                if (avatarBtn) {
                    avatarBtn.innerHTML = '<span style="color: white; font-size: 14px;">👤</span>';
                    avatarBtn.classList.remove('abrir-auth');
                }
                
           
                if (userMenu && menuNombreTxt) {
                    menuNombreTxt.textContent = 'Hola, ' + data.usuario_nombre;
                    
                    // Mostrar botón de admin solo si es admin
                    if (adminBtn) {
                        adminBtn.style.display = data.es_admin ? 'block' : 'none';
                    }
                    
              
                    avatarBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        userMenu.style.display = userMenu.style.display === 'none' ? 'block' : 'none';
                    });
                    

                    logoutBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        if (confirm('¿Deseas cerrar sesión?')) {
                            window.location.href = 'logout.php';
                        }
                    });
                    
    
                    document.addEventListener('click', function(e) {
                        if (!e.target.closest('.usuario') && !e.target.closest('#user-menu')) {
                            userMenu.style.display = 'none';
                        }
                    });
                }
                
   
                const formularioCita = document.getElementById('formulario-cita');
                const alertaSesion = document.getElementById('alerta-sesion');
                if (formularioCita) {
                    formularioCita.style.display = 'block';
                }
                if (alertaSesion) {
                    alertaSesion.style.display = 'none';
                }
     
                const campoNombre = document.getElementById('nombre');
                if (campoNombre) {
                    campoNombre.value = data.usuario_nombre;
                }
            } else {
             
                if (avatarBtn) {
                    avatarBtn.innerHTML = '<img class="avatar" src="/src/img/avatar.png" alt="usuario">';
                    avatarBtn.classList.add('abrir-auth');
                }
                
    
                if (userMenu) {
                    userMenu.style.display = 'none';
                }
                
                // Ocultar botón de admin
                if (adminBtn) {
                    adminBtn.style.display = 'none';
                }

                const formularioCita = document.getElementById('formulario-cita');
                const alertaSesion = document.getElementById('alerta-sesion');
                if (formularioCita) {
                    formularioCita.style.display = 'none';
                }
                if (alertaSesion) {
                    alertaSesion.style.display = 'block';
                }
            }
        })
        .catch(error => {
            console.error('Error al verificar sesión:', error);
        });
});
