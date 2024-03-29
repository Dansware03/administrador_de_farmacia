$(document).ready(function() {
    buscar_type();
    var edit=false;
    $('#form-crear-tipo').submit(function (e) {
        e.preventDefault();
        let nombre_type = $('#nombre-tipo').val();
        let id_editado = $('#id_editar_type').val();
        let funcion = id_editado ? 'editar' : 'crear'; // Utilizar id_editado para determinar si es edición o creació
        $.post('../controller/TypeController.php', { nombre_type, id_editado, funcion })
        .done(function (Response) {
            $('#form-crear-tipo').trigger('reset');
            buscar_type();
            if (Response === 'add' || Response === 'edit') {
                mostrarMensajeExitoso(Response === 'add' ? 'Tipo Creado con Éxito' : 'Cambio Realizado con Éxito', '#crear-tipo');
            } else {
                mostrarMensajeError('Error al Realizar', '#crear-tipo');
            }
        })
        .fail(function () {
            mostrarMensajeError('Error al Realizar', '#crear-tipo');
        })
        .always(function () {
            edit = false;
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
    function buscar_type(consulta) {
        $.post('../controller/TypeController.php', { consulta, funcion: 'buscar' }, (Response) => {
            const types = JSON.parse(Response);types
            const typesContainer = $('#tipos');
            const template = types.map(tipo_producto => `
                <tr typId="${tipo_producto.id}" typNombre="${tipo_producto.nombre}">
                    <td>${tipo_producto.nombre}</td>
                    <td>
                        <button class="editar btn btn-success" title="Editar Tipo" type="button" data-toggle="modal" data-target="#crear-tipo"><i class="fas fa-pencil-alt"></i></button>
                        <button class="borrar_type btn btn-danger" title="Eliminar Tipo" type="button" data-toggle="modal" data-target="#remove-type"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
            `).join('');
            typesContainer.html(template);
        });
    }
    $(document).on('keyup', '#buscar-tipo', function () {
        let valor = $(this).val();
        buscar_type(valor !== "" ? valor : undefined);
    });
    $(document).on('click', '.borrar_type', function (e) {
        const funcion = "borrar_type";
        const elemento = $(this).parent().parent();
        const id = $(elemento).attr('typId');
        const nombre = $(elemento).attr('typNombre');
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
                $.post('../controller/TypeController.php', { id, funcion }, (Response) => {
                    if (Response === 'borrado') {
                        swalWithBootstrapButtons.fire(
                            'Eliminado',
                            'Tu Tipo ' + nombre + ' ha sido eliminado.',
                            'success'
                        );
                        buscar_type();
                    } else {
                        swalWithBootstrapButtons.fire(
                            'Cancelado',
                            'Tu Tipo ' + nombre + ' no fue eliminado porque está siendo usado en un producto.',
                            'error'
                        );
                    }
                });
            } else if (result.dismiss === Swal.DismissReason.cancel) {
                swalWithBootstrapButtons.fire(
                    'Cancelado',
                    'Tu Tipo ' + nombre + ' imaginario está a salvo :)',
                    'error'
                );
                $('#crear-tipo').modal('hide');
            }
        });
    });
    $(document).on('click','.editar',(e)=>{
        const elemento = $(this)[0].activeElement.parentElement.parentElement;
        const id = $(elemento).attr('typId');
        const nombre = $(elemento).attr('typNombre');
        $('#id_editar_type').val(id);
        $('#nombre-tipo').val(nombre);
        edit=true;
    })
});