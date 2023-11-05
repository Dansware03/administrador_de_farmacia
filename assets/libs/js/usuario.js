$(document).ready(function() {
    var funcion='';
    var id_usuario = $('#id_usuario').val();
    var edit=false;
    buscar_usuario(id_usuario);
    function buscar_usuario(dato) {
        funcion='buscar_usuario';
        $.post('../controller/UserController.php', { dato, funcion }, (Response) => {
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
                nombre+=`${usuario.nombre}`;
                apellidos+=`${usuario.apellidos}`;
                edad+=`${usuario.edad}`;
                ci+=`${usuario.ci}`;
                if (usuario.tipo=='Root') {
                    tipo+=`<h1 class="badge badge-danger">${usuario.tipo}</h1>`;
                }
                if (usuario.tipo=='Administrador') {
                    tipo+=`<h1 class="badge badge-primary">${usuario.tipo}</h1>`;
                }
                if (usuario.tipo=='Farmaceutico') {
                    tipo+=`<h1 class="badge badge-success">${usuario.tipo}</h1>`;
                }
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
            }
        })
    }
    $(document).on('click','.edit',(e)=>{
        funcion='capturar_datos';
        edit=true;
        $.post('../controller/UserController.php',{funcion,id_usuario},(Response)=>{
            const usuario = JSON.parse(Response);
            $('#telefono').val(usuario.telefono);
            $('#email').val(usuario.correo);
            $('#genero').val(usuario.genero);
            $('#info-adicional').val(usuario.info);
        })
    });
    $('#form-usuario').submit(e=>{
        e.preventDefault();
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
                        $('#form-usuario').trigger('reset');
                        Swal.fire({
                            position: 'center',
                            icon: 'success',
                            title: 'Realizado Exito',
                            showConfirmButton: false,
                            timer: 1500
                        })
                    }
                    edit = false;
                    buscar_usuario(id_usuario);
                }
            );
        }
        else{
        $('#form-usuario').trigger('reset');
        Swal.fire({
            position: 'center',
            icon: 'error',
            title: 'Ah Ocurrido Un Error!!',
            showConfirmButton: false,
            timer: 1500
        })
        }
        e.preventDefault();
    });
    $('#form-pass').submit(e => {
        let oldpass = $('#oldpass').val();
        let newpass = $('#newpass').val();
        funcion='cambiar_contra';
        $.post('../controller/UserController.php',{id_usuario,funcion,oldpass,newpass},(Response)=>{
            if (Response === 'update') {
            $('#form-pass').trigger('reset');
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: 'Realizado Exito',
                showConfirmButton: false,
                timer: 1500
            })
            } else {
            $('#form-pass').trigger('reset');
            Swal.fire({
                position: 'center',
                icon: 'error',
                title: 'Ah Ocurrido Un Error!!',
                showConfirmButton: false,
                timer: 1500
            })
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
            const json = JSON.parse(Response);
            if (json.alert=='edit') {
                $('#avatar1').attr('src',json.ruta);
                buscar_usuario(id_usuario);
                $('#form-foto').trigger('reset');
                Swal.fire({
                    position: 'center',
                    icon: 'success',
                    title: 'Realizado Exito',
                    showConfirmButton: false,
                    timer: 1500
                })
            } else{
                $('#form-foto').trigger('reset');
                Swal.fire({
                    position: 'center',
                    icon: 'error',
                    title: 'Ah Ocurrido Un Error!!',
                    showConfirmButton: false,
                    timer: 1500
                })
            }
        });
        e.preventDefault();
    })
})