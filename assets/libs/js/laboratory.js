$(document).ready(function() {
    buscar_lab();
    var edit=false;
    $('#form-crear-laboratorio').submit(e => {
        e.preventDefault();
        let nombre_laboratory = $('#nombre-laboratorio').val();
        let id_editado = $('#id_editar_lab').val();
        let funcion = edit ? 'editar' : 'crear';
        $.post('../controller/LaboratoryController.php', { nombre_laboratory, id_editado, funcion }, (Response) => {
            $('#form-crear-laboratorio').trigger('reset');
            buscar_lab();
            if (Response === 'add' || Response === 'edit') {
                Swal.fire({
                    position: 'center',
                    icon: 'success',
                    title: Response === 'add' ? 'Laboratorio Creado con Éxito' : 'Cambio Realizado con Éxito',
                    showConfirmButton: false,
                    timer: 1000
                }).then(() => {
                    Swal.close();
                    $('#crear-laboratorio').modal('hide');
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
                    $('#crear-laboratorio').modal('hide');
                });
            }
            edit = false;
        });
    });
    function buscar_lab(consulta) {
        $.post('../controller/LaboratoryController.php', { consulta, funcion: 'buscar' }, (Response) => {
            const laboratorios = JSON.parse(Response);
            const laboriosContainer = $('#laboratorios');
            const template = laboratorios.map(laboratorio => `
                <tr labId="${laboratorio.id}" labNombre="${laboratorio.nombre}" labAvatar="${laboratorio.avatar}">
                    <td>${laboratorio.nombre}</td>
                    <td>
                        <img class="img-fluid rounded" width="70" height="70" src="${laboratorio.avatar}" alt="">
                    </td>
                    <td>
                        <button class="avatar btn btn-info" title="Cambiar Logo" type="button" data-toggle="modal" data-target="#cambiar-foto-lab"><i class="far fa-image"></i></button>
                        <button class="editar btn btn-success" title="Editar Laboratorio" type="button" data-toggle="modal" data-target="#crear-laboratorio"><i class="fas fa-pencil-alt"></i></button>
                        <button class="borrar_lab btn btn-danger" title="Eliminar Laboratorio" type="button" data-toggle="modal" data-target="#remove-lab"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
            `).join('');
            laboriosContainer.html(template);
        });
    }
    $(document).on('keyup', '#buscar-laboratory', function () {
        let valor = $(this).val();
        buscar_lab(valor !== "" ? valor : undefined);
    });
    $(document).on('click', '.avatar', function (e) {
        const funcion = "cambiar_logo";
        const elemento = $(this).closest('tr');
        const id = elemento.attr('labId');
        const nombre = elemento.attr('labNombre');
        const avatar = elemento.attr('labAvatar');
        $('#lab-actual').attr('src', avatar);
        $('#nombre_logo').html(nombre);
        $('#id_logo_lab').val(id);
    });
    $('#form-cambiar-foto-laboratorio').submit(e => {
        e.preventDefault();
        const formData = new FormData(document.getElementById('form-cambiar-foto-laboratorio'));
        $.ajax({
            url: '../controller/LaboratoryController.php',
            type: 'POST',
            data: formData,
            cache: false,
            processData: false,
            contentType: false
        }).done(function (Response) {
            const json = JSON.parse(Response);
            if (json.alert === 'edit') {
                $('#lab-actual').attr('src', json.ruta);
                buscar_lab();
                $('#form-cambiar-foto-laboratorio').trigger('reset');
                Swal.fire({
                    position: 'center',
                    icon: 'success',
                    title: 'Avatar Cambiado con Éxito',
                    showConfirmButton: false,
                    timer: 1500
                });
            } else {
                $('#form-cambiar-foto-laboratorio').trigger('reset');
                Swal.fire({
                    position: 'center',
                    icon: 'error',
                    title: 'Error al Subir Imagen',
                    showConfirmButton: false,
                    timer: 1500
                });
            }
        });
    });
    $(document).on('click', '.borrar_lab', function (e) {
        const funcion = "borrar_lab";
        const elemento = $(this).closest('tr');
        const id = elemento.attr('labId');
        const nombre = elemento.attr('labNombre');
        const avatar = elemento.attr('labAvatar');
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
            imageUrl: avatar,
            imageWidth: 100,
            imageHeight: 100,
            showCancelButton: true,
            confirmButtonText: '¡Sí, bórralo!',
            cancelButtonText: '¡No, cancela!',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                $.post('../controller/LaboratoryController.php', { id, funcion }, (Response) => {
                    if (Response === 'borrado') {
                        swalWithBootstrapButtons.fire(
                            'Eliminado',
                            'Tu Laboratorio ' + nombre + ' ha sido eliminado.',
                            'success'
                        );
                        buscar_lab();
                    } else {
                        swalWithBootstrapButtons.fire(
                            'Cancelado',
                            'Tu Laboratorio ' + nombre + ' no fue eliminado porque está siendo usado en un producto.',
                            'error'
                        );
                    }
                });
            } else if (result.dismiss === Swal.DismissReason.cancel) {
                swalWithBootstrapButtons.fire(
                    'Cancelado',
                    'Tu Laboratorio ' + nombre + ' imaginario está a salvo :)',
                    'error'
                );
            }
        });
    });
    $(document).on('click','.editar',(e)=>{
        const elemento = $(this)[0].activeElement.parentElement.parentElement;
        const id = $(elemento).attr('labId');
        const nombre = $(elemento).attr('labNombre');
        $('#id_editar_lab').val(id);
        $('#nombre-laboratorio').val(nombre);
        edit=true;
    })
});