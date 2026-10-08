<?php
/**
 * ============================================================================
 * SCRIPT: generate.php - Generador Autónomo de Documentación SIMAP (Ponytail)
 * ============================================================================
 * Escanea el código fuente en PHP en tiempo real, parsea los DocBlocks
 * de forma dinámica y genera tanto las vistas Markdown para Docsify
 * (docs/controladores.md, docs/modelos.md) como el manual imprimible
 * (docs/manual_tecnico.html).
 * 
 * CERO DUPLICACIÓN MANUAL: La única fuente de verdad es el código .php.
 * 
 * @author Grupo de Proyecto
 * @version 1.0.0
 * ============================================================================
 */

$rootDir = dirname(__DIR__);
$controllerDir = $rootDir . '/assets/controller';
$modelDir = $rootDir . '/assets/db';
$outputPdfFile = __DIR__ . '/manual_tecnico.html';
$outputControllersMd = __DIR__ . '/controladores.md';
$outputModelsMd = __DIR__ . '/modelos.md';

/**
 * Extrae y parsea los DocBlocks de un controlador PHP.
 *
 * @param string $filePath Ruta absoluta al archivo PHP.
 * @return array Metadatos del controlador y casos de uso.
 */
function parseControllerDocBlocks(string $filePath): array {
    $content = file_get_contents($filePath);
    $filename = basename($filePath);

    // 1. Extraer encabezado del archivo
    $fileDesc = '';
    if (preg_match('/\/\*\*\s*\n\s*\* =+\s*\n\s*\* ARCHIVO:\s*([^\n]+)\s*\n\s*\* CAPA:\s*([^\n]+)\s*\n\s*\* DESCRIPCIÓN:\s*([^\*]+?)(?=\n\s*\* (?:ENTRADA|SALIDA|DEPENDENCIAS|@author))/s', $content, $mHeader)) {
        $fileDesc = trim(preg_replace('/^\s*\*\s*/m', '', $mHeader[3]));
    }

    // 2. Extraer casos de uso documentados
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

/**
 * Extrae y parsea los DocBlocks de un modelo PDO PHP.
 *
 * @param string $filePath Ruta absoluta al archivo del modelo.
 * @return array Metadatos de la clase del modelo y sus métodos.
 */
function parseModelDocBlocks(string $filePath): array {
    $content = file_get_contents($filePath);
    $filename = basename($filePath);

    // Extraer descripción a nivel de clase/archivo
    $classDesc = '';
    if (preg_match('/\/\*\*\s*\n\s*\* ([^\n]+)\s*\n\s*\*(.*?)(?=@package|@author|\*\/)/s', $content, $mHeader)) {
        $firstLine = trim($mHeader[1]);
        $rest = trim(preg_replace('/^\s*\*\s*/m', '', $mHeader[2]));
        $classDesc = $firstLine . ($rest ? " - " . $rest : "");
    }

    // Extraer métodos y sus PHPDocs
    $methods = [];
    preg_match_all('/\/\*\*(.*?)\*\/\s*(?:public\s+|private\s+|protected\s+)?function\s+([a-zA-Z0-9_]+)\s*\((.*?)\)/s', $content, $matches, PREG_SET_ORDER);
    foreach ($matches as $m) {
        $rawDoc = $m[1];
        $methodName = $m[2];
        $methodParams = trim($m[3]);

        // Extraer primera línea de descripción del método
        $methodDesc = '';
        if (preg_match('/^\s*\*\s*([^\n@]+)/m', $rawDoc, $mDesc)) {
            $methodDesc = trim($mDesc[1]);
        }

        $methods[] = [
            'name' => $methodName,
            'params' => $methodParams,
            'desc' => $methodDesc
        ];
    }

    return [
        'file' => $filename,
        'description' => $classDesc,
        'methods' => $methods
    ];
}

// 1. Escanear controladores
$controllersData = [];
foreach (glob($controllerDir . '/*.php') as $file) {
    $controllersData[basename($file)] = parseControllerDocBlocks($file);
}

// 2. Escanear modelos
$modelsData = [];
foreach (glob($modelDir . '/*.php') as $file) {
    $filename = basename($file);
    if ($filename === 'conexion.php') continue;
    $modelsData[$filename] = parseModelDocBlocks($file);
}

// 3. Generar docs/controladores.md automáticamente desde el código
$mdControllers = "# Controladores del Sistema (Capa de Orquestación)\n\n";
$mdControllers .= "> **Generado automáticamente a partir del código fuente PHP (`assets/controller/`).**\n";
$mdControllers .= "> La única fuente de verdad son los comentarios DocBlocks en los archivos del sistema.\n\n---\n\n";

foreach ($controllersData as $ctrl) {
    $mdControllers .= "## " . $ctrl['filename'] . "\n\n";
    if (!empty($ctrl['description'])) {
        $mdControllers .= "**Descripción:** " . $ctrl['description'] . "\n\n";
    }

    if (!empty($ctrl['cases'])) {
        $mdControllers .= "### Casos de Uso Documentados en Código\n\n";
        $mdControllers .= "| Acción (`funcion`) | Ruta / Endpoint | Parámetros |\n";
        $mdControllers .= "| :--- | :--- | :--- |\n";
        foreach ($ctrl['cases'] as $case) {
            $paramStr = empty($case['params']) ? '*Ninguno*' : implode(', ', array_map(fn($p) => "`{$p['name']}`", $case['params']));
            $mdControllers .= "| `{$case['action']}` | `{$case['route']}` | {$paramStr} |\n";
        }
        $mdControllers .= "\n";
    } else {
        $mdControllers .= "_Controlador de acción directa o redirección de sesión._\n\n";
    }
    $mdControllers .= "---\n\n";
}
file_put_contents($outputControllersMd, $mdControllers);
echo "Archivo Markdown generado: $outputControllersMd\n";

// 4. Generar docs/modelos.md automáticamente desde el código
$mdModels = "# Modelos de Persistencia (Capa de Datos PDO)\n\n";
$mdModels .= "> **Generado automáticamente a partir del código fuente PHP (`assets/db/`).**\n";
$mdModels .= "> La capa de datos gestiona transacciones atómicas (ACID), consultas preparadas e integridad referencial en SQLite 3 (Modo WAL).\n\n---\n\n";

foreach ($modelsData as $mod) {
    $mdModels .= "## " . $mod['file'] . "\n\n";
    if (!empty($mod['description'])) {
        $mdModels .= "**Descripción:** " . $mod['description'] . "\n\n";
    }

    if (!empty($mod['methods'])) {
        $mdModels .= "### Métodos y Operaciones Disponibles\n\n";
        $mdModels .= "| Método | Parámetros | Propósito |\n";
        $mdModels .= "| :--- | :--- | :--- |\n";
        foreach ($mod['methods'] as $m) {
            $params = empty($m['params']) ? '*Sin parámetros*' : "`" . htmlspecialchars($m['params']) . "`";
            $desc = !empty($m['desc']) ? htmlspecialchars($m['desc']) : 'Operación de persistencia';
            $mdModels .= "| `{$m['name']}()` | {$params} | {$desc} |\n";
        }
        $mdModels .= "\n";
    }
    $mdModels .= "---\n\n";
}
file_put_contents($outputModelsMd, $mdModels);
echo "Archivo Markdown generado: $outputModelsMd\n";

// 5. Generar manual_tecnico.html imprimible
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
        h2 { color: #0f172a; font-size: 14pt; border-bottom: 2px solid #0284c7; padding-bottom: 5px; margin-top: 30px; }
        .doc-card { border: 1px solid #e2e8f0; border-radius: 6px; padding: 14px; margin-bottom: 14px; background: #f8fafc; page-break-inside: avoid; }
        .doc-card h4 { margin: 0 0 6px; color: #0284c7; font-size: 11pt; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 8.5pt; }
        th, td { border: 1px solid #cbd5e1; padding: 6px 8px; text-align: left; }
        th { background: #f1f5f9; font-weight: 600; color: #334155; }
        .btn-print { background: #0284c7; color: white; padding: 10px 18px; border-radius: 6px; font-weight: 600; cursor: pointer; border: none; float: right; box-shadow: 0 2px 6px rgba(2, 132, 199, 0.3); }
        .btn-print:hover { background: #0369a1; }
        @media print { .btn-print { display: none !important; } body { padding: 0; } }
    </style>
</head>
<body>
    <button class="btn-print" onclick="window.print()">🖨️ Imprimir / Guardar en PDF</button>
    <div class="header-cover">
        <img src="../assets/libs/img/logo.png" alt="Logo SIMAP">
        <h1>SIMAP &bull; Manual Técnico y Arquitectura</h1>
        <p style="color: #64748b; margin: 0;">Clínica Popular Especializada La Fría &bull; Documento generado automáticamente el <?php echo date('d/m/Y H:i'); ?></p>
        <p style="font-size: 9pt; color: #94a3b8; margin: 4px 0 0 0;">Autor: Grupo de Proyecto &bull; Versión 1.0.0</p>
    </div>

    <h2>1. Controladores del Sistema (Capa de Orquestación Backend)</h2>
    <?php foreach ($controllersData as $ctrl): ?>
        <div class="doc-card">
            <h4><?php echo htmlspecialchars($ctrl['filename']); ?></h4>
            <p><?php echo htmlspecialchars($ctrl['description']); ?></p>
            <?php if (!empty($ctrl['cases'])): ?>
                <table>
                    <thead><tr><th style="width: 25%;">Acción</th><th style="width: 45%;">Ruta / Endpoint</th><th style="width: 30%;">Parámetros</th></tr></thead>
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

    <h2>2. Modelos de Persistencia (Capa de Datos SQLite / PDO)</h2>
    <?php foreach ($modelsData as $mod): ?>
        <div class="doc-card">
            <h4><?php echo htmlspecialchars($mod['file']); ?></h4>
            <?php if (!empty($mod['description'])): ?>
                <p style="margin-bottom: 8px;"><?php echo htmlspecialchars($mod['description']); ?></p>
            <?php endif; ?>
            <?php if (!empty($mod['methods'])): ?>
                <table>
                    <thead><tr><th style="width: 28%;">Método</th><th style="width: 32%;">Parámetros</th><th style="width: 40%;">Propósito / Descripción</th></tr></thead>
                    <tbody>
                        <?php foreach ($mod['methods'] as $m): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($m['name']); ?>()</strong></td>
                                <td><code><?php echo htmlspecialchars($m['params'] ?: 'Sin parámetros'); ?></code></td>
                                <td><?php echo htmlspecialchars($m['desc'] ?: 'Operación del modelo'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</body>
</html>
<?php
file_put_contents($outputPdfFile, ob_get_clean());
echo "Manual imprimible generado: $outputPdfFile\n";
