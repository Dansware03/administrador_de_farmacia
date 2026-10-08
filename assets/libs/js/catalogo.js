$(document).ready(function () {
  $("#cat-carrito").show();
  var funcion;
  let productosCache = [];
  let filtroActual = "todos";
  let paginaActual = 1;
  let itemsPorPagina = 12;
  let listaFiltradaActual = [];
  let debounceTimer = null;

  buscar_product();
  mostrar_lotes_riesgo();

  function buscar_product(consulta = "") {
    $.post(
      "../controller/ProductoController.php",
      { consulta, funcion: "buscar_product" },
      (Response) => {
        try {
          productosCache = typeof Response === "object" ? Response : JSON.parse(Response || "[]");
          actualizarKPIsProductos(productosCache);
          paginaActual = 1;
          aplicarFiltroYRenderizar();
        } catch (error) {
          console.error("Error al analizar la respuesta JSON de productos:", error);
        }
      }
    );
  }

  function actualizarKPIsProductos(products) {
    if (!products) return;
    const total = products.length;
    const bajoStock = products.filter((p) => {
      const stock = parseInt(p.stock) || 0;
      return stock > 0 && stock <= 10;
    }).length;

    $("#kpi-total-insumos").text(total);
    $("#kpi-stock-bajo").text(bajoStock);
  }

  function aplicarFiltroYRenderizar() {
    let filtrados = productosCache;
    if (filtroActual === "stock") {
      filtrados = productosCache.filter((p) => (parseInt(p.stock) || 0) > 0);
    } else if (filtroActual === "bajo") {
      filtrados = productosCache.filter((p) => {
        const s = parseInt(p.stock) || 0;
        return s > 0 && s <= 10;
      });
    } else if (filtroActual === "agotado") {
      filtrados = productosCache.filter((p) => (parseInt(p.stock) || 0) <= 0);
    }
    listaFiltradaActual = filtrados;
    renderizarConPaginacion();
  }

  // Manejo de chips de filtro
  $(document).on("click", ".simap-filter-chip", function () {
    $(".simap-filter-chip").removeClass("active");
    $(this).addClass("active");
    filtroActual = $(this).data("filter") || "todos";
    paginaActual = 1;
    aplicarFiltroYRenderizar();
  });

  // Selector de cantidad por página
  $(document).on("change", "#items-por-pagina", function () {
    itemsPorPagina = parseInt($(this).val()) || 12;
    paginaActual = 1;
    renderizarConPaginacion();
  });

  // Clic en botones de paginación
  $(document).on("click", ".pagina-btn", function (e) {
    e.preventDefault();
    const targetPage = parseInt($(this).data("page"));
    if (!isNaN(targetPage) && targetPage !== paginaActual) {
      paginaActual = targetPage;
      renderizarConPaginacion();
      // Scroll suave hacia arriba de la sección de productos
      const grid = document.getElementById("productos");
      if (grid) {
        grid.scrollIntoView({ behavior: "smooth", block: "start" });
      }
    }
  });

  function renderizarConPaginacion() {
    const totalItems = listaFiltradaActual.length;
    const totalPaginas = Math.ceil(totalItems / itemsPorPagina) || 1;

    if (paginaActual > totalPaginas) paginaActual = totalPaginas;
    if (paginaActual < 1) paginaActual = 1;

    const inicio = (paginaActual - 1) * itemsPorPagina;
    const fin = inicio + itemsPorPagina;
    const itemsPagina = listaFiltradaActual.slice(inicio, fin);

    mostrarProductos(itemsPagina);
    actualizarControlesPaginacion(totalItems, totalPaginas, inicio, fin);
  }

  function actualizarControlesPaginacion(totalItems, totalPaginas, inicio, fin) {
    const contenedor = $("#contenedor-paginacion");
    const info = $("#info-paginacion");
    const lista = $("#paginacion-lista");

    if (totalItems === 0) {
      info.text("No hay insumos para mostrar");
      lista.empty();
      return;
    }

    const finReal = Math.min(fin, totalItems);
    info.html(`Mostrando <strong>${inicio + 1}</strong> a <strong>${finReal}</strong> de <strong>${totalItems}</strong> insumos`);

    if (totalPaginas <= 1) {
      lista.empty();
      return;
    }

    let html = "";
    // Botón Anterior
    const prevDisabled = paginaActual === 1 ? "disabled" : "";
    html += `
      <li class="page-item ${prevDisabled}">
        <a class="page-link pagina-btn" href="#" data-page="${paginaActual - 1}" aria-label="Anterior">
          <i class="bi bi-chevron-left"></i>
        </a>
      </li>
    `;

    // Botones de Páginas (rango inteligente de hasta 5 páginas visibles)
    let startPage = Math.max(1, paginaActual - 2);
    let endPage = Math.min(totalPaginas, startPage + 4);
    if (endPage - startPage < 4) {
      startPage = Math.max(1, endPage - 4);
    }

    if (startPage > 1) {
      html += `<li class="page-item"><a class="page-link pagina-btn" href="#" data-page="1">1</a></li>`;
      if (startPage > 2) {
        html += `<li class="page-item disabled"><span class="page-link">&hellip;</span></li>`;
      }
    }

    for (let p = startPage; p <= endPage; p++) {
      const active = p === paginaActual ? "active" : "";
      html += `<li class="page-item ${active}"><a class="page-link pagina-btn" href="#" data-page="${p}">${p}</a></li>`;
    }

    if (endPage < totalPaginas) {
      if (endPage < totalPaginas - 1) {
        html += `<li class="page-item disabled"><span class="page-link">&hellip;</span></li>`;
      }
      html += `<li class="page-item"><a class="page-link pagina-btn" href="#" data-page="${totalPaginas}">${totalPaginas}</a></li>`;
    }

    // Botón Siguiente
    const nextDisabled = paginaActual === totalPaginas ? "disabled" : "";
    html += `
      <li class="page-item ${nextDisabled}">
        <a class="page-link pagina-btn" href="#" data-page="${paginaActual + 1}" aria-label="Siguiente">
          <i class="bi bi-chevron-right"></i>
        </a>
      </li>
    `;

    lista.html(html);
  }

  function mostrarProductos(products) {
    const productContainer = $("#productos");
    if (!products || products.length === 0) {
      productContainer.html(`
        <div class="col-12 text-center py-5">
          <i class="bi bi-inbox text-muted display-4"></i>
          <p class="text-muted mt-2 fw-semibold">No se encontraron insumos disponibles con ese criterio.</p>
        </div>
      `);
      return;
    }

    const template = products
      .map((product) => {
        const stockNum = parseInt(product.stock) || 0;
        let stockBadgeHtml = '';
        let botonDeshabilitado = '';

        if (stockNum <= 0) {
          stockBadgeHtml = '<span class="badge bg-secondary bg-opacity-75 text-white px-2 py-1"><i class="bi bi-x-circle me-1"></i>Agotado</span>';
          botonDeshabilitado = 'disabled';
        } else if (stockNum <= 10) {
          stockBadgeHtml = `<span class="badge bg-danger text-white px-2 py-1"><i class="bi bi-exclamation-circle me-1"></i>Bajo: ${stockNum}</span>`;
        } else if (stockNum <= 25) {
          stockBadgeHtml = `<span class="badge bg-warning text-dark px-2 py-1"><i class="bi bi-boxes me-1"></i>Stock: ${stockNum}</span>`;
        } else {
          stockBadgeHtml = `<span class="badge bg-success text-white px-2 py-1"><i class="bi bi-check-circle me-1"></i>Stock: ${stockNum}</span>`;
        }

        const avatarSrc = product.avatar && product.avatar.trim() !== '' ? product.avatar : '../libs/img/product/prod_default.png';
        const unidadCodigo = product.unidad_codigo || 'und';
        const unidadNombre = product.unidad_medida || 'Unidad';
        const categoriaNombre = product.tipo || 'General';
        const especTexto = product.especificacion_talla || product.concentracion || '';
        const empaqueTexto = product.nombre_presentacion || 'Unidad individual';
        const fabricanteTexto = product.nombre_laboratorio || 'N/A';

        return `
          <div proId="${product.id}" proNombre="${product.nombre}" productStock="${product.stock}" conNombre="${product.concentracion}" addNombre="${product.adicional}" nLabNombre="${product.laboratorio_id}" nTypeNombre="${product.tipo_id}" nPreNombre="${product.presentacion_id}" idUnidad="${product.id_unidad || 1}" uniMedida="${unidadNombre}" uniCodigo="${unidadCodigo}" espTalla="${product.especificacion_talla || ''}" avaNombre="${avatarSrc}" class="col-12 col-sm-6 col-lg-4 col-xl-3 d-flex align-items-stretch">
            <div class="product-card-modern w-100 d-flex flex-column justify-content-between">
              <div>
                <!-- Encabezado de la tarjeta -->
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <span class="product-category-chip text-truncate" title="${categoriaNombre}">
                    ${categoriaNombre}
                  </span>
                  <div>${stockBadgeHtml}</div>
                </div>

                <!-- Imagen e Identificación del Insumo -->
                <div class="text-center my-2">
                  <div class="product-thumb-wrap mx-auto mb-2">
                    <img src="${avatarSrc}" alt="${product.nombre}" class="product-thumb-img" onerror="this.src='../libs/img/product/prod_default.png'">
                  </div>
                  <h6 class="fw-bold text-dark mb-1 text-truncate" title="${product.nombre}">${product.nombre}</h6>
                  <span class="text-muted small d-block mb-2">Cód: #${product.id}</span>
                </div>

                <!-- Metadatos de Dotación / Especificación -->
                <div class="d-flex flex-wrap gap-1 justify-content-center mb-3">
                  <span class="spec-badge" title="Unidad de medida">
                    <i class="bi bi-rulers text-secondary me-1"></i>${unidadCodigo}
                  </span>
                  ${especTexto ? `<span class="spec-badge" title="Talla o especificación técnica"><i class="bi bi-tag text-secondary me-1"></i>${especTexto}</span>` : ''}
                </div>

                <div class="border-top pt-2 text-muted small">
                  <div class="d-flex justify-content-between mb-1">
                    <span class="text-secondary">Empaque:</span>
                    <span class="fw-semibold text-truncate ms-2 text-end text-dark" style="max-width: 140px;" title="${empaqueTexto}">${empaqueTexto}</span>
                  </div>
                  <div class="d-flex justify-content-between">
                    <span class="text-secondary">Fabricante:</span>
                    <span class="fw-semibold text-truncate ms-2 text-end text-dark" style="max-width: 140px;" title="${fabricanteTexto}">${fabricanteTexto}</span>
                  </div>
                </div>
              </div>

              <!-- Acción de Solicitud -->
              <div class="mt-3 pt-2">
                <button class="agg_compra btn btn-primary w-100 shadow-sm d-flex align-items-center justify-content-center gap-2 py-2 fw-semibold" ${botonDeshabilitado} title="Agregar a Solicitud de Insumos" aria-label="Agregar ${product.nombre} a la solicitud de insumos" type="button">
                  <i class="bi bi-cart-plus"></i>
                  <span>${stockNum <= 0 ? 'Sin Existencias' : 'Añadir a Solicitud'}</span>
                </button>
              </div>
            </div>
          </div>
        `;
      })
      .join("");

    productContainer.empty().append(template);
  }

  // Buscador con debounce nativo para máxima velocidad y evitar peticiones repetitivas
  $(document).on("keyup", "#buscar_producto", function () {
    const valor = $(this).val();
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
      buscar_product(valor);
    }, 280);
  });

  function mostrar_lotes_riesgo() {
    funcion = "buscar_lote";
    $.post("../controller/LoteController.php", { funcion }, (response) => {
      try {
        const lotes = typeof response === "object" ? response : JSON.parse(response || "[]");
        mostrarlotes(lotes);
      } catch (error) {
        console.error("Error al analizar la respuesta JSON de lotes:", error);
      }
    });
  }

  function mostrarlotes(lotes) {
    const navbarList = $("#lista_lotes_navbar");
    const badgeAlerta = $("#badge-lotes-alerta");
    const badgeTotalAlerta = $("#total-lotes-alerta-badge");
    const kpiLotesRiesgo = $("#kpi-lotes-riesgo");

    if (!lotes || lotes.length === 0) {
      navbarList.html(`<tr><td class="text-center py-3 text-muted small"><i class="bi bi-shield-check text-success me-1"></i>Sin lotes en riesgo actualmente.</td></tr>`);
      badgeAlerta.addClass("d-none").text(0);
      badgeTotalAlerta.text("0 alertas");
      kpiLotesRiesgo.text(0);
      return;
    }

    let lotesCriticosContador = 0;
    const itemsNavbar = [];

    lotes.forEach((lote) => {
      if (lote.estado === "warning" || lote.estado === "danger") {
        lotesCriticosContador++;
        const esVencido = lote.estado === "danger";
        const badgeClass = esVencido ? "bg-danger text-white" : "bg-warning text-dark";
        const iconClass = esVencido ? "bi-x-circle-fill" : "bi-clock-fill";

        const mesesAbs = Math.abs(parseInt(lote.mes) || 0);
        const diasAbs = Math.abs(parseInt(lote.dia) || 0);
        const diagnostico = esVencido
          ? `Vencido hace ${mesesAbs > 0 ? mesesAbs + 'm ' : ''}${diasAbs}d`
          : `Vence en ${mesesAbs > 0 ? mesesAbs + 'm ' : ''}${diasAbs}d`;

        const codLote = lote.cod_lote && lote.cod_lote.trim() !== '' ? lote.cod_lote : `#${lote.id}`;
        const fechaVenc = lote.vencimiento ? lote.vencimiento.substring(0, 10) : 'N/A';

        itemsNavbar.push(`
          <tr>
            <td class="py-2">
              <div class="d-flex justify-content-between align-items-center mb-1">
                <span class="fw-bold small text-dark">${lote.nombre}</span>
                <span class="badge ${badgeClass} small" style="font-size: 0.7rem;">
                  <i class="bi ${iconClass} me-1"></i>${diagnostico}
                </span>
              </div>
              <div class="d-flex justify-content-between align-items-center text-muted" style="font-size: 0.75rem;">
                <span>Lote: <strong class="text-dark">${codLote}</strong> &bull; Disp: <strong class="text-dark">${lote.stock}</strong></span>
                <span>${fechaVenc}</span>
              </div>
            </td>
          </tr>
        `);
      }
    });

    kpiLotesRiesgo.text(lotesCriticosContador);

    if (lotesCriticosContador > 0) {
      badgeAlerta.removeClass("d-none").text(lotesCriticosContador);
      badgeTotalAlerta.text(`${lotesCriticosContador} en riesgo`).removeClass("bg-light text-dark").addClass("bg-danger text-white");
      navbarList.html(itemsNavbar.join(""));
    } else {
      badgeAlerta.addClass("d-none").text(0);
      badgeTotalAlerta.text("0 alertas").removeClass("bg-danger text-white").addClass("bg-light text-dark");
      navbarList.html(`<tr><td class="text-center py-3 text-muted small"><i class="bi bi-shield-check text-success me-1"></i>Sin lotes en riesgo actualmente.</td></tr>`);
    }
  }
});
