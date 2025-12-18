document.addEventListener('DOMContentLoaded', function() {
    const formularioCita = document.getElementById('formulario-cita');
    const alertaSesion = document.getElementById('alerta-sesion');
    const inputFecha = document.getElementById('fecha');
    

    function verificarSesion() {
        
        fetch('verificar-sesion.php')
            .then(response => response.json())
            .then(data => {
                if (data.logueado) {
                  
                    formularioCita.style.display = 'block';
                    alertaSesion.style.display = 'none';
                    
                    if (document.getElementById('nombre') && data.usuario_nombre) {
                        document.getElementById('nombre').value = data.usuario_nombre;
                    }
                } else {
                    
                    formularioCita.style.display = 'none';
                    alertaSesion.style.display = 'block';
                }
            })
            .catch(error => {
                console.error('Error verificando sesión:', error);
                
                formularioCita.style.display = 'none';
                alertaSesion.style.display = 'block';
            });
    }
    
    // Preseleccionar servicio si viene desde servicios.html
    function preseleccionarServicio() {
        const params = new URLSearchParams(window.location.search);
        const servicio = params.get('servicio');
        
        if (servicio) {
            const checkbox = document.querySelector(`input[name="servicio"][value="${servicio}"]`);
            if (checkbox) {
                checkbox.checked = true;
            }
        }
    }
    
    // Manejar envío del formulario
    if (formularioCita) {
        formularioCita.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Recopilar datos del formulario
            const formData = new FormData(formularioCita);
            
            // Enviar a citas.php
            fetch('citas.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('✅ ' + data.message);
                    // Redirigir a mis citas
                    window.location.href = 'mis-citas.html';
                } else {
                    alert('❌ ' + (data.error || 'Error al agendar la cita'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('❌ Error al conectar con el servidor');
            });
        });
    }
    
    // Validar sesión
    verificarSesion();
    preseleccionarServicio();
    
 
    document.addEventListener('auth:login', function() {
        verificarSesion();
    });
    
    document.addEventListener('auth:logout', function() {
        verificarSesion();
    });
});
