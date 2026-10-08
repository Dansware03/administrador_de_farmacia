<?php
include_once 'conexion.php';
class Producto {
    var $objetos;
    private $acceso;
    public function __construct() {
        $db = new Conexion();
        $this->acceso = $db->pdo;
    }
    public function crear($nombre, $concentracion, $adicional, $avatar = 'ProductDefault.png', $prod_lab = '', $prod_tip_prod = '', $prod_present = '', $id_unidad = 1, $especificacion_talla = '') {
        try {
            $sql = "SELECT id_producto FROM producto WHERE nombre = :nombre and concentracion=:concentracion and adicional=:adicional and prod_lab=:laboratorio and prod_tip_prod=:tipo and prod_present=:presentacion";
            $query = $this->acceso->prepare($sql);
            $query->execute(array(':nombre' => $nombre, ':concentracion' => $concentracion, ':adicional' => $adicional, ':laboratorio' => $prod_lab, ':tipo' => $prod_tip_prod, ':presentacion' => $prod_present));
            $result = $query->fetchAll();
            if (!empty($result)) {
                throw new Exception('El insumo o producto ya existe.');
            }
            $sql = "INSERT INTO producto (nombre, concentracion, adicional, avatar, prod_lab, prod_tip_prod, prod_present, id_unidad, especificacion_talla) VALUES (:nombre, :concentracion, :adicional, :avatar, :laboratorio, :tipo, :presentacion, :id_unidad, :especificacion_talla)";
            $query = $this->acceso->prepare($sql);
            if ($query->execute(array(
                ':nombre' => $nombre,
                ':concentracion' => $concentracion,
                ':adicional' => $adicional,
                ':avatar' => $avatar,
                ':laboratorio' => $prod_lab,
                ':tipo' => $prod_tip_prod,
                ':presentacion' => $prod_present,
                ':id_unidad' => $id_unidad,
                ':especificacion_talla' => $especificacion_talla
            ))) {
                echo 'add';
            } else {
                throw new Exception('Error al insertar el insumo.');
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
    function buscar($consulta = '') {
        try {
            // ponytail: Consulta unificada con suma de stock de lotes agrupada para evitar N+1 queries.
            $sql = "SELECT
                producto.id_producto,
                producto.nombre,
                producto.concentracion,
                producto.adicional,
                producto.especificacion_talla,
                producto.id_unidad,
                unidad_medida.nombre AS unidad_medida,
                unidad_medida.codigo AS unidad_codigo,
                laboratorio.nombre AS nombre_laboratorio,
                tipo_producto.nombre AS tipo,
                presentacion.nombre AS nombre_presentacion,
                producto.avatar, prod_lab, prod_tip_prod, prod_present,
                COALESCE(lote_sum.total_stock, 0) AS total_stock
            FROM producto
            JOIN laboratorio ON prod_lab = id_laboratorio
            JOIN tipo_producto ON prod_tip_prod = id_tip_prod
            JOIN presentacion ON prod_present = id_presentacion
            LEFT JOIN unidad_medida ON producto.id_unidad = unidad_medida.id_unidad
            LEFT JOIN (
                SELECT id_lote_prod, SUM(stock) AS total_stock
                FROM lote
                GROUP BY id_lote_prod
            ) AS lote_sum ON lote_sum.id_lote_prod = producto.id_producto";

            if (!empty($consulta)) {
                $sql .= " WHERE producto.nombre LIKE :consulta OR tipo_producto.nombre LIKE :consulta OR laboratorio.nombre LIKE :consulta";
                $sql .= " ORDER BY producto.nombre";
                $query = $this->acceso->prepare($sql);
                $query->execute([':consulta' => "%$consulta%"]);
            } else {
                $sql .= " ORDER BY producto.nombre";
                $query = $this->acceso->prepare($sql);
                $query->execute();
            }

            $this->objetos = $query->fetchAll(PDO::FETCH_ASSOC);
            return $this->objetos;
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }
    function cambiar_avatar($id,$nombre) {
        $sql = "UPDATE producto SET avatar=:nombre where id_producto =:id";
        $query = $this->acceso->prepare($sql);
        $query->execute(array(':id' => $id, ':nombre' => $nombre));
    }
    public function editar($id_edit_prod, $nombre, $concentracion, $adicional, $prod_lab = '', $prod_tip_prod = '', $prod_present = '', $id_unidad = 1, $especificacion_talla = '') {
        try {
            $sql_update = "UPDATE producto SET nombre = :nombre, concentracion = :concentracion, adicional = :adicional, prod_lab = :laboratorio, prod_tip_prod = :tipo, prod_present = :presentacion, id_unidad = :id_unidad, especificacion_talla = :especificacion_talla WHERE id_producto = :id_edit_prod";
            $query_update = $this->acceso->prepare($sql_update);
            $query_update->execute(array(
                ':id_edit_prod' => $id_edit_prod,
                ':nombre' => $nombre,
                ':concentracion' => $concentracion,
                ':adicional' => $adicional,
                ':laboratorio' => $prod_lab,
                ':tipo' => $prod_tip_prod,
                ':presentacion' => $prod_present,
                ':id_unidad' => $id_unidad,
                ':especificacion_talla' => $especificacion_talla
            ));
            if ($query_update->rowCount() > 0) {
                echo 'edit';
            } else {
                echo 'edit'; // Si los datos eran idénticos no arroja excepción al cliente
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
    function borrar_produts($id){
        try {
            $sql = "SELECT avatar FROM producto WHERE id_producto = :id";
            $query = $this->acceso->prepare($sql);
            $query->execute(array(':id' => $id));
            $result = $query->fetch(PDO::FETCH_ASSOC);
            if ($result && isset($result['avatar'])) {
                $avatarNombre = $result['avatar'];
                if ($avatarNombre !== 'ProductDefault.png') {
                    $rutaAvatar = "../libs/img/product/" . $avatarNombre;
                    if (file_exists($rutaAvatar)) {
                        unlink($rutaAvatar);
                    }
                }
            }
            $sql = "DELETE FROM producto WHERE id_producto = :id";
            $query = $this->acceso->prepare($sql);
            $query->execute(array(':id' => $id));
            if ($query->rowCount() > 0) {
                echo 'borrado';
            } else {
                echo json_encode(['status' => 'error', 'message' => 'No se pudo eliminar el producto.']);
            }
        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => 'Error en el servidor.']);
            // Puedes agregar un mensaje de error o log aquí
        }
    }
    function obtener_stock($id){
        $sql="SELECT SUM(stock) as total FROM lote where id_lote_prod=:id";
        $query = $this->acceso->prepare($sql);
        $query->execute(array(':id' => $id));
        $this->objetos = $query->fetchAll();
        return $this->objetos;
    }
};
?>