<?php
/**
 * Gestor Central de Conexión a Base de Datos SQLite (PDO)
 *
 * Configura y provee la instancia PDO compartida para la base de datos local
 * del sistema SIMAP (simap.db). Garantiza integridad referencial mediante claves foráneas,
 * manejo estricto de excepciones y tiempos de espera para evitar bloqueos en modo WAL.
 *
 * Flujo de Integración:
 * 1. Inicialización por Modelos del Sistema (`Despacho`, `Laboratorio`, `Producto`, etc.).
 * 2. Carga de archivo de base de datos `simap.db` en el mismo directorio (`__DIR__`).
 * 3. Configuración de atributos PDO (Excepciones, Fetch Object, Timeout de 5s).
 * 4. Activación obligatoria de `PRAGMA foreign_keys = ON;`.
 *
 * @package SIMAP\Database
 * @author Grupo de Proyecto
 * @version 1.0.0
 */
class Conexion {
    /**
     * @var string Ruta absoluta al archivo de base de datos SQLite.
     */
    private $db_path;

    /**
     * @var PDO|null Instancia activa de conexión a la base de datos.
     */
    public $pdo = null;

    /**
     * @var array Opciones de configuración de atributos de PDO.
     */
    private $atributos = [
        PDO::ATTR_CASE => PDO::CASE_NATURAL,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_ORACLE_NULLS => PDO::NULL_EMPTY_STRING,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
        PDO::ATTR_TIMEOUT => 5,
    ];

    /**
     * Constructor de la clase Conexion.
     *
     * Inicializa el conector PDO con SQLite y fuerza la activación de llaves foráneas.
     *
     * @throws PDOException Si no se puede abrir o crear el archivo de base de datos.
     */
    public function __construct() {
        $this->db_path = __DIR__ . '/simap.db';
        $this->pdo = new PDO("sqlite:{$this->db_path}", null, null, $this->atributos);
        $this->pdo->exec("PRAGMA foreign_keys = ON;");
    }

    /**
     * Destructor de la clase Conexion.
     *
     * Libera la instancia PDO cerrando la conexión con SQLite.
     */
    public function __destruct() {
        $this->pdo = null;
    }
}
?>