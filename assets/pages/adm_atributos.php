<?php session_start(); if ($_SESSION['us_tipo']==1||$_SESSION['us_tipo']==3) { include_once 'layouts/header.php'; ?>
<title><?php echo htmlspecialchars($_SESSION['nombre_us']); ?> | Gestión de Atributos</title>
<?php include_once 'layouts/nav.php'; ?>

<!-- Modal Laboratorio -->
<div class="modal fade" id="crear-laboratorio" tabindex="-1" aria-labelledby="crearLabLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title fw-bold" id="crearLabLabel">
          <i class="bi bi-building me-2"></i>Laboratorio / Fabricante
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <form id="form-crear-laboratorio">
        <div class="modal-body p-4">
          <div class="mb-3">
            <label for="nombre-laboratorio" class="form-label fw-semibold small text-secondary">Nombre de la Empresa o Laboratorio <span class="text-danger">*</span></label>
            <input id="nombre-laboratorio" type="text" class="form-control" placeholder="Ej: Laboratorios Behrens" required>
            <input type="hidden" id="id_editar_lab">
          </div>
        </div>
        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary fw-semibold">
            <i class="bi bi-check-lg me-1"></i>Guardar Laboratorio
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Tipo -->
<div class="modal fade" id="crear-tipo" tabindex="-1" aria-labelledby="crearTipoLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title fw-bold" id="crearTipoLabel">
          <i class="bi bi-tags me-2"></i>Tipo / Categoría de Insumo
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <form id="form-crear-tipo">
        <div class="modal-body p-4">
          <div class="mb-3">
            <label for="nombre-tipo" class="form-label fw-semibold small text-secondary">Nombre del Tipo / Categoría <span class="text-danger">*</span></label>
            <input id="nombre-tipo" type="text" class="form-control" placeholder="Ej: Material Descartable, Desinfectante" required>
            <input type="hidden" id="id_editar_type">
          </div>
        </div>
        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary fw-semibold">
            <i class="bi bi-check-lg me-1"></i>Guardar Categoría
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Presentación -->
<div class="modal fade" id="crear-presentacion" tabindex="-1" aria-labelledby="crearPresLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title fw-bold" id="crearPresLabel">
          <i class="bi bi-box-seam me-2"></i>Presentación / Empaque
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <form id="form-crear-presentacion">
        <div class="modal-body p-4">
          <div class="mb-3">
            <label for="nombre-presentacion" class="form-label fw-semibold small text-secondary">Nombre de la Presentación <span class="text-danger">*</span></label>
            <input id="nombre-presentacion" type="text" class="form-control" placeholder="Ej: Frasco de 1L, Galón, Paquete x 100" required>
            <input type="hidden" id="id_editar_presentacion">
          </div>
        </div>
        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary fw-semibold">
            <i class="bi bi-check-lg me-1"></i>Guardar Presentación
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
          <h1 class="h3 fw-bold mb-1 text-primary">Gestión de Atributos y Clasificaciones</h1>
          <p class="text-muted small mb-0">Laboratorios, tipos de material y tipos de empaque</p>
        </div>
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb mb-0 small">
            <li class="breadcrumb-item"><a href="adm_catalogo.php" class="text-decoration-none">Inicio</a></li>
            <li class="breadcrumb-item active" aria-current="page">Atributos</li>
          </ol>
        </nav>
      </div>
    </div>
  </section>

  <!-- Main content -->
  <section>
    <div class="container-fluid">
      <div class="card border-0 shadow-sm">
        <!-- Navegación por Pestañas (Pills) -->
        <div class="card-header bg-white border-bottom p-3">
          <ul class="nav nav-pills gap-2" id="atributosTab" role="tablist">
            <li class="nav-item" role="presentation">
              <button class="nav-link active px-4 py-2 fw-semibold" id="lab-tab" data-bs-toggle="pill" data-bs-target="#laboratory" type="button" role="tab" aria-controls="laboratory" aria-selected="true">
                <i class="bi bi-building me-2"></i>Laboratorios
              </button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link px-4 py-2 fw-semibold" id="tipo-tab" data-bs-toggle="pill" data-bs-target="#tipo" type="button" role="tab" aria-controls="tipo" aria-selected="false">
                <i class="bi bi-tags me-2"></i>Tipos de Insumo
              </button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link px-4 py-2 fw-semibold" id="pres-tab" data-bs-toggle="pill" data-bs-target="#presentacion" type="button" role="tab" aria-controls="presentacion" aria-selected="false">
                <i class="bi bi-box-seam me-2"></i>Presentaciones
              </button>
            </li>
          </ul>
        </div>

        <div class="card-body p-4">
          <div class="tab-content" id="atributosTabContent">
            <!-- TAB 1: LABORATORIOS -->
            <div class="tab-pane fade show active" id="laboratory" role="tabpanel" aria-labelledby="lab-tab">
              <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
                <div class="input-group flex-grow-1" style="max-width: 450px;">
                  <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                  <input id="buscar-laboratory" type="text" class="form-control bg-light" placeholder="Buscar laboratorio...">
                </div>
                <button type="button" data-bs-toggle="modal" data-bs-target="#crear-laboratorio" class="btn btn-primary shadow-sm fw-semibold">
                  <i class="bi bi-plus-circle me-1"></i>Nuevo Laboratorio
                </button>
              </div>
              <div class="table-responsive rounded-3 border">
                <table class="table table-hover align-middle mb-0">
                  <thead class="table-light small">
                    <tr>
                      <th class="ps-3">Nombre del Laboratorio / Fabricante</th>
                      <th class="text-end pe-3" style="width: 150px;">Acciones</th>
                    </tr>
                  </thead>
                  <tbody id="laboratorios"></tbody>
                </table>
              </div>
            </div>

            <!-- TAB 2: TIPOS -->
            <div class="tab-pane fade" id="tipo" role="tabpanel" aria-labelledby="tipo-tab">
              <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
                <div class="input-group flex-grow-1" style="max-width: 450px;">
                  <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                  <input id="buscar-tipo" type="text" class="form-control bg-light" placeholder="Buscar tipo o categoría...">
                </div>
                <button type="button" data-bs-toggle="modal" data-bs-target="#crear-tipo" class="btn btn-primary shadow-sm fw-semibold">
                  <i class="bi bi-plus-circle me-1"></i>Nuevo Tipo
                </button>
              </div>
              <div class="table-responsive rounded-3 border">
                <table class="table table-hover align-middle mb-0">
                  <thead class="table-light small">
                    <tr>
                      <th class="ps-3">Tipo / Clasificación</th>
                      <th class="text-end pe-3" style="width: 150px;">Acciones</th>
                    </tr>
                  </thead>
                  <tbody id="tipos"></tbody>
                </table>
              </div>
            </div>

            <!-- TAB 3: PRESENTACIONES -->
            <div class="tab-pane fade" id="presentacion" role="tabpanel" aria-labelledby="pres-tab">
              <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
                <div class="input-group flex-grow-1" style="max-width: 450px;">
                  <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                  <input id="buscar-presentacion" type="text" class="form-control bg-light" placeholder="Buscar presentación...">
                </div>
                <button type="button" data-bs-toggle="modal" data-bs-target="#crear-presentacion" class="btn btn-primary shadow-sm fw-semibold">
                  <i class="bi bi-plus-circle me-1"></i>Nueva Presentación
                </button>
              </div>
              <div class="table-responsive rounded-3 border">
                <table class="table table-hover align-middle mb-0">
                  <thead class="table-light small">
                    <tr>
                      <th class="ps-3">Presentación / Formato de Empaque</th>
                      <th class="text-end pe-3" style="width: 150px;">Acciones</th>
                    </tr>
                  </thead>
                  <tbody id="presentaciones"></tbody>
                </table>
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
<script src="../libs/js/laboratory.js"></script>
<script src="../libs/js/type.js"></script>
<script src="../libs/js/presentaciones.js"></script>