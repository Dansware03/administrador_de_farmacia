<?php
/**
 * SIMAP Asset Cache-Buster Helper
 * Devuelve la ruta relativa del archivo adjuntando ?v=timestamp basándose en
 * la fecha real de modificación del archivo en disco.
 * Si el archivo se modifica o guarda, el timestamp cambia inmediatamente (idéntico a Vite / Webpack HMR).
 */
if (!function_exists('asset_v')) {
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
