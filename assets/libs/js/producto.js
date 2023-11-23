$(document).ready(function() {
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
        $.post('../controller/LaboratoryController.php', { funcion }, (response) => {
            const laboratorios = JSON.parse(response);
            const opciones = laboratorios.map(laboratorio => `<option value="${laboratorio.id}">${laboratorio.nombre}</option>`);
            $('#laboratorio').html(opciones.join(''));
        });
    }
    function rellenar_type() {
        const funcion = "rellenar_type";
        $.post('../controller/typeController.php', { funcion }, (response) => {
            const types = JSON.parse(response);
            let templete = '';
            types.forEach((tipo_producto) => {
                templete += `<option value="${tipo_producto.id}">${tipo_producto.nombre}</option>`;
            });
            $('#tipo').html(templete);
        });
    }
    function rellenar_presentacion() {
        const funcion = "rellenar_presentacion";
        $.post('../controller/presentacionesController.php', { funcion }, (response) => {
            const presentaciones = JSON.parse(response);
            let templete = '';
            presentaciones.forEach((presentacion) => {
                templete += `<option value="${presentacion.id}">${presentacion.nombre}</option>`;
            });
            $('#presentacion').html(templete);
        });
    }
    $('#form-crear-producto').submit(e => {
        let nombre = $('#nombre-producto').val();
        let concentracionText = $('#concentracion').val();
        let unidad = $('#unidad').val();
        let adicional = $('#adicional').val();
        let precio = $('#precio').val();
        let prod_lab = $('#laboratorio').val();
        let prod_tip_prod = $('#tipo').val();
        let prod_present = $('#presentacion').val();
        let concentracion = concentracionText + ' ' + unidad;
        funcion="crear";
        $.post('../controller/ProductoController.php', { funcion , nombre , concentracion , adicional , precio , prod_lab , prod_tip_prod , prod_present}, (response) => {
            if (response=='add') {
                Swal.fire({
                    position: 'center',
                    icon: 'success',
                    title: 'Producto Creado con Exito',
                    showConfirmButton: false,
                    timer: 1000
                }).then(() => {
                    Swal.close();
                    $('#crearproducto').modal('hide');
                    $('#form-crear-producto').trigger('reset');
                    buscar_product();
                });
            } else {
                Swal.fire({
                    position: 'center',
                    icon: 'error',
                    title: 'Producto Ya Existe',
                    showConfirmButton: false,
                    timer: 1500
                });
            }
        });
        e.preventDefault();
    });
    function buscar_product(consulta) {
        $.post('../controller/ProductoController.php', { consulta, funcion: 'buscar_product' }, (Response) => {
            const products = JSON.parse(Response);
            const productContainer = $('#productos');
            const template = products.map(product => `
            <div proId="${product.id}" proNombre="${product.nombre}" conNombre="${product.concentracion}" addNombre="${product.adicional}" preNombre="${product.precio}" stkNombre="${product.stock}" nLabNombre="${product.nombre_laboratorio}" nTypeNombre="${product.tipo}" nPreNombre="${product.nombre_presentacion}" avaNombre="${product.avatar}" class="col-12 col-sm-6 col-md-4 d-flex align-items-stretch" bis_skin_checked="1">
            <div class="card bg-light" bis_skin_checked="1">
                <div class="card-header text-muted border-bottom-0" bis_skin_checked="1">
                <i class="fas fa-lg fa-cubes mr-1"></i>${product.stock}
                </div>
                <div class="card-body pt-0" bis_skin_checked="1">
                <div class="row" bis_skin_checked="1">
                    <div class="col-7" bis_skin_checked="1">
                    <h2 class="lead"><b>${product.nombre}</b></h2>
                    <h4 class="lead"><b><i class="fas fa-lg fa-dollar-sign mr-1"></i>${product.precio}</b></h4>

                    <ul class="ml-4 mb-0 fa-ul text-muted">
                        <li class="small"><span class="fa-li"><i class="fa-solid fa-mortar-pestle mr-1"></i></span>Concentración: ${product.concentracion}</li>
                        <li class="small"><span class="fa-li"><i class="fa-solid fa-prescription-bottle mr-1"></i></span>Adicional: ${product.adicional}</li>
                        <li class="small"><span class="fa-li"><i class="fas fa-lg fa-flask mr-1"></i></span>Laboratorio: ${product.nombre_laboratorio}</li>
                        <li class="small"><span class="fa-li"><i class="fas fa-lg fa-copyright mr-1"></i></span>Tipo: ${product.tipo}</li>
                        <li class="small"><span class="fa-li"><i class="fas fa-lg fa-pills mr-1"></i></span>Presentaión: ${product.nombre_presentacion}</li>
                    </ul>
                    </div>
                    <div class="col-5 text-center" bis_skin_checked="1">
                    <img src="${product.avatar}" alt="" class="img-circle img-fluid">
                    </div>
                </div>
                </div>
                <div class="card-footer" bis_skin_checked="1">
                <div class="text-right" bis_skin_checked="1">
                <button class="imagen btn btn-sm bg-teal" title="Editar Imagen" type="button" data-toggle="modal" data-target="#fotoproducts"><i class="fas fa-image"></i></button>
                <button class="editar btn btn-sm btn-success" title="Editar Producto" type="button" data-toggle="modal" data-target="#crearproducto"><i class="fas fa-pencil-alt mr-1"></i></button>
                <button class="lote btn btn-sm btn-primary" title="Agregar Lote" type="button" data-toggle="modal" data-target="#"><i class="fas fa-plus-square"></i></button>
                <button class="borrar_produts btn btn-sm btn-danger" title="Eliminar Producto" type="button" data-toggle="modal" data-target="#remove-products"><i class="fas fa-trash-alt mr-1"></i></button>
                </div>
                </div>
            </div>
            </div>
            `).join('');
            productContainer.html(template);
        });
    }
    $(document).on('keyup','#buscar_producto',function(){
        let valor = $(this).val();
        if (valor!==""){
            buscar_product(valor);
        } else {
            buscar_product();
        }
    });
    $(document).on('click', '.imagen', function() {
        const producto = $(this).closest('.col-12');
        const id = producto.attr('proId');
        const avatar = producto.attr('avaNombre');
        const nombre = producto.attr('proNombre');
        $('#funcion').val(funcion);
        $('#id_logo_products').val(id);
        $('#avatar').val(avatar);
        $('#fotoproducts1').attr('src',avatar);
        $('#nombre_logo').html(nombre);
    });
    $('#form-foto').submit((e) => {
        let formData = new FormData($('#form-foto')[0]);
        $.ajax({
        url: '../controller/ProductoController.php',
        type: 'POST',
        data: formData,
        cache: false,
        processData: false,
        contentType: false
        }).done(function (Response) {
        const json = JSON.parse(Response);
        if (json.alert == 'edit') {
            $('#fotoproducts1').attr('src', json.ruta);
            buscarUsuario(id_usuario);
            $('#form-foto').trigger('reset');
            Swal.fire({
            position: 'center',
            icon: 'success',
            title: 'Realizado Exito',
            showConfirmButton: false,
            timer: 1500
            });
            $('#cambiofoto').modal('hide');
        } else {
            $('#form-foto').trigger('reset');
            Swal.fire({
            position: 'center',
            icon: 'error',
            title: 'Ah Ocurrido Un Error!!',
            showConfirmButton: false,
            timer: 1500
            });
        }
        $('#cambiofoto').modal('hide');
        });
        e.preventDefault();
    });
    $(document).on('click', '.editar', function() {
        // Lógica para el evento de clic en el botón de editar
        // ...

        // Ejemplo: abrir el modal de edición
        $('#crearproducto').modal('show');
    });

    $(document).on('click', '.lote', function() {
        // Lógica para el evento de clic en el botón de agregar lote
        // ...

        // Ejemplo: abrir el modal de agregar lote
        // $('#modalAgregarLote').modal('show');
    });

    $(document).on('click', '.borrar_produts', function() {
        // Lógica para el evento de clic en el botón de eliminar producto
        // ...

        // Ejemplo: abrir el modal de confirmación de eliminación
        // $('#remove-products').modal('show');
    });
});