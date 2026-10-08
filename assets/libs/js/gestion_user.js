/**
 * SIMAP - Administración y Control de Acceso de Usuarios
 *
 * Administra el panel de gestión de operadores y personal asistencial (`adm_usuario.php`):
 * - Consulta asíncrona de usuarios con filtros en tiempo real.
 * - Registro de nuevos usuarios con rol asignado y avatar predeterminado.
 * - Ascenso a Administrador y descenso a Secretario con confirmación de credenciales.
 * - Eliminación segura de cuentas con validación de contraseña maestra.
 * - Aplicación estricta de reglas de negocio en la interfaz:
 *   * Bloqueo de auto-descenso y auto-eliminación.
 *   * Protección visual para preservar al último administrador activo del sistema.
 *
 * @package SIMAP\Frontend\JS
 * @author Grupo de Proyecto
 * @version 1.0.0
 */

$(document).ready(function() {
  let funcion;
  const tipo_usuario = $('#tipo_usuario').val();
  const id_usuario_actual = $('#id_usuario_actual').val();
  
  if (tipo_usuario == 2) {
    $('#button_crear').hide();
  }
  
  buscar_datos();

  // Consultar usuarios y renderizar tarjetas interactivas
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
            
            // Reglas de negocio: si es la propia cuenta, bloquear degradación y eliminación
            if (esUsuarioActual) {
              templete += `
                <span class="text-muted small px-2 py-1"><i class="bi bi-lock me-1"></i>Cuenta en uso</span>
              `;
            } else if (tipo_usuario == 1 && usuario.tipo_usuario == 2) {
              // Usuario Secretario: se permite ascender o eliminar
              templete += `
                <button class="delete-user btn btn-outline-danger btn-sm rounded-pill px-3" type="button" data-bs-toggle="modal" data-bs-target="#check">
                  <i class="bi bi-trash me-1"></i>Eliminar
                </button>
                <button class="ascender btn btn-outline-primary btn-sm rounded-pill px-3" type="button" data-bs-toggle="modal" data-bs-target="#confirmar">
                  <i class="bi bi-arrow-up-circle me-1"></i>Ascender
                </button>
              `;
            } else if (tipo_usuario == 1 && usuario.tipo_usuario == 1) {
              // Usuario Administrador alternativo: se permite descender o eliminar
              templete += `
                <button class="descender btn btn-outline-secondary btn-sm rounded-pill px-3" type="button" data-bs-toggle="modal" data-bs-target="#confirmar">
                  <i class="bi bi-arrow-down-circle me-1"></i>Descender
                </button>
                <button class="delete-user btn btn-outline-danger btn-sm rounded-pill px-3" type="button" data-bs-toggle="modal" data-bs-target="#check">
                  <i class="bi bi-trash me-1"></i>Eliminar
                </button>
              `;
            }

            templete += `
            </div>
          </div>
        </div>
        `;
      });
      $('#usuarios').html(templete);
    });
  }

  // Filtrado de usuarios por búsqueda en vivo
  $(document).on('keyup', '#buscar', function() {
    let valor = $(this).val();
    if (valor != '') {
      buscar_datos(valor);
    } else {
      buscar_datos();
    }
  });

  // Registro de nuevo usuario
  $('#form-crear').submit(e => {
    e.preventDefault();
    let nombre = $('#nombre').val();
    let apellido = $('#apellido').val();
    let edad = $('#edad').val();
    let ci = $('#ci').val();
    let pass = $('#pass').val();
    let genero = $('#genero').val();
    funcion = 'crear_usuario';

    $.post('../controller/UserController.php', { nombre, apellido, edad, ci, pass, genero, funcion }, (Response) => {
      if (Response == 'add') {
        $('#form-crear').trigger('reset');
        Swal.fire({
          position: 'center',
          icon: 'success',
          title: 'Usuario Registrado con Éxito',
          showConfirmButton: false,
          timer: 1000
        }).then(() => {
          const modalObj = bootstrap.Modal.getInstance(document.getElementById('crearusuario'));
          if (modalObj) modalObj.hide();
          buscar_datos();
        });
      } else {
        Swal.fire({
          position: 'center',
          icon: 'error',
          title: 'La cédula ya está registrada',
          showConfirmButton: false,
          timer: 1500
        });
      }
    });
  });

  // Preparar modal para ascender usuario
  $(document).on('click', '.ascender', (e) => {
    const elemento = $(this).closest('[usuarioId]');
    const id = $(elemento).attr('usuarioId');
    funcion = 'ascender';
    $('#id_user').val(id);
    $('#funcion').val(funcion);
    $('#pass-conf').val('');
  });

  // Preparar modal para descender usuario
  $(document).on('click', '.descender', (e) => {
    const elemento = $(this).closest('[usuarioId]');
    const id = $(elemento).attr('usuarioId');
    funcion = 'descender';
    $('#id_user').val(id);
    $('#funcion').val(funcion);
    $('#pass-conf').val('');
  });

  // Preparar modal para eliminar usuario
  $(document).on('click', '.delete-user', (e) => {
    const elemento = $(this).closest('[usuarioId]');
    const id = $(elemento).attr('usuarioId');
    funcion = 'borrar_usuario';
    $('#id_user_del').val(id);
    $('#funcion_del').val(funcion);
    $('#pass-check').val('');
  });

  // Procesar ascenso o descenso de rol
  $('#form-confirmar').submit(e => {
    e.preventDefault();
    let pass = $('#pass-conf').val();
    let id_usuario = $('#id_user').val();
    funcion = $('#funcion').val();

    $.post('../controller/UserController.php', { pass, id_usuario, funcion }, (Response) => {
      const modalObj = bootstrap.Modal.getInstance(document.getElementById('confirmar'));
      if (modalObj) modalObj.hide();
      $('#form-confirmar').trigger('reset');

      if (Response == 'ascendido' || Response == 'descendido') {
        Swal.fire({
          position: 'center',
          icon: 'success',
          title: (funcion == 'ascender') ? 'Usuario Ascendido a Administrador' : 'Usuario Descendido a Secretario',
          showConfirmButton: false,
          timer: 1000
        });
        buscar_datos();
      } else if (Response == 'self-downgrade') {
        Swal.fire({
          position: 'center',
          icon: 'warning',
          title: 'Acción Bloqueada',
          text: 'No puedes degradar tu propia cuenta de Administrador.',
          showConfirmButton: true
        });
      } else if (Response == 'last-admin') {
        Swal.fire({
          position: 'center',
          icon: 'error',
          title: 'Acción No Permitida',
          text: 'Debe existir al menos un Administrador activo en el sistema.',
          showConfirmButton: true
        });
      } else {
        Swal.fire({
          position: 'center',
          icon: 'error',
          title: 'Contraseña Incorrecta',
          showConfirmButton: false,
          timer: 1500
        });
      }
    });
  });

  // Procesar eliminación de cuenta
  $('#form-check').submit(e => {
    e.preventDefault();
    let pass = $('#pass-check').val();
    let id_usuario = $('#id_user_del').val();
    funcion = $('#funcion_del').val();

    $.post('../controller/UserController.php', { pass, id_usuario, funcion }, (Response) => {
      const modalObj = bootstrap.Modal.getInstance(document.getElementById('check'));
      if (modalObj) modalObj.hide();
      $('#form-check').trigger('reset');

      if (Response == 'borrado') {
        Swal.fire({
          position: 'center',
          icon: 'success',
          title: 'Usuario Eliminado',
          showConfirmButton: false,
          timer: 1000
        });
        buscar_datos();
      } else if (Response == 'self-delete') {
        Swal.fire({
          position: 'center',
          icon: 'warning',
          title: 'Acción Bloqueada',
          text: 'No puedes eliminar tu propia cuenta en sesión.',
          showConfirmButton: true
        });
      } else if (Response == 'last-admin') {
        Swal.fire({
          position: 'center',
          icon: 'error',
          title: 'Acción No Permitida',
          text: 'No puedes eliminar al único Administrador activo del sistema.',
          showConfirmButton: true
        });
      } else {
        Swal.fire({
          position: 'center',
          icon: 'error',
          title: 'Contraseña Incorrecta',
          showConfirmButton: false,
          timer: 1500
        });
      }
    });
  });
});