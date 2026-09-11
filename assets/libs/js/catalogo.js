$(document).ready(function () {
  $("#cat-carrito").show();
  var funcion;
  buscar_product();
  mostrar_lotes_riesgo();

  function buscar_product(consulta) {
    $.post(
      "../controller/ProductoController.php",
      { consulta, funcion: "buscar_product" },
      (Response) => {
        try {
          const products = JSON.parse(Response);
          mostrarProductos(products);
        } catch (error) {
          console.error("Error al analizar la respuesta JSON:", error);
        }
      }
    );
  }

  function mostrarProductos(products) {
    const productContainer = $("#productos");
    if (!products || products.length === 0) {
      productContainer.html(`
        <div class="col-12 text-center py-5">
          <i class="bi bi-inbox text-muted display-4"></i>
          <p class="text-muted mt-2">No se encontraron insumos disponibles con ese criterio.</p>
        </div>
      `);
      return;
    }

    const template = products
      .map(
        (product) => {
          const stockNum = parseInt(product.stock) || 0;
          let stockBadge = 'bg-success';
          if (stockNum <= 5) {
            stockBadge = 'bg-danger';
          } else if (stockNum <= 15) {
            stockBadge = 'bg-warning text-dark';
          }

          const avatarSrc = product.avatar && product.avatar.trim() !== '' ? product.avatar : '../libs/img/product/prod_default.png';

          return `
            <div proId="${product.id}" proNombre="${product.nombre}" productStock="${product.stock}" conNombre="${product.concentracion}" addNombre="${product.adicional}" preNombre="${product.precio}" nLabNombre="${product.laboratorio_id}" nTypeNombre="${product.tipo_id}" nPreNombre="${product.presentacion_id}" avaNombre="${avatarSrc}" class="col-12 col-sm-6 col-md-4 d-flex align-items-stretch">
                <div class="card product-card w-100 shadow-sm border-0 d-flex flex-column justify-content-between">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="badge ${stockBadge} rounded-pill px-3 py-2 small">
                                <i class="bi bi-boxes me-1"></i>Stock: ${product.stock}
                            </span>
                            <span class="text-muted small fw-semibold">#${product.id}</span>
                        </div>

                        <div class="text-center mb-3">
                            <img src="${avatarSrc}" alt="${product.nombre}" class="product-avatar mb-2 shadow-sm" onerror="this.src='../libs/img/product/prod_default.png'">
                            <h5 class="fw-bold text-primary mb-1 text-truncate" title="${product.nombre}">${product.nombre}</h5>
                            <div class="fw-bold fs-5 text-dark">
                                $${parseFloat(product.precio || 0).toFixed(2)}
                            </div>
                        </div>

                        <ul class="list-unstyled small text-muted border-top pt-3 mb-0">
                            <li class="mb-1 text-truncate"><i class="bi bi-tag text-secondary me-2"></i><b>Concentración:</b> ${product.concentracion || 'N/A'}</li>
                            <li class="mb-1 text-truncate"><i class="bi bi-info-circle text-secondary me-2"></i><b>Adicional:</b> ${product.adicional || 'N/A'}</li>
                            <li class="mb-1 text-truncate"><i class="bi bi-building text-secondary me-2"></i><b>Laboratorio:</b> ${product.nombre_laboratorio || 'N/A'}</li>
                            <li class="mb-1 text-truncate"><i class="bi bi-collection text-secondary me-2"></i><b>Tipo:</b> ${product.tipo || 'N/A'}</li>
                            <li class="text-truncate"><i class="bi bi-box-seam text-secondary me-2"></i><b>Presentación:</b> ${product.nombre_presentacion || 'N/A'}</li>
                        </ul>
                    </div>

                    <div class="card-footer bg-light border-0 p-3">
                        <button class="agg_compra btn btn-primary w-100 shadow-sm d-flex align-items-center justify-content-center gap-2 fw-semibold" title="Agregar al Carrito" type="button">
                            <i class="bi bi-cart-plus"></i>
                            <span>Agregar a Solicitud</span>
                        </button>
                    </div>
                </div>
            </div>
          `;
        }
      )
      .join("");
    productContainer.empty().append(template);
  }

  $(document).on("keyup", "#buscar_producto", function () {
    let valor = $(this).val();
    if (valor !== "") {
      buscar_product(valor);
    } else {
      buscar_product();
    }
  });

  function mostrar_lotes_riesgo() {
    funcion = "buscar_lote";
    $.post("../controller/LoteController.php", { funcion }, (response) => {
      try {
        const lotes = JSON.parse(response);
        mostrarlotes(lotes);
      } catch (error) {
        console.error("Error al analizar la respuesta JSON:", error);
      }
    });
  }

  function mostrarlotes(lotes) {
    const loteContainer = $("#lotes");
    if (!lotes || lotes.length === 0) {
      loteContainer.html(`<tr><td colspan="8" class="text-center py-3 text-muted">No hay lotes en riesgo actualmente.</td></tr>`);
      return;
    }

    const template = lotes
      .map((lote) => {
        if (lote.estado === "warning" || lote.estado === "danger") {
          const rowClass = lote.estado === "danger" ? "lote-danger" : "lote-warning";
          const badgeClass = lote.estado === "danger" ? "bg-danger" : "bg-warning text-dark";
          const estadoText = lote.estado === "danger" ? "Vencido" : "Por Vencer";

          return `
            <tr class="${rowClass}">
                <td class="ps-3 fw-bold">${lote.id}</td>
                <td>
                  <span class="fw-semibold text-dark">${lote.nombre}</span>
                  <span class="badge ${badgeClass} ms-1 small">${estadoText}</span>
                </td>
                <td class="fw-bold">${lote.stock}</td>
                <td>${lote.nombre_laboratorio}</td>
                <td>${lote.nombre_presentacion}</td>
                <td>${lote.proveedor}</td>
                <td>${lote.mes}</td>
                <td>${lote.dia}</td>
            </tr>
          `;
        } else {
          return "";
        }
      })
      .join("");

    loteContainer.empty().append(template || `<tr><td colspan="8" class="text-center py-3 text-muted">No hay lotes en riesgo.</td></tr>`);
  }
});
