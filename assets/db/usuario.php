<?php
/**
 * Modelo de Datos y Lógica de Negocio para Usuarios y Seguridad
 *
 * Administra el ciclo de vida de los operadores y administradores del sistema SIMAP.
 * Implementa autenticación robusta mediante Bcrypt (`password_verify`, `password_hash`),
 * gestión del perfil personal, control de roles (Administrador = 1, Secretario = 2),
 * transacciones atómicas para operaciones sensibles y reglas de negocio críticas:
 * prevención de auto-descenso, prevención de auto-eliminación y protección del último administrador.
 *
 * Flujo de Integración Extremo a Extremo:
 * 1. UI/Cliente: Formulario de perfil (`assets/libs/js/usuario.js`) y gestión de usuarios (`gestion_user.js`).
 * 2. Petición HTTP: AJAX POST hacia `assets/controller/UserController.php` y `LoginController.php`.
 * 3. Procesamiento Modelo: `Usuario::Loguearse`, `obtener_datos`, `editar`, `cambiar_contra`, `ascender`, `descender`, `delete`.
 * 4. Persistencia DB: Consultas preparadas en tabla `usuario` y relaciones con `tipo_us`.
 * 5. Respuesta: Notificaciones de estado ('update', 'up', 'donw', 'delete', etc.) o colección JSON.
 *
 * @package SIMAP\Models
 * @author Grupo de Proyecto
 * @version 1.0.0
 */

include_once 'conexion.php';

class Usuario {
    /**
     * @var array Colección de registros resultantes de consultas.
     */
    public $objetos;

    /**
     * @var PDO Instancia activa de conexión a la base de datos SQLite.
     */
    private $acceso;

    /**
     * Constructor del modelo Usuario.
     *
     * Inicializa la conexión PDO mediante la clase centralizada `Conexion`.
     */
    public function __construct() {
        $db = new Conexion();
        $this->acceso = $db->pdo;
    }

    /**
     * Autenticar credenciales de usuario mediante algoritmo seguro Bcrypt.
     *
     * Verifica la cédula y la contraseña cifrada contra la tabla `usuario`.
     *
     * @param string $ci Cédula de identidad del usuario.
     * @param string $pass Contraseña en texto plano ingresada en el login.
     * @return array Arreglo con el objeto del usuario autenticado si coincide, o arreglo vacío.
     */
    public function Loguearse($ci, $pass) {
        $sql = "SELECT usuario.*, tipo_us.nombre_tipo 
                FROM usuario 
                INNER JOIN tipo_us ON us_tipo = id_tipo_us 
                WHERE ci_us = :ci";
        $query = $this->acceso->prepare($sql);
        $query->execute([':ci' => $ci]);
        $usuarios = $query->fetchAll();

        $this->objetos = [];
        if (!empty($usuarios)) {
            $user = $usuarios[0];
            if (password_verify($pass, $user->contrasena_us)) {
                $this->objetos = [$user];
            }
        }
        return $this->objetos;
    }

    /**
     * Obtener los datos completos de perfil de un usuario por identificador.
     *
     * @param int $id Identificador único del usuario (`id_usuario`).
     * @return array Registro del usuario con su rol asociado.
     */
    public function obtener_datos($id) {
        $sql = "SELECT * FROM usuario 
                JOIN tipo_us ON us_tipo = id_tipo_us 
                WHERE id_usuario = :id";
        $query = $this->acceso->prepare($sql);
        $query->execute([':id' => $id]);
        $this->objetos = $query->fetchAll();
        return $this->objetos;
    }

    /**
     * Editar información de contacto y biográfica del perfil propio.
     *
     * @param int $id_usuario Identificador del usuario.
     * @param string $telefono Número de teléfono.
     * @param string $correo Correo electrónico.
     * @param string $genero Género ('M' o 'F').
     * @param string $info Información adicional o notas de perfil.
     * @return bool True si la actualización se confirmó, false ante excepciones.
     */
    public function editar($id_usuario, $telefono, $correo, $genero, $info) {
        try {
            $this->acceso->beginTransaction();
            $sql = "UPDATE usuario SET telefono_us = :telefono, correo_us = :correo, genero_us = :genero, info_us = :info 
                    WHERE id_usuario = :id";
            $query = $this->acceso->prepare($sql);
            $query->execute([
                ':id' => $id_usuario,
                ':telefono' => $telefono,
                ':correo' => $correo,
                ':genero' => $genero,
                ':info' => $info
            ]);
            $this->acceso->commit();
            return true;
        } catch (Exception $e) {
            $this->acceso->rollBack();
            return false;
        }
    }

    /**
     * Modificar la contraseña personal del usuario previa validación de la clave actual.
     *
     * Cifra la nueva clave usando Bcrypt (`PASSWORD_BCRYPT`).
     *
     * @param int $id_usuario Identificador del usuario.
     * @param string $oldpass Contraseña actual en texto plano.
     * @param string $newpass Nueva contraseña en texto plano a establecer.
     * @return void Emite 'update' en caso de éxito o 'noupdate' si la clave previa no coincide.
     */
    public function cambiar_contra($id_usuario, $oldpass, $newpass) {
        try {
            $sql = "SELECT id_usuario, contrasena_us FROM usuario WHERE id_usuario = :id";
            $query = $this->acceso->prepare($sql);
            $query->execute([':id' => $id_usuario]);
            $user = $query->fetch();

            if ($user && password_verify($oldpass, $user->contrasena_us)) {
                $this->acceso->beginTransaction();
                $newHash = password_hash($newpass, PASSWORD_BCRYPT);
                $updateSql = "UPDATE usuario SET contrasena_us = :newpass WHERE id_usuario = :id";
                $updateQuery = $this->acceso->prepare($updateSql);
                $updateQuery->execute([':id' => $id_usuario, ':newpass' => $newHash]);
                $this->acceso->commit();
                echo 'update';
                return;
            }
            echo 'noupdate';
        } catch (Exception $e) {
            if ($this->acceso->inTransaction()) {
                $this->acceso->rollBack();
            }
            echo 'noupdate';
        }
    }

    /**
     * Actualizar el nombre de archivo del avatar de perfil del usuario.
     *
     * @param int $id_usuario Identificador del usuario.
     * @param string $nombre Nombre de archivo del nuevo avatar guardado.
     * @return array|false Objeto con el avatar previo o false en caso de error.
     */
    public function cambiar_foto($id_usuario, $nombre) {
        try {
            $sql = "SELECT avatar FROM usuario WHERE id_usuario = :id";
            $query = $this->acceso->prepare($sql);
            $query->execute([':id' => $id_usuario]);
            $this->objetos = $query->fetchAll();

            $this->acceso->beginTransaction();
            $sql = "UPDATE usuario SET avatar = :nombre WHERE id_usuario = :id";
            $query = $this->acceso->prepare($sql);
            $query->execute([':id' => $id_usuario, ':nombre' => $nombre]);
            $this->acceso->commit();
            return $this->objetos;
        } catch (Exception $e) {
            if ($this->acceso->inTransaction()) {
                $this->acceso->rollBack();
            }
            return false;
        }
    }

    /**
     * Buscar usuarios por nombre, apellido o cédula de identidad.
     *
     * @return array Lista de usuarios coincidentes o primeros 50 ordenados.
     */
    public function buscar() {
        if (!empty($_POST['consulta'])) {
            $consulta = trim($_POST['consulta']);
            $sql = "SELECT * FROM usuario 
                    JOIN tipo_us ON us_tipo = id_tipo_us 
                    WHERE nombre_us LIKE :q OR apellidos_us LIKE :q OR ci_us LIKE :q 
                    ORDER BY id_usuario DESC LIMIT 50";
            $query = $this->acceso->prepare($sql);
            $query->execute([':q' => "%$consulta%"]);
            $this->objetos = $query->fetchAll();
            return $this->objetos;
        } else {
            $sql = "SELECT * FROM usuario 
                    JOIN tipo_us ON us_tipo = id_tipo_us 
                    ORDER BY id_usuario ASC LIMIT 50";
            $query = $this->acceso->prepare($sql);
            $query->execute();
            $this->objetos = $query->fetchAll();
            return $this->objetos;
        }
    }

    /**
     * Registrar un nuevo operador en el sistema con contraseña Bcrypt.
     *
     * Verifica la unicidad de la cédula de identidad antes de crear el registro.
     *
     * @param string $nombre Nombre de pila.
     * @param string $apellido Apellidos.
     * @param string $edad Fecha de nacimiento en formato YYYY-MM-DD.
     * @param string $ci Cédula de identidad.
     * @param string $genero Género ('M' o 'F').
     * @param string $pass Contraseña en texto plano a hashear.
     * @param int $tipo Rol del usuario (1 = Admin, 2 = Secretario).
     * @param string $avatar Nombre del archivo de avatar.
     * @return void Emite 'add' en caso de éxito o 'no add' si está duplicado o falla.
     */
    public function crear($nombre, $apellido, $edad, $ci, $genero, $pass, $tipo, $avatar) {
        try {
            $sql = "SELECT id_usuario FROM usuario WHERE ci_us = :ci";
            $query = $this->acceso->prepare($sql);
            $query->execute([':ci' => $ci]);
            $this->objetos = $query->fetchAll();

            if (!empty($this->objetos)) {
                echo 'no add';
            } else {
                $this->acceso->beginTransaction();
                $hash = password_hash($pass, PASSWORD_BCRYPT);
                $sql = "INSERT INTO usuario (nombre_us, apellidos_us, fecha_nacimiento, ci_us, genero_us, contrasena_us, us_tipo, avatar) 
                        VALUES (:nombre, :apellido, :fecha_nacimiento, :ci, :genero, :pass, :tipo, :avatar)";
                $query = $this->acceso->prepare($sql);
                $query->execute([
                    ':nombre' => $nombre,
                    ':apellido' => $apellido,
                    ':fecha_nacimiento' => $edad,
                    ':ci' => $ci,
                    ':genero' => $genero,
                    ':pass' => $hash,
                    ':tipo' => $tipo,
                    ':avatar' => $avatar
                ]);
                $this->acceso->commit();
                echo 'add';
            }
        } catch (Exception $e) {
            if ($this->acceso->inTransaction()) {
                $this->acceso->rollBack();
            }
            echo 'no add';
        }
    }

    /**
     * Validar la contraseña de confirmación del administrador ejecutor.
     *
     * @param string $pass Contraseña en texto plano a verificar.
     * @param int $id_usuario Identificador del administrador.
     * @return bool True si coincide con el hash almacenado, false de lo contrario.
     */
    private function verificarPasswordAdmin($pass, $id_usuario) {
        $sql = "SELECT contrasena_us FROM usuario WHERE id_usuario = :id_usuario";
        $query = $this->acceso->prepare($sql);
        $query->execute([':id_usuario' => $id_usuario]);
        $user = $query->fetch();
        if ($user) {
            return password_verify($pass, $user->contrasena_us);
        }
        return false;
    }

    /**
     * Ascender un usuario operador a rol de Administrador.
     *
     * Requiere confirmación de la contraseña del administrador en sesión.
     *
     * @param string $pass Contraseña del administrador ejecutor.
     * @param int $id_up Identificador del usuario que se ascenderá.
     * @param int $id_usuario Identificador del administrador en sesión.
     * @return void Emite 'up' o 'no-up'.
     */
    public function ascender($pass, $id_up, $id_usuario) {
        try {
            if ($this->verificarPasswordAdmin($pass, $id_usuario)) {
                $this->acceso->beginTransaction();
                $tipo = 1;
                $sql = "UPDATE usuario SET us_tipo = :tipo WHERE id_usuario = :id";
                $query = $this->acceso->prepare($sql);
                $query->execute([':id' => $id_up, ':tipo' => $tipo]);
                $this->acceso->commit();
                echo 'up';
            } else {
                echo 'no-up';
            }
        } catch (Exception $e) {
            if ($this->acceso->inTransaction()) {
                $this->acceso->rollBack();
            }
            echo 'no-up';
        }
    }

    /**
     * Degradar un usuario administrador a rol de Secretario/Operador.
     *
     * Aplica reglas de negocio estrictas:
     * 1. No permite auto-descenso.
     * 2. No permite degradar si queda un único administrador en el sistema (`last-admin`).
     *
     * @param string $pass Contraseña del administrador ejecutor.
     * @param int $id_donw Identificador del usuario que se degradará.
     * @param int $id_usuario Identificador del administrador en sesión.
     * @return void Emite 'donw', 'self-downgrade', 'last-admin' o 'no-donw'.
     */
    public function descender($pass, $id_donw, $id_usuario) {
        try {
            if ($id_donw == $id_usuario) {
                echo 'self-downgrade';
                return;
            }

            $countQuery = $this->acceso->query("SELECT COUNT(*) AS total FROM usuario WHERE us_tipo = 1");
            $totalAdmins = $countQuery->fetch()->total ?? 0;
            if ($totalAdmins <= 1) {
                echo 'last-admin';
                return;
            }

            if ($this->verificarPasswordAdmin($pass, $id_usuario)) {
                $this->acceso->beginTransaction();
                $tipo = 2;
                $sql = "UPDATE usuario SET us_tipo = :tipo WHERE id_usuario = :id";
                $query = $this->acceso->prepare($sql);
                $query->execute([':id' => $id_donw, ':tipo' => $tipo]);
                $this->acceso->commit();
                echo 'donw';
            } else {
                echo 'no-donw';
            }
        } catch (Exception $e) {
            if ($this->acceso->inTransaction()) {
                $this->acceso->rollBack();
            }
            echo 'no-donw';
        }
    }

    /**
     * Eliminar un usuario del sistema y borrar su avatar personalizado del disco.
     *
     * Aplica protecciones:
     * 1. Prohibida la auto-eliminación (`self-delete`).
     * 2. Prohibido eliminar al último administrador (`last-admin`).
     * 3. Remoción física del avatar si no es el avatar por defecto del sistema.
     *
     * @param string $pass Contraseña del administrador ejecutor.
     * @param int $id_delete Identificador del usuario a remover.
     * @param int $id_usuario Identificador del administrador en sesión.
     * @return void Emite 'delete', 'self-delete', 'last-admin' o 'no-delete'.
     */
    public function delete($pass, $id_delete, $id_usuario) {
        try {
            if ($id_delete == $id_usuario) {
                echo 'self-delete';
                return;
            }

            $checkUser = $this->acceso->prepare("SELECT us_tipo, avatar FROM usuario WHERE id_usuario = :id");
            $checkUser->execute([':id' => $id_delete]);
            $targetUser = $checkUser->fetch();

            if (!$targetUser) {
                echo 'no-delete';
                return;
            }

            if ($targetUser->us_tipo == 1) {
                $countQuery = $this->acceso->query("SELECT COUNT(*) AS total FROM usuario WHERE us_tipo = 1");
                $totalAdmins = $countQuery->fetch()->total ?? 0;
                if ($totalAdmins <= 1) {
                    echo 'last-admin';
                    return;
                }
            }

            if ($this->verificarPasswordAdmin($pass, $id_usuario)) {
                $this->acceso->beginTransaction();
                $sql = "DELETE FROM usuario WHERE id_usuario = :id";
                $query = $this->acceso->prepare($sql);
                $query->execute([':id' => $id_delete]);
                $this->acceso->commit();

                if (!empty($targetUser->avatar) && strpos($targetUser->avatar, 'user-default') === false) {
                    $avatarPath = '../libs/img/avatars/' . $targetUser->avatar;
                    if (file_exists($avatarPath)) {
                        @unlink($avatarPath);
                    }
                }

                echo 'delete';
            } else {
                echo 'no-delete';
            }
        } catch (Exception $e) {
            if ($this->acceso->inTransaction()) {
                $this->acceso->rollBack();
            }
            echo 'no-delete';
        }
    }
}
?>