var funcion; // Declarar la variable funcion antes de su uso
$(document).ready(function() {
    buscar_type();
    $('#form-crear-tipo').submit(e=>{
        let nombre_type = $('#nombre-tipo').val();
        funcion='crear';
        $.post('../controller/TypeController.php', { nombre_type, funcion }, (Response) => {
            if (Response=='add') {
                $('#add-type').hide('slow', function () {
                    $(this).show(1000, function () {
                    $(this).hide(2000, function () {
                        $('#form-crear-tipo').trigger('reset');
                        });
                    });
                    buscar_type();
                });
                } else {
                $('#noadd-type').hide('slow', function () {
                    $(this).show(1000, function () {
                    $(this).hide(2000, function () {
                        $('#form-crear-tipo').trigger('reset');
                        });
                    });
                });
                }
        });
        e.preventDefault();
    });
        function buscar_type(consulta) {
        funcion='buscar';
        $.post('../controller/TypeController.php',{consulta,funcion},(Response)=>{
            const tipos = JSON.parse(Response);
            let templete='';
            tipos.forEach(tipo=> {
                templete+=`
                <tr typeId="${tipo.id}" typeNombre="${tipo.nombre}">
                <td>${tipo.nombre}</td>
                <td>
                <button class="editar btn btn-success" title="Editar tipo" type="button" data-toggle="modal" data-target="#cambiar-nombre-type"><i class="fas fa-pencil-alt"></i></button>
                <button class="borrar btn btn-danger" title="Eliminar tipo" type="button" data-toggle="modal" data-target="#remove-type"><i class="fas fa-trash"></i></button>
                </td>
                </tr>
                `;
            })
            $('#tipos').html(templete);
        });
    }
    $(document).on('keyup','#buscar-type',function(){
        let valor = $(this).val();
        if (valor!==""){
            buscar_type(valor);
        } else {
            buscar_type();
        }
    });
});