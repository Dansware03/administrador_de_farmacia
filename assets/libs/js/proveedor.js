/**
 * SIMAP - Gestión de Proveedores y Entidades Donantes
 *
 * Administra el directorio de proveedores comerciales y organismos humanitarios.
 * Maneja el ciclo completo: altas, listado en tarjetas, edición de contactos,
 * subida asíncrona de logos/avatares y eliminación física segura de registros.
 *
 * Flujo de Integración Extremo a Extremo:
 * 1. UI: Tarjetas en `#proveedores` y modal `#newproveedor` en `adm_proveedor.php`.
 * 2. AJAX: POST hacia `assets/controller/ProveedorController.php`.
 * 3. Backend: Procesamiento en modelo `Proveedor` (`crear`, `buscar`, `editar`, `borrar_prove`, `cambiar_avatar`).
 * 4. Respuesta: Feedback SweetAlert2 y actualización de cards y selectores en memoria.
 *
 * @package SIMAP\Frontend\JS
 * @author Grupo de Proyecto
 * @version 1.0.0
 */

$(document).ready(function () {
    buscar_prov();
    var funcion;
    var edit = false;

    // Abrir modal de creación y limpiar campos
    $(document).on('click', '.crearprov', function() {
        $('#form-crear-proveedor').trigger('reset');
        $('#crearProveedorLabel').html('<i class="bi bi-truck me-2"></i>Nuevo Proveedor');
        edit = false;
    });

    // Registrar o actualizar datos del proveedor
    $('#form-crear-proveedor').submit(e => {
        e.preventDefault();
        let nombre = $('#nombre').val();
        let telefono = $('#telefono').val();
        let correo = $('#correo').val();
        let direccion = $('#direccion').val();
        let id_editado = $('#id_editar_prov').val();
        funcion = edit ? 'editar' : 'crear';

        if (!nombre || !telefono) {
            Swal.fire({
                position: 'center',
                icon: 'warning',
                title: 'Nombre y teléfono son obligatorios',
                showConfirmButton: false,
                timer: 1500
            });
            return;
        }

        $.post('../controller/ProveedorController.php', { id_editado, nombre, telefono, correo, direccion, funcion }, (response) => {
            if (response == 'add') {
                $('#form-crear-proveedor').trigger('reset');
                Swal.fire({
                    position: 'center',
                    icon: 'success',
                    title: 'Proveedor Creado con Éxito',
                    showConfirmButton: false,
                    timer: 1000
                }).then(() => {
                    const modalObj = bootstrap.Modal.getInstance(document.getElementById('newproveedor'));
                    if (modalObj) modalObj.hide();
                    buscar_prov();
                });
            } else if (response == 'edit') {
                $('#form-crear-proveedor').trigger('reset');
                Swal.fire({
                    position: 'center',
                    icon: 'success',
                    title: 'Proveedor Actualizado',
                    showConfirmButton: false,
                    timer: 1000
                }).then(() => {
                    const modalObj = bootstrap.Modal.getInstance(document.getElementById('newproveedor'));
                    if (modalObj) modalObj.hide();
                    buscar_prov();
                });
            } else if (response == 'no add') {
                Swal.fire({
                    position: 'center',
                    icon: 'warning',
                    title: 'El proveedor ya existe',
                    showConfirmButton: false,
                    timer: 1500
                });
            } else {
                Swal.fire({
                    position: 'center',
                    icon: 'error',
                    title: 'Ha ocurrido un error',
                    showConfirmButton: false,
                    timer: 1500
                });
            }
        });
    });

    // Consultar lista de proveedores
    function buscar_prov(consulta) {
        funcion = "buscar_prov";
        $.post('../controller/ProveedorController.php', { consulta, funcion }, (response) => {
            try {
                const proveedores = JSON.parse(response);
                mostrarProveedores(proveedores);
            } catch (error) {
                console.error("Error al analizar JSON:", error);
            }
        });
    }

    // Renderizar tarjetas de proveedores
    function mostrarProveedores(proveedores) {
        const proveedorContainer = $('#proveedores');
        if (!proveedores || proveedores.length === 0) {
            proveedorContainer.html(`
                <div class="col-12 text-center py-5">
                    <i class="bi bi-inbox text-muted display-4"></i>
                    <p class="text-muted mt-2">No se encontraron proveedores registrados.</p>
                </div>
            `);
            return;
        }

        const template = proveedores.map(proveedor => `
            <div provId="${proveedor.id_proveedor}" provNombre="${proveedor.nombre}" provTelefono="${proveedor.telefono}" provCorreo="${proveedor.correo}" provDireccion="${proveedor.direccion}" provAvatar="${proveedor.avatar}" class="col-12 col-sm-6 col-md-4 col-xl-3 d-flex align-items-stretch">
                <div class="card product-card w-100 shadow-sm border-0 border-top border-primary border-3 d-flex flex-column justify-content-between">
                    <div class="card-body p-4 text-center">
                        <div class="mb-3 position-relative d-inline-block">
                            <img src="${proveedor.avatar ? '../libs/img/proveedors/' + proveedor.avatar : '../libs/img/proveedors/ProveedorDefault.png'}" 
                                 alt="${proveedor.nombre}" 
                                 class="product-avatar shadow-sm"
                                 onerror="this.src='../libs/img/proveedors/ProveedorDefault.png'">
                            <button class="cambiar_logo btn btn-sm btn-primary rounded-circle position-absolute bottom-0 end-0 p-1" 
                                    style="width: 28px; height: 28px;" 
                                    title="Cambiar Logo" 
                                    type="button" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#cambiologo">
                                <i class="bi bi-camera-fill" style="font-size: 0.75rem;"></i>
                            </button>
                        </div>
                        <h5 class="fw-bold text-primary mb-2 text-truncate" title="${proveedor.nombre}">${proveedor.nombre}</h5>
                        
                        <ul class="list-unstyled small text-muted text-start border-top pt-3 mb-0">
                            <li class="mb-2 text-truncate">
                                <i class="bi bi-telephone text-secondary me-2"></i><b>Teléfono:</b> ${proveedor.telefono || 'N/A'}
                            </li>
                            <li class="mb-2 text-truncate">
                                <i class="bi bi-envelope text-secondary me-2"></i><b>Correo:</b> ${proveedor.correo || 'N/A'}
                            </li>
                            <li class="text-truncate">
                                <i class="bi bi-geo-alt text-secondary me-2"></i><b>Dirección:</b> ${proveedor.direccion || 'N/A'}
                            </li>
                        </ul>
                    </div>

                    <div class="card-footer bg-light border-0 p-2 d-flex justify-content-center gap-2">
                        <button class="editar btn btn-sm btn-outline-success px-3" title="Editar" type="button" data-bs-toggle="modal" data-bs-target="#newproveedor">
                            <i class="bi bi-pencil me-1"></i>Editar
                        </button>
                        <button class="borrar_prove btn btn-sm btn-outline-danger px-3" title="Eliminar" type="button">
                            <i class="bi bi-trash me-1"></i>Eliminar
                        </button>
                    </div>
                </div>
            </div>
        `).join('');

        proveedorContainer.empty().append(template);
    }

    // Búsqueda en vivo
    $(document).on('keyup', '#buscar_proveedor', function () {
        let valor = $(this).val();
        if (valor !== "") {
            buscar_prov(valor);
        } else {
            buscar_prov();
        }
    });

    // Cargar proveedor en el modal de cambio de logo
    $(document).on('click', '.cambiar_logo', function () {
        const elemento = $(this).closest('.d-flex.align-items-stretch');
        const id = $(elemento).attr('provId');
        const nombre = $(elemento).attr('provNombre');
        const avatar = $(elemento).attr('provAvatar');
        $('#logoactual').attr('src', avatar ? '../libs/img/proveedors/' + avatar : '../libs/img/proveedors/ProveedorDefault.png');
        $('#nombre_logo').html(nombre);
        $('#id_logo_prov').val(id);
    });

    // Cargar datos en el formulario de edición
    $(document).on('click', '.editar', function () {
        const elemento = $(this).closest('.d-flex.align-items-stretch');
        const id = $(elemento).attr('provId');
        const nombre = $(elemento).attr('provNombre');
        const telefono = $(elemento).attr('provTelefono');
        const correo = $(elemento).attr('provCorreo');
        const direccion = $(elemento).attr('provDireccion');

        $('#id_editar_prov').val(id);
        $('#nombre').val(nombre);
        $('#telefono').val(telefono);
        $('#correo').val(correo);
        $('#direccion').val(direccion);
        $('#crearProveedorLabel').html('<i class="bi bi-pencil-square me-2"></i>Editar Proveedor');
        edit = true;
    });

    // Eliminar proveedor
    $(document).on('click', '.borrar_prove', function () {
        const elemento = $(this).closest('.d-flex.align-items-stretch');
        const id = $(elemento).attr('provId');
        const nombre = $(elemento).attr('provNombre');
        funcion = 'borrar';

        Swal.fire({
            title: `¿Eliminar Proveedor "${nombre}"?`,
            text: "Esta acción no se puede deshacer.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#64748b',
            confirmButtonText: '<i class="bi bi-trash me-1"></i> Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post('../controller/ProveedorController.php', { id, funcion }, (response) => {
                    if (response == 'borrado') {
                        Swal.fire({
                            position: 'center',
                            icon: 'success',
                            title: 'Proveedor Eliminado',
                            showConfirmButton: false,
                            timer: 1000
                        });
                        buscar_prov();
                    } else {
                        Swal.fire({
                            position: 'center',
                            icon: 'error',
                            title: 'Error al eliminar el proveedor',
                            showConfirmButton: false,
                            timer: 1500
                        });
                    }
                });
            }
        });
    });

    // Subir y actualizar nuevo logo institucional
    $('#form-logo').submit(e => {
        e.preventDefault();
        let formData = new FormData($('#form-logo')[0]);
        $.ajax({
            url: '../controller/ProveedorController.php',
            type: 'POST',
            data: formData,
            cache: false,
            processData: false,
            contentType: false
        }).done(function (response) {
            const json = JSON.parse(response);
            if (json.alert == 'edit') {
                $('#logoactual').attr('src', json.ruta);
                $('#form-logo').trigger('reset');
                Swal.fire({
                    position: 'center',
                    icon: 'success',
                    title: 'Logo Actualizado',
                    showConfirmButton: false,
                    timer: 1000
                }).then(() => {
                    const modalObj = bootstrap.Modal.getInstance(document.getElementById('cambiologo'));
                    if (modalObj) modalObj.hide();
                    buscar_prov();
                });
            } else {
                Swal.fire({
                    position: 'center',
                    icon: 'error',
                    title: 'Error al actualizar el logo',
                    showConfirmButton: false,
                    timer: 1500
                });
            }
        });
    });
});
