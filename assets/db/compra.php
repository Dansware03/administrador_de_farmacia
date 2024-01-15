<?php
include_once 'conexion.php';
class Compra {
    private $acceso;
    public function __construct() {
        $db = new Conexion();
        $this->acceso = $db->pdo;
    }
    
}
?>
