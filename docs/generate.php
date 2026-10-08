<?php
/**
 * ============================================================================
 * SCRIPT: generate.php - Generador Autónomo de Documentación SIMAP (Ponytail)
 * ============================================================================
 * Escanea el código fuente en PHP en tiempo real, parsea los DocBlocks
 * de forma dinámica y genera tanto la vista Markdown para Docsify
 * (docs/controladores.md) como el manual imprimible (docs/manual_tecnico.html).
 * 
 * CERO DUPLICACIÓN MANUAL: La única fuente de verdad es el código .php.
 * ============================================================================
 */

$rootDir = dirname(__DIR__);
$controllerDir = $rootDir . '/assets/controller';
$modelDir = $rootDir . '/assets/db';
$outputPdfFile = __DIR__ . '/manual_tecnico.html';
$outputMdFile = __DIR__ . '/controladores.md';

// Función para extraer docblocks estructurados de un archivo PHP
function parseControllerDocBlocks($filePath) {
    $content = file_get_contents($filePath);
    $filename = basename($filePath);

    // 1. Extraer encabezado del archivo
    $fileDesc = '';
    if (preg_match('/\/\*\*\s*\n\s*\* =+\s*\n\s*\* ARCHIVO:\s*([^\n]+)\s*\n\s*\* CAPA:\s*([^\n]+)\s*\n\s*\* DESCRIPCIÓN:\s*([^\*]+)/s', $content, $mHeader)) {
        $fileDesc = trim(preg_replace('/^\s*\*\s*/m', '', $mHeader[3]));
    }

    // 2. Extraer casos de uso documentados
    // Busca bloques /** CASO DE USO: ... */ seguidos de case '...': o if ($funcion == '...')
    $useCases = [];
    $tokens = token_get_all($content);
    $currentDoc = '';

    foreach ($tokens as $token) {
        if (is_array($token)) {
            if ($token[0] === T_DOC_COMMENT) {
                $docText = $token[1];
                if (str_contains($docText, 'CASO DE USO:') || str_contains($docText, 'SERVICIO:')) {
                    $currentDoc = $docText;
                }
            } elseif ($token[0] === T_CONSTANT_ENCAPSED_STRING && !empty($currentDoc)) {
                $val = trim($token[1], "'\"");
                // Extraer título, ruta, parámetros y flujo del docblock
                preg_match('/\*\s*(?:CASO DE USO|SERVICIO):\s*([^\n]+)/', $currentDoc, $mTitle);
                preg_match('/@route\s+([^\n]+)/', $currentDoc, $mRoute);
                preg_match_all('/@param\s+([^\s]+)\s+([^\s]+)\s+([^\n]+)/', $currentDoc, $mParams, PREG_SET_ORDER);

                $params = [];
                foreach ($mParams as $p) {
                    $params[] = [
                        'type' => $p[1],
                        'name' => $p[2],
                        'desc' => $p[3]
                    ];
                }

                $useCases[$val] = [
                    'action' => $val,
                    'title' => trim($mTitle[1] ?? $val),
                    'route' => trim($mRoute[1] ?? ''),
                    'params' => $params,
                    'raw_doc' => $currentDoc
                ];
                $currentDoc = '';
            }
        }
    }

    return [
        'filename' => $filename,
        'description' => $fileDesc,
        'cases' => $useCases
    ];
}

// 1. Escanear todos los controladores
$controllersData = [];
foreach (glob($controllerDir . '/*.php') as $file) {
    $controllersData[basename($file)] = parseControllerDocBlocks($file);
}

// 2. Generar docs/controladores.md automáticamente desde el código
$md = "# Controladores del Sistema (Capa de Orquestación)\n\n";
$md .= "> **Generado automáticamente a partir del código fuente PHP (`assets/controller/`).**\n";
$md .= "> La única fuente de verdad son los comentarios DocBlocks en los archivos del sistema.\n\n---\n\n";

foreach ($controllersData as $ctrl) {
    $md .= "## " . $ctrl['filename'] . "\n\n";
    if (!empty($ctrl['description'])) {
        $md .= "**Descripción:** " . $ctrl['description'] . "\n\n";
    }

    if (!empty($ctrl['cases'])) {
        $md .= "### Casos de Uso Documentados en Código\n\n";
        $md .= "| Acción (`funcion`) | Ruta / Endpoint | Parámetros |\n";
        $md .= "| :--- | :--- | :--- |\n";
        foreach ($ctrl['cases'] as $case) {
            $paramStr = empty($case['params']) ? '*Ninguno*' : implode(', ', array_map(fn($p) => "`{$p['name']}`", $case['params']));
            $md .= "| `{$case['action']}` | `{$case['route']}` | {$paramStr} |\n";
        }
        $md .= "\n";
    } else {
        $md .= "_Controlador de acción directa o redirección de sesión._\n\n";
    }
    $md .= "---\n\n";
}

file_put_contents($outputMdFile, $md);
echo "Archivo Markdown generado dinámicamente: $outputMdFile\n";

// 3. Generar manual_tecnico.html imprimible
// Escanear modelos
$models = [];
foreach (glob($modelDir . '/*.php') as $file) {
    $filename = basename($file);
    if ($filename === 'conexion.php') continue;
    $content = file_get_contents($file);
    preg_match_all('/\/\*\*(.*?)\*\/\s*(?:public\s+)?function\s+([a-zA-Z0-9_]+)\s*\((.*?)\)/s', $content, $matches, PREG_SET_ORDER);
    $methods = [];
    foreach ($matches as $m) {
        $methods[] = ['name' => $m[2], 'params' => trim($m[3])];
    }
    $models[$filename] = ['file' => $filename, 'methods' => $methods];
}

ob_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>SIMAP - Manual Técnico y Arquitectura de Software</title>
    <style>
        @page { size: A4; margin: 1.8cm 1.5cm; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; color: #1e293b; line-height: 1.5; font-size: 10pt; background: #fff; margin: 0; padding: 20px; }
        .header-cover { text-align: center; padding: 30px 0; border-bottom: 3px solid #0284c7; margin-bottom: 25px; }
        .header-cover img { width: 70px; height: 70px; }
        .header-cover h1 { color: #0f172a; font-size: 20pt; margin: 8px 0; }
        h2 { color: #0f172a; font-size: 14pt; border-bottom: 1px solid #e2e8f0; padding-bottom: 5px; margin-top: 25px; }
        .doc-card { border: 1px solid #e2e8f0; border-radius: 6px; padding: 12px; margin-bottom: 12px; background: #f8fafc; page-break-inside: avoid; }
        .doc-card h4 { margin: 0 0 6px; color: #0284c7; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; font-size: 9pt; }
        th, td { border: 1px solid #cbd5e1; padding: 6px 8px; text-align: left; }
        th { background: #f1f5f9; }
        .btn-print { background: #0284c7; color: white; padding: 8px 16px; border-radius: 6px; font-weight: 600; cursor: pointer; border: none; float: right; }
        @media print { .btn-print { display: none !important; } body { padding: 0; } }
    </style>
</head>
<body>
    <button class="btn-print" onclick="window.print()">🖨️ Imprimir / Guardar en PDF</button>
    <div class="header-cover">
        <img src="../assets/libs/img/logo.png" alt="Logo">
        <h1>SIMAP &bull; Manual Técnico y Arquitectura</h1>
        <p style="color: #64748b; margin: 0;">Clínica Popular Especializada La Fría &bull; Generado automáticamente el <?php echo date('d/m/Y H:i'); ?></p>
    </div>

    <h2>1. Controladores del Sistema (Backend)</h2>
    <?php foreach ($controllersData as $ctrl): ?>
        <div class="doc-card">
            <h4><?php echo htmlspecialchars($ctrl['filename']); ?></h4>
            <p><?php echo htmlspecialchars($ctrl['description']); ?></p>
            <?php if (!empty($ctrl['cases'])): ?>
                <table>
                    <thead><tr><th>Acción</th><th>Ruta / Endpoint</th><th>Parámetros</th></tr></thead>
                    <tbody>
                        <?php foreach ($ctrl['cases'] as $c): ?>
                            <tr>
                                <td><code><?php echo htmlspecialchars($c['action']); ?></code></td>
                                <td><code><?php echo htmlspecialchars($c['route']); ?></code></td>
                                <td><?php echo empty($c['params']) ? '<em>Ninguno</em>' : htmlspecialchars(implode(', ', array_map(fn($p)=>$p['name'], $c['params']))); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>

    <h2>2. Modelos de Persistencia (SQLite / PDO)</h2>
    <?php foreach ($models as $mod): ?>
        <div class="doc-card">
            <h4><?php echo htmlspecialchars($mod['file']); ?></h4>
            <ul>
                <?php foreach ($mod['methods'] as $m): ?>
                    <li><strong><?php echo htmlspecialchars($m['name']); ?></strong>(<em><?php echo htmlspecialchars($m['params']); ?></em>)</li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endforeach; ?>
</body>
</html>
<?php
file_put_contents($outputPdfFile, ob_get_clean());
echo "Manual imprimible generado: $outputPdfFile\n";
