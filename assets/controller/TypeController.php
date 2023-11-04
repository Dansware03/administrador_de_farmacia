<?php
include_once '../db/type.php';
$type = new type();
if ($_POST['funcion']=='crear') {
    $nombre = $_POST['nombre_type'];
    $type->crear($nombre);
}
if ($_POST['funcion'] == 'buscar') {
    $type->buscar();
    $json = array();
    foreach ($type->objetos as $objeto) {
        $json[] = array(
            'id' => $objeto->id_tip_prod,
            'nombre' => $objeto->nombre
        );
    }
    // Codifica el array en JSON y lo imprime
    echo json_encode($json);
}

?>