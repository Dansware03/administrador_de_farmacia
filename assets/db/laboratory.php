<?php
include_once 'conexion.php';

class Laboratorio {
    private $acceso;
    public $objetos;

    public function __construct() {
        $db = new Conexion();
        $this->acceso = $db->pdo;
    }

    public function crear($nombre) {
        if (empty($nombre)) {
            echo json_encode(['status' => 'error', 'message' => 'El nombre del laboratorio no puede estar vacío.']);
            return;
        }

        // Verificar si existe otro laboratorio con el mismo nombre
        $sql_verificar = "SELECT id_laboratorio FROM laboratorio WHERE nombre = :nombre";
        $query_verificar = $this->acceso->prepare($sql_verificar);
        $query_verificar->execute([':nombre' => $nombre]);
        $objetos_verificar = $query_verificar->fetchAll();

        if (!empty($objetos_verificar)) {
            echo json_encode(['status' => 'error', 'message' => 'Ya existe otro laboratorio con el mismo nombre']);
        } else {
            // Crear el laboratorio
            $sql = "INSERT INTO laboratorio (nombre) VALUES (:nombre)";
            $query = $this->acceso->prepare($sql);

            if ($query->execute([':nombre' => $nombre])) {
                echo json_encode(['status' => 'success', 'message' => 'Laboratorio creado con éxito']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Error al crear el laboratorio']);
            }
        }
    }

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

    public function borrar_lab($id) {
        // Verificar si el laboratorio está asociado a algún producto
        $verificar_sql = "SELECT COUNT(*) as count FROM producto WHERE prod_lab = :id";
        $verificar_query = $this->acceso->prepare($verificar_sql);
        $verificar_query->execute([':id' => $id]);
        $count = $verificar_query->fetch(PDO::FETCH_ASSOC)['count'];

        if ($count > 0) {
            echo json_encode(['status' => 'error', 'message' => 'No se puede eliminar el laboratorio. Está siendo utilizado por al menos un producto.']);
        } else {
            // Proceder con la eliminación si no hay productos asociados
            $sql = "DELETE FROM laboratorio WHERE id_laboratorio = :id";
            $query = $this->acceso->prepare($sql);
            if ($query->execute([':id' => $id])) {
                echo json_encode(['status' => 'success', 'message' => 'Laboratorio borrado con éxito']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Error al borrar el laboratorio']);
            }
        }
    }
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
        } else {
            $sql_actualizar = "UPDATE laboratorio SET nombre = :nombre WHERE id_laboratorio = :id";
            $query_actualizar = $this->acceso->prepare($sql_actualizar);
            if ($query_actualizar->execute([':id' => $id_editado, ':nombre' => $nombre])) {
                echo json_encode(['status' => 'success', 'message' => 'Laboratorio editado con éxito']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Error al editar el laboratorio']);
            }
        }
    }

    public function rellenar_laboratorio() {
        $sql = "SELECT * FROM laboratorio ORDER BY nombre ASC";
        $query = $this->acceso->prepare($sql);
        $query->execute();
        $this->objetos = $query->fetchAll();
        return $this->objetos;
    }
}

?>
