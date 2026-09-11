// SIMAP - Historial de Salidas y Entregas
$(document).ready(function() {
    // Inicializar DataTable con traducción local 100% offline
    let tabla_ventas = $('#tabla_ventas').DataTable({
        responsive: true,
        autoWidth: false,
        deferRender: true,
        ajax: {
            url: "../controller/VentaController.php",
            method: "POST",
            data: {
                funcion: "listar_ventas"
            },
            dataSrc: ""
        },
        columns: [
            { data: "id_venta" },
            { data: "fecha" },
            { data: "cliente" },
            { data: "ci" },
            { 
                data: "total",
                render: function(data, type, row) {
                    return `$${parseFloat(data).toFixed(2)}`;
                } 
            },
            { data: "vendedor" },
            {
                defaultContent: `
                <div class="btn-group btn-group-sm" role="group">
                    <button class="ver_detalles btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#vista_venta" title="Ver comprobante">
                        <i class="bi bi-eye"></i>
                    </button>
                    <button class="editar btn btn-outline-warning" title="Editar salida">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <button class="revertir btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#confirmar_revertir" title="Anular entrega">
                        <i class="bi bi-trash"></i>
                    </button>
                    <button class="imprimir btn btn-outline-primary" title="Imprimir recibo">
                        <i class="bi bi-printer"></i>
                    </button>
                </div>
                `
            }
        ],
        language: {
            processing: "Procesando registros...",
            search: "Buscar:",
            lengthMenu: "Mostrar _MENU_ registros",
            info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
            infoEmpty: "Mostrando 0 a 0 de 0 registros",
            infoFiltered: "(filtrado de _MAX_ registros en total)",
            zeroRecords: "No se encontraron entregas registradas",
            emptyTable: "No hay registros disponibles",
            paginate: {
                first: "Primero",
                previous: "Anterior",
                next: "Siguiente",
                last: "Último"
            }
        },
        order: [[ 0, "desc" ]]
    });

    let id_venta_seleccionada;

    // Ver detalles
    $('#tabla_ventas tbody').on('click', '.ver_detalles', function() {
        let data = tabla_ventas.row($(this).parents('tr')).data();
        id_venta_seleccionada = data.id_venta;
        
        $('#cliente_detalle').text(data.cliente);
        $('#ci_detalle').text(data.ci);
        $('#fecha_detalle').text(data.fecha);
        $('#vendedor_detalle').text(data.vendedor);
        $('#total_detalle').text(`$${parseFloat(data.total).toFixed(2)}`);
        
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
                        <td class="fw-semibold text-dark">${detalle.producto}</td>
                        <td><span class="badge bg-primary-subtle text-primary fw-bold">${detalle.cantidad}</span></td>
                        <td>$${parseFloat(detalle.precio).toFixed(2)}</td>
                        <td class="fw-semibold">$${parseFloat(detalle.subtotal).toFixed(2)}</td>
                        <td><span class="badge bg-light text-secondary border">${detalle.lote || 'N/A'}</span></td>
                        <td><small class="text-muted">${detalle.vencimiento || 'N/A'}</small></td>
                    </tr>
                    `;
                });
                
                $('#detalles_venta').html(template);
            },
            error: function(error) {
                console.error('Error al cargar detalles de salida:', error);
            }
        });
    });

    // Abrir modal de anulación
    $('#tabla_ventas tbody').on('click', '.revertir', function() {
        let data = tabla_ventas.row($(this).parents('tr')).data();
        id_venta_seleccionada = data.id_venta;
    });

    // Confirmar anulación
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
                const modalEl = document.getElementById('confirmar_revertir');
                const modalInstance = bootstrap.Modal.getInstance(modalEl);
                if (modalInstance) modalInstance.hide();

                if (resultado.status === 'success') {
                    Swal.fire({
                        position: 'center',
                        icon: 'success',
                        title: 'Entrega anulada',
                        text: resultado.message,
                        showConfirmButton: false,
                        timer: 1500
                    }).then(function() {
                        tabla_ventas.ajax.reload();
                    });
                } else {
                    Swal.fire({
                        position: 'center',
                        icon: 'error',
                        title: 'Error',
                        text: resultado.message,
                        showConfirmButton: true
                    });
                }
            },
            error: function(error) {
                console.error('Error al revertir salida:', error);
            }
        });
    });

    // Editar venta/salida
    $('#tabla_ventas tbody').on('click', '.editar', function() {
        let data = tabla_ventas.row($(this).parents('tr')).data();
        id_venta_seleccionada = data.id_venta;
        window.location.href = `editar_venta.php?id=${id_venta_seleccionada}`;
    });

    // Imprimir desde tabla
    $('#tabla_ventas tbody').on('click', '.imprimir', function() {
        let data = tabla_ventas.row($(this).parents('tr')).data();
        id_venta_seleccionada = data.id_venta;
        window.open(`../pages/recibo_venta.php?id=${id_venta_seleccionada}`, '_blank');
    });

    // Imprimir desde modal
    $('#btn_imprimir').click(function() {
        if (id_venta_seleccionada) {
            window.open(`../pages/recibo_venta.php?id=${id_venta_seleccionada}`, '_blank');
        }
    });

    // Filtrar por fechas
    $('#btn_filtrar').click(function() {
        let fecha_inicio = $('#fecha_inicio').val();
        let fecha_fin = $('#fecha_fin').val();
        
        if (fecha_inicio === '' || fecha_fin === '') {
            Swal.fire({
                icon: 'warning',
                title: 'Rango de fechas requerido',
                text: 'Debe seleccionar tanto la fecha inicial como la final para filtrar.'
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