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
                console.error("Error al analizar la respuesta JSON:", error);
            }
        });
    }
    function mostrarlotes(lotes) {
        const loteContainer = $('#lotes');
        const template = lotes.map(lote => `
            <div loteID="${lote.id}" stockID="${lote.stock}" class="col-12 col-sm-6 col-md-4 d-flex align-items-stretch">
                <div class="card bg-${lote.estado}">
                    <div class="card-header border-bottom-0">
                    <h6>Cod de Lote: ${lote.cod_lote}</h6>
                        <i class="fas fa-lg fa-cubes mr-1"></i>${lote.stock}
                    </div>
                    <div class="card-body pt-0">
                        <div class="row">
                            <div class="col-7">
                                <h2 class="lead"><b>${lote.nombre}</b></h2>
                                <ul class="ml-4 mb-0 fa-ul">
                                    <li class="small"><span class="fa-li"><i class="fa-solid fa-mortar-pestle mr-1"></i></span>Concentración: ${lote.concentracion}</li>
                                    <li class="small"><span class="fa-li"><i class="fa-solid fa-prescription-bottle mr-1"></i></span>Adicional: ${lote.adicional}</li>
                                    <li class="small"><span class="fa-li"><i class="fas fa-lg fa-flask mr-1"></i></span>Laboratorio: ${lote.nombre_laboratorio}</li>
                                    <li class="small"><span class="fa-li"><i class="fas fa-lg fa-copyright mr-1"></i></span>Tipo: ${lote.tipo}</li>
                                    <li class="small"><span class="fa-li"><i class="fas fa-lg fa-pills mr-1"></i></span>Presentación: ${lote.nombre_presentacion}</li>
                                    <li class="small"><span class="fa-li"><i class="fas fa-lg fa-calendar mr-1"></i></span>Vencimiento: ${lote.vencimiento}</li>
                                    <li class="small"><span class="fa-li"><i class="fas fa-lg fa-truck mr-1"></i></span>Proveedor: ${lote.proveedor}</li>
                                    <li class="small"><span class="fa-li"><i class="fas fa-lg fa-calendar-alt mr-1"></i></span>Mes: ${lote.mes}</li>
                                    <li class="small"><span class="fa-li"><i class="fas fa-lg fa-calendar-day mr-1"></i></span>dia: ${lote.dia}</li>
                                </ul>
                            </div>
                            <div class="col-5 text-center">
                                <img src="${lote.avatar}" alt="" class="img-circle img-fluid">
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="text-right">
                            <button class="editar btn btn-sm btn-success" title="Editar lote" type="button" data-toggle="modal" data-target="#editarlote"><i class="fas fa-pencil-alt mr-1"></i></button>
                            <button class="borrar_lote btn btn-sm btn-danger" title="Eliminar lote" type="button" data-toggle="modal" data-target="#remove-lotes"><i class="fas fa-trash-alt mr-1"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        `).join('');
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
    $(document).on('click', '.editar',(e)=>{
        const elemento= $(this)[0].activeElement.parentElement.parentElement.parentElement.parentElement;
        const id=$(elemento).attr('loteID');
        const stock=$(elemento).attr('stockID');
        $('#id_lote_prod').val(id);
        $('#codigo_lote').html(id);
        $('#stock').val(stock);
    });
    $('#form-editar-lote').submit(e => {
        e.preventDefault();
        const id = $('#id_lote_prod').val();
        const stock = $('#stock').val();
        funcion = "editar";
        $.post(
            '../controller/LoteController.php',
            { funcion, stock, id}
        ).done(response => {
            if (response === 'edit') {
                console.log(response);
                Swal.fire({
                    position: 'center',
                    icon: 'success',
                    title: 'Lote Editado con Éxito',
                    showConfirmButton: false,
                    timer: 1000
                }).then(() => {
                    Swal.close();
                    $('#editarlote').modal('hide');
                    $('#form-editar-lote').trigger('reset');
                    buscar_lotes();
                });
            } else {
                console.log(response);
                Swal.fire({
                    position: 'center',
                    icon: 'error',
                    title: response,
                    showConfirmButton: true,
                    timer: 1500
                });
            }
        })
        .fail(error => {
            console.error("Error en la solicitud AJAX:", error);
            Swal.fire({
                position: 'center',
                icon: 'error',
                title: 'Error en la solicitud AJAX',
                showConfirmButton: false,
                timer: 1500
            });
        });
    });
    $(document).on('click', '.borrar_lote', function (e) {
        const funcion = "borrar_lote";
        const elemento = $(this).closest('.col-12.col-sm-6.col-md-4.d-flex.align-items-stretch');
        const id = elemento.attr('loteID');
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-success',
                cancelButton: 'btn btn-danger mr-1'
            },
            buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
            title: '¿Estás seguro de que deseas eliminar el Lote ' + id + '?',
            text: '¡No podrás revertir esto!',
            icon: 'success',
            showCancelButton: true,
            confirmButtonText: '¡Sí, bórralo!',
            cancelButtonText: '¡No, cancela!',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                $.post('../controller/LoteController.php', { id, funcion }, (response) => {
                    if (response === 'borrado') {
                        swalWithBootstrapButtons.fire(
                            'Eliminado',
                            'Tu Lote ' + id + ' ha sido eliminado.',
                            'success'
                        );
                        buscar_lotes();
                    } else {
                        swalWithBootstrapButtons.fire(
                            'Cancelado',
                            'Tu Lote ' + id + ' no fue eliminado.',
                            'error'
                        );
                    }
                });
            } else if (result.dismiss === Swal.DismissReason.cancel) {
                swalWithBootstrapButtons.fire(
                    'Cancelado',
                    'Tu Lote ' + id + ' está a salvo :)',
                    'error'
                );
            }
        });
    });
});