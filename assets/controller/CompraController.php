<?php
include_once '../db/compra.php';
$compra = new Compra();
if (isset($_POST['funcion'])) {
    $funcion = $_POST['funcion'];
    switch ($funcion) {
        case 'registrar_compra':
            
            break;
    }
}
?>