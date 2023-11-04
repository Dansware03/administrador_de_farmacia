var funcion; // Declarar la variable funcion antes de su uso
$(document).ready(function() {
    buscar_presentacion();
    $('#form-crear-presentacion').submit(e=>{
        let nombre_presentacion = $('#nombre-presentacion').val();
        funcion='crear';
        $.post('../controller/PresentacionesController.php', { nombre_presentacion, funcion }, (Response) => {
            if (Response=='add') {
                $('#add-presentacion').hide('slow', function () {
                    $(this).show(1000, function () {
                    $(this).hide(2000, function () {
                        $('#form-crear-presentacion').trigger('reset');
                        });
                    });
                    buscar_presentacion();
                });
                } else {
                $('#noadd-presentacion').hide('slow', function () {
                    $(this).show(1000, function () {
                    $(this).hide(2000, function () {
                        $('#form-crear-presentacion').trigger('reset');
                        });
                    });
                });
                }
        });
        e.preventDefault();
    });
        function buscar_presentacion(consulta) {
        funcion='buscar';
        $.post('../controller/PresentacionesController.php',{consulta,funcion},(Response)=>{
            const tipos = JSON.parse(Response);
            let templete='';
            tipos.forEach(tipo=> {
                templete+=`
                <tr presentacionId="${tipo.id}" presentacionNombre="${tipo.nombre}">
                <td>${tipo.nombre}</td>
                <td>
                <button class="editar btn btn-success" title="Editar tipo" type="button" data-toggle="modal" data-target="#cambiar-nombre-presentacion"><i class="fas fa-pencil-alt"></i></button>
                <button class="borrar btn btn-danger" title="Eliminar tipo" type="button" data-toggle="modal" data-target="#remove-presentacion"><i class="fas fa-trash"></i></button>
                </td>
                </tr>
                `;
            })
            $('#presentaciones').html(templete);
        });
    }
    $(document).on('keyup','#buscar_presentacion',function(){
        let valor = $(this).val();
        if (valor!==""){
            buscar_presentacion(valor);
        } else {
            buscar_presentacion();
        }
    });
});