<?php
/**
 * ============================================================================
 * ARCHIVO: DespachoController.php
 * CAPA: Controlador (Backend / Orquestación HTTP)
 * DESCRIPCIÓN: Administra las transacciones institucionales de salida y despacho
 *              de insumos médicos hacia las áreas de servicio de la clínica, así
 *              como la emisión de actas oficiales de entrega y reversiones de stock.
 * ENTRADA: Peticiones AJAX POST/GET desde assets/libs/js/carrito.js, despacho.js
 *          y solicitudes de generación de actas PDF (acta_despacho.php).
 * SALIDA: Respuestas en formato JSON con estados de transacción e información de actas.
 * DEPENDENCIAS: assets/db/despacho.php (Modelo Despacho).
 * ============================================================================
 */

include_once '../db/despacho.php';
$despacho = new Despacho();

$funcion = $_POST['funcion'] ?? $_GET['funcion'] ?? null;

if ($funcion) {
    switch ($funcion) {
        /**
         * CASO DE USO: Registrar Despacho y Acta de Entrega
         * --------------------------------------------------------------------
         * @route POST assets/controller/DespachoController.php [funcion=registrar_despacho]
         * @param string $nombre / $receptor  Nombre completo de la persona que recibe.
         * @param string $ci / $ci_receptor   Cédula de identidad del receptor.
         * @param int    $id_area             ID del área hospitalaria de destino.
         * @param string $cargo_receptor      Cargo o responsabilidad de quien recibe.
         * @param string $observacion         Nota institucional u orden de entrega.
         * @param string $productos           JSON array con los insumos y cantidades solicitadas.
         * 
         * FLUJO DE EJECUCIÓN:
         *   1. Sesión: Obtiene ID del usuario autenticado que autoriza ($responsable).
         *   2. Decodificación: Parsea JSON de productos [{ id, cantidad }, ...].
         *   3. Validación: Verifica que el carrito no esté vacío.
         *   4. Transacción Atómica en Modelo: Invoca Despacho::registrar_despacho(...),
         *      el cual descuenta stock de lotes (FEFO: Primero en Vencer, Primero en Salir),
         *      inserta cabecera `despacho`, totales `despacho_insumo` y trazabilidad `detalle_despacho`.
         *   5. Respuesta: Emite JSON { status: 'success', id_despacho, message }.
         * --------------------------------------------------------------------
         */
        case 'registrar_despacho':
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $receptor = isset($_POST['nombre']) ? trim($_POST['nombre']) : (isset($_POST['receptor']) ? trim($_POST['receptor']) : '');
            $ci_receptor = isset($_POST['ci']) ? trim($_POST['ci']) : (isset($_POST['ci_receptor']) ? trim($_POST['ci_receptor']) : '');
            $id_area = !empty($_POST['id_area']) ? intval($_POST['id_area']) : null;
            $cargo_receptor = isset($_POST['cargo_receptor']) ? trim($_POST['cargo_receptor']) : '';
            $observacion = isset($_POST['observacion']) ? trim($_POST['observacion']) : '';
            $productos = isset($_POST['productos']) ? json_decode($_POST['productos'], true) : [];

            $responsable = !empty($_SESSION['usuario']) ? intval($_SESSION['usuario']) : 1;

            try {
                if (empty($productos) || !is_array($productos)) {
                    throw new Exception("No hay insumos seleccionados para despachar.");
                }
                $id = $despacho->registrar_despacho($receptor, $ci_receptor, $responsable, $productos, $id_area, $cargo_receptor, $observacion);
                echo json_encode([
                    'status' => 'success',
                    'id_despacho' => $id,
                    'message' => 'Despacho registrado exitosamente.'
                ]);
            } catch (Exception $e) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Error al procesar el despacho: ' . $e->getMessage()
                ]);
            }
            break;

        /**
         * CASO DE USO: Listar Despachos Realizados (Historial)
         * --------------------------------------------------------------------
         * @route GET/POST assets/controller/DespachoController.php [funcion=listar_despachos]
         * @param string $_GET['fecha_inicio'] Filtro de fecha inicial (opcional).
         * @param string $_GET['fecha_fin']    Filtro de fecha final (opcional).
         * 
         * FLUJO DE EJECUCIÓN:
         *   1. Evalúa si se proporcionó un rango de fechas.
         *   2. Si existe rango, filtra mediante `listar_despachos_por_fechas(...)`.
         *   3. Si no existe rango, invoca `listar_despachos()` para obtener el histórico general.
         *   4. Retorna arreglo en formato JSON con información de cabecera de despachos.
         * --------------------------------------------------------------------
         */
        case 'listar_despachos':
            $fecha_inicio = $_GET['fecha_inicio'] ?? null;
            $fecha_fin = $_GET['fecha_fin'] ?? null;

            if ($fecha_inicio && $fecha_fin) {
                $resultado = $despacho->listar_despachos_por_fechas($fecha_inicio, $fecha_fin);
            } else {
                $resultado = $despacho->listar_despachos();
            }
            echo json_encode($resultado);
            break;

        /**
         * CASO DE USO: Ver Detalle de un Despacho Específico
         * --------------------------------------------------------------------
         * @route POST/GET assets/controller/DespachoController.php [funcion=ver_detalle_despacho]
         * @param int $id_despacho ID del despacho a inspeccionar.
         * 
         * FLUJO DE EJECUCIÓN:
         *   1. Consulta las líneas de insumos entregados en la tabla `detalle_despacho`.
         *   2. Relaciona insumo, presentación, unidad de medida y lote asignado.
         *   3. Emite arreglo JSON con los renglones detallados del acta.
         * --------------------------------------------------------------------
         */
        case 'ver_detalle_despacho':
            $id_despacho = $_POST['id_despacho'] ?? $_GET['id_despacho'] ?? null;
            $resultado = $despacho->ver_detalle_despacho($id_despacho);
            echo json_encode($resultado);
            break;

        /**
         * CASO DE USO: Anular y Revertir Despacho (Reintegro de Inventario)
         * --------------------------------------------------------------------
         * @route POST assets/controller/DespachoController.php [funcion=revertir_despacho]
         * @param int $_POST['id_despacho'] ID del despacho a anular.
         * 
         * FLUJO DE EJECUCIÓN:
         *   1. Invoca `Despacho::revertir_despacho($id_despacho)`.
         *   2. La transacción atómica restituye las cantidades en la tabla `lote`.
         *   3. Elimina las relaciones de detalle y cabecera del despacho anulado.
         *   4. Emite respuesta JSON confirmando la restitución de existencias.
         * --------------------------------------------------------------------
         */
        case 'revertir_despacho':
            $id_despacho = $_POST['id_despacho'] ?? null;
            try {
                $despacho->revertir_despacho($id_despacho);
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Despacho anulado correctamente. Los insumos han sido reintegrados al inventario.'
                ]);
            } catch (Exception $e) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Error al anular el despacho: ' . $e->getMessage()
                ]);
            }
            break;

        /**
         * CASO DE USO: Obtener Datos de Cabecera de un Despacho
         * --------------------------------------------------------------------
         * @route POST/GET assets/controller/DespachoController.php [funcion=obtener_despacho]
         * @param int $id_despacho ID del despacho consultado.
         * --------------------------------------------------------------------
         */
        case 'obtener_despacho':
            $id_despacho = $_POST['id_despacho'] ?? $_GET['id_despacho'] ?? null;
            try {
                $data = $despacho->obtener_despacho($id_despacho);
                if ($data) {
                    echo json_encode([
                        'status' => 'success',
                        'despacho' => $data
                    ]);
                } else {
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'Despacho no encontrado'
                    ]);
                }
            } catch (Exception $e) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Error al obtener despacho: ' . $e->getMessage()
                ]);
            }
            break;

        /**
         * CASO DE USO: Consultar Existencia Disponible por Lote
         * --------------------------------------------------------------------
         * @route POST assets/controller/DespachoController.php [funcion=obtener_stock_lote]
         * @param int $_POST['id_producto'] ID del insumo médico.
         * @param int $_POST['id_lote']     ID del lote específico.
         * --------------------------------------------------------------------
         */
        case 'obtener_stock_lote':
            $id_producto = $_POST['id_producto'] ?? null;
            $id_lote = $_POST['id_lote'] ?? null;
            try {
                $stock = $despacho->obtener_stock_lote($id_producto, $id_lote);
                echo json_encode([
                    'status' => 'success',
                    'stock' => $stock
                ]);
            } catch (Exception $e) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Error al obtener stock: ' . $e->getMessage()
                ]);
            }
            break;

        /**
         * CASO DE USO: Actualizar Datos de Acta de Despacho
         * --------------------------------------------------------------------
         * @route POST assets/controller/DespachoController.php [funcion=actualizar_despacho]
         * @param int    $_POST['id_despacho'] Identificador de la entrega.
         * @param string $_POST['receptor']    Nuevo nombre de receptor.
         * @param string $_POST['ci_receptor'] Cédula actualizada.
         * @param string $_POST['productos']   JSON con insumos ajustados.
         * --------------------------------------------------------------------
         */
        case 'actualizar_despacho':
            $id_despacho = $_POST['id_despacho'] ?? null;
            $receptor = $_POST['receptor'] ?? $_POST['cliente'] ?? '';
            $ci_receptor = $_POST['ci_receptor'] ?? $_POST['ci'] ?? '';
            $productos = $_POST['productos'] ?? '[]';
            $id_area = !empty($_POST['id_area']) ? $_POST['id_area'] : null;
            $cargo_receptor = $_POST['cargo_receptor'] ?? '';
            $observacion = $_POST['observacion'] ?? '';

            try {
                $despacho->actualizar_despacho($id_despacho, $receptor, $ci_receptor, $productos, $id_area, $cargo_receptor, $observacion);
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Acta de despacho actualizada correctamente'
                ]);
            } catch (Exception $e) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Error al actualizar el despacho: ' . $e->getMessage()
                ]);
            }
            break;
    }
}

/**
 * SERVICIO: Obtener Despacho Consolidado para Impresión de Acta PDF
 * ----------------------------------------------------------------------------
 * @route GET assets/controller/DespachoController.php?accion=obtener_despacho_pdf&id=X
 * @description Utilizado por la vista acta_despacho.php para compilar la cabecera
 *              y el detalle de insumos en una sola petición asíncrona.
 * ----------------------------------------------------------------------------
 */
if (isset($_GET['accion']) && $_GET['accion'] === 'obtener_despacho_pdf') {
    $id_despacho = $_GET['id'] ?? null;
    if ($id_despacho) {
        $data = $despacho->obtener_despacho($id_despacho);
        $detalles = $despacho->ver_detalle_despacho($id_despacho);
        echo json_encode([
            'despacho' => $data,
            'detalles' => $detalles
        ]);
    }
}
?>
