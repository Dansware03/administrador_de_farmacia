/**
 * SIMAP - Perfil y Configuración de Datos Personales
 *
 * Administra la vista de perfil del usuario en sesión (`adm_mas_datos.php`):
 * - Carga asíncrona de datos personales y roles.
 * - Edición de teléfonos, correos y biografía con confirmación y restauración.
 * - Cambio de contraseña con validación de clave actual y encriptación Bcrypt.
 * - Actualización de fotografía / avatar de perfil.
 * - Alternador interactivo para visualizar contraseñas.
 *
 * @package SIMAP\Frontend\JS
 * @author Grupo de Proyecto
 * @version 1.0.0
 */

$(document).ready(function() {
    let funcion = '';
    const id_usuario = $('#id_usuario').val();
    let datosOriginales = {};

    // Obtener información del usuario en sesión
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
                
                // Respaldar datos originales por si el usuario cancela la edición
                datosOriginales = {
                    telefono: usuario.telefono || '',
                    correo: usuario.correo || '',
                    genero: usuario.genero || 'hombre',
                    info: usuario.info || ''
                };

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

    // Habilitar campos de contacto para edición
    $(document).on('click', '#btn-toggle-edit', function() {
        $('#telefono, #email, #genero, #info-adicional').prop('disabled', false);
        $('#btn-toggle-edit').hide();
        $('#acciones-edicion').removeAttr('style').show();
        $('#telefono').focus();
    });

    // Cancelar edición y restablecer valores originales
    $(document).on('click', '#btn-cancelar-edit', function() {
        $('#telefono').val(datosOriginales.telefono);
        $('#email').val(datosOriginales.correo);
        $('#genero').val(datosOriginales.genero);
        $('#info-adicional').val(datosOriginales.info);

        $('#telefono, #email, #genero, #info-adicional').prop('disabled', true);
        $('#acciones-edicion').hide();
        $('#btn-toggle-edit').show();
    });

    // Guardar cambios en el perfil
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

    // Alternar visibilidad de contraseña en inputs
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

    // Modificar contraseña personal
    $('#form-pass').submit(e => {
        e.preventDefault();
        let oldpass = $('#oldpass').val();
        let newpass = $('#newpass').val();
        funcion = 'cambiar_contra';

        $.post('../controller/UserController.php', { funcion, oldpass, newpass }, (Response) => {
            if (Response == 'update') {
                $('#form-pass').trigger('reset');
                Swal.fire({
                    position: 'center',
                    icon: 'success',
                    title: 'Contraseña Actualizada con Éxito',
                    showConfirmButton: false,
                    timer: 1200
                }).then(() => {
                    const modalObj = bootstrap.Modal.getInstance(document.getElementById('cambiarcontrasena'));
                    if (modalObj) modalObj.hide();
                });
            } else {
                Swal.fire({
                    position: 'center',
                    icon: 'error',
                    title: 'La contraseña actual no es correcta',
                    showConfirmButton: false,
                    timer: 1500
                });
            }
        });
    });

    // Actualizar foto de perfil
    $('#form-photo').submit(e => {
        e.preventDefault();
        let formData = new FormData($('#form-photo')[0]);
        $.ajax({
            url: '../controller/UserController.php',
            type: 'POST',
            data: formData,
            cache: false,
            processData: false,
            contentType: false
        }).done(function(Response) {
            const json = JSON.parse(Response);
            if (json.alert == 'edit') {
                $('#avatar1, #avatar2, #avatar3').attr('src', json.ruta);
                $('#form-photo').trigger('reset');
                Swal.fire({
                    position: 'center',
                    icon: 'success',
                    title: 'Foto Actualizada con Éxito',
                    showConfirmButton: false,
                    timer: 1200
                }).then(() => {
                    const modalObj = bootstrap.Modal.getInstance(document.getElementById('cambiarfoto'));
                    if (modalObj) modalObj.hide();
                });
            } else {
                Swal.fire({
                    position: 'center',
                    icon: 'error',
                    title: 'Formato o imagen no válida',
                    showConfirmButton: false,
                    timer: 1500
                });
            }
        });
    });

    buscarUsuario(id_usuario);
});