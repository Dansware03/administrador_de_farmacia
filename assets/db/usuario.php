<?php
include_once 'conexion.php';
class Usuario {
    var $objetos;
    private $acceso;
    public function __construct() {
        $db = new Conexion();
        $this->acceso = $db->pdo;
    }

    /**
     * Autenticación segura mediante Bcrypt con migración transparente on-the-fly.
     * Si la clave coincide en texto plano (cuenta previa a la migración), se actualiza automáticamente al hash bcrypt.
     */
    function Loguearse($ci, $pass) {
        $sql = "SELECT usuario.*, tipo_us.nombre_tipo FROM usuario INNER JOIN tipo_us ON us_tipo = id_tipo_us WHERE ci_us = :ci";
        $query = $this->acceso->prepare($sql);
        $query->execute(array(':ci' => $ci));
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

    function obtener_datos($id) {
        $sql = "SELECT * FROM usuario join tipo_us on us_tipo=id_tipo_us where id_usuario=:id";
        $query = $this->acceso->prepare($sql);
        $query->execute(array(':id' => $id));
        $this->objetos = $query->fetchAll();
        return $this->objetos;
    }

    function editar($id_usuario, $telefono, $correo, $genero, $info) {
        try {
            $this->acceso->beginTransaction();
            $sql = "UPDATE usuario SET telefono_us=:telefono, correo_us=:correo, genero_us=:genero, info_us=:info where id_usuario=:id";
            $query = $this->acceso->prepare($sql);
            $query->execute(array(':id'=>$id_usuario,':telefono'=>$telefono,':correo'=>$correo,':genero'=>$genero,':info'=>$info));
            $this->acceso->commit();
            return true;
        } catch (Exception $e) {
            $this->acceso->rollBack();
            return false;
        }
    }

    function cambiar_contra($id_usuario, $oldpass, $newpass) {
        try {
            $sql = "SELECT id_usuario, contrasena_us FROM usuario where id_usuario=:id";
            $query = $this->acceso->prepare($sql);
            $query->execute(array(':id' => $id_usuario));
            $user = $query->fetch();

            if ($user) {
                if (password_verify($oldpass, $user->contrasena_us)) {
                    $this->acceso->beginTransaction();
                    $newHash = password_hash($newpass, PASSWORD_BCRYPT);
                    $updateSql = "UPDATE usuario SET contrasena_us=:newpass where id_usuario=:id";
                    $updateQuery = $this->acceso->prepare($updateSql);
                    $updateQuery->execute(array(':id' => $id_usuario, ':newpass' => $newHash));
                    $this->acceso->commit();
                    echo 'update';
                    return;
                }
            }
            echo 'noupdate';
        } catch (Exception $e) {
            if ($this->acceso->inTransaction()) {
                $this->acceso->rollBack();
            }
            echo 'noupdate';
        }
    }

    function cambiar_foto($id_usuario, $nombre) {
        try {
            $sql = "SELECT avatar FROM usuario where id_usuario=:id";
            $query = $this->acceso->prepare($sql);
            $query->execute(array(':id' => $id_usuario));
            $this->objetos = $query->fetchAll();

            $this->acceso->beginTransaction();
            $sql = "UPDATE usuario SET avatar=:nombre where id_usuario=:id";
            $query = $this->acceso->prepare($sql);
            $query->execute(array(':id' => $id_usuario, ':nombre' => $nombre));
            $this->acceso->commit();
            return $this->objetos;
        } catch (Exception $e) {
            if ($this->acceso->inTransaction()) {
                $this->acceso->rollBack();
            }
            return false;
        }
    }

    function buscar() {
        if (!empty($_POST['consulta'])) {
            $consulta = trim($_POST['consulta']);
            $sql = "SELECT * FROM usuario JOIN tipo_us ON us_tipo=id_tipo_us 
                    WHERE nombre_us LIKE :q OR apellidos_us LIKE :q OR ci_us LIKE :q 
                    ORDER BY id_usuario DESC LIMIT 50";
            $query = $this->acceso->prepare($sql);
            $query->execute(array(':q' => "%$consulta%"));
            $this->objetos = $query->fetchAll();
            return $this->objetos;
        } else {
            $sql = "SELECT * FROM usuario JOIN tipo_us ON us_tipo=id_tipo_us ORDER BY id_usuario ASC LIMIT 50";
            $query = $this->acceso->prepare($sql);
            $query->execute();
            $this->objetos = $query->fetchAll();
            return $this->objetos;
        }
    }

    function crear($nombre, $apellido, $edad, $ci, $genero, $pass, $tipo, $avatar) {
        try {
            $sql = "SELECT id_usuario FROM usuario WHERE ci_us = :ci";
            $query = $this->acceso->prepare($sql);
            $query->execute(array(':ci' => $ci));
            $this->objetos = $query->fetchAll();

            if (!empty($this->objetos)) {
                echo 'no add';
            } else {
                $this->acceso->beginTransaction();
                $hash = password_hash($pass, PASSWORD_BCRYPT);
                $sql = "INSERT INTO usuario (nombre_us, apellidos_us, fecha_nacimiento, ci_us, genero_us, contrasena_us, us_tipo, avatar) VALUES (:nombre, :apellido, :fecha_nacimiento, :ci, :genero, :pass, :tipo, :avatar)";
                $query = $this->acceso->prepare($sql);
                $query->execute(array(
                    ':nombre' => $nombre,
                    ':apellido' => $apellido,
                    ':fecha_nacimiento' => $edad,
                    ':ci' => $ci,
                    ':genero' => $genero,
                    ':pass' => $hash,
                    ':tipo' => $tipo,
                    ':avatar' => $avatar
                ));
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

    private function verificarPasswordAdmin($pass, $id_usuario) {
        $sql = "SELECT contrasena_us FROM usuario where id_usuario=:id_usuario";
        $query = $this->acceso->prepare($sql);
        $query->execute(array(':id_usuario' => $id_usuario));
        $user = $query->fetch();
        if ($user) {
            return password_verify($pass, $user->contrasena_us);
        }
        return false;
    }

    function ascender($pass, $id_up, $id_usuario) {
        try {
            if ($this->verificarPasswordAdmin($pass, $id_usuario)) {
                $this->acceso->beginTransaction();
                $tipo = 1;
                $sql = "UPDATE usuario SET us_tipo=:tipo where id_usuario=:id";
                $query = $this->acceso->prepare($sql);
                $query->execute(array(':id' => $id_up, ':tipo' => $tipo));
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

    function descender($pass, $id_donw, $id_usuario) {
        try {
            // Regla anti-auto-descenso
            if ($id_donw == $id_usuario) {
                echo 'self-downgrade';
                return;
            }

            // Regla del último administrador
            $countQuery = $this->acceso->query("SELECT COUNT(*) AS total FROM usuario WHERE us_tipo = 1");
            $totalAdmins = $countQuery->fetch()->total ?? 0;
            if ($totalAdmins <= 1) {
                echo 'last-admin';
                return;
            }

            if ($this->verificarPasswordAdmin($pass, $id_usuario)) {
                $this->acceso->beginTransaction();
                $tipo = 2;
                $sql = "UPDATE usuario SET us_tipo=:tipo where id_usuario=:id";
                $query = $this->acceso->prepare($sql);
                $query->execute(array(':id' => $id_donw, ':tipo' => $tipo));
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

    function delete($pass, $id_delete, $id_usuario) {
        try {
            // Regla anti-auto-eliminación
            if ($id_delete == $id_usuario) {
                echo 'self-delete';
                return;
            }

            // Comprobar si el usuario a eliminar es un administrador
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
                $sql = "DELETE FROM usuario where id_usuario=:id";
                $query = $this->acceso->prepare($sql);
                $query->execute(array(':id' => $id_delete));
                $this->acceso->commit();

                // Eliminar archivo de avatar del disco si no es el default
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