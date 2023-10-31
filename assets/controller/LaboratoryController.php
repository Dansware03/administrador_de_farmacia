<?php
include_once '../db/laboratory.php';
$laboratorio = new laboratorio();
if ($_POST['funcion']=='crear') {
    $nombre = $_POST['nombre_laboratory'];
    $avatar='LabDefault.png';
    $laboratorio->crear($nombre,$avatar);
}
session_start();

?>