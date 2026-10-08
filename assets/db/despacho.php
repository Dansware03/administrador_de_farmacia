<?php
// ponytail: Modelo unificado para la gestión institucional de despachos y actas de entrega de insumos.
include_once 'conexion.php';

class Despacho {
    private $acceso;

    public function __construct() {
        $db = new Conexion();
        $this->acceso = $db->pdo;
    }

    /**
     * Registrar un nuevo despacho y deducir existencias según vencimiento (FEFO)
     */
    public function registrar_despacho($receptor, $ci_receptor, $responsable, $productos, $id_area = null, $cargo_receptor = '', $observacion = '') {
        $this->acceso->beginTransaction();

        try {
            $fecha = date('Y-m-d H:i:s');
            $query = "INSERT INTO despacho(fecha, receptor, ci_receptor, responsable, id_area, cargo_receptor, observacion) VALUES (?,?,?,?,?,?,?)";
            $stmtDespacho = $this->acceso->prepare($query);
            $stmtDespacho->execute([$fecha, $receptor, $ci_receptor, $responsable, $id_area, $cargo_receptor, $observacion]);

            $id_despacho = $this->acceso->lastInsertId();

            $queryInsumo = "INSERT INTO despacho_insumo(cantidad, producto_id_producto, despacho_id_despacho) VALUES (?,?,?)";
            $stmtInsumo = $this->acceso->prepare($queryInsumo);

            foreach ($productos as $producto) {
                $cantidad = (int)($producto['cantidad'] ?? 1);
                $id_producto = (int)$producto['id'];

                $stmtInsumo->execute([$cantidad, $id_producto, $id_despacho]);
                $this->actualizar_stock_por_lotes($id_producto, $cantidad, $id_despacho);
            }

            $this->acceso->commit();
            return $id_despacho;

        } catch (Exception $error) {
            $this->acceso->rollBack();
            throw $error;
        }
    }

    /**
     * Deducir existencias por lote priorizando fechas de vencimiento próximas
     */
    public function actualizar_stock_por_lotes($id_producto, $cantidad_requerida, $id_despacho) {
        $query = "SELECT * FROM lote 
                  WHERE id_lote_prod = ? AND stock > 0 
                  ORDER BY vencimiento ASC";
        $stmt = $this->acceso->prepare($query);
        $stmt->execute([$id_producto]);
        $lotes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $cantidad_pendiente = $cantidad_requerida;

        foreach ($lotes as $lote) {
            if ($cantidad_pendiente <= 0) {
                break;
            }

            $id_lote = $lote['id_lote'];
            $stock_lote = $lote['stock'];
            $vencimiento = $lote['vencimiento'];
            $lote_id_prov = $lote['lote_id_prov'];

            $cantidad_a_tomar = min($stock_lote, $cantidad_pendiente);

            $stmt_update = $this->acceso->prepare("UPDATE lote SET stock = stock - ? WHERE id_lote = ?");
            $stmt_update->execute([$cantidad_a_tomar, $id_lote]);

            $stmt_detalle = $this->acceso->prepare("INSERT INTO detalle_despacho(det_cantidad, det_vencimiento, id_det_lote, id_det_prod, lote_id_prov, id_det_despacho) VALUES (?,?,?,?,?,?)");
            $stmt_detalle->execute([
                $cantidad_a_tomar,
                $vencimiento,
                $id_lote,
                $id_producto,
                $lote_id_prov,
                $id_despacho
            ]);

            $cantidad_pendiente -= $cantidad_a_tomar;
        }

        if ($cantidad_pendiente > 0) {
            throw new Exception("Stock insuficiente en lotes para el insumo ID: " . $id_producto);
        }
    }

    /**
     * Listar todos los despachos con responsable y área asignada
     */
    public function listar_despachos() {
        $sql = "SELECT d.*, (u.nombre_us || ' ' || u.apellidos_us) as responsable_nombre,
                       COALESCE(a.nombre_area, 'Área General') as area
                FROM despacho d 
                JOIN usuario u ON d.responsable = u.id_usuario 
                LEFT JOIN area_servicio a ON d.id_area = a.id_area
                ORDER BY d.id_despacho DESC";
        $query = $this->acceso->prepare($sql);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Filtrar despachos por rango de fechas
     */
    public function listar_despachos_por_fechas($fecha_inicio, $fecha_fin) {
        $sql = "SELECT d.*, (u.nombre_us || ' ' || u.apellidos_us) as responsable_nombre,
                       COALESCE(a.nombre_area, 'Área General') as area
                FROM despacho d 
                JOIN usuario u ON d.responsable = u.id_usuario 
                LEFT JOIN area_servicio a ON d.id_area = a.id_area
                WHERE DATE(d.fecha) BETWEEN :fecha_inicio AND :fecha_fin 
                ORDER BY d.id_despacho DESC";
        $query = $this->acceso->prepare($sql);
        $query->bindParam(':fecha_inicio', $fecha_inicio);
        $query->bindParam(':fecha_fin', $fecha_fin);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener el detalle de insumos despachados por acta
     */
    public function ver_detalle_despacho($id_despacho) {
        $sql = "SELECT di.*, 
                       p.nombre as producto, 
                       p.especificacion_talla,
                       COALESCE(um.nombre, 'Unidad') as unidad_medida,
                       COALESCE(um.codigo, 'und') as unidad_codigo,
                       l.cod_lote as lote, 
                       l.vencimiento 
                FROM despacho_insumo di 
                JOIN producto p ON di.producto_id_producto = p.id_producto 
                LEFT JOIN unidad_medida um ON p.id_unidad = um.id_unidad
                LEFT JOIN detalle_despacho dd ON dd.id_det_despacho = di.despacho_id_despacho 
                                             AND dd.id_det_prod = p.id_producto 
                LEFT JOIN lote l ON dd.id_det_lote = l.id_lote 
                WHERE di.despacho_id_despacho = :id_despacho";
        $query = $this->acceso->prepare($sql);
        $query->bindParam(':id_despacho', $id_despacho);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener cabecera de un despacho específico
     */
    public function obtener_despacho($id_despacho) {
        $sql = "SELECT d.*, (u.nombre_us || ' ' || u.apellidos_us) as responsable_nombre,
                       a.nombre_area
                FROM despacho d 
                JOIN usuario u ON d.responsable = u.id_usuario 
                LEFT JOIN area_servicio a ON d.id_area = a.id_area
                WHERE d.id_despacho = :id_despacho";
        $query = $this->acceso->prepare($sql);
        $query->bindParam(':id_despacho', $id_despacho);
        $query->execute();
        return $query->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Anular despacho y reponer existencias en lotes
     */
    public function revertir_despacho($id_despacho) {
        $this->acceso->beginTransaction();

        try {
            $stmt = $this->acceso->prepare("SELECT * FROM detalle_despacho WHERE id_det_despacho = :id_despacho");
            $stmt->bindParam(':id_despacho', $id_despacho);
            $stmt->execute();
            $detalles = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($detalles as $detalle) {
                $stmtStock = $this->acceso->prepare("UPDATE lote SET stock = stock + :cantidad WHERE id_lote = :id_lote");
                $stmtStock->bindParam(':cantidad', $detalle['det_cantidad']);
                $stmtStock->bindParam(':id_lote', $detalle['id_det_lote']);
                $stmtStock->execute();
            }

            $stmtDelDet = $this->acceso->prepare("DELETE FROM detalle_despacho WHERE id_det_despacho = :id_despacho");
            $stmtDelDet->bindParam(':id_despacho', $id_despacho);
            $stmtDelDet->execute();

            $stmtDelIns = $this->acceso->prepare("DELETE FROM despacho_insumo WHERE despacho_id_despacho = :id_despacho");
            $stmtDelIns->bindParam(':id_despacho', $id_despacho);
            $stmtDelIns->execute();

            $stmtDelDes = $this->acceso->prepare("DELETE FROM despacho WHERE id_despacho = :id_despacho");
            $stmtDelDes->bindParam(':id_despacho', $id_despacho);
            $stmtDelDes->execute();

            $this->acceso->commit();
            return true;

        } catch (Exception $e) {
            $this->acceso->rollBack();
            throw $e;
        }
    }

    /**
     * Consultar stock disponible de un lote específico
     */
    public function obtener_stock_lote($id_producto, $id_lote) {
        $sql = "SELECT stock FROM lote WHERE id_lote_prod = :id_producto AND id_lote = :id_lote";
        $query = $this->acceso->prepare($sql);
        $query->bindParam(':id_producto', $id_producto);
        $query->bindParam(':id_lote', $id_lote);
        $query->execute();
        $resultado = $query->fetch(PDO::FETCH_ASSOC);
        return $resultado ? (int)$resultado['stock'] : 0;
    }

    /**
     * Actualizar datos del acta y recalcular inventario
     */
    public function actualizar_despacho($id_despacho, $receptor, $ci_receptor, $productos, $id_area = null, $cargo_receptor = '', $observacion = '') {
        $this->acceso->beginTransaction();

        try {
            // Reintegrar stock previo
            $stmtDetalles = $this->acceso->prepare("SELECT * FROM detalle_despacho WHERE id_det_despacho = :id_despacho");
            $stmtDetalles->bindParam(':id_despacho', $id_despacho);
            $stmtDetalles->execute();
            $detalles_previos = $stmtDetalles->fetchAll(PDO::FETCH_ASSOC);

            foreach ($detalles_previos as $dp) {
                $stmtR = $this->acceso->prepare("UPDATE lote SET stock = stock + :cantidad WHERE id_lote = :id_lote");
                $stmtR->execute([':cantidad' => $dp['det_cantidad'], ':id_lote' => $dp['id_det_lote']]);
            }

            // Limpiar líneas anteriores
            $stmtDelDet = $this->acceso->prepare("DELETE FROM detalle_despacho WHERE id_det_despacho = :id_despacho");
            $stmtDelDet->execute([':id_despacho' => $id_despacho]);

            $stmtDelIns = $this->acceso->prepare("DELETE FROM despacho_insumo WHERE despacho_id_despacho = :id_despacho");
            $stmtDelIns->execute([':id_despacho' => $id_despacho]);

            // Actualizar cabecera
            $stmtUp = $this->acceso->prepare("UPDATE despacho SET 
                receptor = :receptor, 
                ci_receptor = :ci_receptor, 
                id_area = :id_area,
                cargo_receptor = :cargo_receptor,
                observacion = :observacion
                WHERE id_despacho = :id_despacho");
            $stmtUp->execute([
                ':receptor' => $receptor,
                ':ci_receptor' => $ci_receptor,
                ':id_area' => $id_area,
                ':cargo_receptor' => $cargo_receptor,
                ':observacion' => $observacion,
                ':id_despacho' => $id_despacho
            ]);

            // Insertar nuevas líneas
            $productos_array = is_array($productos) ? $productos : json_decode($productos, true);

            foreach ($productos_array as $p) {
                $id_producto = (int)($p['producto_id_producto'] ?? $p['id']);
                $id_lote = !empty($p['id_det_lote']) ? (int)$p['id_det_lote'] : null;
                $cantidad = (int)($p['cantidad'] ?? 1);

                $stmtIns = $this->acceso->prepare("INSERT INTO despacho_insumo (cantidad, producto_id_producto, despacho_id_despacho) VALUES (:cantidad, :id_producto, :id_despacho)");
                $stmtIns->execute([
                    ':cantidad' => $cantidad,
                    ':id_producto' => $id_producto,
                    ':id_despacho' => $id_despacho
                ]);

                if ($id_lote) {
                    $stmtLote = $this->acceso->prepare("SELECT vencimiento, lote_id_prov FROM lote WHERE id_lote = :id_lote");
                    $stmtLote->execute([':id_lote' => $id_lote]);
                    $lote_data = $stmtLote->fetch(PDO::FETCH_ASSOC);

                    $stmtDet = $this->acceso->prepare("INSERT INTO detalle_despacho (det_cantidad, det_vencimiento, id_det_lote, id_det_prod, lote_id_prov, id_det_despacho) VALUES (:cantidad, :vencimiento, :id_lote, :id_producto, :lote_id_prov, :id_despacho)");
                    $stmtDet->execute([
                        ':cantidad' => $cantidad,
                        ':vencimiento' => $lote_data['vencimiento'],
                        ':id_lote' => $id_lote,
                        ':id_producto' => $id_producto,
                        ':lote_id_prov' => $lote_data['lote_id_prov'],
                        ':id_despacho' => $id_despacho
                    ]);

                    $stmtStockDown = $this->acceso->prepare("UPDATE lote SET stock = stock - :cantidad WHERE id_lote = :id_lote");
                    $stmtStockDown->execute([':cantidad' => $cantidad, ':id_lote' => $id_lote]);
                }
            }

            $this->acceso->commit();
            return true;

        } catch (Exception $e) {
            $this->acceso->rollBack();
            throw $e;
        }
    }
}
?>
