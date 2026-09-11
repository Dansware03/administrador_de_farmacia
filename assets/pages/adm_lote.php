<?php session_start(); if ($_SESSION['us_tipo']==1||$_SESSION['us_tipo']==3) { include_once 'layouts/header.php'; ?>
<title><?php echo htmlspecialchars($_SESSION['nombre_us']); ?> | Gestión de Lotes</title>
<?php include_once 'layouts/nav.php'; ?>

<!-- Modal Editar Stock de Lote -->
<div class="modal fade" id="editarlote" tabindex="-1" aria-labelledby="editarLoteLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title fw-bold" id="editarLoteLabel">
          <i class="bi bi-pencil-square me-2"></i>Ajustar Stock de Lote
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <form id="form-editar-lote">
        <div class="modal-body p-4">
          <div class="p-3 bg-light rounded-3 mb-3">
            <small class="text-muted d-block">Identificador de Lote:</small>
            <strong class="text-primary fs-6" id="codigo_lote">#</strong>
          </div>

          <div class="mb-3">
            <label for="stock" class="form-label fw-semibold small text-secondary">Cantidad en Stock <span class="text-danger">*</span></label>
            <input id="stock" type="number" min="0" class="form-control form-control-lg" placeholder="0" required>
            <small class="text-muted">Ajuste manual de existencias físicas del lote.</small>
          </div>
          <input type="hidden" id="id_lote_prod">
        </div>
        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary fw-semibold">
            <i class="bi bi-check-lg me-1"></i>Actualizar Lote
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Content Wrapper -->
<div class="content-wrapper">
  <!-- Content Header -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2 mb-2">
        <div>
          <h1 class="h3 fw-bold mb-1 text-primary">Control y Trazabilidad de Lotes</h1>
          <p class="text-muted small mb-0">Seguimiento de fechas de expiración, existencias y proveedores</p>
        </div>
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb mb-0 small">
            <li class="breadcrumb-item"><a href="adm_catalogo.php" class="text-decoration-none">Inicio</a></li>
            <li class="breadcrumb-item active" aria-current="page">Gestión de Lotes</li>
          </ol>
        </nav>
      </div>
    </div>
  </section>

  <!-- Main content -->
  <section>
    <div class="container-fluid">
      <!-- Buscador -->
      <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
          <div class="input-group input-group-lg">
            <span class="input-group-text bg-light border-end-0 text-muted">
              <i class="bi bi-search"></i>
            </span>
            <input type="text" id="buscar_lotes" class="form-control border-start-0 bg-light" placeholder="Buscar por código de lote, nombre de producto o proveedor...">
          </div>
        </div>
      </div>

      <!-- Grid de Lotes -->
      <div id="lotes" class="row g-3 g-md-4 align-items-stretch"></div>
    </div>
  </section>
</div>
<!-- /.content-wrapper -->

<?php include_once 'layouts/footer.php'; } else { header('Location: ../../index.php'); } ?>
<script src="../libs/js/lote.js"></script>