$(document).ready(function () {
  RecuperarLS_carrito();
  Contar_productos();
  RecuperarLS_carrito_Pedido();

  function mostrarNotificacion(mensaje, tipo) {
    if (typeof toastr !== "undefined") {
      toastr[tipo](mensaje);
    }
  }

  $(document).on("click", ".agg_compra", function () {
    const elemento = $(this).closest("[proId]");
    const id = elemento.attr("proId");
    const nombre = elemento.attr("proNombre");
    const adicional = elemento.attr("addNombre");
    const prod_lab = elemento.attr("nLabNombre");
    const prod_tip_prod = elemento.attr("nTypeNombre");
    const prod_present = elemento.attr("nPreNombre");
    const concentracionCompleta = elemento.attr("conNombre");
    const avatar = elemento.attr("avaNombre");
    const stock = elemento.attr("productStock");
    const uniMedida = elemento.attr("uniMedida") || "Unidad";
    const uniCodigo = elemento.attr("uniCodigo") || "und";
    const espTalla = elemento.attr("espTalla") || concentracionCompleta || "";

    const producto = {
      id: id,
      nombre: nombre,
      adicional: adicional,
      prod_lab: prod_lab,
      prod_tip_prod: prod_tip_prod,
      prod_present: prod_present,
      concentracionCompleta: concentracionCompleta,
      uniMedida: uniMedida,
      uniCodigo: uniCodigo,
      espTalla: espTalla,
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
    $("#contador, #fab-contador, .contador").text(contador);
  }

  function RecuperarLS_carrito_Pedido() {
    let productos = RecuperarLS();
    $("#lista-compra").empty();

    if (productos.length === 0) {
      const emptyState = `
        <tr>
          <td colspan="5" class="text-center py-5">
            <div class="text-muted">
              <i class="bi bi-cart-x display-4 d-block mb-3 text-secondary opacity-50"></i>
              <h6 class="fw-semibold text-dark">No hay insumos seleccionados en la solicitud</h6>
              <p class="small text-muted mb-3">Diríjase al catálogo de insumos para seleccionar los artículos a despachar.</p>
              <a href="adm_catalogo.php" class="btn btn-primary btn-sm fw-semibold">
                <i class="bi bi-grid me-1"></i>Ir al Catálogo de Insumos
              </a>
            </div>
          </td>
        </tr>
      `;
      $("#lista-compra").append(emptyState);
      return;
    }

    productos.forEach((producto) => {
      const unidadTexto = producto.uniMedida ? `${producto.uniMedida} (${producto.uniCodigo || 'und'})` : 'Unidad (und)';
      const especTexto = producto.espTalla || producto.concentracionCompleta || '-';
      const template = `
        <tr data_id="${producto.id}">
            <td class="fw-bold">${producto.nombre}</td>
            <td><span class="badge bg-secondary">${producto.stock}</span></td>
            <td>
              <div class="small fw-semibold text-dark">${unidadTexto}</div>
              <small class="text-muted">${especTexto}</small>
            </td>
            <td style="width: 140px;">
              <input type="number" min="1" max="${producto.stock}" class="form-control form-control-sm cantidad_producto" value="${producto.cantidad}">
            </td>
            <td class="text-center">
              <button class="borrar_de_carrito btn btn-sm btn-outline-danger" title="Eliminar ítem">
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
      }
    });

    localStorage.setItem("productos", JSON.stringify(productos));
    if (typeof calcularTotal === "function") {
      calcularTotal();
    }
  });

  if (window.location.pathname.includes("adm_retiro.php")) {
    cargar_areas_servicio();
    calcularTotal();

    function cargar_areas_servicio(id_seleccionar = null) {
      $.post('../controller/AreaController.php', { funcion: 'cargar_areas' })
        .done(function(response) {
          const areas = JSON.parse(response);
          const opciones = areas.map(a => `<option value="${a.id_area}">${a.nombre_area}</option>`);
          $('#area_destino').html(opciones.join(''));
          if (id_seleccionar) {
            $('#area_destino').val(id_seleccionar).trigger('change');
          }
        })
        .fail(function(error) {
          console.error("Error al cargar áreas de servicio:", error);
        });
    }

    // Registro rápido de área hospitalaria desde adm_retiro
    $('#form_nueva_area_rapida').on('submit', function(e) {
      e.preventDefault();
      const nombre_area = $('#modal_nombre_area').val().trim();

      if (!nombre_area) return;

      $.post('../controller/AreaController.php', {
        funcion: 'crear_area',
        nombre_area: nombre_area
      }, function(response) {
        let res = {};
        try {
          res = JSON.parse(response);
        } catch (err) {
          res = { status: 'error', message: 'Respuesta inválida del servidor' };
        }

        if (res.status === 'success') {
          $('#form_nueva_area_rapida').trigger('reset');
          const modalEl = document.getElementById('modal_nueva_area');
          const modalObj = bootstrap.Modal.getInstance(modalEl);
          if (modalObj) modalObj.hide();

          cargar_areas_servicio(res.id_area);

          Swal.fire({
            icon: 'success',
            title: 'Área Registrada',
            text: `Se ha añadido "${res.nombre_area}" y seleccionado automáticamente.`,
            showConfirmButton: false,
            timer: 1600
          });
        } else {
          Swal.fire({
            icon: 'error',
            title: 'No se pudo registrar',
            text: res.message || 'Verifique si el área ya existe.'
          });
        }
      });
    });

    function calcularTotal() {
      let totalCantidad = 0;
      let productos = RecuperarLS();
      productos.forEach((producto) => {
        let cant = parseInt(producto.cantidad) || 1;
        totalCantidad += cant;
      });

      $("#total_items_tipos").text(productos.length);
      $("#total_items").text(totalCantidad);
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
    let id_area = $("#area_destino").val();
    let cargo_receptor = $("#cargo_receptor").val() || "";
    let observacion = $("#observacion_entrega").val() || "";

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
        text: "Por favor complete el nombre del solicitante/funcionario y su cédula.",
        confirmButtonColor: "#1a3a5c"
      });
      return;
    }

    if (!id_area) {
      Swal.fire({
        icon: "warning",
        title: "Área Requerida",
        text: "Por favor seleccione el área hospitalaria de destino.",
        confirmButtonColor: "#1a3a5c"
      });
      return;
    }

    let productos = JSON.stringify(RecuperarLS());
    $.post(
      "../controller/DespachoController.php",
      { funcion: "registrar_despacho", nombre, ci, id_area, cargo_receptor, observacion, productos },
      (response) => {
        let isSuccess = false;
        let errorMsg = "";

        let idDespachoCreado = null;
        try {
          let res = JSON.parse(response);
          if (res.status === "success") {
            isSuccess = true;
            idDespachoCreado = res.id_despacho;
          } else {
            errorMsg = res.message || "Error al procesar el despacho.";
          }
        } catch (e) {
          if (response.trim() === "add") {
            isSuccess = true;
          } else {
            errorMsg = response;
          }
        }

        if (isSuccess) {
          // Guardar receptor en historial local para autocompletar futuros despachos
          guardarReceptorHabitual({ nombre, ci, cargo: cargo_receptor });

          Swal.fire({
            icon: "success",
            title: "¡Despacho Procesado con Éxito!",
            text: idDespachoCreado ? `Acta de Entrega #${idDespachoCreado} generada.` : "La salida de insumos del depósito ha sido registrada.",
            showCancelButton: true,
            confirmButtonText: '<i class="bi bi-printer me-1"></i> Imprimir Acta Oficial',
            cancelButtonText: '<i class="bi bi-arrow-left me-1"></i> Volver al Catálogo',
            confirmButtonColor: "#0284c7",
            cancelButtonColor: "#64748b"
          }).then((result) => {
            EliminarLS();
            if (idDespachoCreado && result.isConfirmed) {
              window.open(`acta_despacho.php?id=${idDespachoCreado}`, '_blank');
            }
            if (document.referrer && document.referrer.includes("tec_catalogo.php")) {
              window.location.href = "tec_catalogo.php";
            } else {
              window.location.href = "adm_catalogo.php";
            }
          });
        } else {
          Swal.fire({
            icon: "error",
            title: "Error al procesar",
            text: "No se pudo registrar la entrega de insumos: " + errorMsg,
            confirmButtonColor: "#1a3a5c"
          });
        }
      }
    );
  }

  // Manejo de receptores habituales (autocompletado rápido sin fricción)
  function cargarReceptoresHabituales() {
    try {
      const historial = JSON.parse(localStorage.getItem("simap_receptores_habituales") || "[]");
      const datalist = $("#lista_receptores_habituales");
      if (datalist.length && historial.length) {
        datalist.empty();
        historial.forEach(r => {
          datalist.append(`<option value="${r.nombre}">${r.ci} - ${r.cargo || 'Funcionario'}</option>`);
        });
      }
    } catch (e) {}
  }

  function guardarReceptorHabitual(rec) {
    try {
      let historial = JSON.parse(localStorage.getItem("simap_receptores_habituales") || "[]");
      historial = historial.filter(r => r.ci !== rec.ci);
      historial.unshift(rec);
      if (historial.length > 8) historial.pop();
      localStorage.setItem("simap_receptores_habituales", JSON.stringify(historial));
    } catch (e) {}
  }

  if (window.location.pathname.includes("adm_retiro.php")) {
    cargarReceptoresHabituales();

    $("#cliente").on("input change", function() {
      const val = $(this).val().trim();
      const historial = JSON.parse(localStorage.getItem("simap_receptores_habituales") || "[]");
      const encontrado = historial.find(r => r.nombre.toLowerCase() === val.toLowerCase());
      if (encontrado) {
        if (!$("#ci").val()) $("#ci").val(encontrado.ci);
        if (!$("#cargo_receptor").val() && encontrado.cargo) $("#cargo_receptor").val(encontrado.cargo);
      }
    });
  }
});
