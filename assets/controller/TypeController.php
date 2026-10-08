<?php
/**
 * ============================================================================
 * ARCHIVO: TypeController.php
 * CAPA: Controlador (Backend / Orquestación HTTP)
 * DESCRIPCIÓN: Administra el catálogo de tipos o categorías de insumos médicos
 *              (Productos Químicos, Equipos de Limpieza, Equipos de Protección
 *              Individual EPI, Productos de Papel, Repuestos y Mantenimiento).
 * ENTRADA: Peticiones AJAX POST desde assets/libs/js/tipo.js y producto.js.
 * SALIDA: Respuestas en texto plano ('add', 'edit', 'borrado') o arreglos en formato JSON.
 * DEPENDENCIAS: assets/db/type.php (Modelo Tipo_Producto).
 * ============================================================================
 */

include_once '../db/type.php';
$tipo_producto = new tipo_producto();

if (isset($_POST['funcion'])) {
    $funcion = $_POST['funcion'];
    switch ($funcion) {
        /**
         * CASO DE USO: Registrar Nueva Categoría o Tipo de Insumo
         * --------------------------------------------------------------------
         * @route POST assets/controller/TypeController.php [funcion=crear]
         * @param string $_POST['nombre_type'] Nombre descriptivo de la categoría.
         * 
         * FLUJO DE EJECUCIÓN:
         *   1. Captura la variable $_POST['nombre_type'].
         *   2. Invoca el método `tipo_producto::crear($nombre)`.
         *   3. Valida no duplicidad e inserta el registro en la tabla `tipo_producto`.
         *   4. Emite respuesta ('add' o mensaje de validación).
         * --------------------------------------------------------------------
         */
        case 'crear':
            $nombre = $_POST['nombre_type'];
            $tipo_producto->crear($nombre);
            break;

        /**
         * CASO DE USO: Editar Categoría Existente
         * --------------------------------------------------------------------
         * @route POST assets/controller/TypeController.php [funcion=editar_type]
         * @param string $_POST['nombre_type'] Nuevo nombre asignado a la categoría.
         * @param int    $_POST['id_editado']  Identificador único de la categoría.
         * 
         * FLUJO DE EJECUCIÓN:
         *   1. Captura el ID y nuevo nombre de la categoría.
         *   2. Delega en `tipo_producto::editar($nombre, $id_editado)`.
         *   3. Ejecuta sentencia UPDATE parametrizada en SQLite.
         *   4. Emite respuesta 'edit'.
         * --------------------------------------------------------------------
         */
        case 'editar_type':
            $nombre = $_POST['nombre_type'];
            $id_editado = $_POST['id_editado'];
            $tipo_producto->editar($nombre, $id_editado);
            break;

        /**
         * CASO DE USO: Buscar y Listar Categorías de Insumos
         * --------------------------------------------------------------------
         * @route POST assets/controller/TypeController.php [funcion=buscar]
         * @param string $_POST['consulta'] Filtro textual de búsqueda (opcional).
         * 
         * FLUJO DE EJECUCIÓN:
         *   1. Invoca `tipo_producto::buscar()` con filtro LIKE o listado general.
         *   2. Mapea la colección a un arreglo asociativo [{ id, nombre }, ...].
         *   3. Emite respuesta serializada en formato JSON.
         * --------------------------------------------------------------------
         */
        case 'buscar':
            $tipo_producto->buscar();
            $json = array();
            foreach ($tipo_producto->objetos as $objeto) {
                $json[] = array(
                    'id' => $objeto->id_tip_prod,
                    'nombre' => $objeto->nombre
                );
            }
            $jsonstring = json_encode($json);
            echo $jsonstring;
            break;

        /**
         * CASO DE USO: Eliminar Categoría de Insumos
         * --------------------------------------------------------------------
         * @route POST assets/controller/TypeController.php [funcion=borrar_type]
         * @param int $_POST['id'] Identificador de la categoría a eliminar.
         * 
         * FLUJO DE EJECUCIÓN:
         *   1. Recibe el ID de la categoría.
         *   2. Invoca `tipo_producto::borrar_type($id)`.
         *   3. Ejecuta sentencia DELETE parametrizada en la tabla `tipo_producto`.
         *   4. Emite respuesta 'borrado' o mensaje de error de integridad referencial.
         * --------------------------------------------------------------------
         */
        case 'borrar_type':
            $id = $_POST['id'];
            $tipo_producto->borrar_type($id);
            break;

        /**
         * CASO DE USO: Poblar Selectores de Categorías (Dropdowns)
         * --------------------------------------------------------------------
         * @route POST assets/controller/TypeController.php [funcion=rellenar_type]
         * @description Obtiene todas las categorías para alimentar los menús
         *              desplegables al registrar o editar insumos en catálogo.
         * 
         * FLUJO DE EJECUCIÓN:
         *   1. Invoca `tipo_producto::rellenar_type()`.
         *   2. Retorna colección en formato JSON [{ id, nombre }, ...].
         * --------------------------------------------------------------------
         */
        case 'rellenar_type':
            $tipo_producto->rellenar_type();
            $json = array();
            foreach ($tipo_producto->objetos as $objeto) {
                $json[] = array(
                    'id' => $objeto->id_tip_prod,
                    'nombre' => $objeto->nombre
                );
            }
            $jsonstring = json_encode($json);
            echo $jsonstring;
            break;
    }
}
?>