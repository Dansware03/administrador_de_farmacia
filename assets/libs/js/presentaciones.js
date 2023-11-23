$(document).ready(function() {
    buscar_pre();
    var edit=false;
    $('#form-crear-presentacion').submit(e => {
        e.preventDefault();
        let nombre_pre = $('#nombre-presentacion').val();
        let id_editado = $('#id_editar_presentacion').val();
        let funcion = edit ? 'editar' : 'crear';
        $.post('../controller/PresentacionesController.php', { nombre_pre, id_editado, funcion }, (Response) => {
            $('#form-crear-presentacion').trigger('reset');
            buscar_pre();
            if (Response === 'add' || Response === 'edit') {
                Swal.fire({
                    position: 'center',
                    icon: 'success',
                    title: Response === 'add' ? 'Presentación Creado con Éxito' : 'Cambio Realizado con Éxito',
                    showConfirmButton: false,
                    timer: 1000
                }).then(() => {
                    Swal.close();
                    $('#crear-presentacion').modal('hide');
                });
            } else {
                Swal.fire({
                    position: 'center',
                    icon: 'error',
                    title: 'Error al Realizar',
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    Swal.close();
                    $('#crear-presentacion').modal('hide');
                });
            }
            edit = false;
        });
    });
    function buscar_pre(consulta) {
        $.post('../controller/PresentacionesController.php', { consulta, funcion: 'buscar' }, (Response) => {
            const pres = JSON.parse(Response);pres
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
                    'Tu Presentación ' + nombre + ' imaginario está a salvo :)',
                    'error'
                );
                $('#crear-presentacion').modal('hide');
            }
        });
    });
    $(document).on('click','.editar',(e)=>{
        const elemento = $(this)[0].activeElement.parentElement.parentElement;
        const id = $(elemento).attr('preId');
        const nombre = $(elemento).attr('preNombre');
        $('#id_editar_presentacion').val(id);
        $('#nombre-presentacion').val(nombre);
        edit=true;
    })
});