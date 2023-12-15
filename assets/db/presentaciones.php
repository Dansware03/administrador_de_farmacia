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
        $sql = "SELECT id_presentacion FROM presentacion WHERE nombre = :nombre";
        $query = $this->acceso->prepare($sql);
        $query->execute(array(':nombre' => $nombre));
        if ($query->rowCount() > 0) {
            echo 'no add';
        } else {
            $sql = "INSERT INTO presentacion (nombre) VALUES (:nombre)";
            $query = $this->acceso->prepare($sql);
            if ($query->execute(array(':nombre' => $nombre))) {
                echo 'add';
            }
        }
    }
    function buscar() {
        if (!empty($_POST['consulta'])) {
            $consulta = $_POST['consulta'];
            $sql = "SELECT * FROM presentacion WHERE nombre LIKE :consulta";
            $query = $this->acceso->prepare($sql);
            $query->execute(array(':consulta' => "%$consulta"));
            $this->objetos = $query->fetchAll();
            return $this->objetos;
        } else {
            $sql = "SELECT * FROM presentacion WHERE nombre NOT LIKE '' ORDER BY id_presentacion LIMIT 10";
            $query = $this->acceso->prepare($sql);
            $query->execute();
            $this->objetos = $query->fetchAll();
            return $this->objetos;
        }
    }
    function borrar_pre($id) {
        $verificar_sql = "SELECT COUNT(*) as count FROM producto WHERE prod_present = :id";
        $verificar_query = $this->acceso->prepare($verificar_sql);
        // Asegúrate de que estás usando el tipo de dato correcto para $id
        $verificar_query->bindValue(':id', $id, PDO::PARAM_INT);
        $verificar_query->execute();
        $count = $verificar_query->fetch(PDO::FETCH_ASSOC)['count'];
        if ($count > 0) {
            echo "No se puede eliminar la presentación. Está siendo utilizada por al menos un producto.";
        } else {
            $sql = "DELETE FROM presentacion WHERE id_presentacion = :id";
            $query = $this->acceso->prepare($sql);
            // Asegúrate de que estás usando el tipo de dato correcto para $id
            $query->bindValue(':id', $id, PDO::PARAM_INT);
            $query->execute();
            if ($query->rowCount() > 0) {
                echo 'borrado';
            } else {
                echo 'no-borrado';
            }
        }
    }
    function editar($nombre, $id_editado) {
        $sql = "UPDATE presentacion SET nombre = :nombre WHERE id_presentacion = :id";
        $query = $this->acceso->prepare($sql);
        $query->execute(array(':id' => $id_editado, ':nombre' => $nombre));
        echo 'edit';
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
