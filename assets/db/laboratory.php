<?php
/**
 * Modelo de Datos y Lógica de Negocio para Laboratorios Fabricantes
 *
 * Administra el catálogo de marcas y laboratorios farmacéuticos productores
 * de insumos y medicamentos. Provee operaciones CRUD con validaciones
 * de unicidad de nombre y verificación de integridad referencial previo al borrado.
 *
 * Flujo de Integración Extremo a Extremo:
 * 1. UI/Cliente: Formulario en vista de laboratorios (`assets/libs/js/laboratorio.js`).
 * 2. Petición HTTP: AJAX POST hacia `assets/controller/LaboratoryController.php`.
 * 3. Procesamiento Modelo: `Laboratorio::crear`, `editar`, `borrar_lab` o `buscar`.
 * 4. Persistencia DB: Consultas preparadas en tabla `laboratorio` y chequeo de FK en `producto`.
 * 5. Respuesta: Retorno de datos o emisión directa de estado JSON al cliente.
 *
 * @package SIMAP\Models
 * @author Grupo de Proyecto
 * @version 1.0.0
 */

include_once 'conexion.php';

class Laboratorio {
    /**
     * @var PDO Instancia de conexión a la base de datos SQLite.
     */
    private $acceso;

    /**
     * @var array Colección de registros resultantes de consultas.
     */
    public $objetos;

    /**
     * Constructor del modelo Laboratorio.
     *
     * Inicializa la conexión PDO a través de la clase Conexion.
     */
    public function __construct() {
        $db = new Conexion();
        $this->acceso = $db->pdo;
    }

    /**
     * Registrar un nuevo laboratorio fabricante.
     *
     * Valida que el nombre no esté vacío ni duplicado en la base de datos.
     * Emite una respuesta JSON estructurada con el resultado de la operación.
     *
     * @param string $nombre Nombre descriptivo del laboratorio.
     * @return void
     */
    public function crear($nombre) {
        if (empty($nombre)) {
            echo json_encode(['status' => 'error', 'message' => 'El nombre del laboratorio no puede estar vacío.']);
            return;
        }

        $sql_verificar = "SELECT id_laboratorio FROM laboratorio WHERE nombre = :nombre";
        $query_verificar = $this->acceso->prepare($sql_verificar);
        $query_verificar->execute([':nombre' => $nombre]);
        $objetos_verificar = $query_verificar->fetchAll();

        if (!empty($objetos_verificar)) {
            echo json_encode(['status' => 'error', 'message' => 'Ya existe otro laboratorio con el mismo nombre']);
            return;
        }

        $sql = "INSERT INTO laboratorio (nombre) VALUES (:nombre)";
        $query = $this->acceso->prepare($sql);

        if ($query->execute([':nombre' => $nombre])) {
            echo json_encode(['status' => 'success', 'message' => 'Laboratorio creado con éxito']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error al crear el laboratorio']);
        }
    }

    /**
     * Buscar laboratorios por coincidencia de texto o listar los primeros registros.
     *
     * Si existe el parámetro `$_POST['consulta']`, realiza un filtrado parcial LIKE.
     * De lo contrario, devuelve los primeros 10 laboratorios ordenados por identificador.
     *
     * @return array Lista de objetos/registros de laboratorios.
     */
    public function buscar() {
        if (!empty($_POST['consulta'])) {
            $consulta = $_POST['consulta'];
            $sql = "SELECT * FROM laboratorio WHERE nombre LIKE :consulta";
            $query = $this->acceso->prepare($sql);
            $query->execute([':consulta' => "%$consulta%"]);
            $this->objetos = $query->fetchAll();
        } else {
            $sql = "SELECT * FROM laboratorio WHERE nombre NOT LIKE '' ORDER BY id_laboratorio LIMIT 10";
            $query = $this->acceso->prepare($sql);
            $query->execute();
            $this->objetos = $query->fetchAll();
        }
        return $this->objetos;
    }

    /**
     * Eliminar un laboratorio garantizando integridad referencial.
     *
     * Verifica previamente si existen productos vinculados a este laboratorio en la tabla `producto`.
     * Si tiene insumos asociados, aborta la eliminación y emite mensaje de advertencia.
     *
     * @param int $id Identificador único del laboratorio a eliminar.
     * @return void
     */
    public function borrar_lab($id) {
        $verificar_sql = "SELECT COUNT(*) as count FROM producto WHERE prod_lab = :id";
        $verificar_query = $this->acceso->prepare($verificar_sql);
        $verificar_query->execute([':id' => $id]);
        $count = $verificar_query->fetch(PDO::FETCH_ASSOC)['count'];

        if ($count > 0) {
            echo json_encode(['status' => 'error', 'message' => 'No se puede eliminar el laboratorio. Está siendo utilizado por al menos un producto.']);
            return;
        }

        $sql = "DELETE FROM laboratorio WHERE id_laboratorio = :id";
        $query = $this->acceso->prepare($sql);
        if ($query->execute([':id' => $id])) {
            echo json_encode(['status' => 'success', 'message' => 'Laboratorio borrado con éxito']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error al borrar el laboratorio']);
        }
    }

    /**
     * Actualizar el nombre descriptivo de un laboratorio existente.
     *
     * Valida que el nuevo nombre no esté vacío y no colisione con el de otro registro distinto.
     *
     * @param string $nombre Nuevo nombre asignado al laboratorio.
     * @param int $id_editado Identificador del laboratorio a actualizar.
     * @return void
     */
    public function editar($nombre, $id_editado) {
        if (empty($nombre)) {
            echo json_encode(['status' => 'error', 'message' => 'El nombre del laboratorio no puede estar vacío.']);
            return;
        }

        $sql_verificar = "SELECT id_laboratorio FROM laboratorio WHERE nombre = :nombre AND id_laboratorio != :id";
        $query_verificar = $this->acceso->prepare($sql_verificar);
        $query_verificar->execute([':nombre' => $nombre, ':id' => $id_editado]);
        $objetos_verificar = $query_verificar->fetchAll();

        if (!empty($objetos_verificar)) {
            echo json_encode(['status' => 'error', 'message' => 'Ya existe otro laboratorio con el mismo nombre']);
            return;
        }

        $sql_actualizar = "UPDATE laboratorio SET nombre = :nombre WHERE id_laboratorio = :id";
        $query_actualizar = $this->acceso->prepare($sql_actualizar);
        if ($query_actualizar->execute([':id' => $id_editado, ':nombre' => $nombre])) {
            echo json_encode(['status' => 'success', 'message' => 'Laboratorio editado con éxito']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error al editar el laboratorio']);
        }
    }

    /**
     * Obtener el catálogo completo de laboratorios ordenados alfabéticamente.
     *
     * Utilizado para poblar selectores desplegables (HTML `<select>`) en la gestión de productos e inventario.
     *
     * @return array Colección con todos los laboratorios registrados.
     */
    public function rellenar_laboratorio() {
        $sql = "SELECT * FROM laboratorio ORDER BY nombre ASC";
        $query = $this->acceso->prepare($sql);
        $query->execute();
        $this->objetos = $query->fetchAll();
        return $this->objetos;
    }
}
?>
