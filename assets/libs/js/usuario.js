$(document).ready(function() {
    let funcion = '';
    let id_usuario = $('#id_usuario').val();
    let edit = false;
    function buscarUsuario(dato) {
        funcion = 'buscar_usuario';
    $.post('../controller/UserController.php', { dato, funcion }, (Response) => {
        const usuario = JSON.parse(Response);
        $('#nombre_us').html(usuario.nombre);
        $('#apellidos_us').html(usuario.apellidos);
        $('#edad').html(usuario.edad);
        $('#ci_us').html(usuario.ci);
        let tipoBadge = '';
        if (usuario.tipo == 'Root') {
            tipoBadge = 'badge-danger';
        } else if (usuario.tipo == 'Administrador') {
            tipoBadge = 'badge-primary';
        } else if (usuario.tipo == 'Farmaceutico') {
            tipoBadge = 'badge-success';
        }
        $('#tipo_us').html(`<h1 class="badge ${tipoBadge}">${usuario.tipo}</h1>`);
        $('#telefono_us').html(usuario.telefono);
        $('#correo_us').html(usuario.correo);
        $('#genero_us').html(usuario.genero);
        $('#info_us').html(usuario.info);
        $('#avatar1, #avatar2, #avatar3, #avatar4').attr('src', usuario.avatar);
    });
    $(document).on('click', '.edit', (e) => {
        funcion = 'capturar_datos';
        edit = true;
        $.post('../controller/UserController.php', { funcion, id_usuario }, (Response) => {
        const usuario = JSON.parse(Response);
        $('#telefono').val(usuario.telefono);
        $('#email').val(usuario.correo);
        $('#genero').val(usuario.genero);
        $('#info-adicional').val(usuario.info);
        });
    });
    $('#form-usuario').submit((e) => {
        e.preventDefault();
        if (edit) {
        let telefono = $('#telefono').val();
        let correo = $('#email').val();
        let genero = $('#genero').val();
        let info = $('#info-adicional').val();
        funcion = 'editar_usuario';
        $.post('../controller/UserController.php', { id_usuario, funcion, telefono, correo, genero, info }, (Response) => {
            if (Response === 'editado') {
            $('#form-usuario').trigger('reset');
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: 'Realizado Exito',
                showConfirmButton: false,
                timer: 1500
            });
            $('#cambiofoto').modal('hide');
            }
            edit = false;
            buscarUsuario(id_usuario);
        });
        } else {
        $('#form-usuario').trigger('reset');
        Swal.fire({
            position: 'center',
            icon: 'error',
            title: 'Ah Ocurrido Un Error!!',
            showConfirmButton: false,
            timer: 1500
        });
        }
    });
    $('#form-pass').submit((e) => {
        let oldpass = $('#oldpass').val();
        let newpass = $('#newpass').val();
        funcion = 'cambiar_contra';
        $.post('../controller/UserController.php', { id_usuario, funcion, oldpass, newpass }, (Response) => {
        if (Response === 'update') {
            $('#form-pass').trigger('reset');
            Swal.fire({
            position: 'center',
            icon: 'success',
            title: 'Realizado Exito',
            showConfirmButton: false,
            timer: 1500
            });
            $('#cambiarcontrasena').modal('hide');
        } else {
            $('#form-pass').trigger('reset');
            Swal.fire({
            position: 'center',
            icon: 'error',
            title: 'Ah Ocurrido Un Error!!',
            showConfirmButton: false,
            timer: 1500
            });
        }
        $('#cambiarcontrasena').modal('hide');
        });
        e.preventDefault();
    });
    $('#form-foto').submit((e) => {
        let formData = new FormData($('#form-foto')[0]);
        $.ajax({
        url: '../controller/UserController.php',
        type: 'POST',
        data: formData,
        cache: false,
        processData: false,
        contentType: false
        }).done(function (Response) {
        const json = JSON.parse(Response);
        if (json.alert == 'edit') {
            $('#avatar1').attr('src', json.ruta);
            buscarUsuario(id_usuario);
            $('#form-foto').trigger('reset');
            Swal.fire({
            position: 'center',
            icon: 'success',
            title: 'Realizado Exito',
            showConfirmButton: false,
            timer: 1500
            });
            $('#cambiofoto').modal('hide');
        } else {
            $('#form-foto').trigger('reset');
            Swal.fire({
            position: 'center',
            icon: 'error',
            title: 'Ah Ocurrido Un Error!!',
            showConfirmButton: false,
            timer: 1500
            });
        }
        $('#cambiofoto').modal('hide');
        });
        e.preventDefault();
    });
    }
    buscarUsuario(id_usuario);
});