// SIMAP - Modificar Registro de Entrega/Salida
$(document).ready(function() {
    let id_venta = new URLSearchParams(window.location.search).get('id');
    let productos_venta = [];
    let stock_original = {};
    let total_venta = 0;

    cargar_areas();

    function cargar_areas(id_seleccionado = null) {
        $.post('../controller/AreaController.php', { funcion: 'cargar_areas' }, function(response) {
            let areas = JSON.parse(response);
            let opciones = areas.map(a => `<option value="${a.id_area}">${a.nombre_area} (Riesgo: ${a.nivel_riesgo})</option>`);
            $('#area_destino').html(opciones.join(''));
            if (id_seleccionado) {
                $('#area_destino').val(id_seleccionado).trigger('change');
            }
        });
    }

    // Cargar datos de la venta/salida
    function cargar_venta() {
        $.post('../controller/VentaController.php', {
            funcion: 'obtener_venta',
            id_venta: id_venta
        }, function(response) {
            let data = JSON.parse(response);
            if (data.status === 'success') {
                let venta = data.venta;
                $('#cliente').val(venta.cliente);
                $('#ci').val(venta.ci);
                $('#cargo_receptor').val(venta.cargo_receptor || '');
                $('#observacion').val(venta.observacion || '');
                if (venta.id_area) {
                    cargar_areas(venta.id_area);
                }
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message
                }).then(() => {
                    window.location.href = '../pages/adm_retiro_ventas.php';
                });
            }
        });
    }

    // Cargar detalles de productos
    function cargar_detalles_venta() {
        $.post('../controller/VentaController.php', {
            funcion: 'ver_detalle_venta',
            id_venta: id_venta
        }, function(response) {
            productos_venta = JSON.parse(response);
            mostrar_productos_tabla();
            calcular_total();
        });
    }

    // Mostrar productos en la tabla
    function mostrar_productos_tabla() {
        let template = '';
        productos_venta.forEach(producto => {
            const unidad = producto.unidad_codigo ? `(${producto.unidad_codigo})` : '';
            template += `
                <tr>
                    <td>
                        <div class="fw-semibold text-dark">${producto.producto}</div>
                        <small class="text-muted">${producto.especificacion_talla || ''} ${unidad}</small>
                    </td>
                    <td><span class="badge bg-light text-secondary border">${producto.lote || 'N/A'}</span></td>
                    <td><small class="text-muted">${producto.vencimiento || 'N/A'}</small></td>
                    <td>$${parseFloat(producto.precio).toFixed(2)}</td>
                    <td><span class="badge bg-primary-subtle text-primary fw-bold fs-6">${producto.cantidad}</span></td>
                    <td class="fw-semibold">$${(producto.precio * producto.cantidad).toFixed(2)}</td>
                    <td class="text-center">
                        <div class="btn-group btn-group-sm" role="group">
                            <button class="btn btn-outline-warning editar-cantidad" data-id="${producto.id_ventaproducto}" data-producto="${producto.producto}" data-cantidad="${producto.cantidad}" title="Modificar cantidad">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-outline-danger eliminar-producto" data-id="${producto.id_ventaproducto}" title="Remover insumo">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
            
            // Guardar stock original para referencia
            stock_original[producto.id_ventaproducto] = producto.cantidad;
        });
        
        $('#tabla_detalle_venta tbody').html(template);
    }

    // Calcular el total de la entrega/salida
    function calcular_total() {
        total_venta = 0;
        productos_venta.forEach(producto => {
            total_venta += producto.precio * producto.cantidad;
        });
        $('#total_venta').text(`$${total_venta.toFixed(2)}`);
    }

    // Obtener stock disponible para un producto
    function obtener_stock_disponible(id_producto, id_lote, callback) {
        $.post('../controller/VentaController.php', {
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
        
        let producto = productos_venta.find(p => p.id_ventaproducto == id_detalle);
        
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
        let cantidad_actual = parseInt($('#cantidad_actual').val());
        
        if (isNaN(nueva_cantidad) || nueva_cantidad <= 0) {
            Swal.fire('Error', 'La cantidad debe ser mayor a cero', 'error');
            return;
        }
        
        if (nueva_cantidad > stock_disponible) {
            Swal.fire('Stock Insuficiente', `La cantidad solicitada excede el stock disponible (${stock_disponible})`, 'warning');
            return;
        }
        
        let index = productos_venta.findIndex(p => p.id_ventaproducto == id_detalle);
        if (index !== -1) {
            productos_venta[index].cantidad = nueva_cantidad;
            mostrar_productos_tabla();
            calcular_total();
            const modalEl = document.getElementById('modal_editar_cantidad');
            const modalInstance = bootstrap.Modal.getInstance(modalEl);
            if (modalInstance) modalInstance.hide();
        }
    });

    // Eliminar producto de la venta
    $(document).on('click', '.eliminar-producto', function() {
        let id_detalle = $(this).data('id');
        
        Swal.fire({
            title: '¿Remover insumo?',
            text: "Este insumo será retirado de la solicitud y no se entregará",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#0d6efd',
            cancelButtonColor: '#dc3545',
            confirmButtonText: 'Sí, remover',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                productos_venta = productos_venta.filter(p => p.id_ventaproducto != id_detalle);
                mostrar_productos_tabla();
                calcular_total();
            }
        });
    });

    // Guardar cambios en la venta
    $('#btn_actualizar_venta').click(function() {
        let cliente = $('#cliente').val();
        let ci = $('#ci').val();
        let id_area = $('#area_destino').val();
        let cargo_receptor = $('#cargo_receptor').val() || '';
        let observacion = $('#observacion').val() || '';
        
        if (productos_venta.length === 0) {
            Swal.fire('Error', 'No hay ningún insumo en la solicitud', 'error');
            return;
        }

        if (!cliente || cliente.trim() === '' || !ci || ci.trim() === '') {
            Swal.fire('Atención', 'Complete el nombre del funcionario y su cédula', 'warning');
            return;
        }
        
        Swal.fire({
            title: '¿Guardar cambios?',
            text: "Se recalcularán las existencias y los comprobantes asociados",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#0d6efd',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, guardar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post('../controller/VentaController.php', {
                    funcion: 'actualizar_venta',
                    id_venta: id_venta,
                    cliente: cliente,
                    ci: ci,
                    id_area: id_area,
                    cargo_receptor: cargo_receptor,
                    observacion: observacion,
                    total: total_venta,
                    productos: JSON.stringify(productos_venta)
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
                            window.location.href = '../pages/adm_retiro_ventas.php';
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
    cargar_venta();
    cargar_detalles_venta();
});