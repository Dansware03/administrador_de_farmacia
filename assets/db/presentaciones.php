<?php
/**
 * Modelo de Datos y Lógica de Negocio para Presentaciones Farmacéuticas
 *
 * Administra el catálogo maestro de presentaciones, formas farmacéuticas
 * y empaques de insumos y medicamentos (Ampolla, Frasco, Blíster, Caja x 50, etc.).
 * Implementa control de duplicados y validación de integridad referencial.
 *
 * Flujo de Integración Extremo a Extremo:
 * 1. UI/Cliente: Formulario en vista de presentaciones (`assets/libs/js/presentacion.js`).
 * 2. Petición HTTP: AJAX POST hacia `assets/controller/PresentacionesController.php`.
 * 3. Procesamiento Modelo: `Presentacion::crear`, `buscar`, `editar`, `borrar_pre`.
 * 4. Persistencia DB: Operaciones sobre tabla `presentacion` y validación de dependencias en `producto`.
 * 5. Respuesta: Notificaciones de texto plano ('add', 'no add', 'edit', 'borrado') o colección JSON.
 *
 * @package SIMAP\Models
 * @author Grupo de Proyecto
 * @version 1.0.0
 */

include_once 'conexion.php';

class Presentacion {
    /**
     * @var array Colección de registros resultantes de consultas.
     */
    public $objetos;

    /**
     * @var PDO Instancia activa de conexión a la base de datos.
     */
    private $acceso;

    /**
     * Constructor del modelo Presentacion.
     *
     * Inicializa la conexión PDO mediante la clase centralizada `Conexion`.
     */
    public function __construct() {
        $db = new Conexion();
        $this->acceso = $db->pdo;
    }

    /**
     * Registrar una nueva presentación farmacéutica.
     *
     * Verifica previamente que no exista otra presentación con la misma denominación.
     *
     * @param string $nombre Nombre descriptivo de la presentación o empaque.
     * @return void Emite 'add' si se inserta exitosamente o 'no add' si ya existe.
     */
    public function crear($nombre) {
        try {
            $sql = "SELECT id_presentacion FROM presentacion WHERE nombre = :nombre";
            $query = $this->acceso->prepare($sql);
            $query->execute([':nombre' => $nombre]);

            if ($query->rowCount() > 0) {
                echo 'no add';
            } else {
                $sql = "INSERT INTO presentacion (nombre) VALUES (:nombre)";
                $query = $this->acceso->prepare($sql);
                if ($query->execute([':nombre' => $nombre])) {
                    echo 'add';
                }
            }
        } catch (PDOException $e) {
            echo 'Error al crear la presentación: ' . $e->getMessage();
        }
    }

    /**
     * Buscar presentaciones por coincidencia de texto en el nombre.
     *
     * @return array Registros coincidentes con el criterio de búsqueda.
     */
    public function buscar() {
        $consulta = $_POST['consulta'] ?? '';
        $sql = "SELECT * FROM presentacion WHERE nombre LIKE :consulta ORDER BY id_presentacion LIMIT 10";
        $query = $this->acceso->prepare($sql);
        $query->execute([':consulta' => "%$consulta%"]);
        $this->objetos = $query->fetchAll();
        return $this->objetos;
    }

    /**
     * Eliminar una presentación garantizando integridad referencial.
     *
     * Valida si existen productos vinculados a la presentación en la tabla `producto`
     * antes de proceder con el borrado.
     *
     * @param int $id Identificador único de la presentación a eliminar.
     * @return void Emite 'borrado', 'no-borrado' o mensaje de advertencia si tiene productos vinculados.
     */
    public function borrar_pre($id) {
        try {
            $verificar_sql = "SELECT COUNT(*) as count FROM producto WHERE prod_present = :id";
            $verificar_query = $this->acceso->prepare($verificar_sql);
            $verificar_query->bindValue(':id', $id, PDO::PARAM_INT);
            $verificar_query->execute();
            $count = $verificar_query->fetch(PDO::FETCH_ASSOC)['count'];

            if ($count > 0) {
                echo "No se puede eliminar la presentación. Está siendo utilizada por al menos un producto.";
            } else {
                $sql = "DELETE FROM presentacion WHERE id_presentacion = :id";
                $query = $this->acceso->prepare($sql);
                $query->bindValue(':id', $id, PDO::PARAM_INT);
                $query->execute();
                echo ($query->rowCount() > 0) ? 'borrado' : 'no-borrado';
            }
        } catch (PDOException $e) {
            echo 'Error al borrar la presentación: ' . $e->getMessage();
        }
    }

    /**
     * Actualizar la denominación de una presentación farmacéutica existente.
     *
     * @param string $nombre Nuevo nombre de la presentación.
     * @param int $id_editado Identificador de la presentación a modificar.
     * @return void Emite 'edit' o mensaje de validación ante campos vacíos.
     */
    public function editar($nombre, $id_editado) {
        try {
            if (!empty($nombre)) {
                $sql = "UPDATE presentacion SET nombre = :nombre WHERE id_presentacion = :id";
                $query = $this->acceso->prepare($sql);
                $query->execute([
                    ':id' => $id_editado,
                    ':nombre' => $nombre
                ]);
                echo 'edit';
            } else {
                echo 'Nombre no válido para editar';
            }
        } catch (PDOException $e) {
            echo 'Error al editar la presentación: ' . $e->getMessage();
        }
    }

    /**
     * Obtener el catálogo completo de presentaciones ordenadas alfabéticamente.
     *
     * Utilizado para poblar selectores desplegables HTML (`<select>`) en la gestión de insumos.
     *
     * @return array Colección completa de presentaciones registradas.
     */
    public function rellenar_presentacion() {
        $sql = "SELECT * FROM presentacion ORDER BY nombre ASC";
        $query = $this->acceso->prepare($sql);
        $query->execute();
        $this->objetos = $query->fetchAll();
        return $this->objetos;
    }
}
?>
