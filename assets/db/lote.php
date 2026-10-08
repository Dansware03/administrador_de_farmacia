<?php
/**
 * Modelo de Datos y Lógica de Negocio para Lotes e Inventario
 *
 * Administra los lotes de insumos médicos y farmacéuticos en la base de datos SQLite.
 * Gestiona existencias por lote, control de caducidad (insumos perecederos y no perecederos),
 * trazabilidad de proveedores/donantes y persistencia transaccional.
 *
 * Flujo de Integración Extremo a Extremo:
 * 1. UI/Cliente: Formularios de registro y edición de lotes (`assets/libs/js/lote.js`, `gestion_lote.js`).
 * 2. Petición HTTP: AJAX POST hacia `assets/controller/LoteController.php`.
 * 3. Procesamiento Modelo: `Lote::crear`, `buscar`, `editar`, `borrar_lote`.
 * 4. Persistencia DB: Operaciones sobre tabla `lote` con relaciones hacia `producto`, `proveedor`.
 * 5. Respuesta: Notificaciones de texto plano ('add', 'edit', 'borrado') o colección JSON.
 *
 * @package SIMAP\Models
 * @author Grupo de Proyecto
 * @version 1.0.0
 */

include_once 'conexion.php';

class Lote {
    /**
     * @var array Colección de registros recuperados por consultas de búsqueda.
     */
    public $objetos;

    /**
     * @var PDO Instancia activa de conexión a la base de datos.
     */
    private $acceso;

    /**
     * Constructor del modelo Lote.
     *
     * Inicializa la conexión PDO mediante el conector centralizado `Conexion`.
     */
    public function __construct() {
        $db = new Conexion();
        $this->acceso = $db->pdo;
    }

    /**
     * Registrar un nuevo lote de insumos médicos.
     *
     * Inserta un nuevo lote vinculando el insumo maestro, proveedor o donante,
     * código de lote asignado, stock inicial y fecha de vencimiento.
     *
     * @param int $id_producto Identificador del insumo médico maestro (`producto`).
     * @param int $proveedor Identificador del proveedor o donante (`proveedor`).
     * @param string $cod_lote Código de identificación del lote.
     * @param int $stock Cantidad inicial de unidades disponibles.
     * @param string $vencimiento Fecha de caducidad (formato YYYY-MM-DD o '0000-00-00').
     * @return void Emite 'add' en caso de éxito o mensaje de error ante fallos.
     */
    public function crear($id_producto, $proveedor, $cod_lote, $stock, $vencimiento) {
        try {
            $sql = "INSERT INTO lote (cod_lote, stock, vencimiento, id_lote_prod, lote_id_prov) 
                    VALUES (:cod_lote, :stock, :vencimiento, :id_producto, :id_proveedor)";
            $query = $this->acceso->prepare($sql);
            $query->execute([
                ':cod_lote' => $cod_lote,
                ':stock' => $stock,
                ':vencimiento' => $vencimiento,
                ':id_producto' => $id_producto,
                ':id_proveedor' => $proveedor
            ]);
            echo 'add';
        } catch (PDOException $e) {
            echo 'Error al crear el lote: ' . $e->getMessage();
        }
    }

    /**
     * Buscar lotes con filtro por nombre de insumo médico.
     *
     * Consulta lotes relacionando insumos, laboratorios, tipos, presentaciones y proveedores.
     * Incluye la columna `prod_tip_prod` para distinguir entre insumos perecederos y materiales no perecederos.
     *
     * @return array Colección de lotes coincidentes con datos enriquecidos.
     */
    public function buscar() {
        $consulta = $_POST['consulta'] ?? '';
        $sql = "SELECT id_lote, cod_lote, stock, vencimiento, concentracion, adicional, prod_tip_prod,
                       producto.nombre AS prod_nom, laboratorio.nombre AS lab_nom, tipo_producto.nombre AS tip_nom, 
                       presentacion.nombre AS pre_nom, proveedor.nombre AS pro_nom, producto.avatar AS logo 
                FROM lote
                JOIN proveedor ON lote_id_prov = id_proveedor
                JOIN producto ON id_lote_prod = id_producto
                JOIN laboratorio ON prod_lab = id_laboratorio
                JOIN tipo_producto ON prod_tip_prod = id_tip_prod
                JOIN presentacion ON prod_present = id_presentacion
                WHERE producto.nombre LIKE :consulta
                ORDER BY producto.nombre LIMIT 25";
        $query = $this->acceso->prepare($sql);
        $query->execute([':consulta' => "%$consulta%"]);
        $this->objetos = $query->fetchAll();
        return $this->objetos;
    }

    /**
     * Actualizar la cantidad de existencias de un lote específico.
     *
     * @param int $id_lote Identificador único del lote a editar.
     * @param int $stock Nueva cantidad de existencias disponible.
     * @return void Emite 'edit' en caso de éxito o mensaje de error ante fallos.
     */
    public function editar($id_lote, $stock) {
        try {
            $sql = "UPDATE lote SET stock = :stock WHERE id_lote = :id_lote";
            $query = $this->acceso->prepare($sql);
            $query->execute([
                ':stock' => $stock,
                ':id_lote' => $id_lote
            ]);
            echo 'edit';
        } catch (PDOException $e) {
            echo 'Error al editar el lote: ' . $e->getMessage();
        }
    }

    /**
     * Eliminar un lote de inventario dentro de una transacción atómica.
     *
     * Verifica la existencia del lote antes de ejecutar el borrado físico en base de datos.
     *
     * @param int $id Identificador del lote a remover.
     * @return void Emite 'borrado', 'El lote no existe' o mensaje descriptivo de error.
     */
    public function borrar_lote($id) {
        try {
            $this->acceso->beginTransaction();
            $existe = $this->verificar_existencia_lote($id);
            if ($existe) {
                $sql = "DELETE FROM lote WHERE id_lote = :id";
                $query = $this->acceso->prepare($sql);
                $query->execute([':id' => $id]);
                $this->acceso->commit();
                echo 'borrado';
            } else {
                $this->acceso->rollBack();
                echo 'El lote no existe';
            }
        } catch (PDOException $e) {
            $this->acceso->rollBack();
            echo 'Error al borrar el lote: ' . $e->getMessage();
        }
    }

    /**
     * Verificar la existencia de un lote específico por ID.
     *
     * @param int $id Identificador único del lote.
     * @return bool True si el lote existe en la base de datos, false de lo contrario.
     */
    public function verificar_existencia_lote($id) {
        $sql = "SELECT COUNT(*) FROM lote WHERE id_lote = :id";
        $query = $this->acceso->prepare($sql);
        $query->execute([':id' => $id]);
        $resultado = $query->fetchColumn();
        return ($resultado > 0);
    }
}
?>
