<?php
class Conexion {
    private $db_path = "../db/simap.db"; // Ruta al archivo SQLite SIMAP
    public $pdo = null;
    private $atributos = [
        PDO::ATTR_CASE => PDO::CASE_NATURAL,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_ORACLE_NULLS => PDO::NULL_EMPTY_STRING,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
    ];
    public function __construct() {
        $this->pdo = new PDO("sqlite:{$this->db_path}", null, null, $this->atributos);
    }
    public function __destruct() {
        $this->pdo = null;
    }
}
?>