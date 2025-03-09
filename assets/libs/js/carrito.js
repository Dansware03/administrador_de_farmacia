$(document).ready(function () {
  RecuperarLS_carrito();
  Contar_productos();
  RecuperarLS_carrito_Pedido();
  function mostrarNotificacion(mensaje, tipo) {
    toastr[tipo](mensaje);
  }
  function actualizarTotalCarrito() {
    let total = 0;
    $("#lista_carrito tr").each(function () {
      const precio = parseFloat(
        $(this).find("td:eq(4)").text().replace("$", "")
      );
      total += precio;
    });
    $("#total_carrito").text(`Total: $${total.toFixed(2)}`);
  }
  $(document).on("click", ".agg_compra", function () {
    const elemento = $(this).closest(
      ".col-12.col-sm-6.col-md-4.d-flex.align-items-stretch"
    );
    const id = elemento.attr("proId");
    const nombre = elemento.attr("proNombre");
    const adicional = elemento.attr("addNombre");
    const precio = elemento.attr("preNombre");
    const prod_lab = elemento.attr("nLabNombre");
    const prod_tip_prod = elemento.attr("nTypeNombre");
    const prod_present = elemento.attr("nPreNombre");
    const concentracionCompleta = elemento.attr("conNombre");
    const avatar = elemento.attr("avaNombre");
    const stock = elemento.attr("productStock");
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
      stock: stock,
      cantidad: 1,
    };
    // Verifica si el producto ya está en el carrito
    const productoExistente = $("#lista_carrito").find(
      `[data_id="${producto.id}"]`
    );
    if (productoExistente.length) {
      // Muestra notificación de que el producto ya está en el carrito
      mostrarNotificacion("Este producto ya está en el carrito", "info");
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
      $("#lista_carrito").append(template).hide().fadeIn(500);
      AgregarLS(producto);
      let contador;
      Contar_productos();

      // Muestra notificación de éxito
      mostrarNotificacion("Producto agregado al carrito", "success");

      // Actualiza el total del carrito
      actualizarTotalCarrito();
    }
  });
  $(document).on("click", ".borrar_de_carrito", function () {
    const elemento = $(this).closest("tr");
    const id = $(elemento).attr("data_id");
    elemento.fadeOut(500, function () {
      $(this).remove();
      Eliminar_producto_LS(id);
      Contar_productos();
      actualizarTotalCarrito();
      calcularTotal();
    });
  });
  $(document).on("click", "#vaciar_carrito", (e) => {
    const elemento = $(this).closest("tr");
    $("#lista_carrito").empty();
    EliminarLS();
    Contar_productos();
  });
  function RecuperarLS() {
    let productos;
    if (localStorage.getItem("productos") === null) {
      productos = [];
    } else {
      productos = JSON.parse(localStorage.getItem("productos"));
    }
    return productos;
  }
  function AgregarLS(producto) {
    let productos;
    productos = RecuperarLS();
    productos.push(producto);
    localStorage.setItem("productos", JSON.stringify(productos));
  }
  function RecuperarLS_carrito() {
    let productos;
    productos = RecuperarLS();
    productos.forEach((producto) => {
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
      $("#lista_carrito").append(template);
    });
  }
  function Eliminar_producto_LS(id) {
    let productos;
    productos = RecuperarLS();
    productos.forEach(function (producto, indice) {
      if (producto.id === id) {
        productos.splice(indice, 1);
        mostrarNotificacion("Se Elimino el Producto.", "success");
      }
    });
    localStorage.setItem("productos", JSON.stringify(productos));
  }
  function EliminarLS() {
    localStorage.clear();
    mostrarNotificacion("Se Vacio el Carrito.", "success");
  }
  function Contar_productos() {
    let productos;
    let contador = 0;
    productos = RecuperarLS();
    productos.forEach((producto) => {
      contador++;
    });
    $("#contador").html(contador);
  }
  $(document).on("click", "#Procesar_pedido", (e) => {
    Procesar_pedido();
  });
  function Procesar_pedido() {
    let productos = RecuperarLS();
    if (productos.length === 0) {
      mostrarNotificacion("Carrito esta Vacio.", "info");
    } else {
      location.href = "../pages/adm_retiro.php";
    }
  }
  function RecuperarLS_carrito_Pedido() {
    let productos;
    productos = RecuperarLS();
    productos.forEach((producto) => {
      template = `
            <tr data_id="${producto.id}">
                <td>${producto.nombre}</td>
                <td>${producto.stock}</td>
                <td>${producto.precio}</td>
                <td>${producto.concentracionCompleta}</td>
                <td> <input type="number" min="1" class="form-control cantidad_producto" value="${
                  producto.cantidad
                }"> </input> </td>
                <td class="subtotales">
                    <h5>${producto.precio * producto.cantidad}</h5>
                </td>
                <td><button class="borrar_de_carrito btn btn-danger"><i class="fas fa-times-circle"></i></button></td>
            </tr>
        `;
      $("#lista-compra").append(template);
    });
  }
  $("#cp").keyup((e) => {
    let id, cantidad, producto, productos, montos;
    producto = e.target.parentElement.parentElement;
    id = $(producto).attr("data_id");
    cantidad = producto.querySelector("input").value;
    montos = document.querySelectorAll(".subtotales");
    productos = RecuperarLS();
    productos.forEach(function (prod, indice) {
      if (prod.id === id) {
        prod.cantidad = cantidad;
        montos[indice].innerHTML = `<h5>${
          cantidad * productos[indice].precio
        }</h5>`;
      }
    });
    localStorage.setItem("productos", JSON.stringify(productos));
    calcularTotal();
    calcularVuelto();
  });
  if (window.location.pathname.includes("adm_retiro.php")) {
    calcularTotal();
    function calcularTotal() {
      let productos, subtotal, conIva, totalSinDescuento, descuentoInput;
      let total = 0,
        iva = 0.08; // 8% de impuesto
      productos = RecuperarLS();
      productos.forEach((producto) => {
        let subtotalProducto = Number(producto.precio * producto.cantidad);
        total += subtotalProducto;
      });
      descuentoInput =
        parseFloat(document.getElementById("descuento").value) || 0;
      total -= descuentoInput;
      totalSinDescuento = total.toFixed(2);
      conIva = parseFloat(total * iva).toFixed(2);
      subtotal = parseFloat(total - conIva).toFixed(2);
      total = parseFloat(total + parseFloat(conIva)).toFixed(2);
      document.getElementById("subtotal").textContent = subtotal;
      document.getElementById("total_sin_descuento").textContent =
        totalSinDescuento;
      document.getElementById("conIva").textContent = conIva;
      document.getElementById("total").textContent = total;
    }
    document.getElementById("pago").addEventListener("keyup", function () {
      calcularVuelto();
    });
    window.addEventListener("load", function () {
      calcularVuelto();
    });
    function calcularVuelto() {
      let ingresoInput = document.getElementById("pago");
      let total = parseFloat(document.getElementById("total").textContent) || 0;
      let ingreso = parseFloat(ingresoInput.value);
      if (isNaN(ingreso) || ingresoInput.value.trim() === "" || ingreso === 0) {
        document.getElementById("vuelto").textContent = "0";
        return;
      }
      let vuelto = ingreso - total;
      document.getElementById("vuelto").textContent = vuelto.toFixed(2);
    }
  }
  $(document).on("click", "#procesar_compra", (e) => {
    procesar_compra();
  });
  function procesar_compra() {
    let nombre, ci;
    nombre = $("#cliente").val();
    ci = $("#ci").val();
    if (RecuperarLS().length == 0) {
      mostrarNotificacion("El Carrito está Vacío.", "info");
      location.href = "../pages/adm_catalogo.php";
    } else if (nombre == "") {
      mostrarNotificacion("Debe Colocar un Nombre al Cliente.", "info");
    } else {
      verificarStock().then((error) => {
        if (error == 0) {
          Registrar_Compra(nombre, ci);
          mostrarNotificacion("Se Realizó la Compra.", "success");
          // setTimeout(function() {
          //     location.href = '../pages/adm_catalogo.php';
          // }, 1500);
        } else {
          mostrarNotificacion("Hay un Producto que no tiene Stock.", "info");
        }
      });
    }
  }
  async function verificarStock() {
    let productos;
    const funcion = "verificarStock";
    productos = RecuperarLS();
    const response = await fetch("../controller/ProductoController.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body:
        "funcion=" +
        funcion +
        "&productos=" +
        encodeURIComponent(JSON.stringify(productos)),
    });
    const error = await response.text();
    return error;
  }
  function Registrar_Compra(nombre, ci) {
    const funcion = "registrar_compra";
    let total = $("#total")[0].textContent;
    let productos = RecuperarLS();

    // Verificar si hay productos en el carrito antes de intentar registrar la compra
    if (productos.length > 0) {
      // Realizar la solicitud AJAX para registrar la compra
      $.ajax({
        type: "POST",
        url: "../controller/CompraController.php",
        data: {
          funcion: funcion,
          total: total,
          nombre: nombre,
          ci: ci,
          productos: JSON.stringify(productos),
        },
        success: function (response) {
          console.log(response);
          mostrarNotificacion("Compra registrada exitosamente.", "success");
          setTimeout(function() {
            EliminarLS();
              // Redirigir a la página deseada
              location.href = '../pages/adm_catalogo.php';
          }, 1500);
        },
        error: function (error) {
          console.error(error);
          mostrarNotificacion(
            "Error al registrar la compra. Por favor, inténtelo nuevamente.",
            "error"
          );
        },
      });
    } else {
      mostrarNotificacion(
        "El carrito está vacío. No se puede procesar la compra.",
        "info"
      );
    }
  }
});
