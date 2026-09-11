$(document).ready(function() {
    buscar_type();
    var edit = false;

    $('#form-crear-tipo').submit(function (e) {
        e.preventDefault();
        let nombre_type = $('#nombre-tipo').val();
        let id_editado = $('#id_editar_type').val();
        let funcion = id_editado ? 'editar' : 'crear';

        $.post('../controller/TypeController.php', { nombre_type, id_editado, funcion })
        .done(function (Response) {
            $('#form-crear-tipo').trigger('reset');
            buscar_type();
            if (Response === 'add' || Response === 'edit') {
                Swal.fire({
                    position: 'center',
                    icon: 'success',
                    title: Response === 'add' ? 'Categoría Creada con Éxito' : 'Categoría Actualizada',
                    showConfirmButton: false,
                    timer: 1000
                }).then(() => {
                    const modalObj = bootstrap.Modal.getInstance(document.getElementById('crear-tipo'));
                    if (modalObj) modalObj.hide();
                });
            } else {
                Swal.fire({
                    position: 'center',
                    icon: 'error',
                    title: 'No se pudo guardar la categoría',
                    showConfirmButton: false,
                    timer: 1500
                });
            }
        })
        .always(function () {
            edit = false;
        });
    });

    function buscar_type(consulta) {
        let funcion = "buscar";
        $.post('../controller/TypeController.php', { consulta, funcion }, (Response) => {
            const types = JSON.parse(Response);
            const typeContainer = $('#tipos');
            if (!types || types.length === 0) {
                typeContainer.html(`<tr><td colspan="2" class="text-center py-4 text-muted">No se encontraron categorías.</td></tr>`);
                return;
            }

            const template = types.map(type => `
                <tr typeId="${type.id}" typeNombre="${type.nombre}">
                    <td class="ps-3 fw-semibold text-dark">
                        <i class="bi bi-tag text-primary me-2"></i>${type.nombre}
                    </td>
                    <td class="text-end pe-3">
                        <button class="editar_type btn btn-sm btn-outline-success me-1" title="Editar" type="button" data-bs-toggle="modal" data-bs-target="#crear-tipo">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="borrar_type btn btn-sm btn-outline-danger" title="Eliminar" type="button">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
            `).join('');
            typeContainer.html(template);
        });
    }

    $(document).on('keyup', '#buscar-tipo', function () {
        let valor = $(this).val();
        buscar_type(valor !== "" ? valor : undefined);
    });

    $(document).on('click', '.borrar_type', function () {
        const funcion = "borrar";
        const elemento = $(this).closest('tr');
        const id = elemento.attr('typeId');
        const nombre = elemento.attr('typeNombre');

        Swal.fire({
            title: `¿Eliminar Categoría "${nombre}"?`,
            text: "Esta acción no se puede deshacer.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#64748b',
            confirmButtonText: '<i class="bi bi-trash me-1"></i> Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post('../controller/TypeController.php', { id, funcion }, (response) => {
                    if (response === 'borrado') {
                        Swal.fire({
                            position: 'center',
                            icon: 'success',
                            title: 'Categoría Eliminada',
                            showConfirmButton: false,
                            timer: 1000
                        });
                        buscar_type();
                    } else {
                        Swal.fire({
                            position: 'center',
                            icon: 'error',
                            title: 'No se puede eliminar (insumos asociados)',
                            showConfirmButton: false,
                            timer: 1500
                        });
                    }
                });
            }
        });
    });

    $(document).on('click', '.editar_type', function () {
        const elemento = $(this).closest('tr');
        const id = elemento.attr('typeId');
        const nombre = elemento.attr('typeNombre');
        $('#nombre-tipo').val(nombre);
        $('#id_editar_type').val(id);
        $('#crearTipoLabel').html('<i class="bi bi-pencil-square me-2"></i>Editar Categoría');
        edit = true;
    });
});