$(document).ready(function () {
    RecuperarLS_carrito();
    Contar_productos();
    // Función para mostrar notificación
    function mostrarNotificacion(mensaje, tipo) {
        toastr[tipo](mensaje);
    }
    // Función para actualizar el total del carrito
    function actualizarTotalCarrito() {
        let total = 0;
        $('#lista_carrito tr').each(function () {
            const precio = parseFloat($(this).find('td:eq(4)').text().replace('$', ''));
            total += precio;
        });

        // Muestra el total en algún lugar de tu interfaz
        $('#total_carrito').text(`Total: $${total.toFixed(2)}`);
    }
    // Evento de clic para agregar productos al carrito
    $(document).on('click', '.agg_compra', function () {
        const elemento = $(this).closest('.col-12.col-sm-6.col-md-4.d-flex.align-items-stretch');
        const id = elemento.attr('proId');
        const nombre = elemento.attr('proNombre');
        const adicional = elemento.attr('addNombre');
        const precio = elemento.attr('preNombre');
        const prod_lab = elemento.attr('nLabNombre');
        const prod_tip_prod = elemento.attr('nTypeNombre');
        const prod_present = elemento.attr('nPreNombre');
        const concentracionCompleta = elemento.attr('conNombre');
        const avatar = elemento.attr('avaNombre');
        const producto = {
            id: id,
            nombre: nombre,
            adicional: adicional,
            precio: precio,
            prod_lab: prod_lab,
            prod_tip_prod: prod_tip_prod,
            prod_present: prod_present,
            concentracionCompleta: concentracionCompleta,
            avatar: avatar,
            cantidad: 1
        };
        // Verifica si el producto ya está en el carrito
        const productoExistente = $('#lista_carrito').find(`[data_id="${producto.id}"]`);
        if (productoExistente.length) {
            // Muestra notificación de que el producto ya está en el carrito
            mostrarNotificacion('Este producto ya está en el carrito', 'info');
        } else {
            // Agrega una nueva fila si el producto no está en el carrito
            const template = `
                <tr data_id="${producto.id}">
                    <td>${producto.id}</td>
                    <td>${producto.nombre}</td>
                    <td>${producto.adicional}</td>
                    <td>${producto.concentracionCompleta}</td>
                    <td>${producto.precio}</td>
                    <td><button class="borrar_de_carrito btn btn-danger"><i class="fas fa-times-circle"></i></button></td>
                </tr>
            `;
            $('#lista_carrito').append(template).hide().fadeIn(500);
            AgregarLS(producto);
            let contador;
            Contar_productos();

            // Muestra notificación de éxito
            mostrarNotificacion('Producto agregado al carrito', 'success');

            // Actualiza el total del carrito
            actualizarTotalCarrito();
        }
    });
    // Evento de clic para eliminar productos del carrito
    $(document).on('click', '.borrar_de_carrito', function () {
        const elemento = $(this).closest('tr');
        const id = $(elemento).attr('data_id')
        elemento.fadeOut(500, function () {
            $(this).remove();
            Eliminar_producto_LS(id);
            Contar_productos();
            // Actualiza el total del carrito después de eliminar un producto
            actualizarTotalCarrito();
        });
    });
    $(document).on('click', '#vaciar_carrito', (e) => {
        const elemento = $(this).closest('tr');
        $('#lista_carrito').empty();
        EliminarLS();
        Contar_productos();
    });
    function RecuperarLS() {
        let productos;
        if (localStorage.getItem('productos') === null) {
            productos = [];
        } else {
            productos = JSON.parse(localStorage.getItem('productos'));
        }
        return productos
    }
    
    function AgregarLS(producto) {
        let productos;
        productos = RecuperarLS();
        productos.push(producto);
        localStorage.setItem('productos', JSON.stringify(productos));
    }
    function RecuperarLS_carrito() {
        let productos;
        productos = RecuperarLS();
        productos.forEach(producto => {
            template = `
            <tr data_id="${producto.id}">
                <td>${producto.id}</td>
                <td>${producto.nombre}</td>
                <td>${producto.adicional}</td>
                <td>${producto.concentracionCompleta}</td>
                <td>${producto.precio}</td>
                <td><button class="borrar_de_carrito btn btn-danger"><i class="fas fa-times-circle"></i></button></td>
            </tr>
        `;
        $('#lista_carrito').append(template);
        });
    }
    function Eliminar_producto_LS(id) {
        let productos;
        productos = RecuperarLS();
        productos.forEach(function(producto,indice){
            if (producto.id===id) {
                productos.splice(indice,1)
            }
        });
        localStorage.setItem('productos', JSON.stringify(productos));
    }
    function EliminarLS() {
        localStorage.clear();
    }
    function Contar_productos() {
        let productos;
        let contador=0;
        productos=RecuperarLS();
        productos.forEach(producto => {
            contador++;
        });
        $('#contador').html(contador);
    }
});
