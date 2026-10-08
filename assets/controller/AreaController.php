<?php
// ponytail: controlador para áreas de servicio hospitalario y unidades de medida
include_once '../db/conexion.php';
session_start();

$funcion = $_POST['funcion'] ?? '';

if ($funcion == 'cargar_areas') {
    $db = new Conexion();
    $sql = "SELECT id_area, nombre_area FROM area_servicio ORDER BY nombre_area ASC";
    $query = $db->pdo->prepare($sql);
    $query->execute();
    $areas = $query->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($areas);
    exit();
}

if ($funcion == 'cargar_unidades') {
    $db = new Conexion();
    $sql = "SELECT id_unidad, nombre, codigo FROM unidad_medida ORDER BY nombre ASC";
    $query = $db->pdo->prepare($sql);
    $query->execute();
    $unidades = $query->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($unidades);
    exit();
}

if ($funcion == 'buscar_areas') {
    $consulta = trim($_POST['consulta'] ?? '');
    $db = new Conexion();
    if (!empty($consulta)) {
        $sql = "SELECT * FROM area_servicio WHERE nombre_area LIKE :q ORDER BY nombre_area ASC";
        $query = $db->pdo->prepare($sql);
        $query->execute([':q' => "%$consulta%"]);
    } else {
        $sql = "SELECT * FROM area_servicio ORDER BY nombre_area ASC";
        $query = $db->pdo->prepare($sql);
        $query->execute();
    }
    echo json_encode($query->fetchAll(PDO::FETCH_ASSOC));
    exit();
}

if ($funcion == 'crear_area') {
    if (!isset($_SESSION['us_tipo']) || $_SESSION['us_tipo'] != 1) {
        echo json_encode(['status' => 'error', 'message' => 'Permisos insuficientes']);
        exit();
    }
    $nombre = trim($_POST['nombre_area'] ?? '');

    if (empty($nombre)) {
        echo json_encode(['status' => 'error', 'message' => 'El nombre del área es requerido']);
        exit();
    }

    $db = new Conexion();
    try {
        // Verificar existencia previa
        $check = $db->pdo->prepare("SELECT id_area FROM area_servicio WHERE LOWER(nombre_area) = LOWER(:nombre)");
        $check->execute([':nombre' => $nombre]);
        if ($check->fetch()) {
            echo json_encode(['status' => 'error', 'message' => 'El área hospitalaria ya se encuentra registrada']);
            exit();
        }

        $db->pdo->beginTransaction();
        $sql = "INSERT INTO area_servicio (nombre_area) VALUES (:nombre)";
        $query = $db->pdo->prepare($sql);
        $query->execute([':nombre' => $nombre]);
        $idNuevo = $db->pdo->lastInsertId();
        $db->pdo->commit();

        echo json_encode([
            'status' => 'success',
            'id_area' => $idNuevo,
            'nombre_area' => $nombre
        ]);
        exit();
    } catch (Exception $e) {
        if ($db->pdo->inTransaction()) {
            $db->pdo->rollBack();
        }
        echo json_encode(['status' => 'error', 'message' => 'Error al registrar área']);
        exit();
    }
}

if ($funcion == 'editar_area') {
    if (!isset($_SESSION['us_tipo']) || $_SESSION['us_tipo'] != 1) {
        echo json_encode(['status' => 'error', 'message' => 'Permisos insuficientes']);
        exit();
    }
    $id = (int)($_POST['id_area'] ?? 0);
    $nombre = trim($_POST['nombre_area'] ?? '');

    if ($id <= 0 || empty($nombre)) {
        echo json_encode(['status' => 'error', 'message' => 'Datos incompletos']);
        exit();
    }

    $db = new Conexion();
    try {
        $db->pdo->beginTransaction();
        $sql = "UPDATE area_servicio SET nombre_area = :nombre WHERE id_area = :id";
        $query = $db->pdo->prepare($sql);
        $query->execute([':nombre' => $nombre, ':id' => $id]);
        $db->pdo->commit();
        echo json_encode(['status' => 'success', 'message' => 'Área actualizada']);
        exit();
    } catch (Exception $e) {
        if ($db->pdo->inTransaction()) {
            $db->pdo->rollBack();
        }
        echo json_encode(['status' => 'error', 'message' => 'Error al actualizar área']);
        exit();
    }
}

if ($funcion == 'borrar_area') {
    if (!isset($_SESSION['us_tipo']) || $_SESSION['us_tipo'] != 1) {
        echo json_encode(['status' => 'error', 'message' => 'Permisos insuficientes']);
        exit();
    }
    $id = (int)($_POST['id_area'] ?? 0);
    if ($id <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'ID inválido']);
        exit();
    }

    $db = new Conexion();
    try {
        // Verificar si existen actas de entrega asociadas a esta área
        $check = $db->pdo->prepare("SELECT COUNT(*) as total FROM despacho WHERE id_area = :id");
        $check->execute([':id' => $id]);
        if ($check->fetch()->total > 0) {
            echo json_encode(['status' => 'error', 'message' => 'No se puede eliminar: el área tiene despachos históricos asociados']);
            exit();
        }

        $db->pdo->beginTransaction();
        $sql = "DELETE FROM area_servicio WHERE id_area = :id";
        $query = $db->pdo->prepare($sql);
        $query->execute([':id' => $id]);
        $db->pdo->commit();
        echo json_encode(['status' => 'success', 'message' => 'Área eliminada']);
        exit();
    } catch (Exception $e) {
        if ($db->pdo->inTransaction()) {
            $db->pdo->rollBack();
        }
        echo json_encode(['status' => 'error', 'message' => 'Error al eliminar área']);
        exit();
    }
}
