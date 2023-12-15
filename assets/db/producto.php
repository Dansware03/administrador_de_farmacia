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
            echo $e->getMessage();
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
                    producto.avatar, prod_lab, prod_tip_prod, prod_present
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
                    producto.avatar, prod_lab, prod_tip_prod, prod_present
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
            echo "Error: " . $e->getMessage();
            return false;
        }
    }
    function cambiar_avatar($id,$nombre) {
        $sql = "UPDATE producto SET avatar=:nombre where id_producto =:id";
        $query = $this->acceso->prepare($sql);
        $query->execute(array(':id' => $id, ':nombre' => $nombre));
    }
    public function editar($id_edit_prod, $nombre, $concentracion, $adicional, $precio, $prod_lab, $prod_tip_prod, $prod_present) {
        try {
            $sql_select = "SELECT nombre, concentracion, adicional, precio, prod_lab, prod_tip_prod, prod_present FROM producto WHERE id_producto = :id_edit_prod";
            $query_select = $this->acceso->prepare($sql_select);
            $query_select->execute(array(':id_edit_prod' => $id_edit_prod));
            $existing_data = $query_select->fetch(PDO::FETCH_ASSOC);
            if ($existing_data === false) {
                throw new Exception('No se encontró el producto con el ID especificado.');
            }
            if (
                $existing_data['nombre'] == $nombre &&
                $existing_data['concentracion'] == $concentracion &&
                $existing_data['adicional'] == $adicional &&
                $existing_data['precio'] == $precio &&
                $existing_data['prod_lab'] == $prod_lab &&
                $existing_data['prod_tip_prod'] == $prod_tip_prod &&
                $existing_data['prod_present'] == $prod_present
            ) {
                throw new Exception('El Producto Es Igual. No se realizaron cambios.');
            }
            $sql_update = "UPDATE producto SET nombre = :nombre, concentracion = :concentracion, adicional = :adicional, precio = :precio, prod_lab = :laboratorio, prod_tip_prod = :tipo, prod_present = :presentacion WHERE id_producto = :id_edit_prod";
            $query_update = $this->acceso->prepare($sql_update);
            $query_update->execute(array(
                ':id_edit_prod' => $id_edit_prod,
                ':nombre' => $nombre,
                ':concentracion' => $concentracion,
                ':adicional' => $adicional,
                ':precio' => $precio,
                ':laboratorio' => $prod_lab,
                ':tipo' => $prod_tip_prod,
                ':presentacion' => $prod_present
            ));
            if ($query_update->rowCount() > 0) {
                echo 'edit';
            } else {
                throw new Exception('No se realizó ninguna actualización. Puede ser que los datos sean iguales a los existentes.');
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
    function borrar_produts($id){
        try {
            $sql = "SELECT avatar FROM producto WHERE id_producto = :id";
            $query = $this->acceso->prepare($sql);
            $query->execute(array(':id' => $id));
            $result = $query->fetch(PDO::FETCH_ASSOC);
            if ($result && isset($result['avatar'])) {
                $avatarNombre = $result['avatar'];
                if ($avatarNombre !== 'ProductDefault.png') {
                    $rutaAvatar = "../libs/img/product/" . $avatarNombre;
                    if (file_exists($rutaAvatar)) {
                        unlink($rutaAvatar);
                    }
                }
            }
            $sql = "DELETE FROM producto WHERE id_producto = :id";
            $query = $this->acceso->prepare($sql);
            $query->execute(array(':id' => $id));
            if ($query->rowCount() > 0) {
                echo 'borrado';
            } else {
                echo json_encode(['status' => 'error', 'message' => 'No se pudo eliminar el producto.']);
            }
        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => 'Error en el servidor.']);
            // Puedes agregar un mensaje de error o log aquí
        }
    }
};
?>