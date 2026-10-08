<?php
/**
 * Modelo de Datos y Lógica de Negocio para Proveedores y Donantes Humanitarios
 *
 * Administra las empresas proveedoras, donantes y organismos humanitarios
 * (INTERSOS, UNICEF, FUNREAHV, etc.) en la base de datos local SQLite.
 * Gestiona el catálogo de entidades, contactos, logos institucionales y validaciones de unicidad.
 *
 * Flujo de Integración Extremo a Extremo:
 * 1. UI/Cliente: Formulario en gestión de proveedores (`assets/libs/js/proveedor.js`).
 * 2. Petición HTTP: AJAX POST hacia `assets/controller/ProveedorController.php`.
 * 3. Procesamiento Modelo: `Proveedor::crear`, `buscar`, `editar`, `borrar_prove`, `cambiar_avatar`.
 * 4. Persistencia DB: Consultas preparadas PDO sobre tabla `proveedor` y eliminación física de logos.
 * 5. Respuesta: Respuestas de estado ('add', 'borrado', etc.) o colección JSON al cliente.
 *
 * @package SIMAP\Models
 * @author Grupo de Proyecto
 * @version 1.0.0
 */

include_once 'conexion.php';

class Proveedor {
    /**
     * @var array Colección de registros resultantes de consultas.
     */
    public $objetos;

    /**
     * @var PDO Instancia activa de conexión a la base de datos SQLite.
     */
    private $acceso;

    /**
     * Constructor del modelo Proveedor.
     *
     * Inicializa la conexión PDO mediante la clase centralizada Conexion.
     */
    public function __construct() {
        $db = new Conexion();
        $this->acceso = $db->pdo;
    }

    /**
     * Registrar un nuevo proveedor o entidad donante.
     *
     * Valida la unicidad del nombre antes de proceder con la inserción en la base de datos.
     *
     * @param string $nombre Nombre o razón social del proveedor u organismo.
     * @param string $telefono Número telefónico de contacto.
     * @param string $correo Correo electrónico institucional.
     * @param string $direccion Dirección física o sede operativa.
     * @param string $avatar Nombre del archivo de imagen asignado como logotipo.
     * @return void Emite 'add' en caso de éxito, 'no add' si ya existe o mensaje de error.
     */
    public function crear($nombre, $telefono, $correo, $direccion, $avatar) {
        try {
            $sql = "SELECT id_proveedor FROM proveedor WHERE nombre = :nombre";
            $query = $this->acceso->prepare($sql);
            $query->execute([':nombre' => $nombre]);
            $this->objetos = $query->fetchAll();

            if (!empty($this->objetos)) {
                echo 'no add';
            } else {
                $sql = "INSERT INTO proveedor (nombre, telefono, correo, direccion, avatar) 
                        VALUES (:nombre, :telefono, :correo, :direccion, :avatar)";
                $query = $this->acceso->prepare($sql);
                $params = [
                    ':nombre' => $nombre,
                    ':telefono' => $telefono,
                    ':correo' => $correo,
                    ':direccion' => $direccion,
                    ':avatar' => $avatar
                ];

                if ($query->execute($params)) {
                    echo 'add';
                } else {
                    $error = $query->errorInfo();
                    echo 'Error: ' . $error[2];
                }
            }
        } catch (PDOException $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    /**
     * Buscar proveedores por filtro de coincidencia o listar los primeros registros.
     *
     * @param string $consulta Texto de búsqueda para filtrar por nombre.
     * @return array Listado de proveedores encontrados.
     */
    public function buscar($consulta = '') {
        try {
            if (!empty($consulta)) {
                $sql = "SELECT id_proveedor, nombre, telefono, correo, direccion, avatar
                        FROM proveedor
                        WHERE nombre LIKE :consulta
                        LIMIT 25";
                $query = $this->acceso->prepare($sql);
                $paramConsulta = "%$consulta%";
                $query->bindParam(':consulta', $paramConsulta, PDO::PARAM_STR);
            } else {
                $sql = "SELECT id_proveedor, nombre, telefono, correo, direccion, avatar
                        FROM proveedor
                        ORDER BY nombre LIMIT 25";
                $query = $this->acceso->prepare($sql);
            }
            $query->execute();
            $this->objetos = $query->fetchAll(PDO::FETCH_ASSOC);
            return $this->objetos;
        } catch (PDOException $e) {
            $this->objetos = [];
            return $this->objetos;
        }
    }

    /**
     * Editar los datos informativos de un proveedor.
     *
     * Verifica que el nuevo nombre no esté siendo utilizado por otro proveedor diferente.
     *
     * @param int $id_proveedor Identificador único del proveedor a editar.
     * @param string $nombre Nombre actualizado.
     * @param string $telefono Teléfono de contacto.
     * @param string $correo Correo electrónico.
     * @param string $direccion Dirección física.
     * @return void Emite 'add' si se actualiza o mensaje de advertencia.
     */
    public function editar($id_proveedor, $nombre, $telefono, $correo, $direccion) {
        $sql = "SELECT id_proveedor FROM proveedor WHERE nombre = :nombre AND id_proveedor <> :id_proveedor";
        $query = $this->acceso->prepare($sql);
        $query->execute([':nombre' => $nombre, ':id_proveedor' => $id_proveedor]);
        $this->objetos = $query->fetchAll();

        if (!empty($this->objetos)) {
            echo 'El nombre ya está en uso por otro proveedor.';
        } else {
            $sql = "UPDATE proveedor SET nombre = :nombre, telefono = :telefono, correo = :correo, direccion = :direccion 
                    WHERE id_proveedor = :id_proveedor";
            $query = $this->acceso->prepare($sql);
            if ($query->execute([
                ':id_proveedor' => $id_proveedor,
                ':nombre' => $nombre,
                ':telefono' => $telefono,
                ':correo' => $correo,
                ':direccion' => $direccion
            ])) {
                echo 'add';
            } else {
                echo 'Error al actualizar.';
            }
        }
    }

    /**
     * Eliminar un proveedor y remover su archivo de logotipo del almacenamiento local.
     *
     * Si el logo asignado no es la imagen predeterminada, lo remueve físicamente del disco.
     *
     * @param int $id Identificador del proveedor a remover.
     * @return void Emite 'borrado' o mensaje de error ante fallos.
     */
    public function borrar_prove($id) {
        try {
            if (!$this->acceso) {
                throw new Exception("No se pudo conectar a la base de datos");
            }

            $sql = "SELECT avatar FROM proveedor WHERE id_proveedor = :id_proveedor";
            $query = $this->acceso->prepare($sql);
            $query->execute([':id_proveedor' => $id]);
            $result = $query->fetch(PDO::FETCH_ASSOC);

            if ($result && isset($result['avatar'])) {
                $avatarNombre = $result['avatar'];
                if ($avatarNombre !== 'ProveedorDefault.png') {
                    $rutaAvatar = "../libs/img/proveedors/" . $avatarNombre;
                    if (file_exists($rutaAvatar)) {
                        @unlink($rutaAvatar);
                    }
                }
            }

            $sql = "DELETE FROM proveedor WHERE id_proveedor = :id_proveedor";
            $query = $this->acceso->prepare($sql);
            $query->execute([':id_proveedor' => $id]);
            echo 'borrado';
        } catch (PDOException $e) {
            echo 'Error al eliminar el proveedor: ' . $e->getMessage();
        } catch (Exception $e) {
            echo 'Error general: ' . $e->getMessage();
        }
    }

    /**
     * Actualizar la ruta del logotipo o avatar del proveedor.
     *
     * @param int $id Identificador del proveedor.
     * @param string $nombre Nombre de archivo de la imagen subida.
     * @return void
     */
    public function cambiar_avatar($id, $nombre) {
        $sql = "UPDATE proveedor SET avatar = :nombre WHERE id_proveedor = :id";
        $query = $this->acceso->prepare($sql);
        $query->execute([':id' => $id, ':nombre' => $nombre]);
    }

    /**
     * Obtener el catálogo completo de proveedores ordenados alfabéticamente.
     *
     * Utilizado para poblar selectores desplegables HTML (`<select>`) en la entrada de insumos y lotes.
     *
     * @return array Colección completa de proveedores registrados.
     */
    public function rellenar_proveedor() {
        $sql = "SELECT * FROM proveedor ORDER BY nombre ASC";
        $query = $this->acceso->prepare($sql);
        $query->execute();
        $this->objetos = $query->fetchAll();
        return $this->objetos;
    }
}
?>
