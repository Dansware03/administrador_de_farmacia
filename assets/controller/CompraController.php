<?php
// ponytail: Representa la dotación y entrega institucional de insumos sin costo mercantil.
// Se preserva el identificador de clase y método por compatibilidad con las llamadas AJAX de carrito.js.
include_once '../db/compra.php';
$compra = new Compra();

if (isset($_POST['funcion'])) {
    $funcion = $_POST['funcion'];
    switch ($funcion) {
        case 'registrar_compra':
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $total = (isset($_POST['total']) && is_numeric($_POST['total'])) ? floatval($_POST['total']) : 0.0;
            $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
            $ci = isset($_POST['ci']) ? trim($_POST['ci']) : '';
            $id_area = !empty($_POST['id_area']) ? intval($_POST['id_area']) : null;
            $cargo_receptor = isset($_POST['cargo_receptor']) ? trim($_POST['cargo_receptor']) : '';
            $observacion = isset($_POST['observacion']) ? trim($_POST['observacion']) : '';
            $productos = isset($_POST['productos']) ? json_decode($_POST['productos'], true) : [];
            
            // Usuario que realiza la entrega/despacho desde almacén
            $vendedor = !empty($_SESSION['usuario']) ? intval($_SESSION['usuario']) : 1;
            
            try {
                if (empty($productos) || !is_array($productos)) {
                    throw new Exception("No hay insumos seleccionados para despachar.");
                }
                $compra->registrar_compra($nombre, $ci, $total, $vendedor, $productos, $id_area, $cargo_receptor, $observacion);
                $mensaje = array(
                    'status' => 'success',
                    'message' => 'Despacho y entrega de insumos registrado exitosamente.'
                );
            } catch (Exception $e) {
                $mensaje = array(
                    'status' => 'error',
                    'message' => 'Error al procesar el despacho: ' . $e->getMessage()
                );
            }
            
            echo json_encode($mensaje);
            break;
    }
}
?>