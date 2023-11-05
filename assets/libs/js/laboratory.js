$(document).ready(function() {
    buscar_lab();
    var funcion
    $('#form-crear-laboratorio').submit(e=>{
        let nombre_laboratory = $('#nombre-laboratorio').val();
        funcion='crear';
        $.post('../controller/LaboratoryController.php',{nombre_laboratory,funcion},(Response)=>{
            if (Response=='add') {
                $('#form-crear-laboratorio').trigger('reset');
                buscar_lab();
                Swal.fire({
                    position: 'center',
                    icon: 'success',
                    title: 'Laboratorio Creado Con Exito',
                    showConfirmButton: false,
                    timer: 1500
                })
                } else {
                    $('#form-crear-laboratorio').trigger('reset');
                        Swal.fire({
                            position: 'center',
                            icon: 'error',
                            title: 'Error al Crear Laboratorio!!',
                            showConfirmButton: false,
                            timer: 1500
                        })
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
                $('#form-cambiar-foto-laboratorio').trigger('reset');
                Swal.fire({
                    position: 'center',
                    icon: 'success',
                    title: 'Avatar Cambiada Con Exito',
                    showConfirmButton: false,
                    timer: 1500
                  })
            } else{
                $('#form-cambiar-foto-laboratorio').trigger('reset');
                Swal.fire({
                    position: 'center',
                    icon: 'error',
                    title: 'Error al Subir Imagen!!',
                    showConfirmButton: false,
                    timer: 1500
                  })
            }
        });
        e.preventDefault();
    })
    $(document).on('click','.borrar',(e)=>{
        funcion="borrar";
        const elemento = $(this)[0].activeElement.parentElement.parentElement;
        const id = $(elemento).attr('labId');
        const nombre = $(elemento).attr('labNombre');
        const avatar = $(elemento).attr('labAvatar');

        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
            confirmButton: 'btn btn-success',
            cancelButton: 'btn btn-danger mr-1'
            },
            buttonsStyling: false
        })
        swalWithBootstrapButtons.fire({
            title: 'Estas seguro que desea eliminar '+nombre+'?',
            text: "¡No podrás revertir esto!",
            
            showCancelButton: true,
            confirmButtonText: '¡Sí, bórralo!',
            cancelButtonText: '¡No, cancela!',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
            swalWithBootstrapButtons.fire(
                'Eliminado!',
                'Su Laboratorio '+nombre+' ha sido eliminado.',
                'success'
            )
            } else if (
              /* Read more about handling dismissals below */
            result.dismiss === Swal.DismissReason.cancel
            ) {
            swalWithBootstrapButtons.fire(
                'Cancelado',
                'Tu Laboratorio '+nombre+' imaginario está a salvo :)',
                'error'
            )
            }
        })
        // $('#lab-actual').attr('src',avatar);
        // $('#nombre_logo').html(nombre);
        // $('#id_logo_lab').val(id);
    })
});