// SIMAP - Historial de Despachos y Actas de Entrega
$(document).ready(function() {
    let tabla_despachos = $('#tabla_despachos').DataTable({
        responsive: true,
        autoWidth: false,
        deferRender: true,
        ajax: {
            url: "../controller/DespachoController.php",
            method: "POST",
            data: {
                funcion: "listar_despachos"
            },
            dataSrc: ""
        },
        columns: [
            { data: "id_despacho" },
            { data: "fecha" },
            { 
                data: "area",
                render: function(data, type, row) {
                    let badgeClass = "bg-secondary";
                    if (row.nivel_riesgo === "Alto") badgeClass = "bg-danger";
                    else if (row.nivel_riesgo === "Medio") badgeClass = "bg-warning text-dark";
                    else if (row.nivel_riesgo === "Bajo") badgeClass = "bg-success";
                    return `<span class="fw-semibold text-dark">${data || 'General'}</span> <span class="badge ${badgeClass} ms-1 small" style="font-size:0.7rem;">${row.nivel_riesgo || 'Bajo'}</span>`;
                }
            },
            { data: "receptor" },
            { data: "ci_receptor" },
            { data: "responsable_nombre" },
            {
                defaultContent: `
                <div class="btn-group btn-group-sm" role="group">
                    <button class="ver_detalles btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#vista_despacho" title="Ver acta">
                        <i class="bi bi-eye"></i>
                    </button>
                    <button class="editar btn btn-outline-warning" title="Editar despacho">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <button class="revertir btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#confirmar_revertir" title="Anular entrega">
                        <i class="bi bi-trash"></i>
                    </button>
                    <button class="imprimir btn btn-outline-primary" title="Imprimir acta">
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
            zeroRecords: "No se encontraron despachos registrados",
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

    let id_despacho_seleccionado;

    // Ver detalles del despacho
    $('#tabla_despachos tbody').on('click', '.ver_detalles', function() {
        let data = tabla_despachos.row($(this).parents('tr')).data();
        id_despacho_seleccionado = data.id_despacho;
        
        $('#receptor_detalle').text(data.receptor);
        $('#cargo_detalle').text(data.cargo_receptor || 'Personal Asignado');
        $('#area_detalle').text(data.area || 'Área General');
        let riesgo = data.nivel_riesgo || 'Bajo';
        let badgeClass = "bg-secondary";
        if (riesgo === "Alto") badgeClass = "bg-danger";
        else if (riesgo === "Medio") badgeClass = "bg-warning text-dark";
        else if (riesgo === "Bajo") badgeClass = "bg-success";
        $('#riesgo_badge_detalle').attr('class', `badge ${badgeClass} small mt-1`).text(`Riesgo: ${riesgo}`);

        $('#ci_detalle').text(data.ci_receptor);
        $('#fecha_detalle').text(data.fecha);
        $('#responsable_detalle').text(data.responsable_nombre);
        
        $.ajax({
            url: '../controller/DespachoController.php',
            type: 'POST',
            data: {
                funcion: 'ver_detalle_despacho',
                id_despacho: id_despacho_seleccionado
            },
            success: function(response) {
                let detalles = JSON.parse(response);
                let template = '';
                let totalPiezas = 0;
                
                detalles.forEach(detalle => {
                    const cant = parseInt(detalle.cantidad) || 0;
                    totalPiezas += cant;
                    const unidad = detalle.unidad_codigo ? `(${detalle.unidad_codigo})` : '';
                    template += `
                    <tr>
                        <td>
                            <div class="fw-semibold text-dark">${detalle.producto}</div>
                            <small class="text-muted">${detalle.especificacion_talla || ''}</small>
                        </td>
                        <td><span class="badge bg-light text-primary border">${unidad || 'und'}</span></td>
                        <td><span class="badge bg-primary-subtle text-primary fw-bold fs-6">${cant}</span></td>
                        <td><span class="badge bg-light text-secondary border">${detalle.lote || 'N/A'}</span></td>
                        <td><small class="text-muted">${detalle.vencimiento || 'N/A'}</small></td>
                    </tr>
                    `;
                });
                
                $('#detalles_despacho').html(template);
                $('#total_detalle').text(`${totalPiezas} unidades`);
            },
            error: function(error) {
                console.error('Error al cargar detalles del despacho:', error);
            }
        });
    });

    // Abrir modal de anulación
    $('#tabla_despachos tbody').on('click', '.revertir', function() {
        let data = tabla_despachos.row($(this).parents('tr')).data();
        id_despacho_seleccionado = data.id_despacho;
    });

    // Confirmar anulación
    $('#btn_confirmar_revertir').click(function() {
        $.ajax({
            url: '../controller/DespachoController.php',
            type: 'POST',
            data: {
                funcion: 'revertir_despacho',
                id_despacho: id_despacho_seleccionado
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
                        title: 'Despacho anulado',
                        text: resultado.message,
                        showConfirmButton: false,
                        timer: 1500
                    }).then(function() {
                        tabla_despachos.ajax.reload();
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
                console.error('Error al anular despacho:', error);
            }
        });
    });

    // Editar despacho
    $('#tabla_despachos tbody').on('click', '.editar', function() {
        let data = tabla_despachos.row($(this).parents('tr')).data();
        id_despacho_seleccionado = data.id_despacho;
        window.location.href = `adm_editar_despacho.php?id=${id_despacho_seleccionado}`;
    });

    // Imprimir acta desde tabla
    $('#tabla_despachos tbody').on('click', '.imprimir', function() {
        let data = tabla_despachos.row($(this).parents('tr')).data();
        id_despacho_seleccionado = data.id_despacho;
        window.open(`../pages/acta_despacho.php?id=${id_despacho_seleccionado}`, '_blank');
    });

    // Imprimir acta desde modal
    $('#btn_imprimir').click(function() {
        if (id_despacho_seleccionado) {
            window.open(`../pages/acta_despacho.php?id=${id_despacho_seleccionado}`, '_blank');
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
        
        tabla_despachos.ajax.url(`../controller/DespachoController.php?funcion=listar_despachos&fecha_inicio=${fecha_inicio}&fecha_fin=${fecha_fin}`).load();
    });

    // Limpiar filtros
    $('#btn_limpiar').click(function() {
        $('#fecha_inicio').val('');
        $('#fecha_fin').val('');
        tabla_despachos.ajax.url('../controller/DespachoController.php?funcion=listar_despachos').load();
    });
});
