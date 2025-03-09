$(document).ready(function() {
    // Inicializar DataTable
    let tabla_ventas = $('#tabla_ventas').DataTable({
        "responsive": true,
        "autoWidth": false,
        "deferRender": true,
        "ajax": {
            "url": "../controller/VentaController.php",
            "method": "POST",
            "data": {
                funcion: "listar_ventas"
            },
            "dataSrc": ""
        },
        "columns": [
            { "data": "id_venta" },
            { "data": "fecha" },
            { "data": "cliente" },
            { "data": "ci" },
            { 
                "data": "total",
                "render": function(data, type, row) {
                    return `$${parseFloat(data).toFixed(2)}`;
                } 
            },
            { "data": "vendedor" },
            {
                "defaultContent": `
                <div class="btn-group">
                    <button class="ver_detalles btn btn-info" data-toggle="modal" data-target="#vista_venta">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="editar btn btn-warning">
                        <i class="fas fa-pencil-alt"></i>
                    </button>
                    <button class="revertir btn btn-danger" data-toggle="modal" data-target="#confirmar_revertir">
                        <i class="fas fa-trash"></i>
                    </button>
                    <button class="imprimir btn btn-primary">
                        <i class="fas fa-print"></i>
                    </button>
                </div>
                `
            }
        ],
        "language": {
            "url": "//cdn.datatables.net/plug-ins/2.2.2/i18n/es-ES.json"
        },
        "order": [[ 0, "desc" ]]
    });

    // Variables para almacenar el ID de la venta seleccionada
    let id_venta_seleccionada;

    // Evento para botón de ver detalles
    $('#tabla_ventas tbody').on('click', '.ver_detalles', function() {
        let data = tabla_ventas.row($(this).parents('tr')).data();
        id_venta_seleccionada = data.id_venta;
        
        // Cargar datos del encabezado
        $('#cliente_detalle').text(data.cliente);
        $('#ci_detalle').text(data.ci);
        $('#fecha_detalle').text(data.fecha);
        $('#vendedor_detalle').text(data.vendedor);
        $('#total_detalle').text(`$${parseFloat(data.total).toFixed(2)}`);
        
        // Cargar detalles de la venta
        $.ajax({
            url: '../controller/VentaController.php',
            type: 'POST',
            data: {
                funcion: 'ver_detalle_venta',
                id_venta: id_venta_seleccionada
            },
            success: function(response) {
                let detalles = JSON.parse(response);
                let template = '';
                
                detalles.forEach(detalle => {
                    template += `
                    <tr>
                        <td>${detalle.producto}</td>
                        <td>${detalle.cantidad}</td>
                        <td>$${parseFloat(detalle.precio).toFixed(2)}</td>
                        <td>$${parseFloat(detalle.subtotal).toFixed(2)}</td>
                        <td>${detalle.lote}</td>
                        <td>${detalle.vencimiento}</td>
                    </tr>
                    `;
                });
                
                $('#detalles_venta').html(template);
            },
            error: function(error) {
                console.error('Error al cargar detalles:', error);
            }
        });
    });

    // Evento para botón de revertir venta
    $('#tabla_ventas tbody').on('click', '.revertir', function() {
        let data = tabla_ventas.row($(this).parents('tr')).data();
        id_venta_seleccionada = data.id_venta;
    });

    // Confirmar revertir venta
    $('#btn_confirmar_revertir').click(function() {
        $.ajax({
            url: '../controller/VentaController.php',
            type: 'POST',
            data: {
                funcion: 'revertir_venta',
                id_venta: id_venta_seleccionada
            },
            success: function(response) {
                const resultado = JSON.parse(response);
                if (resultado.status === 'success') {
                    Swal.fire({
                        position: 'center',
                        icon: 'success',
                        title: 'Venta anulada',
                        text: resultado.message,
                        showConfirmButton: false,
                        timer: 1500
                    }).then(function() {
                        $('#confirmar_revertir').modal('hide');
                        tabla_ventas.ajax.reload();
                    });
                } else {
                    Swal.fire({
                        position: 'center',
                        icon: 'error',
                        title: 'Error',
                        text: resultado.message,
                        showConfirmButton: false,
                        timer: 1500
                    });
                }
            },
            error: function(error) {
                console.error('Error al revertir venta:', error);
            }
        });
    });

    // Evento para botón de editar venta
    $('#tabla_ventas tbody').on('click', '.editar', function() {
        let data = tabla_ventas.row($(this).parents('tr')).data();
        id_venta_seleccionada = data.id_venta;
        
        // Redireccionar a página de edición con el ID de venta
        window.location.href = `editar_venta.php?id=${id_venta_seleccionada}`;
    });

    // Evento para botón de imprimir
    $('#tabla_ventas tbody').on('click', '.imprimir', function() {
        let data = tabla_ventas.row($(this).parents('tr')).data();
        id_venta_seleccionada = data.id_venta;
        
        // Redirigir a la página de impresión
        window.open(`../pages/recibo_venta.php?id=${id_venta_seleccionada}`, '_blank');
    });

    // Evento para botón de imprimir desde el modal
    $('#btn_imprimir').click(function() {
        window.open(`../pages/recibo_venta.php?id=${id_venta_seleccionada}`, '_blank');
    });

    // Filtrar por fechas
    $('#btn_filtrar').click(function() {
        let fecha_inicio = $('#fecha_inicio').val();
        let fecha_fin = $('#fecha_fin').val();
        
        if(fecha_inicio === '' || fecha_fin === '') {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Debe seleccionar ambas fechas para filtrar'
            });
            return;
        }
        
        tabla_ventas.ajax.url(`../controller/VentaController.php?funcion=listar_ventas&fecha_inicio=${fecha_inicio}&fecha_fin=${fecha_fin}`).load();
    });

    // Limpiar filtros
    $('#btn_limpiar').click(function() {
        $('#fecha_inicio').val('');
        $('#fecha_fin').val('');
        tabla_ventas.ajax.url('../controller/VentaController.php?funcion=listar_ventas').load();
    });

});