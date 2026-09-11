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
  // Desvanecimiento de Page Loader Global
  const loader = document.getElementById('simap-page-loader');
  if (loader) {
    window.addEventListener('load', function() {
      loader.classList.add('hidden');
    });
    // Fallback de seguridad si load ya ocurrió o tarda
    setTimeout(function() {
      loader.classList.add('hidden');
    }, 400);
  }

  // Toggle colapso del sidebar en pantalla desktop
  const desktopToggle = document.getElementById('sidebar-toggle-desktop');
  if (desktopToggle) {
    desktopToggle.addEventListener('click', function(e) {
      e.preventDefault();
      document.body.classList.toggle('sidebar-collapsed');
    });
  }

  // Confirmación estética de Cierre de Sesión con SweetAlert2
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

  // Delegación para elementos marcados con clase .update (funciones en mantenimiento)
  document.querySelectorAll('.update').forEach(function(link) {
    link.addEventListener('click', function(e) {
      e.preventDefault();
      Swal.fire({
        icon: 'info',
        title: 'Módulo en proceso',
        text: 'Esta funcionalidad se encuentra en adecuación.',
        confirmButtonColor: '#1a3a5c'
      });
    });
  });
});
</script>
</body>
</html>