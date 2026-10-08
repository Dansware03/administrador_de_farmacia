<?php
/**
 * SIMAP - Vista Unificada de Error y Manejo de Respuestas HTTP
 *
 * Presenta una interfaz visual intuitiva, accesible y corporativa para códigos
 * de estado HTTP de error (400, 401, 403, 404, 500, 503).
 *
 * Ubicación MVC: Capa de Vista (assets/pages/error.php).
 *
 * Características:
 * - Detección automática del código de estado (vía parámetro GET `code` o código nativo del servidor).
 * - Envío formal de encabezados HTTP con `http_response_code($code)`.
 * - Trazabilidad de sesión institucional: si el usuario está autenticado, adapta enlaces y barra de retorno a su rol.
 * - Integración con diseño SIMAP (Bootstrap 5, Bootstrap Icons, tokens cromáticos).
 * - Botón de diagnóstico seguro y detalles técnicos controlados.
 *
 * @package SIMAP\Views
 * @author Grupo de Proyecto
 * @version 1.0.0
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Determinar código HTTP
$raw_code = isset($_GET['code']) ? intval($_GET['code']) : http_response_code();
$allowed_codes = [400, 401, 403, 404, 405, 419, 500, 503];

if (!in_array($raw_code, $allowed_codes, true)) {
    $code = 404;
} else {
    $code = $raw_code;
}

// Establecer cabecera HTTP real
http_response_code($code);

// Configuración semántica y visual por código
$error_definitions = [
    400 => [
        'badge' => 'Petición Incorrecta',
        'badge_class' => 'bg-warning text-dark',
        'title' => 'Parámetros o Solicitud Inválida',
        'description' => 'La solicitud enviada contiene parámetros incorrectos o corruptos que impiden su procesamiento por el servidor.',
        'icon' => 'bi-exclamation-diamond-fill',
        'accent' => '#f59e0b',
        'tip' => 'Verifique que la URL o el formulario contenga los datos necesarios e intente nuevamente.'
    ],
    401 => [
        'badge' => 'No Autenticado',
        'badge_class' => 'bg-danger text-white',
        'title' => 'Autenticación Requerida',
        'description' => 'Debe iniciar sesión en el sistema SIMAP para acceder a los recursos o funciones solicitadas.',
        'icon' => 'bi-shield-lock-fill',
        'accent' => '#ef4444',
        'tip' => 'Ingrese sus credenciales en la pantalla de inicio de sesión.'
    ],
    403 => [
        'badge' => 'Acceso Restringido',
        'badge_class' => 'bg-danger text-white',
        'title' => 'Acceso Denegado / Prohibido',
        'description' => 'Su cuenta actual no dispone de los privilegios o rol institucional suficiente para acceder a este módulo.',
        'icon' => 'bi-shield-slash-fill',
        'accent' => '#dc2626',
        'tip' => 'Si requiere acceso a este módulo, solicite la elevación de permisos a la administración.'
    ],
    404 => [
        'badge' => 'Recurso No Localizado',
        'badge_class' => 'bg-secondary text-white',
        'title' => 'Página o Recurso No Encontrado',
        'description' => 'El enlace al que intenta acceder no existe, ha sido reubicado o el identificador solicitado es inexistente.',
        'icon' => 'bi-compass-fill',
        'accent' => '#0284c7',
        'tip' => 'Compruebe la dirección web o utilice los accesos directos del catálogo general.'
    ],
    405 => [
        'badge' => 'Método No Permitido',
        'badge_class' => 'bg-warning text-dark',
        'title' => 'Método HTTP No Soportado',
        'description' => 'La acción solicitada no admite el método de transferencia HTTP utilizado.',
        'icon' => 'bi-slash-circle-fill',
        'accent' => '#d97706',
        'tip' => 'Las operaciones de datos deben enviarse mediante los canales autorizados de la interfaz.'
    ],
    419 => [
        'badge' => 'Sesión Caducada',
        'badge_class' => 'bg-warning text-dark',
        'title' => 'Sesión o Token Expirado',
        'description' => 'Su tiempo de inactividad ha provocado el cierre preventivo de su sesión por motivos de seguridad institucional.',
        'icon' => 'bi-hourglass-bottom',
        'accent' => '#ea580c',
        'tip' => 'Reinicie sesión para reanudar sus operaciones con seguridad.'
    ],
    500 => [
        'badge' => 'Falla del Servidor',
        'badge_class' => 'bg-danger text-white',
        'title' => 'Error Interno del Sistema',
        'description' => 'Ocurrió una interrupción imprevista al procesar la solicitud en el servidor web o el motor de datos.',
        'icon' => 'bi-exclamation-triangle-fill',
        'accent' => '#b91c1c',
        'tip' => 'El equipo técnico puede diagnosticar la causa ejecutando la suite de auditoría del sistema.'
    ],
    503 => [
        'badge' => 'Servicio No Disponible',
        'badge_class' => 'bg-warning text-dark',
        'title' => 'Mantenimiento o Sobrecarga',
        'description' => 'El sistema SIMAP se encuentra temporalmente fuera de servicio debido a tareas de mantenimiento programado.',
        'icon' => 'bi-tools',
        'accent' => '#d97706',
        'tip' => 'Por favor, aguarde unos minutos y recargue la página.'
    ]
];

$info = $error_definitions[$code];

// Determinar enlaces de retorno según sesión
$user_type = $_SESSION['us_tipo'] ?? 0;
$user_name = $_SESSION['nombre_us'] ?? '';

$base_url = '/';

if ($user_type == 1) {
    $btn_home_url = '/assets/pages/adm_catalogo.php';
    $btn_home_label = 'Ir al Panel de Administrador';
} elseif ($user_type == 2) {
    $btn_home_url = '/assets/pages/tec_catalogo.php';
    $btn_home_label = 'Ir al Catálogo de Secretaría';
} else {
    $btn_home_url = '/index.php';
    $btn_home_label = 'Iniciar Sesión en SIMAP';
}

$page_title = "Error {$code} - {$info['badge']} | SIMAP";
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo htmlspecialchars($page_title); ?></title>
  <link rel="icon" type="image/png" href="/assets/libs/img/logo.png">

  <!-- Estilos Base y Tipografía Institucional (100% Offline) -->
  <link rel="stylesheet" href="/assets/libs/css/bootstrap.min.css">
  <link rel="stylesheet" href="/assets/libs/css/bootstrap-icons.min.css">

  <style>
    :root {
      --simap-primary: #1a3a5c;
      --simap-primary-dark: #0f243a;
      --simap-secondary: #00897b;
      --simap-accent: <?php echo $info['accent']; ?>;
      --simap-bg: #f8fafc;
      --simap-text: #1e293b;
      --simap-text-muted: #64748b;
      --simap-border: #e2e8f0;
    }

    body {
      font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
      background: radial-gradient(circle at 15% 15%, rgba(2, 136, 209, 0.05) 0%, transparent 45%),
                  radial-gradient(circle at 85% 85%, rgba(0, 137, 123, 0.05) 0%, transparent 45%),
                  var(--simap-bg);
      color: var(--simap-text);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      margin: 0;
    }

    /* Barra Superior Minimalista */
    .error-header {
      padding: 1rem 1.5rem;
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(10px);
      border-bottom: 1px solid var(--simap-border);
    }

    .error-brand {
      display: inline-flex;
      align-items: center;
      gap: 0.75rem;
      text-decoration: none;
      color: var(--simap-primary);
    }

    .error-brand img {
      width: 38px;
      height: 38px;
      object-fit: contain;
    }

    /* Tarjeta Principal de Error */
    .error-card {
      background: #ffffff;
      border: 1px solid var(--simap-border);
      border-radius: 1.25rem;
      box-shadow: 0 10px 30px -5px rgba(26, 58, 92, 0.08);
      overflow: hidden;
      transition: transform 0.2s ease;
    }

    .error-code-badge {
      font-size: 5.5rem;
      font-weight: 900;
      line-height: 1;
      letter-spacing: -2px;
      background: linear-gradient(135deg, var(--simap-primary) 0%, var(--simap-accent) 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      user-select: none;
    }

    .error-icon-bubble {
      width: 80px;
      height: 80px;
      border-radius: 50%;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      background-color: rgba(<?php 
        $hex = ltrim($info['accent'], '#');
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        echo "{$r}, {$g}, {$b}, 0.12";
      ?>);
      color: var(--simap-accent);
      font-size: 2.5rem;
      margin-bottom: 1.25rem;
    }

    .error-footer {
      margin-top: auto;
      padding: 1.25rem;
      font-size: 0.85rem;
      color: var(--simap-text-muted);
      border-top: 1px solid var(--simap-border);
      background: #ffffff;
    }

    .btn-simap-primary {
      background-color: var(--simap-primary);
      border-color: var(--simap-primary);
      color: #ffffff;
      font-weight: 600;
      padding: 0.65rem 1.4rem;
      border-radius: 0.5rem;
      transition: all 0.2s ease;
    }

    .btn-simap-primary:hover {
      background-color: var(--simap-primary-dark);
      border-color: var(--simap-primary-dark);
      color: #ffffff;
      transform: translateY(-1px);
      box-shadow: 0 4px 12px rgba(26, 58, 92, 0.2);
    }

    .btn-simap-secondary {
      background-color: #f1f5f9;
      border-color: #e2e8f0;
      color: var(--simap-text);
      font-weight: 600;
      padding: 0.65rem 1.4rem;
      border-radius: 0.5rem;
    }

    .btn-simap-secondary:hover {
      background-color: #e2e8f0;
      color: var(--simap-primary);
    }
  </style>
</head>
<body>

  <!-- Encabezado Institucional -->
  <header class="error-header d-flex justify-content-between align-items-center">
    <a href="<?php echo htmlspecialchars($btn_home_url); ?>" class="error-brand">
      <img src="/assets/libs/img/logo.png" alt="Logo C.P.E. La Fría">
      <div>
        <div class="fw-bold fs-5 lh-1">SIMAP</div>
        <small class="text-muted" style="font-size: 0.75rem;">Clínica Popular Especializada La Fría</small>
      </div>
    </a>

    <?php if ($user_type > 0): ?>
      <div class="d-flex align-items-center gap-2">
        <span class="badge bg-light text-dark border px-2 py-1 small">
          <i class="bi bi-person-circle me-1"></i><?php echo htmlspecialchars($user_name); ?>
        </span>
        <a href="/assets/controller/Logout.php" class="btn btn-sm btn-outline-danger" title="Cerrar sesión">
          <i class="bi bi-box-arrow-right"></i>
        </a>
      </div>
    <?php else: ?>
      <a href="/index.php" class="btn btn-sm btn-outline-primary fw-semibold">
        <i class="bi bi-box-arrow-in-right me-1"></i>Acceso
      </a>
    <?php endif; ?>
  </header>

  <!-- Contenedor Principal de Error -->
  <main class="container my-auto py-5">
    <div class="row justify-content-center">
      <div class="col-12 col-md-10 col-lg-8 col-xl-7">
        <div class="error-card p-4 p-md-5 text-center">

          <!-- Burbuja de Icono Temático -->
          <div class="error-icon-bubble">
            <i class="bi <?php echo $info['icon']; ?>"></i>
          </div>

          <!-- Código Numérico de Error -->
          <div class="error-code-badge mb-1"><?php echo $code; ?></div>

          <!-- Etiqueta de Estado -->
          <div>
            <span class="badge <?php echo $info['badge_class']; ?> px-3 py-2 rounded-pill fw-semibold mb-3">
              <?php echo htmlspecialchars($info['badge']); ?>
            </span>
          </div>

          <!-- Título y Mensaje Explicativo -->
          <h1 class="h3 fw-bold mb-3 text-dark"><?php echo htmlspecialchars($info['title']); ?></h1>
          <p class="text-secondary mb-4 mx-auto" style="max-width: 520px; font-size: 1.02rem;">
            <?php echo htmlspecialchars($info['description']); ?>
          </p>

          <!-- Tarjeta Informativa de Recomendación -->
          <div class="alert alert-light border text-start d-flex align-items-start gap-3 p-3 mb-4 mx-auto" style="max-width: 520px; border-radius: 0.75rem;">
            <i class="bi bi-info-circle-fill text-primary fs-5 mt-1"></i>
            <div class="small">
              <strong class="d-block text-dark mb-1">Recomendación del Sistema:</strong>
              <span class="text-muted"><?php echo htmlspecialchars($info['tip']); ?></span>
            </div>
          </div>

          <!-- Botones de Acción -->
          <div class="d-flex flex-column flex-sm-row justify-content-center gap-3">
            <button onclick="window.history.length > 1 ? window.history.back() : window.location.href='<?php echo $btn_home_url; ?>'" class="btn btn-simap-secondary d-inline-flex align-items-center justify-content-center gap-2">
              <i class="bi bi-arrow-left"></i> Volver a la página anterior
            </button>
            <a href="<?php echo htmlspecialchars($btn_home_url); ?>" class="btn btn-simap-primary d-inline-flex align-items-center justify-content-center gap-2">
              <i class="bi bi-house-door-fill"></i> <?php echo htmlspecialchars($btn_home_label); ?>
            </a>
          </div>

        </div>
      </div>
    </div>
  </main>

  <!-- Pie de Página Institucional -->
  <footer class="error-footer text-center">
    <div class="container d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2">
      <div>
        <strong>SIMAP &copy; <?php echo date('Y'); ?></strong> &bull; Clínica Popular Especializada La Fría
      </div>
      <div>
        <span class="badge bg-light text-secondary border">v1.0.0</span>
        <span class="text-muted ms-2">Inventario y Bioseguridad</span>
      </div>
    </div>
  </footer>

</body>
</html>
