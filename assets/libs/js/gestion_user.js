// SIMAP - Gestión de Usuarios y Accesos
$(document).ready(function() {
  let funcion;
  const tipo_usuario = $('#tipo_usuario').val();
  const id_usuario_actual = $('#id_usuario_actual').val();
  
  if (tipo_usuario == 2) {
    $('#button_crear').hide();
  }
  
  buscar_datos();

  function buscar_datos(consulta = '') {
    funcion = 'buscar_usuario_adm';
    $.post('../controller/UserController.php', { consulta, funcion }, (Response) => {
      let usuarios = [];
      try {
        usuarios = JSON.parse(Response);
      } catch (e) {
        usuarios = [];
      }

      if (!usuarios || usuarios.length === 0) {
        $('#usuarios').html(`
          <div class="col-12 py-5 text-center text-muted">
            <i class="bi bi-people fs-1 d-block mb-2 text-secondary"></i>
            <h5>No se encontraron usuarios</h5>
            <p class="small mb-0">Intente con otro término de búsqueda o registre un nuevo personal.</p>
          </div>
        `);
        return;
      }

      // Contar administradores para saber si se puede descender o eliminar
      const totalAdmins = usuarios.filter(u => u.tipo_usuario == 1).length;

      let templete = '';
      usuarios.forEach(usuario => {
        const esUsuarioActual = (usuario.id == id_usuario_actual);
        const badgeRol = (usuario.tipo_usuario == 1) 
          ? '<span class="badge bg-primary"><i class="bi bi-shield-shaded me-1"></i>Administrador</span>' 
          : '<span class="badge bg-success"><i class="bi bi-person-check me-1"></i>Secretario</span>';

        const badgeActual = esUsuarioActual 
          ? '<span class="badge bg-dark-subtle text-dark border border-secondary-subtle ms-1"><i class="bi bi-person-circle me-1"></i>Tú (Sesión Activa)</span>' 
          : '';

        templete += `
        <div usuarioId="${usuario.id}" usuarioNombre="${usuario.nombre} ${usuario.apellidos}" class="col-12 col-sm-6 col-lg-4 d-flex align-items-stretch mb-4">
          <div class="card border-0 shadow-sm w-100 rounded-3 overflow-hidden d-flex flex-column ${esUsuarioActual ? 'border-start border-4 border-primary' : ''}">
            <div class="card-header bg-light d-flex justify-content-between align-items-center py-2 px-3 border-0">
              <span class="small fw-semibold text-secondary">C.I: ${usuario.ci}</span>
              <div>
                ${badgeRol}
                ${badgeActual}
              </div>
            </div>
            <div class="card-body p-3 flex-grow-1">
              <div class="d-flex align-items-start gap-3 mb-3">
                <img src="${usuario.avatar}" alt="${usuario.nombre}" class="rounded-circle border shadow-sm flex-shrink-0" style="width: 64px; height: 64px; object-fit: cover;">
                <div class="overflow-hidden">
                  <h6 class="fw-bold mb-1 text-truncate">${usuario.nombre} ${usuario.apellidos}</h6>
                  <p class="text-muted small mb-0 line-clamp-2">${usuario.info || 'Personal asistencial registrado en SIMAP.'}</p>
                </div>
              </div>
              <ul class="list-unstyled small mb-0 text-muted">
                <li class="mb-1 d-flex align-items-center gap-2">
                  <i class="bi bi-person-vcard text-primary"></i>
                  <span><strong>Cédula:</strong> ${usuario.ci}</span>
                </li>
                <li class="mb-1 d-flex align-items-center gap-2">
                  <i class="bi bi-calendar3 text-primary"></i>
                  <span><strong>Edad:</strong> ${usuario.edad} años</span>
                </li>
                <li class="mb-1 d-flex align-items-center gap-2">
                  <i class="bi bi-telephone text-primary"></i>
                  <span><strong>Teléfono:</strong> ${usuario.telefono || 'No registrado'}</span>
                </li>
                <li class="mb-1 d-flex align-items-center gap-2">
                  <i class="bi bi-envelope text-primary"></i>
                  <span class="text-truncate"><strong>Correo:</strong> ${usuario.correo || 'No registrado'}</span>
                </li>
                <li class="d-flex align-items-center gap-2">
                  <i class="bi bi-gender-ambiguous text-primary"></i>
                  <span><strong>Género:</strong> ${usuario.genero || 'No especificado'}</span>
                </li>
              </ul>
            </div>
            <div class="card-footer bg-white border-top border-light-subtle p-2 d-flex justify-content-end gap-1">`;
            
            // Reglas de negocio: Si es la propia cuenta del operador, no permitir descender ni eliminar
            if (esUsuarioActual) {
              templete += `
                <span class="text-muted small px-2 py-1"><i class="bi bi-lock me-1"></i>Cuenta en uso</span>
              `;
            } else if (tipo_usuario == 1 && usuario.tipo_usuario == 2) {
              // Usuario Secretario: Se puede eliminar o ascender
              templete += `
                <button class="delete-user btn btn-outline-danger btn-sm rounded-pill px-3" type="button" data-bs-toggle="modal" data-bs-target="#check">
                  <i class="bi bi-trash me-1"></i>Eliminar
                </button>
                <button class="ascender btn btn-outline-primary btn-sm rounded-pill px-3" type="button" data-bs-toggle="modal" data-bs-target="#check">
                  <i class="bi bi-shield-check me-1"></i>Ascender
                </button>
              `;
            } else if (tipo_usuario == 1 && usuario.tipo_usuario == 1) {
              // Otro Administrador: Se puede descender si hay más de 1 administrador
              if (totalAdmins > 1) {
                templete += `
                  <button class="delete-user btn btn-outline-danger btn-sm rounded-pill px-3" type="button" data-bs-toggle="modal" data-bs-target="#check">
                    <i class="bi bi-trash me-1"></i>Eliminar
                  </button>
                  <button class="descender btn btn-outline-secondary btn-sm rounded-pill px-3" type="button" data-bs-toggle="modal" data-bs-target="#check">
                    <i class="bi bi-shield-slash me-1"></i>Descender
                  </button>
                `;
              } else {
                templete += `
                  <span class="text-muted small px-2 py-1"><i class="bi bi-shield-lock me-1"></i>Único Administrador</span>
                `;
              }
            }

            templete += `
            </div>
          </div>
        </div>`;
      });
      $('#usuarios').html(templete);
    });
  }

  // Búsqueda en tiempo real
  $(document).on('keyup', '#buscar', function() {
    const valor = $(this).val();
    buscar_datos(valor);
  });

  // Toggle de visualización de contraseña en modal nuevo usuario
  $('#toggleNewPass').on('click', function() {
    const input = $('#pass');
    const icon = $(this).find('i');
    if (input.attr('type') === 'password') {
      input.attr('type', 'text');
      icon.removeClass('bi-eye').addClass('bi-eye-slash');
    } else {
      input.attr('type', 'password');
      icon.removeClass('bi-eye-slash').addClass('bi-eye');
    }
  });

  // Toggle de visualización de contraseña en modal check
  $('#toggleCheckPass').on('click', function() {
    const input = $('#oldpass');
    const icon = $(this).find('i');
    if (input.attr('type') === 'password') {
      input.attr('type', 'text');
      icon.removeClass('bi-eye').addClass('bi-eye-slash');
    } else {
      input.attr('type', 'password');
      icon.removeClass('bi-eye-slash').addClass('bi-eye');
    }
  });

  // Registro de nuevo usuario
  $('#form-crear').submit(function(e) {
    e.preventDefault();
    const pass = $('#pass').val();
    const ci = $('#ci').val();

    if (pass.length < 6) {
      Swal.fire({
        icon: 'warning',
        title: 'Contraseña insegura',
        text: 'La contraseña debe tener un mínimo de 6 caracteres.'
      });
      return;
    }

    if (!/^\d{6,10}$/.test(ci)) {
      Swal.fire({
        icon: 'warning',
        title: 'Cédula inválida',
        text: 'La cédula debe ser numérica y contener entre 6 y 10 dígitos.'
      });
      return;
    }

    const formData = {
      nombre: $('#nombre').val(),
      apellido: $('#apellido').val(),
      edad: $('#edad').val(),
      ci: ci,
      genero: $('#genero').val(),
      pass: pass,
      funcion: 'crear_usuario'
    };

    $.post('../controller/UserController.php', formData, function(response) {
      if (response === 'add') {
        $('#form-crear').trigger('reset');
        Swal.fire({
          icon: 'success',
          title: 'Personal registrado con éxito',
          text: 'El nuevo usuario ha sido añadido con rol Secretario.',
          showConfirmButton: false,
          timer: 1600
        });
        const modalEl = document.getElementById('newuser');
        const modalInstance = bootstrap.Modal.getInstance(modalEl);
        if (modalInstance) modalInstance.hide();
        buscar_datos();
      } else {
        Swal.fire({
          icon: 'error',
          title: 'Error al registrar',
          text: 'No se pudo crear el usuario. Verifique si la cédula ya se encuentra registrada en el sistema.'
        });
      }
    });
  });

  // Preparar modal de confirmación con nombre de usuario y acción
  $(document).on('click', '.ascender', function() {
    const elemento = $(this).closest('[usuarioId]');
    const id = $(elemento).attr('usuarioId');
    const nombre = $(elemento).attr('usuarioNombre');
    funcion = 'ascender';
    $('#id_user_rol').val(id);
    $('#funcion').val(funcion);
    $('#check-accion-msg').removeClass('alert-danger alert-warning').addClass('alert-primary')
      .html(`¿Desea promover a <strong>${nombre}</strong> al rol de <strong>Administrador</strong>?`);
  });

  $(document).on('click', '.descender', function() {
    const elemento = $(this).closest('[usuarioId]');
    const id = $(elemento).attr('usuarioId');
    const nombre = $(elemento).attr('usuarioNombre');
    funcion = 'descender';
    $('#id_user_rol').val(id);
    $('#funcion').val(funcion);
    $('#check-accion-msg').removeClass('alert-primary alert-danger').addClass('alert-warning')
      .html(`¿Desea degradar a <strong>${nombre}</strong> al rol de <strong>Secretario</strong>?`);
  });

  $(document).on('click', '.delete-user', function() {
    const elemento = $(this).closest('[usuarioId]');
    const id = $(elemento).attr('usuarioId');
    const nombre = $(elemento).attr('usuarioNombre');
    funcion = 'delete_user';
    $('#id_user_rol').val(id);
    $('#funcion').val(funcion);
    $('#check-accion-msg').removeClass('alert-primary alert-warning').addClass('alert-danger')
      .html(`¿Está seguro de eliminar definitivamente a <strong>${nombre}</strong> de SIMAP?`);
  });

  // Confirmar acción con clave de administrador
  $('#form-check').submit(function(e) {
    e.preventDefault();
    const pass = $('#oldpass').val();
    const id_usuario = $('#id_user_rol').val();
    funcion = $('#funcion').val();
    
    $.post('../controller/UserController.php', { pass, id_usuario, funcion }, (response) => {
      $('#oldpass').val('');
      const modalEl = document.getElementById('check');
      const modalInstance = bootstrap.Modal.getInstance(modalEl);
      if (modalInstance) modalInstance.hide();

      if (response == 'up' || response == 'donw' || response == 'delete') {
        const accionTexto = (response == 'up') ? 'Usuario ascendido a Administrador.' :
                            (response == 'donw') ? 'Usuario descendido a Secretario.' : 'Usuario eliminado correctamente.';
        Swal.fire({
          icon: 'success',
          title: 'Operación Exitosa',
          text: accionTexto,
          showConfirmButton: false,
          timer: 1500
        });
      } else if (response == 'self-downgrade' || response == 'self-delete') {
        Swal.fire({
          icon: 'warning',
          title: 'Acción No Permitida',
          text: 'Por seguridad, no puedes alterar el rol ni eliminar tu propia cuenta en sesión activa.'
        });
      } else if (response == 'last-admin') {
        Swal.fire({
          icon: 'error',
          title: 'Protección de Seguridad',
          text: 'No es posible degradar ni eliminar al único Administrador existente en el sistema.'
        });
      } else {
        Swal.fire({
          icon: 'error',
          title: 'Error de Seguridad',
          text: 'Contraseña de administrador incorrecta o permisos insuficientes.'
        });
      }
      buscar_datos();
    });
  });
});