<?php
/**
 * ============================================================================
 * ARCHIVO: AreaController.php
 * CAPA: Controlador (Backend / Orquestación HTTP)
 * DESCRIPCIÓN: Administra las áreas de servicio hospitalario receptoras de insumos
 *              (Quirófano, Emergencia, Nutrición, etc.) y el catálogo de unidades
 *              de medida (und, L, gal, kg, etc.).
 * ENTRADA: Peticiones AJAX POST desde assets/libs/js/area.js, adm_area.js y carrito.js.
 * SALIDA: Respuestas estructuradas en formato JSON con estados y payloads de datos.
 * DEPENDENCIAS: assets/db/conexion.php (Conexión PDO SQLite).
 * ============================================================================
 */

include_once '../db/conexion.php';
session_start();

$funcion = $_POST['funcion'] ?? '';

/**
 * CASO DE USO: Cargar Listado Completo de Áreas Hospitalarias
 * ----------------------------------------------------------------------------
 * @route POST assets/controller/AreaController.php [funcion=cargar_areas]
 * @description Obtiene todas las áreas registradas para poblar selectores en
 *              despachos y catálogos.
 * 
 * FLUJO DE EJECUCIÓN:
 *   1. Prepara y ejecuta consulta SELECT sobre la tabla `area_servicio`.
 *   2. Ordena alfabéticamente por `nombre_area`.
 *   3. Emite respuesta en JSON con pares [{ id_area, nombre_area }, ...].
 * ----------------------------------------------------------------------------
 */
if ($funcion == 'cargar_areas') {
    $db = new Conexion();
    $sql = "SELECT id_area, nombre_area FROM area_servicio ORDER BY nombre_area ASC";
    $query = $db->pdo->prepare($sql);
    $query->execute();
    $areas = $query->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($areas);
    exit();
}

/**
 * CASO DE USO: Cargar Catálogo de Unidades de Medida
 * ----------------------------------------------------------------------------
 * @route POST assets/controller/AreaController.php [funcion=cargar_unidades]
 * @description Retorna las unidades métricas para asignación en insumos y lotes.
 * 
 * FLUJO DE EJECUCIÓN:
 *   1. Ejecuta consulta SELECT sobre la tabla `unidad_medida`.
 *   2. Emite respuesta en JSON [{ id_unidad, nombre, codigo }, ...].
 * ----------------------------------------------------------------------------
 */
if ($funcion == 'cargar_unidades') {
    $db = new Conexion();
    $sql = "SELECT id_unidad, nombre, codigo FROM unidad_medida ORDER BY nombre ASC";
    $query = $db->pdo->prepare($sql);
    $query->execute();
    $unidades = $query->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($unidades);
    exit();
}

/**
 * CASO DE USO: Búsqueda y Filtrado de Áreas
 * ----------------------------------------------------------------------------
 * @route POST assets/controller/AreaController.php [funcion=buscar_areas]
 * @param string $_POST['consulta'] Término de búsqueda textual (opcional).
 * 
 * FLUJO DE EJECUCIÓN:
 *   1. Recibe y sanea el parámetro de consulta mediante trim().
 *   2. Si contiene texto, aplica filtro LIKE parametrizado (:q).
 *   3. Si está vacío, lista todas las áreas ordenadas por nombre.
 *   4. Retorna arreglo asociativo en JSON.
 * ----------------------------------------------------------------------------
 */
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

/**
 * CASO DE USO: Registrar Nueva Área Hospitalaria
 * ----------------------------------------------------------------------------
 * @route POST assets/controller/AreaController.php [funcion=crear_area]
 * @param string $_POST['nombre_area'] Nombre de la nueva área hospitalaria.
 * 
 * FLUJO DE EJECUCIÓN:
 *   1. Control de Acceso: Verifica sesión activa con rol Administrador (us_tipo = 1).
 *   2. Validación de Entrada: Comprueba que el nombre no esté en blanco.
 *   3. Verificación de Duplicados: Consulta insensible a mayúsculas (LOWER).
 *   4. Persistencia Atómica: Abre transacción PDO, inserta y recupera lastInsertId().
 *   5. Retorna JSON { status: 'success', id_area, nombre_area } o mensaje de error.
 * ----------------------------------------------------------------------------
 */
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

/**
 * CASO DE USO: Editar Área Hospitalaria Existente
 * ----------------------------------------------------------------------------
 * @route POST assets/controller/AreaController.php [funcion=editar_area]
 * @param int    $_POST['id_area']     Identificador único del área a modificar.
 * @param string $_POST['nombre_area'] Nuevo nombre asignado al área.
 * 
 * FLUJO DE EJECUCIÓN:
 *   1. Control de Acceso: Verifica privilegios de Administrador.
 *   2. Validación de Entrada: Casting entero de ID y saneamiento de nombre.
 *   3. Persistencia Atómica: Transacción PDO con sentencia UPDATE parametrizada.
 *   4. Retorna confirmación JSON de actualización exitosa.
 * ----------------------------------------------------------------------------
 */
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

/**
 * CASO DE USO: Eliminar Área Hospitalaria
 * ----------------------------------------------------------------------------
 * @route POST assets/controller/AreaController.php [funcion=borrar_area]
 * @param int $_POST['id_area'] Identificador del área a dar de baja.
 * 
 * FLUJO DE EJECUCIÓN:
 *   1. Control de Acceso: Exige privilegios de Administrador.
 *   2. Integridad Referencial: Verifica si existen despachos históricos asociados
 *      en la tabla `despacho`. Si existen, rechaza la operación para preservar auditoría.
 *   3. Persistencia Atómica: Transacción PDO con sentencia DELETE WHERE id_area = :id.
 *   4. Retorna estado JSON con confirmación o motivo de rechazo.
 * ----------------------------------------------------------------------------
 */
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
?>
