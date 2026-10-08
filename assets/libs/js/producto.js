$(document).ready(function() {
    var funcion;
    var edit = false;
    buscar_product();

    $('.select2').select2({
        dropdownParent: $('#crearproducto')
    });

    rellenar_proveedor();
    rellenar_laboratorio();
    rellenar_type();
    rellenar_presentacion();
    rellenar_unidad_medida();

    function rellenar_unidad_medida() {
        $.post('../controller/AreaController.php', { funcion: 'cargar_unidades' })
            .done(function(response) {
                const unidades = JSON.parse(response);
                const opciones = unidades.map(u => `<option value="${u.id_unidad}">${u.nombre} (${u.codigo})</option>`);
                $('#unidad_medida').html(opciones.join(''));
            })
            .fail(function(error) {
                console.error("Error al cargar unidades de medida:", error);
            });
    }

    function rellenar_proveedor() {
        const funcion = "rellenar_proveedor";
        $.post('../controller/ProveedorController.php', { funcion })
            .done(function (response) {
                const proveedores = JSON.parse(response);
                const opciones = proveedores.map(proveedor => `<option value="${proveedor.id}">${proveedor.nombre}</option>`);
                $('#proveedor').html(opciones.join(''));
            })
            .fail(function (error) {
                console.error("Error en rellenar_proveedor:", error);
            });
    }

    function rellenar_laboratorio() {
        const funcion = "rellenar_laboratorio";
        $.post('../controller/LaboratoryController.php', { funcion })
            .done(function (response) {
                const laboratorios = JSON.parse(response);
                const opciones = laboratorios.map(laboratorio => `<option value="${laboratorio.id}">${laboratorio.nombre}</option>`);
                $('#laboratorio').html(opciones.join(''));
            })
            .fail(function (error) {
                console.error("Error en rellenar_laboratorio:", error);
            });
    }

    function rellenar_type() {
        const funcion = "rellenar_type";
        $.post('../controller/TypeController.php', { funcion })
            .done(function(response) {
                const types = JSON.parse(response);
                let template = '';
                types.forEach((type) => {
                    template += `<option value="${type.id}">${type.nombre}</option>`;
                });
                $('#tipo').html(template);
            })
            .fail(function(error) {
                console.error("Error en rellenar_type:", error);
            });
    }

    function rellenar_presentacion() {
        const funcion = "rellenar_presentacion";
        $.post('../controller/PresentacionesController.php', { funcion })
            .done(function(response) {
                const presentaciones = JSON.parse(response);
                let template = '';
                presentaciones.forEach((presentacion) => {
                    template += `<option value="${presentacion.id}">${presentacion.nombre}</option>`;
                });
                $('#presentacion').html(template);
            })
            .fail(function(error) {
                console.error("Error en rellenar_presentacion:", error);
            });
    }

    $(document).on('click', '.crearpd', function() {
        $('#form-crear-producto').trigger('reset');
        $('#noVenta').prop('checked', false);
        $('#precio').prop('disabled', false).val('');
        $('#crearProductoLabel').html('<i class="bi bi-box-seam me-2"></i>Nuevo Insumo');
        $('#id_edit_prod').val('');
        $('#especificacion_talla').val('');
        edit = false;
    });

    $('#form-crear-producto').submit(e => {
        e.preventDefault();
        let id_edit_prod = $('#id_edit_prod').val();
        let nombre = $('#nombre-producto').val();
        let especificacion_talla = $('#especificacion_talla').val();
        let concentracion = especificacion_talla;
        let id_unidad = $('#unidad_medida').val();
        let adicional = $('#adicional').val();
        let precio = 0;
        let prod_lab = $('#laboratorio').val();
        let prod_tip_prod = $('#tipo').val();
        let prod_present = $('#presentacion').val();

        if (edit === true) {
            funcion = "editar";
        } else {
            funcion = "crear";
        }

        $.post(
            '../controller/ProductoController.php',
            { funcion, id_edit_prod, nombre, concentracion, adicional, precio, prod_lab, prod_tip_prod, prod_present, id_unidad, especificacion_talla }
        )
        .done(response => {
            if (response === 'add') {
                Swal.fire({
                    position: 'center',
                    icon: 'success',
                    title: 'Insumo Creado con Éxito',
                    showConfirmButton: false,
                    timer: 1000
                }).then(() => {
                    const modalObj = bootstrap.Modal.getInstance(document.getElementById('crearproducto'));
                    if (modalObj) modalObj.hide();
                    $('#form-crear-producto').trigger('reset');
                    buscar_product();
                    edit = false;
                });
            } else if (response === 'edit') {
                Swal.fire({
                    position: 'center',
                    icon: 'success',
                    title: 'Insumo Editado con Éxito',
                    showConfirmButton: false,
                    timer: 1000
                }).then(() => {
                    const modalObj = bootstrap.Modal.getInstance(document.getElementById('crearproducto'));
                    if (modalObj) modalObj.hide();
                    $('#form-crear-producto').trigger('reset');
                    buscar_product();
                    edit = false;
                });
            } else {
                Swal.fire({
                    position: 'center',
                    icon: 'error',
                    title: response,
                    showConfirmButton: true,
                    timer: 1500
                });
                edit = false;
            }
        })
        .fail(error => {
            console.error("Error en AJAX:", error);
            Swal.fire({
                position: 'center',
                icon: 'error',
                title: 'Error en la solicitud AJAX',
                showConfirmButton: false,
                timer: 1500
            });
        });
    });

    function buscar_product(consulta) {
        $.post('../controller/ProductoController.php', { consulta, funcion: 'buscar_product' }, (Response) => {
            try {
                const products = JSON.parse(Response);
                mostrarProductos(products);
            } catch (error) {
                console.error("Error al analizar JSON:", error);
            }
        });
    }

    function mostrarProductos(products) {
        const productContainer = $('#productos');
        if (!products || products.length === 0) {
            productContainer.html(`
                <div class="col-12 text-center py-5">
                    <i class="bi bi-inbox text-muted display-4"></i>
                    <p class="text-muted mt-2">No se encontraron insumos registrados.</p>
                </div>
            `);
            return;
        }

        const template = products.map(product => {
            const stockNum = parseInt(product.stock) || 0;
            let stockBadge = 'bg-success';
            if (stockNum <= 5) {
                stockBadge = 'bg-danger';
            } else if (stockNum <= 15) {
                stockBadge = 'bg-warning text-dark';
            }

            const avatarSrc = product.avatar && product.avatar.trim() !== '' ? product.avatar : '../libs/img/product/prod_default.png';
            const unidadTexto = product.unidad_medida ? `${product.unidad_medida} (${product.unidad_codigo})` : 'Unidad (und)';
            const especTexto = product.especificacion_talla || product.concentracion || 'Sin especificación';

            return `
                <div proId="${product.id}" proNombre="${product.nombre}" conNombre="${product.concentracion}" addNombre="${product.adicional}" preNombre="${product.precio}" nLabNombre="${product.laboratorio_id}" nTypeNombre="${product.tipo_id}" nPreNombre="${product.presentacion_id}" idUnidad="${product.id_unidad || 1}" espTalla="${product.especificacion_talla || ''}" avaNombre="${avatarSrc}" class="col-12 col-sm-6 col-md-4 col-xl-3 d-flex align-items-stretch">
                    <div class="card product-card w-100 shadow-sm border-0 d-flex flex-column justify-content-between">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="badge ${stockBadge} rounded-pill px-3 py-2 small">
                                    <i class="bi bi-boxes me-1"></i>Stock: ${product.stock}
                                </span>
                                <span class="badge bg-light text-primary border small fw-semibold">
                                    ${product.unidad_codigo || 'und'}
                                </span>
                            </div>

                            <div class="text-center mb-3">
                                <img src="${avatarSrc}" alt="${product.nombre}" class="product-avatar mb-2 shadow-sm" onerror="this.src='../libs/img/product/prod_default.png'">
                                <h5 class="fw-bold text-primary mb-1 text-truncate" title="${product.nombre}">${product.nombre}</h5>
                                <span class="badge bg-light text-secondary border small">Cód: #${product.id}</span>
                            </div>

                            <ul class="list-unstyled small text-muted border-top pt-3 mb-0">
                                <li class="mb-1 text-truncate"><i class="bi bi-rulers text-secondary me-2"></i><b>Unidad:</b> ${unidadTexto}</li>
                                <li class="mb-1 text-truncate"><i class="bi bi-tag text-secondary me-2"></i><b>Talla / Espec.:</b> ${especTexto}</li>
                                <li class="mb-1 text-truncate"><i class="bi bi-building text-secondary me-2"></i><b>Fabricante:</b> ${product.nombre_laboratorio || 'N/A'}</li>
                                <li class="mb-1 text-truncate"><i class="bi bi-collection text-secondary me-2"></i><b>Categoría:</b> ${product.tipo || 'N/A'}</li>
                                <li class="text-truncate"><i class="bi bi-box-seam text-secondary me-2"></i><b>Empaque:</b> ${product.nombre_presentacion || 'N/A'}</li>
                            </ul>
                        </div>

                        <div class="card-footer bg-light border-0 p-2 d-flex justify-content-center gap-1">
                            <button class="imagen btn btn-sm btn-outline-info" title="Cambiar Imagen" type="button" data-bs-toggle="modal" data-bs-target="#cambiarlogo">
                                <i class="bi bi-image"></i>
                            </button>
                            <button class="editar btn btn-sm btn-outline-success" title="Editar Insumo" type="button" data-bs-toggle="modal" data-bs-target="#crearproducto">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="lote btn btn-sm btn-outline-primary" title="Asignar Lote" type="button" data-bs-toggle="modal" data-bs-target="#crearlote">
                                <i class="bi bi-plus-square"></i>
                            </button>
                            <button class="borrar_produts btn btn-sm btn-outline-danger" title="Eliminar Insumo" type="button">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;
        }).join('');
        productContainer.empty().append(template);
    }

    $(document).on('keyup', '#buscar_producto', function () {
        let valor = $(this).val();
        if (valor !== "") {
            buscar_product(valor);
        } else {
            buscar_product();
        }
    });

    $(document).on('click', '.imagen', function(e) {
        funcion = "cambiar_avatar";
        const elemento = $(this).closest('.d-flex.align-items-stretch');
        const id = $(elemento).attr('proId');
        const avatar = $(elemento).attr('avaNombre');
        const nombre = $(elemento).attr('proNombre');
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
            url: '../controller/ProductoController.php',
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
                buscar_product();
                Swal.fire({
                    position: 'center',
                    icon: 'success',
                    title: 'Imagen Actualizada',
                    showConfirmButton: false,
                    timer: 1000
                }).then(() => {
                    const modalObj = bootstrap.Modal.getInstance(document.getElementById('cambiarlogo'));
                    if (modalObj) modalObj.hide();
                });
            } else {
                Swal.fire({
                    position: 'center',
                    icon: 'error',
                    title: 'No se pudo actualizar la imagen',
                    showConfirmButton: false,
                    timer: 1500
                });
            }
        });
    });

    $(document).on('click', '.editar', function() {
        const elemento = $(this).closest('.d-flex.align-items-stretch');
        const id = $(elemento).attr('proId');
        const nombre = $(elemento).attr('proNombre');
        const concentracion = $(elemento).attr('conNombre');
        const adicional = $(elemento).attr('addNombre');
        const precio = $(elemento).attr('preNombre');
        const laboratorio = $(elemento).attr('nLabNombre');
        const tipo = $(elemento).attr('nTypeNombre');
        const presentacion = $(elemento).attr('nPreNombre');
        const id_unidad = $(elemento).attr('idUnidad') || 1;
        const esp_talla = $(elemento).attr('espTalla') || concentracion || '';

        $('#id_edit_prod').val(id);
        $('#nombre-producto').val(nombre);
        $('#especificacion_talla').val(esp_talla);
        $('#unidad_medida').val(id_unidad).trigger('change');
        $('#concentracion').val(esp_talla);
        $('#adicional').val(adicional);
        $('#laboratorio').val(laboratorio).trigger('change');
        $('#tipo').val(tipo).trigger('change');
        $('#presentacion').val(presentacion).trigger('change');

        $('#crearProductoLabel').html('<i class="bi bi-pencil-square me-2"></i>Editar Insumo');
        edit = true;
    });

    $(document).on('click', '.lote', function() {
        const elemento = $(this).closest('.d-flex.align-items-stretch');
        const id = $(elemento).attr('proId');
        const nombre = $(elemento).attr('proNombre');
        const tipoId = parseInt($(elemento).attr('nTypeNombre')) || 0;

        $('#id_lote_prod').val(id);
        $('#nombre_producto_lote').html(nombre);

        // Si es Equipos (2), Papelería/Higiene (4) o Repuestos (5), marcar como sin vencimiento
        if ([2, 4, 5].includes(tipoId)) {
            $('#noPerecedero').prop('checked', true);
            $('#vencimiento').val('').prop('disabled', true).prop('required', false);
        } else {
            $('#noPerecedero').prop('checked', false);
            $('#vencimiento').prop('disabled', false).prop('required', true);
        }
    });

    $(document).on('change', '#noPerecedero', function() {
        if ($(this).is(':checked')) {
            $('#vencimiento').val('').prop('disabled', true).prop('required', false);
        } else {
            $('#vencimiento').prop('disabled', false).prop('required', true);
        }
    });

    $('#form-crear-lote').submit(e => {
        e.preventDefault();
        let id_producto = $('#id_lote_prod').val();
        let proveedor = $('#proveedor').val();
        let cod_lote = $('#cod_lote').val();
        let stock = $('#stock').val();
        let esNoPerecedero = $('#noPerecedero').is(':checked');
        let vencimiento = esNoPerecedero ? '2035-12-31' : $('#vencimiento').val();
        funcion = 'crear';

        $.post('../controller/LoteController.php', { id_producto, proveedor, cod_lote, stock, vencimiento, funcion }, (response) => {
            if (response === 'add') {
                Swal.fire({
                    position: 'center',
                    icon: 'success',
                    title: 'Lote / Ingreso Creado con Éxito',
                    showConfirmButton: false,
                    timer: 1000
                }).then(() => {
                    const modalObj = bootstrap.Modal.getInstance(document.getElementById('crearlote'));
                    if (modalObj) modalObj.hide();
                    $('#form-crear-lote').trigger('reset');
                    buscar_product();
                });
            } else {
                Swal.fire({
                    position: 'center',
                    icon: 'error',
                    title: 'No se pudo crear el lote',
                    showConfirmButton: false,
                    timer: 1500
                });
            }
        });
    });

    $(document).on('click', '.borrar_produts', function() {
        const elemento = $(this).closest('.d-flex.align-items-stretch');
        const id = $(elemento).attr('proId');
        const nombre = $(elemento).attr('proNombre');
        const funcion = 'borrar';

        Swal.fire({
            title: `¿Eliminar insumo "${nombre}"?`,
            text: "Esta acción no se puede deshacer.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#64748b',
            confirmButtonText: '<i class="bi bi-trash me-1"></i> Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post('../controller/ProductoController.php', { id, funcion }, (response) => {
                    if (response === 'borrado') {
                        Swal.fire({
                            position: 'center',
                            icon: 'success',
                            title: 'Insumo Eliminado',
                            showConfirmButton: false,
                            timer: 1000
                        });
                        buscar_product();
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