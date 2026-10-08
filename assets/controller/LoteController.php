<?php
/**
 * ============================================================================
 * ARCHIVO: LoteController.php
 * CAPA: Controlador (Backend / Orquestación HTTP)
 * DESCRIPCIÓN: Administra los lotes de existencias, fechas de vencimiento,
 *              estados de caducidad (en regla, por vencer, vencido, no perecedero)
 *              y trazabilidad de proveedores para el inventario de insumos.
 * ENTRADA: Peticiones AJAX POST desde assets/libs/js/lote.js y gestion_lote.js.
 * SALIDA: Arreglos JSON con información enriquecida de inventario o estados de acción.
 * DEPENDENCIAS: assets/db/lote.php (Modelo Lote).
 * @author Grupo de Proyecto
 * @version 1.0.0
 * ============================================================================
 */

include_once '../db/lote.php';
$lote = new Lote();

if (isset($_POST['funcion'])) {
    $funcion = $_POST['funcion'];
    switch ($funcion) {
        /**
         * CASO DE USO: Registrar Nuevo Lote de Insumo
         * --------------------------------------------------------------------
         * @route POST assets/controller/LoteController.php [funcion=crear]
         * @param int    $_POST['id_producto'] ID del insumo médico asociado.
         * @param int    $_POST['proveedor']   ID del proveedor o entidad donante.
         * @param string $_POST['cod_lote']    Código o número de lote físico.
         * @param int    $_POST['stock']       Cantidad de unidades recibidas.
         * @param string $_POST['vencimiento'] Fecha de caducidad (formato YYYY-MM-DD).
         * 
         * FLUJO DE EJECUCIÓN:
         *   1. Recibe los parámetros del formulario de ingreso de lote.
         *   2. Delega en `Lote::crear($id_producto, $proveedor, $cod_lote, $stock, $vencimiento)`.
         *   3. Ejecuta sentencia INSERT con sentencia preparada en la tabla `lote`.
         * --------------------------------------------------------------------
         */
        case 'crear':
            $id_producto = isset($_POST['id_producto']) ? $_POST['id_producto'] : '';
            $proveedor = isset($_POST['proveedor']) ? $_POST['proveedor'] : '';
            $cod_lote = isset($_POST['cod_lote']) ? $_POST['cod_lote'] : '';
            $stock = isset($_POST['stock']) ? $_POST['stock'] : '';
            $vencimiento = isset($_POST['vencimiento']) ? $_POST['vencimiento'] : '';
            $lote->crear($id_producto, $proveedor, $cod_lote, $stock, $vencimiento);
            break;

        /**
         * CASO DE USO: Buscar y Clasificar Lotes de Inventario
         * --------------------------------------------------------------------
         * @route POST assets/controller/LoteController.php [funcion=buscar_lote]
         * @description Consulta todas las existencias por lote y calcula en tiempo
         *              real la semaforización de vencimiento:
         *                - 'no_perecedero': Equipos, herramientas o consumibles sin caducidad.
         *                - 'light': Vigente con más de 90 días de vida útil.
         *                - 'warning': Próximo a vencer (menos de 90 días).
         *                - 'danger': Vencido (días restantes negativos).
         * 
         * FLUJO DE EJECUCIÓN:
         *   1. Invoca `Lote::buscar()` para traer los lotes con sus productos y proveedores.
         *   2. Itera sobre cada registro y evalúa categoría y fecha:
         *      - Categorías 2 (Equipos), 4 (Papel/Higiene) y 5 (Herramientas) se marcan no perecederas.
         *      - Fechas de marcador '2030-01-01' o '0000-00-00' se consideran no perecederas.
         *      - Para perecederos, calcula la diferencia en días contra la fecha actual (`diff`).
         *   3. Empaqueta el arreglo en formato JSON con métricas de meses, días y estado visual.
         * --------------------------------------------------------------------
         */
        case 'buscar_lote':
            $lote->buscar();
            $json = array();
            $fecha_actual = new Datetime('today');
            foreach ($lote->objetos as $objeto) {
                $esNoPerecedero = false;
                $vencimientoStr = trim($objeto->vencimiento ?? '');
                $categoriaId = isset($objeto->prod_tip_prod) ? (int)$objeto->prod_tip_prod : 0;

                // ponytail: Equipos de limpieza (2), papel e higiene (4) y herramientas (5) son no perecederos.
                // O aquellos con fecha vacía, 0000-00-00 o fecha de marcador no perecedero (>= 2030-01-01).
                if (in_array($categoriaId, [2, 4, 5]) || empty($vencimientoStr) || $vencimientoStr === '0000-00-00' || strpos($vencimientoStr, '2030') === 0) {
                    $esNoPerecedero = true;
                }

                if ($esNoPerecedero) {
                    $estado = 'no_perecedero';
                    $mes = 0;
                    $dia = 0;
                    $totalDias = 9999;
                } else {
                    $vencimiento = new Datetime($vencimientoStr);
                    // $fecha_actual->diff($vencimiento) da positivo si está en el futuro, negativo si ya venció
                    $diferencia = $fecha_actual->diff($vencimiento);
                    $totalDias = (int)$diferencia->format('%r%a');

                    if ($totalDias < 0) {
                        $estado = 'danger'; // Vencido
                        $mes = (int)ceil($totalDias / 30);
                        $dia = $totalDias;
                    } elseif ($totalDias <= 90) {
                        $estado = 'warning'; // Por vencer (menos de 90 días / 3 meses)
                        $mes = (int)floor($totalDias / 30);
                        $dia = $totalDias % 30;
                    } else {
                        $estado = 'light'; // En regla con más de 90 días
                        $mes = (int)floor($totalDias / 30);
                        $dia = $totalDias % 30;
                    }
                }

                $json[] = array(
                    'id' => $objeto->id_lote,
                    'nombre' => $objeto->prod_nom,
                    'concentracion' => $objeto->concentracion,
                    'adicional' => $objeto->adicional,
                    'vencimiento' => $esNoPerecedero ? '' : $objeto->vencimiento,
                    'proveedor' => $objeto->pro_nom,
                    'cod_lote' => $objeto->cod_lote,
                    'stock' => $objeto->stock,
                    'nombre_laboratorio' => $objeto->lab_nom,
                    'tipo' => $objeto->tip_nom,
                    'tipo_id' => $categoriaId,
                    'es_perecedero' => !$esNoPerecedero,
                    'dias_restantes' => $totalDias,
                    'nombre_presentacion' => $objeto->pre_nom,
                    'avatar' => '../libs/img/product/' . $objeto->logo,
                    'mes' => $mes,
                    'dia' => $dia,
                    'estado' => $estado,
                );
            }
            $jsonstring = json_encode($json);
            echo $jsonstring;
            break;

        /**
         * CASO DE USO: Actualizar Stock de un Lote
         * --------------------------------------------------------------------
         * @route POST assets/controller/LoteController.php [funcion=editar]
         * @param int $_POST['id']    ID del lote a ajustar.
         * @param int $_POST['stock'] Nueva cantidad de existencias.
         * --------------------------------------------------------------------
         */
        case 'editar':
            $id_lote = isset($_POST['id']) ? $_POST['id'] : '';
            $stock = isset($_POST['stock']) ? $_POST['stock'] : '';
            $lote->editar($id_lote, $stock);
            break;

        /**
         * CASO DE USO: Eliminar Lote de Inventario
         * --------------------------------------------------------------------
         * @route POST assets/controller/LoteController.php [funcion=borrar_lote]
         * @param int $_POST['id'] ID del lote a eliminar.
         * --------------------------------------------------------------------
         */
        case 'borrar_lote':
            $id = $_POST['id'];
            $lote->borrar_lote($id);
            break;
    }
}
?>