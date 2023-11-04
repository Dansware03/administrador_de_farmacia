<?php
include_once '../db/usuario.php';
session_start();
// Comprueba si el usuario ya ha iniciado sesión
if (isset($_SESSION['us_tipo'])) {
    // El usuario ya ha iniciado sesión, redirige a la página correspondiente
    switch ($_SESSION['us_tipo']) {
        case 1:
            header('Location: ../pages/adm_catalogo.php');
            exit; // Importante: detiene la ejecución del script
        case 2:
            header('Location: ../pages/tec_catalogo.php');
            exit; // Importante: detiene la ejecución del script
        case 3:
                header('Location: ../pages/adm_catalogo.php');
                exit; // Importante: detiene la ejecución del script
    }
}
// Si el usuario no ha iniciado sesión y está enviando un formulario de inicio de sesión
if (isset($_POST['user']) && isset($_POST['pass'])) {
    $user = $_POST['user'];
    $pass = $_POST['pass'];
    $usuario = new Usuario();
    // Intenta iniciar sesión
    $usuario->Loguearse($user, $pass);
    if (!empty($usuario->objetos)) {
        foreach ($usuario->objetos as $objeto) {
            $_SESSION['usuario'] = $objeto->id_usuario;
            $_SESSION['us_tipo'] = $objeto->us_tipo;
            $_SESSION['nombre_us'] = $objeto->nombre_us;

            // Redirige según el tipo de usuario
            switch ($_SESSION['us_tipo']) {
                case 1:
                    header('Location: ../pages/adm_catalogo.php');
                exit; // Importante: detiene la ejecución del script
                case 2:
                    header('Location: ../pages/tec_catalogo.php');
                exit; // Importante: detiene la ejecución del script
                case 3:
                    header('Location: ../pages/adm_catalogo.php');
                    exit; // Importante: detiene la ejecución del script
            }
        }
    } else {
        // Si el inicio de sesión no fue exitoso, puedes redirigir al usuario al formulario de inicio de sesión con un mensaje de error, por ejemplo.
        header('Location: ../../index.php?login_error=1');
        exit; // Importante: detiene la ejecución del script
    }
}
// Si no se inició sesión y no se envió el formulario, redirige al formulario de inicio de sesión
header('Location: ../../index.php');