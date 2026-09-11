<?php session_start(); if ($_SESSION['us_tipo']==1||$_SESSION['us_tipo']==3) { include_once 'layouts/header.php'; ?>
<title><?php echo htmlspecialchars($_SESSION['nombre_us']); ?> | Historial de Salidas</title>
<?php include_once 'layouts/nav.php'; ?>

<!-- Modal Detalles de Solicitud/Salida -->
<div class="modal fade" id="vista_venta" tabindex="-1" aria-labelledby="vistaVentaLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title fw-bold" id="vistaVentaLabel">
          <i class="bi bi-receipt me-2"></i>Comprobante de Entrega de Insumos
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body p-4">
        <div class="row g-3 p-3 bg-light rounded-3 mb-4">
          <div class="col-md-6">
            <small class="text-muted d-block">Receptor / Área:</small>
            <strong class="fs-6 text-dark" id="cliente_detalle">---</strong>
          </div>
          <div class="col-md-6">
            <small class="text-muted d-block">Cédula / Identificador:</small>
            <strong class="fs-6 text-dark" id="ci_detalle">---</strong>
          </div>
          <div class="col-md-6">
            <small class="text-muted d-block">Fecha y Hora de Salida:</small>
            <strong class="text-secondary" id="fecha_detalle">---</strong>
          </div>
          <div class="col-md-6">
            <small class="text-muted d-block">Responsable de Entrega:</small>
            <strong class="text-secondary" id="vendedor_detalle">---</strong>
          </div>
        </div>

        <h6 class="fw-bold text-primary mb-2"><i class="bi bi-box-seam me-1"></i>Materiales Suministrados:</h6>
        <div class="table-responsive border rounded-3">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light small">
              <tr>
                <th>Insumo</th>
                <th>Cantidad</th>
                <th>Costo Ref.</th>
                <th>Subtotal</th>
                <th>Lote</th>
                <th>Vencimiento</th>
              </tr>
            </thead>
            <tbody id="detalles_venta"></tbody>
            <tfoot class="table-light">
              <tr>
                <th colspan="3" class="text-end fw-bold">Total Asignado:</th>
                <th id="total_detalle" colspan="3" class="fw-bold text-primary fs-6"></th>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
      <div class="modal-footer bg-light justify-content-between">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cerrar</button>
        <button type="button" class="btn btn-primary fw-semibold" id="btn_imprimir">
          <i class="bi bi-printer me-1"></i>Imprimir Recibo
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Confirmar Anulación -->
<div class="modal fade" id="confirmar_revertir" tabindex="-1" aria-labelledby="revertirLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title fw-bold" id="revertirLabel">
          <i class="bi bi-exclamation-octagon me-2"></i>Confirmar Anulación
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body p-4 text-center">
        <i class="bi bi-arrow-counterclockwise text-danger display-4 mb-3 d-block"></i>
        <h5 class="fw-bold text-dark">¿Anular esta entrega de insumos?</h5>
        <p class="text-muted small mb-0">
          Esta acción cancelará el registro de salida y devolverá automáticamente los productos a las existencias físicas del inventario.
        </p>
      </div>
      <div class="modal-footer bg-light justify-content-between">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-danger fw-semibold" id="btn_confirmar_revertir">
          <i class="bi bi-check-circle me-1"></i>Sí, Anular y Reintegrar
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
          <h1 class="h3 fw-bold mb-1 text-primary">Historial de Salidas y Entregas</h1>
          <p class="text-muted small mb-0">Registro histórico de insumos entregados a departamentos y personal</p>
        </div>
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb mb-0 small">
            <li class="breadcrumb-item"><a href="adm_catalogo.php" class="text-decoration-none">Inicio</a></li>
            <li class="breadcrumb-item active" aria-current="page">Salidas y Entregas</li>
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
          <!-- Filtros de Fecha -->
          <div class="row g-3 align-items-end mb-4 p-3 bg-light rounded-3 border">
            <div class="col-md-4">
              <label for="fecha_inicio" class="form-label small fw-semibold text-secondary">Fecha Inicial</label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-calendar-event"></i></span>
                <input type="date" class="form-control" id="fecha_inicio">
              </div>
            </div>

            <div class="col-md-4">
              <label for="fecha_fin" class="form-label small fw-semibold text-secondary">Fecha Final</label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-calendar-event"></i></span>
                <input type="date" class="form-control" id="fecha_fin">
              </div>
            </div>

            <div class="col-md-4 d-flex gap-2">
              <button id="btn_filtrar" class="btn btn-primary flex-grow-1 fw-semibold">
                <i class="bi bi-funnel me-1"></i>Filtrar
              </button>
              <button id="btn_limpiar" class="btn btn-outline-secondary flex-grow-1 fw-semibold">
                <i class="bi bi-arrow-counterclockwise me-1"></i>Limpiar
              </button>
            </div>
          </div>

          <!-- Tabla con DataTables -->
          <div class="table-responsive">
            <table id="tabla_ventas" class="table table-hover align-middle w-100">
              <thead class="table-light small">
                <tr>
                  <th>N°</th>
                  <th>Fecha y Hora</th>
                  <th>Receptor / Área</th>
                  <th>Cédula / Ident.</th>
                  <th>Total Ref.</th>
                  <th>Responsable</th>
                  <th class="text-center">Acciones</th>
                </tr>
              </thead>
              <tbody></tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
<!-- /.content-wrapper -->

<?php include_once 'layouts/footer.php'; } else { header('Location: ../../index.php'); } ?>
<script src="../libs/js/retiro_ventas.js"></script>