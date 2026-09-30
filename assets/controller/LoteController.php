<?php
include_once '../db/lote.php';
$lote = new Lote();
if (isset($_POST['funcion'])) {
    $funcion = $_POST['funcion'];
    switch ($funcion) {
        case 'crear':
            $id_producto = isset($_POST['id_producto']) ? $_POST['id_producto'] : '';
            $proveedor = isset($_POST['proveedor']) ? $_POST['proveedor'] : '';
            $cod_lote = isset($_POST['cod_lote']) ? $_POST['cod_lote'] : '';
            $stock = isset($_POST['stock']) ? $_POST['stock'] : '';
            $vencimiento = isset($_POST['vencimiento']) ? $_POST['vencimiento'] : '';
            $lote->crear($id_producto, $proveedor, $cod_lote, $stock, $vencimiento);
        break;
        case 'buscar_lote':
            $lote->buscar();
            $json = array();
            $fecha_actual = new Datetime('today');
            foreach ($lote->objetos as $objeto) {
                $esNoPerecedero = false;
                $vencimientoStr = trim($objeto->vencimiento ?? '');
                $categoriaId = isset($objeto->prod_tip_prod) ? (int)$objeto->prod_tip_prod : 0;

                // ponytail: Equipos de limpieza (2), papel e higiene (4) y herramientas (5) son no perecederos.
                // O aquellos con fecha vacía, 0000-00-00 o fecha de marcador no perecedero (>= 2030-01-01).
                if (in_array($categoriaId, [2, 4, 5]) || empty($vencimientoStr) || $vencimientoStr === '0000-00-00' || strpos($vencimientoStr, '2030') === 0) {
                    $esNoPerecedero = true;
                }

                if ($esNoPerecedero) {
                    $estado = 'no_perecedero';
                    $mes = 0;
                    $dia = 0;
                    $totalDias = 9999;
                } else {
                    $vencimiento = new Datetime($vencimientoStr);
                    // $fecha_actual->diff($vencimiento) da positivo si está en el futuro, negativo si ya venció
                    $diferencia = $fecha_actual->diff($vencimiento);
                    $totalDias = (int)$diferencia->format('%r%a');

                    if ($totalDias < 0) {
                        $estado = 'danger'; // Vencido
                        $mes = (int)ceil($totalDias / 30);
                        $dia = $totalDias;
                    } elseif ($totalDias <= 90) {
                        $estado = 'warning'; // Por vencer (menos de 90 días / 3 meses)
                        $mes = (int)floor($totalDias / 30);
                        $dia = $totalDias % 30;
                    } else {
                        $estado = 'light'; // En regla con más de 90 días
                        $mes = (int)floor($totalDias / 30);
                        $dia = $totalDias % 30;
                    }
                }

                $json[] = array(
                    'id' => $objeto->id_lote,
                    'nombre' => $objeto->prod_nom,
                    'concentracion' => $objeto->concentracion,
                    'adicional' => $objeto->adicional,
                    'vencimiento' => $esNoPerecedero ? '' : $objeto->vencimiento,
                    'proveedor' => $objeto->pro_nom,
                    'cod_lote' => $objeto->cod_lote,
                    'stock' => $objeto->stock,
                    'nombre_laboratorio' => $objeto->lab_nom,
                    'tipo' => $objeto->tip_nom,
                    'tipo_id' => $categoriaId,
                    'es_perecedero' => !$esNoPerecedero,
                    'dias_restantes' => $totalDias,
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