<?php
include_once '../db/Proveedor.php';
$proveedor = new Proveedor();
if (isset($_POST['funcion'])) {
    $funcion = $_POST['funcion'];
    switch ($funcion) {
        case 'crear':
            $nombre = isset($_POST['nombre']) ? $_POST['nombre'] : '';
            $telefono = isset($_POST['telefono']) ? $_POST['telefono'] : '';
            $correo = isset($_POST['correo']) ? $_POST['correo'] : '';
            $direccion = isset($_POST['direccion']) ? $_POST['direccion'] : '';
            $avatar = 'ProveedorDefault.png';
            $proveedor->crear($nombre, $telefono, $correo, $direccion, $avatar);
            break;
        case 'cambiar_avatar':
            $id = $_POST['id_logo_prod'];
            $avatar = $_POST['avatar'];
            if (
                ($_FILES['foto']['type'] == 'image/jpeg') ||
                ($_FILES['foto']['type'] == 'image/png') ||
                ($_FILES['foto']['type'] == 'image/jpg') ||
                ($_FILES['foto']['type'] == 'image/bmp')
            ) {
                $nombre = uniqid() . '-' . $_FILES['foto']['name'];
                $ruta = '../libs/img/proveedors/' . $nombre;
                move_uploaded_file($_FILES['foto']['tmp_name'], $ruta);
                $proveedor->cambiar_avatar($id, $nombre);
                if ($avatar != 'ProveedorDefault.png' && $avatar != '../libs/img/proveedors/ProveedorDefault.png' && file_exists($avatar)) {
                    unlink($avatar);
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
        case 'buscar_prov':
            $proveedor->buscar(isset($_POST['consulta']) ? $_POST['consulta'] : '');
            $json = array();
            foreach ($proveedor->objetos as $objeto) {
                $json[] = array(
                    'id' => $objeto['id_proveedor'],
                    'nombre' => $objeto['nombre'],
                    'telefono' => $objeto['telefono'],
                    'correo' => $objeto['correo'],
                    'direccion' => $objeto['direccion'],
                    'fecha' => 'Fecha',
                    'avatar' => '../libs/img/proveedors/' . $objeto['avatar']
                );
            }
            $jsonstring = json_encode($json);
            echo $jsonstring;
        break;
        case 'editar':
            $id = isset($_POST['id_editado']) ? $_POST['id_editado'] : '';
            $nombre = isset($_POST['nombre']) ? $_POST['nombre'] : '';
            $telefono = isset($_POST['telefono']) ? $_POST['telefono'] : '';
            $correo = isset($_POST['correo']) ? $_POST['correo'] : '';
            $direccion = isset($_POST['direccion']) ? $_POST['direccion'] : '';

            $proveedor->editar($id, $nombre, $telefono, $direccion, $correo);
        break;
        case 'borrar_prove':
            $id=$_POST['id'];
            $proveedor->borrar_prove($id);
        break;
    }
}
?>