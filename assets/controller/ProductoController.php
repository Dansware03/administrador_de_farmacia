<?php
include_once '../db/producto.php';
$producto = new Producto();
if (isset($_POST['funcion'])) {
    $funcion = $_POST['funcion'];
    switch ($funcion) {
        case 'crear':
            $nombre = isset($_POST['nombre']) ? $_POST['nombre'] : '';
            $concentracion = isset($_POST['concentracion']) ? $_POST['concentracion'] : '';
            $adicional = isset($_POST['adicional']) ? $_POST['adicional'] : '';
            $precio = isset($_POST['precio']) ? $_POST['precio'] : '';
            $prod_lab = isset($_POST['prod_lab']) ? $_POST['prod_lab'] : '';
            $prod_tip_prod = isset($_POST['prod_tip_prod']) ? $_POST['prod_tip_prod'] : '';
            $prod_present = isset($_POST['prod_present']) ? $_POST['prod_present'] : '';
            $avatar = 'ProductDefault.png';
            if (!is_numeric($precio) || empty(trim($nombre)) || empty(trim($concentracion)) || empty(trim($prod_lab)) || empty(trim($prod_tip_prod)) || empty(trim($prod_present))) {
                echo 'Faltan datos obligatorios o el precio no es válido.';
            } else {
                $producto->crear($nombre, $concentracion, $adicional, $precio, $avatar, $prod_lab, $prod_tip_prod, $prod_present);
            }
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
                    $ruta = '../libs/img/product/' . $nombre;
                    move_uploaded_file($_FILES['foto']['tmp_name'], $ruta);
                    $producto->cambiar_avatar($id, $nombre);
                    if ($avatar != 'ProductDefault.png' && $avatar != '../libs/img/product/ProductDefault.png' && file_exists($avatar)) {
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
        case 'buscar_product':
            $producto->buscar(isset($_POST['consulta']) ? $_POST['consulta'] : '');
            $json = array();
            foreach ($producto->objetos as $objeto) {
                $producto->obtener_stock($objeto['id_producto']);
                foreach ($producto->objetos as $obj) {
                    $total = $obj->total;
                }
                $json[] = array(
                    'id' => $objeto['id_producto'],
                    'nombre' => $objeto['nombre'],
                    'concentracion' => $objeto['concentracion'],
                    'adicional' => $objeto['adicional'],
                    'precio' => $objeto['precio'],
                    'stock' => $total,
                    'nombre_laboratorio' => $objeto['nombre_laboratorio'],
                    'tipo' => $objeto['tipo'],
                    'nombre_presentacion' => $objeto['nombre_presentacion'],
                    'laboratorio_id' => $objeto['prod_lab'],
                    'tipo_id' => $objeto['prod_tip_prod'],
                    'presentacion_id' => $objeto['prod_present'],
                    'avatar' => '../libs/img/product/' . $objeto['avatar']
                );
            }
            $jsonstring = json_encode($json);
            echo $jsonstring;
        break;
        case 'editar':
            $id_edit_prod = isset($_POST['id_edit_prod']) ? $_POST['id_edit_prod'] : '';
            $nombre = isset($_POST['nombre']) ? $_POST['nombre'] : '';
            $concentracion = isset($_POST['concentracion']) ? $_POST['concentracion'] : '';
            $adicional = isset($_POST['adicional']) ? $_POST['adicional'] : '';
            $precio = isset($_POST['precio']) ? $_POST['precio'] : '';
            $prod_lab = isset($_POST['prod_lab']) ? $_POST['prod_lab'] : '';
            $prod_tip_prod = isset($_POST['prod_tip_prod']) ? $_POST['prod_tip_prod'] : '';
            $prod_present = isset($_POST['prod_present']) ? $_POST['prod_present'] : '';
            if (!is_numeric($precio) || empty(trim($nombre)) || empty(trim($concentracion)) || empty(trim($prod_lab)) || empty(trim($prod_tip_prod)) || empty(trim($prod_present))) {
                echo 'Faltan datos obligatorios o el precio no es válido.';
            } else {
                $producto->editar($id_edit_prod, $nombre, $concentracion, $adicional, $precio, $prod_lab, $prod_tip_prod, $prod_present);
            }
        break;
        case 'borrar_produts':
            $id = $_POST['id'];
            $producto->borrar_produts($id);
        break;
    }
}
?>