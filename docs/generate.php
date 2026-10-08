<?php
/**
 * ============================================================================
 * SCRIPT: generate.php - Generador Autónomo de Documentación Técnica SIMAP
 * ============================================================================
 * Escanea el código fuente del backend y genera un Manual Técnico unificado
 * en HTML con estilos de impresión de alta precisión para exportación directa
 * a PDF (Ctrl + P / Imprimir a PDF).
 * ============================================================================
 */

$rootDir = dirname(__DIR__);
$controllerDir = $rootDir . '/assets/controller';
$modelDir = $rootDir . '/assets/db';
$outputFile = __DIR__ . '/manual_tecnico.html';

// 1. Recopilar Modelos
$models = [];
foreach (glob($modelDir . '/*.php') as $file) {
    $filename = basename($file);
    if ($filename === 'conexion.php') continue;
    $content = file_get_contents($file);

    // Extraer docblocks
    preg_match_all('/\/\*\*(.*?)\*\/\s*(?:public\s+)?function\s+([a-zA-Z0-9_]+)\s*\((.*?)\)/s', $content, $matches, PREG_SET_ORDER);
    $methods = [];
    foreach ($matches as $m) {
        $methods[] = [
            'name' => $m[2],
            'params' => trim($m[3]),
            'doc' => trim($m[1])
        ];
    }
    $models[$filename] = [
        'file' => $filename,
        'path' => 'assets/db/' . $filename,
        'methods' => $methods
    ];
}

// 2. Recopilar Controladores
$controllers = [];
foreach (glob($controllerDir . '/*.php') as $file) {
    $filename = basename($file);
    $content = file_get_contents($file);

    // Extraer casos de uso
    preg_match_all('/case\s+[\'"]([a-zA-Z0-9_]+)[\'"]\s*:/', $content, $cases);
    $controllers[$filename] = [
        'file' => $filename,
        'path' => 'assets/controller/' . $filename,
        'actions' => $cases[1] ?? []
    ];
}

// 3. Generar HTML unificado para PDF
ob_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>SIMAP - Manual Técnico y Arquitectura de Software</title>
    <style>
        @page {
            size: A4;
            margin: 1.8cm 1.5cm;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            color: #1e293b;
            line-height: 1.5;
            font-size: 10pt;
            background: #fff;
            margin: 0;
            padding: 20px;
        }
        .header-cover {
            text-align: center;
            padding: 40px 0 30px;
            border-bottom: 3px solid #0284c7;
            margin-bottom: 30px;
        }
        .header-cover img {
            width: 75px;
            height: 75px;
            margin-bottom: 10px;
        }
        .header-cover h1 {
            color: #0f172a;
            font-size: 22pt;
            margin: 0 0 8px;
            font-weight: 800;
        }
        .header-cover .subtitle {
            color: #64748b;
            font-size: 11pt;
            margin: 0;
        }
        h2 {
            color: #0f172a;
            font-size: 15pt;
            border-bottom: 1.5px solid #e2e8f0;
            padding-bottom: 6px;
            margin-top: 25px;
            page-break-after: avoid;
        }
        h3 {
            color: #0369a1;
            font-size: 12pt;
            margin-top: 18px;
            page-break-after: avoid;
        }
        .doc-card {
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 12px 16px;
            margin-bottom: 14px;
            background: #f8fafc;
            page-break-inside: avoid;
        }
        .doc-card h4 {
            margin: 0 0 6px;
            color: #0284c7;
            font-size: 11pt;
        }
        .badge {
            background: #e0f2fe;
            color: #0369a1;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 8pt;
            font-weight: 600;
        }
        pre {
            background: #0f172a;
            color: #f8fafc;
            padding: 10px;
            border-radius: 6px;
            font-size: 8pt;
            overflow-x: auto;
            white-space: pre-wrap;
        }
        .print-btn-bar {
            text-align: right;
            margin-bottom: 20px;
        }
        .btn-print {
            background: #0284c7;
            color: white;
            padding: 8px 16px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            display: inline-block;
            cursor: pointer;
            border: none;
        }
        @media print {
            .print-btn-bar { display: none !important; }
            body { padding: 0; }
            .page-break { page-break-before: always; }
        }
    </style>
</head>
<body>
    <div class="print-btn-bar">
        <button class="btn-print" onclick="window.print()">🖨️ Imprimir / Guardar en PDF</button>
    </div>

    <div class="header-cover">
        <img src="../assets/libs/img/logo.png" alt="Logo SIMAP">
        <h1>SIMAP &bull; Manual Técnico de Arquitectura</h1>
        <p class="subtitle">Clínica Popular Especializada La Fría &bull; Generado automáticamente el <?php echo date('d/m/Y H:i'); ?></p>
    </div>

    <h2>1. Resumen de Arquitectura</h2>
    <p>
        El sistema implementa una arquitectura modular MVC nativa en PHP 8 y SQLite 3. Los datos fluyen de forma continua:
        <strong>Formulario HTML &rarr; Script AJAX (JS) &rarr; Controlador (PHP) &rarr; Modelo de Persistencia (PDO) &rarr; Almacén SQLite (Modo WAL)</strong>.
    </p>

    <h2>2. Controladores del Sistema (Capa de Orquestación)</h2>
    <?php foreach ($controllers as $ctrl): ?>
        <div class="doc-card">
            <h4><?php echo htmlspecialchars($ctrl['file']); ?> <span class="badge">Controlador</span></h4>
            <p><strong>Ubicación:</strong> <code><?php echo htmlspecialchars($ctrl['path']); ?></code></p>
            <p><strong>Acciones / Casos de uso registrados:</strong></p>
            <ul>
                <?php if (!empty($ctrl['actions'])): ?>
                    <?php foreach ($ctrl['actions'] as $act): ?>
                        <li><code><?php echo htmlspecialchars($act); ?></code></li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li><em>Acción única o controlador de flujo directo.</em></li>
                <?php endif; ?>
            </ul>
        </div>
    <?php endforeach; ?>

    <div class="page-break"></div>

    <h2>3. Modelos de Base de Datos y Persistencia PDO</h2>
    <?php foreach ($models as $mod): ?>
        <div class="doc-card">
            <h4><?php echo htmlspecialchars($mod['file']); ?> <span class="badge">Modelo PDO</span></h4>
            <p><strong>Ubicación:</strong> <code><?php echo htmlspecialchars($mod['path']); ?></code></p>
            <p><strong>Métodos disponibles:</strong></p>
            <ul>
                <?php foreach ($mod['methods'] as $m): ?>
                    <li>
                        <strong><?php echo htmlspecialchars($m['name']); ?></strong>(<em><?php echo htmlspecialchars($m['params']); ?></em>)
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endforeach; ?>

    <h2>4. Instrucciones para Exportación a PDF</h2>
    <p>
        Para generar el documento físico o digital oficial:
        <ol>
            <li>Presione <strong>Ctrl + P</strong> (o el botón Imprimir arriba a la derecha).</li>
            <li>En Destino, seleccione <strong>Guardar como PDF</strong>.</li>
            <li>En Configuración de página, asegúrese de seleccionar tamaño <strong>A4</strong> o <strong>Carta</strong> con gráficos de fondo activados.</li>
        </ol>
    </p>
</body>
</html>
<?php
$htmlContent = ob_get_clean();
file_put_contents($outputFile, $htmlContent);
echo "Manual técnico generado con éxito en: $outputFile\n";
