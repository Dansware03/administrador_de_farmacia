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
        $verificar_sql = "SELECT COUNT(*) as count FROM producto WHERE prod_lab = :id";
        $verificar_query = $this->acceso->prepare($verificar_sql);
        $verificar_query->execute(array(':id' => $id));
        $count = $verificar_query->fetch(PDO::FETCH_ASSOC)['count'];
        if ($count > 0) {
            echo "No se puede eliminar el laboratorio. Está siendo utilizado por al menos un producto.";
        } else {
            $sql = "DELETE FROM laboratorio WHERE id_laboratorio = :id";
            $query = $this->acceso->prepare($sql);
            $query->execute(array(':id' => $id));
            if ($query->rowCount() > 0) {
                echo 'borrado';
            } else {
                echo 'no-borrado';
            }
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