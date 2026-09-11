<?php session_start(); if ($_SESSION['us_tipo']==1||$_SESSION['us_tipo']==3) { include_once 'layouts/header.php'; ?>
<title><?php echo htmlspecialchars($_SESSION['nombre_us']); ?> | Gestión de Usuarios</title>
<?php include_once 'layouts/nav.php'; ?>

<!-- Modal Confirmar Credenciales (Ascender/Descender/Eliminar) -->
<div class="modal fade" id="check" tabindex="-1" aria-labelledby="checkLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title fw-bold" id="checkLabel">
          <i class="bi bi-shield-lock me-2"></i>Confirmación de Seguridad
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <form id="form-check">
        <div class="modal-body p-4 text-center">
          <img id="avatar3" src="../libs/img/avatars/user-default.png" class="rounded-circle shadow-sm border mb-2" style="width: 80px; height: 80px; object-fit: cover;">
          <div class="fw-bold text-dark fs-6 mb-1"><?php echo htmlspecialchars($_SESSION['nombre_us']); ?></div>
          <p class="text-muted small mb-3">Ingrese su contraseña de administrador para confirmar esta operación:</p>

          <div class="input-group mb-2">
            <span class="input-group-text"><i class="bi bi-key"></i></span>
            <input class="form-control" type="password" id="oldpass" placeholder="Contraseña actual" required autofocus>
          </div>
          <input type="hidden" id="id_user_rol">
          <input type="hidden" id="funcion">
        </div>
        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary fw-semibold">
            <i class="bi bi-check-lg me-1"></i>Confirmar
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Crear Usuario -->
<div class="modal fade" id="newuser" tabindex="-1" aria-labelledby="newUserLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title fw-bold" id="newUserLabel">
          <i class="bi bi-person-plus me-2"></i>Registrar Nuevo Usuario
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <form id="form-crear">
        <div class="modal-body p-4">
          <div class="row g-3">
            <div class="col-md-6">
              <label for="nombre" class="form-label fw-semibold small text-secondary">Nombres <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-person"></i></span>
                <input id="nombre" type="text" class="form-control" placeholder="Ej: Carlos Alberto" required>
              </div>
            </div>

            <div class="col-md-6">
              <label for="apellido" class="form-label fw-semibold small text-secondary">Apellidos <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-person"></i></span>
                <input id="apellido" type="text" class="form-control" placeholder="Ej: Gómez Pérez" required>
              </div>
            </div>

            <div class="col-md-6">
              <label for="ci" class="form-label fw-semibold small text-secondary">Cédula de Identidad <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-person-vcard"></i></span>
                <input id="ci" type="text" class="form-control" placeholder="Ej: 12345678" required>
              </div>
            </div>

            <div class="col-md-6">
              <label for="edad" class="form-label fw-semibold small text-secondary">Fecha de Nacimiento <span class="text-danger">*</span></label>
              <input id="edad" type="date" class="form-control" required>
            </div>

            <div class="col-md-6">
              <label for="genero" class="form-label fw-semibold small text-secondary">Género <span class="text-danger">*</span></label>
              <select id="genero" class="form-select" required>
                <option value="hombre">Hombre</option>
                <option value="mujer">Mujer</option>
                <option value="otro">Otro</option>
              </select>
            </div>

            <div class="col-md-6">
              <label for="pass" class="form-label fw-semibold small text-secondary">Contraseña Temporal <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                <input id="pass" type="password" class="form-control" placeholder="••••••••" required>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary fw-semibold">
            <i class="bi bi-check-lg me-1"></i>Crear Usuario
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
            Control de Usuarios y Accesos
            <button id="button_crear" type="button" data-bs-toggle="modal" data-bs-target="#newuser" class="btn btn-primary btn-sm ms-2 shadow-sm">
              <i class="bi bi-person-plus me-1"></i>Nuevo Usuario
            </button>
          </h1>
          <p class="text-muted small mb-0">Gestión de cuentas, roles de acceso y personal del centro asistencial</p>
          <input type="hidden" id="tipo_usuario" value="<?php echo $_SESSION['us_tipo']?>">
        </div>
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb mb-0 small">
            <li class="breadcrumb-item"><a href="adm_catalogo.php" class="text-decoration-none">Inicio</a></li>
            <li class="breadcrumb-item active" aria-current="page">Usuarios</li>
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
            <input type="text" id="buscar" class="form-control border-start-0 bg-light" placeholder="Buscar por nombre, apellido, cédula o rol...">
          </div>
        </div>
      </div>

      <!-- Grid de Usuarios -->
      <div id="usuarios" class="row g-3 g-md-4 align-items-stretch"></div>
    </div>
  </section>
</div>
<!-- /.content-wrapper -->

<?php include_once 'layouts/footer.php'; } else { header('Location: ../../index.php'); } ?>
<script src="../libs/js/gestion_user.js"></script>