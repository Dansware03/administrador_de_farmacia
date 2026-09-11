// SIMAP - Perfil y Datos Personales
$(document).ready(function() {
    let funcion = '';
    const id_usuario = $('#id_usuario').val();
    let edit = false;

    function buscarUsuario(dato) {
        funcion = 'buscar_usuario';
        $.post('../controller/UserController.php', { dato, funcion }, (Response) => {
            const usuario = JSON.parse(Response);
            $('#nombre_us').html(usuario.nombre);
            $('#apellidos_us').html(usuario.apellidos);
            $('#edad').html(usuario.edad ? usuario.edad + ' años' : 'No especificada');
            $('#ci_us').html(usuario.ci);
            
            let tipoBadge = 'bg-secondary';
            if (usuario.tipo == 'Root') {
                tipoBadge = 'bg-danger';
            } else if (usuario.tipo == 'Administrador') {
                tipoBadge = 'bg-primary';
            } else if (usuario.tipo == 'Farmaceutico') {
                tipoBadge = 'bg-success';
            }
            $('#tipo_us').html(`<span class="badge ${tipoBadge} px-3 py-2 fs-6 shadow-sm">${usuario.tipo}</span>`);
            
            // Llenar campos de contacto y perfil
            $('#telefono').val(usuario.telefono);
            $('#email').val(usuario.correo);
            $('#genero').val(usuario.genero);
            $('#info-adicional').val(usuario.info);

            $('#avatar1, #avatar2, #avatar3, #avatar4').attr('src', usuario.avatar);
        });
    }

    $(document).on('click', '.edit', (e) => {
        funcion = 'capturar_datos';
        edit = true;
        $.post('../controller/UserController.php', { funcion, id_usuario }, (Response) => {
            const usuario = JSON.parse(Response);
            $('#telefono').val(usuario.telefono);
            $('#email').val(usuario.correo);
            $('#genero').val(usuario.genero);
            $('#info-adicional').val(usuario.info);
            
            Swal.fire({
                position: 'top-end',
                icon: 'info',
                title: 'Modo edición activado',
                showConfirmButton: false,
                timer: 1200,
                toast: true
            });
        });
    });

    $('#form-usuario').submit((e) => {
        e.preventDefault();
        const telefono = $('#telefono').val();
        const correo = $('#email').val();
        const genero = $('#genero').val();
        const info = $('#info-adicional').val();
        funcion = 'editar_usuario';

        $.post('../controller/UserController.php', { id_usuario, funcion, telefono, correo, genero, info }, (Response) => {
            if (Response.trim() === 'editado') {
                Swal.fire({
                    position: 'center',
                    icon: 'success',
                    title: 'Datos actualizados con éxito',
                    showConfirmButton: false,
                    timer: 1500
                });
                edit = false;
                buscarUsuario(id_usuario);
            } else {
                Swal.fire({
                    position: 'center',
                    icon: 'error',
                    title: 'Error al actualizar datos',
                    showConfirmButton: false,
                    timer: 1500
                });
            }
        });
    });

    $('#form-pass').submit((e) => {
        e.preventDefault();
        const oldpass = $('#oldpass').val();
        const newpass = $('#newpass').val();
        funcion = 'cambiar_contra';

        $.post('../controller/UserController.php', { id_usuario, funcion, oldpass, newpass }, (Response) => {
            $('#form-pass').trigger('reset');
            const modalEl = document.getElementById('cambiarcontrasena');
            const modalInstance = bootstrap.Modal.getInstance(modalEl);
            if (modalInstance) modalInstance.hide();

            if (Response.trim() === 'update') {
                Swal.fire({
                    position: 'center',
                    icon: 'success',
                    title: 'Contraseña actualizada con éxito',
                    showConfirmButton: false,
                    timer: 1500
                });
            } else {
                Swal.fire({
                    position: 'center',
                    icon: 'error',
                    title: 'Error al cambiar contraseña',
                    text: 'Verifique su contraseña actual.',
                    showConfirmButton: false,
                    timer: 1500
                });
            }
        });
    });

    $('#form-foto').submit((e) => {
        e.preventDefault();
        const formData = new FormData($('#form-foto')[0]);
        $.ajax({
            url: '../controller/UserController.php',
            type: 'POST',
            data: formData,
            cache: false,
            processData: false,
            contentType: false
        }).done(function(Response) {
            const modalEl = document.getElementById('cambiofoto');
            const modalInstance = bootstrap.Modal.getInstance(modalEl);
            if (modalInstance) modalInstance.hide();
            $('#form-foto').trigger('reset');

            try {
                const json = JSON.parse(Response);
                if (json.alert == 'edit') {
                    $('#avatar1, #avatar2, #avatar3, #avatar4').attr('src', json.ruta);
                    buscarUsuario(id_usuario);
                    Swal.fire({
                        position: 'center',
                        icon: 'success',
                        title: 'Foto de perfil actualizada',
                        showConfirmButton: false,
                        timer: 1500
                    });
                } else {
                    Swal.fire({
                        position: 'center',
                        icon: 'error',
                        title: 'Formato o imagen inválida',
                        showConfirmButton: false,
                        timer: 1500
                    });
                }
            } catch (err) {
                console.error("Error al procesar respuesta:", err);
            }
        });
    });

    buscarUsuario(id_usuario);
});