document.addEventListener('DOMContentLoaded', function() {
  // Crear el modal si no existe
  if (!document.getElementById('auth-modal')) {
    const modalHtml = `
    <div id="auth-modal" class="auth-modal" style="display: none;">
      <div class="auth-backdrop"></div>
      <div class="auth-window">
        <button class="auth-close" type="button">×</button>
        <h3 id="auth-title">Iniciar sesión</h3>

        <div class="auth-tabs">
          <button class="tab-btn active" data-tab="login" type="button">Iniciar sesión</button>
          <button class="tab-btn" data-tab="register" type="button">Registrarse</button>
        </div>

        <div class="auth-body">
          <form id="login-form" class="auth-form" data-tab="login">
            <input type="hidden" name="accion" value="login">
            <div>
              <label for="login-correo">Correo electrónico</label>
              <input type="email" id="login-correo" name="correo" placeholder="Correo electrónico" required>
            </div>
            <div>
              <label for="login-contrasena">Contraseña</label>
              <input type="password" id="login-contrasena" name="contrasena" placeholder="Contraseña" required>
            </div>
            <button type="submit" class="boton primario">Iniciar sesión</button>
            <div class="auth-footer">
              <a href="#" class="auth-link">¿Olvidaste tu contraseña?</a>
              <a href="#" class="auth-link abrir-registrarse">Regístrate</a>
            </div>
          </form>

          <form id="register-form" class="auth-form" data-tab="register" style="display:none;">
            <input type="hidden" name="accion" value="register">
            <div>
              <label for="reg-nombres">Nombres completos</label>
              <input type="text" id="reg-nombres" name="nombres" placeholder="Nombres completos" required>
            </div>
            <div>
              <label for="reg-apellidopat">Apellido paterno</label>
              <input type="text" id="reg-apellidopat" name="apellidopat" placeholder="Apellido paterno" required>
            </div>
            <div>
              <label for="reg-apellidomat">Apellido materno</label>
              <input type="text" id="reg-apellidomat" name="apellidomat" placeholder="Apellido materno" required>
            </div>
            <div>
              <label for="reg-correo">Correo electrónico</label>
              <input type="email" id="reg-correo" name="correo" placeholder="Correo electrónico" required>
            </div>
            <div>
              <label for="reg-telefono">Teléfono</label>
              <input type="tel" id="reg-telefono" name="telefono" placeholder="Teléfono" required>
            </div>
            <div>
              <label for="reg-contrasena">Contraseña</label>
              <input type="password" id="reg-contrasena" name="contrasena" placeholder="Contraseña" required>
            </div>
            <div>
              <label for="reg-confirmar">Confirmar contraseña</label>
              <input type="password" id="reg-confirmar" name="confirmar_contrasena" placeholder="Confirmar contraseña" required>
            </div>
            <button type="submit" class="boton primario">Crear cuenta</button>
            <div class="auth-footer">
              <a href="#" class="auth-link abrir-inicio">¿Ya tienes cuenta? Inicia sesión</a>
            </div>
          </form>
        </div>
      </div>
    </div>
    `;
    document.body.insertAdjacentHTML('beforeend', modalHtml);
  }

  const modal = document.getElementById('auth-modal');
  const loginForm = modal.querySelector('#login-form');
  const registerForm = modal.querySelector('#register-form');
  const backdrop = modal.querySelector('.auth-backdrop');
  const closeBtn = modal.querySelector('.auth-close');
  const tabButtons = modal.querySelectorAll('.tab-btn');

  // Función para abrir el modal
  function openModal(tab = 'login') {
    modal.style.display = 'flex';
    setTimeout(() => modal.classList.add('open'), 10);
    showTab(tab);
  }

  // Función para cerrar el modal
  function closeModal() {
    modal.classList.remove('open');
    setTimeout(() => {
      modal.style.display = 'none';
    }, 300);
  }

  // Función para cambiar tab
  function showTab(name) {
    tabButtons.forEach(btn => {
      btn.classList.toggle('active', btn.dataset.tab === name);
    });
    modal.querySelectorAll('.auth-form').forEach(form => {
      form.style.display = form.dataset.tab === name ? '' : 'none';
    });
    modal.querySelector('#auth-title').textContent = 
      name === 'register' ? 'Crear cuenta' : 'Iniciar sesión';
  }

  // Abrir modal
  document.querySelectorAll('.abrir-auth, [data-abrir-auth]').forEach(btn => {
    btn.addEventListener('click', function(e) {
      e.preventDefault();
      fetch('verificar-sesion.php')
        .then(r => r.json())
        .then(data => {
          if (!data.logueado) {
            openModal('login');
          }
        })
        .catch(() => openModal('login'));
    });
  });

  // Cerrar modal
  backdrop.addEventListener('click', closeModal);
  closeBtn.addEventListener('click', closeModal);
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && modal.style.display === 'flex') closeModal();
  });

  // Cambiar tabs
  tabButtons.forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      showTab(btn.dataset.tab);
    });
  });

  // Links de tab
  modal.querySelectorAll('.abrir-registrarse').forEach(link => {
    link.addEventListener('click', (e) => {
      e.preventDefault();
      showTab('register');
    });
  });

  modal.querySelectorAll('.abrir-inicio').forEach(link => {
    link.addEventListener('click', (e) => {
      e.preventDefault();
      showTab('login');
    });
  });

  // Submit login
  loginForm.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('auth.php', {
      method: 'POST',
      body: formData
    })
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        closeModal();
        loginForm.reset();
        setTimeout(() => {
          window.location.href = 'index.html';
        }, 300);
      } else {
        alert(data.error || 'Error al iniciar sesión');
      }
    })
    .catch(error => {
      alert('Error: ' + error.message);
    });
  });

  // Submit registro
  registerForm.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('auth.php', {
      method: 'POST',
      body: formData
    })
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        closeModal();
        registerForm.reset();
        setTimeout(() => {
          window.location.href = 'index.html';
        }, 300);
      } else {
        if (Array.isArray(data.errores)) {
          alert(data.errores.join('\n'));
        } else {
          alert(data.error || 'Error al registrarse');
        }
      }
    })
    .catch(error => {
      alert('Error: ' + error.message);
    });
  });
});
