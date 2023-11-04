<?php
include_once '../db/presentaciones.php';
$presentacion = new presentacion();
if ($_POST['funcion']=='crear') {
    $nombre = $_POST['nombre_presentacion'];
    $presentacion->crear($nombre);
}
if ($_POST['funcion'] == 'buscar') {
    $presentacion->buscar();
    $json = array();
    foreach ($presentacion->objetos as $objeto) {
        $json[] = array(
            'id' => $objeto->id_presentacion,
            'nombre' => $objeto->nombre
        );
    }
    // Codifica el array en JSON y lo imprime
    echo json_encode($json);
}
?>