<?php
include_once '../db/venta.php';
$venta = new Venta();

if (isset($_POST['funcion'])) {
    $funcion = $_POST['funcion'];
    
    switch ($funcion) {
        case 'listar_ventas':
            $json = array();
            
            // Verificar si hay filtros de fecha
            $fecha_inicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : null;
            $fecha_fin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : null;
            
            if ($fecha_inicio && $fecha_fin) {
                $json = $venta->listar_ventas_por_fechas($fecha_inicio, $fecha_fin);
            } else {
                $json = $venta->listar_ventas();
            }
            
            echo json_encode($json);
            break;
            
        case 'ver_detalle_venta':
            $id_venta = $_POST['id_venta'];
            $json = $venta->ver_detalle_venta($id_venta);
            echo json_encode($json);
            break;
            
        case 'revertir_venta':
            $id_venta = $_POST['id_venta'];
            try {
                $venta->revertir_venta($id_venta);
                $mensaje = array(
                    'status' => 'success',
                    'message' => 'Venta anulada correctamente. Los productos han sido devueltos al inventario.'
                );
            } catch (Exception $e) {
                $mensaje = array(
                    'status' => 'error',
                    'message' => 'Error al anular la venta: ' . $e->getMessage()
                );
            }
            echo json_encode($mensaje);
            break;
    }
}
?>