<?php session_start(); 
if (empty($_SESSION['us_tipo'])) {
    header('Location: error.php?code=401');
    exit();
}
if ($_SESSION['us_tipo'] != 1) {
    header('Location: error.php?code=403');
    exit();
}
include_once 'layouts/header.php'; ?>
<title><?php echo htmlspecialchars($_SESSION['nombre_us']); ?> | Gestión de Insumos</title>
<?php include_once 'layouts/nav.php'; ?>

<!-- Modal Crear / Editar Producto -->
<div class="modal fade" id="crearproducto" tabindex="-1" aria-labelledby="crearProductoLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title fw-bold" id="crearProductoLabel">
          <i class="bi bi-box-seam me-2"></i>Insumo / Material
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <form id="form-crear-producto">
        <div class="modal-body p-4">
          <div class="row g-3">
            <div class="col-12">
              <label for="nombre-producto" class="form-label fw-semibold small text-secondary">Nombre del Insumo / Material <span class="text-danger">*</span></label>
              <input id="nombre-producto" type="text" class="form-control" placeholder="Ej: Jabón Quirúrgico / Guantes de Látex" required>
            </div>

            <div class="col-md-6">
              <label for="unidad_medida" class="form-label fw-semibold small text-secondary">Unidad de Medida <span class="text-danger">*</span></label>
              <select class="form-select select2" id="unidad_medida" name="unidad_medida" style="width: 100%;" required></select>
            </div>

            <div class="col-md-6">
              <label for="especificacion_talla" class="form-label fw-semibold small text-secondary">Talla / Especificación Física</label>
              <input id="especificacion_talla" type="text" class="form-control" placeholder="Ej: Talla M, 70%, 1000ml, 5.25%">
              <input type="hidden" id="concentracion" value="">
            </div>

            <div class="col-12">
              <label for="adicional" class="form-label fw-semibold small text-secondary">Información Adicional / Componentes</label>
              <textarea id="adicional" class="form-control" rows="2" placeholder="Detalles de uso, precauciones o almacenamiento..."></textarea>
            </div>

            <div class="col-md-4">
              <div class="d-flex justify-content-between align-items-center mb-1">
                <label for="laboratorio" class="form-label fw-semibold small text-secondary mb-0">Fabricante <span class="text-danger">*</span></label>
                <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none small text-primary" data-bs-toggle="modal" data-bs-target="#modal_nuevo_lab_rapido">
                  <i class="bi bi-plus-circle me-1"></i>Nuevo
                </button>
              </div>
              <select id="laboratorio" class="form-select select2" style="width: 100%;" required></select>
            </div>

            <div class="col-md-4">
              <div class="d-flex justify-content-between align-items-center mb-1">
                <label for="tipo" class="form-label fw-semibold small text-secondary mb-0">Categoría <span class="text-danger">*</span></label>
                <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none small text-primary" data-bs-toggle="modal" data-bs-target="#modal_nuevo_tipo_rapido">
                  <i class="bi bi-plus-circle me-1"></i>Nueva
                </button>
              </div>
              <select name="tipo" id="tipo" class="form-select select2" style="width: 100%" required></select>
            </div>

            <div class="col-md-4">
              <div class="d-flex justify-content-between align-items-center mb-1">
                <label for="presentacion" class="form-label fw-semibold small text-secondary mb-0">Presentación <span class="text-danger">*</span></label>
                <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none small text-primary" data-bs-toggle="modal" data-bs-target="#modal_nueva_pres_rapida">
                  <i class="bi bi-plus-circle me-1"></i>Nueva
                </button>
              </div>
              <select name="presentacion" id="presentacion" class="form-select select2" style="width: 100%" required></select>
            </div>

            <!-- Sección de Dotación Inicial / Lote In-Situ -->
            <div class="col-12 mt-3" id="seccion_dotacion_inicial">
              <div class="card border border-primary-subtle bg-light shadow-none">
                <div class="card-body p-3">
                  <div class="form-check form-switch mb-2">
                    <input class="form-check-input" type="checkbox" id="check_dotacion_inicial">
                    <label class="form-check-label fw-semibold text-primary" for="check_dotacion_inicial">
                      <i class="bi bi-box-arrow-in-down me-1"></i>Registrar Dotación / Stock Inicial Inmediato
                    </label>
                  </div>
                  <div id="campos_dotacion_inicial" class="d-none mt-3">
                    <div class="row g-2">
                      <div class="col-md-6">
                        <label for="proveedor_inicial" class="form-label small fw-semibold text-secondary">Proveedor / Donante</label>
                        <select id="proveedor_inicial" class="form-select select2" style="width: 100%;"></select>
                      </div>
                      <div class="col-md-6">
                        <label for="cod_lote_inicial" class="form-label small fw-semibold text-secondary">Código de Lote</label>
                        <input type="text" id="cod_lote_inicial" class="form-control form-control-sm" placeholder="Ej: LOT-2026-INI">
                      </div>
                      <div class="col-md-6">
                        <label for="stock_inicial" class="form-label small fw-semibold text-secondary">Cantidad / Unidades</label>
                        <input type="number" id="stock_inicial" min="1" class="form-control form-control-sm" placeholder="0">
                      </div>
                      <div class="col-md-6">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                          <label for="vencimiento_inicial" class="form-label small fw-semibold text-secondary mb-0">Vencimiento</label>
                          <div class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox" id="noPerecedero_inicial">
                            <label class="form-check-label small text-muted" for="noPerecedero_inicial">No perecedero</label>
                          </div>
                        </div>
                        <input type="date" id="vencimiento_inicial" class="form-control form-control-sm">
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

          </div>
          <input type="hidden" id="id_edit_prod">
        </div>
        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary fw-semibold">
            <i class="bi bi-check-lg me-1"></i>Guardar Insumo
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Rápido Laboratorio / Fabricante -->
<div class="modal fade" id="modal_nuevo_lab_rapido" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-primary text-white py-2">
        <h6 class="modal-title fw-bold"><i class="bi bi-building me-1"></i>Nuevo Fabricante / Laboratorio</h6>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <form id="form_lab_rapido">
        <div class="modal-body p-3">
          <label for="modal_nombre_lab" class="form-label small fw-semibold text-secondary">Nombre de la Empresa / Fabricante <span class="text-danger">*</span></label>
          <input type="text" class="form-control" id="modal_nombre_lab" placeholder="Ej: Laboratorios Behrens" required>
        </div>
        <div class="modal-footer bg-light py-2">
          <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-sm btn-primary fw-semibold"><i class="bi bi-check-lg me-1"></i>Guardar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Rápido Categoría / Tipo -->
<div class="modal fade" id="modal_nuevo_tipo_rapido" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-primary text-white py-2">
        <h6 class="modal-title fw-bold"><i class="bi bi-tags me-1"></i>Nueva Categoría</h6>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <form id="form_tipo_rapido">
        <div class="modal-body p-3">
          <label for="modal_nombre_tipo" class="form-label small fw-semibold text-secondary">Nombre de la Categoría <span class="text-danger">*</span></label>
          <input type="text" class="form-control" id="modal_nombre_tipo" placeholder="Ej: Material Descartable" required>
        </div>
        <div class="modal-footer bg-light py-2">
          <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-sm btn-primary fw-semibold"><i class="bi bi-check-lg me-1"></i>Guardar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Rápido Presentación -->
<div class="modal fade" id="modal_nueva_pres_rapida" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-primary text-white py-2">
        <h6 class="modal-title fw-bold"><i class="bi bi-box-seam me-1"></i>Nueva Presentación</h6>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <form id="form_pres_rapida">
        <div class="modal-body p-3">
          <label for="modal_nombre_pres" class="form-label small fw-semibold text-secondary">Nombre de la Presentación / Empaque <span class="text-danger">*</span></label>
          <input type="text" class="form-control" id="modal_nombre_pres" placeholder="Ej: Galón, Caja x 100" required>
        </div>
        <div class="modal-footer bg-light py-2">
          <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-sm btn-primary fw-semibold"><i class="bi bi-check-lg me-1"></i>Guardar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Asignar Lote -->
<div class="modal fade" id="crearlote" tabindex="-1" aria-labelledby="crearLoteLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title fw-bold" id="crearLoteLabel">
          <i class="bi bi-collection me-2"></i>Asignar Lote a Insumo
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <form id="form-crear-lote">
        <div class="modal-body p-4">
          <div class="p-3 bg-light rounded-3 mb-3">
            <small class="text-muted d-block">Insumo Seleccionado:</small>
            <strong class="text-primary fs-6" id="nombre_producto_lote">Nombre de Producto</strong>
          </div>

          <div class="mb-3">
            <label for="proveedor" class="form-label fw-semibold small text-secondary">Proveedor <span class="text-danger">*</span></label>
            <select name="proveedor" id="proveedor" class="form-select select2" style="width: 100%" required></select>
          </div>

          <div class="row g-3">
            <div class="col-md-6">
              <label for="cod_lote" class="form-label fw-semibold small text-secondary">Código de Lote <span class="text-danger">*</span></label>
              <input id="cod_lote" type="text" class="form-control" placeholder="Ej: LOT-2026-A" required>
            </div>

            <div class="col-md-6">
              <label for="stock" class="form-label fw-semibold small text-secondary">Cantidad / Stock <span class="text-danger">*</span></label>
              <input id="stock" type="number" min="1" class="form-control" placeholder="0" required>
            </div>

            <div class="col-12">
              <div class="d-flex justify-content-between align-items-center mb-1">
                <label for="vencimiento" class="form-label fw-semibold small text-secondary mb-0">Fecha de Vencimiento</label>
                <div class="form-check form-switch mb-0">
                  <input class="form-check-input" type="checkbox" id="noPerecedero" name="noPerecedero">
                  <label class="form-check-label small text-muted" for="noPerecedero">Sin vencimiento</label>
                </div>
              </div>
              <input id="vencimiento" type="date" class="form-control">
            </div>
          </div>
          <input type="hidden" id="id_lote_prod">
        </div>
        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary fw-semibold">
            <i class="bi bi-check-lg me-1"></i>Registrar Lote
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Cambiar Imagen -->
<div class="modal fade" id="cambiarlogo" tabindex="-1" aria-labelledby="cambiarLogoLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title fw-bold" id="cambiarLogoLabel">
          <i class="bi bi-image me-2"></i>Cambiar Imagen del Insumo
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <form id="form-logo" enctype="multipart/form-data">
        <div class="modal-body p-4 text-center">
          <img id="logoactual1" src="../libs/img/product/prod_default.png" class="rounded-circle shadow-sm border mb-3" style="width: 120px; height: 120px; object-fit: cover;">
          <div class="fw-bold fs-6 text-dark mb-3" id="nombre_logo">Nombre del Insumo</div>

          <div class="mb-3 text-start">
            <label for="foto" class="form-label small fw-semibold text-secondary">Seleccionar nueva imagen</label>
            <input type="file" id="foto" name="foto" class="form-control" accept="image/*" required>
            <small class="text-muted">Formatos admitidos: JPG, PNG, WEBP.</small>
          </div>

          <input type="hidden" name="funcion" id="funcion">
          <input type="hidden" name="id_logo_prod" id="id_logo_prod">
          <input type="hidden" name="avatar" id="avatar">
        </div>
        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary fw-semibold">
            <i class="bi bi-upload me-1"></i>Subir Imagen
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
          <h1 class="h3 fw-bold mb-1 text-primary">
            Gestión de Insumos y Materiales
            <button id="button_crear" type="button" data-bs-toggle="modal" data-bs-target="#crearproducto" class="crearpd btn btn-primary btn-sm ms-2 shadow-sm">
              <i class="bi bi-plus-circle me-1"></i>Nuevo Insumo
            </button>
          </h1>
          <p class="text-muted small mb-0">Catálogo general, configuración de atributos y control de inventario</p>
        </div>
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb mb-0 small">
            <li class="breadcrumb-item"><a href="adm_catalogo.php" class="text-decoration-none">Inicio</a></li>
            <li class="breadcrumb-item active" aria-current="page">Gestión de Insumos</li>
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
            <input type="text" id="buscar_producto" class="form-control border-start-0 bg-light" placeholder="Buscar por nombre, código o especificación...">
          </div>
        </div>
      </div>

      <!-- Grid de Productos -->
      <div id="productos" class="row g-3 g-md-4 align-items-stretch"></div>
    </div>
  </section>
</div>
<!-- /.content-wrapper -->

<?php 
$page_scripts = '
<script src="' . asset_v('../libs/js/producto.js') . '"></script>
';
include_once 'layouts/footer.php'; 
?>
