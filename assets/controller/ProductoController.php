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
            $avatar = 'ProductDefault.png';
            $prod_lab = isset($_POST['prod_lab']) ? $_POST['prod_lab'] : '';
            $prod_tip_prod = isset($_POST['prod_tip_prod']) ? $_POST['prod_tip_prod'] : '';
            $prod_present = isset($_POST['prod_present']) ? $_POST['prod_present'] : '';
            // Validar los datos aquí antes de enviarlos a la función crear
            $producto->crear($nombre, $concentracion, $adicional, $precio, $avatar, $prod_lab, $prod_tip_prod, $prod_present);
            break;
        case 'buscar_product':
            $producto->buscar(isset($_POST['consulta']) ? $_POST['consulta'] : '');
            $json = array();
            foreach ($producto->objetos as $objeto) {
                $json[] = array(
                    'id' => $objeto['id_producto'],
                    'nombre' => $objeto['nombre'],
                    'concentracion' => $objeto['concentracion'],
                    'adicional' => $objeto['adicional'],
                    'precio' => $objeto['precio'],
                    'stock' => 'stock',
                    'nombre_laboratorio' => $objeto['nombre_laboratorio'],
                    'tipo' => $objeto['tipo'],
                    'nombre_presentacion' => $objeto['nombre_presentacion'],
                    'avatar' =>'../libs/img/product/'.$objeto['avatar']
                );
            }
            $jsonstring = json_encode($json);
            echo $jsonstring;
            break;
    }
}
?>