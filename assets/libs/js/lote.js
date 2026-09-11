$(document).ready(function() {
    var funcion;
    buscar_lotes();

    function buscar_lotes(consulta) {
        funcion = "buscar_lote";
        $.post('../controller/LoteController.php', { consulta, funcion }, (response) => {
            try {
                const lotes = JSON.parse(response);
                mostrarlotes(lotes);
            } catch (error) {
                console.error("Error al analizar JSON:", error);
            }
        });
    }

    function mostrarlotes(lotes) {
        const loteContainer = $('#lotes');
        if (!lotes || lotes.length === 0) {
            loteContainer.html(`
                <div class="col-12 text-center py-5">
                    <i class="bi bi-inbox text-muted display-4"></i>
                    <p class="text-muted mt-2">No se encontraron lotes registrados.</p>
                </div>
            `);
            return;
        }

        const template = lotes.map(lote => {
            let cardBorder = 'border-success';
            let badgeClass = 'bg-success';
            let estadoTexto = 'En Regla';

            if (lote.estado === 'danger') {
                cardBorder = 'border-danger';
                badgeClass = 'bg-danger';
                estadoTexto = 'Vencido';
            } else if (lote.estado === 'warning') {
                cardBorder = 'border-warning';
                badgeClass = 'bg-warning text-dark';
                estadoTexto = 'Por Vencer';
            }

            const avatarSrc = lote.avatar && lote.avatar.trim() !== '' ? lote.avatar : '../libs/img/product/prod_default.png';

            return `
                <div loteID="${lote.id}" stockID="${lote.stock}" class="col-12 col-sm-6 col-md-4 col-xl-3 d-flex align-items-stretch">
                    <div class="card product-card w-100 shadow-sm ${cardBorder} border-2 d-flex flex-column justify-content-between">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="badge ${badgeClass} rounded-pill px-3 py-2 small">
                                    <i class="bi bi-boxes me-1"></i>Stock: ${lote.stock}
                                </span>
                                <span class="badge bg-light text-dark border small">${estadoTexto}</span>
                            </div>

                            <div class="text-center mb-3">
                                <img src="${avatarSrc}" alt="${lote.nombre}" class="product-avatar mb-2 shadow-sm" onerror="this.src='../libs/img/product/prod_default.png'">
                                <h5 class="fw-bold text-primary mb-1 text-truncate" title="${lote.nombre}">${lote.nombre}</h5>
                                <div class="badge bg-light text-secondary border">Código: ${lote.cod_lote || lote.id}</div>
                            </div>

                            <ul class="list-unstyled small text-muted border-top pt-3 mb-0">
                                <li class="mb-1 text-truncate"><i class="bi bi-calendar-event text-secondary me-2"></i><b>Vence:</b> ${lote.vencimiento || 'N/A'}</li>
                                <li class="mb-1 text-truncate"><i class="bi bi-truck text-secondary me-2"></i><b>Proveedor:</b> ${lote.proveedor || 'N/A'}</li>
                                <li class="mb-1 text-truncate"><i class="bi bi-building text-secondary me-2"></i><b>Laboratorio:</b> ${lote.nombre_laboratorio || 'N/A'}</li>
                                <li class="text-truncate"><i class="bi bi-box-seam text-secondary me-2"></i><b>Presentación:</b> ${lote.nombre_presentacion || 'N/A'}</li>
                            </ul>
                        </div>

                        <div class="card-footer bg-light border-0 p-2 d-flex justify-content-center gap-2">
                            <button class="editar btn btn-sm btn-outline-success px-3" title="Ajustar Stock" type="button" data-bs-toggle="modal" data-bs-target="#editarlote">
                                <i class="bi bi-pencil me-1"></i>Ajustar
                            </button>
                            <button class="borrar_lote btn btn-sm btn-outline-danger px-3" title="Eliminar Lote" type="button">
                                <i class="bi bi-trash me-1"></i>Eliminar
                            </button>
                        </div>
                    </div>
                </div>
            `;
        }).join('');
        loteContainer.empty().append(template);
    }

    $(document).on('keyup', '#buscar_lotes', function () {
        let valor = $(this).val();
        if (valor !== "") {
            buscar_lotes(valor);
        } else {
            buscar_lotes();
        }
    });

    $(document).on('click', '.editar', function() {
        const elemento = $(this).closest('.d-flex.align-items-stretch');
        const id = $(elemento).attr('loteID');
        const stock = $(elemento).attr('stockID');
        $('#id_lote_prod').val(id);
        $('#codigo_lote').html('#' + id);
        $('#stock').val(stock);
    });

    $('#form-editar-lote').submit(e => {
        e.preventDefault();
        let id = $('#id_lote_prod').val();
        let stock = $('#stock').val();
        funcion = 'editar';

        $.post('../controller/LoteController.php', { id, stock, funcion }, (response) => {
            if (response === 'edit') {
                Swal.fire({
                    position: 'center',
                    icon: 'success',
                    title: 'Stock Actualizado con Éxito',
                    showConfirmButton: false,
                    timer: 1000
                }).then(() => {
                    const modalObj = bootstrap.Modal.getInstance(document.getElementById('editarlote'));
                    if (modalObj) modalObj.hide();
                    $('#form-editar-lote').trigger('reset');
                    buscar_lotes();
                });
            } else {
                Swal.fire({
                    position: 'center',
                    icon: 'error',
                    title: 'No se pudo editar el stock',
                    showConfirmButton: false,
                    timer: 1500
                });
            }
        });
    });

    $(document).on('click', '.borrar_lote', function() {
        const elemento = $(this).closest('.d-flex.align-items-stretch');
        const id = $(elemento).attr('loteID');
        funcion = 'borrar';

        Swal.fire({
            title: `¿Eliminar Lote #${id}?`,
            text: "Esta acción no se puede deshacer.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#64748b',
            confirmButtonText: '<i class="bi bi-trash me-1"></i> Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post('../controller/LoteController.php', { id, funcion }, (response) => {
                    if (response === 'borrado') {
                        Swal.fire({
                            position: 'center',
                            icon: 'success',
                            title: 'Lote Eliminado',
                            showConfirmButton: false,
                            timer: 1000
                        });
                        buscar_lotes();
                    } else {
                        Swal.fire({
                            position: 'center',
                            icon: 'error',
                            title: 'Error al eliminar el lote',
                            showConfirmButton: false,
                            timer: 1500
                        });
                    }
                });
            }
        });
    });
});