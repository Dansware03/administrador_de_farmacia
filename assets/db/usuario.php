<?php
include_once 'conexion.php';
class Usuario {
    var $objetos;
    private $acceso;
    public function __construct() {
        $db = new Conexion();
        $this->acceso = $db->pdo;
    }
    function Loguearse($ci, $pass) {
        $sql = "SELECT * FROM usuario INNER JOIN tipo_us ON us_tipo = id_tipo_us WHERE ci_us = :ci AND contrasena_us = :pass";
        $query = $this->acceso->prepare($sql);
        $query->execute(array(':ci' => $ci, ':pass' => $pass));
        $this->objetos = $query->fetchAll(); // Corrección aquí
        return $this->objetos;
    }
    function obtener_datos($id) {
        $sql = "SELECT * FROM usuario join tipo_us on us_tipo=id_tipo_us and id_usuario=:id";
        $query = $this->acceso->prepare($sql);
        $query->execute(array(':id' => $id));
        $this->objetos = $query->fetchAll();
        return $this->objetos;
    }
    function editar ($id_usuario,$telefono,$correo,$genero,$info) {
        $sql = "UPDATE usuario SET telefono_us=:telefono, correo_us=:correo, genero_us=:genero, info_us=:info where id_usuario=:id";
        $query = $this->acceso->prepare($sql);
        $query->execute(array(':id'=>$id_usuario,':telefono'=>$telefono,':correo'=>$correo,':genero'=>$genero,':info'=>$info));
    }
    function cambiar_contra ($id_usuario,$oldpass,$newpass) {
        $sql = "SELECT * FROM usuario where id_usuario=:id and contrasena_us=:oldpass";
        $query = $this->acceso->prepare($sql);
        $query->execute(array(':id'=>$id_usuario,':oldpass'=>$oldpass));
        $this->objetos = $query->fetchAll();
        if (!empty($this->objetos)){
            $sql = "UPDATE usuario SET contrasena_us=:newpass where id_usuario=:id";
            $query=$this->acceso->prepare($sql);
            $query->execute(array(':id'=>$id_usuario,':newpass'=>$newpass));
            echo 'update';
        } else {
            echo 'noupdate';
        }
    }
    function cambiar_foto ($id_usuario,$nombre) {
        $sql = "SELECT avatar FROM usuario where id_usuario=:id";
        $query = $this->acceso->prepare($sql);
        $query->execute(array(':id'=>$id_usuario));
        $this->objetos = $query->fetchAll();

            $sql = "UPDATE usuario SET avatar=:nombre where id_usuario=:id";
            $query=$this->acceso->prepare($sql);
            $query->execute(array(':id'=>$id_usuario,':nombre'=>$nombre));
            return $this->objetos;
    }
}
?>