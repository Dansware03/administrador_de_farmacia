$(document).ready(function() {
    buscar_lab();
    var edit=false;
    $('#form-crear-laboratorio').submit(e => {
        e.preventDefault();
        let nombre_laboratory = $('#nombre-laboratorio').val();
        let id_editado = $('#id_editar_lab').val();
        let funcion = edit ? 'editar' : 'crear';
        $.post('../controller/LaboratoryController.php', { nombre_laboratory, id_editado, funcion }, (response) => {
            $('#form-crear-laboratorio').trigger('reset');
            buscar_lab();
            try {
                const result = JSON.parse(response);
                if (result.status === 'success') {
                    Swal.fire({
                        position: 'center',
                        icon: 'success',
                        title: result.message,
                        showConfirmButton: false,
                        timer: 1000
                    }).then(() => {
                        Swal.close();
                        $('#crear-laboratorio').modal('hide');
                    });
                } else if (result.status === 'error') {
                    Swal.fire({
                        position: 'center',
                        icon: 'error',
                        title: result.message,
                        showConfirmButton: false,
                        timer: 1000
                    }).then(() => {
                        $('#crear-laboratorio').modal('hide');
                        if (result.message === 'Ya existe otro laboratorio con el mismo nombre' && edit) {
                        }
                    });
                }
            } catch (error) {
                console.error('Error al analizar la respuesta JSON', error);
            }
            edit = false;
        });
    });
    function buscar_lab(consulta) {
        $.post('../controller/LaboratoryController.php', { consulta, funcion: 'buscar' }, (Response) => {
            const laboratorios = JSON.parse(Response);
            const laboriosContainer = $('#laboratorios');
            const template = laboratorios.map(laboratorio => `
                <tr labId="${laboratorio.id}" labNombre="${laboratorio.nombre}">
                    <td>${laboratorio.nombre}</td>
                    <td>
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
    $(document).on('click', '.borrar_lab', function (e) {
        const funcion = "borrar_lab";
        const elemento = $(this).closest('tr');
        const id = elemento.attr('labId');
        const nombre = elemento.attr('labNombre');
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
            icon: 'success',
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
                    'Tu Laboratorio ' + nombre + ' está a salvo :)',
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