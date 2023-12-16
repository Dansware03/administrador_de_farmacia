<?php
include_once '../db/lote.php';
$lote = new Lote();
if (isset($_POST['funcion'])) {
    $funcion = $_POST['funcion'];
    switch ($funcion) {
        case 'crear':
            $id_producto = isset($_POST['id_producto']) ? $_POST['id_producto'] : '';
            $proveedor = isset($_POST['proveedor']) ? $_POST['proveedor'] : '';
            $stock = isset($_POST['stock']) ? $_POST['stock'] : '';
            $vencimiento = isset($_POST['vencimiento']) ? $_POST['vencimiento'] : '';
            $lote->crear($id_producto, $proveedor, $stock, $vencimiento);
        break;
        case 'buscar_lote':
            $lote->buscar();
            $json = array();
            $fecha_actual = new Datetime();
            foreach ($lote->objetos as $objeto) {
            $vencimiento = new Datetime($objeto->vencimiento);
            $diferencia = $vencimiento->diff($fecha_actual);
            $mes = $diferencia->m;
            $dia = $diferencia->d;
            $verificado = $diferencia->invert;
            if ($verificado==0) {
                $estado = 'danger';
                $mes = $mes*(-1);
                $dia = $dia*(-1);
            } else {
                if ($mes>3) {
                    $estado = 'light';
                }
                if ($mes<=3) {
                    $estado = 'warning';
                }
            }
                $json[] = array(
                    'id' => $objeto->id_lote,
                    'nombre' => $objeto->prod_nom,
                    'concentracion' => $objeto->concentracion,
                    'adicional' => $objeto->adicional,
                    'vencimiento' => $objeto->vencimiento,
                    'proveedor' => $objeto->pro_nom,
                    'stock' => $objeto->stock,
                    'nombre_laboratorio' => $objeto->lab_nom,
                    'tipo' => $objeto->tip_nom,
                    'nombre_presentacion' => $objeto->pre_nom,
                    'avatar' => '../libs/img/product/' . $objeto->logo,
                    'mes' => $mes,
                    'dia' => $dia,
                    'estado' => $estado,
                );
            }
            $jsonstring = json_encode($json);
            echo $jsonstring;
        break;
        case 'editar':
            $id_lote = isset($_POST['id']) ? $_POST['id'] : '';
            $stock = isset($_POST['stock']) ? $_POST['stock'] : '';
            $lote->editar($id_lote, $stock);
        break;
        case 'borrar_lote':
            $id = $_POST['id'];
            $lote->borrar_lote($id);
        break;
    }
}
?>