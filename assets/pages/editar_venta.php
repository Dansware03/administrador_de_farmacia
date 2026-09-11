<?php session_start(); if ($_SESSION['us_tipo']==1||$_SESSION['us_tipo']==3) { include_once 'layouts/header.php'; ?>
<title><?php echo htmlspecialchars($_SESSION['nombre_us']); ?> | Modificar Solicitud</title>
<?php include_once 'layouts/nav.php'; $id_venta = $_GET['id'] ?? ''; ?>

<!-- Modal Editar Cantidad -->
<div class="modal fade" id="modal_editar_cantidad" tabindex="-1" aria-labelledby="modalEditarCantidadLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title fw-bold" id="modalEditarCantidadLabel">
          <i class="bi bi-pencil-square me-2"></i>Modificar Cantidad Suministrada
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body p-4">
        <div class="mb-3">
          <label for="producto_editar" class="form-label small fw-semibold text-secondary">Insumo:</label>
          <input type="text" class="form-control bg-light" id="producto_editar" readonly>
          <input type="hidden" id="id_detalle_editar">
        </div>

        <div class="row g-2 mb-3">
          <div class="col-6">
            <label for="stock_disponible" class="form-label small fw-semibold text-secondary">Stock Disp.:</label>
            <input type="number" class="form-control bg-light" id="stock_disponible" readonly>
          </div>
          <div class="col-6">
            <label for="cantidad_actual" class="form-label small fw-semibold text-secondary">Cant. Actual:</label>
            <input type="number" class="form-control bg-light" id="cantidad_actual" readonly>
          </div>
        </div>

        <div class="mb-2">
          <label for="nueva_cantidad" class="form-label small fw-semibold text-primary">Nueva Cantidad Solicitada <span class="text-danger">*</span></label>
          <input type="number" class="form-control form-control-lg" id="nueva_cantidad" min="1" required>
        </div>
      </div>
      <div class="modal-footer bg-light justify-content-between">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary fw-semibold" id="btn_guardar_cantidad">
          <i class="bi bi-check-lg me-1"></i>Actualizar Cantidad
        </button>
      </div>
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
          <h1 class="h3 fw-bold mb-1 text-primary">Modificar Registro de Entrega #<?php echo htmlspecialchars($id_venta); ?></h1>
          <p class="text-muted small mb-0">Corrección de cantidades e información de receptor</p>
        </div>
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb mb-0 small">
            <li class="breadcrumb-item"><a href="adm_catalogo.php" class="text-decoration-none">Inicio</a></li>
            <li class="breadcrumb-item"><a href="adm_retiro_ventas.php" class="text-decoration-none">Historial</a></li>
            <li class="breadcrumb-item active" aria-current="page">Editar</li>
          </ol>
        </nav>
      </div>
    </div>
  </section>

  <!-- Main content -->
  <section>
    <div class="container-fluid">
      <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
          <div class="alert alert-info d-flex align-items-center mb-4" role="alert">
            <i class="bi bi-info-circle-fill fs-4 me-3"></i>
            <div>
              <strong>Aviso de Inventario:</strong> Al modificar las cantidades de esta entrega, las existencias en el depósito se recalcularán automáticamente en el sistema.
            </div>
          </div>

          <!-- Datos de la solicitud -->
          <div class="row g-3 mb-4 p-3 bg-light rounded-3 border">
            <div class="col-md-6">
              <label for="cliente" class="form-label small fw-semibold text-secondary">Nombre del Solicitante / Área:</label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-person"></i></span>
                <input type="text" class="form-control" id="cliente" name="cliente">
              </div>
            </div>
            <div class="col-md-6">
              <label for="ci" class="form-label small fw-semibold text-secondary">Cédula / Identificador:</label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-person-vcard"></i></span>
                <input type="text" class="form-control" id="ci" name="ci">
              </div>
            </div>
          </div>

          <!-- Tabla de productos -->
          <div class="table-responsive border rounded-3 mb-4">
            <table class="table table-hover align-middle mb-0" id="tabla_detalle_venta">
              <thead class="table-light small">
                <tr>
                  <th>Insumo / Material</th>
                  <th>Lote</th>
                  <th>Vencimiento</th>
                  <th>Costo Ref.</th>
                  <th>Cantidad</th>
                  <th>Subtotal</th>
                  <th class="text-center">Acciones</th>
                </tr>
              </thead>
              <tbody></tbody>
              <tfoot class="table-light">
                <tr>
                  <th colspan="5" class="text-end fw-bold">Total Asignado:</th>
                  <th id="total_venta" class="fw-bold text-primary fs-6">0.00</th>
                  <th></th>
                </tr>
              </tfoot>
            </table>
          </div>

          <div class="d-flex justify-content-between align-items-center">
            <a href="../pages/adm_retiro_ventas.php" class="btn btn-outline-secondary">
              <i class="bi bi-arrow-left me-1"></i>Volver al Historial
            </a>
            <button id="btn_actualizar_venta" class="btn btn-primary fw-semibold px-4 shadow-sm">
              <i class="bi bi-save me-1"></i>Guardar Cambios
            </button>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
<!-- /.content-wrapper -->

<?php include_once 'layouts/footer.php'; } else { header('Location: ../../index.php'); } ?>
<script src="../libs/js/editar_venta.js"></script>