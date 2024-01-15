<?php session_start(); if ($_SESSION['us_tipo']==1||$_SESSION['us_tipo']==3) { include_once 'layouts/header.php'; ?>
<title><?php echo $_SESSION['nombre_us']; ?> | Farmacia</title>
<?php include_once 'layouts/nav.php'; ?>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Sistema de Farmacia</h1>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>
<!-- Main Lotes -->
<section>
    <div class="container-fluid">
        <div class="card card-warning">
            <div class="card-header">
                <h3 class="card-title">Lotes en Riesgo</h3>
                <button class="btn btn-sm btn-outline-secondary float-right" id="toggleLotesBtn">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
            <div class="card-body p-0 table-responsive" id="lotesSection">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th class="col-1 col-md-1">Codigo.</th>
                            <th class="col-3 col-md-3">Producto</th>
                            <th class="col-1 col-md-1">Stock</th>
                            <th class="col-2 col-md-2">Laboratorio</th>
                            <th class="col-2 col-md-2">Presentacion</th>
                            <th class="col-2 col-md-2">Proveedor</th>
                            <th class="col-1 col-md-1">Mes</th>
                            <th class="col-1 col-md-1">Dia</th>
                        </tr>
                    </thead>
                    <tbody class="table-active" id="lotes"></tbody>
                </table>
            </div>
            <div class="card-footer"></div>
        </div>
    </div>
</section>
<!-- Main Productos -->
        <section>
          <div class="container-fluid">
            <div class="card card-primary">
              <div class="card-header">
                  <h3 class="card-title">Buscar Productos</h3>
                    <div class="input-group">
                        <input type="text" id="buscar_producto" class="form-control float-left" placeholder="Ingrese Nombre de producto">
                        <div class="input-group-append"><button class="btn btn-default"><i class="fas fa-search"></i></button></div>
                    </div>
              </div>
                <div class="card-body"></div>
                <div id="productos" class="row d-flex align-items-stretch">
                </div>
                <div class="card-footer"></div>
            </div>
          </div>
        </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
<?php include_once 'layouts/footer.php'; } else { header('Location: ../../index.php'); } ?>
<script src="../libs/js/catalogo.js"></script>
<script src="../libs/js/carrito.js"></script>
<script>
    $(document).ready(function () {
        $("#toggleLotesBtn").click(function () {
            $("#lotesSection").toggle();
            $(this).find("i").toggleClass("bi-dash bi-plus");
        });
    });
</script>
