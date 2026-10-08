<?php
/**
 * ============================================================================
 * ARCHIVO: LoginController.php
 * CAPA: Controlador (Autenticación y Control de Sesiones)
 * DESCRIPCIÓN: Procesa el inicio de sesión de usuarios, valida credenciales
 *              mediante hash seguro Bcrypt (Usuario::Loguearse) y orquesta la
 *              redirección basada en roles institucionales (Administrador / Secretario).
 * ENTRADA: Petición HTTP POST desde el formulario de login en index.php (user, pass).
 * SALIDA: Redirección mediante cabeceras HTTP (Location) hacia las vistas autorizadas
 *         o retorno con bandera de error (?login_error=1).
 * DEPENDENCIAS: assets/db/usuario.php (Modelo Usuario).
 * ============================================================================
 */

include_once '../db/usuario.php';
session_start();

/**
 * CONTROL DE SESIÓN ACTIVA PREVIA
 * ----------------------------------------------------------------------------
 * Si el usuario ya posee una sesión válida en memoria, se redirige de inmediato
 * a su panel de inicio correspondiente sin volver a procesar credenciales.
 *
 * ROLES DISPONIBLES EN SIMAP:
 *   - 1: Administrador -> Redirige a assets/pages/adm_catalogo.php
 *   - 2: Secretario    -> Redirige a assets/pages/tec_catalogo.php
 * ----------------------------------------------------------------------------
 */
if (isset($_SESSION['us_tipo'])) {
    $userTypeRedirect = [
        1 => '../pages/adm_catalogo.php',
        2 => '../pages/tec_catalogo.php'
    ];
    $tipo = $_SESSION['us_tipo'];
    if (isset($userTypeRedirect[$tipo])) {
        header('Location: ' . $userTypeRedirect[$tipo]);
        exit;
    } else {
        // Si el rol ya no es válido (ej: sesión obsoleta o rol eliminado), limpiar sesión
        session_destroy();
        header('Location: ../../index.php');
        exit;
    }
}

/**
 * CASO DE USO: Autenticación de Usuario (Login Form Submit)
 * ----------------------------------------------------------------------------
 * @route POST assets/controller/LoginController.php
 * @param string $_POST['user'] Cédula de identidad (CI) del usuario.
 * @param string $_POST['pass'] Contraseña en texto plano ingresada en el login.
 * 
 * FLUJO DE EJECUCIÓN:
 *   1. Captura las variables $_POST['user'] y $_POST['pass'].
 *   2. Invoca el método Usuario::Loguearse($user, $pass), el cual realiza:
 *      a) Consulta PDO SELECT filtrando por ci_us.
 *      b) Validación criptográfica segura mediante password_verify($pass, $hash).
 *   3. Si la verificación es exitosa:
 *      a) Registra variables de sesión ($_SESSION['usuario'], 'us_tipo', 'nombre_us').
 *      b) Redirige al catálogo según el nivel de privilegios (Rol 1 o Rol 2).
 *   4. Si las credenciales son incorrectas:
 *      a) Destruye cualquier vestigio de sesión.
 *      b) Redirige a index.php?login_error=1 para disparar SweetAlert2 de rechazo.
 * ----------------------------------------------------------------------------
 */
if (isset($_POST['user']) && isset($_POST['pass'])) {
    $user = $_POST['user'];
    $pass = $_POST['pass'];

    $usuario = new Usuario();
    $usuario->Loguearse($user, $pass);

    if (!empty($usuario->objetos)) {
        foreach ($usuario->objetos as $objeto) {
            $_SESSION['usuario'] = $objeto->id_usuario;
            $_SESSION['us_tipo'] = $objeto->us_tipo;
            $_SESSION['nombre_us'] = $objeto->nombre_us;

            $userTypeRedirect = [
                1 => '../pages/adm_catalogo.php',
                2 => '../pages/tec_catalogo.php'
            ];

            $tipo = $objeto->us_tipo;
            if (isset($userTypeRedirect[$tipo])) {
                header('Location: ' . $userTypeRedirect[$tipo]);
                exit;
            } else {
                session_destroy();
                header('Location: ../../index.php?login_error=1');
                exit;
            }
        }
    } else {
        header('Location: ../../index.php?login_error=1');
        exit;
    }
}

// Redirección de seguridad por defecto si se accede directamente por GET
header('Location: ../../index.php');
?>
