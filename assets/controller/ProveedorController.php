<?php
/**
 * ============================================================================
 * ARCHIVO: ProveedorController.php
 * CAPA: Controlador (Backend / Orquestación HTTP)
 * DESCRIPCIÓN: Gestiona las entidades de proveedores comerciales, organismos
 *              humanitarios y donantes institucionales (INTERSOS, FUNREAHV, etc.).
 *              Orquesta altas, edición, subida de logos y consulta para poblar
 *              selectores en la recepción de lotes de insumos.
 * ENTRADA: Peticiones AJAX POST desde assets/libs/js/proveedor.js y lote.js.
 * SALIDA: Estados en texto plano ('add', 'edit', 'borrado') o arreglos en formato JSON.
 * DEPENDENCIAS: assets/db/Proveedor.php (Modelo Proveedor).
 * ============================================================================
 */

include_once '../db/Proveedor.php';
$proveedor = new Proveedor();

if (isset($_POST['funcion'])) {
    $funcion = $_POST['funcion'];
    switch ($funcion) {
        /**
         * CASO DE USO: Registrar Proveedor o Donante
         * --------------------------------------------------------------------
         * @route POST assets/controller/ProveedorController.php [funcion=crear]
         * @param string $_POST['nombre']    Nombre de la entidad proveedora o donante.
         * @param string $_POST['telefono']  Número de contacto telefónico.
         * @param string $_POST['correo']    Correo electrónico institucional (opcional).
         * @param string $_POST['direccion'] Ubicación física o sede operativa.
         * 
         * FLUJO DE EJECUCIÓN:
         *   1. Recepción y saneamiento de variables de entrada.
         *   2. Validación de presencia de campos obligatorios (nombre y teléfono).
         *   3. Delegación al Modelo: `Proveedor::crear(...)`.
         *   4. Respuesta: Imprime 'add' en éxito o mensaje de error.
         * --------------------------------------------------------------------
         */
        case 'crear':
            try {
                $nombre = isset($_POST['nombre']) ? $_POST['nombre'] : '';
                $telefono = isset($_POST['telefono']) ? $_POST['telefono'] : '';
                $correo = isset($_POST['correo']) ? $_POST['correo'] : '';
                $direccion = isset($_POST['direccion']) ? $_POST['direccion'] : '';
                $avatar = 'ProveedorDefault.png';
                
                // Validación básica
                if (empty($nombre) || empty($telefono)) {
                    echo 'Error: Nombre y teléfono son obligatorios';
                    break;
                }
                
                $proveedor->crear($nombre, $telefono, $correo, $direccion, $avatar);
            } catch (Exception $e) {
                error_log("Exception en crear proveedor: " . $e->getMessage());
                echo 'Error: ' . $e->getMessage();
            }
            break;

        /**
         * CASO DE USO: Actualizar Avatar o Logotipo del Proveedor
         * --------------------------------------------------------------------
         * @route POST assets/controller/ProveedorController.php [funcion=cambiar_avatar]
         * @param int   $_POST['id_logo_prod'] ID del proveedor a actualizar.
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
                $ruta = '../libs/img/proveedors/' . $nombre;
                move_uploaded_file($_FILES['foto']['tmp_name'], $ruta);
                $proveedor->cambiar_avatar($id, $nombre);
                if ($avatar != 'ProveedorDefault.png' && $avatar != '../libs/img/proveedors/ProveedorDefault.png' && file_exists($avatar)) {
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
         * CASO DE USO: Buscar y Listar Proveedores
         * --------------------------------------------------------------------
         * @route POST assets/controller/ProveedorController.php [funcion=buscar_prov]
         * @param string $_POST['consulta'] Filtro de búsqueda textual (opcional).
         * 
         * FLUJO DE EJECUCIÓN:
         *   1. Invoca `Proveedor::buscar($consulta)`.
         *   2. Mapea la colección a un arreglo asociativo en JSON.
         *   3. Emite respuesta serializada para renderizar la tabla de proveedores.
         * --------------------------------------------------------------------
         */
        case 'buscar_prov':
            try {
                $proveedor->buscar(isset($_POST['consulta']) ? $_POST['consulta'] : '');
                $json = array();
                foreach ($proveedor->objetos as $objeto) {
                    $json[] = array(
                        'id' => $objeto['id_proveedor'],
                        'nombre' => $objeto['nombre'],
                        'telefono' => $objeto['telefono'],
                        'correo' => $objeto['correo'],
                        'direccion' => $objeto['direccion'],
                        'fecha' => 'Fecha',
                        'avatar' => '../libs/img/proveedors/' . $objeto['avatar']
                    );
                }
                $jsonstring = json_encode($json);
                echo $jsonstring;
            } catch (Exception $e) {
                echo json_encode([]);
            }
            break;

        /**
         * CASO DE USO: Editar Datos de un Proveedor
         * --------------------------------------------------------------------
         * @route POST assets/controller/ProveedorController.php [funcion=editar]
         * @param int    $_POST['id_editado'] ID del proveedor a modificar.
         * @param string $_POST['nombre']     Nombre comercial actualizado.
         * @param string $_POST['telefono']   Teléfono actualizado.
         * @param string $_POST['correo']     Correo actualizado.
         * @param string $_POST['direccion']  Dirección actualizada.
         * --------------------------------------------------------------------
         */
        case 'editar':
            $id = isset($_POST['id_editado']) ? $_POST['id_editado'] : '';
            $nombre = isset($_POST['nombre']) ? $_POST['nombre'] : '';
            $telefono = isset($_POST['telefono']) ? $_POST['telefono'] : '';
            $correo = isset($_POST['correo']) ? $_POST['correo'] : '';
            $direccion = isset($_POST['direccion']) ? $_POST['direccion'] : '';

            $proveedor->editar($id, $nombre, $telefono, $correo, $direccion);
            break;

        /**
         * CASO DE USO: Eliminar Proveedor del Sistema
         * --------------------------------------------------------------------
         * @route POST assets/controller/ProveedorController.php [funcion=borrar_prove]
         * @param int $_POST['id'] ID del proveedor a dar de baja.
         * --------------------------------------------------------------------
         */
        case 'borrar_prove':
            $id = $_POST['id'];
            $proveedor->borrar_prove($id);
            break;

        /**
         * CASO DE USO: Poblar Selectores de Proveedores (Dropdowns)
         * --------------------------------------------------------------------
         * @route POST assets/controller/ProveedorController.php [funcion=rellenar_proveedor]
         * @description Obtiene los proveedores para poblar el menú desplegable
         *              al registrar nuevos lotes en el inventario.
         * --------------------------------------------------------------------
         */
        case 'rellenar_proveedor':
            $proveedor->rellenar_proveedor();
            $json = array();
            foreach ($proveedor->objetos as $objeto) {
                $json[] = array(
                    'id' => $objeto->id_proveedor,
                    'nombre' => $objeto->nombre
                );
            }
            $jsonstring = json_encode($json);
            echo $jsonstring;
            break;
    }
}
?>