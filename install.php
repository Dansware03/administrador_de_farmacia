<?php
// install.php - Asistente de Instalación SIMAP
session_start();

$db_file = __DIR__ . '/assets/db/simap.db';
$schema_file = __DIR__ . '/assets/db/schema.sql';
$db_dir = __DIR__ . '/assets/db';
$img_dir = __DIR__ . '/assets/libs/img';

// Función para verificar si el sistema ya está completamente instalado
function esta_instalado($db_path) {
    if (!file_exists($db_path) || filesize($db_path) === 0) {
        return false;
    }
    try {
        $pdo = new PDO("sqlite:" . $db_path);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $pdo->query("SELECT COUNT(*) FROM usuario WHERE us_tipo = 1");
        return ($stmt && (int)$stmt->fetchColumn() > 0);
    } catch (Exception $e) {
        return false;
    }
}

// Si ya está instalado y no es una petición AJAX, redirigir al login
if (esta_instalado($db_file) && empty($_POST['action'])) {
    header('Location: index.php');
    exit;
}

// -------------------------------------------------------------
// MANEJO DE ACCIONES AJAX DEL ASISTENTE
// -------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['action'])) {
    header('Content-Type: application/json; charset=utf-8');
    $action = $_POST['action'];

    // 1. Diagnóstico de Requisitos
    if ($action === 'check_requirements') {
        $php_version_ok = version_compare(PHP_VERSION, '8.1.0', '>=');
        $ext_pdo = extension_loaded('pdo');
        $ext_sqlite = extension_loaded('pdo_sqlite');
        $ext_openssl = extension_loaded('openssl');
        $ext_mbstring = extension_loaded('mbstring');
        $ext_fileinfo = extension_loaded('fileinfo');
        $ext_session = extension_loaded('session');

        $writable_db = is_writable($db_dir) || (!file_exists($db_dir) && is_writable(dirname($db_dir)));
        $writable_img = is_writable($img_dir) || (!file_exists($img_dir) && is_writable(dirname($img_dir)));

        $all_ok = $php_version_ok && $ext_pdo && $ext_sqlite && $ext_openssl && $ext_mbstring && $ext_fileinfo && $ext_session && $writable_db && $writable_img;

        echo json_encode([
            'success' => true,
            'all_ok' => $all_ok,
            'data' => [
                'php_version' => ['ok' => $php_version_ok, 'val' => PHP_VERSION, 'req' => '>= 8.1.0'],
                'pdo' => ['ok' => $ext_pdo, 'val' => $ext_pdo ? 'Habilitada' : 'No disponible'],
                'pdo_sqlite' => ['ok' => $ext_sqlite, 'val' => $ext_sqlite ? 'Habilitada' : 'No disponible'],
                'openssl' => ['ok' => $ext_openssl, 'val' => $ext_openssl ? 'Habilitada' : 'No disponible'],
                'mbstring' => ['ok' => $ext_mbstring, 'val' => $ext_mbstring ? 'Habilitada' : 'No disponible'],
                'fileinfo' => ['ok' => $ext_fileinfo, 'val' => $ext_fileinfo ? 'Habilitada' : 'No disponible'],
                'session' => ['ok' => $ext_session, 'val' => $ext_session ? 'Habilitada' : 'No disponible'],
                'writable_db' => ['ok' => $writable_db, 'val' => $writable_db ? 'Escribible' : 'Solo Lectura'],
                'writable_img' => ['ok' => $writable_img, 'val' => $writable_img ? 'Escribible' : 'Solo Lectura']
            ]
        ]);
        exit;
    }

    // 2. Inicialización de Base de Datos
    if ($action === 'init_database') {
        if (!extension_loaded('pdo_sqlite')) {
            echo json_encode(['success' => false, 'message' => 'La extensión pdo_sqlite no está habilitada en el servidor.']);
            exit;
        }

        if (!file_exists($schema_file)) {
            echo json_encode(['success' => false, 'message' => 'No se encontró el archivo de esquema canónico schema.sql.']);
            exit;
        }

        try {
            // Eliminar archivos previos si existieran residuales
            if (file_exists($db_file)) @unlink($db_file);
            if (file_exists($db_file . '-wal')) @unlink($db_file . '-wal');
            if (file_exists($db_file . '-shm')) @unlink($db_file . '-shm');

            $pdo = new PDO("sqlite:" . $db_file, null, null, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_TIMEOUT => 5
            ]);

            // Configurar WAL mode para alto rendimiento y seguridad
            $pdo->exec("PRAGMA journal_mode = WAL;");
            $pdo->exec("PRAGMA synchronous = NORMAL;");
            $pdo->exec("PRAGMA foreign_keys = ON;");

            // Cargar y ejecutar DDL del esquema
            $sql = file_get_contents($schema_file);
            $pdo->exec($sql);

            echo json_encode(['success' => true, 'message' => 'Base de datos SQLite inicializada correctamente con catálogos base.']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error al crear la base de datos: ' . $e->getMessage()]);
        }
        exit;
    }

    // 3. Crear Usuario Administrador Principal
    if ($action === 'create_admin') {
        if (!file_exists($db_file)) {
            echo json_encode(['success' => false, 'message' => 'La base de datos aún no ha sido creada. Ejecute el paso 2 primero.']);
            exit;
        }

        $nombre = trim($_POST['nombre'] ?? '');
        $apellido = trim($_POST['apellido'] ?? '');
        $ci = trim($_POST['ci'] ?? '');
        $correo = trim($_POST['correo'] ?? '');
        $telefono = trim($_POST['telefono'] ?? '');
        $fecha_nac = trim($_POST['fecha_nac'] ?? '');
        $genero = trim($_POST['genero'] ?? 'hombre');
        $pass = $_POST['pass'] ?? '';
        $pass_confirm = $_POST['pass_confirm'] ?? '';

        if (empty($nombre) || empty($apellido) || empty($ci) || empty($fecha_nac) || empty($pass)) {
            echo json_encode(['success' => false, 'message' => 'Por favor complete todos los campos obligatorios.']);
            exit;
        }

        if ($pass !== $pass_confirm) {
            echo json_encode(['success' => false, 'message' => 'Las contraseñas no coinciden.']);
            exit;
        }

        if (strlen($pass) < 6) {
            echo json_encode(['success' => false, 'message' => 'La contraseña debe contener al menos 6 caracteres.']);
            exit;
        }

        try {
            $pdo = new PDO("sqlite:" . $db_file, null, null, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_TIMEOUT => 5
            ]);
            $pdo->exec("PRAGMA foreign_keys = ON;");

            // Verificar si ya existe administrador
            $stmt = $pdo->query("SELECT COUNT(*) FROM usuario WHERE us_tipo = 1");
            if ((int)$stmt->fetchColumn() > 0) {
                echo json_encode(['success' => false, 'message' => 'Ya existe un administrador registrado en el sistema.']);
                exit;
            }

            // Hashear contraseña con Bcrypt
            $hash = password_hash($pass, PASSWORD_BCRYPT);

            $insert = $pdo->prepare("INSERT INTO usuario (
                nombre_us, apellidos_us, fecha_nacimiento, ci_us, contrasena_us, 
                telefono_us, correo_us, genero_us, info_us, avatar, us_tipo
            ) VALUES (
                :nombre, :apellido, :fecha_nac, :ci, :pass,
                :telefono, :correo, :genero, 'Superadministrador Principal de SIMAP', 'user-default.png', 1
            )");

            $insert->execute([
                ':nombre' => $nombre,
                ':apellido' => $apellido,
                ':fecha_nac' => $fecha_nac,
                ':ci' => $ci,
                ':pass' => $hash,
                ':telefono' => $telefono,
                ':correo' => $correo,
                ':genero' => $genero
            ]);

            echo json_encode(['success' => true, 'message' => 'Superadministrador configurado con éxito.']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error al registrar administrador: ' . $e->getMessage()]);
        }
        exit;
    }

    echo json_encode(['success' => false, 'message' => 'Acción no válida.']);
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIMAP - Asistente de Instalación</title>
    <link rel="icon" type="image/png" href="assets/libs/img/logo.png">
    <link rel="stylesheet" href="assets/libs/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/libs/css/bootstrap-icons.min.css">
    <style>
        :root {
            --simap-primary: #0284c7;
            --simap-dark: #0f172a;
            --simap-card-bg: #ffffff;
            --simap-border: #e2e8f0;
        }

        body {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0369a1 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: system-ui, -apple-system, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            padding: 1.5rem;
            color: #334155;
        }

        .installer-card {
            background: var(--simap-card-bg);
            border-radius: 1rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.35);
            width: 100%;
            max-width: 780px;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .installer-header {
            background: #0f172a;
            color: #fff;
            padding: 1.75rem 2rem;
            border-bottom: 3px solid var(--simap-primary);
        }

        .step-indicator {
            display: flex;
            background: #f8fafc;
            border-bottom: 1px solid var(--simap-border);
            padding: 0.75rem 1rem;
        }

        .step-item {
            flex: 1;
            text-align: center;
            font-size: 0.85rem;
            font-weight: 600;
            color: #94a3b8;
            padding: 0.5rem 0.25rem;
            position: relative;
        }

        .step-item.active {
            color: var(--simap-primary);
        }

        .step-item.completed {
            color: #10b981;
        }

        .step-item i {
            font-size: 1.1rem;
            display: block;
            margin-bottom: 0.25rem;
        }

        .installer-body {
            padding: 2rem;
        }

        .step-pane {
            display: none;
        }

        .step-pane.active {
            display: block;
            animation: fadeIn 0.3s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(6px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .check-table td {
            padding: 0.65rem 0.75rem;
            vertical-align: middle;
            font-size: 0.9rem;
        }

        .btn-simap {
            background: var(--simap-primary);
            color: #fff;
            font-weight: 600;
            padding: 0.65rem 1.5rem;
            border-radius: 0.5rem;
            border: none;
            transition: all 0.2s ease;
        }

        .btn-simap:hover {
            background: #0369a1;
            color: #fff;
        }

        .form-label {
            font-size: 0.85rem;
            font-weight: 600;
            color: #475569;
            margin-bottom: 0.3rem;
        }

        .form-control, .form-select {
            border-radius: 0.45rem;
            border-color: #cbd5e1;
            padding: 0.55rem 0.75rem;
            font-size: 0.9rem;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--simap-primary);
            box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.15);
        }
    </style>
</head>

<body>
    <div class="installer-card">
        <!-- Encabezado Institucional -->
        <div class="installer-header d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-3">
                <img src="assets/libs/img/logo.png" alt="Logo SIMAP" class="bg-white p-1 rounded-circle" style="width: 52px; height: 52px; object-fit: contain;">
                <div>
                    <h5 class="mb-0 fw-bold tracking-tight">SIMAP &bull; Asistente de Instalación</h5>
                    <small class="text-white-50">Clínica Popular Especializada La Fría</small>
                </div>
            </div>
            <span class="badge bg-primary px-3 py-2">Configuración Inicial</span>
        </div>

        <!-- Indicador de Pasos (Stepper) -->
        <div class="step-indicator">
            <div class="step-item active" id="tab-step-1">
                <i class="bi bi-cpu"></i>
                1. Requisitos
            </div>
            <div class="step-item" id="tab-step-2">
                <i class="bi bi-database-check"></i>
                2. Base de Datos
            </div>
            <div class="step-item" id="tab-step-3">
                <i class="bi bi-person-badge"></i>
                3. Superadmin
            </div>
            <div class="step-item" id="tab-step-4">
                <i class="bi bi-check-circle"></i>
                4. Listo
            </div>
        </div>

        <!-- Contenido de los Pasos -->
        <div class="installer-body">
            <!-- ALERTA DINÁMICA -->
            <div id="alert-box" class="alert d-none mb-4" role="alert"></div>

            <!-- PASO 1: Diagnóstico de Entorno -->
            <div class="step-pane active" id="step-1">
                <h5 class="fw-bold mb-1 text-dark">Paso 1: Verificación del Entorno PHP</h5>
                <p class="text-muted small mb-4">Comprobando versión, extensiones críticas y permisos de escritura en el servidor local.</p>

                <div class="table-responsive mb-4">
                    <table class="table table-bordered table-sm check-table align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Componente / Extensión</th>
                                <th>Requerimiento</th>
                                <th>Estado Local</th>
                                <th class="text-center" style="width: 90px;">Resultado</th>
                            </tr>
                        </thead>
                        <tbody id="table-requirements-body">
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">
                                    <div class="spinner-border spinner-border-sm text-primary me-2"></div>
                                    Comprobando entorno...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <button class="btn btn-outline-secondary" onclick="loadRequirements()">
                        <i class="bi bi-arrow-clockwise me-1"></i> Reevaluar
                    </button>
                    <button id="btn-next-1" class="btn btn-simap" onclick="goToStep(2)" disabled>
                        Siguiente: Base de Datos <i class="bi bi-arrow-right ms-1"></i>
                    </button>
                </div>
            </div>

            <!-- PASO 2: Creación de Base de Datos -->
            <div class="step-pane" id="step-2">
                <h5 class="fw-bold mb-1 text-dark">Paso 2: Inicialización de Base de Datos SQLite</h5>
                <p class="text-muted small mb-4">Se creará el archivo <code>simap.db</code> con el esquema relacional limpio y catálogos institucionales.</p>

                <div class="card border mb-4 bg-light">
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-3">
                            <i class="bi bi-database fs-1 text-primary"></i>
                            <div>
                                <h6 class="fw-bold mb-1">Almacén Local Embebido SQLite (Modo WAL)</h6>
                                <p class="text-muted small mb-0">Ubicación destino: <code>assets/db/simap.db</code>. Se aplicarán 13 tablas relacionales con soporte de transacciones atómicas y claves foráneas.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="alert alert-info py-2 small d-flex align-items-center gap-2 mb-4">
                    <i class="bi bi-info-circle-fill fs-5"></i>
                    <div>La base de datos se inicializará completamente vacía de transacciones, lista para comenzar la operación formal del centro médico.</div>
                </div>

                <div class="d-flex justify-content-between">
                    <button class="btn btn-outline-secondary" onclick="goToStep(1)">
                        <i class="bi bi-arrow-left me-1"></i> Anterior
                    </button>
                    <button id="btn-create-db" class="btn btn-simap" onclick="initializeDatabase()">
                        <i class="bi bi-play-circle me-1"></i> Crear e Inicializar BD
                    </button>
                </div>
            </div>

            <!-- PASO 3: Registro de Administrador -->
            <div class="step-pane" id="step-3">
                <h5 class="fw-bold mb-1 text-dark">Paso 3: Creación de la Cuenta Principal (Superadmin)</h5>
                <p class="text-muted small mb-4">Configure la cuenta con privilegios totales de Administrador para gestionar el sistema.</p>

                <form id="form-admin" onsubmit="event.preventDefault(); submitAdmin();">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nombres <span class="text-danger">*</span></label>
                            <input type="text" id="admin_nombre" class="form-control" required placeholder="Ej: Administrador">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Apellidos <span class="text-danger">*</span></label>
                            <input type="text" id="admin_apellido" class="form-control" required placeholder="Ej: SIMAP">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Cédula de Identidad (Usuario Login) <span class="text-danger">*</span></label>
                            <input type="text" id="admin_ci" class="form-control" required placeholder="Ej: 12345678">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Fecha de Nacimiento <span class="text-danger">*</span></label>
                            <input type="date" id="admin_fecha_nac" class="form-control" required value="2000-01-01">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Correo Electrónico</label>
                            <input type="email" id="admin_correo" class="form-control" placeholder="admin@simap.gob.ve">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Teléfono de Contacto</label>
                            <input type="text" id="admin_telefono" class="form-control" placeholder="0414-1234567">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Género</label>
                            <select id="admin_genero" class="form-select">
                                <option value="hombre">Hombre</option>
                                <option value="mujer">Mujer</option>
                                <option value="otro">Otro</option>
                            </select>
                        </div>
                        <div class="col-md-6"></div>

                        <div class="col-md-6">
                            <label class="form-label">Contraseña <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" id="admin_pass" class="form-control" required minlength="6" placeholder="Mínimo 6 caracteres">
                                <button class="btn btn-outline-secondary btn-toggle-pass" type="button" onclick="togglePasswordVisibility('admin_pass', this)" title="Mostrar/ocultar contraseña">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Confirmar Contraseña <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" id="admin_pass_confirm" class="form-control" required minlength="6" placeholder="Repita la contraseña">
                                <button class="btn btn-outline-secondary btn-toggle-pass" type="button" onclick="togglePasswordVisibility('admin_pass_confirm', this)" title="Mostrar/ocultar contraseña">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <button type="button" class="btn btn-outline-secondary" onclick="goToStep(2)">
                            <i class="bi bi-arrow-left me-1"></i> Anterior
                        </button>
                        <button type="submit" id="btn-submit-admin" class="btn btn-simap">
                            <i class="bi bi-check-lg me-1"></i> Guardar y Finalizar
                        </button>
                    </div>
                </form>
            </div>

            <!-- PASO 4: Finalización -->
            <div class="step-pane text-center py-4" id="step-4">
                <div class="mb-3 text-success">
                    <i class="bi bi-patch-check-fill" style="font-size: 4rem;"></i>
                </div>
                <h4 class="fw-bold text-dark mb-2">¡Instalación Completada con Éxito!</h4>
                <p class="text-muted mb-4 mx-auto" style="max-width: 500px;">
                    El sistema SIMAP ha sido configurado correctamente. La base de datos SQLite se encuentra activa en modo WAL y la cuenta de Superadministrador ha sido creada.
                </p>

                <div class="card bg-light border p-3 mx-auto mb-4 text-start" style="max-width: 440px;">
                    <div class="small text-muted mb-1">Credenciales de Inicio de Sesión:</div>
                    <div class="fw-bold text-dark">Usuario: <span id="resumen-ci" class="text-primary">-</span></div>
                    <div class="small text-muted">Contraseña: <i>La indicada durante la instalación</i></div>
                </div>

                <a href="index.php" class="btn btn-simap px-4 py-2 fs-6">
                    <i class="bi bi-box-arrow-in-right me-1"></i> Iniciar Sesión en SIMAP
                </a>
            </div>
        </div>
    </div>

    <!-- Scripts del Asistente -->
    <script>
        let currentStep = 1;

        function showAlert(msg, type = 'danger') {
            const box = document.getElementById('alert-box');
            box.className = `alert alert-${type} mb-4`;
            box.innerHTML = `<i class="bi bi-exclamation-triangle-fill me-2"></i> ${msg}`;
            box.classList.remove('d-none');
            box.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }

        function hideAlert() {
            document.getElementById('alert-box').classList.add('d-none');
        }

        function togglePasswordVisibility(inputId, btn) {
            const input = document.getElementById(inputId);
            const icon = btn.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'bi bi-eye-slash';
            } else {
                input.type = 'password';
                icon.className = 'bi bi-eye';
            }
            input.focus();
        }

        function goToStep(step) {
            hideAlert();
            currentStep = step;
            document.querySelectorAll('.step-pane').forEach(el => el.classList.remove('active'));
            document.getElementById(`step-${step}`).classList.add('active');

            for (let i = 1; i <= 4; i++) {
                const tab = document.getElementById(`tab-step-${i}`);
                tab.classList.remove('active', 'completed');
                if (i === step) tab.classList.add('active');
                if (i < step) tab.classList.add('completed');
            }
        }

        async function loadRequirements() {
            hideAlert();
            const tbody = document.getElementById('table-requirements-body');
            tbody.innerHTML = `<tr><td colspan="4" class="text-center py-4 text-muted"><div class="spinner-border spinner-border-sm text-primary me-2"></div> Comprobando entorno...</td></tr>`;

            try {
                const fd = new FormData();
                fd.append('action', 'check_requirements');
                const res = await fetch('install.php', { method: 'POST', body: fd });
                const json = await res.json();

                if (!json.success) throw new Error('Error al evaluar los requerimientos');

                const d = json.data;
                const rows = [
                    { name: 'PHP Version', req: d.php_version.req, val: d.php_version.val, ok: d.php_version.ok },
                    { name: 'Extensión PDO', req: 'Habilitada', val: d.pdo.val, ok: d.pdo.ok },
                    { name: 'Extensión PDO SQLite', req: 'Habilitada', val: d.pdo_sqlite.val, ok: d.pdo_sqlite.ok },
                    { name: 'Extensión OpenSSL', req: 'Habilitada', val: d.openssl.val, ok: d.openssl.ok },
                    { name: 'Extensión Multibyte String (mbstring)', req: 'Habilitada', val: d.mbstring.val, ok: d.mbstring.ok },
                    { name: 'Extensión Fileinfo', req: 'Habilitada', val: d.fileinfo.val, ok: d.fileinfo.ok },
                    { name: 'Extensión Session', req: 'Habilitada', val: d.session.val, ok: d.session.ok },
                    { name: 'Permisos de Escritura (assets/db/)', req: 'Escribible', val: d.writable_db.val, ok: d.writable_db.ok },
                    { name: 'Permisos de Escritura (assets/libs/img/)', req: 'Escribible', val: d.writable_img.val, ok: d.writable_img.ok }
                ];

                let html = '';
                rows.forEach(r => {
                    const badge = r.ok 
                        ? '<span class="badge bg-success"><i class="bi bi-check-lg"></i> OK</span>' 
                        : '<span class="badge bg-danger"><i class="bi bi-x-lg"></i> Error</span>';
                    html += `<tr>
                        <td class="fw-semibold">${r.name}</td>
                        <td class="text-muted small">${r.req}</td>
                        <td class="small">${r.val}</td>
                        <td class="text-center">${badge}</td>
                    </tr>`;
                });
                tbody.innerHTML = html;

                const btnNext = document.getElementById('btn-next-1');
                if (json.all_ok) {
                    btnNext.removeAttribute('disabled');
                } else {
                    btnNext.setAttribute('disabled', 'disabled');
                    showAlert('El entorno no cumple con todos los requisitos críticos. Habilite las extensiones marcadas en rojo en su configuración php.ini.', 'danger');
                }
            } catch (err) {
                tbody.innerHTML = `<tr><td colspan="4" class="text-danger text-center">Error al conectar con el servidor: ${err.message}</td></tr>`;
                showAlert('No se pudo verificar el entorno local.', 'danger');
            }
        }

        async function initializeDatabase() {
            hideAlert();
            const btn = document.getElementById('btn-create-db');
            btn.setAttribute('disabled', 'disabled');
            btn.innerHTML = `<span class="spinner-border spinner-border-sm me-1"></span> Inicializando BD...`;

            try {
                const fd = new FormData();
                fd.append('action', 'init_database');
                const res = await fetch('install.php', { method: 'POST', body: fd });
                const json = await res.json();

                if (json.success) {
                    goToStep(3);
                } else {
                    showAlert(json.message || 'Error al inicializar la base de datos.', 'danger');
                    btn.removeAttribute('disabled');
                    btn.innerHTML = `<i class="bi bi-play-circle me-1"></i> Reintentar Inicialización`;
                }
            } catch (err) {
                showAlert('Fallo de conexión al inicializar la base de datos: ' + err.message, 'danger');
                btn.removeAttribute('disabled');
                btn.innerHTML = `<i class="bi bi-play-circle me-1"></i> Reintentar Inicialización`;
            }
        }

        async function submitAdmin() {
            hideAlert();
            const pass = document.getElementById('admin_pass').value;
            const passConfirm = document.getElementById('admin_pass_confirm').value;

            if (pass !== passConfirm) {
                showAlert('Las contraseñas no coinciden.', 'warning');
                return;
            }

            const btn = document.getElementById('btn-submit-admin');
            btn.setAttribute('disabled', 'disabled');
            btn.innerHTML = `<span class="spinner-border spinner-border-sm me-1"></span> Registrando...`;

            try {
                const fd = new FormData();
                fd.append('action', 'create_admin');
                fd.append('nombre', document.getElementById('admin_nombre').value);
                fd.append('apellido', document.getElementById('admin_apellido').value);
                fd.append('ci', document.getElementById('admin_ci').value);
                fd.append('fecha_nac', document.getElementById('admin_fecha_nac').value);
                fd.append('correo', document.getElementById('admin_correo').value);
                fd.append('telefono', document.getElementById('admin_telefono').value);
                fd.append('genero', document.getElementById('admin_genero').value);
                fd.append('pass', pass);
                fd.append('pass_confirm', passConfirm);

                const res = await fetch('install.php', { method: 'POST', body: fd });
                const json = await res.json();

                if (json.success) {
                    // Purgar carrito previo del navegador
                    localStorage.removeItem('productos');
                    document.getElementById('resumen-ci').textContent = document.getElementById('admin_ci').value;
                    goToStep(4);
                } else {
                    showAlert(json.message || 'Error al crear la cuenta de administrador.', 'danger');
                    btn.removeAttribute('disabled');
                    btn.innerHTML = `<i class="bi bi-check-lg me-1"></i> Guardar y Finalizar`;
                }
            } catch (err) {
                showAlert('Fallo de conexión al crear administrador: ' + err.message, 'danger');
                btn.removeAttribute('disabled');
                btn.innerHTML = `<i class="bi bi-check-lg me-1"></i> Guardar y Finalizar`;
            }
        }

        // Cargar diagnóstico al inicio
        document.addEventListener('DOMContentLoaded', () => {
            loadRequirements();
        });
    </script>
</body>
</html>
