<?php
// ponytail: controlador ultra simple para catálogos auxiliares SIMAP (áreas y unidades de medida)
include_once '../db/conexion.php';

$funcion = $_POST['funcion'] ?? '';

if ($funcion == 'cargar_areas') {
    $db = new Conexion();
    $sql = "SELECT id_area, nombre_area, nivel_riesgo FROM area_servicio ORDER BY nombre_area ASC";
    $query = $db->pdo->prepare($sql);
    $query->execute();
    $areas = $query->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($areas);
    exit();
}

if ($funcion == 'cargar_unidades') {
    $db = new Conexion();
    $sql = "SELECT id_unidad, nombre, codigo FROM unidad_medida ORDER BY nombre ASC";
    $query = $db->pdo->prepare($sql);
    $query->execute();
    $unidades = $query->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($unidades);
    exit();
}
