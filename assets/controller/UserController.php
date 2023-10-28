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
    $id_usuario=$_POST['id_usuario'];
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
    $id_usuario=$_POST['id_usuario'];
    $telefono=$_POST['telefono'];
    $correo=$_POST['correo'];
    $genero=$_POST['genero'];
    $info=$_POST['info'];
    $usuario->editar($id_usuario,$telefono,$correo,$genero,$info);
    echo 'editado';
}
if ($_POST['funcion']=='cambiar_contra'){
    $id_usuario=$_POST['id_usuario'];
    $oldpass=$_POST['oldpass'];
    $newpass=$_POST['newpass'];
    $usuario->cambiar_contra($id_usuario,$oldpass,$newpass);
}
if ($_POST['funcion']=='cambiar_foto'){
    if (($_FILES['foto']['type'] == 'image/jpeg') || ($_FILES['foto']['type'] == 'image/png') || ($_FILES['foto']['type'] == 'image/jpg') || ($_FILES['foto']['type'] == 'image/bmp')){
    $nombre=uniqid().'-'.$_FILES['foto']['name'];
    $ruta='../libs/img/avatars/'.$nombre;
    move_uploaded_file($_FILES['foto']['tmp_name'],$ruta);
    $usuario->cambiar_foto($id_usuario,$nombre);
    foreach ($usuario->objetos as $objeto) {
        unlink('../libs/img/avatars/' . $objeto->avatar);
    }
        $json= array();
        $json[]=array(
            'ruta'=>$ruta,
            'alert'=>'edit'
        );
        $jsonstring = json_encode($json[0]);
        echo $jsonstring;
    }else {
        $json= array();
        $json[]=array(
            'alert'=>'noedit'
        );
        $jsonstring = json_encode($json[0]);
        echo $jsonstring;
    }
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
    $tipo=2;
    $avatar='user-default.png';
    $usuario->crear($nombre,$apellido,$edad,$ci,$genero,$pass,$tipo,$avatar);
}
?>
