<?php session_start();
if (!empty($_SESSION['us_tipo'])) {
    header('Location: assets/controller/LoginController.php');
    exit;
} else {
    session_destroy();
}
include_once 'headers.php'; ?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIMAP - Iniciar Sesión</title>
    <!-- Bootstrap 5 y Bootstrap Icons (Offline) -->
    <link rel="stylesheet" href="assets/libs/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/libs/css/bootstrap-icons.min.css">
    <link rel="stylesheet" href="assets/libs/css/animate.min.css">
    <!-- Estilos de Login SIMAP -->
    <link rel="stylesheet" href="assets/libs/css/login.css">
</head>

<body>
    <div class="container-fluid p-0">
        <div class="row g-0 min-vh-100">
            <!-- Columna Institucional / Banner (Desktop) -->
            <div class="col-lg-6 d-none d-lg-flex flex-column justify-content-between p-5 login-banner">
                <div>
                    <div class="banner-badge mb-4">
                        <i class="bi bi-shield-check text-warning"></i>
                        <span>Sistema Hospitalario Oficial</span>
                    </div>
                </div>

                <div class="text-center px-4">
                    <img src="assets/libs/img/doctores.svg" alt="Ilustración SIMAP" class="img-fluid mb-4" style="max-height: 280px;">
                    <h2 class="fw-bold tracking-tight mb-2">SIMAP</h2>
                    <p class="lead text-light-50 mb-1 fs-6">
                        Sistema de Inventario de Materiales e Insumos de Protección
                    </p>
                    <p class="text-white-50 small">
                        Clínica Popular Especializada La Fría
                    </p>
                </div>

                <div class="text-center text-white-50 small">
                    &copy; <?php echo date('Y'); ?> SIMAP. Todos los derechos reservados.
                </div>
            </div>

            <!-- Columna de Autenticación / Formulario -->
            <div class="col-12 col-lg-6 d-flex align-items-center justify-content-center p-4 p-md-5">
                <div class="login-card">
                    <!-- Cabecera Formulario -->
                    <div class="text-center mb-4">
                        <div class="d-inline-flex align-items-center justify-content-center bg-light rounded-circle p-3 mb-3 shadow-sm" style="width: 80px; height: 80px;">
                            <img src="assets/libs/img/logo.png" alt="Logo" class="img-fluid" style="max-height: 55px;">
                        </div>
                        <h3 class="fw-bold text-dark mb-1">Bienvenido</h3>
                        <p class="text-muted small">Ingrese sus credenciales para acceder al sistema</p>
                    </div>

                    <!-- Alerta de Credenciales Incorrectas -->
                    <div id="login-alert-error" class="alert alert-danger alert-dismissible fade show d-none shadow-sm" role="alert">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-exclamation-triangle-fill fs-5 me-2"></i>
                            <div>
                                <strong>Acceso denegado:</strong> Cédula o contraseña incorrectas.
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                    </div>

                    <!-- Formulario -->
                    <form id="login-form" action="assets/controller/LoginController.php" method="post" autocomplete="off">
                        <!-- Campo Cédula -->
                        <div class="mb-3">
                            <label for="login-user" class="form-label fw-semibold small text-secondary">
                                Cédula de Identidad
                            </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person-vcard"></i></span>
                                <input type="text" id="login-user" name="user" class="form-control" placeholder="Ej: 12345678" required autofocus>
                            </div>
                        </div>

                        <!-- Campo Contraseña -->
                        <div class="mb-4">
                            <label for="login-pass" class="form-label fw-semibold small text-secondary">
                                Contraseña
                            </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                <input type="password" id="login-pass" name="pass" class="form-control" placeholder="••••••••" required>
                                <button class="btn btn-outline-secondary btn-toggle-pass" type="button" id="toggle-password" title="Mostrar/ocultar contraseña">
                                    <i class="bi bi-eye" id="toggle-icon"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Botón de Envío -->
                        <div class="d-grid mb-3">
                            <button type="submit" id="btn-submit" class="btn btn-simap shadow-sm">
                                <i class="bi bi-box-arrow-in-right me-2"></i>Iniciar Sesión
                            </button>
                        </div>
                    </form>

                    <!-- Pie institucional (visible especialmente en móvil) -->
                    <div class="text-center mt-4 pt-2 border-top">
                        <small class="text-muted d-block">
                            Área de Mantenimiento y Protección Institucional
                        </small>
                        <small class="text-secondary fw-semibold">
                            Clínica Popular Especializada La Fría
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts Esenciales (Offline) -->
    <script src="assets/libs/js/bootstrap.bundle.min.js"></script>
    <script src="assets/libs/js/sweetalert2.all.min.js"></script>
    <script src="assets/libs/js/login.js"></script>
</body>

</html>