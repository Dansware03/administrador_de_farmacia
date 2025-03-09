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
}
?>
