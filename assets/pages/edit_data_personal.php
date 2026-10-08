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
<title><?php echo htmlspecialchars($_SESSION['nombre_us']); ?> | Mi Perfil</title>
<?php include_once 'layouts/nav.php'; ?>

<!-- Modal Cambiar Contraseña Seguro -->
<div class="modal fade" id="cambiarcontrasena" tabindex="-1" aria-labelledby="cambiarPassLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg rounded-3 overflow-hidden">
      <div class="modal-header bg-primary text-white py-3">
        <h5 class="modal-title fw-bold" id="cambiarPassLabel">
          <i class="bi bi-shield-lock me-2"></i>Actualizar Contraseña
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <form id="form-pass">
        <div class="modal-body p-4 text-center">
          <img id="avatar3" src="../libs/img/avatars/user-default.png" class="rounded-circle shadow-sm border mb-2" style="width: 76px; height: 76px; object-fit: cover;">
          <div class="fw-bold text-dark fs-6 mb-1"><?php echo htmlspecialchars($_SESSION['nombre_us']); ?></div>
          <p class="text-muted small mb-4">Ingrese su clave actual y defina una nueva contraseña cifrada (mínimo 6 caracteres).</p>

          <div class="mb-3 text-start">
            <label for="oldpass" class="form-label small fw-semibold text-secondary">Contraseña Actual <span class="text-danger">*</span></label>
            <div class="input-group">
              <span class="input-group-text bg-light text-muted"><i class="bi bi-unlock"></i></span>
              <input class="form-control" type="password" id="oldpass" placeholder="Contraseña actual" required autocomplete="current-password">
              <button class="btn btn-outline-secondary btn-toggle-pass" type="button" data-target="oldpass" title="Mostrar/Ocultar">
                <i class="bi bi-eye"></i>
              </button>
            </div>
          </div>

          <div class="mb-3 text-start">
            <label for="newpass" class="form-label small fw-semibold text-secondary">Nueva Contraseña <span class="text-danger">*</span></label>
            <div class="input-group">
              <span class="input-group-text bg-light text-muted"><i class="bi bi-lock"></i></span>
              <input class="form-control" type="password" id="newpass" placeholder="Mínimo 6 caracteres" minlength="6" required autocomplete="new-password">
              <button class="btn btn-outline-secondary btn-toggle-pass" type="button" data-target="newpass" title="Mostrar/Ocultar">
                <i class="bi bi-eye"></i>
              </button>
            </div>
            <div class="form-text small text-muted">Use letras, números o símbolos para mayor seguridad.</div>
          </div>

          <div class="mb-2 text-start">
            <label for="confirmpass" class="form-label small fw-semibold text-secondary">Confirmar Nueva Contraseña <span class="text-danger">*</span></label>
            <div class="input-group">
              <span class="input-group-text bg-light text-muted"><i class="bi bi-lock-fill"></i></span>
              <input class="form-control" type="password" id="confirmpass" placeholder="Repita la nueva contraseña" minlength="6" required autocomplete="new-password">
              <button class="btn btn-outline-secondary btn-toggle-pass" type="button" data-target="confirmpass" title="Mostrar/Ocultar">
                <i class="bi bi-eye"></i>
              </button>
            </div>
            <div id="pass-match-feedback" class="small mt-1 d-none"></div>
          </div>
        </div>
        <div class="modal-footer bg-light border-top">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary fw-semibold" id="btn-submit-pass">
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
    <div class="modal-content border-0 shadow-lg rounded-3 overflow-hidden">
      <div class="modal-header bg-primary text-white py-3">
        <h5 class="modal-title fw-bold" id="cambioFotoLabel">
          <i class="bi bi-camera me-2"></i>Actualizar Foto de Perfil
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <form id="form-foto" enctype="multipart/form-data">
        <div class="modal-body p-4 text-center">
          <div class="position-relative d-inline-block mb-3">
            <img id="avatar1" src="../libs/img/avatars/user-default.png" class="rounded-circle shadow-sm border" style="width: 120px; height: 120px; object-fit: cover;">
          </div>
          <p class="text-muted small mb-3">Seleccione una imagen en formato JPG, PNG o WEBP (máx. 5 MB).</p>

          <div class="mb-3 text-start">
            <label for="foto_user" class="form-label small fw-semibold text-secondary">Archivo de imagen</label>
            <input type="file" id="foto_user" class="form-control" name="foto" accept="image/jpeg,image/png,image/jpg,image/webp" required>
            <input type="hidden" name="funcion" value="cambiar_foto">
          </div>
        </div>
        <div class="modal-footer bg-light border-top">
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
          <div class="card border-0 shadow-sm text-center p-4 rounded-3">
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
              <button type="button" data-bs-toggle="modal" data-bs-target="#cambiofoto" class="btn btn-outline-primary btn-sm fw-semibold">
                <i class="bi bi-camera me-1"></i>Cambiar Foto
              </button>
              <button type="button" data-bs-toggle="modal" data-bs-target="#cambiarcontrasena" class="btn btn-outline-secondary btn-sm fw-semibold">
                <i class="bi bi-key me-1"></i>Cambiar Contraseña
              </button>
            </div>
          </div>
        </div>

        <!-- Columna Derecha: Formulario de Datos -->
        <div class="col-lg-8">
          <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
              <h5 class="card-title fw-bold text-primary mb-0">
                <i class="bi bi-pencil-square me-2"></i>Información de Contacto y Datos
              </h5>
              <button type="button" class="btn btn-sm btn-outline-primary edit fw-semibold" id="btn-toggle-edit">
                <i class="bi bi-pencil me-1"></i>Habilitar Edición
              </button>
            </div>
            <div class="card-body p-4">
              <form id="form-usuario">
                <div class="row g-3 mb-4">
                  <div class="col-md-6">
                    <label for="telefono" class="form-label fw-semibold small text-secondary">Teléfono</label>
                    <div class="input-group">
                      <span class="input-group-text bg-light text-muted"><i class="bi bi-telephone"></i></span>
                      <input id="telefono" type="text" class="form-control" placeholder="Ej: 0414-1234567" disabled>
                    </div>
                  </div>

                  <div class="col-md-6">
                    <label for="email" class="form-label fw-semibold small text-secondary">Correo Electrónico</label>
                    <div class="input-group">
                      <span class="input-group-text bg-light text-muted"><i class="bi bi-envelope"></i></span>
                      <input id="email" type="email" class="form-control" placeholder="correo@ejemplo.com" disabled>
                    </div>
                  </div>

                  <div class="col-md-6">
                    <label for="genero" class="form-label fw-semibold small text-secondary">Género</label>
                    <select id="genero" class="form-select" disabled>
                      <option value="hombre">Hombre</option>
                      <option value="mujer">Mujer</option>
                      <option value="otro">Otro</option>
                    </select>
                  </div>

                  <div class="col-12">
                    <label for="info-adicional" class="form-label fw-semibold small text-secondary">Información Adicional / Cargo / Observaciones</label>
                    <textarea id="info-adicional" class="form-control" rows="3" placeholder="Detalles de rol institucional o área asignada..." disabled></textarea>
                  </div>
                </div>

                <div class="d-flex justify-content-end gap-2" id="acciones-edicion" style="display: none !important;">
                  <button type="button" class="btn btn-outline-secondary btn-sm px-3" id="btn-cancelar-edit">
                    Cancelar
                  </button>
                  <button type="submit" class="btn btn-primary fw-semibold px-4 shadow-sm btn-sm">
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

<input type="hidden" id="id_usuario" value="<?php echo $_SESSION['usuario']; ?>">

<?php 
$page_scripts = '
<script src="' . asset_v('../libs/js/usuario.js') . '"></script>
';
include_once 'layouts/footer.php'; 
?>
