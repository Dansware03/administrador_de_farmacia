<?php session_start(); if ($_SESSION['us_tipo']==1||$_SESSION['us_tipo']==3) { include_once 'layouts/header.php'; ?>
<title><?php echo htmlspecialchars($_SESSION['nombre_us']); ?> | Mi Perfil</title>
<?php include_once 'layouts/nav.php'; ?>

<!-- Modal Cambiar Contraseña -->
<div class="modal fade" id="cambiarcontrasena" tabindex="-1" aria-labelledby="cambiarPassLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title fw-bold" id="cambiarPassLabel">
          <i class="bi bi-key me-2"></i>Actualizar Contraseña
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <form id="form-pass">
        <div class="modal-body p-4 text-center">
          <img id="avatar3" src="../libs/img/avatars/user-default.png" class="rounded-circle shadow-sm border mb-2" style="width: 80px; height: 80px; object-fit: cover;">
          <div class="fw-bold text-dark fs-6 mb-3"><?php echo htmlspecialchars($_SESSION['nombre_us']); ?></div>

          <div class="mb-3 text-start">
            <label for="oldpass" class="form-label small fw-semibold text-secondary">Contraseña Actual <span class="text-danger">*</span></label>
            <div class="input-group">
              <span class="input-group-text"><i class="bi bi-unlock"></i></span>
              <input class="form-control" type="password" id="oldpass" placeholder="••••••••" required>
            </div>
          </div>

          <div class="mb-2 text-start">
            <label for="newpass" class="form-label small fw-semibold text-secondary">Nueva Contraseña <span class="text-danger">*</span></label>
            <div class="input-group">
              <span class="input-group-text"><i class="bi bi-lock"></i></span>
              <input class="form-control" type="password" id="newpass" placeholder="••••••••" required>
            </div>
          </div>
        </div>
        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary fw-semibold">
            <i class="bi bi-check-lg me-1"></i>Actualizar Contraseña
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Cambiar Avatar -->
<div class="modal fade" id="cambiofoto" tabindex="-1" aria-labelledby="cambioFotoLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title fw-bold" id="cambioFotoLabel">
          <i class="bi bi-image me-2"></i>Actualizar Foto de Perfil
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <form id="form-foto" enctype="multipart/form-data">
        <div class="modal-body p-4 text-center">
          <img id="avatar1" src="../libs/img/avatars/user-default.png" class="rounded-circle shadow-sm border mb-3" style="width: 120px; height: 120px; object-fit: cover;">

          <div class="mb-3 text-start">
            <label for="foto_user" class="form-label small fw-semibold text-secondary">Seleccionar imagen</label>
            <input type="file" id="foto_user" class="form-control" name="foto" accept="image/*" required>
            <input type="hidden" name="funcion" value="cambiar_foto">
          </div>
        </div>
        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary fw-semibold">
            <i class="bi bi-upload me-1"></i>Guardar Foto
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
          <h1 class="h3 fw-bold mb-1 text-primary">Perfil y Datos Personales</h1>
          <p class="text-muted small mb-0">Información del usuario autenticado y preferencias de seguridad</p>
        </div>
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb mb-0 small">
            <li class="breadcrumb-item"><a href="adm_catalogo.php" class="text-decoration-none">Inicio</a></li>
            <li class="breadcrumb-item active" aria-current="page">Mi Perfil</li>
          </ol>
        </nav>
      </div>
    </div>
  </section>

  <!-- Main content -->
  <section>
    <div class="container-fluid">
      <div class="row g-4">
        <!-- Columna Izquierda: Tarjeta de Perfil -->
        <div class="col-lg-4">
          <div class="card border-0 shadow-sm text-center p-4">
            <div class="position-relative d-inline-block mx-auto mb-3">
              <img id="avatar2" src="../libs/img/avatars/user-default.png" class="rounded-circle shadow-sm border" style="width: 130px; height: 130px; object-fit: cover;" alt="Avatar">
            </div>

            <h4 class="fw-bold text-dark mb-1">
              <span id="nombre_us">Cargando...</span> <span id="apellidos_us"></span>
            </h4>

            <div class="mb-3" id="tipo_us">
              <span class="badge bg-primary px-3 py-2">Usuario</span>
            </div>

            <ul class="list-group list-group-flush text-start small border-top pt-2 mb-4">
              <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                <span class="text-muted"><i class="bi bi-person-vcard me-2"></i>Cédula:</span>
                <strong id="ci_us" class="text-dark">---</strong>
              </li>
              <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                <span class="text-muted"><i class="bi bi-calendar-event me-2"></i>Fecha Nac.:</span>
                <strong id="edad" class="text-dark">---</strong>
              </li>
            </ul>

            <div class="d-grid gap-2">
              <button type="button" data-bs-toggle="modal" data-bs-target="#cambiofoto" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-camera me-1"></i>Cambiar Foto
              </button>
              <button type="button" data-bs-toggle="modal" data-bs-target="#cambiarcontrasena" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-key me-1"></i>Cambiar Contraseña
              </button>
            </div>
          </div>
        </div>

        <!-- Columna Derecha: Formulario de Datos -->
        <div class="col-lg-8">
          <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
              <h5 class="card-title fw-bold text-primary mb-0">
                <i class="bi bi-pencil-square me-2"></i>Información de Contacto y Datos
              </h5>
              <button type="button" class="btn btn-sm btn-outline-primary edit">
                <i class="bi bi-pencil me-1"></i>Habilitar Edición
              </button>
            </div>
            <div class="card-body p-4">
              <form id="form-usuario">
                <div class="row g-3 mb-4">
                  <div class="col-md-6">
                    <label for="telefono" class="form-label fw-semibold small text-secondary">Teléfono</label>
                    <div class="input-group">
                      <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                      <input id="telefono" type="text" class="form-control" placeholder="Ej: 0414-1234567">
                    </div>
                  </div>

                  <div class="col-md-6">
                    <label for="email" class="form-label fw-semibold small text-secondary">Correo Electrónico</label>
                    <div class="input-group">
                      <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                      <input id="email" type="email" class="form-control" placeholder="correo@ejemplo.com">
                    </div>
                  </div>

                  <div class="col-md-6">
                    <label for="genero" class="form-label fw-semibold small text-secondary">Género</label>
                    <select id="genero" class="form-select">
                      <option value="hombre">Hombre</option>
                      <option value="mujer">Mujer</option>
                      <option value="otro">Otro</option>
                    </select>
                  </div>

                  <div class="col-12">
                    <label for="info-adicional" class="form-label fw-semibold small text-secondary">Información Adicional / Cargo / Observaciones</label>
                    <textarea id="info-adicional" class="form-control" rows="3" placeholder="Detalles de rol institucional o área asignada..."></textarea>
                  </div>
                </div>

                <div class="d-flex justify-content-end">
                  <button type="submit" class="btn btn-primary fw-semibold px-4 shadow-sm">
                    <i class="bi bi-save me-1"></i>Guardar Cambios
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
<!-- /.content-wrapper -->

<input type="hidden" id="id_usuario" value="<?php echo $_SESSION['usuario']?>">

<?php include_once 'layouts/footer.php'; } else { header('Location: ../../index.php'); } ?>
<script src="../libs/js/usuario.js"></script>