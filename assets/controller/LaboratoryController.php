<?php
/**
 * ============================================================================
 * ARCHIVO: LaboratoryController.php
 * CAPA: Controlador (Backend / Orquestación HTTP)
 * DESCRIPCIÓN: Administra las entidades de fabricantes y laboratorios farmacéuticos
 *              u organizaciones humanitarias donantes (INTERSOS, UNICEF, 3M, etc.).
 * ENTRADA: Peticiones AJAX POST desde assets/libs/js/laboratorio.js y producto.js.
 * SALIDA: Estados en texto plano ('add', 'edit', 'borrado') o arreglos en formato JSON.
 * DEPENDENCIAS: assets/db/laboratory.php (Modelo Laboratorio).
 * ============================================================================
 */

include_once '../db/laboratory.php';
$laboratorio = new laboratorio();

if (isset($_POST['funcion'])) {
    $funcion = $_POST['funcion'];
    switch ($funcion) {
        /**
         * CASO DE USO: Registrar Nuevo Fabricante o Laboratorio
         * --------------------------------------------------------------------
         * @route POST assets/controller/LaboratoryController.php [funcion=crear]
         * @param string $_POST['nombre_laboratory'] Denominación del laboratorio.
         * 
         * FLUJO DE EJECUCIÓN:
         *   1. Recibe el nombre del fabricante o entidad donante.
         *   2. Delega en `laboratorio::crear($nombre)`.
         *   3. Valida no duplicidad e inserta en la tabla `laboratorio`.
         *   4. Emite respuesta ('add' o mensaje de validación).
         * --------------------------------------------------------------------
         */
        case 'crear':
            $nombre = $_POST['nombre_laboratory'];
            $laboratorio->crear($nombre);
            break;

        /**
         * CASO DE USO: Editar Laboratorio Existente
         * --------------------------------------------------------------------
         * @route POST assets/controller/LaboratoryController.php [funcion=editar]
         * @param string $_POST['nombre_laboratory'] Nuevo nombre asignado.
         * @param int    $_POST['id_editado']          Identificador del laboratorio.
         * 
         * FLUJO DE EJECUCIÓN:
         *   1. Recibe el identificador y nuevo nombre.
         *   2. Delega en `laboratorio::editar($nombre, $id_editado)`.
         *   3. Ejecuta sentencia UPDATE parametrizada en SQLite.
         *   4. Emite confirmación 'edit'.
         * --------------------------------------------------------------------
         */
        case 'editar':
            $nombre = $_POST['nombre_laboratory'];
            $id_editado = $_POST['id_editado'];
            $laboratorio->editar($nombre, $id_editado);
            break;

        /**
         * CASO DE USO: Buscar y Filtrar Laboratorios
         * --------------------------------------------------------------------
         * @route POST assets/controller/LaboratoryController.php [funcion=buscar]
         * @param string $_POST['consulta'] Filtro textual de búsqueda (opcional).
         * 
         * FLUJO DE EJECUCIÓN:
         *   1. Ejecuta `laboratorio::buscar()` con filtro LIKE o listado general.
         *   2. Mapea la colección a un arreglo con id y nombre.
         *   3. Emite respuesta en formato JSON.
         * --------------------------------------------------------------------
         */
        case 'buscar':
            $laboratorio->buscar();
            $json = array();
            foreach ($laboratorio->objetos as $objeto) {
                $json[] = array(
                    'id' => $objeto->id_laboratorio,
                    'nombre' => $objeto->nombre
                );
            }
            echo json_encode($json);
            break;

        /**
         * CASO DE USO: Eliminar Fabricante o Laboratorio
         * --------------------------------------------------------------------
         * @route POST assets/controller/LaboratoryController.php [funcion=borrar_lab]
         * @param int $_POST['id'] Identificador del laboratorio a dar de baja.
         * 
         * FLUJO DE EJECUCIÓN:
         *   1. Recibe el ID del laboratorio.
         *   2. Invoca `laboratorio::borrar_lab($id)`.
         *   3. Ejecuta sentencia DELETE parametrizada.
         *   4. Emite 'borrado' o mensaje de error referencial.
         * --------------------------------------------------------------------
         */
        case 'borrar_lab':
            $id = $_POST['id'];
            $laboratorio->borrar_lab($id);
            break;

        /**
         * CASO DE USO: Poblar Selectores de Laboratorio (Dropdowns)
         * --------------------------------------------------------------------
         * @route POST assets/controller/LaboratoryController.php [funcion=rellenar_laboratorio]
         * @description Obtiene todos los fabricantes para alimentar la lista
         *              desplegable al crear o editar insumos médicos.
         * 
         * FLUJO DE EJECUCIÓN:
         *   1. Invoca `laboratorio::rellenar_laboratorio()`.
         *   2. Retorna colección en formato JSON [{ id, nombre }, ...].
         * --------------------------------------------------------------------
         */
        case 'rellenar_laboratorio':
            $laboratorio->rellenar_laboratorio();
            $json = array();
            foreach ($laboratorio->objetos as $objeto) {
                $json[] = array(
                    'id' => $objeto->id_laboratorio,
                    'nombre' => $objeto->nombre
                );
            }
            echo json_encode($json);
            break;
    }
}
?>
