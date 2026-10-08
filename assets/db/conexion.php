<?php
class Conexion {
    private $db_path;
    public $pdo = null;
    private $atributos = [
        PDO::ATTR_CASE => PDO::CASE_NATURAL,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_ORACLE_NULLS => PDO::NULL_EMPTY_STRING,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
        PDO::ATTR_TIMEOUT => 5,
    ];

    public function __construct() {
        $this->db_path = __DIR__ . '/simap.db';
        $this->pdo = new PDO("sqlite:{$this->db_path}", null, null, $this->atributos);
        $this->pdo->exec("PRAGMA foreign_keys = ON;");
    }

    public function __destruct() {
        $this->pdo = null;
    }
}
?>