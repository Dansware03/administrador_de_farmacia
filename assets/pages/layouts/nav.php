<?php
$current_page = basename($_SERVER['PHP_SELF']);
$user_type = $_SESSION['us_tipo'] ?? 0;
$home_url = ($user_type == 2) ? 'tec_catalogo.php' : 'adm_catalogo.php';
?>
  <link rel="icon" type="image/png" href="../libs/img/logo.png">
  <!-- Bootstrap 5, Bootstrap Icons y Animate.css (Offline) -->
  <link rel="stylesheet" href="../libs/css/bootstrap.min.css">
  <link rel="stylesheet" href="../libs/css/bootstrap-icons.min.css">
  <link rel="stylesheet" href="../libs/css/animate.min.css">
  <!-- Plugins locales (Offline) -->
  <link rel="stylesheet" href="../libs/css/toastr.min.css">
  <link rel="stylesheet" href="../libs/css/select2.min.css">
  <link rel="stylesheet" href="../libs/css/datatables.min.css">
  <!-- Estilos del Sistema SIMAP -->
  <link rel="stylesheet" href="<?php echo asset_v('../libs/css/app.css'); ?>">
  <link rel="stylesheet" href="<?php echo asset_v('../libs/css/main.css'); ?>">
</head>
<body>
<!-- Preloader Global SIMAP -->
<div id="simap-page-loader" class="simap-loader-overlay">
  <div class="text-center">
    <div class="spinner-border text-primary mb-2" role="status" style="width: 3rem; height: 3rem;">
      <span class="visually-hidden">Cargando SIMAP...</span>
    </div>
    <div class="fw-bold text-primary small">SIMAP</div>
  </div>
</div>

<div class="wrapper">

  <!-- Navbar Superior -->
  <header class="simap-navbar sticky-top d-flex align-items-center justify-content-between px-3 px-lg-4">
    <div class="d-flex align-items-center gap-3">
      <!-- Toggle Sidebar Desktop -->
      <button class="btn btn-sm btn-outline-secondary d-none d-lg-inline-flex align-items-center justify-content-center" id="sidebar-toggle-desktop" title="Alternar menú lateral" aria-label="Alternar menú lateral">
        <i class="bi bi-list fs-5"></i>
      </button>

      <!-- Toggle Sidebar Móvil (Offcanvas) -->
      <button class="btn btn-sm btn-outline-secondary d-lg-none d-inline-flex align-items-center justify-content-center" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarOffcanvas" aria-controls="sidebarOffcanvas" title="Abrir menú" aria-label="Abrir menú de navegación">
        <i class="bi bi-list fs-5"></i>
      </button>

      <!-- Título y Logo de Navbar Móvil -->
      <a href="<?php echo $home_url; ?>" class="d-flex align-items-center gap-2 text-decoration-none d-lg-none">
        <img src="../libs/img/logo.png" alt="Logo SIMAP" class="rounded-circle bg-white p-1 shadow-sm" style="width: 36px; height: 36px; object-fit: contain;">
        <span class="fw-bold text-primary fs-5 mb-0">SIMAP</span>
      </a>
    </div>

    <!-- Menú Derecho -->
    <div class="d-flex align-items-center gap-2 gap-md-3">
      <!-- Centro de Alertas: Lotes en Riesgo (Dropdown Estilo Artículos Solicitados) -->
      <div id="cat-alertas-lotes" class="dropdown">
        <button class="btn btn-light position-relative border" type="button" id="lotesAlertaDropdown" data-bs-toggle="dropdown" aria-expanded="false" title="Lotes en riesgo o próximos a vencer" aria-label="Lotes en riesgo">
          <i class="bi bi-clock-history fs-5 text-secondary"></i>
          <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger d-none" id="badge-lotes-alerta">0</span>
        </button>
        <div class="dropdown-menu dropdown-menu-end p-3 shadow-lg border-0" aria-labelledby="lotesAlertaDropdown" style="width: 360px; max-width: 90vw;">
          <div class="d-flex align-items-center justify-content-between border-bottom pb-2 mb-2">
            <h6 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
              <i class="bi bi-exclamation-triangle-fill text-warning"></i>
              <span>Lotes en Riesgo</span>
            </h6>
            <span class="badge bg-light text-dark border small" id="total-lotes-alerta-badge">0 alertas</span>
          </div>
          <div class="table-responsive mb-2" style="max-height: 260px;">
            <table class="table table-sm align-middle mb-0">
              <tbody id="lista_lotes_navbar">
                <tr><td class="text-center py-3 text-muted small"><i class="bi bi-shield-check text-success me-1"></i>Sin lotes en riesgo</td></tr>
              </tbody>
            </table>
          </div>
          <div class="pt-2 border-top">
            <a class="btn btn-outline-primary btn-sm w-100 fw-semibold d-flex align-items-center justify-content-center gap-1" href="adm_lote.php">
              <i class="bi bi-collection"></i> Ir a Gestión de Lotes
            </a>
          </div>
        </div>
      </div>

      <!-- Carrito Dropdown (Preservando IDs para carrito.js) -->
      <div id="cat-carrito" <?php echo in_array($current_page, ['adm_catalogo.php', 'tec_catalogo.php']) ? '' : 'style="display: none;"'; ?> class="dropdown">
        <button class="btn btn-light position-relative border" type="button" id="navbarDropdown" data-bs-toggle="dropdown" aria-expanded="false" title="Ver carrito" aria-label="Ver artículos en solicitud">
          <i class="bi bi-cart3 fs-5"></i>
          <span class="contador position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="contador">0</span>
        </button>
        <div class="dropdown-menu dropdown-menu-end p-3 shadow-lg" aria-labelledby="navbarDropdown" style="width: 320px;">
          <h6 class="fw-bold border-bottom pb-2 mb-2"><i class="bi bi-cart-check me-2 text-primary"></i>Artículos Solicitados</h6>
          <div class="table-responsive mb-2" style="max-height: 220px;">
            <table class="carro table table-sm table-hover mb-0">
              <thead class="table-light small">
                <tr>
                  <th>Código</th>
                  <th>Ítem</th>
                  <th>Cant.</th>
                  <th>Acción</th>
                </tr>
              </thead>
              <tbody id="lista_carrito"></tbody>
            </table>
          </div>
          <div class="d-grid gap-2">
            <a class="btn btn-primary btn-sm" href="adm_retiro.php" id="Procesar_pedido">
              <i class="bi bi-check2-circle me-1"></i>Procesar Entrega
            </a>
            <a class="btn btn-outline-danger btn-sm" href="#" id="vaciar_carrito">
              <i class="bi bi-trash me-1"></i>Vaciar Carrito
            </a>
          </div>
        </div>
      </div>

      <!-- Usuario y Botón Cerrar Sesión -->
      <div class="d-flex align-items-center gap-2">
        <span class="d-none d-md-inline small fw-semibold text-secondary">
          <?php echo htmlspecialchars($_SESSION['nombre_us'] ?? 'Usuario'); ?>
        </span>
        <a class="btn btn-sm btn-outline-danger d-inline-flex align-items-center gap-1" href="../controller/Logout.php" id="btn-logout" role="button" title="Cerrar sesión" aria-label="Cerrar sesión">
          <i class="bi bi-box-arrow-right"></i>
          <span class="d-none d-sm-inline">Salir</span>
        </a>
      </div>
    </div>
  </header>

  <!-- Sidebar Desktop (Fijo) -->
  <aside class="simap-sidebar simap-sidebar-desktop d-none d-lg-flex flex-column">
    <!-- Brand Logo Showcase -->
    <a href="<?php echo $home_url; ?>" class="simap-brand">
      <div class="simap-brand-badge">
        <img src="../libs/img/logo.png" alt="Logo Clínica" class="rounded-circle img-fluid" style="width: 42px; height: 42px; object-fit: contain;">
      </div>
      <div class="overflow-hidden">
        <div class="simap-brand-title">SIMAP</div>
        <div class="simap-brand-sub text-truncate">C.P.E. La Fría</div>
      </div>
    </a>

    <!-- Info Usuario (User Card) -->
    <div class="simap-user-panel d-flex align-items-center gap-3">
      <div class="simap-user-avatar-wrap">
        <img id="avatar4" src="../libs/img/avatars/user-default.png" class="rounded-circle border border-2 border-white shadow-sm" alt="Avatar" style="width: 40px; height: 40px; object-fit: cover;">
        <span class="simap-user-status-dot" title="En línea"></span>
      </div>
      <div class="overflow-hidden flex-grow-1">
        <div class="small fw-semibold text-white text-truncate"><?php echo htmlspecialchars($_SESSION['nombre_us'] ?? 'Usuario'); ?></div>
        <div class="mt-1">
          <?php if ($user_type == 1): ?>
            <span class="simap-role-chip simap-role-admin"><i class="bi bi-shield-check"></i>Administrador</span>
          <?php else: ?>
            <span class="simap-role-chip simap-role-secretario"><i class="bi bi-person-badge"></i>Secretario</span>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <!-- Menú de Navegación -->
    <nav class="flex-grow-1 py-2">
      <?php if ($user_type == 2): ?>
        <!-- Menú Exclusivo para Secretario -->
        <div class="nav-header">Catálogo</div>
        <a href="tec_catalogo.php" class="nav-link <?php echo ($current_page == 'tec_catalogo.php') ? 'active' : ''; ?>">
          <i class="bi bi-grid"></i>
          <span>Catálogo de Insumos</span>
        </a>

        <div class="nav-header">Mi Cuenta</div>
        <a href="edit_data_personal.php" class="nav-link <?php echo ($current_page == 'edit_data_personal.php') ? 'active' : ''; ?>">
          <i class="bi bi-person-gear"></i>
          <span>Mis Datos Personales</span>
        </a>
      <?php else: ?>
        <!-- Menú Completo para Administrador y Root -->
        <div class="nav-header">Catálogo</div>
        <a href="adm_catalogo.php" class="nav-link <?php echo ($current_page == 'adm_catalogo.php') ? 'active' : ''; ?>">
          <i class="bi bi-grid"></i>
          <span>Catálogo Principal</span>
        </a>

        <div class="nav-header">Administración</div>
        <a href="edit_data_personal.php" class="nav-link <?php echo ($current_page == 'edit_data_personal.php') ? 'active' : ''; ?>">
          <i class="bi bi-person-gear"></i>
          <span>Datos de Usuario</span>
        </a>
        <a href="adm_usuario.php" class="nav-link <?php echo ($current_page == 'adm_usuario.php') ? 'active' : ''; ?>">
          <i class="bi bi-people"></i>
          <span>Usuarios</span>
        </a>

        <div class="nav-header">Despachos y Entregas</div>
        <a href="adm_retiro.php" class="nav-link <?php echo ($current_page == 'adm_retiro.php') ? 'active' : ''; ?>">
          <i class="bi bi-cart-check"></i>
          <span>Procesar Despacho</span>
        </a>
        <a href="adm_despachos.php" class="nav-link <?php echo in_array($current_page, ['adm_despachos.php', 'adm_editar_despacho.php']) ? 'active' : ''; ?>">
          <i class="bi bi-box-arrow-up-right"></i>
          <span>Historial de Despachos</span>
        </a>

        <div class="nav-header">Depósito de Insumos</div>
        <a href="adm_productos.php" class="nav-link <?php echo ($current_page == 'adm_productos.php') ? 'active' : ''; ?>">
          <i class="bi bi-shield-shaded"></i>
          <span>Gestión de Insumos</span>
        </a>
        <a href="adm_atributos.php" class="nav-link <?php echo ($current_page == 'adm_atributos.php') ? 'active' : ''; ?>">
          <i class="bi bi-tags"></i>
          <span>Gestión de Atributos</span>
        </a>
        <a href="adm_lote.php" class="nav-link <?php echo ($current_page == 'adm_lote.php') ? 'active' : ''; ?>">
          <i class="bi bi-collection"></i>
          <span>Gestión de Lotes</span>
        </a>

        <div class="nav-header">Proveedores y Dotación</div>
        <a href="adm_proveedor.php" class="nav-link <?php echo ($current_page == 'adm_proveedor.php') ? 'active' : ''; ?>">
          <i class="bi bi-truck"></i>
          <span>Directorio de Proveedores</span>
        </a>
      <?php endif; ?>
    </nav>
  </aside>

  <!-- Sidebar Móvil (Offcanvas de Bootstrap 5) -->
  <div class="offcanvas offcanvas-start simap-sidebar p-0 d-lg-none" tabindex="-1" id="sidebarOffcanvas" aria-labelledby="sidebarOffcanvasLabel">
    <div class="offcanvas-header simap-brand justify-content-between">
      <div class="d-flex align-items-center gap-2">
        <div class="simap-brand-badge" style="width: 38px; height: 38px;">
          <img src="../libs/img/logo.png" alt="Logo" class="rounded-circle img-fluid" style="width: 34px; height: 34px; object-fit: contain;">
        </div>
        <div>
          <div class="simap-brand-title fs-6">SIMAP</div>
          <div class="simap-brand-sub" style="font-size: 0.65rem;">C.P.E. La Fría</div>
        </div>
      </div>
      <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Cerrar"></button>
    </div>
    <div class="offcanvas-body p-0 d-flex flex-column">
      <div class="simap-user-panel d-flex align-items-center gap-3">
        <div class="simap-user-avatar-wrap">
          <img src="../libs/img/avatars/user-default.png" class="rounded-circle border border-2 border-white shadow-sm" alt="Avatar" style="width: 38px; height: 38px; object-fit: cover;">
          <span class="simap-user-status-dot" title="En línea"></span>
        </div>
        <div class="overflow-hidden flex-grow-1">
          <div class="small fw-semibold text-white text-truncate"><?php echo htmlspecialchars($_SESSION['nombre_us'] ?? 'Usuario'); ?></div>
          <div class="mt-1">
            <?php if ($user_type == 1): ?>
              <span class="simap-role-chip simap-role-admin"><i class="bi bi-shield-check"></i>Administrador</span>
            <?php else: ?>
              <span class="simap-role-chip simap-role-secretario"><i class="bi bi-person-badge"></i>Secretario</span>
            <?php endif; ?>
          </div>
        </div>
      </div>
      <nav class="py-2">
        <?php if ($user_type == 2): ?>
          <!-- Menú Exclusivo para Secretario (Móvil) -->
          <div class="nav-header">Catálogo</div>
          <a href="tec_catalogo.php" class="nav-link <?php echo ($current_page == 'tec_catalogo.php') ? 'active' : ''; ?>">
            <i class="bi bi-grid"></i>
            <span>Catálogo de Insumos</span>
          </a>

          <div class="nav-header">Mi Cuenta</div>
          <a href="edit_data_personal.php" class="nav-link <?php echo ($current_page == 'edit_data_personal.php') ? 'active' : ''; ?>">
            <i class="bi bi-person-gear"></i>
            <span>Mis Datos Personales</span>
          </a>
        <?php else: ?>
          <!-- Menú Completo para Administrador y Root (Móvil) -->
          <div class="nav-header">Catálogo</div>
          <a href="adm_catalogo.php" class="nav-link <?php echo ($current_page == 'adm_catalogo.php') ? 'active' : ''; ?>">
            <i class="bi bi-grid"></i>
            <span>Catálogo Principal</span>
          </a>

          <div class="nav-header">Administración</div>
          <a href="edit_data_personal.php" class="nav-link <?php echo ($current_page == 'edit_data_personal.php') ? 'active' : ''; ?>">
            <i class="bi bi-person-gear"></i>
            <span>Datos de Usuario</span>
          </a>
          <a href="adm_usuario.php" class="nav-link <?php echo ($current_page == 'adm_usuario.php') ? 'active' : ''; ?>">
            <i class="bi bi-people"></i>
            <span>Usuarios</span>
          </a>

          <div class="nav-header">Despachos y Entregas</div>
          <a href="adm_retiro.php" class="nav-link <?php echo ($current_page == 'adm_retiro.php') ? 'active' : ''; ?>">
            <i class="bi bi-cart-check"></i>
            <span>Procesar Despacho</span>
          </a>
          <a href="adm_despachos.php" class="nav-link <?php echo in_array($current_page, ['adm_despachos.php', 'adm_editar_despacho.php']) ? 'active' : ''; ?>">
            <i class="bi bi-box-arrow-up-right"></i>
            <span>Historial de Despachos</span>
          </a>

          <div class="nav-header">Depósito de Insumos</div>
          <a href="adm_productos.php" class="nav-link <?php echo ($current_page == 'adm_productos.php') ? 'active' : ''; ?>">
            <i class="bi bi-shield-shaded"></i>
            <span>Gestión de Insumos</span>
          </a>
          <a href="adm_atributos.php" class="nav-link <?php echo ($current_page == 'adm_atributos.php') ? 'active' : ''; ?>">
            <i class="bi bi-tags"></i>
            <span>Gestión de Atributos</span>
          </a>
          <a href="adm_lote.php" class="nav-link <?php echo ($current_page == 'adm_lote.php') ? 'active' : ''; ?>">
            <i class="bi bi-collection"></i>
            <span>Gestión de Lotes</span>
          </a>

          <div class="nav-header">Proveedores y Dotación</div>
          <a href="adm_proveedor.php" class="nav-link <?php echo ($current_page == 'adm_proveedor.php') ? 'active' : ''; ?>">
            <i class="bi bi-truck"></i>
            <span>Directorio de Proveedores</span>
          </a>
        <?php endif; ?>
      </nav>
    </div>
  </div>