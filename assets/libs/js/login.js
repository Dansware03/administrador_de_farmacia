/**
 * SIMAP - Lógica de Interfaz para Pantalla de Autenticación
 *
 * Administra el comportamiento interactivo del formulario de acceso:
 * - Alternar visibilidad de contraseña con icono reactivo.
 * - Feedback visual y deshabilitación del botón al procesar envío para evitar envíos múltiples.
 * - Detección y despliegue del mensaje de error tras redirección con parámetro `?login_error=1`.
 *
 * @package SIMAP\Frontend\JS
 * @author Grupo de Proyecto
 * @version 1.0.0
 */

document.addEventListener('DOMContentLoaded', () => {
    // Alternar visibilidad de contraseña
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

    // Bloqueo y animación del botón durante el envío de credenciales
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

    // Alerta de credenciales inválidas si la URL incluye parámetro de error
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('login_error') === '1') {
        const errorAlert = document.getElementById('login-alert-error');
        if (errorAlert) {
            errorAlert.classList.remove('d-none');
        }
    }
});
