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
?>