<footer class="main-footer">
    <div class="float-right d-none d-sm-block">
      <b>Version</b> 0.0.34
    </div>
    <strong>Copyright &copy; 2023 <a href="#">Omaira Rico</a>.</strong> All rights
    reserved.
  </footer>
</div>
<!-- Creditos Orginales -->
<!-- <footer class="main-footer">
    <div class="float-right d-none d-sm-block">
      <b>Version</b> 3.0.4
    </div>
    <strong>Copyright &copy; 2014-2019 <a href="http://adminlte.io">AdminLTE.io</a>.</strong> All rights
    reserved.
  </footer>
</div> -->
<!-- ./wrapper -->
<!-- jQuery -->
<script src="../libs/js/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="../libs/js/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- Incluye Toastr JS después de jQuery -->
<script src="../libs/js/toastr.js"></script>
<!-- SweetAlert2 -->
<script src="../libs/js/sweetalert2.js"></script>
<!-- SELECT2 -->
<script src="../libs/js/select2.js"></script>
<!-- SELECT2 ESPAÑOL -->
<script src="../libs/js/select2es.js"></script>
<!-- AdminLTE App -->
<script src="../libs/js/adminlte.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="../libs/js/demo.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  const logoutButton = document.querySelector('a.btn.btn-danger[href="../controller/Logout.php"]');
  if (logoutButton) {
    logoutButton.addEventListener('click', function(event) {
      event.preventDefault();
      const Toast = Swal.mixin({
        toast: true,
        position: "top-end",
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
          toast.onmouseenter = Swal.stopTimer;
          toast.onmouseleave = Swal.resumeTimer;
        }
      });
      Toast.fire({
        icon: "success",
        title: "Se ha cerrado la sesión"
      });
      setTimeout(function() {
        window.location.href = logoutButton.href;
      }, 1500);
    });
  }
  var navLinks = document.querySelectorAll('.update');
    navLinks.forEach(function (link) {
      link.addEventListener('click', function (event) {
        event.preventDefault();
        Swal.fire({
          icon: 'info',
          title: 'En mantenimiento',
          text: 'Disculpe las molestias, esta función está en mantenimiento.',
        });
      });
    });
});
</script>
</body>
</html>