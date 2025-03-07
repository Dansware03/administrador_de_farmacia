<?php
include_once '../db/usuario.php';
session_start();

// Redirigir si ya está autenticado
if (isset($_SESSION['us_tipo'])) {
    $userTypeRedirect = [
        1 => '../pages/adm_catalogo.php',
        2 => '../pages/tec_catalogo.php',
        3 => '../pages/adm_catalogo.php'
    ];
    header('Location: ' . $userTypeRedirect[$_SESSION['us_tipo']]);
    exit;
}

// Procesar el formulario de login
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
                2 => '../pages/tec_catalogo.php',
                3 => '../pages/adm_catalogo.php'
            ];

            header('Location: ' . $userTypeRedirect[$_SESSION['us_tipo']]);
            exit;
        }
    } else {
        header('Location: ../../index.php?login_error=1');
        exit;
    }
}

header('Location: ../../index.php');
?>
