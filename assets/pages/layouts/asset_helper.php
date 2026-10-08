<?php
/**
 * SIMAP - Asistente de Versionado y Cache-Busting de Activos
 *
 * Añade un parámetro de versión dinámico (`?v=timestamp`) a los archivos CSS/JS
 * según su fecha de última modificación física en disco (`filemtime`).
 * Evita la retención de archivos desactualizados en la memoria caché del navegador
 * tras actualizaciones del código.
 *
 * @package SIMAP\Layouts
 * @author Grupo de Proyecto
 * @version 1.0.0
 */

if (!function_exists('asset_v')) {
    /**
     * Generar URL con versión de archivo basada en tiempo de modificación.
     *
     * @param string $ruta_relativa Ruta relativa del activo web.
     * @return string Ruta relativa con parámetro `?v=timestamp`.
     */
    function asset_v($ruta_relativa) {
        $ruta_limpia = explode('?', $ruta_relativa)[0];
        $ruta_disco = __DIR__ . '/../../' . ltrim($ruta_limpia, './');
        
        if (file_exists($ruta_disco)) {
            $version = filemtime($ruta_disco);
        } else {
            $version = '1.0';
        }
        return $ruta_limpia . '?v=' . $version;
    }
}
?>
