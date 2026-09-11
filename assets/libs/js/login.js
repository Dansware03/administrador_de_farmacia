/**
 * SIMAP - Logica de Interfaz para Pantalla de Login
 */

document.addEventListener('DOMContentLoaded', () => {
    // Alternar visibilidad de contrasena
    const togglePassBtn = document.getElementById('toggle-password');
    const passInput = document.getElementById('login-pass');
    const toggleIcon = document.getElementById('toggle-icon');

    if (togglePassBtn && passInput && toggleIcon) {
        togglePassBtn.addEventListener('click', () => {
            const isPassword = passInput.getAttribute('type') === 'password';
            passInput.setAttribute('type', isPassword ? 'text' : 'password');
            toggleIcon.className = isPassword ? 'bi bi-eye-slash' : 'bi bi-eye';
            passInput.focus();
        });
    }

    // Feedback visual al enviar formulario
    const loginForm = document.getElementById('login-form');
    const submitBtn = document.getElementById('btn-submit');

    if (loginForm && submitBtn) {
        loginForm.addEventListener('submit', () => {
            submitBtn.disabled = true;
            submitBtn.innerHTML = `
                <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                Iniciando sesión...
            `;
        });
    }

    // Deteccion de error de autenticacion por URL (?login_error=1)
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('login_error') === '1') {
        const errorAlert = document.getElementById('login-alert-error');
        if (errorAlert) {
            errorAlert.classList.remove('d-none');
        }
    }
});
