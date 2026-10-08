// SIMAP - Modificar Registro de Despacho y Entrega
$(document).ready(function() {
    let id_despacho = new URLSearchParams(window.location.search).get('id');
    let productos_despacho = [];
    let stock_original = {};
    let total_despacho = 0;

    cargar_areas();

    function cargar_areas(id_seleccionado = null) {
        $.post('../controller/AreaController.php', { funcion: 'cargar_areas' }, function(response) {
            let areas = JSON.parse(response);
            let opciones = areas.map(a => `<option value="${a.id_area}">${a.nombre_area}</option>`);
            $('#area_destino').html(opciones.join(''));
            if (id_seleccionado) {
                $('#area_destino').val(id_seleccionado).trigger('change');
            }
        });
    }

    // Cargar datos del despacho
    function cargar_despacho() {
        $.post('../controller/DespachoController.php', {
            funcion: 'obtener_despacho',
            id_despacho: id_despacho
        }, function(response) {
            let data = JSON.parse(response);
            if (data.status === 'success') {
                let despacho = data.despacho;
                $('#receptor').val(despacho.receptor);
                $('#ci_receptor').val(despacho.ci_receptor);
                $('#cargo_receptor').val(despacho.cargo_receptor || '');
                $('#observacion').val(despacho.observacion || '');
                if (despacho.id_area) {
                    cargar_areas(despacho.id_area);
                }
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message
                }).then(() => {
                    window.location.href = '../pages/adm_despachos.php';
                });
            }
        });
    }

    // Cargar detalles de productos despachados
    function cargar_detalles_despacho() {
        $.post('../controller/DespachoController.php', {
            funcion: 'ver_detalle_despacho',
            id_despacho: id_despacho
        }, function(response) {
            productos_despacho = JSON.parse(response);
            mostrar_productos_tabla();
            calcular_total();
        });
    }

    // Mostrar productos en la tabla
    function mostrar_productos_tabla() {
        let template = '';
        productos_despacho.forEach(producto => {
            const unidad = producto.unidad_codigo ? `(${producto.unidad_codigo})` : '';
            const idItem = producto.id_despacho_insumo || producto.id_ventaproducto;
            template += `
                <tr>
                    <td>
                        <div class="fw-semibold text-dark">${producto.producto}</div>
                    </td>
                    <td>
                        <span class="badge bg-light text-primary border">${unidad || 'und'}</span>
                        <small class="text-muted d-block">${producto.especificacion_talla || ''}</small>
                    </td>
                    <td><span class="badge bg-light text-secondary border">${producto.lote || 'N/A'}</span></td>
                    <td><small class="text-muted">${producto.vencimiento || 'N/A'}</small></td>
                    <td><span class="badge bg-primary-subtle text-primary fw-bold fs-6">${producto.cantidad}</span></td>
                    <td class="text-center">
                        <div class="btn-group btn-group-sm" role="group">
                            <button class="btn btn-outline-warning editar-cantidad" data-id="${idItem}" data-producto="${producto.producto}" data-cantidad="${producto.cantidad}" title="Modificar cantidad">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-outline-danger eliminar-producto" data-id="${idItem}" title="Remover insumo">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
            
            // Guardar stock original para referencia
            stock_original[idItem] = producto.cantidad;
        });
        
        $('#tabla_detalle_despacho tbody').html(template);
    }

    // Calcular el total de unidades físicas despachadas
    function calcular_total() {
        total_despacho = 0;
        productos_despacho.forEach(producto => {
            total_despacho += parseInt(producto.cantidad) || 0;
        });
        $('#total_despacho').text(`${total_despacho} unidades`);
    }

    // Obtener stock disponible para un lote de producto
    function obtener_stock_disponible(id_producto, id_lote, callback) {
        $.post('../controller/DespachoController.php', {
            funcion: 'obtener_stock_lote',
            id_producto: id_producto,
            id_lote: id_lote
        }, function(response) {
            let data = JSON.parse(response);
            callback(data.stock || 0);
        });
    }

    // Evento para editar cantidad
    $(document).on('click', '.editar-cantidad', function() {
        let id_detalle = $(this).data('id');
        let producto_nombre = $(this).data('producto');
        let cantidad_actual = $(this).data('cantidad');
        
        let producto = productos_despacho.find(p => (p.id_despacho_insumo == id_detalle || p.id_ventaproducto == id_detalle));
        
        $('#producto_editar').val(producto_nombre);
        $('#id_detalle_editar').val(id_detalle);
        $('#cantidad_actual').val(cantidad_actual);
        $('#nueva_cantidad').val(cantidad_actual);
        
        obtener_stock_disponible(producto.producto_id_producto, producto.id_det_lote, function(stock) {
            let stock_total = parseInt(stock) + parseInt(cantidad_actual);
            $('#stock_disponible').val(stock_total);
            const modalEl = document.getElementById('modal_editar_cantidad');
            const modalInstance = new bootstrap.Modal(modalEl);
            modalInstance.show();
        });
    });

    // Guardar nueva cantidad
    $('#btn_guardar_cantidad').click(function() {
        let id_detalle = $('#id_detalle_editar').val();
        let nueva_cantidad = parseInt($('#nueva_cantidad').val());
        let stock_disponible = parseInt($('#stock_disponible').val());
        
        if (isNaN(nueva_cantidad) || nueva_cantidad <= 0) {
            Swal.fire('Error', 'La cantidad debe ser mayor a cero', 'error');
            return;
        }
        
        if (nueva_cantidad > stock_disponible) {
            Swal.fire('Stock Insuficiente', `La cantidad solicitada excede el stock disponible (${stock_disponible})`, 'warning');
            return;
        }
        
        let index = productos_despacho.findIndex(p => (p.id_despacho_insumo == id_detalle || p.id_ventaproducto == id_detalle));
        if (index !== -1) {
            productos_despacho[index].cantidad = nueva_cantidad;
            mostrar_productos_tabla();
            calcular_total();
            const modalEl = document.getElementById('modal_editar_cantidad');
            const modalInstance = bootstrap.Modal.getInstance(modalEl);
            if (modalInstance) modalInstance.hide();
        }
    });

    // Eliminar producto del despacho
    $(document).on('click', '.eliminar-producto', function() {
        let id_detalle = $(this).data('id');
        
        Swal.fire({
            title: '¿Remover insumo?',
            text: "Este insumo será retirado del despacho y reintegrado al inventario",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#0d6efd',
            cancelButtonColor: '#dc3545',
            confirmButtonText: 'Sí, remover',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                productos_despacho = productos_despacho.filter(p => (p.id_despacho_insumo != id_detalle && p.id_ventaproducto != id_detalle));
                mostrar_productos_tabla();
                calcular_total();
            }
        });
    });

    // Guardar cambios en el despacho
    $('#btn_actualizar_despacho').click(function() {
        let receptor = $('#receptor').val();
        let ci_receptor = $('#ci_receptor').val();
        let id_area = $('#area_destino').val();
        let cargo_receptor = $('#cargo_receptor').val() || '';
        let observacion = $('#observacion').val() || '';
        
        if (productos_despacho.length === 0) {
            Swal.fire('Error', 'No hay ningún insumo en la solicitud de despacho', 'error');
            return;
        }

        if (!receptor || receptor.trim() === '' || !ci_receptor || ci_receptor.trim() === '') {
            Swal.fire('Atención', 'Complete el nombre del funcionario receptor y su cédula', 'warning');
            return;
        }
        
        Swal.fire({
            title: '¿Guardar cambios?',
            text: "Se recalcularán las existencias y el acta oficial de entrega",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#0d6efd',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, guardar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post('../controller/DespachoController.php', {
                    funcion: 'actualizar_despacho',
                    id_despacho: id_despacho,
                    receptor: receptor,
                    ci_receptor: ci_receptor,
                    id_area: id_area,
                    cargo_receptor: cargo_receptor,
                    observacion: observacion,
                    productos: JSON.stringify(productos_despacho)
                }, function(response) {
                    let data = JSON.parse(response);
                    if (data.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Éxito',
                            text: data.message,
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            window.location.href = '../pages/adm_despachos.php';
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message
                        });
                    }
                });
            }
        });
    });

    // Inicializar
    cargar_despacho();
    cargar_detalles_despacho();
});
