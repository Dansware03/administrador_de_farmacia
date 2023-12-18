<!-- Tell the browser to be responsive to screen width -->
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- Incluye Toastr CSS -->
<link rel="stylesheet" href="../libs/css/toastr.css">
<!-- Carrito  -->
<link rel="stylesheet" href="../libs/css/main.css">
<!-- SELECT2  -->
<link rel="stylesheet" href="../libs/css/select2.css">
<!-- Font Awesome -->
<link rel="stylesheet" href="../libs/css/css/all.min.css">
<!-- Ionicons -->
<link rel="stylesheet" href="../libs/css/fonts/ionicons.min.css">
<!-- overlayScrollbars -->
<link rel="stylesheet" href="../libs/css/adminlte.min.css">
<!-- Google Font: Source Sans Pro -->
<link rel="stylesheet" type="text/css" href="../libs/css/source-sans-release/source-sans-3.css">
<!-- SweetAlert2  -->
<link rel="stylesheet" href="../libs/css/sweetalert2.css">
<!-- PEDIDOS  -->
<link rel="stylesheet" href="../libs/css/pedido.css">
</head>
<body class="hold-transition sidebar-mini">
<!-- Site wrapper -->
<div class="wrapper">
<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
  <!-- Left navbar links -->
  <ul class="navbar-nav">
    <li class="nav-item">
      <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
    </li>
    <li id="cat-carrito" style="display: none;" class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="img_carrito fas fa-shopping-bag"></i></a>
        <span class="contador badge badge-primary" id="contador"></span>
        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
          <table class="carro table table-responsive text-nowrap table-bordered p-0">
            <thead class="table-success">
              <tr>
                <th>Codigo: </th>
                <th>Nombre: </th>
                <th>Concentración: </th>
                <th>Adicional: </th>
                <th>Precio: </th>
                <th>Eliminar</th>
              </tr>
            </thead>
            <tbody id="lista_carrito">
            </tbody>
          </table>.
          <a class="btn btn-danger btn-block" href="#" id="Procesar_pedido"><i class="fas fa-check"></i> Procesar Compra</a>
          <a id="vaciar_carrito" class="btn btn-success btn-block" href="#"><i class="fas fa-trash"></i> Vaciar Carrito</a>
        </div>
      </li>
  </ul>
  <!-- Right navbar links -->
  <ul class="navbar-nav ml-auto">
    <a class="btn btn-danger" href="../controller/Logout.php" role="button">Cerrar Sesión</a>
</nav>
<!-- /.navbar -->
<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
  <!-- Brand Logo -->
  <a href="adm_catalogo.php" class="brand-link">
    <img src="../libs/img/logo.png"
         alt="Farmacia Logo"
         class="brand-image img-circle elevation-3"
         style="opacity: .8">
    <span class="brand-text font-weight-light">Farmacia</span>
  </a>
  <!-- Sidebar -->
  <div class="sidebar">
    <!-- Sidebar user (optional) -->
    <div class="user-panel mt-3 pb-3 mb-3 d-flex">
      <div class="image">
        <img id="avatar4" src="../libs/img/avatars/user-default.png" class="img-circle elevation-2" alt="User Image">
      </div>
      <div class="info">
        <a href="#" class="d-block"><?php echo $_SESSION['nombre_us']; ?></a>
      </div>
    </div>
    <!-- Sidebar Menu -->
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <!-- Add icons to the links using the .nav-icon class
             with font-awesome or any other icon font library -->
        <li class="nav-header">Administración</li>
        <li class="nav-item">
          <a href="edit_data_personal.php" class="nav-link">
            <i class="nav-icon fas fa-user-cog"></i>
            <p>
              Datos de Usuario
            </p>
          </a>
        </li>
        <li class="nav-item">
          <a href="adm_usuario.php" class="nav-link">
            <i class="nav-icon fas fa-users"></i>
            <p>
              Usuarios
            </p>
          </a>
        </li>
        <li class="nav-header">Retiros/Ventas</li>
        <li class="nav-item">
          <a href="#" class="update nav-link">
            <i class="nav-icon fas fa-pills"></i>
            <p>
              Lista de Retiros/Ventas
            </p>
          </a>
        </li>
        <li class="nav-header">Deposito de Farmacia</li>
        <li class="nav-item">
          <a href="adm_productos.php" class="nav-link">
            <i class="nav-icon fas fa-pills"></i>
            <p>
              Gestión de Productos
            </p>
          </a>
        </li>
        <li class="nav-item">
          <a href="adm_atributos.php" class="nav-link">
            <i class="nav-icon fas fa-vials"></i>
            <p>
              Gestión de Atributos
            </p>
          </a>
        </li>
        <li class="nav-item">
          <a href="adm_lote.php" class="nav-link">
            <i class="nav-icon fas fa-cubes"></i>
            <p>
              Gestión de lotes
            </p>
          </a>
        </li>
        <li class="nav-header">Entregas/Compras</li>
        <li class="nav-item">
          <a href="adm_proveedor.php" class="nav-link">
            <i class="nav-icon fas fa-truck"></i>
            <p>
              Gestión de Proveedor
            </p>
          </a>
        </li>
      </ul>
    </nav>
    <!-- /.sidebar-menu -->
  </div>
  <!-- /.sidebar -->
</aside>