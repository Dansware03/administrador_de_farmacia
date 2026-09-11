// SIMAP - Gestión de Usuarios
$(document).ready(function() {
  let funcion;
  const tipo_usuario = $('#tipo_usuario').val();
  
  if (tipo_usuario == 2) {
    $('#button_crear').hide();
  }
  
  buscar_datos();

  function buscar_datos(consulta) {
    funcion = 'buscar_usuario_adm';
    $.post('../controller/UserController.php', { consulta, funcion }, (Response) => {
      const usuarios = JSON.parse(Response);
      let templete = '';
      usuarios.forEach(usuario => {
        let badgeRol = '';
        if (usuario.tipo_usuario == 3) {
          badgeRol = '<span class="badge bg-danger">Root</span>';
        } else if (usuario.tipo_usuario == 1) {
          badgeRol = '<span class="badge bg-primary">Administrador</span>';
        } else if (usuario.tipo_usuario == 2) {
          badgeRol = '<span class="badge bg-success">Farmacéutico</span>';
        }

        templete += `
        <div usuarioId="${usuario.id}" class="col-12 col-sm-6 col-lg-4 d-flex align-items-stretch mb-4">
          <div class="card border-0 shadow-sm w-100 rounded-3 overflow-hidden d-flex flex-column">
            <div class="card-header bg-light d-flex justify-content-between align-items-center py-2 px-3 border-0">
              <span class="small fw-semibold text-secondary">#${usuario.ci}</span>
              ${badgeRol}
            </div>
            <div class="card-body p-3 flex-grow-1">
              <div class="d-flex align-items-start gap-3 mb-3">
                <img src="${usuario.avatar}" alt="${usuario.nombre}" class="rounded-circle border shadow-sm flex-shrink-0" style="width: 64px; height: 64px; object-fit: cover;">
                <div class="overflow-hidden">
                  <h6 class="fw-bold mb-1 text-truncate">${usuario.nombre} ${usuario.apellidos}</h6>
                  <p class="text-muted small mb-0 line-clamp-2">${usuario.info || 'Sin información adicional registrada.'}</p>
                </div>
              </div>
              <ul class="list-unstyled small mb-0 text-muted">
                <li class="mb-1 d-flex align-items-center gap-2">
                  <i class="bi bi-person-vcard text-primary"></i>
                  <span><strong>C.I:</strong> ${usuario.ci}</span>
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
                  <span class="text-truncate"><strong>Email:</strong> ${usuario.correo || 'No registrado'}</span>
                </li>
                <li class="d-flex align-items-center gap-2">
                  <i class="bi bi-gender-ambiguous text-primary"></i>
                  <span><strong>Género:</strong> ${usuario.genero || 'No especificado'}</span>
                </li>
              </ul>
            </div>
            <div class="card-footer bg-white border-top border-light-subtle p-2 d-flex justify-content-end gap-1">`;
            
            if (tipo_usuario == 3) {
              if (usuario.tipo_usuario != 3) {
                templete += `
                  <button class="delete-user btn btn-outline-danger btn-sm rounded-pill px-3" type="button" data-bs-toggle="modal" data-bs-target="#check">
                    <i class="bi bi-trash me-1"></i>Eliminar
                  </button>
                `;
              }
              if (usuario.tipo_usuario == 2) {
                templete += `
                  <button class="ascender btn btn-outline-primary btn-sm rounded-pill px-3" type="button" data-bs-toggle="modal" data-bs-target="#check">
                    <i class="bi bi-shield-check me-1"></i>Ascender
                  </button>
                `;
              }
              if (usuario.tipo_usuario == 1) {
                templete += `
                  <button class="descender btn btn-outline-secondary btn-sm rounded-pill px-3" type="button" data-bs-toggle="modal" data-bs-target="#check">
                    <i class="bi bi-shield-slash me-1"></i>Descender
                  </button>
                `;
              }
            } else {
              if (tipo_usuario == 1 && usuario.tipo_usuario != 1 && usuario.tipo_usuario !== 3) {
                templete += `
                  <button class="delete-user btn btn-outline-danger btn-sm rounded-pill px-3" type="button" data-bs-toggle="modal" data-bs-target="#check">
                    <i class="bi bi-trash me-1"></i>Eliminar
                  </button>
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

  $(document).on('keyup', '#buscar', function() {
    const valor = $(this).val();
    if (valor !== "") {
      buscar_datos(valor);
    } else {
      buscar_datos();
    }
  });

  $('#form-crear').submit(function(e) {
    e.preventDefault();
    const formData = {
      nombre: $('#nombre').val(),
      apellido: $('#apellido').val(),
      edad: $('#edad').val(),
      ci: $('#ci').val(),
      genero: $('#genero').val(),
      pass: $('#pass').val(),
      funcion: 'crear_usuario'
    };
    $.post('../controller/UserController.php', formData, function(response) {
      if (response === 'add') {
        $('#form-crear').trigger('reset');
        Swal.fire({
          position: 'center',
          icon: 'success',
          title: 'Usuario registrado con éxito',
          showConfirmButton: false,
          timer: 1500
        });
        const modalEl = document.getElementById('newuser');
        const modalInstance = bootstrap.Modal.getInstance(modalEl);
        if (modalInstance) modalInstance.hide();
        buscar_datos();
      } else {
        $('#form-crear').trigger('reset');
        Swal.fire({
          position: 'center',
          icon: 'error',
          title: 'Error al registrar usuario',
          text: 'Verifique si la cédula ya se encuentra registrada en el sistema.',
          showConfirmButton: true
        });
        const modalEl = document.getElementById('newuser');
        const modalInstance = bootstrap.Modal.getInstance(modalEl);
        if (modalInstance) modalInstance.hide();
      }
    });
  });

  $(document).on('click', '.ascender', function() {
    const elemento = $(this).closest('[usuarioId]');
    const id = $(elemento).attr('usuarioId');
    funcion = 'ascender';
    $('#id_user_rol').val(id);
    $('#funcion').val(funcion);
  });

  $(document).on('click', '.descender', function() {
    const elemento = $(this).closest('[usuarioId]');
    const id = $(elemento).attr('usuarioId');
    funcion = 'descender';
    $('#id_user_rol').val(id);
    $('#funcion').val(funcion);
  });

  $(document).on('click', '.delete-user', function() {
    const elemento = $(this).closest('[usuarioId]');
    const id = $(elemento).attr('usuarioId');
    funcion = 'delete_user';
    $('#id_user_rol').val(id);
    $('#funcion').val(funcion);
  });

  $('#form-check').submit(function(e) {
    e.preventDefault();
    const pass = $('#oldpass').val();
    const id_usuario = $('#id_user_rol').val();
    funcion = $('#funcion').val();
    
    $.post('../controller/UserController.php', { pass, id_usuario, funcion }, (response) => {
      if (response == 'up' || response == 'donw' || response == 'delete') {
        $('#form-check').trigger('reset');
        Swal.fire({
          position: 'center',
          icon: 'success',
          title: 'Operación realizada con éxito',
          showConfirmButton: false,
          timer: 1500
        });
        const modalEl = document.getElementById('check');
        const modalInstance = bootstrap.Modal.getInstance(modalEl);
        if (modalInstance) modalInstance.hide();
      } else {
        $('#form-check').trigger('reset');
        Swal.fire({
          position: 'center',
          icon: 'error',
          title: 'Error de autenticación',
          text: 'Contraseña incorrecta o permisos insuficientes.',
          showConfirmButton: false,
          timer: 1500
        });
        const modalEl = document.getElementById('check');
        const modalInstance = bootstrap.Modal.getInstance(modalEl);
        if (modalInstance) modalInstance.hide();
      }
      buscar_datos();
    });
  });
});