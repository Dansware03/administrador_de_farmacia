<?php
include_once '../db/laboratory.php';
$laboratorio = new laboratorio();
if (isset($_POST['funcion'])) {
    $funcion = $_POST['funcion'];
    switch ($funcion) {
        case 'crear':
            $nombre = $_POST['nombre_laboratory'];
            $laboratorio->crear($nombre);
            break;
        case 'editar':
            $nombre = $_POST['nombre_laboratory'];
            $id_editado = $_POST['id_editado'];
            $laboratorio->editar($nombre, $id_editado);
            break;
        case 'buscar':
            $laboratorio->buscar();
            $json = array();
            foreach ($laboratorio->objetos as $objeto) {
                $json[] = array(
                    'id' => $objeto->id_laboratorio,
                    'nombre' => $objeto->nombre
                );
            }
            $jsonstring = json_encode($json);
            echo $jsonstring;
            break;
        case 'borrar_lab':
            $id = $_POST['id'];
            $laboratorio->borrar_lab($id);
            break;
        case 'rellenar_laboratorio':
            $laboratorio->rellenar_laboratorio();
            $json = array();
            foreach ($laboratorio->objetos as $objeto) {
                $json[]=array(
                    'id'=>$objeto->id_laboratorio,
                    'nombre'=>$objeto->nombre
                );
            }
            $jsonstring=json_encode($json);
            echo $jsonstring;
            break;
    }
}
?>