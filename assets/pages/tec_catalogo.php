<?php session_start(); if ($_SESSION['us_tipo']==2) { include_once 'layouts/header.php'; ?>
<title><?php echo htmlspecialchars($_SESSION['nombre_us']); ?> | Catálogo Técnico</title>
<?php include_once 'layouts/nav.php'; ?>

  <!-- Content Wrapper -->
  <div class="content-wrapper">
    <!-- Content Header -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2 mb-2">
          <div>
            <h1 class="h3 fw-bold mb-1 text-primary">
              <i class="bi bi-shield-check me-2"></i>Catálogo de Insumos y Materiales
            </h1>
            <p class="text-muted small mb-0">Área técnica y asistencial - Consulta de disponibilidad y solicitud de materiales</p>
          </div>
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
              <li class="breadcrumb-item"><a href="tec_catalogo.php" class="text-decoration-none">Inicio</a></li>
              <li class="breadcrumb-item active" aria-current="page">Catálogo Técnico</li>
            </ol>
          </nav>
        </div>
      </div>
    </section>

    <!-- Main Buscador y Catálogo -->
    <section>
      <div class="container-fluid">
        <!-- Banner Informativo Técnico -->
        <div class="alert alert-primary bg-primary bg-opacity-10 border-0 shadow-sm d-flex align-items-center gap-3 p-3 mb-4 rounded-3" role="alert">
          <i class="bi bi-info-circle-fill text-primary fs-3 flex-shrink-0"></i>
          <div class="small">
            <strong class="d-block text-primary fs-6">Perfil Asistencial / Técnico:</strong>
            Puede consultar el stock de materiales de protección y limpieza, y agregarlos a su solicitud de entrega mediante el botón en cada insumo.
          </div>
        </div>

        <!-- Buscador -->
        <div class="card border-0 shadow-sm mb-4">
          <div class="card-body p-3">
            <div class="input-group input-group-lg">
              <span class="input-group-text bg-light border-end-0 text-muted">
                <i class="bi bi-search"></i>
              </span>
              <input type="text" id="buscar_producto" class="form-control border-start-0 bg-light" placeholder="Escriba el nombre del insumo o material a consultar..." aria-label="Buscar insumos o materiales">
            </div>
          </div>
        </div>

        <!-- Grid de Productos -->
        <div id="productos" class="row g-3 g-md-4 align-items-stretch"></div>
      </div>
    </section>
  </div>
  <!-- /.content-wrapper -->

<?php include_once 'layouts/footer.php'; } else { header('Location: ../../index.php'); } ?>
<script src="../libs/js/catalogo.js"></script>
<script src="../libs/js/carrito.js"></script>