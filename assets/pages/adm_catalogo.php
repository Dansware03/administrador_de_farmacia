<?php session_start();
if ($_SESSION['us_tipo'] == 1) {
  include_once 'layouts/header.php'; ?>
  <title><?php echo htmlspecialchars($_SESSION['nombre_us']); ?> | Catálogo de Insumos</title>
  <?php include_once 'layouts/nav.php'; ?>

  <!-- Content Wrapper -->
  <div class="content-wrapper">
    <!-- Content Header -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2 mb-3">
          <div>
            <h1 class="h3 fw-bold mb-1 text-primary">Catálogo de Insumos y Materiales</h1>
            <p class="text-muted small mb-0">Gestión de existencias hospitalarias, protección y dotación de áreas</p>
          </div>
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
              <li class="breadcrumb-item"><a href="adm_catalogo.php" class="text-decoration-none">Inicio</a></li>
              <li class="breadcrumb-item active" aria-current="page">Catálogo</li>
            </ol>
          </nav>
        </div>

        <!-- KPI Cards Superiores -->
        <div class="row g-3 mb-4">
          <div class="col-12 col-sm-6 col-lg-4">
            <div class="simap-kpi-card p-3 d-flex align-items-center justify-content-between">
              <div>
                <span class="text-muted small text-uppercase fw-semibold d-block">Total de Insumos</span>
                <h3 class="fw-bold mb-0 text-dark" id="kpi-total-insumos">--</h3>
              </div>
              <div class="simap-kpi-icon bg-primary bg-opacity-10 text-primary">
                <i class="bi bi-boxes"></i>
              </div>
            </div>
          </div>
          <div class="col-12 col-sm-6 col-lg-4">
            <div class="simap-kpi-card p-3 d-flex align-items-center justify-content-between">
              <div>
                <span class="text-muted small text-uppercase fw-semibold d-block">Existencias Bajas</span>
                <h3 class="fw-bold mb-0 text-warning" id="kpi-stock-bajo">--</h3>
              </div>
              <div class="simap-kpi-icon bg-warning bg-opacity-10 text-warning">
                <i class="bi bi-exclamation-triangle"></i>
              </div>
            </div>
          </div>
          <div class="col-12 col-sm-6 col-lg-4">
            <div class="simap-kpi-card p-3 d-flex align-items-center justify-content-between">
              <div>
                <span class="text-muted small text-uppercase fw-semibold d-block">Lotes Críticos</span>
                <h3 class="fw-bold mb-0 text-danger" id="kpi-lotes-riesgo">--</h3>
              </div>
              <div class="simap-kpi-icon bg-danger bg-opacity-10 text-danger">
                <i class="bi bi-clock-history"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Main Buscador y Catálogo de Productos -->
    <section>
      <div class="container-fluid">
        <!-- Buscador y Filtros Rápidos -->
        <div class="card border-0 shadow-sm mb-4">
          <div class="card-body p-3">
            <div class="row g-2 align-items-center">
              <div class="col-12 col-md-6 col-lg-7">
                <div class="input-group">
                  <span class="input-group-text bg-light border-end-0 text-muted">
                    <i class="bi bi-search"></i>
                  </span>
                  <input type="text" id="buscar_producto" class="form-control border-start-0 bg-light"
                    placeholder="Buscar por nombre de insumo, material, código o categoría..."
                    aria-label="Buscar insumos">
                </div>
              </div>
              <div class="col-12 col-md-6 col-lg-5">
                <div class="d-flex flex-wrap gap-2 justify-content-md-end" id="filtros-chips">
                  <button type="button" class="simap-filter-chip active" data-filter="todos">Todos</button>
                  <button type="button" class="simap-filter-chip" data-filter="stock">En Stock</button>
                  <button type="button" class="simap-filter-chip" data-filter="bajo">Bajo Stock (&le;10)</button>
                  <button type="button" class="simap-filter-chip" data-filter="agotado">Agotados</button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Grid de Productos -->
        <div id="productos" class="row g-3 g-md-4 align-items-stretch">
          <!-- Los productos se inyectan dinámicamente vía catalogo.js -->
        </div>

        <!-- Barra de Paginación Moderna y Resumen -->
        <div
          class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3 mt-4 pt-3 border-top pb-4"
          id="contenedor-paginacion">
          <div class="d-flex align-items-center gap-3 text-muted small">
            <span id="info-paginacion">Cargando inventario...</span>
            <div class="d-flex align-items-center gap-1">
              <label for="items-por-pagina" class="mb-0 text-nowrap">Mostrar:</label>
              <select id="items-por-pagina" class="form-select form-select-sm py-1 pe-4" style="width: auto;">
                <option value="12" selected>12</option>
                <option value="24">24</option>
                <option value="48">48</option>
              </select>
            </div>
          </div>
          <nav aria-label="Paginación de insumos hospitalarios">
            <ul class="pagination pagination-sm mb-0 gap-1" id="paginacion-lista"></ul>
          </nav>
        </div>
      </div>
    </section>
  </div>
  <!-- /.content-wrapper -->

  <!-- Botón Flotante Rápido de Solicitud (FAB) -->
  <div class="position-fixed bottom-0 end-0 p-4" style="z-index: 1040;">
    <a href="adm_retiro.php" class="btn btn-primary rounded-pill shadow-lg d-flex align-items-center gap-2 px-3 py-2 border-2 border-white" title="Ir a Procesar Solicitud de Insumos">
      <i class="bi bi-cart-check fs-5"></i>
      <span class="fw-semibold d-none d-sm-inline">Ver Solicitud</span>
      <span class="badge bg-danger rounded-pill contador" id="fab-contador">0</span>
    </a>
  </div>

  <?php
  $page_scripts = '
<script src="' . asset_v('../libs/js/catalogo.js') . '"></script>
<script src="' . asset_v('../libs/js/carrito.js') . '"></script>
';
  include_once 'layouts/footer.php';
} else {
  header('Location: ../../index.php');
}
?>