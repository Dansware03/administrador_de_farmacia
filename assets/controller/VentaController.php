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
            
        // Nuevas funciones para editar ventas
        case 'obtener_venta':
            $id_venta = $_POST['id_venta'];
            try {
                $data = $venta->obtener_venta($id_venta);
                if ($data) {
                    $mensaje = array(
                        'status' => 'success',
                        'venta' => $data
                    );
                } else {
                    $mensaje = array(
                        'status' => 'error',
                        'message' => 'Venta no encontrada'
                    );
                }
            } catch (Exception $e) {
                $mensaje = array(
                    'status' => 'error',
                    'message' => 'Error al obtener la venta: ' . $e->getMessage()
                );
            }
            echo json_encode($mensaje);
            break;
            
        case 'obtener_stock_lote':
            $id_producto = $_POST['id_producto'];
            $id_lote = $_POST['id_lote'];
            try {
                $stock = $venta->obtener_stock_lote($id_producto, $id_lote);
                $mensaje = array(
                    'status' => 'success',
                    'stock' => $stock
                );
            } catch (Exception $e) {
                $mensaje = array(
                    'status' => 'error',
                    'message' => 'Error al obtener stock: ' . $e->getMessage()
                );
            }
            echo json_encode($mensaje);
            break;
            
        case 'actualizar_venta':
            $id_venta = $_POST['id_venta'];
            $cliente = $_POST['cliente'];
            $ci = $_POST['ci'];
            $total = $_POST['total'];
            $productos = $_POST['productos'];
            
            try {
                $venta->actualizar_venta($id_venta, $cliente, $ci, $total, $productos);
                $mensaje = array(
                    'status' => 'success',
                    'message' => 'Venta actualizada correctamente'
                );
            } catch (Exception $e) {
                $mensaje = array(
                    'status' => 'error',
                    'message' => 'Error al actualizar la venta: ' . $e->getMessage()
                );
            }
            echo json_encode($mensaje);
            break;
    }
}

// Para la impresión de recibos en PDF, usamos el método GET
if (isset($_GET['accion']) && $_GET['accion'] == 'obtener_venta_pdf') {
    if (isset($_GET['id'])) {
        $id_venta = $_GET['id'];
        $data = $venta->obtener_venta($id_venta);
        $detalles = $venta->ver_detalle_venta($id_venta);
        
        $resultado = array(
            'venta' => $data,
            'detalles' => $detalles
        );
        
        echo json_encode($resultado);
    }
}
?>