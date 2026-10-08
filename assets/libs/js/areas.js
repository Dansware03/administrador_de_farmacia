/**
 * SIMAP - Gestión de Áreas Hospitalarias
 *
 * Administra la interfaz de usuario para el catálogo de áreas y servicios receptores
 * de insumos (Emergencia, Quirófano, Hospitalización, etc.). Orquesta la búsqueda
 * asíncrona en tiempo real, registro, edición modal y eliminación con confirmación.
 *
 * Flujo de Integración Extremo a Extremo:
 * 1. Evento UI: Envío de formulario o click en botones de acción en `adm_area.php`.
 * 2. Petición HTTP: AJAX POST hacia `assets/controller/AreaController.php`.
 * 3. Procesamiento Backend: Verificación de dependencias y consultas preparadas PDO.
 * 4. Respuesta: Alertas modales con SweetAlert2 y refresco dinámico de la tabla.
 *
 * @package SIMAP\Frontend\JS
 * @author Grupo de Proyecto
 * @version 1.0.0
 */

$(document).ready(function() {
    buscar_areas();

    // Registrar o actualizar un área hospitalaria
    $('#form-crear-area').submit(function (e) {
        e.preventDefault();
        let id_area = $('#id_editar_area').val();
        let nombre_area = $('#nombre-area').val().trim();
        let funcion = id_area ? 'editar_area' : 'crear_area';

        $.post('../controller/AreaController.php', { id_area, nombre_area, funcion }, function(response) {
            let res = {};
            try {
                res = JSON.parse(response);
            } catch (err) {
                res = { status: 'error', message: 'Respuesta inválida del servidor' };
            }

            if (res.status === 'success') {
                $('#form-crear-area').trigger('reset');
                $('#id_editar_area').val('');
                buscar_areas();
                const modalObj = bootstrap.Modal.getInstance(document.getElementById('crear-area'));
                if (modalObj) modalObj.hide();

                Swal.fire({
                    icon: 'success',
                    title: id_area ? 'Área Actualizada' : 'Área Creada con Éxito',
                    showConfirmButton: false,
                    timer: 1200
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: res.message || 'No se pudo guardar el área'
                });
            }
        });
    });

    // Consultar áreas con filtro opcional de búsqueda
    function buscar_areas(consulta = '') {
        $.post('../controller/AreaController.php', { consulta, funcion: 'buscar_areas' }, function(response) {
            let areas = [];
            try {
                areas = JSON.parse(response);
            } catch (err) {
                areas = [];
            }

            const container = $('#areas_tabla');
            if (!areas || areas.length === 0) {
                container.html(`<tr><td colspan="2" class="text-center py-4 text-muted">No se encontraron áreas hospitalarias.</td></tr>`);
                return;
            }

            const template = areas.map(area => {
                return `
                <tr areaId="${area.id_area}" areaNombre="${area.nombre_area}">
                    <td class="ps-3 fw-semibold text-dark">
                        <i class="bi bi-hospital text-primary me-2"></i>${area.nombre_area}
                    </td>
                    <td class="text-end pe-3">
                        <button class="editar_area btn btn-sm btn-outline-success me-1" title="Editar" type="button" data-bs-toggle="modal" data-bs-target="#crear-area">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="borrar_area btn btn-sm btn-outline-danger" title="Eliminar" type="button">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>`;
            }).join('');

            container.html(template);
        });
    }

    // Filtrado en vivo al tipear en el buscador
    $(document).on('keyup', '#buscar-area', function () {
        buscar_areas($(this).val());
    });

    // Preparar modal para edición de área existente
    $(document).on('click', '.editar_area', function () {
        const elemento = $(this).closest('tr');
        const id = elemento.attr('areaId');
        const nombre = elemento.attr('areaNombre');

        $('#id_editar_area').val(id);
        $('#nombre-area').val(nombre);
        $('#crearAreaLabel').html('<i class="bi bi-pencil me-2"></i>Editar Área Hospitalaria');
    });

    // Reiniciar modal para registro de nueva área
    $(document).on('click', '[data-bs-target="#crear-area"]', function () {
        if (!$(this).hasClass('editar_area')) {
            $('#form-crear-area').trigger('reset');
            $('#id_editar_area').val('');
            $('#crearAreaLabel').html('<i class="bi bi-hospital me-2"></i>Nueva Área Hospitalaria');
        }
    });

    // Eliminación protegida con verificación de integridad referencial
    $(document).on('click', '.borrar_area', function () {
        const elemento = $(this).closest('tr');
        const id = elemento.attr('areaId');
        const nombre = elemento.attr('areaNombre');

        Swal.fire({
            title: `¿Eliminar "${nombre}"?`,
            text: "No se podrá eliminar si el área ya cuenta con actas de entrega registradas.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post('../controller/AreaController.php', { id_area: id, funcion: 'borrar_area' }, function(response) {
                    let res = {};
                    try {
                        res = JSON.parse(response);
                    } catch (e) {
                        res = { status: 'error', message: 'Error de respuesta' };
                    }

                    if (res.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Eliminada',
                            text: 'El área hospitalaria ha sido eliminada.',
                            showConfirmButton: false,
                            timer: 1200
                        });
                        buscar_areas();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'No se pudo eliminar',
                            text: res.message || 'Error al eliminar área'
                        });
                    }
                });
            }
        });
    });
});
