$(document).ready(function () {
    var funcion;

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
    });
});

