<?php
include_once '../db/presentaciones.php';
$presentacion = new presentacion();
if (isset($_POST['funcion'])) {
    $funcion = $_POST['funcion'];
    switch ($funcion) {
        case 'crear':
            $nombre = $_POST['nombre_pre'];
            $presentacion->crear($nombre);
            break;
        case 'editar':
            $nombre = $_POST['nombre_pre'];
            $id_editado = $_POST['id_editado'];
            $presentacion->editar($nombre, $id_editado);
            break;
        case 'buscar':
            $presentacion->buscar();
            $json = array();
            foreach ($presentacion->objetos as $objeto) {
                $json[] = array(
                    'id' => $objeto->id_presentacion,
                    'nombre' => $objeto->nombre
                );
            }
            $jsonstring = json_encode($json);
            echo $jsonstring;
            break;
        case 'borrar_pre':
            $id = $_POST['id'];
            $presentacion->borrar_pre($id);
            break;
    }
}
?>