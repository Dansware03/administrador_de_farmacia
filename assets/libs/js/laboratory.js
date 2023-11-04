$(document).ready(function() {
    buscar_lab();
    var funcion
    $('#form-crear-laboratorio').submit(e=>{
        let nombre_laboratory = $('#nombre-laboratorio').val();
        funcion='crear';
        $.post('../controller/LaboratoryController.php',{nombre_laboratory,funcion},(Response)=>{
            if (Response=='add') {
                $('#add-lab').hide('slow', function () {
                    $(this).show(1000, function () {
                    $(this).hide(2000, function () {
                        $('#form-crear-laboratorio').trigger('reset');
                        });
                    });
                    buscar_lab();
                });
                } else {
                $('#noadd-lab').hide('slow', function () {
                    $(this).show(1000, function () {
                    $(this).hide(2000, function () {
                        $('#form-crear-laboratorio').trigger('reset');
                        });
                    });
                });
                }
        });
        e.preventDefault();
    });
        function buscar_lab(consulta) {
        funcion='buscar';
        $.post('../controller/LaboratoryController.php',{consulta,funcion},(Response)=>{
            const laboratorios = JSON.parse(Response);
            let templete='';
            laboratorios.forEach(laboratorio=> {
                templete+=`
                <tr labId="${laboratorio.id}" labNombre="${laboratorio.nombre}" labAvatar="${laboratorio.avatar}">
                <td>${laboratorio.nombre}</td>
                <td>
                    <img class="img-fluid rounded" width="70" height="70" src="${laboratorio.avatar}" alt="" >
                </td>
                <td>
                <button class="avatar btn btn-info" title="Cambiar Logo" type="button" data-toggle="modal" data-target="#cambiar-foto-lab"><i class="far fa-image"></i></button>
                <button class="editar btn btn-success" title="Editar Laboratorio" type="button" data-toggle="modal" data-target="#cambiar-nombre-lab"><i class="fas fa-pencil-alt"></i></button>
                <button class="borrar btn btn-danger" title="Eliminar Laboratorio" type="button" data-toggle="modal" data-target="#remove-lab"><i class="fas fa-trash"></i></button>
                </td>
                </tr>
                `;
            })
            $('#laboratorios').html(templete);
        });
    }
    $(document).on('keyup','#buscar-laboratory',function(){
        let valor = $(this).val();
        if (valor!==""){
            buscar_lab(valor);
        } else {
            buscar_lab();
        }
    });
    $(document).on('click','.avatar',(e)=>{
        funcion="cambiar_logo";
        const elemento = $(this)[0].activeElement.parentElement.parentElement;
        const id = $(elemento).attr('labId');
        const nombre = $(elemento).attr('labNombre');
        const avatar = $(elemento).attr('labAvatar');
        $('#lab-actual').attr('src',avatar);
        $('#nombre_logo').html(nombre);
        $('#id_logo_lab').val(id);
    })
    $('#form-cambiar-foto-laboratorio').submit(e=>{
        let formData = new FormData($('#form-cambiar-foto-laboratorio')[0]);
        $.ajax({
            url:'../controller/LaboratoryController.php',
            type:'POST',
            data:formData,
            cache:false,
            processData: false,
            contentType:false
        }).done(function(Response){
            const json = JSON.parse(Response);
            if (json.alert=='edit') {
                $('#lab-actual').attr('src',json.ruta);
                buscar_lab();
                $('#edit').hide('slow', function () {
                    $('#form-cambiar-foto-laboratorio').trigger('reset');
                    $(this).show(1000, function () {
                    $(this).hide(2000, function () {
                        });
                    });
                });
            } else{
                $('#form-cambiar-foto-laboratorio').trigger('reset');
                $('#noedit').hide('slow', function () {
                    $(this).show(1000, function () {
                    $(this).hide(2000, function () {
                        });
                    });
                });
            }
        });
        e.preventDefault();
    })
});