$(document).ready(function() {
    var funcion='';
    var id_usuario = $('#id_usuario').val();
    var edit=false;
    buscar_usuario(id_usuario);
    function buscar_usuario(dato) {
        funcion='buscar_usuario';
        $.post('../controller/UserController.php', { dato, funcion }, (Response) => {
            // console.log(Response)
            let nombre='';
            let apellidos='';
            let edad='';
            let ci='';
            let tipo='';
            let telefono='';
            let correo='';
            let genero='';
            let info='';
            try {
            const usuario = JSON.parse(Response);
                // El código para mostrar la información del usuario aquí...
                nombre+=`${usuario.nombre}`;
                apellidos+=`${usuario.apellidos}`;
                edad+=`${usuario.edad}`;
                ci+=`${usuario.ci}`;
                tipo+=`${usuario.tipo}`;
                telefono+=`${usuario.telefono}`;
                correo+=`${usuario.correo}`;
                genero+=`${usuario.genero}`;
                info+=`${usuario.info}`;
                $('#nombre_us').html(nombre);
                $('#apellidos_us').html(apellidos);
                $('#edad').html(edad);
                $('#ci_us').html(ci);
                $('#tipo_us').html(tipo);
                $('#telefono_us').html(telefono);
                $('#correo_us').html(correo);
                $('#genero_us').html(genero);
                $('#info_us').html(info);

                $('#avatar1').attr('src', usuario.avatar);
                $('#avatar2').attr('src', usuario.avatar);
                $('#avatar3').attr('src', usuario.avatar);
                $('#avatar4').attr('src', usuario.avatar);
            } catch (error) {
                console.error('La respuesta no es un JSON válido:', error);
                // Puedes mostrar un mensaje de error o realizar alguna otra acción adecuada.
            }
        })
    }
    $(document).on('click','.edit',(e)=>{
        funcion='capturar_datos';
        edit=true;
        $.post('../controller/UserController.php',{funcion,id_usuario},(Response)=>{
            // console.log(Response);
            const usuario = JSON.parse(Response);
            $('#telefono').val(usuario.telefono);
            $('#email').val(usuario.correo);
            $('#genero').val(usuario.genero);
            $('#info-adicional').val(usuario.info);
        })
    });
    $('#form-usuario').submit(e=>{
        e.preventDefault(); // Evita que el formulario se envíe de inmediato
        if (edit==true) {
            let telefono=$('#telefono').val();
            let correo=$('#email').val();
            let genero=$('#genero').val();
            let info=$('#info-adicional').val();
            funcion='editar_usuario';
            $.post(
                '../controller/UserController.php',
                { id_usuario, funcion, telefono, correo, genero, info },
                function (Response) {
                    if (Response === 'editado') {
                        $('#editado').hide('slow', function () {
                            $(this).show(1000, function () {
                                $(this).hide(2000, function () {
                                    $('#form-usuario').trigger('reset');
                                });
                            });
                        });
                    }
                    edit = false;
                    buscar_usuario(id_usuario);
                }
            );
        }
        else{
            $('#noeditado').hide('slow', function () {
                $(this).show(1000, function () {
                    $(this).hide(3000, function () {
                        $('#form-usuario').trigger('reset');
                    });
                });
            });
        }
        e.preventDefault();
    });
    $('#form-pass').submit(e => {
        let oldpass = $('#oldpass').val();
        let newpass = $('#newpass').val();
        // console.log(oldpass + newpass);
        funcion='cambiar_contra';
        $.post('../controller/UserController.php',{id_usuario,funcion,oldpass,newpass},(Response)=>{
            if (Response === 'update') {
                $('#update').hide('slow', function () {
                    $(this).show(1000, function () {
                        $('#form-pass').trigger('reset');
                    $(this).hide(2000, function () {
                        });
                    });
                });
            } else {
                $('#noupdate').hide('slow', function () {
                    $(this).show(1000, function () {
                        $('#form-pass').trigger('reset');
                        $(this).hide(3000, function () {
                        });
                    });
                });
            }
        })
        e.preventDefault();
        })
    $('#form-foto').submit(e=>{
        let formData = new FormData($('#form-foto')[0]);
        $.ajax({
            url:'../controller/UserController.php',
            type:'POST',
            data:formData,
            cache:false,
            processData: false,
            contentType:false
        }).done(function(Response){
            console.log(Response);
            const json = JSON.parse(Response);
            if (json.alert=='edit') {
                $('#avatar1').attr('src',json.ruta);
                buscar_usuario(id_usuario);
                $('#edit').hide('slow', function () {
                    $(this).show(1000, function () {
                    $(this).hide(2000, function () {
                        $('#form-foto').trigger('reset');
                        });
                    });
                });
            } else{
                $('#form-foto').trigger('reset');
                $('#noedit').hide('slow', function () {
                    $(this).show(1000, function () {
                    $(this).hide(2000, function () {
                        });
                    });
                });
            }
            
            // console.log(Response)
        });
        e.preventDefault();
    })
})