<?php
include_once 'conexion.php';
class laboratorio {
    var $objetos;
    private $acceso;
    public function __construct() {
        $db = new Conexion();
        $this->acceso = $db->pdo;
    }
    function crear($nombre) {
        $sql = "SELECT id_laboratorio FROM laboratorio WHERE nombre = :nombre";
        $query = $this->acceso->prepare($sql);
        $query->execute(array(':nombre' => $nombre));
        $this->objetos = $query->fetchAll();
        if (!empty($this->objetos)) {
            echo 'no add';
        }else {
            $sql = "INSERT INTO laboratorio (nombre) VALUES (:nombre)";
            $query = $this->acceso->prepare($sql);
            if ($query->execute(array(
                ':nombre' => $nombre
            )));
            $this->objetos=$query->fetchAll();
            echo 'add';
        }
    }
    function buscar(){
        if (!empty($_POST['consulta'])) {
            $consulta=$_POST['consulta'];
            $sql="SELECT * FROM laboratorio where nombre LIKE :consulta";
            $query = $this->acceso->prepare($sql);
            $query->execute(array(':consulta'=>"%$consulta%"));
            $this->objetos=$query->fetchAll();
            return $this->objetos;
        } else {
            $sql="SELECT * FROM laboratorio where nombre NOT LIKE '' ORDER BY id_laboratorio LIMIT 10";
            $query = $this->acceso->prepare($sql);
            $query->execute();
            $this->objetos=$query->fetchAll();
            return $this->objetos;
        }
    }
    function borrar_lab($id) {
        $sql = "DELETE FROM laboratorio where id_laboratorio=:id";
        $query = $this->acceso->prepare($sql);
        $query->execute(array(':id'=>$id));
        if (!empty($query->execute(array(':id'=>$id)))) {
            echo 'borrado';
        }
        else {
            echo 'no-borrado';
        }
    }
    function editar($nombre,$id_editado) {
        $sql = "UPDATE laboratorio SET nombre=:nombre WHERE id_laboratorio=:id";
        $query = $this->acceso->prepare($sql);
        $query->execute(array(':id'=>$id_editado,':nombre'=>$nombre));
        echo 'edit';
    }
    function rellenar_laboratorio() {
        $sql = "SELECT * FROM laboratorio ORDER BY nombre asc";
        $query = $this->acceso->prepare($sql);
        $query->execute();
        $this->objetos = $query->fetchAll();
        return $this->objetos;
    }
};
?>