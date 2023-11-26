<?php
include_once 'conexion.php';
class tipo_producto {
    var $objetos;
    private $acceso;
    public function __construct() {
        $db = new Conexion();
        $this->acceso = $db->pdo;
    }
    function crear($nombre) {
        $sql = "SELECT id_tip_prod FROM tipo_producto WHERE nombre = :nombre";
        $query = $this->acceso->prepare($sql);
        $query->execute(array(':nombre' => $nombre));
        $this->objetos = $query->fetchAll();
        if (!empty($this->objetos)) {
            echo 'no add';
        } else {
            $sql = "INSERT INTO tipo_producto (nombre) VALUES (:nombre)";
            $query = $this->acceso->prepare($sql);
            if ($query->execute(array(':nombre' => $nombre))) {
                $this->objetos = $query->fetchAll();
                echo 'add';
            }
        }
    }
    function buscar() {
        if (!empty($_POST['consulta'])) {
            $consulta = $_POST['consulta'];
            $sql = "SELECT * FROM tipo_producto WHERE nombre LIKE :consulta";
            $query = $this->acceso->prepare($sql);
            $query->execute(array(':consulta' => "%$consulta"));
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
    function borrar_type($id) {
        $verificar_sql = "SELECT COUNT(*) as count FROM producto WHERE prod_tip_prod = :id";
        $verificar_query = $this->acceso->prepare($verificar_sql);
        $verificar_query->execute(array(':id' => $id));
        $count = $verificar_query->fetch(PDO::FETCH_ASSOC)['count'];
        if ($count > 0) {
            echo "No se puede eliminar el tipo de producto. Está siendo utilizado por al menos un producto.";
        } else {
            $sql = "DELETE FROM tipo_producto WHERE id_tip_prod = :id";
            $query = $this->acceso->prepare($sql);
            $query->execute(array(':id' => $id));
            if ($query->rowCount() > 0) {
                echo 'Tipo de producto borrado exitosamente.';
            } else {
                echo 'Error al intentar borrar el tipo de producto.';
            }
        }
    }
    function editar($nombre, $id_editado) {
        $sql = "UPDATE tipo_producto SET nombre = :nombre WHERE id_tip_prod = :id";
        $query = $this->acceso->prepare($sql);
        $query->execute(array(':id' => $id_editado, ':nombre' => $nombre));
        echo 'edit';
    }
    function rellenar_type() {
        $sql = "SELECT * FROM tipo_producto ORDER BY nombre asc";
        $query = $this->acceso->prepare($sql);
        $query->execute();
        $this->objetos = $query->fetchAll();
        return $this->objetos;
    }
}
?>
