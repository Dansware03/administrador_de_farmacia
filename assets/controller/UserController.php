<?php
include_once '../db/usuario.php';
$usuario = new Usuario();
session_start();
$id_usuario= $_SESSION['usuario'];
if ($_POST['funcion']=='buscar_usuario'){
    $json=array();
    $fecha_actual = new DateTime();
    $usuario->obtener_datos($_POST['dato']);
    foreach ($usuario->objetos as $objeto) {
        $nacimiento = new DateTime($objeto->edad);
        $edad = $nacimiento->diff($fecha_actual);
        $edad_year = $edad->y;
        $json[]=array(
            'nombre'=>$objeto->nombre_us,
            'apellidos'=>$objeto->apellidos_us,
            'edad' => $edad_year,
            'ci'=>$objeto->ci_us,
            'tipo'=>$objeto->nombre_tipo,
            'telefono'=>$objeto->telefono_us,
            'correo'=>$objeto->correo_us,
            'genero'=>$objeto->genero_us,
            'info'=>$objeto->info_us,
            'avatar'=>'../libs/img/avatars/'.$objeto->avatar
        );
    }
    $jsonstring = json_encode($json[0]);
    echo $jsonstring;
}
if ($_POST['funcion']=='capturar_datos'){
    $json=array();
    // ponytail: Garantizar que capturar_datos use la sesión activa del usuario
    $id_usuario = $_SESSION['usuario'];
    $usuario->obtener_datos($id_usuario);
    foreach ($usuario->objetos as $objeto) {
        $json[]=array(
            'telefono'=>$objeto->telefono_us,
            'correo'=>$objeto->correo_us,
            'genero'=>$objeto->genero_us,
            'info'=>$objeto->info_us
        );
    }
    $jsonstring = json_encode($json[0]);
    echo $jsonstring;
}
if ($_POST['funcion']=='editar_usuario'){
    // ponytail: Anti-IDOR: siempre actualizar el usuario de la sesión autenticada
    $id_usuario = $_SESSION['usuario'];
    $telefono = trim($_POST['telefono'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    $genero = trim($_POST['genero'] ?? '');
    $info = trim($_POST['info'] ?? '');
    $usuario->editar($id_usuario, $telefono, $correo, $genero, $info);
    echo 'editado';
}
if ($_POST['funcion']=='cambiar_contra'){
    // ponytail: Anti-IDOR: el cambio de contraseña solo aplica al usuario autenticado
    $id_usuario = $_SESSION['usuario'];
    $oldpass = $_POST['oldpass'] ?? '';
    $newpass = $_POST['newpass'] ?? '';
    $usuario->cambiar_contra($id_usuario, $oldpass, $newpass);
}
if ($_POST['funcion']=='cambiar_foto'){
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
            // ponytail: Proteger user-default.png para que nunca se elimine del disco
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
if ($_POST['funcion'] == 'buscar_usuario_adm') {
    $json = array();
    $fecha_actual = new DateTime();
    $usuario->buscar();
    foreach ($usuario->objetos as $objeto) {
        $nacimiento = new DateTime($objeto->edad);
        $edad = $nacimiento->diff($fecha_actual);
        $edad_year = $edad->y;
        $json[] = array(
            'id'=>$objeto->id_usuario,
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
            'tipo_usuario'=>$objeto->us_tipo
        );
    }
    $jsonstring = json_encode($json);
    echo $jsonstring;
}
if ($_POST['funcion'] == 'crear_usuario') {
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $edad = $_POST['edad'];
    $ci = $_POST['ci'];
    $genero = $_POST['genero'];
    $pass = $_POST['pass'];
    // ponytail: Por política institucional SIMAP, los nuevos registros creados corresponden al rol Secretario (tipo = 2).
    $tipo=2;
    $avatar='user-default.png';
    $usuario->crear($nombre,$apellido,$edad,$ci,$genero,$pass,$tipo,$avatar);
}
if ($_POST['funcion']=='ascender') {
    $pass = $_POST['pass'];
    $id_up = $_POST['id_usuario'];
    $usuario->ascender($pass,$id_up,$id_usuario);
}
if ($_POST['funcion']=='descender') {
    $pass = $_POST['pass'];
    $id_donw = $_POST['id_usuario'];
    $usuario->descender($pass,$id_donw,$id_usuario);
}
if ($_POST['funcion']=='delete_user') {
    $pass = $_POST['pass'];
    $id_delete = $_POST['id_usuario'];
    $usuario->delete($pass,$id_delete,$id_usuario);
}

?>
