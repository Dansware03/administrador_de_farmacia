$(document).ready(function() {
    // Variables globales
    let id_venta = new URLSearchParams(window.location.search).get('id');
    let productos_venta = [];
    let stock_original = {};
    let total_venta = 0;

    // Cargar datos de la venta
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
            template += `
                <tr>
                    <td>${producto.producto}</td>
                    <td>${producto.lote || 'N/A'}</td>
                    <td>${producto.vencimiento || 'N/A'}</td>
                    <td>${producto.precio}</td>
                    <td>${producto.cantidad}</td>
                    <td>${(producto.precio * producto.cantidad).toFixed(2)}</td>
                    <td>
                        <button class="btn btn-sm btn-warning editar-cantidad" data-id="${producto.id_ventaproducto}" data-producto="${producto.producto}" data-cantidad="${producto.cantidad}">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger eliminar-producto" data-id="${producto.id_ventaproducto}">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </td>
                </tr>
            `;
            
            // Guardar stock original para referencia
            stock_original[producto.id_ventaproducto] = producto.cantidad;
        });
        
        $('#tabla_detalle_venta tbody').html(template);
    }

    // Calcular el total de la venta
    function calcular_total() {
        total_venta = 0;
        productos_venta.forEach(producto => {
            total_venta += producto.precio * producto.cantidad;
        });
        $('#total_venta').text(total_venta.toFixed(2));
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
        
        // Buscar el producto en el array
        let producto = productos_venta.find(p => p.id_ventaproducto == id_detalle);
        
        $('#producto_editar').val(producto_nombre);
        $('#id_detalle_editar').val(id_detalle);
        $('#cantidad_actual').val(cantidad_actual);
        $('#nueva_cantidad').val(cantidad_actual);
        
        // Obtener stock disponible actual + la cantidad actual (que ya está reservada)
        obtener_stock_disponible(producto.producto_id_producto, producto.id_det_lote, function(stock) {
            let stock_total = parseInt(stock) + parseInt(cantidad_actual);
            $('#stock_disponible').val(stock_total);
            $('#modal_editar_cantidad').modal('show');
        });
    });

    // Guardar nueva cantidad
    $('#btn_guardar_cantidad').click(function() {
        let id_detalle = $('#id_detalle_editar').val();
        let nueva_cantidad = parseInt($('#nueva_cantidad').val());
        let stock_disponible = parseInt($('#stock_disponible').val());
        let cantidad_actual = parseInt($('#cantidad_actual').val());
        
        if (nueva_cantidad <= 0) {
            Swal.fire('Error', 'La cantidad debe ser mayor a cero', 'error');
            return;
        }
        
        if (nueva_cantidad > stock_disponible) {
            Swal.fire('Error', 'La cantidad excede el stock disponible', 'error');
            return;
        }
        
        // Actualizar en el array local
        let index = productos_venta.findIndex(p => p.id_ventaproducto == id_detalle);
        if (index !== -1) {
            productos_venta[index].cantidad = nueva_cantidad;
            mostrar_productos_tabla();
            calcular_total();
            $('#modal_editar_cantidad').modal('hide');
        }
    });

    // Eliminar producto de la venta
    $(document).on('click', '.eliminar-producto', function() {
        let id_detalle = $(this).data('id');
        
        Swal.fire({
            title: '¿Estás seguro?',
            text: "Este producto será eliminado de la venta",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Eliminar del array local
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
        
        if (productos_venta.length === 0) {
            Swal.fire('Error', 'No hay productos en la venta', 'error');
            return;
        }
        
        Swal.fire({
            title: '¿Guardar cambios?',
            text: "Se actualizará la venta y el inventario",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, guardar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post('../controller/VentaController.php', {
                    funcion: 'actualizar_venta',
                    id_venta: id_venta,
                    cliente: cliente,
                    ci: ci,
                    total: total_venta,
                    productos: JSON.stringify(productos_venta)
                }, function(response) {
                    let data = JSON.parse(response);
                    if (data.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Éxito',
                            text: data.message
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