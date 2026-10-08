<?php
/**
 * SIMAP - Pie de Página y Scripts Fundamentales del Shell
 *
 * Cierra la estructura del layout institucional:
 * - Pie de página (Footer) con año actual y versión del sistema.
 * - Inclusión de scripts fundamentales locales (100% Offline: jQuery, Bootstrap, SweetAlert2, Toastr, Select2, DataTables).
 * - Lógica del Shell:
 *   * Ocultamiento controlado del Page Loader overlay.
 *   * Colapso interactivo de la barra lateral en desktop.
 *   * Diálogo modal de confirmación de salida con SweetAlert2 y purga de caché local (`localStorage.removeItem('productos')`).
 *
 * @package SIMAP\Layouts
 * @author Grupo de Proyecto
 * @version 1.0.0
 */
?>
  <!-- Footer Base SIMAP -->
  <footer class="simap-main-footer d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2">
    <div>
      <strong>SIMAP &copy; <?php echo date('Y'); ?></strong> 
      <span class="text-secondary">- Clínica Popular Especializada La Fría</span>
    </div>
    <div class="small">
      <span class="badge bg-light text-dark border">v1.0.0</span>
      <span class="text-muted ms-1">Mantenimiento y Protección</span>
    </div>
  </footer>
</div>
<!-- ./wrapper -->

<!-- Scripts Fundamentales (100% Offline) -->
<script src="../libs/js/jquery.min.js"></script>
<script src="../libs/js/bootstrap.bundle.min.js"></script>
<script src="../libs/js/sweetalert2.all.min.js"></script>
<script src="../libs/js/toastr.min.js"></script>
<script src="../libs/js/select2.min.js"></script>
<script src="../libs/js/select2.es.min.js"></script>
<script src="../libs/js/datatables.min.js"></script>

<!-- Lógica del Shell SIMAP -->
<script>
document.addEventListener('DOMContentLoaded', function() {
  // Ocultar preloader de página
  const loader = document.getElementById('simap-page-loader');
  if (loader) {
    window.addEventListener('load', function() {
      loader.classList.add('hidden');
    });
    setTimeout(function() {
      loader.classList.add('hidden');
    }, 400);
  }

  // Alternar colapso de barra lateral en pantalla grande
  const desktopToggle = document.getElementById('sidebar-toggle-desktop');
  if (desktopToggle) {
    desktopToggle.addEventListener('click', function(e) {
      e.preventDefault();
      document.body.classList.toggle('sidebar-collapsed');
    });
  }

  // Confirmación de cierre de sesión y purga de almacenamiento local
  const logoutBtn = document.getElementById('btn-logout');
  if (logoutBtn) {
    logoutBtn.addEventListener('click', function(e) {
      e.preventDefault();
      const targetUrl = this.href;

      Swal.fire({
        title: '¿Cerrar sesión?',
        text: 'Saldrá de su sesión actual en el sistema SIMAP.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#1a3a5c',
        cancelButtonColor: '#64748b',
        confirmButtonText: '<i class="bi bi-box-arrow-right me-1"></i> Sí, salir',
        cancelButtonText: 'Cancelar'
      }).then((result) => {
        if (result.isConfirmed) {
          localStorage.removeItem('productos');

          Swal.fire({
            title: 'Sesión finalizada',
            text: 'Redirigiendo...',
            icon: 'success',
            timer: 1200,
            showConfirmButton: false
          });
          setTimeout(() => {
            window.location.href = targetUrl;
          }, 1100);
        }
      });
    });
  }
});
</script>
<?php if (isset($page_scripts) && !empty($page_scripts)) { echo $page_scripts; } ?>
</body>
</html>