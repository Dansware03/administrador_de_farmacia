<?php
include_once 'conexion.php';
class Proveedor{
    var $objetos;
    private $acceso;
    public function __construct()
    {
        $db = new Conexion();
        $this->acceso = $db->pdo;
    }
    function crear($nombre, $telefono, $correo,$direccion, $avatar) {
        $sql = "SELECT id_proveedor FROM proveedor WHERE nombre = :nombre";
        $query = $this->acceso->prepare($sql);
        $query->execute(array(':nombre' => $nombre));
        $this->objetos = $query->fetchAll();
        if (!empty($this->objetos)) {
            echo 'no add';
        } else {
            $sql = "INSERT INTO proveedor (nombre, telefono, correo, direccion,avatar) VALUES (:nombre, :telefono, :correo, :direccion, :avatar)";
            $query = $this->acceso->prepare($sql);
            if ($query->execute(array(
                ':nombre' => $nombre,
                ':telefono' => $telefono,
                ':correo' => $correo,
                ':direccion' => $direccion,
                ':avatar' => $avatar
            ))) {
                echo 'add';
            }
        }
    }
    function buscar($consulta = '') {
        try {
            if (!empty($consulta)) {
                $sql = "SELECT
                    id_proveedor,
                    nombre,
                    telefono,
                    correo,
                    direccion,
                    avatar
                FROM proveedor
                WHERE nombre LIKE :consulta
                LIMIT 25";
                $query = $this->acceso->prepare($sql);
                $consulta = "%$consulta%";
                $query->bindValue(':consulta', $consulta, PDO::PARAM_STR);
            } else {
                $sql = "SELECT
                    id_proveedor,
                    nombre,
                    telefono,
                    correo,
                    direccion,
                    avatar
                FROM proveedor
                ORDER BY nombre LIMIT 25";
                $query = $this->acceso->prepare($sql);
            }
            $query->execute();
            $this->objetos = $query->fetchAll(PDO::FETCH_ASSOC);
            return $this->objetos;
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }
    function editar($id_proveedor, $nombre, $telefono, $correo, $direccion) {
        $sql = "SELECT id_proveedor FROM proveedor WHERE nombre = :nombre AND id_proveedor <> :id_proveedor";
        $query = $this->acceso->prepare($sql);
        $query->execute(array(':nombre' => $nombre, ':id_proveedor' => $id_proveedor));
        $this->objetos = $query->fetchAll();
        if (!empty($this->objetos)) {
            echo 'El nombre ya está en uso por otro proveedor.';
        } else {
            $sql = "UPDATE proveedor SET nombre = :nombre, telefono = :telefono, correo = :correo, direccion = :direccion WHERE id_proveedor = :id_proveedor";
            $query = $this->acceso->prepare($sql);
            if ($query->execute(array(
                ':id_proveedor' => $id_proveedor,
                ':nombre' => $nombre,
                ':telefono' => $telefono,
                ':correo' => $correo,
                ':direccion' => $direccion
            ))) {
                echo 'add';
            } else {
                echo 'Error al actualizar.';
            }
        }
    }
    function borrar_prove($id) {
        try {
            // Verificar la conexión a la base de datos
            if (!$this->acceso) {
                throw new Exception("No se pudo conectar a la base de datos");
            }
            // Obtener el nombre de la imagen del proveedor
            $sql = "SELECT avatar FROM proveedor WHERE id_proveedor = :id_proveedor";
            $query = $this->acceso->prepare($sql);
            $query->execute(array(':id_proveedor' => $id));
            $result = $query->fetch(PDO::FETCH_ASSOC);
            if ($result && isset($result['avatar'])) {
                $avatarNombre = $result['avatar'];
                if ($avatarNombre !== 'ProveedorDefault.png') {
                    // Eliminar la imagen del proveedor
                    $rutaAvatar = "../libs/img/proveedors/" . $avatarNombre;
                    if (file_exists($rutaAvatar)) {
                        unlink($rutaAvatar);
                    }
                }
            }
            // Eliminar el proveedor
            $sql = "DELETE FROM proveedor WHERE id_proveedor = :id_proveedor";
            $query = $this->acceso->prepare($sql);
            $query->execute(array(':id_proveedor' => $id));
            echo 'borrado';
        } catch (PDOException $e) {
            echo 'Error al eliminar el proveedor: ' . $e->getMessage();
        } catch (Exception $e) {
            echo 'Error general: ' . $e->getMessage();
        }
    }
    function cambiar_avatar($id,$nombre) {
        $sql = "UPDATE proveedor SET avatar=:nombre where id_proveedor =:id";
        $query = $this->acceso->prepare($sql);
        $query->execute(array(':id' => $id, ':nombre' => $nombre));
    }
    function rellenar_proveedor() {
        $sql = "SELECT * FROM proveedor ORDER BY nombre asc";
        $query = $this->acceso->prepare($sql);
        $query->execute();
        $this->objetos = $query->fetchAll();
        return $this->objetos;
    }
}
?>
