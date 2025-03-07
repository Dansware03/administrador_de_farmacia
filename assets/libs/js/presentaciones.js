$(document).ready(function() {
  buscar_pre();
  var edit = false;  // Asegúrate de que la variable edit esté en false por defecto.

  $('#form-crear-presentacion').submit(function (e) {
      e.preventDefault();
      let nombre_pre = $('#nombre-presentacion').val();
      let id_editado = $('#id_editar_presentacion').val();
      let funcion = id_editado ? 'editar' : 'crear'; // Utiliza id_editado para determinar si es edición o creación

      $.post('../controller/PresentacionesController.php', { nombre_pre, id_editado, funcion })
        .done(function (Response) {
          $('#form-crear-presentacion').trigger('reset');
          buscar_pre();

          if (Response === 'add' || Response === 'edit') {
            mostrarMensajeExitoso(Response === 'add' ? 'Presentación Creada con Éxito' : 'Cambio Realizado con Éxito', '#crear-presentacion');
          } else {
            mostrarMensajeError('Error al Realizar', '#crear-presentacion');
          }
        })
        .fail(function () {
          mostrarMensajeError('Error al Realizar', '#crear-presentacion');
        })
        .always(function () {
          edit = false;  // Restablecer el estado de 'edit' después de cada operación.
          $('#id_editar_presentacion').val('');  // Limpiar el campo oculto de edición.
        });
  });

  function mostrarMensajeExitoso(mensaje, modal) {
      Swal.fire({
        position: 'center',
        icon: 'success',
        title: mensaje,
        showConfirmButton: false,
        timer: 1000
      }).then(function () {
        Swal.close();
        $(modal).modal('hide');
      });
  }

  function mostrarMensajeError(mensaje, modal) {
      Swal.fire({
        position: 'center',
        icon: 'error',
        title: mensaje,
        showConfirmButton: false,
        timer: 1500
      }).then(function () {
        Swal.close();
        $(modal).modal('hide');
      });
  }

  // Función para buscar presentaciones
  function buscar_pre(consulta) {
      $.post('../controller/PresentacionesController.php', { consulta, funcion: 'buscar' }, (Response) => {
          const pres = JSON.parse(Response);
          const presContainer = $('#presentaciones');
          const template = pres.map(presentacion => `
              <tr preId="${presentacion.id}" preNombre="${presentacion.nombre}">
                  <td>${presentacion.nombre}</td>
                  <td>
                      <button class="editar btn btn-success" title="Editar Tipo" type="button" data-toggle="modal" data-target="#crear-presentacion"><i class="fas fa-pencil-alt"></i></button>
                      <button class="borrar_pre btn btn-danger" title="Eliminar Tipo" type="button" data-toggle="modal" data-target="#remove-pre"><i class="fas fa-trash"></i></button>
                  </td>
              </tr>
          `).join('');
          presContainer.html(template);
      });
  }

  $(document).on('keyup', '#buscar-presentacion', function () {
      let valor = $(this).val();
      buscar_pre(valor !== "" ? valor : undefined);
  });

  $(document).on('click', '.borrar_pre', function (e) {
      const funcion = "borrar_pre";
      const elemento = $(this).closest('tr');
      const id = elemento.attr('preId');
      const nombre = elemento.attr('preNombre');
      const swalWithBootstrapButtons = Swal.mixin({
          customClass: {
              confirmButton: 'btn btn-success',
              cancelButton: 'btn btn-danger mr-1'
          },
          buttonsStyling: false
      });

      swalWithBootstrapButtons.fire({
          title: '¿Estás seguro de que deseas eliminar ' + nombre + '?',
          text: '¡No podrás revertir esto!',
          showCancelButton: true,
          confirmButtonText: '¡Sí, bórralo!',
          cancelButtonText: '¡No, cancela!',
          reverseButtons: true
      }).then((result) => {
          if (result.isConfirmed) {
              $.post('../controller/PresentacionesController.php', { id, funcion }, (Response) => {
                  if (Response === 'borrado') {
                      swalWithBootstrapButtons.fire(
                          'Eliminado',
                          'Tu Presentación ' + nombre + ' ha sido eliminado.',
                          'success'
                      );
                      buscar_pre();
                  } else {
                      swalWithBootstrapButtons.fire(
                          'Cancelado',
                          'Tu Presentación ' + nombre + ' no fue eliminado porque está siendo usado en un producto.',
                          'error'
                      );
                  }
              });
          } else if (result.dismiss === Swal.DismissReason.cancel) {
              swalWithBootstrapButtons.fire(
                  'Cancelado',
                  'Tu Presentación ' + nombre + ' está a salvo :)',
                  'error'
              );
              $('#crear-presentacion').modal('hide');
          }
      });
  });

  $(document).on('click','.editar', (e) => {
      const elemento = $(this)[0].activeElement.parentElement.parentElement;
      const id = $(elemento).attr('preId');
      const nombre = $(elemento).attr('preNombre');
      $('#id_editar_presentacion').val(id);
      $('#nombre-presentacion').val(nombre);
      edit = true;
  });
});
