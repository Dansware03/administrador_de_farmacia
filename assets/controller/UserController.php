<?php
/**
 * ============================================================================
 * ARCHIVO: UserController.php
 * CAPA: Controlador (Backend / Orquestación HTTP)
 * DESCRIPCIÓN: Administra el ciclo de vida de los usuarios del sistema SIMAP,
 *              la gestión del perfil personal (datos, foto, contraseña Bcrypt),
 *              el catálogo de operadores y las acciones administrativas de control
 *              de acceso (creación de secretarios, ascensos, descensos y eliminaciones).
 *              Implementa protecciones estrictas contra vulnerabilidades IDOR.
 * ENTRADA: Peticiones AJAX POST desde assets/libs/js/usuario.js y gestion_user.js.
 * SALIDA: Arreglos JSON o respuestas de estado ('editado', 'no add', 'no-up', etc.).
 * DEPENDENCIAS: assets/db/usuario.php (Modelo Usuario).
 * ============================================================================
 */

include_once '../db/usuario.php';
$usuario = new Usuario();
session_start();

$id_usuario = $_SESSION['usuario'] ?? 0;
$funcion = $_POST['funcion'] ?? '';

/**
 * CASO DE USO: Buscar Datos de un Usuario por ID
 * ----------------------------------------------------------------------------
 * @route POST assets/controller/UserController.php [funcion=buscar_usuario]
 * @param int $_POST['dato'] ID del usuario a consultar.
 * 
 * FLUJO DE EJECUCIÓN:
 *   1. Invoca `Usuario::obtener_datos($_POST['dato'])`.
 *   2. Calcula la edad en años a partir de la fecha de nacimiento (`DateTime::diff`).
 *   3. Retorna objeto JSON con la información pública del usuario.
 * ----------------------------------------------------------------------------
 */
if ($funcion == 'buscar_usuario') {
    $json = array();
    $fecha_actual = new DateTime();
    $usuario->obtener_datos($_POST['dato']);
    foreach ($usuario->objetos as $objeto) {
        $nacimiento = new DateTime($objeto->fecha_nacimiento);
        $edad = $nacimiento->diff($fecha_actual);
        $edad_year = $edad->y;
        $json[] = array(
            'nombre' => $objeto->nombre_us,
            'apellidos' => $objeto->apellidos_us,
            'edad' => $edad_year,
            'ci' => $objeto->ci_us,
            'tipo' => $objeto->nombre_tipo,
            'telefono' => $objeto->telefono_us,
            'correo' => $objeto->correo_us,
            'genero' => $objeto->genero_us,
            'info' => $objeto->info_us,
            'avatar' => '../libs/img/avatars/' . $objeto->avatar
        );
    }
    $jsonstring = json_encode($json[0] ?? []);
    echo $jsonstring;
    exit;
}

/**
 * CASO DE USO: Capturar Datos del Usuario Autenticado en Sesión
 * ----------------------------------------------------------------------------
 * @route POST assets/controller/UserController.php [funcion=capturar_datos]
 * @description Protección Anti-IDOR: Utiliza exclusivamente $_SESSION['usuario'].
 * 
 * FLUJO DE EJECUCIÓN:
 *   1. Obtiene el ID del usuario directamente de la sesión activa en el servidor.
 *   2. Invoca `Usuario::obtener_datos($id_usuario)`.
 *   3. Emite respuesta JSON con teléfono, correo, género e información adicional.
 * ----------------------------------------------------------------------------
 */
if ($funcion == 'capturar_datos') {
    $json = array();
    $id_usuario = $_SESSION['usuario'];
    $usuario->obtener_datos($id_usuario);
    foreach ($usuario->objetos as $objeto) {
        $json[] = array(
            'telefono' => $objeto->telefono_us,
            'correo' => $objeto->correo_us,
            'genero' => $objeto->genero_us,
            'info' => $objeto->info_us
        );
    }
    $jsonstring = json_encode($json[0] ?? []);
    echo $jsonstring;
    exit;
}

/**
 * CASO DE USO: Editar Datos de Perfil Personal
 * ----------------------------------------------------------------------------
 * @route POST assets/controller/UserController.php [funcion=editar_usuario]
 * @param string $_POST['telefono'] Número telefónico de contacto.
 * @param string $_POST['correo']   Correo electrónico.
 * @param string $_POST['genero']   Género seleccionado.
 * @param string $_POST['info']     Información biográfica o profesional.
 * 
 * FLUJO DE EJECUCIÓN:
 *   1. Anti-IDOR: Toma el ID del usuario autenticado en sesión.
 *   2. Sanea las entradas de texto con trim().
 *   3. Delega en `Usuario::editar($id_usuario, $telefono, $correo, $genero, $info)`.
 *   4. Emite respuesta 'editado'.
 * ----------------------------------------------------------------------------
 */
if ($funcion == 'editar_usuario') {
    $id_usuario = $_SESSION['usuario'];
    $telefono = trim($_POST['telefono'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    $genero = trim($_POST['genero'] ?? '');
    $info = trim($_POST['info'] ?? '');
    $usuario->editar($id_usuario, $telefono, $correo, $genero, $info);
    echo 'editado';
    exit;
}

/**
 * CASO DE USO: Cambiar Contraseña de Usuario con Bcrypt
 * ----------------------------------------------------------------------------
 * @route POST assets/controller/UserController.php [funcion=cambiar_contra]
 * @param string $_POST['oldpass'] Contraseña actual del usuario.
 * @param string $_POST['newpass'] Nueva contraseña (mínimo 6 caracteres).
 * 
 * FLUJO DE EJECUCIÓN:
 *   1. Anti-IDOR: Aplica exclusivamente al ID de la sesión autenticada.
 *   2. Delega en `Usuario::cambiar_contra($id_usuario, $oldpass, $newpass)`.
 *   3. El modelo verifica la clave actual y aplica `password_hash(..., PASSWORD_BCRYPT)`.
 * ----------------------------------------------------------------------------
 */
if ($funcion == 'cambiar_contra') {
    $id_usuario = $_SESSION['usuario'];
    $oldpass = $_POST['oldpass'] ?? '';
    $newpass = $_POST['newpass'] ?? '';
    $usuario->cambiar_contra($id_usuario, $oldpass, $newpass);
    exit;
}

/**
 * CASO DE USO: Cambiar Avatar / Foto de Perfil
 * ----------------------------------------------------------------------------
 * @route POST assets/controller/UserController.php [funcion=cambiar_foto]
 * @param array $_FILES['foto'] Archivo de imagen subido (JPEG, PNG, WebP).
 * 
 * FLUJO DE EJECUCIÓN:
 *   1. Valida tipo MIME, extensión permitida y peso máximo (<= 5 MB).
 *   2. Genera nombre único con prefijo `usr_` y mueve el archivo al disco.
 *   3. Actualiza el nombre del avatar en la base de datos.
 *   4. Protege `user-default.png` para que nunca sea eliminado al sustituir avatares.
 *   5. Emite JSON con la ruta de la nueva imagen y estado 'edit'.
 * ----------------------------------------------------------------------------
 */
if ($funcion == 'cambiar_foto') {
    $id_usuario = $_SESSION['usuario'];
    $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
    $fileType = $_FILES['foto']['type'] ?? '';
    $ext = strtolower(pathinfo($_FILES['foto']['name'] ?? '', PATHINFO_EXTENSION));
    $allowedExts = ['jpeg', 'jpg', 'png', 'webp'];

    if (in_array($fileType, $allowedTypes) && in_array($ext, $allowedExts) && ($_FILES['foto']['size'] ?? 0) <= 5242880) {
        $nombre = uniqid('usr_') . '.' . $ext;
        $ruta = '../libs/img/avatars/' . $nombre;
        if (move_uploaded_file($_FILES['foto']['tmp_name'], $ruta)) {
            $prevAvatares = $usuario->cambiar_foto($id_usuario, $nombre);
            if (!empty($prevAvatares)) {
                foreach ($prevAvatares as $objeto) {
                    $oldAvatar = $objeto->avatar ?? '';
                    if (!empty($oldAvatar) && strpos($oldAvatar, 'user-default') === false && file_exists('../libs/img/avatars/' . $oldAvatar)) {
                        unlink('../libs/img/avatars/' . $oldAvatar);
                    }
                }
            }
            echo json_encode(['ruta' => $ruta, 'alert' => 'edit']);
            exit;
        }
    }
    echo json_encode(['alert' => 'noedit']);
    exit;
}

/**
 * CASO DE USO: Listar y Buscar Usuarios (Gestión Administrativa)
 * ----------------------------------------------------------------------------
 * @route POST assets/controller/UserController.php [funcion=buscar_usuario_adm]
 * @description Consulta el listado general de operadores para el panel administrativo.
 * ----------------------------------------------------------------------------
 */
if ($funcion == 'buscar_usuario_adm') {
    $json = array();
    $fecha_actual = new DateTime();
    $usuario->buscar();
    foreach ($usuario->objetos as $objeto) {
        $nacimiento = new DateTime($objeto->fecha_nacimiento);
        $edad = $nacimiento->diff($fecha_actual);
        $edad_year = $edad->y;
        $json[] = array(
            'id' => $objeto->id_usuario,
            'nombre' => $objeto->nombre_us,
            'apellidos' => $objeto->apellidos_us,
            'edad' => $edad_year,
            'ci' => $objeto->ci_us,
            'tipo' => $objeto->nombre_tipo,
            'telefono' => $objeto->telefono_us,
            'correo' => $objeto->correo_us,
            'genero' => $objeto->genero_us,
            'info' => $objeto->info_us,
            'avatar' => '../libs/img/avatars/' . $objeto->avatar,
            'tipo_usuario' => $objeto->us_tipo
        );
    }
    $jsonstring = json_encode($json);
    echo $jsonstring;
    exit;
}

/**
 * CASO DE USO: Registrar Nuevo Usuario Operador (Secretario)
 * ----------------------------------------------------------------------------
 * @route POST assets/controller/UserController.php [funcion=crear_usuario]
 * @param string $_POST['nombre']   Nombres del usuario.
 * @param string $_POST['apellido'] Apellidos del usuario.
 * @param string $_POST['edad']     Fecha de nacimiento (YYYY-MM-DD).
 * @param string $_POST['ci']       Cédula de identidad (numérica).
 * @param string $_POST['genero']   Género ('hombre', 'mujer', 'otro').
 * @param string $_POST['pass']     Contraseña inicial (mínimo 6 caracteres).
 * 
 * FLUJO DE EJECUCIÓN:
 *   1. Control de Privilegios: Exige rol Administrador ($_SESSION['us_tipo'] == 1).
 *   2. Validaciones de Integridad: Comprueba que CI sea numérica y contraseña válida.
 *   3. Asignación de Rol Institucional: Todo nuevo operador inicia como Secretario (tipo = 2).
 *   4. Delegación al Modelo: `Usuario::crear(...)` aplicando Bcrypt en base de datos.
 * ----------------------------------------------------------------------------
 */
if ($funcion == 'crear_usuario') {
    if (!isset($_SESSION['us_tipo']) || $_SESSION['us_tipo'] != 1) {
        echo 'no add';
        exit;
    }
    $nombre = trim($_POST['nombre'] ?? '');
    $apellido = trim($_POST['apellido'] ?? '');
    $edad = trim($_POST['edad'] ?? '');
    $ci = trim($_POST['ci'] ?? '');
    $genero = trim($_POST['genero'] ?? 'hombre');
    $pass = $_POST['pass'] ?? '';

    // Validaciones de robustez en el servidor
    if (empty($nombre) || empty($apellido) || empty($ci) || empty($edad) || strlen($pass) < 6 || !is_numeric($ci)) {
        echo 'no add';
        exit;
    }

    $tipo = 2; // Secretario
    $avatar = 'user-default.png';
    $usuario->crear($nombre, $apellido, $edad, $ci, $genero, $pass, $tipo, $avatar);
    exit;
}

/**
 * CASO DE USO: Ascender Usuario a Rol Administrador
 * ----------------------------------------------------------------------------
 * @route POST assets/controller/UserController.php [funcion=ascender]
 * @param string $_POST['pass']       Contraseña del Administrador que autoriza.
 * @param int    $_POST['id_usuario'] ID del usuario que será ascendido.
 * ----------------------------------------------------------------------------
 */
if ($funcion == 'ascender') {
    if (!isset($_SESSION['us_tipo']) || $_SESSION['us_tipo'] != 1) {
        echo 'no-up';
        exit;
    }
    $pass = $_POST['pass'] ?? '';
    $id_up = (int)($_POST['id_usuario'] ?? 0);
    $id_admin_session = (int)$_SESSION['usuario'];
    $usuario->ascender($pass, $id_up, $id_admin_session);
    exit;
}

/**
 * CASO DE USO: Descender Administrador a Rol Secretario
 * ----------------------------------------------------------------------------
 * @route POST assets/controller/UserController.php [funcion=descender]
 * @param string $_POST['pass']       Contraseña del Administrador que autoriza.
 * @param int    $_POST['id_usuario'] ID del usuario que será degradado.
 * ----------------------------------------------------------------------------
 */
if ($funcion == 'descender') {
    if (!isset($_SESSION['us_tipo']) || $_SESSION['us_tipo'] != 1) {
        echo 'no-donw';
        exit;
    }
    $pass = $_POST['pass'] ?? '';
    $id_donw = (int)($_POST['id_usuario'] ?? 0);
    $id_admin_session = (int)$_SESSION['usuario'];
    $usuario->descender($pass, $id_donw, $id_admin_session);
    exit;
}

/**
 * CASO DE USO: Eliminar Usuario del Sistema
 * ----------------------------------------------------------------------------
 * @route POST assets/controller/UserController.php [funcion=delete_user]
 * @param string $_POST['pass']       Contraseña del Administrador que autoriza.
 * @param int    $_POST['id_usuario'] ID del usuario a eliminar.
 * ----------------------------------------------------------------------------
 */
if ($funcion == 'delete_user') {
    if (!isset($_SESSION['us_tipo']) || $_SESSION['us_tipo'] != 1) {
        echo 'no-delete';
        exit;
    }
    $pass = $_POST['pass'] ?? '';
    $id_delete = (int)($_POST['id_usuario'] ?? 0);
    $id_admin_session = (int)$_SESSION['usuario'];
    $usuario->delete($pass, $id_delete, $id_admin_session);
    exit;
}
?>
