<?php session_start(); if ($_SESSION['us_tipo']==1||$_SESSION['us_tipo']==3) { include_once 'layouts/header.php'; ?>
<title><?php echo htmlspecialchars($_SESSION['nombre_us']); ?> | Procesar Entrega de Insumos</title>
<?php include_once 'layouts/nav.php'; ?>

  <!-- Content Wrapper -->
  <div class="content-wrapper">
    <!-- Content Header -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2 mb-2">
          <div>
            <h1 class="h3 fw-bold mb-1 text-primary">Procesar Entrega / Solicitud</h1>
            <p class="text-muted small mb-0">Comprobante y registro de salida de insumos del depósito SIMAP</p>
          </div>
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
              <li class="breadcrumb-item"><a href="adm_catalogo.php" class="text-decoration-none">Inicio</a></li>
              <li class="breadcrumb-item active" aria-current="page">Procesar Pedido</li>
            </ol>
          </nav>
        </div>
      </div>
    </section>

    <!-- Main content -->
    <section>
      <div class="container-fluid">
        <div class="row g-4">
          <!-- Columna Principal: Datos y Tabla de Productos -->
          <div class="col-lg-8">
            <!-- Datos del Solicitante / Área -->
            <div class="card border-0 shadow-sm mb-4">
              <div class="card-header bg-primary bg-opacity-10 py-3">
                <h5 class="card-title fw-bold text-primary mb-0">
                  <i class="bi bi-person-lines-fill me-2"></i>Datos del Receptor / Área Solicitante
                </h5>
              </div>
              <div class="card-body p-4">
                <div class="row g-3">
                  <div class="col-md-6">
                    <label for="cliente" class="form-label fw-semibold small text-secondary">
                      Nombre del Solicitante o Departamento <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                      <span class="input-group-text"><i class="bi bi-person"></i></span>
                      <input type="text" class="form-control" id="cliente" placeholder="Ej: Mantenimiento General / Juan Pérez" required>
                    </div>
                  </div>

                  <div class="col-md-6">
                    <label for="ci" class="form-label fw-semibold small text-secondary">
                      Cédula / Identificación <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                      <span class="input-group-text"><i class="bi bi-person-vcard"></i></span>
                      <input type="number" class="form-control" id="ci" placeholder="Ej: 12345678" required>
                    </div>
                  </div>

                  <div class="col-12">
                    <div class="p-3 bg-light rounded-3 d-flex align-items-center justify-content-between">
                      <span class="small text-muted">
                        <i class="bi bi-shield-check text-success me-1"></i>Responsable de Entrega (Sesión):
                      </span>
                      <span class="fw-bold text-dark"><?php echo htmlspecialchars($_SESSION['nombre_us']); ?></span>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Tabla de Ítems Solicitados -->
            <div class="card border-0 shadow-sm mb-4">
              <div class="card-header bg-light py-3 d-flex align-items-center justify-content-between">
                <h5 class="card-title fw-bold text-dark mb-0">
                  <i class="bi bi-boxes me-2 text-primary"></i>Insumos y Materiales Seleccionados
                </h5>
              </div>
              <div class="card-body p-0 table-responsive" id="cp">
                <table class="table table-hover align-middle mb-0">
                  <thead class="table-light small">
                    <tr>
                      <th class="ps-3">Insumo</th>
                      <th>Stock Disp.</th>
                      <th>Precio Unit.</th>
                      <th>Concentración</th>
                      <th>Cantidad</th>
                      <th>Subtotal</th>
                      <th class="text-center">Quitar</th>
                    </tr>
                  </thead>
                  <tbody id="lista-compra"></tbody>
                </table>
              </div>
            </div>

            <!-- Botón Volver -->
            <div class="mb-4">
              <a href="adm_catalogo.php" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Volver al Catálogo
              </a>
            </div>
          </div>

          <!-- Columna Lateral: Resumen de Totales y Confirmación -->
          <div class="col-lg-4">
            <div class="card border-0 shadow-sm sticky-top" style="top: 80px;">
              <div class="card-header bg-primary text-white py-3">
                <h5 class="card-title fw-bold text-white mb-0">
                  <i class="bi bi-receipt me-2"></i>Resumen de Solicitud
                </h5>
              </div>
              <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <span class="text-muted">Subtotal Base</span>
                  <span class="fw-semibold text-dark" id="subtotal">$0.00</span>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-2">
                  <span class="text-muted">IVA (8%)</span>
                  <span class="fw-semibold text-dark" id="conIva">$0.00</span>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-3">
                  <span class="text-muted">Total sin Descuento</span>
                  <span class="fw-semibold text-dark" id="total_sin_descuento">$0.00</span>
                </div>

                <div class="mb-3">
                  <label for="descuento" class="form-label small text-secondary fw-semibold">Descuento ($)</label>
                  <div class="input-group input-group-sm">
                    <span class="input-group-text"><i class="bi bi-percent"></i></span>
                    <input id="descuento" type="number" min="0" value="0" step="0.01" class="form-control" placeholder="0.00">
                  </div>
                </div>

                <hr class="my-3">

                <div class="d-flex justify-content-between align-items-center mb-4">
                  <span class="fs-5 fw-bold text-primary">TOTAL</span>
                  <span class="fs-4 fw-bold text-primary" id="total">$0.00</span>
                </div>

                <div class="mb-3">
                  <label for="pago" class="form-label small text-secondary fw-semibold">Monto Recibido / Asignado ($)</label>
                  <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-cash-stack"></i></span>
                    <input type="number" id="pago" min="0" step="0.01" class="form-control" placeholder="0.00">
                  </div>
                </div>

                <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded-3 mb-4">
                  <span class="fw-semibold text-secondary">Cambio / Restante:</span>
                  <span class="fw-bold fs-5 text-success" id="vuelto">$0.00</span>
                </div>

                <div class="d-grid gap-2">
                  <button type="button" class="btn btn-primary btn-lg shadow-sm fw-semibold" id="procesar_compra">
                    <i class="bi bi-check2-circle me-1"></i>Confirmar Entrega
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
  <!-- /.content-wrapper -->

<?php include_once 'layouts/footer.php'; } else { header('Location: ../../index.php'); } ?>
<script src="../libs/js/catalogo.js"></script>
<script src="../libs/js/carrito.js"></script>
