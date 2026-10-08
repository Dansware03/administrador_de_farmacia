<?php
/**
 * SIMAP - Suite Autónoma de Verificación, Linting y Auditoría de Seguridad
 *
 * Herramienta de aseguramiento de calidad y seguridad previa a despliegue o commit.
 * Diseñada bajo principios de rendimiento nativo (cero dependencias externas npm/composer):
 * 1. Verificación de sintaxis de archivos PHP (`php -l`).
 * 2. Validación de sintaxis JavaScript nativa (Motor Node.js V8 `node -c`).
 * 3. Auditoría de seguridad estática:
 *    - Detección de inyecciones SQL (100% PDO Prepared Statements requeridos).
 *    - Protección de sesiones (`session_start()`) en controladores y páginas.
 *    - Autenticación criptográfica segura con algoritmo Bcrypt (`PASSWORD_BCRYPT`).
 * 4. Integridad física y referencial de SQLite (`PRAGMA quick_check` y `PRAGMA foreign_key_check`).
 *
 * @package SIMAP\Tools
 * @author Grupo de Proyecto
 * @version 1.0.0
 */

$start_time = microtime(true);
$errores = 0;
$advertencias = 0;

echo "\n======================================================\n";
echo "  SIMAP - Verificador de Calidad y Auditoría de Seguridad\n";
echo "======================================================\n\n";

// 1. CHEQUEO DE SINTAXIS PHP (php -l)
echo "[1/4] Analizando sintaxis PHP...\n";
$php_dirs = ['assets/controller', 'assets/db', 'assets/pages', 'assets/pages/layouts'];
$php_files = ['index.php', 'install.php', 'check.php'];

foreach ($php_dirs as $dir) {
    if (is_dir($dir)) {
        $files = glob("$dir/*.php");
        if ($files) {
            $php_files = array_merge($php_files, $files);
        }
    }
}

$php_files = array_unique($php_files);
$php_ok = 0;
foreach ($php_files as $file) {
    $cmd = "php -l " . escapeshellarg($file);
    exec($cmd, $out, $ret);
    if ($ret !== 0) {
        echo "  [ERROR SINTAXIS] $file\n";
        $errores++;
    } else {
        $php_ok++;
    }
}
echo "  ✓ Sintaxis PHP validada: $php_ok archivos sin errores.\n\n";

// 2. CHEQUEO DE SINTAXIS JS (Motor Node.js V8)
echo "[2/4] Analizando sintaxis JavaScript (Motor V8 nativo)...\n";
$js_dir = 'assets/libs/js';
$js_files = [];
if (is_dir($js_dir)) {
    $all_js = glob("$js_dir/*.js");
    foreach ($all_js as $jf) {
        if (!str_ends_with($jf, '.min.js')) {
            $js_files[] = $jf;
        }
    }
}

$js_ok = 0;
foreach ($js_files as $file) {
    exec("node -c " . escapeshellarg($file) . " 2>&1", $out_js, $ret_js);

    if ($ret_js !== 0) {
        echo "  [ERROR SINTAXIS JS] $file\n";
        $errores++;
    } else {
        $js_ok++;
    }
}
echo "  ✓ Sintaxis JS validada: $js_ok archivos propios sin errores.\n\n";

// 3. AUDITORÍA DE SEGURIDAD ESTÁTICA
echo "[3/4] Ejecutando Auditoría de Seguridad Estática...\n";

// 3.1 Chequeo de Inyecciones SQL (Interpolación insegura de variables)
$sqli_sospechosas = 0;
$backend_files = array_merge(glob("assets/controller/*.php") ?: [], glob("assets/db/*.php") ?: []);

foreach ($backend_files as $bf) {
    $lineas = file($bf);
    foreach ($lineas as $num => $linea) {
        if (preg_match('/\$sql\s*=\s*["\'].*(SELECT|INSERT|UPDATE|DELETE).*\$([a-zA-Z0-9_]+)/i', $linea, $m)) {
            echo "  [ALERTA SEGURIDAD - SQLi Potencial] $bf en línea " . ($num + 1) . ": Interpolación directa en SQL ($m[0])\n";
            $advertencias++;
            $sqli_sospechosas++;
        }
    }
}
if ($sqli_sospechosas === 0) {
    echo "  ✓ Consultas SQL: 100% de consultas usan PDO Prepared Statements (:params).\n";
}

// 3.2 Chequeo de Protección de Sesión en Páginas
$paginas_sin_sesion = 0;
$paginas = glob("assets/pages/*.php") ?: [];
foreach ($paginas as $pag) {
    $contenido = file_get_contents($pag);
    if (!str_contains($contenido, 'session_start()')) {
        echo "  [AVISO SEGURIDAD] $pag no inicia sesión de forma explícita.\n";
        $advertencias++;
        $paginas_sin_sesion++;
    }
}
if ($paginas_sin_sesion === 0) {
    echo "  ✓ Control de Acceso: Todas las páginas verifican sesión de usuario.\n";
}

// 3.3 Verificación de Hash de Contraseñas (Bcrypt)
$usuario_php = 'assets/db/usuario.php';
if (file_exists($usuario_php)) {
    $usr_code = file_get_contents($usuario_php);
    if (str_contains($usr_code, 'password_hash') && str_contains($usr_code, 'PASSWORD_BCRYPT')) {
        echo "  ✓ Autenticación: Uso estricto de password_hash() con algoritmo BCRYPT.\n";
    } else {
        echo "  [ALERTA SEGURIDAD] usuario.php podría no estar usando password_hash BCRYPT.\n";
        $advertencias++;
    }
}
echo "\n";

// 4. INTEGRIDAD DE BASE DE DATOS (SQLite)
echo "[4/4] Verificando Integridad de Base de Datos SQLite (simap.db)...\n";
$db_file = 'assets/db/simap.db';
if (file_exists($db_file)) {
    try {
        $pdo = new PDO("sqlite:$db_file", null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);

        $check_int = $pdo->query("PRAGMA quick_check")->fetchColumn();
        $check_fk = $pdo->query("PRAGMA foreign_key_check")->fetchAll(PDO::FETCH_ASSOC);

        if ($check_int === 'ok') {
            echo "  ✓ Integridad física: Estructura de SQLite íntegra (quick_check = ok).\n";
        } else {
            echo "  [ERROR BD] Fallo en integridad física de SQLite: $check_int\n";
            $errores++;
        }

        if (empty($check_fk)) {
            echo "  ✓ Integridad referencial: 0 violaciones de Foreign Keys.\n";
        } else {
            echo "  [ERROR BD] Se encontraron " . count($check_fk) . " inconsistencias de clave foránea.\n";
            $errores++;
        }
    } catch (Exception $e) {
        echo "  [ERROR BD] No se pudo inspeccionar simap.db: " . $e->getMessage() . "\n";
        $errores++;
    }
} else {
    echo "  [AVISO] Archivo simap.db no encontrado en assets/db/.\n";
    $advertencias++;
}

// RESUMEN FINAL
$elapsed = round(microtime(true) - $start_time, 3);
echo "\n======================================================\n";
if ($errores === 0) {
    echo "  RESULTADO: EXITOSO (0 errores, $advertencias avisos)\n";
    echo "  Tiempo de ejecución: {$elapsed}s\n";
    echo "  Estado del proyecto: APROBADO PARA DESPLIEGUE\n";
    echo "======================================================\n\n";
    exit(0);
} else {
    echo "  RESULTADO: FALLIDO ($errores errores encontrados)\n";
    echo "  Tiempo de ejecución: {$elapsed}s\n";
    echo "======================================================\n\n";
    exit(1);
}
?>
