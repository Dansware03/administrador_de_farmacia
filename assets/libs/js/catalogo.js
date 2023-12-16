$(document).ready(function() {
    $('#cat-carrito').show();
    var funcion
    buscar_product();
    mostrar_lotes_riesgo();
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
                        <button class="agg_compra btn btn-sm btn-primary" title="Agregar al Carrito" type="button">
                        <i class="fa-solid fa-cart-shopping mr-2"></i>Agregar al Carrito
                    </button>                    
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
    function mostrar_lotes_riesgo() {
        funcion="buscar_lote"
        $.post('../controller/LoteController.php', {funcion}, (response) => {
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
        const template = lotes.map(lote => {
            if (lote.estado === 'warning' || lote.estado === 'danger') {
                return `
                <tr class="bg-${lote.estado} ${lote.estado === 'warning' ? 'warning' : ''}">
                    <td class="col-md-1">${lote.id}</td>
                    <td class="col-md-3">${lote.nombre}</td>
                    <td class="col-md-1">${lote.stock}</td>
                    <td class="col-md-2">${lote.nombre_laboratorio}</td>
                    <td class="col-md-2">${lote.nombre_presentacion}</td>
                    <td class="col-md-2">${lote.proveedor}</td>
                    <td class="col-md-1">${lote.mes}</td>
                    <td class="col-md-1">${lote.dia}</td>
                </tr>
                `;
            } else {
                // Retorna una cadena vacía para los lotes en estado normal
                return '';
            }
        }).join('');
        loteContainer.empty().append(template);
        function parpadearLotesPorVencer() {
            $('#lotes tr.warning').fadeOut(1000).fadeIn(1000, function () {
                parpadearLotesPorVencer();
            });
        }
        if ($('#lotes tr.warning').length > 0) {
            parpadearLotesPorVencer();
        }
    }
});