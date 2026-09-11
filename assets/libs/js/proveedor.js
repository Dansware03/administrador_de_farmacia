$(document).ready(function () {
    buscar_prov();
    var funcion;
    var edit = false;

    $(document).on('click', '.crearprov', function() {
        $('#form-crear-proveedor').trigger('reset');
        $('#crearProveedorLabel').html('<i class="bi bi-truck me-2"></i>Nuevo Proveedor');
        edit = false;
    });

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

    function buscar_prov(consulta) {
        funcion = "buscar";
        $.post('../controller/ProveedorController.php', { consulta, funcion }, (response) => {
            try {
                const proveedores = JSON.parse(response);
                mostrarProveedores(proveedores);
            } catch (error) {
                console.error("Error al analizar JSON:", error);
            }
        });
    }

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

        const template = proveedores.map(proveedor => {
            const avatarSrc = proveedor.avatar && proveedor.avatar.trim() !== '' ? proveedor.avatar : '../libs/img/proveedors/ProveedorDefault.png';

            return `
                <div provId="${proveedor.id}" provNombre="${proveedor.nombre}" provTelefono="${proveedor.telefono}" provCorreo="${proveedor.correo}" provDireccion="${proveedor.direccion}" provAvatar="${avatarSrc}" class="col-12 col-sm-6 col-md-4 col-xl-3 d-flex align-items-stretch">
                    <div class="card product-card w-100 shadow-sm border-0 d-flex flex-column justify-content-between">
                        <div class="card-body p-4">
                            <div class="text-center mb-3">
                                <img src="${avatarSrc}" alt="${proveedor.nombre}" class="product-avatar mb-2 shadow-sm" onerror="this.src='../libs/img/proveedors/ProveedorDefault.png'">
                                <h5 class="fw-bold text-primary mb-1 text-truncate" title="${proveedor.nombre}">${proveedor.nombre}</h5>
                                <span class="badge bg-light text-secondary border small">#${proveedor.id}</span>
                            </div>

                            <ul class="list-unstyled small text-muted border-top pt-3 mb-0">
                                <li class="mb-2 text-truncate">
                                    <i class="bi bi-telephone text-primary me-2"></i>
                                    <a href="tel:${proveedor.telefono}" class="text-decoration-none text-dark fw-semibold">${proveedor.telefono}</a>
                                </li>
                                <li class="mb-2 text-truncate">
                                    <i class="bi bi-envelope text-primary me-2"></i>
                                    <a href="mailto:${proveedor.correo}" class="text-decoration-none text-secondary">${proveedor.correo || 'Sin correo'}</a>
                                </li>
                                <li class="text-truncate">
                                    <i class="bi bi-geo-alt text-primary me-2"></i>
                                    <span>${proveedor.direccion || 'Sin dirección'}</span>
                                </li>
                            </ul>
                        </div>

                        <div class="card-footer bg-light border-0 p-2 d-flex justify-content-center gap-2">
                            <button class="avatar btn btn-sm btn-outline-info" title="Cambiar Logotipo" type="button" data-bs-toggle="modal" data-bs-target="#cambioavatar">
                                <i class="bi bi-image"></i>
                            </button>
                            <button class="editar btn btn-sm btn-outline-success" title="Editar Proveedor" type="button" data-bs-toggle="modal" data-bs-target="#newproveedor">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="borrar_prov btn btn-sm btn-outline-danger" title="Eliminar Proveedor" type="button">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;
        }).join('');
        proveedorContainer.empty().append(template);
    }

    $(document).on('keyup', '#buscar_proveedor', function () {
        let valor = $(this).val();
        if (valor !== "") {
            buscar_prov(valor);
        } else {
            buscar_prov();
        }
    });

    $(document).on('click', '.avatar', function(e) {
        funcion = "cambiar_logo";
        const elemento = $(this).closest('.d-flex.align-items-stretch');
        const id = $(elemento).attr('provId');
        const nombre = $(elemento).attr('provNombre');
        const avatar = $(elemento).attr('provAvatar');
        $('#funcion').val(funcion);
        $('#id_logo_prod').val(id);
        $('#avatar').val(avatar);
        $('#logoactual1').attr('src', avatar);
        $('#nombre_logo').html(nombre);
    });

    $('#form-logo').submit((e) => {
        e.preventDefault();
        const fileInput = $('#foto')[0];
        if (!fileInput.files || fileInput.files.length === 0) return;

        let formData = new FormData($('#form-logo')[0]);
        $.ajax({
            url: '../controller/ProveedorController.php',
            type: 'POST',
            data: formData,
            cache: false,
            processData: false,
            contentType: false
        }).done(function(response) {
            const json = JSON.parse(response);
            if (json.alert == 'edit') {
                $('#logoactual1').attr('src', json.ruta);
                $('#form-logo').trigger('reset');
                buscar_prov();
                Swal.fire({
                    position: 'center',
                    icon: 'success',
                    title: 'Logotipo Actualizado',
                    showConfirmButton: false,
                    timer: 1000
                }).then(() => {
                    const modalObj = bootstrap.Modal.getInstance(document.getElementById('cambioavatar'));
                    if (modalObj) modalObj.hide();
                });
            } else {
                Swal.fire({
                    position: 'center',
                    icon: 'error',
                    title: 'No se pudo actualizar el logotipo',
                    showConfirmButton: false,
                    timer: 1500
                });
            }
        });
    });

    $(document).on('click', '.editar', function() {
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

    $(document).on('click', '.borrar_prov', function() {
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
                    if (response === 'borrado') {
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
                            title: 'No se puede eliminar (lotes asociados)',
                            showConfirmButton: false,
                            timer: 1500
                        });
                    }
                });
            }
        });
    });
});
