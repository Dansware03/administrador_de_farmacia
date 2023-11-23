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
        $this->objetos = $query->fetchAll();
        if (!empty($this->objetos)) {
            echo 'no add';
        } else {
            $sql = "INSERT INTO presentacion (nombre) VALUES (:nombre)";
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
        $sql = "DELETE FROM presentacion WHERE id_presentacion = :id";
        $query = $this->acceso->prepare($sql);
        $query->execute(array(':id'=>$id));
        if (!empty($query->execute(array(':id'=>$id)))) {
            echo 'borrado';
        }
        else {
            echo 'no-borrado';
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
