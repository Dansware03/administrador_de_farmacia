<?php session_start(); 
if (empty($_SESSION['us_tipo'])) {
    header('Location: error.php?code=401');
    exit();
}
if ($_SESSION['us_tipo'] != 1 && $_SESSION['us_tipo'] != 2) {
    header('Location: error.php?code=403');
    exit();
}
include_once 'layouts/header.php'; ?>
<title><?php echo htmlspecialchars($_SESSION['nombre_us']); ?> | Procesar Entrega de Insumos</title>
<?php include_once 'layouts/nav.php'; ?>

<!-- Modal Registro Rápido de Área Hospitalaria -->
<div class="modal fade" id="modal_nueva_area" tabindex="-1" aria-labelledby="modalNuevaAreaLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title fw-bold" id="modalNuevaAreaLabel">
          <i class="bi bi-hospital me-2"></i>Registrar Nueva Área Hospitalaria
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <form id="form_nueva_area_rapida">
        <div class="modal-body p-4">
          <div class="mb-3">
            <label for="modal_nombre_area" class="form-label fw-semibold small text-secondary">Nombre del Área / Departamento <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="modal_nombre_area" placeholder="Ej: Pediatría y Neonatología" required maxlength="100">
          </div>
        </div>
        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary fw-semibold" id="btn_guardar_area_rapida">
            <i class="bi bi-check-lg me-1"></i>Guardar y Seleccionar
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
                      Nombre del Receptor / Funcionario <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                      <span class="input-group-text"><i class="bi bi-person"></i></span>
                      <input type="text" class="form-control" id="cliente" placeholder="Ej: Lic. Adriana López" list="lista_receptores_habituales" required>
                      <datalist id="lista_receptores_habituales"></datalist>
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

                  <div class="col-md-6">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                      <label for="area_destino" class="form-label fw-semibold small text-secondary mb-0">
                        Área Hospitalaria de Destino <span class="text-danger">*</span>
                      </label>
                      <?php if ($_SESSION['us_tipo'] == 1): ?>
                      <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none small text-primary" data-bs-toggle="modal" data-bs-target="#modal_nueva_area">
                        <i class="bi bi-plus-circle me-1"></i>Nueva Área
                      </button>
                      <?php endif; ?>
                    </div>
                    <div class="input-group">
                      <span class="input-group-text"><i class="bi bi-hospital"></i></span>
                      <select class="form-select select2" id="area_destino" style="width: 85%;" required></select>
                    </div>
                  </div>

                  <div class="col-md-6">
                    <label for="cargo_receptor" class="form-label fw-semibold small text-secondary">
                      Cargo / Función del Receptor
                    </label>
                    <div class="input-group">
                      <span class="input-group-text"><i class="bi bi-briefcase"></i></span>
                      <input type="text" class="form-control" id="cargo_receptor" placeholder="Ej: Enfermera Jefe / Supervisor de Aseo">
                    </div>
                  </div>

                  <div class="col-12">
                    <label for="observacion_entrega" class="form-label fw-semibold small text-secondary">
                      Observaciones / Justificación de la Salida
                    </label>
                    <textarea class="form-control" id="observacion_entrega" rows="2" placeholder="Observaciones de la entrega institucional o motivo de dotación..."></textarea>
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
                      <th>Unidad / Espec.</th>
                      <th>Cantidad a Despachar</th>
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
                  <span class="text-muted">Tipos de Insumos</span>
                  <span class="fw-semibold text-dark" id="total_items_tipos">0</span>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-3">
                  <span class="text-muted">Total Unidades Físicas</span>
                  <span class="fw-bold fs-5 text-primary" id="total_items">0</span>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-3">
                  <span class="text-muted">Tipo de Movimiento</span>
                  <span class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle">Dotación / Suministro</span>
                </div>

                <hr class="my-3">

                <div class="d-grid gap-2">
                  <button type="button" class="btn btn-primary btn-lg shadow-sm fw-semibold" id="procesar_compra">
                    <i class="bi bi-check2-circle me-1"></i>Procesar Acta de Entrega
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

<?php 
$page_scripts = '
<script src="' . asset_v('../libs/js/catalogo.js') . '"></script>
<script src="' . asset_v('../libs/js/carrito.js') . '"></script>
';
include_once 'layouts/footer.php'; 
?>
