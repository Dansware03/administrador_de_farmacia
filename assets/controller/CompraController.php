<?php
include_once '../db/compra.php';
$compra = new Compra();
if (isset($_POST['funcion'])) {
    $funcion = $_POST['funcion'];
    switch ($funcion) {
        case 'registrar_compra':
            $total = $_POST['total'];
            $nombre = $_POST['nombre'];
            $ci = $_POST['ci'];
            $productos = json_decode($_POST['productos'], true);
            
            // Obtener el ID del vendedor (normalmente vendría de la sesión)
            $vendedor = 1; // Puedes modificar esto para obtener el ID del vendedor actual
            
            try {
                $compra->registrar_compra($nombre, $ci, $total, $vendedor, $productos);
                $mensaje = array(
                    'status' => 'success',
                    'message' => 'Compra realizada exitosamente'
                );
            } catch (Exception $e) {
                $mensaje = array(
                    'status' => 'error',
                    'message' => 'Error al procesar la compra: ' . $e->getMessage()
                );
            }
            
            echo json_encode($mensaje);
            break;
    }
}
?>