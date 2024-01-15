<?php
include_once '../db/usuario.php';
session_start();
if (isset($_SESSION['us_tipo'])) {
    switch ($_SESSION['us_tipo']) {
        case 1:
            header('Location: ../pages/adm_catalogo.php');
            exit;
        case 2:
            header('Location: ../pages/tec_catalogo.php');
            exit; // Importante: detiene la ejecución del script
        case 3:
                header('Location: ../pages/adm_catalogo.php');
                exit; // Importante: detiene la ejecución del script
    }
}
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

            // Redirige según el tipo de usuario
            switch ($_SESSION['us_tipo']) {
                case 1:
                    header('Location: ../pages/adm_catalogo.php');
                exit;
                case 2:
                    header('Location: ../pages/tec_catalogo.php');
                exit; // Importante: detiene la ejecución del script
                case 3:
                    header('Location: ../pages/adm_catalogo.php');
                    exit; // Importante: detiene la ejecución del script
            }
        }
    } else {
        header('Location: ../../index.php?login_error=1');
        exit;
    }
}
header('Location: ../../index.php');