<?php
/**
 * Modelo de Insumos y Materiales Médicos (Catálogo Central)
 *
 * Administra el catálogo de insumos sanitarios, materiales de bioseguridad y EPIs.
 * Gestiona inserciones atómicas, consultas optimizadas con consolidación de inventario
 * para prevenir problemas N+1, y trazabilidad de almacenamiento.
 *
 * @package SIMAP\Database
 * @author Grupo de Proyecto
 * @version 1.0.0
 */
include_once 'conexion.php';

class Producto {
    /** @var array Arreglo contenedor de resultados de consultas. */
    var $objetos;

    /** @var PDO Instancia activa del conector de base de datos. */
    private $acceso;

    /**
     * Constructor de la clase Producto.
     * Inicializa la conexión PDO mediante la instancia singleton.
     */
    public function __construct() {
        $db = new Conexion();
        $this->acceso = $db->pdo;
    }

    /**
     * Registra un nuevo insumo y opcionalmente su primer lote de inventario en una transacción atómica.
     *
     * @param string $nombre Nombre del insumo o material.
     * @param string $concentracion Concentración o detalle técnico (opcional).
     * @param string $adicional Indicaciones complementarias o especificaciones.
     * @param string $avatar Nombre del archivo de avatar/imagen.
     * @param int|string $prod_lab Identificador del fabricante o laboratorio.
     * @param int|string $prod_tip_prod Identificador de la categoría o tipo.
     * @param int|string $prod_present Identificador de la presentación comercial.
     * @param int $id_unidad Identificador de la unidad de medida (und, L, gal, etc.).
     * @param string $especificacion_talla Talla o medida física (S, M, L, XL, etc.).
     * @param array|null $lote_data Datos opcionales para la inserción inicial de lote.
     * @return void Emite salida de texto ('add' o mensaje de error).
     */
    public function crear($nombre, $concentracion, $adicional, $avatar = 'ProductDefault.png', $prod_lab = '', $prod_tip_prod = '', $prod_present = '', $id_unidad = 1, $especificacion_talla = '', $lote_data = null) {
        try {
            $sql = "SELECT id_producto FROM producto WHERE nombre = :nombre and concentracion=:concentracion and adicional=:adicional and prod_lab=:laboratorio and prod_tip_prod=:tipo and prod_present=:presentacion";
            $query = $this->acceso->prepare($sql);
            $query->execute(array(':nombre' => $nombre, ':concentracion' => $concentracion, ':adicional' => $adicional, ':laboratorio' => $prod_lab, ':tipo' => $prod_tip_prod, ':presentacion' => $prod_present));
            $result = $query->fetchAll();
            if (!empty($result)) {
                throw new Exception('El insumo o producto ya existe.');
            }

            $this->acceso->beginTransaction();

            $sql = "INSERT INTO producto (nombre, concentracion, adicional, avatar, prod_lab, prod_tip_prod, prod_present, id_unidad, especificacion_talla) VALUES (:nombre, :concentracion, :adicional, :avatar, :laboratorio, :tipo, :presentacion, :id_unidad, :especificacion_talla)";
            $query = $this->acceso->prepare($sql);
            $query->execute(array(
                ':nombre' => $nombre,
                ':concentracion' => $concentracion,
                ':adicional' => $adicional,
                ':avatar' => $avatar,
                ':laboratorio' => $prod_lab,
                ':tipo' => $prod_tip_prod,
                ':presentacion' => $prod_present,
                ':id_unidad' => $id_unidad,
                ':especificacion_talla' => $especificacion_talla
            ));

            $id_nuevo_producto = $this->acceso->lastInsertId();

            // ponytail: Si se enviaron datos de lote inicial, insertar el lote de forma atómica
            if ($lote_data && !empty($lote_data['cod_lote']) && !empty($lote_data['stock']) && !empty($lote_data['proveedor'])) {
                $sql_lote = "INSERT INTO lote (cod_lote, stock, vencimiento, id_lote_prod, lote_id_prov) VALUES (:cod_lote, :stock, :vencimiento, :id_producto, :id_proveedor)";
                $query_lote = $this->acceso->prepare($sql_lote);
                $query_lote->execute(array(
                    ':cod_lote' => $lote_data['cod_lote'],
                    ':stock' => intval($lote_data['stock']),
                    ':vencimiento' => !empty($lote_data['vencimiento']) ? $lote_data['vencimiento'] : '2035-12-31',
                    ':id_producto' => $id_nuevo_producto,
                    ':id_proveedor' => $lote_data['proveedor']
                ));
            }

            $this->acceso->commit();
            echo 'add';
        } catch (Exception $e) {
            if ($this->acceso->inTransaction()) {
                $this->acceso->rollBack();
            }
            echo $e->getMessage();
        }
    }
    /**
     * Consulta el inventario consolidado de insumos con búsqueda opcional por texto.
     * Agrupa y suma el stock de todos los lotes asociados para prevenir consultas N+1.
     *
     * @param string $consulta Texto de filtro (nombre, categoría o fabricante).
     * @return array|false Lista de insumos con su stock total calculado o false en caso de error.
     */
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

    /**
     * Actualiza la imagen representativa del insumo.
     *
     * @param int|string $id Identificador del producto.
     * @param string $nombre Nombre del archivo de imagen.
     * @return void
     */
    function cambiar_avatar($id, $nombre) {
        $sql = "UPDATE producto SET avatar=:nombre where id_producto =:id";
        $query = $this->acceso->prepare($sql);
        $query->execute(array(':id' => $id, ':nombre' => $nombre));
    }

    /**
     * Actualiza la ficha descriptiva y clasificaciones de un insumo.
     *
     * @param int|string $id_edit_prod Identificador del producto.
     * @param string $nombre Denominación del producto.
     * @param string $concentracion Concentración técnica o dosificación.
     * @param string $adicional Notas adicionales.
     * @param int|string $prod_lab Fabricante o laboratorio.
     * @param int|string $prod_tip_prod Categoría del producto.
     * @param int|string $prod_present Presentación empaquetada.
     * @param int $id_unidad Unidad de medida física.
     * @param string $especificacion_talla Talla o medida complementaria.
     * @return void Emite 'edit' o error.
     */
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

    /**
     * Elimina un producto y remueve su archivo de imagen asociado si no es el por defecto.
     *
     * @param int|string $id Identificador del producto a eliminar.
     * @return void Emite 'borrado' o JSON con mensaje de error.
     */
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
        }
    }

    /**
     * Obtiene la sumatoria total del stock físico de todos los lotes de un producto.
     *
     * @param int|string $id Identificador del producto.
     * @return array Registros con la sumatoria de stock ('total').
     */
    function obtener_stock($id){
        $sql="SELECT SUM(stock) as total FROM lote where id_lote_prod=:id";
        $query = $this->acceso->prepare($sql);
        $query->execute(array(':id' => $id));
        $this->objetos = $query->fetchAll();
        return $this->objetos;
    }

    /**
     * Valida de manera masiva la existencia de un conjunto de IDs de productos en la base de datos.
     *
     * @param array $ids Lista de IDs a verificar.
     * @return array IDs confirmados existentes.
     */
    function validar_existentes($ids = []){
        if (empty($ids) || !is_array($ids)) {
            return [];
        }
        $marks = implode(',', array_fill(0, count($ids), '?'));
        $query = $this->acceso->prepare("SELECT id_producto FROM producto WHERE id_producto IN (" . $marks . ")");
        $query->execute(array_values($ids));
        return $query->fetchAll(PDO::FETCH_COLUMN);
    }
};
?>