  <!-- Bootstrap 5, Bootstrap Icons y Animate.css (Offline) -->
  <link rel="stylesheet" href="../libs/css/bootstrap.min.css">
  <link rel="stylesheet" href="../libs/css/bootstrap-icons.min.css">
  <link rel="stylesheet" href="../libs/css/animate.min.css">
  <!-- Plugins locales (Offline) -->
  <link rel="stylesheet" href="../libs/css/toastr.min.css">
  <link rel="stylesheet" href="../libs/css/select2.min.css">
  <link rel="stylesheet" href="../libs/css/datatables.min.css">
  <!-- Estilos del Sistema SIMAP -->
  <link rel="stylesheet" href="../libs/css/app.css">
  <link rel="stylesheet" href="../libs/css/main.css">
</head>
<body>
<div class="wrapper">

  <!-- Navbar Superior -->
  <header class="simap-navbar sticky-top d-flex align-items-center justify-content-between px-3 px-lg-4">
    <div class="d-flex align-items-center gap-3">
      <!-- Toggle Sidebar Desktop -->
      <button class="btn btn-sm btn-outline-secondary d-none d-lg-inline-flex align-items-center justify-content-center" id="sidebar-toggle-desktop" title="Alternar menú lateral">
        <i class="bi bi-list fs-5"></i>
      </button>

      <!-- Toggle Sidebar Móvil (Offcanvas) -->
      <button class="btn btn-sm btn-outline-secondary d-lg-none d-inline-flex align-items-center justify-content-center" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarOffcanvas" aria-controls="sidebarOffcanvas" title="Abrir menú">
        <i class="bi bi-list fs-5"></i>
      </button>

      <!-- Título de Navbar Móvil -->
      <span class="fw-bold text-primary d-lg-none">SIMAP</span>
    </div>

    <!-- Menú Derecho -->
    <div class="d-flex align-items-center gap-2 gap-md-3">
      <!-- Carrito Dropdown (Preservando IDs para carrito.js) -->
      <div id="cat-carrito" style="display: none;" class="dropdown">
        <button class="btn btn-light position-relative border" type="button" id="navbarDropdown" data-bs-toggle="dropdown" aria-expanded="false" title="Ver carrito">
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
            <a class="btn btn-primary btn-sm" href="#" id="Procesar_pedido">
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
        <a class="btn btn-sm btn-outline-danger d-inline-flex align-items-center gap-1" href="../controller/Logout.php" id="btn-logout" role="button" title="Cerrar sesión">
          <i class="bi bi-box-arrow-right"></i>
          <span class="d-none d-sm-inline">Salir</span>
        </a>
      </div>
    </div>
  </header>

  <!-- Sidebar Desktop (Fijo) -->
  <aside class="simap-sidebar simap-sidebar-desktop d-none d-lg-flex flex-column">
    <!-- Brand Logo -->
    <a href="adm_catalogo.php" class="simap-brand">
      <img src="../libs/img/logo.png" alt="Logo" class="rounded-circle me-2" style="max-height: 38px;">
      <div>
        <div class="fw-bold lh-1 text-white">SIMAP</div>
        <small class="text-white-50" style="font-size: 0.7rem;">C.P.E. La Fría</small>
      </div>
    </a>

    <!-- Info Usuario -->
    <div class="simap-user-panel d-flex align-items-center gap-2">
      <img id="avatar4" src="../libs/img/avatars/user-default.png" class="rounded-circle border border-2 border-white-50" alt="Avatar" style="width: 36px; height: 36px; object-fit: cover;">
      <div class="overflow-hidden">
        <div class="small fw-semibold text-white text-truncate"><?php echo htmlspecialchars($_SESSION['nombre_us'] ?? 'Usuario'); ?></div>
        <div class="text-white-50" style="font-size: 0.72rem;">En línea</div>
      </div>
    </div>

    <!-- Menú de Navegación -->
    <nav class="flex-grow-1 py-2">
      <div class="nav-header">Administración</div>
      <a href="edit_data_personal.php" class="nav-link">
        <i class="bi bi-person-gear"></i>
        <span>Datos de Usuario</span>
      </a>
      <a href="adm_usuario.php" class="nav-link">
        <i class="bi bi-people"></i>
        <span>Usuarios</span>
      </a>

      <div class="nav-header">Retiros y Entregas</div>
      <a href="adm_retiro_ventas.php" class="nav-link">
        <i class="bi bi-box-arrow-up-right"></i>
        <span>Lista de Retiros</span>
      </a>

      <div class="nav-header">Depósito de Insumos</div>
      <a href="adm_productos.php" class="nav-link">
        <i class="bi bi-shield-shaded"></i>
        <span>Gestión de Insumos</span>
      </a>
      <a href="adm_atributos.php" class="nav-link">
        <i class="bi bi-tags"></i>
        <span>Gestión de Atributos</span>
      </a>
      <a href="adm_lote.php" class="nav-link">
        <i class="bi bi-collection"></i>
        <span>Gestión de Lotes</span>
      </a>

      <div class="nav-header">Entregas y Compras</div>
      <a href="adm_proveedor.php" class="nav-link">
        <i class="bi bi-truck"></i>
        <span>Gestión de Proveedor</span>
      </a>
    </nav>
  </aside>

  <!-- Sidebar Móvil (Offcanvas de Bootstrap 5) -->
  <div class="offcanvas offcanvas-start simap-sidebar p-0 d-lg-none" tabindex="-1" id="sidebarOffcanvas" aria-labelledby="sidebarOffcanvasLabel">
    <div class="offcanvas-header simap-brand justify-content-between">
      <div class="d-flex align-items-center">
        <img src="../libs/img/logo.png" alt="Logo" class="rounded-circle me-2" style="max-height: 36px;">
        <span class="fw-bold text-white fs-5" id="sidebarOffcanvasLabel">SIMAP</span>
      </div>
      <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Cerrar"></button>
    </div>
    <div class="offcanvas-body p-0 d-flex flex-column">
      <div class="simap-user-panel d-flex align-items-center gap-2">
        <img src="../libs/img/avatars/user-default.png" class="rounded-circle border border-2 border-white-50" alt="Avatar" style="width: 36px; height: 36px; object-fit: cover;">
        <div class="overflow-hidden">
          <div class="small fw-semibold text-white text-truncate"><?php echo htmlspecialchars($_SESSION['nombre_us'] ?? 'Usuario'); ?></div>
          <div class="text-white-50" style="font-size: 0.72rem;">C.P.E. La Fría</div>
        </div>
      </div>
      <nav class="py-2">
        <div class="nav-header">Administración</div>
        <a href="edit_data_personal.php" class="nav-link">
          <i class="bi bi-person-gear"></i>
          <span>Datos de Usuario</span>
        </a>
        <a href="adm_usuario.php" class="nav-link">
          <i class="bi bi-people"></i>
          <span>Usuarios</span>
        </a>

        <div class="nav-header">Retiros y Entregas</div>
        <a href="adm_retiro_ventas.php" class="nav-link">
          <i class="bi bi-box-arrow-up-right"></i>
          <span>Lista de Retiros</span>
        </a>

        <div class="nav-header">Depósito de Insumos</div>
        <a href="adm_productos.php" class="nav-link">
          <i class="bi bi-shield-shaded"></i>
          <span>Gestión de Insumos</span>
        </a>
        <a href="adm_atributos.php" class="nav-link">
          <i class="bi bi-tags"></i>
          <span>Gestión de Atributos</span>
        </a>
        <a href="adm_lote.php" class="nav-link">
          <i class="bi bi-collection"></i>
          <span>Gestión de Lotes</span>
        </a>

        <div class="nav-header">Entregas y Compras</div>
        <a href="adm_proveedor.php" class="nav-link">
          <i class="bi bi-truck"></i>
          <span>Gestión de Proveedor</span>
        </a>
      </nav>
    </div>
  </div>