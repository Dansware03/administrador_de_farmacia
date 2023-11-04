<?php
include_once '../db/laboratory.php';
$laboratorio = new laboratorio();
if ($_POST['funcion']=='crear') {
    $nombre = $_POST['nombre_laboratory'];
    $avatar='LabDefault.png';
    $laboratorio->crear($nombre,$avatar);
}
if ($_POST['funcion']=='buscar') {
    $laboratorio->buscar();
    $json=array();
    foreach ($laboratorio->objetos as $objeto) {
        $json[]=array(
            'id'=>$objeto->id_laboratorio,
            'nombre'=>$objeto->nombre,
            'avatar'=>'../libs/img/laboratory/'.$objeto->avatar
        );
    }
    $jsonstring = json_encode($json);
    echo $jsonstring;
}
if ($_POST['funcion']=='cambiar_logo') {
    $id=$_POST['id_logo_lab'];
    if (($_FILES['foto']['type'] == 'image/jpeg') || ($_FILES['foto']['type'] == 'image/png') || ($_FILES['foto']['type'] == 'image/jpg') || ($_FILES['foto']['type'] == 'image/bmp')){
        $nombre=uniqid().'-'.$_FILES['foto']['name'];
        $ruta='../libs/img/laboratory/'.$nombre;
        move_uploaded_file($_FILES['foto']['tmp_name'],$ruta);
        $laboratorio->cambiar_logo($id,$nombre);
        foreach ($laboratorio->objetos as $objeto) {
            if($objeto->avatar != 'LabDefault.png'){
                unlink('../libs/img/laboratory/' . $objeto->avatar);
            }
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
?>