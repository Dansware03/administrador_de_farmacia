$(document).ready(function() {
    buscar_lab();
    var edit = false;

    // Función para crear o editar laboratorio
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
                Swal.fire({
                    position: 'center',
                    icon: result.status === 'success' ? 'success' : 'error',
                    title: result.message,
                    showConfirmButton: false,
                    timer: 1000
                }).then(() => {
                    $('#crear-laboratorio').modal('hide');
                });
            } catch (error) {
                console.error('Error al analizar la respuesta JSON', error);
            }
            edit = false;
        });
    });

    // Función para buscar laboratorios
    function buscar_lab(consulta = '') {
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

    // Búsqueda en tiempo real
    $(document).on('keyup', '#buscar-laboratory', function () {
        let valor = $(this).val();
        buscar_lab(valor !== "" ? valor : undefined);
    });

    // Eliminación de laboratorio
    $(document).on('click', '.borrar_lab', function (e) {
        const funcion = "borrar_lab";
        const elemento = $(this).closest('tr');
        const id = elemento.attr('labId');
        const nombre = elemento.attr('labNombre');
        
        Swal.fire({
            title: '¿Estás seguro de que deseas eliminar ' + nombre + '?',
            text: '¡No podrás revertir esto!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: '¡Sí, bórralo!',
            cancelButtonText: '¡No, cancela!',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                $.post('../controller/LaboratoryController.php', { id, funcion }, (Response) => {
                    if (Response.status === 'success') {
                        Swal.fire('success', 'Laboratorio ' + nombre + ' eliminado.', 'success');
                        buscar_lab();
                    } else {
                        Swal.fire('Error', 'El laboratorio no se puede eliminar.', 'error');
                        buscar_lab();
                    }
                });
            } else {
                Swal.fire('Cancelado', 'El laboratorio está a salvo :)', 'info');
            }
        });
    });

    // Función para editar laboratorio
    $(document).on('click', '.editar', (e) => {
        const elemento = $(this)[0].activeElement.parentElement.parentElement;
        const id = $(elemento).attr('labId');
        const nombre = $(elemento).attr('labNombre');
        $('#id_editar_lab').val(id);
        $('#nombre-laboratorio').val(nombre);
        edit = true;
    });
});
