<?php session_start(); if ($_SESSION['us_tipo']==1||$_SESSION['us_tipo']==3) { include_once 'layouts/header.php'; ?>
<title><?php echo htmlspecialchars($_SESSION['nombre_us']); ?> | Gestión de Proveedores</title>
<?php include_once 'layouts/nav.php'; ?>

<!-- Modal Crear / Editar Proveedor -->
<div class="modal fade" id="newproveedor" tabindex="-1" aria-labelledby="crearProveedorLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title fw-bold" id="crearProveedorLabel">
          <i class="bi bi-truck me-2"></i>Proveedor
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <form id="form-crear-proveedor">
        <div class="modal-body p-4">
          <div class="mb-3">
            <label for="nombre" class="form-label fw-semibold small text-secondary">Razón Social / Nombre <span class="text-danger">*</span></label>
            <div class="input-group">
              <span class="input-group-text"><i class="bi bi-building"></i></span>
              <input id="nombre" type="text" class="form-control" placeholder="Ej: Distribuidora Médica Andina C.A." required>
            </div>
            <input type="hidden" id="id_editar_prov">
          </div>

          <div class="mb-3">
            <label for="telefono" class="form-label fw-semibold small text-secondary">Teléfono de Contacto <span class="text-danger">*</span></label>
            <div class="input-group">
              <span class="input-group-text"><i class="bi bi-telephone"></i></span>
              <input id="telefono" type="text" class="form-control" placeholder="Ej: 0276-1234567" required>
            </div>
          </div>

          <div class="mb-3">
            <label for="correo" class="form-label fw-semibold small text-secondary">Correo Electrónico</label>
            <div class="input-group">
              <span class="input-group-text"><i class="bi bi-envelope"></i></span>
              <input id="correo" type="email" class="form-control" placeholder="contacto@proveedor.com">
            </div>
          </div>

          <div class="mb-3">
            <label for="direccion" class="form-label fw-semibold small text-secondary">Dirección Fiscal / Ubicación</label>
            <textarea id="direccion" class="form-control" rows="2" placeholder="Ciudad, calle, zona industrial..."></textarea>
          </div>
        </div>
        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary fw-semibold">
            <i class="bi bi-check-lg me-1"></i>Guardar Proveedor
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Cambiar Avatar Proveedor -->
<div class="modal fade" id="cambioavatar" tabindex="-1" aria-labelledby="cambioAvatarLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title fw-bold" id="cambioAvatarLabel">
          <i class="bi bi-image me-2"></i>Logotipo del Proveedor
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <form id="form-logo" enctype="multipart/form-data">
        <div class="modal-body p-4 text-center">
          <img id="logoactual1" src="../libs/img/proveedors/ProveedorDefault.png" class="rounded-circle shadow-sm border mb-3" style="width: 120px; height: 120px; object-fit: cover;">
          <div class="fw-bold fs-6 text-dark mb-3" id="nombre_logo">Nombre del Proveedor</div>

          <div class="mb-3 text-start">
            <label for="foto" class="form-label small fw-semibold text-secondary">Seleccionar imagen o logo</label>
            <input type="file" id="foto" name="foto" class="form-control" accept="image/*" required>
          </div>

          <input type="hidden" name="funcion" id="funcion">
          <input type="hidden" name="id_logo_prod" id="id_logo_prod">
          <input type="hidden" name="avatar" id="avatar">
        </div>
        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary fw-semibold">
            <i class="bi bi-upload me-1"></i>Subir Logotipo
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
            Directorio de Proveedores
            <button id="button_crear" type="button" data-bs-toggle="modal" data-bs-target="#newproveedor" class="crearprov btn btn-primary btn-sm ms-2 shadow-sm">
              <i class="bi bi-plus-circle me-1"></i>Nuevo Proveedor
            </button>
          </h1>
          <p class="text-muted small mb-0">Empresas fabricantes, distribuidoras y donantes de insumos</p>
          <input type="hidden" id="proveedor" value="<?php echo $_SESSION['us_tipo']?>">
        </div>
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb mb-0 small">
            <li class="breadcrumb-item"><a href="adm_catalogo.php" class="text-decoration-none">Inicio</a></li>
            <li class="breadcrumb-item active" aria-current="page">Gestión de Proveedores</li>
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
            <input type="text" id="buscar_proveedor" class="form-control border-start-0 bg-light" placeholder="Buscar por nombre de empresa, teléfono o correo...">
          </div>
        </div>
      </div>

      <!-- Grid de Proveedores -->
      <div id="proveedores" class="row g-3 g-md-4 align-items-stretch"></div>
    </div>
  </section>
</div>
<!-- /.content-wrapper -->

<?php include_once 'layouts/footer.php'; } else { header('Location: ../../index.php'); } ?>
<script src="../libs/js/proveedor.js"></script>