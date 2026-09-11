$(document).ready(function () {
  RecuperarLS_carrito();
  Contar_productos();
  RecuperarLS_carrito_Pedido();

  function mostrarNotificacion(mensaje, tipo) {
    if (typeof toastr !== "undefined") {
      toastr[tipo](mensaje);
    }
  }

  function actualizarTotalCarrito() {
    let total = 0;
    $("#lista_carrito tr").each(function () {
      const precio = parseFloat(
        $(this).find("td:eq(4)").text().replace("$", "")
      ) || 0;
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
      mostrarNotificacion("Este insumo ya se encuentra en la solicitud", "info");
    } else {
      const template = `
        <tr data_id="${producto.id}">
            <td class="small fw-bold">${producto.id}</td>
            <td class="small text-truncate" style="max-width: 120px;" title="${producto.nombre}">${producto.nombre}</td>
            <td class="small">${producto.cantidad}</td>
            <td class="text-end">
              <button class="borrar_de_carrito btn btn-sm btn-outline-danger p-0 px-1" title="Quitar">
                <i class="bi bi-x-lg"></i>
              </button>
            </td>
        </tr>
      `;
      $("#lista_carrito").append(template);
      AgregarLS(producto);
      Contar_productos();
      mostrarNotificacion("Insumo agregado a la solicitud", "success");
      actualizarTotalCarrito();
    }
  });

  $(document).on("click", ".borrar_de_carrito", function () {
    const elemento = $(this).closest("tr");
    const id = $(elemento).attr("data_id");
    $(elemento).fadeOut(200, function () {
      $(this).remove();
      Eliminar_producto_LS(id);
      Contar_productos();
      actualizarTotalCarrito();
      if (typeof calcularTotal === "function") {
        calcularTotal();
      }
    });
  });

  $(document).on("click", "#vaciar_carrito", (e) => {
    e.preventDefault();
    $("#lista_carrito").empty();
    EliminarLS();
    Contar_productos();
    mostrarNotificacion("Se ha vaciado la solicitud", "warning");
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
    let productos = RecuperarLS();
    productos.push(producto);
    localStorage.setItem("productos", JSON.stringify(productos));
  }

  function RecuperarLS_carrito() {
    let productos = RecuperarLS();
    $("#lista_carrito").empty();
    productos.forEach((producto) => {
      const template = `
        <tr data_id="${producto.id}">
            <td class="small fw-bold">${producto.id}</td>
            <td class="small text-truncate" style="max-width: 120px;" title="${producto.nombre}">${producto.nombre}</td>
            <td class="small">${producto.cantidad}</td>
            <td class="text-end">
              <button class="borrar_de_carrito btn btn-sm btn-outline-danger p-0 px-1" title="Quitar">
                <i class="bi bi-x-lg"></i>
              </button>
            </td>
        </tr>
      `;
      $("#lista_carrito").append(template);
    });
  }

  function Eliminar_producto_LS(id) {
    let productos = RecuperarLS();
    productos.forEach((producto, indice) => {
      if (producto.id === id) {
        productos.splice(indice, 1);
      }
    });
    localStorage.setItem("productos", JSON.stringify(productos));
  }

  function EliminarLS() {
    localStorage.removeItem("productos");
  }

  function Contar_productos() {
    let productos = RecuperarLS();
    let contador = productos.length;
    $("#contador").text(contador);
  }

  function RecuperarLS_carrito_Pedido() {
    let productos = RecuperarLS();
    $("#lista-compra").empty();
    productos.forEach((producto) => {
      const subtotal = (parseFloat(producto.precio || 0) * parseInt(producto.cantidad || 1)).toFixed(2);
      const template = `
        <tr data_id="${producto.id}">
            <td class="fw-bold">${producto.nombre}</td>
            <td><span class="badge bg-secondary">${producto.stock}</span></td>
            <td>$${parseFloat(producto.precio || 0).toFixed(2)}</td>
            <td>${producto.concentracionCompleta || 'N/A'}</td>
            <td style="width: 120px;">
              <input type="number" min="1" max="${producto.stock}" class="form-control form-control-sm cantidad_producto" value="${producto.cantidad}">
            </td>
            <td class="subtotales fw-bold text-primary">$${subtotal}</td>
            <td class="text-center">
              <button class="borrar_de_carrito btn btn-sm btn-danger" title="Eliminar ítem">
                <i class="bi bi-trash"></i>
              </button>
            </td>
        </tr>
      `;
      $("#lista-compra").append(template);
    });
  }

  $("#cp").on("keyup change", ".cantidad_producto", function (e) {
    const fila = $(this).closest("tr");
    const id = fila.attr("data_id");
    const cantidad = parseInt($(this).val()) || 1;
    let productos = RecuperarLS();

    productos.forEach(function (prod) {
      if (prod.id === id) {
        prod.cantidad = cantidad;
        fila.find(".subtotales").text(`$${(cantidad * parseFloat(prod.precio || 0)).toFixed(2)}`);
      }
    });

    localStorage.setItem("productos", JSON.stringify(productos));
    if (typeof calcularTotal === "function") {
      calcularTotal();
    }
  });

  if (window.location.pathname.includes("adm_retiro.php")) {
    calcularTotal();

    function calcularTotal() {
      let total = 0;
      let productos = RecuperarLS();
      productos.forEach((producto) => {
        let subtotalProducto = Number(producto.precio * producto.cantidad) || 0;
        total += subtotalProducto;
      });

      let descuentoInput = parseFloat($("#descuento").val()) || 0;
      let totalConDescuento = Math.max(0, total - descuentoInput);
      let iva = totalConDescuento * 0.08;
      let subtotalBase = totalConDescuento - iva;

      $("#subtotal").text(`$${subtotalBase.toFixed(2)}`);
      $("#total_sin_descuento").text(`$${total.toFixed(2)}`);
      $("#conIva").text(`$${iva.toFixed(2)}`);
      $("#total").text(`$${totalConDescuento.toFixed(2)}`);

      calcularVuelto();
    }

    $("#descuento").on("keyup change", function () {
      calcularTotal();
    });

    $("#pago").on("keyup change", function () {
      calcularVuelto();
    });

    function calcularVuelto() {
      let totalTexto = $("#total").text().replace("$", "");
      let total = parseFloat(totalTexto) || 0;
      let ingreso = parseFloat($("#pago").val()) || 0;
      let vuelto = Math.max(0, ingreso - total);
      $("#vuelto").text(`$${vuelto.toFixed(2)}`);
    }

    window.calcularTotal = calcularTotal;
  }

  $(document).on("click", "#procesar_compra", function (e) {
    e.preventDefault();
    procesar_compra();
  });

  function procesar_compra() {
    let nombre = $("#cliente").val();
    let ci = $("#ci").val();
    let total = $("#total").text().replace("$", "");

    if (RecuperarLS().length === 0) {
      Swal.fire({
        icon: "warning",
        title: "Solicitud Vacía",
        text: "Debe agregar al menos un insumo al pedido.",
        confirmButtonColor: "#1a3a5c"
      });
      return;
    }

    if (!nombre || nombre.trim() === "" || !ci || ci.trim() === "") {
      Swal.fire({
        icon: "warning",
        title: "Datos Incompletos",
        text: "Por favor complete el nombre del solicitante/área y su cédula.",
        confirmButtonColor: "#1a3a5c"
      });
      return;
    }

    let productos = JSON.stringify(RecuperarLS());
    $.post(
      "../controller/CompraController.php",
      { total, nombre, ci, productos },
      (response) => {
        if (response.trim() === "add") {
          Swal.fire({
            icon: "success",
            title: "¡Entrega Registrada!",
            text: "La solicitud de insumos ha sido procesada exitosamente.",
            confirmButtonColor: "#1a3a5c"
          }).then(() => {
            EliminarLS();
            window.location.href = "adm_catalogo.php";
          });
        } else {
          Swal.fire({
            icon: "error",
            title: "Error al procesar",
            text: "No se pudo registrar la entrega de insumos.",
            confirmButtonColor: "#1a3a5c"
          });
        }
      }
    );
  }
});
