$(document).ready(function() {
    var funcion
    var edit=false;
    buscar_product();
    $('#noVenta').change(function() {
        if ($(this).is(':checked')) {
            $('#precio').prop('disabled', true).val('0');
        } else {
            $('#precio').prop('disabled', false).val('');
        }
    });
    $('.select2').select2();
    rellenar_laboratorio();
    rellenar_type();
    rellenar_presentacion();
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
    function inicializarSelect2() {
    $('#laboratorio').select2({
        tags: true,
        tokenSeparators: [',', ' '],
        createTag: function (params) {
            return {
                id: params.term,
                text: params.term,
                newOption: true
            };
        }
    });
    $('#laboratorio').on('select2:select', function (e) {
        const data = e.params.data;
        if (data.newOption) {
            const nuevoLaboratorio = data.text;
            Swal.fire({
                title: `¿Confirmas que deseas crear el laboratorio "${nuevoLaboratorio}"?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí',
                cancelButtonText: 'Cancelar',
            }).then((result) => {
                if (result.isConfirmed) {
                    crearLaboratorioDesdeSelect2(nuevoLaboratorio);
                } else {
                    $('#laboratorio').val(null).trigger('change');
                }
            });
        }
    });
    }
    inicializarSelect2();
    function crearLaboratorioDesdeSelect2(nombreLaboratorio) {
    $.post('../controller/LaboratoryController.php', { nombre_laboratory: nombreLaboratorio, funcion: 'crear' }, (response) => {
        if (response === 'add') {
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: 'Laboratorio Creado con Éxito',
                showConfirmButton: false,
                timer: 1000
            });
            rellenar_laboratorio();
            $('#laboratorio').append(new Option(nombreLaboratorio, nombreLaboratorio, true, true));
            $('#laboratorio').trigger('change');
        } else {
            Swal.fire({
                position: 'center',
                icon: 'error',
                title: 'Error al Crear el Laboratorio',
                showConfirmButton: false,
                timer: 1500
            });
            rellenar_laboratorio();
        }
    });
    }
    function rellenar_type() {
        const funcion = "rellenar_type";
        $.post('../controller/typeController.php', { funcion })
            .done(function(response) {
                const types = JSON.parse(response);
                let template = '';
                types.forEach((tipo_producto) => {
                    template += `<option value="${tipo_producto.id}">${tipo_producto.nombre}</option>`;
                });
                $('#tipo').html(template);
            })
            .fail(function(error) {
                console.error("Error en rellenar_type:", error);
            });
    }
    function inicializarSelect2Tipo() {
    $('#tipo').select2({
        tags: true,
        tokenSeparators: [',', ' '],
        createTag: function (params) {
            return {
                id: params.term,
                text: params.term,
                newOption: true
            };
        }
    });
    $('#tipo').on('select2:select', function (e) {
        const data = e.params.data;
        if (data.newOption) {
            const nuevoTipo = data.text;
            Swal.fire({
                title: `¿Confirmas que deseas crear el tipo "${nuevoTipo}"?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí',
                cancelButtonText: 'Cancelar',
            }).then((result) => {
                if (result.isConfirmed) {
                    crearTipoDesdeSelect2(nuevoTipo);
                } else {
                    $('#tipo').val(null).trigger('change');
                }
            });
        }
    });
    }
    inicializarSelect2Tipo();
    function crearTipoDesdeSelect2(nombreTipo) {
    $.post('../controller/TypeController.php', { nombre_type: nombreTipo, funcion: 'crear' }, (response) => {
        if (response === 'add') {
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: 'Tipo Creado con Éxito',
                showConfirmButton: false,
                timer: 1000
            }); rellenar_type();
            $('#tipo').append(new Option(nombreTipo, nombreTipo, true, true));
            $('#tipo').trigger('change');
        } else {
            Swal.fire({
                position: 'center',
                icon: 'error',
                title: 'Error al Crear el Tipo',
                showConfirmButton: false,
                timer: 1500
            }); rellenar_type();
        }
    });
    }
    function rellenar_presentacion() {
        const funcion = "rellenar_presentacion";
        $.post('../controller/presentacionesController.php', { funcion })
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
    function inicializarSelect2Presentacion() {
    $('#presentacion').select2({
        tags: true,
        tokenSeparators: [',', ' '],
        createTag: function (params) {
            return {
                id: params.term,
                text: params.term,
                newOption: true
            };
        }
    });
    $('#presentacion').on('select2:select', function (e) {
        const data = e.params.data;
        if (data.newOption) {
            const nuevaPresentacion = data.text;
            Swal.fire({
                title: `¿Confirmas que deseas crear la presentación "${nuevaPresentacion}"?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí',
                cancelButtonText: 'Cancelar',
            }).then((result) => {
                if (result.isConfirmed) {
                    crearPresentacionDesdeSelect2(nuevaPresentacion);
                } else {
                    $('#presentacion').val(null).trigger('change');
                }
            });
        }
    });
    }
    inicializarSelect2Presentacion();
    function crearPresentacionDesdeSelect2(nombrePresentacion) {
    $.post('../controller/PresentacionesController.php', { nombre_pre: nombrePresentacion, funcion: 'crear' }, (response) => {
        if (response === 'add') {
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: 'Presentación Creada con Éxito',
                showConfirmButton: false,
                timer: 1000
            }); rellenar_presentacion();
            $('#presentacion').append(new Option(nombrePresentacion, nombrePresentacion, true, true));
            $('#presentacion').trigger('change');
        } else {
            Swal.fire({
                position: 'center',
                icon: 'error',
                title: 'Error al Crear la Presentación',
                showConfirmButton: false,
                timer: 1500
            }); rellenar_presentacion();
        }
    });
    }
    $(document).on('click', '.crearpd', function() {
        $('#form-crear-producto').trigger('reset');
        $('#noVenta').prop('checked', false);
            $('#precio').prop('disabled', false).val(precio);
    });
    $('#form-crear-producto').submit(e => {
        e.preventDefault();
        const id_edit_prod = $('#id_edit_prod').val();
        const nombre = $('#nombre-producto').val();
        const concentracionText = $('#concentracion').val();
        const unidad = $('#unidad').val();
        let adicional = $('#adicional').val();
        const precio = $('#precio').val();
        const prod_lab = $('#laboratorio').val();
        const prod_tip_prod = $('#tipo').val();
        const prod_present = $('#presentacion').val();
        const concentracion = concentracionText + ' ' + unidad;
        if (adicional.trim() === '') {
            adicional = 'Sin Información';
        }
        if (edit) {
            funcion = "editar";
        } else {
            funcion = "crear";
        }
        $.post(
            '../controller/ProductoController.php',
            { funcion, id_edit_prod, nombre, concentracion, adicional, precio, prod_lab, prod_tip_prod, prod_present }
        )
        .done(response => {
            if (response === 'add') {
                Swal.fire({
                    position: 'center',
                    icon: 'success',
                    title: 'Producto Creado con Éxito',
                    showConfirmButton: false,
                    timer: 1000
                }).then(() => {
                    Swal.close();
                    $('#crearproducto').modal('hide');
                    $('#form-crear-producto').trigger('reset');
                    buscar_product();
                    edit == false;
                });
            } else if (response === 'edit') {
                Swal.fire({
                    position: 'center',
                    icon: 'success',
                    title: 'Producto Editado con Éxito',
                    showConfirmButton: false,
                    timer: 1000
                }).then(() => {
                    Swal.close();
                    $('#crearproducto').modal('hide');
                    $('#form-crear-producto').trigger('reset');
                    buscar_product();
                    edit == false;
                });
            } else {
                Swal.fire({
                    position: 'center',
                    icon: 'error',
                    title: response,
                    showConfirmButton: true,
                    timer: 1500
                });
                edit == false;
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
    function buscar_product(consulta) {
        $.post('../controller/ProductoController.php', { consulta, funcion: 'buscar_product' }, (Response) => {
            try {
                const products = JSON.parse(Response);
                mostrarProductos(products);
            } catch (error) {
                console.error("Error al analizar la respuesta JSON:", error);
            }
        });
    }
    function mostrarProductos(products) {
        const productContainer = $('#productos');
        const template = products.map(product => `
            <div proId="${product.id}" proNombre="${product.nombre}" conNombre="${product.concentracion}" addNombre="${product.adicional}" preNombre="${product.precio}" nLabNombre="${product.laboratorio_id}" nTypeNombre="${product.tipo_id}" nPreNombre="${product.presentacion_id}" avaNombre="${product.avatar}" class="col-12 col-sm-6 col-md-4 d-flex align-items-stretch">
                <div class="card bg-light">
                    <div class="card-header text-muted border-bottom-0">
                        <i class="fas fa-lg fa-cubes mr-1"></i>${product.stock}
                    </div>
                    <div class="card-body pt-0">
                        <div class="row">
                            <div class="col-7">
                                <h2 class="lead"><b>${product.nombre}</b></h2>
                                <h4 class="lead"><b><i class="fas fa-lg fa-dollar-sign mr-1"></i>${product.precio}</b></h4>
                                <ul class="ml-4 mb-0 fa-ul text-muted">
                                    <li class="small"><span class="fa-li"><i class="fa-solid fa-mortar-pestle mr-1"></i></span>Concentración: ${product.concentracion}</li>
                                    <li class="small"><span class="fa-li"><i class="fa-solid fa-prescription-bottle mr-1"></i></span>Adicional: ${product.adicional}</li>
                                    <li class="small"><span class="fa-li"><i class="fas fa-lg fa-flask mr-1"></i></span>Laboratorio: ${product.nombre_laboratorio}</li>
                                    <li class="small"><span class="fa-li"><i class="fas fa-lg fa-copyright mr-1"></i></span>Tipo: ${product.tipo}</li>
                                    <li class="small"><span class="fa-li"><i class="fas fa-lg fa-pills mr-1"></i></span>Presentación: ${product.nombre_presentacion}</li>
                                </ul>
                            </div>
                            <div class="col-5 text-center">
                                <img src="${product.avatar}" alt="" class="img-circle img-fluid">
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="text-right">
                            <button class="imagen btn btn-sm bg-teal" title="Editar Imagen" type="button" data-toggle="modal" data-target="#cambiarlogo"><i class="fas fa-image"></i></button>
                            <button class="editar btn btn-sm btn-success" title="Editar Producto" type="button" data-toggle="modal" data-target="#crearproducto"><i class="fas fa-pencil-alt mr-1"></i></button>
                            <button class="lote btn btn-sm btn-primary" title="Agregar Lote" type="button" data-toggle="modal" data-target="#"><i class="fas fa-plus-square"></i></button>
                            <button class="borrar_produts btn btn-sm btn-danger" title="Eliminar Producto" type="button" data-toggle="modal" data-target="#remove-products"><i class="fas fa-trash-alt mr-1"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        `).join('');
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
    $(document).on('click','.imagen',(e)=>{
        funcion="cambiar_avatar";
        const elemento= $(this)[0].activeElement.parentElement.parentElement.parentElement.parentElement;
        const id=$(elemento).attr('proId');
        const avatar=$(elemento).attr('avaNombre');
        const nombre=$(elemento).attr('proNombre');
        $('#funcion').val(funcion);
        $('#id_logo_prod').val(id);
        $('#avatar').val(avatar);
        $('#logoactual1').attr('src',avatar);
        $('#nombre_logo').html(nombre);
    });
    $('#form-logo').submit((e) => {
        e.preventDefault();
        const fileInput = $('#foto')[0];
        const fileType = fileInput.files[0].type;
        const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/bmp'];
        if (!allowedTypes.includes(fileType)) {
            Swal.fire({
                position: 'center',
                icon: 'error',
                title: 'No se permite este archivo. Solo se permiten imágenes.',
                showConfirmButton: false,
                timer: 1500
            });
            return;
        }
        let formData = new FormData($('#form-logo')[0]);
        $.ajax({
            url: '../controller/ProductoController.php',
            type: 'POST',
            data: formData,
            cache: false,
            processData: false,
            contentType: false
        }).done(function (response) {
            const json = JSON.parse(response);
            if (json.alert == 'edit') {
                $('#logoactual1').attr('src', json.ruta);
                buscar_product();
                $('#form-logo').trigger('reset');
                Swal.fire({
                    position: 'center',
                    icon: 'success',
                    title: 'Realizado Éxito',
                    showConfirmButton: false,
                    timer: 1500
                });
                $('#cambiarlogo').modal('hide');
            } else {
                $('#form-logo').trigger('reset');
                Swal.fire({
                    position: 'center',
                    icon: 'error',
                    title: 'Ha Ocurrido Un Error!!',
                    showConfirmButton: false,
                    timer: 1500
                });
            }
            $('#cambiarlogo').modal('hide');
        }).fail(function (error) {
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
    $(document).on('click', '.editar', function() {
        const elemento = $(this).closest('.col-12.col-sm-6.col-md-4.d-flex.align-items-stretch');
        const id = elemento.attr('proId');
        const nombre = elemento.attr('proNombre');
        const adicional = elemento.attr('addNombre');
        const precio = elemento.attr('preNombre');
        const prod_lab = elemento.attr('nLabNombre');
        const prod_tip_prod = elemento.attr('nTypeNombre');
        const prod_present = elemento.attr('nPreNombre');
        const concentracionCompleta = elemento.attr('conNombre');
        const partes = concentracionCompleta.split(' ');
        const concentracion = partes.slice(0, -1).join(' ');
        const unidad = partes.slice(-1)[0];
        $('#id_edit_prod').val(id);
        $('#nombre-producto').val(nombre);
        $('#concentracion').val(concentracion);
        $('#unidad').val(unidad);
        $('#adicional').val(adicional);
        if (parseFloat(precio) === 0) {
            $('#noVenta').prop('checked', true);
            $('#precio').prop('disabled', true).val('0');
        } else {
            $('#noVenta').prop('checked', false);
            $('#precio').prop('disabled', false).val(precio);
        }
        $('#laboratorio').val(prod_lab).trigger('change');
        $('#tipo').val(prod_tip_prod).trigger('change');
        $('#presentacion').val(prod_present).trigger('change');
        edit=true;
    });
    // $(document).on('click', '.lote', function() {
    //     // Lógica para el evento de clic en el botón de agregar lote
    //     // ...
    // });
    $(document).on('click', '.borrar_produts', function (e) {
        const funcion = "borrar_produts";
        const elemento = $(this).closest('.col-12.col-sm-6.col-md-4.d-flex.align-items-stretch');
        const id = elemento.attr('proId');
        const nombre = elemento.attr('proNombre');
        const avatar = elemento.attr('avaNombre');
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-success',
                cancelButton: 'btn btn-danger mr-1'
            },
            buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
            title: '¿Estás seguro de que deseas eliminar ' + nombre + '?',
            text: '¡No podrás revertir esto!',
            icon: 'success',
            showCancelButton: true,
            confirmButtonText: '¡Sí, bórralo!',
            cancelButtonText: '¡No, cancela!',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                $.post('../controller/ProductoController.php', { id, funcion, avatar }, (Response) => {
                    if (Response === 'borrado') {
                        swalWithBootstrapButtons.fire(
                            'Eliminado',
                            'Tu Producto ' + nombre + ' ha sido eliminado.',
                            'success'
                        );
                        buscar_product();
                    } else {
                        swalWithBootstrapButtons.fire(
                            'Cancelado',
                            'Tu Producto ' + nombre + ' no fue eliminado porque está siendo usado en un Lote.',
                            'error'
                        );
                    }
                });
            } else if (result.dismiss === Swal.DismissReason.cancel) {
                swalWithBootstrapButtons.fire(
                    'Cancelado',
                    'Tu Producto ' + nombre + ' está a salvo :)',
                    'error'
                );
            }
        });
    });
});