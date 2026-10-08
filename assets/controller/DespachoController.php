<?php
// ponytail: Controlador unificado para transacciones de despacho institucional y actas de entrega.
include_once '../db/despacho.php';
$despacho = new Despacho();

$funcion = $_POST['funcion'] ?? $_GET['funcion'] ?? null;

if ($funcion) {
    switch ($funcion) {
        case 'registrar_despacho':
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $receptor = isset($_POST['nombre']) ? trim($_POST['nombre']) : (isset($_POST['receptor']) ? trim($_POST['receptor']) : '');
            $ci_receptor = isset($_POST['ci']) ? trim($_POST['ci']) : (isset($_POST['ci_receptor']) ? trim($_POST['ci_receptor']) : '');
            $id_area = !empty($_POST['id_area']) ? intval($_POST['id_area']) : null;
            $cargo_receptor = isset($_POST['cargo_receptor']) ? trim($_POST['cargo_receptor']) : '';
            $observacion = isset($_POST['observacion']) ? trim($_POST['observacion']) : '';
            $productos = isset($_POST['productos']) ? json_decode($_POST['productos'], true) : [];

            $responsable = !empty($_SESSION['usuario']) ? intval($_SESSION['usuario']) : 1;

            try {
                if (empty($productos) || !is_array($productos)) {
                    throw new Exception("No hay insumos seleccionados para despachar.");
                }
                $id = $despacho->registrar_despacho($receptor, $ci_receptor, $responsable, $productos, $id_area, $cargo_receptor, $observacion);
                echo json_encode([
                    'status' => 'success',
                    'id_despacho' => $id,
                    'message' => 'Despacho registrado exitosamente.'
                ]);
            } catch (Exception $e) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Error al procesar el despacho: ' . $e->getMessage()
                ]);
            }
            break;

        case 'listar_despachos':
            $fecha_inicio = $_GET['fecha_inicio'] ?? null;
            $fecha_fin = $_GET['fecha_fin'] ?? null;

            if ($fecha_inicio && $fecha_fin) {
                $resultado = $despacho->listar_despachos_por_fechas($fecha_inicio, $fecha_fin);
            } else {
                $resultado = $despacho->listar_despachos();
            }
            echo json_encode($resultado);
            break;

        case 'ver_detalle_despacho':
            $id_despacho = $_POST['id_despacho'] ?? $_GET['id_despacho'] ?? null;
            $resultado = $despacho->ver_detalle_despacho($id_despacho);
            echo json_encode($resultado);
            break;

        case 'revertir_despacho':
            $id_despacho = $_POST['id_despacho'] ?? null;
            try {
                $despacho->revertir_despacho($id_despacho);
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Despacho anulado correctamente. Los insumos han sido reintegrados al inventario.'
                ]);
            } catch (Exception $e) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Error al anular el despacho: ' . $e->getMessage()
                ]);
            }
            break;

        case 'obtener_despacho':
            $id_despacho = $_POST['id_despacho'] ?? $_GET['id_despacho'] ?? null;
            try {
                $data = $despacho->obtener_despacho($id_despacho);
                if ($data) {
                    echo json_encode([
                        'status' => 'success',
                        'despacho' => $data
                    ]);
                } else {
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'Despacho no encontrado'
                    ]);
                }
            } catch (Exception $e) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Error al obtener despacho: ' . $e->getMessage()
                ]);
            }
            break;

        case 'obtener_stock_lote':
            $id_producto = $_POST['id_producto'] ?? null;
            $id_lote = $_POST['id_lote'] ?? null;
            try {
                $stock = $despacho->obtener_stock_lote($id_producto, $id_lote);
                echo json_encode([
                    'status' => 'success',
                    'stock' => $stock
                ]);
            } catch (Exception $e) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Error al obtener stock: ' . $e->getMessage()
                ]);
            }
            break;

        case 'actualizar_despacho':
            $id_despacho = $_POST['id_despacho'] ?? null;
            $receptor = $_POST['receptor'] ?? $_POST['cliente'] ?? '';
            $ci_receptor = $_POST['ci_receptor'] ?? $_POST['ci'] ?? '';
            $productos = $_POST['productos'] ?? '[]';
            $id_area = !empty($_POST['id_area']) ? $_POST['id_area'] : null;
            $cargo_receptor = $_POST['cargo_receptor'] ?? '';
            $observacion = $_POST['observacion'] ?? '';

            try {
                $despacho->actualizar_despacho($id_despacho, $receptor, $ci_receptor, $productos, $id_area, $cargo_receptor, $observacion);
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Acta de despacho actualizada correctamente'
                ]);
            } catch (Exception $e) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Error al actualizar el despacho: ' . $e->getMessage()
                ]);
            }
            break;
    }
}

// Soporte para PDF de actas institucionales
if (isset($_GET['accion']) && $_GET['accion'] === 'obtener_despacho_pdf') {
    $id_despacho = $_GET['id'] ?? null;
    if ($id_despacho) {
        $data = $despacho->obtener_despacho($id_despacho);
        $detalles = $despacho->ver_detalle_despacho($id_despacho);
        echo json_encode([
            'despacho' => $data,
            'detalles' => $detalles
        ]);
    }
}
?>
