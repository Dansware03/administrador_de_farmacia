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
    function buscar(){
        if (!empty($_POST['consulta'])) {
            $consulta=$_POST['consulta'];
            $sql="SELECT * FROM usuario join tipo_us ON us_tipo=id_tipo_us where nombre_us LIKE :consulta";
            $query = $this->acceso->prepare($sql);
            $query->execute(array(':consulta'=>"%$consulta%"));
            $this->objetos=$query->fetchAll();
            return $this->objetos;
        } else {
            $sql="SELECT * FROM usuario join tipo_us ON us_tipo=id_tipo_us where nombre_us NOT LIKE '' ORDER BY id_usuario LIMIT 25";
            $query = $this->acceso->prepare($sql);
            $query->execute();
            $this->objetos=$query->fetchAll();
            return $this->objetos;
        }
    }
    function crear($nombre, $apellido, $edad, $ci, $genero, $pass, $tipo, $avatar) {
        $sql = "SELECT id_usuario FROM usuario WHERE ci_us = :ci";
        $query = $this->acceso->prepare($sql);
        $query->execute(array(':ci' => $ci));
        $this->objetos = $query->fetchAll();
        
        if (!empty($this->objetos)) {
            echo 'no add';
        }else {
            $sql = "INSERT INTO usuario (nombre_us, apellidos_us, edad, ci_us, genero_us, contrasena_us, us_tipo, avatar) VALUES (:nombre, :apellido, :edad, :ci, :genero, :pass, :tipo, :avatar)";
            $query = $this->acceso->prepare($sql);
            if ($query->execute(array(
                ':nombre' => $nombre,
                ':apellido' => $apellido,
                ':edad' => $edad,
                ':ci' => $ci,
                ':genero' => $genero,
                ':pass' => $pass,
                ':tipo' => $tipo,
                ':avatar' => $avatar
            )));
            $this->objetos=$query->fetchAll();
            echo 'add';
        }
    }
    function ascender($pass, $id_up, $id_usuario) {
        $sql = "SELECT id_usuario FROM usuario where id_usuario=:id_usuario and contrasena_us=:pass";
        $query = $this->acceso->prepare($sql);
        $query->execute(array(':id_usuario'=>$id_usuario,':pass'=>$pass));
        $this->objetos = $query->fetchAll();
        if (!empty($this->objetos)) {
            $tipo=1;
            $sql="UPDATE usuario SET us_tipo=:tipo where id_usuario=:id";
            $query=$this->acceso->prepare($sql);
            $query->execute(array(':id'=>$id_up,':tipo'=>$tipo));
            echo 'up';
        } else {
            echo 'no-up';
        }
    }
    function descender($pass, $id_donw, $id_usuario) {
        $sql = "SELECT id_usuario FROM usuario where id_usuario=:id_usuario and contrasena_us=:pass";
        $query = $this->acceso->prepare($sql);
        $query->execute(array(':id_usuario'=>$id_usuario,':pass'=>$pass));
        $this->objetos = $query->fetchAll();
        if (!empty($this->objetos)) {
            $tipo = 2;
            $sql = "UPDATE usuario SET us_tipo=:tipo where id_usuario=:id";
            $query = $this->acceso->prepare($sql);
            $query->execute(array(':id'=> $id_donw,':tipo'=>$tipo));
            echo 'donw';
        } else {
            echo 'no-donw';
        }
    }
    function delete($pass,$id_delete,$id_usuario) {
        $sql = "SELECT id_usuario FROM usuario where id_usuario=:id_usuario and contrasena_us=:pass";
        $query = $this->acceso->prepare($sql);
        $query->execute(array(':id_usuario'=>$id_usuario,':pass'=>$pass));
        $this->objetos = $query->fetchAll();
        if (!empty($this->objetos)) {
            $sql = "DELETE FROM usuario where id_usuario=:id";
            $query = $this->acceso->prepare($sql);
            $query->execute(array(':id'=> $id_delete));
            echo 'delete';
        } else {
            echo 'no-delete';
        }
    }
};
?>