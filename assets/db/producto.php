<?php
include_once 'conexion.php';
class Producto {
    var $objetos;
    private $acceso;

    public function __construct() {
        $db = new Conexion();
        $this->acceso = $db->pdo;
    }

    public function crear($nombre, $concentracion, $adicional, $precio, $avatar, $prod_lab, $prod_tip_prod, $prod_present) {
        try {
            $sql = "SELECT id_producto FROM producto WHERE nombre = :nombre and concentracion=:concentracion and adicional=:adicional and precio=:precio and avatar=:avatar and prod_lab=:laboratorio and prod_tip_prod=:tipo and prod_present=:presentacion";
            $query = $this->acceso->prepare($sql);
            $query->execute(array(':nombre' => $nombre, ':concentracion' => $concentracion, ':adicional' => $adicional, ':precio' => $precio, ':avatar' => $avatar, ':laboratorio' => $prod_lab, ':tipo' => $prod_tip_prod, ':presentacion' => $prod_present));
            $result = $query->fetchAll();

            if (!empty($result)) {
                throw new Exception('El producto ya existe.');
            }

            $sql = "INSERT INTO producto (nombre, concentracion, adicional, precio, avatar, prod_lab, prod_tip_prod, prod_present) VALUES (:nombre, :concentracion, :adicional, :precio, :avatar, :laboratorio, :tipo, :presentacion)";
            $query = $this->acceso->prepare($sql);

            if ($query->execute(array(
                ':nombre' => $nombre,
                ':concentracion' => $concentracion,
                ':adicional' => $adicional,
                ':precio' => $precio,
                ':avatar' => $avatar,
                ':laboratorio' => $prod_lab,
                ':tipo' => $prod_tip_prod,
                ':presentacion' => $prod_present
            ))) {
                echo 'add';
            } else {
                throw new Exception('Error al insertar el producto.');
            }
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }
    function buscar($consulta = '') {
        try {
            if (!empty($consulta)) {
                $sql = "SELECT
                    id_producto,
                    producto.nombre,
                    concentracion,
                    adicional,
                    precio,
                    laboratorio.nombre AS nombre_laboratorio,
                    tipo_producto.nombre AS tipo,
                    presentacion.nombre AS nombre_presentacion,
                    producto.avatar
                FROM producto
                JOIN laboratorio ON prod_lab = id_laboratorio
                JOIN tipo_producto ON prod_tip_prod = id_tip_prod
                JOIN presentacion ON prod_present = id_presentacion AND producto.nombre LIKE :consulta
                LIMIT 25";
                $query = $this->acceso->prepare($sql);
                $consulta = "%$consulta%";
                $query->bindValue(':consulta', $consulta, PDO::PARAM_STR);
            } else {
                $sql = "SELECT
                    id_producto,
                    producto.nombre,
                    concentracion,
                    adicional,
                    precio,
                    laboratorio.nombre AS nombre_laboratorio,
                    tipo_producto.nombre AS tipo,
                    presentacion.nombre AS nombre_presentacion,
                    producto.avatar
                FROM producto
                JOIN laboratorio ON prod_lab = id_laboratorio
                JOIN tipo_producto ON prod_tip_prod = id_tip_prod
                JOIN presentacion ON prod_present = id_presentacion
                ORDER BY producto.nombre LIMIT 25";
                $query = $this->acceso->prepare($sql);
            }
            $query->execute();
            $this->objetos = $query->fetchAll(PDO::FETCH_ASSOC);
            return $this->objetos;
        } catch (PDOException $e) {
            // Manejo de errores en la ejecución de la consulta
            echo "Error: " . $e->getMessage();
            return false;
        }
    }
    // function cambiar_logo($id,$nombre) {
    //     $sql = "SELECT avatar FROM producto where id_producto =:id";
    //     $query = $this->acceso->prepare($sql);
    //     $query->execute(array(':id'=>$id));
    //     $this->objetos = $query->fetchAll();
    //     $sql = "UPDATE producto SET avatar=:nombre where id_producto =:id";
    //     $query = $this->acceso->prepare($sql);
    //     $query->execute(array(':id' => $id, ':nombre' => $nombre));
    //     return $this->objetos;
    // }
    // function borrar_product($id) {
    //     $sql = "DELETE FROM producto where id_producto =:id";
    //     $query = $this->acceso->prepare($sql);
    //     $query->execute(array(':id'=>$id));
    //     if (!empty($query->execute(array(':id'=>$id)))) {
    //         echo 'borrado';
    //     }
    //     else {
    //         echo 'no-borrado';
    //     }
    // }
    // function editar($nombre,$id_editado) {
    //     $sql = "UPDATE producto SET nombre=:nombre WHERE id_producto =:id";
    //     $query = $this->acceso->prepare($sql);
    //     $query->execute(array(':id'=>$id_editado,':nombre'=>$nombre));
    //     echo 'edit';
    // }
};
?>