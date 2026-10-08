<?php
include_once '../db/producto.php';
$producto = new Producto();
if (isset($_POST['funcion'])) {
    $funcion = $_POST['funcion'];
    switch ($funcion) {
        case 'crear':
            $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
            $concentracion = isset($_POST['concentracion']) ? trim($_POST['concentracion']) : '';
            $adicional = isset($_POST['adicional']) ? trim($_POST['adicional']) : '';
            $prod_lab = isset($_POST['prod_lab']) ? $_POST['prod_lab'] : '';
            $prod_tip_prod = isset($_POST['prod_tip_prod']) ? $_POST['prod_tip_prod'] : '';
            $prod_present = isset($_POST['prod_present']) ? $_POST['prod_present'] : '';
            $id_unidad = isset($_POST['id_unidad']) ? $_POST['id_unidad'] : 1;
            $especificacion_talla = isset($_POST['especificacion_talla']) ? trim($_POST['especificacion_talla']) : '';
            $avatar = 'ProductDefault.png';

            // ponytail: Soporte opcional para registrar lote y stock inicial en la misma llamada
            $lote_data = null;
            if (!empty($_POST['cod_lote']) && !empty($_POST['stock_inicial']) && !empty($_POST['proveedor_lote'])) {
                $lote_data = [
                    'cod_lote' => trim($_POST['cod_lote']),
                    'stock' => intval($_POST['stock_inicial']),
                    'proveedor' => intval($_POST['proveedor_lote']),
                    'vencimiento' => !empty($_POST['vencimiento_lote']) ? trim($_POST['vencimiento_lote']) : '2035-12-31'
                ];
            }

            if (empty($nombre) || empty($prod_lab) || empty($prod_tip_prod) || empty($prod_present)) {
                echo 'Faltan datos obligatorios para el registro del insumo.';
            } else {
                $producto->crear($nombre, $concentracion, $adicional, $avatar, $prod_lab, $prod_tip_prod, $prod_present, $id_unidad, $especificacion_talla, $lote_data);
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
                $total = (int)($objeto['total_stock'] ?? 0);
                $json[] = array(
                    'id' => $objeto['id_producto'],
                    'nombre' => $objeto['nombre'],
                    'concentracion' => $objeto['concentracion'] ?? '',
                    'adicional' => $objeto['adicional'] ?? '',
                    'stock' => $total,
                    'nombre_laboratorio' => $objeto['nombre_laboratorio'],
                    'tipo' => $objeto['tipo'],
                    'nombre_presentacion' => $objeto['nombre_presentacion'],
                    'laboratorio_id' => $objeto['prod_lab'],
                    'tipo_id' => $objeto['prod_tip_prod'],
                    'presentacion_id' => $objeto['prod_present'],
                    'id_unidad' => $objeto['id_unidad'] ?? 1,
                    'unidad_medida' => $objeto['unidad_medida'] ?? 'Unidad',
                    'unidad_codigo' => $objeto['unidad_codigo'] ?? 'und',
                    'especificacion_talla' => $objeto['especificacion_talla'] ?? '',
                    'avatar' => '../libs/img/product/' . $objeto['avatar']
                );
            }
            $jsonstring = json_encode($json);
            echo $jsonstring;
        break;
        case 'editar':
            $id_edit_prod = isset($_POST['id_edit_prod']) ? $_POST['id_edit_prod'] : '';
            $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
            $concentracion = isset($_POST['concentracion']) ? trim($_POST['concentracion']) : '';
            $adicional = isset($_POST['adicional']) ? trim($_POST['adicional']) : '';
            $prod_lab = isset($_POST['prod_lab']) ? $_POST['prod_lab'] : '';
            $prod_tip_prod = isset($_POST['prod_tip_prod']) ? $_POST['prod_tip_prod'] : '';
            $prod_present = isset($_POST['prod_present']) ? $_POST['prod_present'] : '';
            $id_unidad = isset($_POST['id_unidad']) ? $_POST['id_unidad'] : 1;
            $especificacion_talla = isset($_POST['especificacion_talla']) ? trim($_POST['especificacion_talla']) : '';
            if (empty($nombre) || empty($prod_lab) || empty($prod_tip_prod) || empty($prod_present)) {
                echo 'Faltan datos obligatorios para actualizar el insumo.';
            } else {
                $producto->editar($id_edit_prod, $nombre, $concentracion, $adicional, $prod_lab, $prod_tip_prod, $prod_present, $id_unidad, $especificacion_talla);
            }
        break;
        case 'borrar_produts':
            $id = $_POST['id'];
            $producto->borrar_produts($id);
        break;
        case 'validar_existencia':
            // Recibe un array de IDs y retorna únicamente los que aún existen en la base de datos
            $ids = isset($_POST['ids']) ? json_decode($_POST['ids'], true) : [];
            $validos = $producto->validar_existentes($ids);
            echo json_encode(array_map('strval', $validos));
            break;
        case 'verificarStock':
            $error = 0;
            $productos = json_decode($_POST['productos']);
            foreach ($productos as $objeto) {
                $producto->obtener_stock($objeto->id);
                $total = 0;
                foreach ($producto->objetos as $obj) {
                    $total += $obj->total;
                }
                if (!($total >= $objeto->cantidad && $objeto->cantidad > 0)) {
                    $error++;
                }
            }
            echo $error;
            break;
    }
}
?>