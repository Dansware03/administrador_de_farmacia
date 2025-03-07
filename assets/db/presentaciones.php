<?php
include_once 'conexion.php';
class presentacion {
    var $objetos;
    private $acceso;

    public function __construct() {
        $db = new Conexion();
        $this->acceso = $db->pdo;
    }

    function crear($nombre) {
        try {
            // Verifica si la presentación ya existe
            $sql = "SELECT id_presentacion FROM presentacion WHERE nombre = :nombre";
            $query = $this->acceso->prepare($sql);
            $query->execute(array(':nombre' => $nombre));

            // Si ya existe, no la agrega
            if ($query->rowCount() > 0) {
                echo 'no add';
            } else {
                // Si no existe, la agrega
                $sql = "INSERT INTO presentacion (nombre) VALUES (:nombre)";
                $query = $this->acceso->prepare($sql);
                if ($query->execute(array(':nombre' => $nombre))) {
                    echo 'add';
                }
            }
        } catch (PDOException $e) {
            echo 'Error al crear la presentación: ' . $e->getMessage();
        }
    }

    function buscar() {
        $consulta = isset($_POST['consulta']) ? $_POST['consulta'] : '';
        $sql = "SELECT * FROM presentacion WHERE nombre LIKE :consulta ORDER BY id_presentacion LIMIT 10";
        $query = $this->acceso->prepare($sql);
        $query->execute(array(':consulta' => "%$consulta%"));
        $this->objetos = $query->fetchAll();
        return $this->objetos;
    }

    function borrar_pre($id) {
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

    function editar($nombre, $id_editado) {
        try {
            // Verifica que el nombre no sea vacío antes de intentar actualizar
            if (!empty($nombre)) {
                $sql = "UPDATE presentacion SET nombre = :nombre WHERE id_presentacion = :id";
                $query = $this->acceso->prepare($sql);
                $query->execute(array(':id' => $id_editado, ':nombre' => $nombre));
                echo 'edit';
            } else {
                echo 'Nombre no válido para editar';
            }
        } catch (PDOException $e) {
            echo 'Error al editar la presentación: ' . $e->getMessage();
        }
    }

    function rellenar_presentacion() {
        $sql = "SELECT * FROM presentacion ORDER BY nombre asc";
        $query = $this->acceso->prepare($sql);
        $query->execute();
        $this->objetos = $query->fetchAll();
        return $this->objetos;
    }
}
?>
