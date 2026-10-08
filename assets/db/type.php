<?php
/**
 * Modelo de Datos y Lógica de Negocio para Categorías y Tipos de Insumo
 *
 * Administra el catálogo maestro de clasificaciones y familias de insumos médicos
 * (Productos Químicos, Equipos de Limpieza, Equipos de Protección Individual EPI,
 * Productos de Papel, Repuestos y Mantenimiento).
 * Implementa control de duplicados y validación de integridad referencial.
 *
 * Flujo de Integración Extremo a Extremo:
 * 1. UI/Cliente: Formulario en vista de tipos (`assets/libs/js/tipo.js`).
 * 2. Petición HTTP: AJAX POST hacia `assets/controller/TypeController.php`.
 * 3. Procesamiento Modelo: `tipo_producto::crear`, `buscar`, `editar`, `borrar_type`.
 * 4. Persistencia DB: Operaciones sobre tabla `tipo_producto` y verificación en `producto`.
 * 5. Respuesta: Notificaciones de texto plano ('add', 'no add', 'edit', 'borrado') o colección JSON.
 *
 * @package SIMAP\Models
 * @author Grupo de Proyecto
 * @version 1.0.0
 */

include_once 'conexion.php';

class tipo_producto {
    /**
     * @var array Colección de registros resultantes de consultas.
     */
    public $objetos;

    /**
     * @var PDO Instancia activa de conexión a la base de datos SQLite.
     */
    private $acceso;

    /**
     * Constructor del modelo tipo_producto.
     *
     * Inicializa la conexión PDO mediante la clase centralizada `Conexion`.
     */
    public function __construct() {
        $db = new Conexion();
        $this->acceso = $db->pdo;
    }

    /**
     * Registrar una nueva categoría o tipo de insumo médico.
     *
     * Valida que no exista previamente otra categoría con el mismo nombre.
     *
     * @param string $nombre Denominación descriptiva de la categoría.
     * @return void Emite 'add' si se registra satisfactoriamente o 'no add' si está duplicada.
     */
    public function crear($nombre) {
        $sql = "SELECT id_tip_prod FROM tipo_producto WHERE nombre = :nombre";
        $query = $this->acceso->prepare($sql);
        $query->execute([':nombre' => $nombre]);

        if ($query->rowCount() > 0) {
            echo 'no add';
        } else {
            $sql = "INSERT INTO tipo_producto (nombre) VALUES (:nombre)";
            $query = $this->acceso->prepare($sql);
            if ($query->execute([':nombre' => $nombre])) {
                echo 'add';
            }
        }
    }

    /**
     * Buscar tipos de producto por coincidencia de texto.
     *
     * @return array Colección de categorías coincidentes o las primeras 10 registradas.
     */
    public function buscar() {
        if (!empty($_POST['consulta'])) {
            $consulta = $_POST['consulta'];
            $sql = "SELECT * FROM tipo_producto WHERE nombre LIKE :consulta";
            $query = $this->acceso->prepare($sql);
            $query->execute([':consulta' => "%$consulta%"]);
            $this->objetos = $query->fetchAll();
            return $this->objetos;
        } else {
            $sql = "SELECT * FROM tipo_producto WHERE nombre NOT LIKE '' ORDER BY id_tip_prod LIMIT 10";
            $query = $this->acceso->prepare($sql);
            $query->execute();
            $this->objetos = $query->fetchAll();
            return $this->objetos;
        }
    }

    /**
     * Eliminar un tipo o categoría garantizando integridad referencial.
     *
     * Comprueba si la categoría está asociada a productos en la tabla `producto`
     * antes de proceder con el borrado físico.
     *
     * @param int $id Identificador del tipo a remover.
     * @return void Emite 'borrado', 'no-borrado' o mensaje de advertencia si tiene productos vinculados.
     */
    public function borrar_type($id) {
        $verificar_sql = "SELECT COUNT(*) as count FROM producto WHERE prod_tip_prod = :id";
        $verificar_query = $this->acceso->prepare($verificar_sql);
        $verificar_query->execute([':id' => $id]);
        $count = $verificar_query->fetch(PDO::FETCH_ASSOC)['count'];

        if ($count > 0) {
            echo "No se puede eliminar el tipo de producto. Está siendo utilizado por al menos un producto.";
        } else {
            $sql = "DELETE FROM tipo_producto WHERE id_tip_prod = :id";
            $query = $this->acceso->prepare($sql);
            $query->execute([':id' => $id]);
            if ($query->rowCount() > 0) {
                echo 'borrado';
            } else {
                echo 'no-borrado';
            }
        }
    }

    /**
     * Actualizar la denominación de una categoría existente.
     *
     * @param string $nombre Nombre actualizado de la categoría.
     * @param int $id_editado Identificador de la categoría a modificar.
     * @return void Emite 'edit' al completarse.
     */
    public function editar($nombre, $id_editado) {
        $sql = "UPDATE tipo_producto SET nombre = :nombre WHERE id_tip_prod = :id";
        $query = $this->acceso->prepare($sql);
        $query->execute([':id' => $id_editado, ':nombre' => $nombre]);
        echo 'edit';
    }

    /**
     * Obtener el catálogo completo de tipos de producto ordenados alfabéticamente.
     *
     * Utilizado para poblar selectores desplegables HTML (`<select>`) en la gestión del catálogo.
     *
     * @return array Colección completa de categorías registradas.
     */
    public function rellenar_type() {
        $sql = "SELECT * FROM tipo_producto ORDER BY nombre ASC";
        $query = $this->acceso->prepare($sql);
        $query->execute();
        $this->objetos = $query->fetchAll();
        return $this->objetos;
    }
}
?>
