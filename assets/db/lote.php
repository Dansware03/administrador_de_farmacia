<?php
include_once 'conexion.php';
class Lote{
    var $objetos;
    private $acceso;
    public function __construct()
    {
        $db = new Conexion();
        $this->acceso = $db->pdo;
    }
    function crear($id_producto, $proveedor, $cod_lote, $stock, $vencimiento) {
        $sql = "INSERT INTO lote (cod_lote, stock, vencimiento, id_lote_prod, lote_id_prov) VALUES (:cod_lote, :stock, :vencimiento, :id_producto, :id_proveedor)";
        $query = $this->acceso->prepare($sql);
        $query->execute(array(
            'cod_lote' => $cod_lote,
            'stock' => $stock,
            'vencimiento' => $vencimiento,
            'id_producto' => $id_producto,
            'id_proveedor' => $proveedor
        ));
        echo 'add';
    }
    function buscar() {
        if (!empty($_POST['consulta'])) {
            $consulta = $_POST['consulta'];
            $sql = "SELECT id_lote,cod_lote,stock,vencimiento,concentracion,adicional,producto.nombre AS prod_nom, laboratorio.nombre AS lab_nom, tipo_producto.nombre AS tip_nom, presentacion.nombre AS pre_nom, proveedor.nombre AS pro_nom, producto.avatar AS logo FROM lote
            JOIN proveedor ON lote_id_prov=id_proveedor
            JOIN producto ON id_lote_prod=id_producto
            JOIN laboratorio ON prod_lab=id_laboratorio
            JOIN tipo_producto ON prod_tip_prod=id_tip_prod
            JOIN presentacion ON prod_present=id_presentacion AND producto.nombre LIKE :consulta ORDER BY producto.nombre LIMIT 25";
            $query = $this->acceso->prepare($sql);
            $query->execute(array(':consulta'=>"%$consulta%"));
            $this->objetos = $query->fetchAll();
            return $this->objetos;
        } else {
            $sql = "SELECT id_lote,cod_lote,stock,vencimiento,concentracion,adicional,producto.nombre AS prod_nom, laboratorio.nombre AS lab_nom, tipo_producto.nombre AS tip_nom, presentacion.nombre AS pre_nom, proveedor.nombre AS pro_nom, producto.avatar AS logo FROM lote
            JOIN proveedor ON lote_id_prov=id_proveedor
            JOIN producto ON id_lote_prod=id_producto
            JOIN laboratorio ON prod_lab=id_laboratorio
            JOIN tipo_producto ON prod_tip_prod=id_tip_prod
            JOIN presentacion ON prod_present=id_presentacion AND producto.nombre NOT LIKE '' ORDER BY producto.nombre LIMIT 25";
            $query = $this->acceso->prepare($sql);
            $query->execute();
            $this->objetos = $query->fetchAll();
            return $this->objetos;
        }
    }
    function editar($id_lote, $stock) {
        $sql = "UPDATE lote SET stock = :stock WHERE id_lote = :id_lote";
        $query = $this->acceso->prepare($sql);
        $query->execute(array(
            'stock' => $stock,
            'id_lote' => $id_lote,
        ));
        echo 'edit';
    }
    function borrar_lote($id) {
        try {
            // Comienza la transacción
            $this->acceso->beginTransaction();
            // Verifica si el lote existe antes de eliminarlo
            $existe = $this->verificar_existencia_lote($id);
            if ($existe) {
                // Elimina el lote
                $sql = "DELETE FROM lote WHERE id_lote = :id";
                $query = $this->acceso->prepare($sql);
                $query->execute(array(':id' => $id));
                // Confirma la transacción
                $this->acceso->commit();
                echo 'borrado';
            } else {
                echo 'El lote no existe';
            }
        } catch (PDOException $e) {
            // Ocurrió un error, revierte la transacción
            $this->acceso->rollBack();
            echo 'Error al borrar el lote: ' . $e->getMessage();
        }
    }
    function verificar_existencia_lote($id) {
        $sql = "SELECT COUNT(*) FROM lote WHERE id_lote = :id";
        $query = $this->acceso->prepare($sql);
        $query->execute(array(':id' => $id));
        $resultado = $query->fetchColumn();
        return ($resultado > 0);
    }
}
?>
