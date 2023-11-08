<?php
include_once '../db/laboratory.php';
$laboratorio = new laboratorio();
if (isset($_POST['funcion'])) {
    $funcion = $_POST['funcion'];
    switch ($funcion) {
        case 'crear':
            $nombre = $_POST['nombre_laboratory'];
            $avatar = 'LabDefault.png';
            $laboratorio->crear($nombre, $avatar);
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
                    'nombre' => $objeto->nombre,
                    'avatar' => '../libs/img/laboratory/' . $objeto->avatar
                );
            }
            $jsonstring = json_encode($json);
            echo $jsonstring;
            break;
        case 'cambiar_logo':
            $id = $_POST['id_logo_lab'];
            if (in_array($_FILES['foto']['type'], ['image/jpeg', 'image/png', 'image/jpg', 'image/bmp'])) {
                $nombre = uniqid() . '-' . $_FILES['foto']['name'];
                $ruta = '../libs/img/laboratory/' . $nombre;
                move_uploaded_file($_FILES['foto']['tmp_name'], $ruta);
                $laboratorio->cambiar_logo($id, $nombre);
                foreach ($laboratorio->objetos as $objeto) {
                    if ($objeto->avatar != 'LabDefault.png') {
                        unlink('../libs/img/laboratory/' . $objeto->avatar);
                    }
                }
                $json = array();
                $json[] = array(
                    'ruta' => $ruta,
                    'alert' => 'edit'
                );
                $jsonstring = json_encode($json[0]);
                echo $jsonstring;
            } else {
                $json = array();
                $json[] = array(
                    'alert' => 'noedit'
                );
                $jsonstring = json_encode($json[0]);
                echo $jsonstring;
            }
            break;
        case 'borrar_lab':
            $id = $_POST['id'];
            $laboratorio->borrar_lab($id);
            break;
    }
}
?>