// SIMAP - Perfil y Datos Personales
$(document).ready(function() {
    let funcion = '';
    const id_usuario = $('#id_usuario').val();
    let datosOriginales = {};

    function buscarUsuario(dato) {
        funcion = 'buscar_usuario';
        $.post('../controller/UserController.php', { dato, funcion }, (Response) => {
            try {
                const usuario = typeof Response === 'object' ? Response : JSON.parse(Response || '{}');
                $('#nombre_us').html(usuario.nombre || 'Usuario');
                $('#apellidos_us').html(usuario.apellidos || '');
                $('#edad').html(usuario.edad ? usuario.edad + ' años' : 'No especificada');
                $('#ci_us').html(usuario.ci || '---');
                
                let tipoBadge = (usuario.tipo == 'Administrador') ? 'bg-primary' : 'bg-success';
                $('#tipo_us').html(`<span class="badge ${tipoBadge} px-3 py-2 fs-6 shadow-sm">${usuario.tipo || 'Usuario'}</span>`);
                
                // Guardar datos originales para restaurar si cancela edición
                datosOriginales = {
                    telefono: usuario.telefono || '',
                    correo: usuario.correo || '',
                    genero: usuario.genero || 'hombre',
                    info: usuario.info || ''
                };

                // Asignar a inputs
                $('#telefono').val(datosOriginales.telefono);
                $('#email').val(datosOriginales.correo);
                $('#genero').val(datosOriginales.genero);
                $('#info-adicional').val(datosOriginales.info);

                const avatarSrc = usuario.avatar || '../libs/img/avatars/user-default.png';
                $('#avatar1, #avatar2, #avatar3').attr('src', avatarSrc);
            } catch (e) {
                console.error('Error al procesar datos del usuario:', e);
            }
        });
    }

    // Toggle para habilitar edición de datos de contacto
    $(document).on('click', '#btn-toggle-edit', function() {
        $('#telefono, #email, #genero, #info-adicional').prop('disabled', false);
        $('#btn-toggle-edit').hide();
        $('#acciones-edicion').removeAttr('style').show();
        $('#telefono').focus();
    });

    // Cancelar edición y restaurar valores
    $(document).on('click', '#btn-cancelar-edit', function() {
        $('#telefono').val(datosOriginales.telefono);
        $('#email').val(datosOriginales.correo);
        $('#genero').val(datosOriginales.genero);
        $('#info-adicional').val(datosOriginales.info);

        $('#telefono, #email, #genero, #info-adicional').prop('disabled', true);
        $('#acciones-edicion').hide();
        $('#btn-toggle-edit').show();
    });

    // Guardar cambios del perfil
    $('#form-usuario').submit(function(e) {
        e.preventDefault();
        const telefono = $('#telefono').val().trim();
        const correo = $('#email').val().trim();
        const genero = $('#genero').val();
        const info = $('#info-adicional').val().trim();
        funcion = 'editar_usuario';

        $.post('../controller/UserController.php', { funcion, telefono, correo, genero, info }, (Response) => {
            if (Response.trim() === 'editado') {
                Swal.fire({
                    position: 'center',
                    icon: 'success',
                    title: 'Datos actualizados con éxito',
                    showConfirmButton: false,
                    timer: 1500
                });
                $('#telefono, #email, #genero, #info-adicional').prop('disabled', true);
                $('#acciones-edicion').hide();
                $('#btn-toggle-edit').show();
                buscarUsuario(id_usuario);
            } else {
                Swal.fire({
                    position: 'center',
                    icon: 'error',
                    title: 'Error al actualizar datos',
                    text: 'Ocurrió un inconveniente al guardar la información.',
                    showConfirmButton: false,
                    timer: 1500
                });
            }
        });
    });

    // Toggle visibilidad de contraseñas (ojo)
    $(document).on('click', '.btn-toggle-pass', function() {
        const targetId = $(this).data('target');
        const input = $('#' + targetId);
        const icon = $(this).find('i');

        if (input.attr('type') === 'password') {
            input.attr('type', 'text');
            icon.removeClass('bi-eye').addClass('bi-eye-slash');
        } else {
            input.attr('type', 'password');
            icon.removeClass('bi-eye-slash').addClass('bi-eye');
        }
    });

    // Validación de coincidencia en tiempo real
    $('#newpass, #confirmpass').on('input keyup', function() {
        const newpass = $('#newpass').val();
        const confirmpass = $('#confirmpass').val();
        const feedback = $('#pass-match-feedback');

        if (confirmpass.length === 0) {
            feedback.addClass('d-none').html('');
            return;
        }

        feedback.removeClass('d-none');
        if (newpass === confirmpass) {
            feedback.html('<span class="text-success fw-semibold"><i class="bi bi-check-circle me-1"></i>Las contraseñas coinciden</span>');
        } else {
            feedback.html('<span class="text-danger fw-semibold"><i class="bi bi-x-circle me-1"></i>Las contraseñas no coinciden</span>');
        }
    });

    // Actualizar contraseña con Bcrypt
    $('#form-pass').submit(function(e) {
        e.preventDefault();
        const oldpass = $('#oldpass').val();
        const newpass = $('#newpass').val();
        const confirmpass = $('#confirmpass').val();

        if (newpass.length < 6) {
            Swal.fire({
                icon: 'warning',
                title: 'Contraseña muy corta',
                text: 'La nueva contraseña debe tener al menos 6 caracteres.',
                confirmButtonColor: '#1a3a5c'
            });
            return;
        }

        if (newpass !== confirmpass) {
            Swal.fire({
                icon: 'warning',
                title: 'Las contraseñas no coinciden',
                text: 'Por favor verifique que la confirmación sea exactamente igual a la nueva contraseña.',
                confirmButtonColor: '#1a3a5c'
            });
            return;
        }

        funcion = 'cambiar_contra';
        $.post('../controller/UserController.php', { funcion, oldpass, newpass }, (Response) => {
            $('#form-pass').trigger('reset');
            $('#pass-match-feedback').addClass('d-none').html('');
            
            const modalEl = document.getElementById('cambiarcontrasena');
            const modalInstance = bootstrap.Modal.getInstance(modalEl);
            if (modalInstance) modalInstance.hide();

            if (Response.trim() === 'update') {
                Swal.fire({
                    position: 'center',
                    icon: 'success',
                    title: 'Contraseña actualizada con éxito',
                    text: 'Su nueva contraseña cifrada se encuentra activa.',
                    showConfirmButton: false,
                    timer: 1600
                });
            } else {
                Swal.fire({
                    position: 'center',
                    icon: 'error',
                    title: 'Error al cambiar contraseña',
                    text: 'Verifique su contraseña actual ingresada.',
                    showConfirmButton: true,
                    confirmButtonColor: '#1a3a5c'
                });
            }
        });
    });

    // Preview dinámico al seleccionar archivo de foto
    $('#foto_user').on('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#avatar1').attr('src', e.target.result);
            };
            reader.readAsDataURL(file);
        }
    });

    // Subida de foto de perfil
    $('#form-foto').submit(function(e) {
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
                const json = typeof Response === 'object' ? Response : JSON.parse(Response);
                if (json.alert == 'edit') {
                    $('#avatar1, #avatar2, #avatar3').attr('src', json.ruta);
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
                        text: 'Asegúrese de subir una imagen JPG, PNG o WEBP de hasta 5 MB.',
                        showConfirmButton: true,
                        confirmButtonColor: '#1a3a5c'
                    });
                }
            } catch (err) {
                console.error("Error al procesar respuesta:", err);
            }
        });
    });

    buscarUsuario(id_usuario);
});