<?php
include_once '../db/type.php';
$tipo_producto = new tipo_producto();
if (isset($_POST['funcion'])) {
    $funcion = $_POST['funcion'];
    switch ($funcion) {
        case 'crear':
            $nombre = $_POST['nombre_type'];
            $tipo_producto->crear($nombre);
            break;
        case 'editar':
            $nombre = $_POST['nombre_type'];
            $id_editado = $_POST['id_editado'];
            $tipo_producto->editar($nombre, $id_editado);
            break;
        case 'buscar':
            $tipo_producto->buscar();
            $json = array();
            foreach ($tipo_producto->objetos as $objeto) {
                $json[] = array(
                    'id' => $objeto->id_tip_prod,
                    'nombre' => $objeto->nombre
                );
            }
            $jsonstring = json_encode($json);
            echo $jsonstring;
            break;
        case 'borrar_type':
            $id = $_POST['id'];
            $tipo_producto->borrar_type($id);
            break;
    }
}
?>