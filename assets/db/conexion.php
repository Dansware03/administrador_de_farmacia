<?php
class Conexion {
    private $db_path = "../../../../apps/phpLiteAdmin/bdfarmacia.db"; // Ruta al archivo SQLite
    public $pdo = null;
    private $atributos = [
        PDO::ATTR_CASE => PDO::CASE_NATURAL,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_ORACLE_NULLS => PDO::NULL_EMPTY_STRING,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
    ];
    public function __construct() {
        $this->pdo = new PDO("mysql:dbname={$this->db};host={$this->servidor};port={$this->puerto};charset={$this->charset}", $this->usuario, $this->contrasena, $this->atributos);
    }
    public function __destruct() {
        $this->pdo = null;
    }
}
?>