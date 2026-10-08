<?php
/**
 * ============================================================================
 * ARCHIVO: ProductoController.php
 * CAPA: Controlador (Backend / Orquestación HTTP)
 * DESCRIPCIÓN: Administra el catálogo maestro de insumos médicos, materiales
 *              de protección individual (EPI) y suministros de la clínica.
 *              Orquesta la creación con asignación de lote/stock inicial opcional,
 *              edición, subida de imágenes, validación de existencias y comprobación de stock.
 * ENTRADA: Peticiones AJAX POST desde assets/libs/js/producto.js y carrito.js.
 * SALIDA: Arreglos JSON o respuestas de estado ('add', 'edit', 'borrado').
 * DEPENDENCIAS: assets/db/producto.php (Modelo Producto).
 * ============================================================================
 */

include_once '../db/producto.php';
$producto = new Producto();

if (isset($_POST['funcion'])) {
    $funcion = $_POST['funcion'];
    switch ($funcion) {
        /**
         * CASO DE USO: Registrar Nuevo Insumo / Producto Médico
         * --------------------------------------------------------------------
         * @route POST assets/controller/ProductoController.php [funcion=crear]
         * @param string $_POST['nombre']               Nombre comercial o genérico del insumo.
         * @param string $_POST['concentracion']        Concentración química o física.
         * @param string $_POST['adicional']            Información de uso clínico.
         * @param int    $_POST['prod_lab']             ID del fabricante/laboratorio.
         * @param int    $_POST['prod_tip_prod']        ID del tipo/categoría de insumo.
         * @param int    $_POST['prod_present']         ID de la presentación de empaque.
         * @param int    $_POST['id_unidad']            ID de la unidad de medida (defecto 1).
         * @param string $_POST['especificacion_talla'] Especificación de talla (S, M, L, etc.).
         * @param string $_POST['cod_lote']             (Opcional) Código del lote inicial.
         * @param int    $_POST['stock_inicial']        (Opcional) Cantidad de stock inicial.
         * @param int    $_POST['proveedor_lote']       (Opcional) ID del proveedor del lote.
         * @param string $_POST['vencimiento_lote']     (Opcional) Fecha de caducidad.
         * 
         * FLUJO DE EJECUCIÓN:
         *   1. Sanea variables con trim() y casting numérico.
         *   2. Empaqueta datos de lote inicial si fueron suministrados.
         *   3. Valida presencia de campos mínimos obligatorios.
         *   4. Invoca `Producto::crear(...)` ejecutando transacción atómica PDO.
         *   5. Emite 'add' en éxito o mensaje de validación.
         * --------------------------------------------------------------------
         */
        case 'crear':
            $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
            $concentracion = isset($_POST['concentracion']) ? trim($_POST['concentracion']) : '';
            $adicional = isset($_POST['adicional']) ? trim($_POST['adicional']) : '';
            $prod_lab = isset($_POST['prod_lab']) ? $_POST['prod_lab'] : '';
            $prod_tip_prod = isset($_POST['prod_tip_prod']) ? $_POST['prod_tip_prod'] : '';
            $prod_present = isset($_POST['prod_present']) ? $_POST['prod_present'] : '';
            $id_unidad = isset($_POST['id_unidad']) ? $_POST['id_unidad'] : 1;
            $especificacion_talla = isset($_POST['especificacion_talla']) ? trim($_POST['especificacion_talla']) : '';
            $avatar = 'ProductDefault.png';

            // Soporte opcional para registrar lote y stock inicial en la misma llamada
            $lote_data = null;
            if (!empty($_POST['cod_lote']) && !empty($_POST['stock_inicial']) && !empty($_POST['proveedor_lote'])) {
                $lote_data = [
                    'cod_lote' => trim($_POST['cod_lote']),
                    'stock' => intval($_POST['stock_inicial']),
                    'proveedor' => intval($_POST['proveedor_lote']),
                    'vencimiento' => !empty($_POST['vencimiento_lote']) ? trim($_POST['vencimiento_lote']) : '2035-12-31'
                ];
            }

            if (empty($nombre) || empty($prod_lab) || empty($prod_tip_prod) || empty($prod_present)) {
                echo 'Faltan datos obligatorios para el registro del insumo.';
            } else {
                $producto->crear($nombre, $concentracion, $adicional, $avatar, $prod_lab, $prod_tip_prod, $prod_present, $id_unidad, $especificacion_talla, $lote_data);
            }
            break;

        /**
         * CASO DE USO: Actualizar Imagen / Avatar de un Insumo
         * --------------------------------------------------------------------
         * @route POST assets/controller/ProductoController.php [funcion=cambiar_avatar]
         * @param int   $_POST['id_logo_prod'] ID del producto a modificar.
         * @param array $_FILES['foto']        Archivo de imagen subido (JPEG, PNG, BMP).
         * --------------------------------------------------------------------
         */
        case 'cambiar_avatar':
            $id = $_POST['id_logo_prod'];
            $avatar = $_POST['avatar'];
            if (
                ($_FILES['foto']['type'] == 'image/jpeg') ||
                ($_FILES['foto']['type'] == 'image/png') ||
                ($_FILES['foto']['type'] == 'image/jpg') ||
                ($_FILES['foto']['type'] == 'image/bmp')
            ) {
                $nombre = uniqid() . '-' . $_FILES['foto']['name'];
                $ruta = '../libs/img/product/' . $nombre;
                move_uploaded_file($_FILES['foto']['tmp_name'], $ruta);
                $producto->cambiar_avatar($id, $nombre);
                if ($avatar != 'ProductDefault.png' && $avatar != '../libs/img/product/ProductDefault.png' && file_exists($avatar)) {
                    unlink($avatar);
                }
                $json = array();
                $json[] = array(
                    'ruta' => $ruta,
                    'alert' => 'edit'
                );
                $jsonstring = json_encode($json[0]);
                echo $jsonstring;
            } else {
                $json = array();
                $json[] = array(
                    'alert' => 'noedit'
                );
                $jsonstring = json_encode($json[0]);
                echo $jsonstring;
            }
            break;

        /**
         * CASO DE USO: Buscar y Listar Insumos Médicos en Catálogo
         * --------------------------------------------------------------------
         * @route POST assets/controller/ProductoController.php [funcion=buscar_product]
         * @param string $_POST['consulta'] Filtro de búsqueda textual (opcional).
         * 
         * FLUJO DE EJECUCIÓN:
         *   1. Invoca `Producto::buscar($consulta)`.
         *   2. Realiza JOIN relacional entre `producto`, `laboratorio`, `tipo_producto`,
         *      `presentacion` y `unidad_medida`, sumando stock consolidado de lotes.
         *   3. Retorna colección estructurada en formato JSON para pintar las tarjetas.
         * --------------------------------------------------------------------
         */
        case 'buscar_product':
            $producto->buscar(isset($_POST['consulta']) ? $_POST['consulta'] : '');
            $json = array();
            foreach ($producto->objetos as $objeto) {
                $total = (int)($objeto['total_stock'] ?? 0);
                $json[] = array(
                    'id' => $objeto['id_producto'],
                    'nombre' => $objeto['nombre'],
                    'concentracion' => $objeto['concentracion'] ?? '',
                    'adicional' => $objeto['adicional'] ?? '',
                    'stock' => $total,
                    'nombre_laboratorio' => $objeto['nombre_laboratorio'],
                    'tipo' => $objeto['tipo'],
                    'nombre_presentacion' => $objeto['nombre_presentacion'],
                    'laboratorio_id' => $objeto['prod_lab'],
                    'tipo_id' => $objeto['prod_tip_prod'],
                    'presentacion_id' => $objeto['prod_present'],
                    'id_unidad' => $objeto['id_unidad'] ?? 1,
                    'unidad_medida' => $objeto['unidad_medida'] ?? 'Unidad',
                    'unidad_codigo' => $objeto['unidad_codigo'] ?? 'und',
                    'especificacion_talla' => $objeto['especificacion_talla'] ?? '',
                    'avatar' => '../libs/img/product/' . $objeto['avatar']
                );
            }
            $jsonstring = json_encode($json);
            echo $jsonstring;
            break;

        /**
         * CASO DE USO: Editar Datos Básicos de un Insumo
         * --------------------------------------------------------------------
         * @route POST assets/controller/ProductoController.php [funcion=editar]
         * @param int $_POST['id_edit_prod'] ID del producto a modificar.
         * --------------------------------------------------------------------
         */
        case 'editar':
            $id_edit_prod = isset($_POST['id_edit_prod']) ? $_POST['id_edit_prod'] : '';
            $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
            $concentracion = isset($_POST['concentracion']) ? trim($_POST['concentracion']) : '';
            $adicional = isset($_POST['adicional']) ? trim($_POST['adicional']) : '';
            $prod_lab = isset($_POST['prod_lab']) ? $_POST['prod_lab'] : '';
            $prod_tip_prod = isset($_POST['prod_tip_prod']) ? $_POST['prod_tip_prod'] : '';
            $prod_present = isset($_POST['prod_present']) ? $_POST['prod_present'] : '';
            $id_unidad = isset($_POST['id_unidad']) ? $_POST['id_unidad'] : 1;
            $especificacion_talla = isset($_POST['especificacion_talla']) ? trim($_POST['especificacion_talla']) : '';
            if (empty($nombre) || empty($prod_lab) || empty($prod_tip_prod) || empty($prod_present)) {
                echo 'Faltan datos obligatorios para actualizar el insumo.';
            } else {
                $producto->editar($id_edit_prod, $nombre, $concentracion, $adicional, $prod_lab, $prod_tip_prod, $prod_present, $id_unidad, $especificacion_talla);
            }
            break;

        /**
         * CASO DE USO: Eliminar Insumo del Catálogo
         * --------------------------------------------------------------------
         * @route POST assets/controller/ProductoController.php [funcion=borrar_produts]
         * @param int $_POST['id'] ID del insumo a dar de baja.
         * --------------------------------------------------------------------
         */
        case 'borrar_produts':
            $id = $_POST['id'];
            $producto->borrar_produts($id);
            break;

        /**
         * CASO DE USO: Validar Existencia Real de Insumos en Carrito
         * --------------------------------------------------------------------
         * @route POST assets/controller/ProductoController.php [funcion=validar_existencia]
         * @param string $_POST['ids'] JSON array con IDs de insumos en localStorage.
         * @description Purga insumos huérfanos del almacenamiento local si ya no
         *              existen en la base de datos tras vaciados o eliminaciones.
         * --------------------------------------------------------------------
         */
        case 'validar_existencia':
            $ids = isset($_POST['ids']) ? json_decode($_POST['ids'], true) : [];
            $validos = $producto->validar_existentes($ids);
            echo json_encode(array_map('strval', $validos));
            break;

        /**
         * CASO DE USO: Verificar Stock Disponible antes de Despachar
         * --------------------------------------------------------------------
         * @route POST assets/controller/ProductoController.php [funcion=verificarStock]
         * @param string $_POST['productos'] JSON array con insumos y cantidades solicitadas.
         * @return int Cantidad de errores detectados (0 si hay stock suficiente).
         * --------------------------------------------------------------------
         */
        case 'verificarStock':
            $error = 0;
            $productos = json_decode($_POST['productos']);
            foreach ($productos as $objeto) {
                $producto->obtener_stock($objeto->id);
                $total = 0;
                foreach ($producto->objetos as $obj) {
                    $total += $obj->total;
                }
                if (!($total >= $objeto->cantidad && $objeto->cantidad > 0)) {
                    $error++;
                }
            }
            echo $error;
            break;
    }
}
?>