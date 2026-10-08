/**
 * SIMAP - Gestión de Fabricantes y Laboratorios Farmacéuticos
 *
 * Administra el catálogo de marcas y laboratorios farmacéuticos productores.
 * Implementa renderizado dinámico de filas, búsqueda en vivo, soporte modal para
 * alta y edición, y confirmaciones de eliminación protegidas contra dependencias.
 *
 * Flujo de Integración Extremo a Extremo:
 * 1. UI: Formulario modal `#form-crear-laboratorio` o tabla `#laboratorios`.
 * 2. AJAX: POST hacia `assets/controller/LaboratoryController.php`.
 * 3. Backend: Procesamiento de operaciones en `Laboratorio` (`crear`, `editar`, `borrar_lab`).
 * 4. Salida: Notificaciones visuales y actualización sin recarga del DOM.
 *
 * @package SIMAP\Frontend\JS
 * @author Grupo de Proyecto
 * @version 1.0.0
 */

$(document).ready(function() {
    buscar_lab();
    var edit = false;

    // Registrar o actualizar un laboratorio
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
                    const modalObj = bootstrap.Modal.getInstance(document.getElementById('crear-laboratorio'));
                    if (modalObj) modalObj.hide();
                });
            } catch (error) {
                console.error('Error al analizar respuesta JSON:', error);
            }
            edit = false;
        });
    });

    // Listar laboratorios con búsqueda reactiva
    function buscar_lab(consulta = '') {
        $.post('../controller/LaboratoryController.php', { consulta, funcion: 'buscar' }, (Response) => {
            const laboratorios = JSON.parse(Response);
            const laboriosContainer = $('#laboratorios');
            if (!laboratorios || laboratorios.length === 0) {
                laboriosContainer.html(`<tr><td colspan="2" class="text-center py-4 text-muted">No se encontraron laboratorios.</td></tr>`);
                return;
            }

            const template = laboratorios.map(laboratorio => `
                <tr labId="${laboratorio.id}" labNombre="${laboratorio.nombre}">
                    <td class="ps-3 fw-semibold text-dark">
                        <i class="bi bi-building text-primary me-2"></i>${laboratorio.nombre}
                    </td>
                    <td class="text-end pe-3">
                        <button class="editar btn btn-sm btn-outline-success me-1" title="Editar" type="button" data-bs-toggle="modal" data-bs-target="#crear-laboratorio">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="borrar_lab btn btn-sm btn-outline-danger" title="Eliminar" type="button">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
            `).join('');
            laboriosContainer.html(template);
        });
    }

    // Evento de búsqueda por teclado
    $(document).on('keyup', '#buscar-laboratory', function () {
        let valor = $(this).val();
        buscar_lab(valor !== "" ? valor : undefined);
    });

    // Eliminación con diálogo de confirmación y chequeo de insumos vinculados
    $(document).on('click', '.borrar_lab', function () {
        const funcion = "borrar_lab";
        const elemento = $(this).closest('tr');
        const id = elemento.attr('labId');
        const nombre = elemento.attr('labNombre');

        Swal.fire({
            title: `¿Eliminar Laboratorio "${nombre}"?`,
            text: "Esta acción no se puede deshacer.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#64748b',
            confirmButtonText: '<i class="bi bi-trash me-1"></i> Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post('../controller/LaboratoryController.php', { id, funcion }, (response) => {
                    if (response === 'borrado') {
                        Swal.fire({
                            position: 'center',
                            icon: 'success',
                            title: 'Laboratorio Eliminado',
                            showConfirmButton: false,
                            timer: 1000
                        });
                        buscar_lab();
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

    // Cargar datos en el formulario para edición
    $(document).on('click', '.editar', function () {
        const elemento = $(this).closest('tr');
        const id = elemento.attr('labId');
        const nombre = elemento.attr('labNombre');
        $('#nombre-laboratorio').val(nombre);
        $('#id_editar_lab').val(id);
        $('#crearLabLabel').html('<i class="bi bi-pencil-square me-2"></i>Editar Laboratorio');
        edit = true;
    });
});
