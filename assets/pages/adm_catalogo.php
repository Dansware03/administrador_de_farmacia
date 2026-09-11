<?php session_start(); if ($_SESSION['us_tipo']==1||$_SESSION['us_tipo']==3) { include_once 'layouts/header.php'; ?>
<title><?php echo htmlspecialchars($_SESSION['nombre_us']); ?> | Catálogo de Insumos</title>
<?php include_once 'layouts/nav.php'; ?>

  <!-- Content Wrapper -->
  <div class="content-wrapper">
    <!-- Content Header -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2 mb-2">
          <div>
            <h1 class="h3 fw-bold mb-1 text-primary">Catálogo de Insumos y Materiales</h1>
            <p class="text-muted small mb-0">Gestión de existencias y solicitudes para mantenimiento y protección</p>
          </div>
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
              <li class="breadcrumb-item"><a href="adm_catalogo.php" class="text-decoration-none">Inicio</a></li>
              <li class="breadcrumb-item active" aria-current="page">Catálogo</li>
            </ol>
          </nav>
        </div>
      </div>
    </section>

    <!-- Main Lotes en Riesgo -->
    <section class="mb-4">
      <div class="container-fluid">
        <div class="card border-warning shadow-sm">
          <div class="card-header bg-warning bg-opacity-10 d-flex align-items-center justify-content-between py-2">
            <div class="d-flex align-items-center gap-2">
              <i class="bi bi-exclamation-triangle-fill text-warning fs-5"></i>
              <h5 class="card-title fw-bold text-dark mb-0">Lotes en Riesgo / Por Vencer</h5>
            </div>
            <button class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1" id="toggleLotesBtn" type="button" title="Plegar/Desplegar">
              <i class="bi bi-dash-lg"></i>
            </button>
          </div>
          <div class="card-body p-0 table-responsive" id="lotesSection">
            <table class="table table-hover align-middle mb-0">
              <thead class="table-light small">
                <tr>
                  <th class="ps-3">Código</th>
                  <th>Insumo / Material</th>
                  <th>Stock</th>
                  <th>Laboratorio</th>
                  <th>Presentación</th>
                  <th>Proveedor</th>
                  <th>Mes</th>
                  <th>Día</th>
                </tr>
              </thead>
              <tbody id="lotes"></tbody>
            </table>
          </div>
        </div>
      </div>
    </section>

    <!-- Main Buscador y Catálogo de Productos -->
    <section>
      <div class="container-fluid">
        <!-- Buscador -->
        <div class="card border-0 shadow-sm mb-4">
          <div class="card-body p-3">
            <div class="input-group input-group-lg">
              <span class="input-group-text bg-light border-end-0 text-muted">
                <i class="bi bi-search"></i>
              </span>
              <input type="text" id="buscar_producto" class="form-control border-start-0 bg-light" placeholder="Escribe el nombre del insumo o material para buscar en tiempo real...">
            </div>
          </div>
        </div>

        <!-- Grid de Productos -->
        <div id="productos" class="row g-3 g-md-4 align-items-stretch">
          <!-- Los productos se inyectan dinámicamente vía catalogo.js -->
        </div>
      </div>
    </section>
  </div>
  <!-- /.content-wrapper -->

<?php include_once 'layouts/footer.php'; } else { header('Location: ../../index.php'); } ?>
<script src="../libs/js/catalogo.js"></script>
<script src="../libs/js/carrito.js"></script>
<script>
  $(document).ready(function () {
    $("#toggleLotesBtn").click(function () {
      $("#lotesSection").slideToggle(200);
      $(this).find("i").toggleClass("bi-dash-lg bi-plus-lg");
    });
  });
</script>
