<?php
include_once 'conexion.php';
class Compra {
    private $acceso;
    public function __construct() {
        $db = new Conexion();
        $this->acceso = $db->pdo;
    }
    
    public function registrar_compra($nombre, $ci, $total, $vendedor, $productos) {
        // Iniciar una transacción para garantizar la integridad de los datos
        $this->acceso->beginTransaction();
        
        try {
            // 1. Insertar en la tabla venta
            $fecha = date('Y-m-d H:i:s');
            $query = "INSERT INTO venta(fecha, cliente, ci, total, vendedor) VALUES (?,?,?,?,?)";
            $stmtVenta = $this->acceso->prepare($query);
            $stmtVenta->execute([$fecha, $nombre, $ci, $total, $vendedor]);
            
            // Obtener el ID de la venta recién insertada
            $id_venta = $this->acceso->lastInsertId();
            
            // 2. Insertar cada producto en la tabla venta_producto
            $query = "INSERT INTO venta_producto(cantidad, subtotal, producto_id_producto, venta_id_venta) VALUES (?,?,?,?)";
            $stmtProducto = $this->acceso->prepare($query);
            
            foreach ($productos as $producto) {
                $cantidad = $producto['cantidad'];
                $precio = $producto['precio'];
                $id_producto = $producto['id'];
                $subtotal = $cantidad * $precio;
                
                $stmtProducto->execute([$cantidad, $subtotal, $id_producto, $id_venta]);
                
                // 3. Actualizar el stock en lotes
                $this->actualizar_stock_por_lotes($id_producto, $cantidad, $id_venta);
            }
            
            // Confirmar la transacción
            $this->acceso->commit();
            return true;
            
        } catch (Exception $error) {
            // Si hay algún error, revertir todas las operaciones
            $this->acceso->rollBack();
            echo $error->getMessage();
            return false;
        }
    }
    
    // Método para actualizar el stock por lotes, priorizando los que vencen primero
    public function actualizar_stock_por_lotes($id_producto, $cantidad_requerida, $id_venta) {
        // Obtener lotes disponibles del producto ordenados por fecha de vencimiento (ascendente)
        $query = "SELECT * FROM lote 
                 WHERE id_lote_prod = ? AND stock > 0 
                 ORDER BY vencimiento ASC";
        $stmt = $this->acceso->prepare($query);
        $stmt->execute([$id_producto]);
        $lotes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $cantidad_pendiente = $cantidad_requerida;
        
        foreach ($lotes as $lote) {
            // Si ya se satisfizo la cantidad requerida, salir del ciclo
            if ($cantidad_pendiente <= 0) {
                break;
            }
            
            $id_lote = $lote['id_lote'];
            $stock_lote = $lote['stock'];
            $vencimiento = $lote['vencimiento'];
            $lote_id_prov = $lote['lote_id_prov'];
            
            // Determinar cuánto se puede tomar de este lote
            $cantidad_a_tomar = min($stock_lote, $cantidad_pendiente);
            
            // Actualizar el stock del lote
            $query_update = "UPDATE lote SET stock = stock - ? WHERE id_lote = ?";
            $stmt_update = $this->acceso->prepare($query_update);
            $stmt_update->execute([$cantidad_a_tomar, $id_lote]);
            
            // Registrar en detalle_venta
            $query_detalle = "INSERT INTO detalle_venta(det_cantidad, det_vencimiento, id_det_lote, id_det_prod, lote_id_prov, id_det_venta) 
                             VALUES (?,?,?,?,?,?)";
            $stmt_detalle = $this->acceso->prepare($query_detalle);
            $stmt_detalle->execute([
                $cantidad_a_tomar,
                $vencimiento,
                $id_lote,
                $id_producto,
                $lote_id_prov,
                $id_venta
            ]);
            
            // Reducir la cantidad pendiente
            $cantidad_pendiente -= $cantidad_a_tomar;
        }
        
        // Verificar si se pudo satisfacer toda la cantidad requerida
        if ($cantidad_pendiente > 0) {
            // No había suficiente stock en los lotes
            throw new Exception("No hay suficiente stock para el producto ID: " . $id_producto);
        }
    }
}
?>