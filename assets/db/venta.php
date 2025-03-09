<?php
include_once 'conexion.php';

class Venta {
    private $acceso;
    
    public function __construct() {
        $db = new Conexion();
        $this->acceso = $db->pdo;
    }
    
    public function listar_ventas() {
        $sql = "SELECT v.*, (u.nombre_us || ' ' || u.apellidos_us) as vendedor 
                FROM venta v 
                JOIN usuario u ON v.vendedor = u.id_usuario 
                ORDER BY v.id_venta DESC";
        $query = $this->acceso->prepare($sql);
        $query->execute();
        $ventas = $query->fetchAll(PDO::FETCH_ASSOC);
        return $ventas;
    }
    
    public function listar_ventas_por_fechas($fecha_inicio, $fecha_fin) {
        $sql = "SELECT v.*, (u.nombre_us || ' ' || u.apellidos_us) as vendedor 
                FROM venta v 
                JOIN usuario u ON v.vendedor = u.id_usuario 
                WHERE DATE(v.fecha) BETWEEN :fecha_inicio AND :fecha_fin 
                ORDER BY v.id_venta DESC";
        $query = $this->acceso->prepare($sql);
        $query->bindParam(':fecha_inicio', $fecha_inicio);
        $query->bindParam(':fecha_fin', $fecha_fin);
        $query->execute();
        $ventas = $query->fetchAll(PDO::FETCH_ASSOC);
        return $ventas;
    }
    
    public function ver_detalle_venta($id_venta) {
        $sql = "SELECT vp.*, 
                       p.nombre as producto, 
                       p.precio as precio, 
                       l.cod_lote as lote, 
                       l.vencimiento 
                FROM venta_producto vp 
                JOIN producto p ON vp.producto_id_producto = p.id_producto 
                LEFT JOIN detalle_venta dv ON dv.id_det_venta = vp.venta_id_venta 
                                          AND dv.id_det_prod = p.id_producto 
                LEFT JOIN lote l ON dv.id_det_lote = l.id_lote 
                WHERE vp.venta_id_venta = :id_venta";
        $query = $this->acceso->prepare($sql);
        $query->bindParam(':id_venta', $id_venta);
        $query->execute();
        $detalles = $query->fetchAll(PDO::FETCH_ASSOC);
        return $detalles;
    }    
    
    public function revertir_venta($id_venta) {
        // Iniciar transacción
        $this->acceso->beginTransaction();
        
        try {
            // Obtener detalles de la venta
            $sql = "SELECT * FROM detalle_venta WHERE id_det_venta = :id_venta";
            $query = $this->acceso->prepare($sql);
            $query->bindParam(':id_venta', $id_venta);
            $query->execute();
            $detalles = $query->fetchAll(PDO::FETCH_ASSOC);
            
            // Devolver productos al inventario
            foreach ($detalles as $detalle) {
                $id_lote = $detalle['id_det_lote'];
                $cantidad = $detalle['det_cantidad'];
                
                // Actualizar stock del lote
                $sql = "UPDATE lote SET stock = stock + :cantidad WHERE id_lote = :id_lote";
                $query = $this->acceso->prepare($sql);
                $query->bindParam(':cantidad', $cantidad);
                $query->bindParam(':id_lote', $id_lote);
                $query->execute();
            }
            
            // Eliminar detalles de venta
            $sql = "DELETE FROM detalle_venta WHERE id_det_venta = :id_venta";
            $query = $this->acceso->prepare($sql);
            $query->bindParam(':id_venta', $id_venta);
            $query->execute();
            
            // Eliminar productos de venta
            $sql = "DELETE FROM venta_producto WHERE venta_id_venta = :id_venta";
            $query = $this->acceso->prepare($sql);
            $query->bindParam(':id_venta', $id_venta);
            $query->execute();
            
            // Eliminar la venta
            $sql = "DELETE FROM venta WHERE id_venta = :id_venta";
            $query = $this->acceso->prepare($sql);
            $query->bindParam(':id_venta', $id_venta);
            $query->execute();
            
            // Confirmar transacción
            $this->acceso->commit();
            return true;
            
        } catch (Exception $e) {
            // Revertir transacción en caso de error
            $this->acceso->rollBack();
            throw $e;
        }
    }
    
    // Nueva función para obtener datos de una venta específica
    public function obtener_venta($id_venta) {
        $sql = "SELECT v.*, (u.nombre_us || ' ' || u.apellidos_us) as vendedor 
                FROM venta v 
                JOIN usuario u ON v.vendedor = u.id_usuario 
                WHERE v.id_venta = :id_venta";
        $query = $this->acceso->prepare($sql);
        $query->bindParam(':id_venta', $id_venta);
        $query->execute();
        $venta = $query->fetch(PDO::FETCH_ASSOC);
        return $venta;
    }
    
    // Nueva función para obtener el stock disponible de un lote
    public function obtener_stock_lote($id_producto, $id_lote) {
        $sql = "SELECT stock FROM lote WHERE id_lote_prod = :id_producto AND id_lote = :id_lote";
        $query = $this->acceso->prepare($sql);
        $query->bindParam(':id_producto', $id_producto);
        $query->bindParam(':id_lote', $id_lote);
        $query->execute();
        $resultado = $query->fetch(PDO::FETCH_ASSOC);
        return $resultado ? $resultado['stock'] : 0;
    }
    
    // Nueva función para actualizar una venta existente
    public function actualizar_venta($id_venta, $cliente, $ci, $total, $productos) {
        // Iniciar transacción
        $this->acceso->beginTransaction();
        
        try {
            // Obtener detalles actuales de la venta para restaurar stock
            $detalles_actuales = $this->ver_detalle_venta($id_venta);
            
            // Restaurar stock de productos actuales
            foreach ($detalles_actuales as $detalle) {
                // Obtener información del lote desde detalle_venta
                $sql = "SELECT id_det_lote FROM detalle_venta 
                        WHERE id_det_venta = :id_venta 
                        AND id_det_prod = :id_producto";
                $query = $this->acceso->prepare($sql);
                $query->bindParam(':id_venta', $id_venta);
                $query->bindParam(':id_producto', $detalle['producto_id_producto']);
                $query->execute();
                $lote_info = $query->fetch(PDO::FETCH_ASSOC);
                
                if ($lote_info) {
                    $id_lote = $lote_info['id_det_lote'];
                    $cantidad = $detalle['cantidad'];
                    
                    // Restaurar stock al lote
                    $sql = "UPDATE lote SET stock = stock + :cantidad 
                            WHERE id_lote = :id_lote";
                    $query = $this->acceso->prepare($sql);
                    $query->bindParam(':cantidad', $cantidad);
                    $query->bindParam(':id_lote', $id_lote);
                    $query->execute();
                }
            }
            
            // Eliminar detalles existentes
            $sql = "DELETE FROM detalle_venta WHERE id_det_venta = :id_venta";
            $query = $this->acceso->prepare($sql);
            $query->bindParam(':id_venta', $id_venta);
            $query->execute();
            
            // Eliminar productos de venta
            $sql = "DELETE FROM venta_producto WHERE venta_id_venta = :id_venta";
            $query = $this->acceso->prepare($sql);
            $query->bindParam(':id_venta', $id_venta);
            $query->execute();
            
            // Actualizar datos de la venta
            $sql = "UPDATE venta SET 
                    cliente = :cliente, 
                    ci = :ci, 
                    total = :total 
                    WHERE id_venta = :id_venta";
            $query = $this->acceso->prepare($sql);
            $query->bindParam(':cliente', $cliente);
            $query->bindParam(':ci', $ci);
            $query->bindParam(':total', $total);
            $query->bindParam(':id_venta', $id_venta);
            $query->execute();
            
            // Insertar nuevos detalles de venta y reducir stock
            $productos_array = json_decode($productos, true);
            
            foreach ($productos_array as $producto) {
                $id_producto = $producto['producto_id_producto'];
                $id_lote = $producto['id_det_lote'] ?? null;
                $cantidad = $producto['cantidad'];
                $subtotal = $producto['precio'] * $cantidad;
                
                // Insertar en venta_producto
                $sql = "INSERT INTO venta_producto (cantidad, subtotal, producto_id_producto, venta_id_venta) 
                        VALUES (:cantidad, :subtotal, :id_producto, :id_venta)";
                $query = $this->acceso->prepare($sql);
                $query->bindParam(':cantidad', $cantidad);
                $query->bindParam(':subtotal', $subtotal);
                $query->bindParam(':id_producto', $id_producto);
                $query->bindParam(':id_venta', $id_venta);
                $query->execute();
                
                // Si hay un lote asociado, insertar en detalle_venta y actualizar stock
                if ($id_lote) {
                    // Obtener vencimiento del lote
                    $sql = "SELECT vencimiento FROM lote WHERE id_lote = :id_lote";
                    $query = $this->acceso->prepare($sql);
                    $query->bindParam(':id_lote', $id_lote);
                    $query->execute();
                    $lote_data = $query->fetch(PDO::FETCH_ASSOC);
                    $vencimiento = $lote_data['vencimiento'];
                    
                    // Insertar en detalle_venta
                    $sql = "INSERT INTO detalle_venta (det_cantidad, det_vencimiento, id_det_lote, id_det_prod, lote_id_prov, id_det_venta) 
                            VALUES (:cantidad, :vencimiento, :id_lote, :id_producto, 
                            (SELECT lote_id_prov FROM lote WHERE id_lote = :id_lote2), :id_venta)";
                    $query = $this->acceso->prepare($sql);
                    $query->bindParam(':cantidad', $cantidad);
                    $query->bindParam(':vencimiento', $vencimiento);
                    $query->bindParam(':id_lote', $id_lote);
                    $query->bindParam(':id_producto', $id_producto);
                    $query->bindParam(':id_lote2', $id_lote);
                    $query->bindParam(':id_venta', $id_venta);
                    $query->execute();
                    
                    // Reducir stock del lote
                    $sql = "UPDATE lote SET stock = stock - :cantidad 
                            WHERE id_lote = :id_lote";
                    $query = $this->acceso->prepare($sql);
                    $query->bindParam(':cantidad', $cantidad);
                    $query->bindParam(':id_lote', $id_lote);
                    $query->execute();
                }
            }
            
            // Confirmar transacción
            $this->acceso->commit();
            return true;
            
        } catch (Exception $e) {
            // Revertir transacción en caso de error
            $this->acceso->rollBack();
            throw $e;
        }
    }
}
?>