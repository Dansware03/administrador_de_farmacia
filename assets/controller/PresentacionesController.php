<?php
/**
 * ============================================================================
 * ARCHIVO: PresentacionesController.php
 * CAPA: Controlador (Backend / Orquestación HTTP)
 * DESCRIPCIÓN: Administra el catálogo de formas farmacéuticas y tipos de empaque
 *              o presentación de insumos médicos (Unidad, Litro, Galón, Caja x 50, etc.).
 * ENTRADA: Peticiones AJAX POST desde assets/libs/js/presentacion.js y producto.js.
 * SALIDA: Respuestas en texto plano ('add', 'edit', 'borrado') o arreglos en formato JSON.
 * DEPENDENCIAS: assets/db/presentaciones.php (Modelo Presentacion).
 * @author Grupo de Proyecto
 * @version 1.0.0
 * ============================================================================
 */

include_once '../db/presentaciones.php';
$presentacion = new presentacion();

if (isset($_POST['funcion'])) {
    $funcion = $_POST['funcion'];
    switch ($funcion) {
        /**
         * CASO DE USO: Registrar Nueva Presentación de Empaque
         * --------------------------------------------------------------------
         * @route POST assets/controller/PresentacionesController.php [funcion=crear]
         * @param string $_POST['nombre_pre'] Nombre descriptivo del empaque (Ej: 'Caja x 100').
         * 
         * FLUJO DE EJECUCIÓN:
         *   1. Captura la variable $_POST['nombre_pre'].
         *   2. Invoca el método `presentacion::crear($nombre)`.
         *   3. Valida no duplicidad e inserta el registro en la tabla `presentacion`.
         *   4. Emite respuesta ('add' o mensaje de validación).
         * --------------------------------------------------------------------
         */
        case 'crear':
            $nombre = $_POST['nombre_pre'];
            $presentacion->crear($nombre);
            break;

        /**
         * CASO DE USO: Editar Presentación Existente
         * --------------------------------------------------------------------
         * @route POST assets/controller/PresentacionesController.php [funcion=editar]
         * @param string $_POST['nombre_pre'] Nuevo nombre asignado a la presentación.
         * @param int    $_POST['id_editado'] Identificador único del registro a modificar.
         * 
         * FLUJO DE EJECUCIÓN:
         *   1. Captura el ID y nuevo nombre de la presentación.
         *   2. Delega en `presentacion::editar($nombre, $id_editado)`.
         *   3. Ejecuta sentencia UPDATE parametrizada en SQLite.
         *   4. Emite respuesta 'edit'.
         * --------------------------------------------------------------------
         */
        case 'editar':
            $nombre = $_POST['nombre_pre'];
            $id_editado = $_POST['id_editado'];
            $presentacion->editar($nombre, $id_editado);
            break;

        /**
         * CASO DE USO: Buscar y Filtrar Presentaciones
         * --------------------------------------------------------------------
         * @route POST assets/controller/PresentacionesController.php [funcion=buscar]
         * @param string $_POST['consulta'] Filtro textual de búsqueda (opcional).
         * 
         * FLUJO DE EJECUCIÓN:
         *   1. Ejecuta `presentacion::buscar()` con filtro LIKE o listado general.
         *   2. Mapea la colección a un arreglo asociativo [{ id, nombre }, ...].
         *   3. Emite respuesta serializada en formato JSON.
         * --------------------------------------------------------------------
         */
        case 'buscar':
            $presentacion->buscar();
            $json = array();
            foreach ($presentacion->objetos as $objeto) {
                $json[] = array(
                    'id' => $objeto->id_presentacion,
                    'nombre' => $objeto->nombre
                );
            }
            $jsonstring = json_encode($json);
            echo $jsonstring;
            break;

        /**
         * CASO DE USO: Eliminar Presentación de Empaque
         * --------------------------------------------------------------------
         * @route POST assets/controller/PresentacionesController.php [funcion=borrar_pre]
         * @param int $_POST['id'] Identificador de la presentación a eliminar.
         * 
         * FLUJO DE EJECUCIÓN:
         *   1. Recibe el ID de la presentación.
         *   2. Invoca `presentacion::borrar_pre($id)`.
         *   3. Ejecuta sentencia DELETE parametrizada en la tabla `presentacion`.
         *   4. Emite respuesta 'borrado' o mensaje de error de integridad referencial.
         * --------------------------------------------------------------------
         */
        case 'borrar_pre':
            $id = $_POST['id'];
            $presentacion->borrar_pre($id);
            break;

        /**
         * CASO DE USO: Poblar Selectores de Presentación (Dropdowns)
         * --------------------------------------------------------------------
         * @route POST assets/controller/PresentacionesController.php [funcion=rellenar_presentacion]
         * @description Obtiene todas las presentaciones para alimentar el selector
         *              desplegable al crear o editar insumos médicos en catálogo.
         * 
         * FLUJO DE EJECUCIÓN:
         *   1. Invoca `presentacion::rellenar_presentacion()`.
         *   2. Retorna colección en formato JSON [{ id, nombre }, ...].
         * --------------------------------------------------------------------
         */
        case 'rellenar_presentacion':
            $presentacion->rellenar_presentacion();
            $json = array();
            foreach ($presentacion->objetos as $objeto) {
                $json[] = array(
                    'id' => $objeto->id_presentacion,
                    'nombre' => $objeto->nombre
                );
            }
            $jsonstring = json_encode($json);
            echo $jsonstring;
            break;
    }
}
?>