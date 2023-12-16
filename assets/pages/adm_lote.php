<?php session_start(); if ($_SESSION['us_tipo']==3) { include_once 'layouts/header.php'; ?>
<title><?php echo $_SESSION['nombre_us']; ?> | Gestión de Lotes</title>
<?php include_once 'layouts/nav.php'; ?>
<!-- Modal Lote-->
<div class="modal fade" id="editarlote" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
        <div class="card card-primary">
            <div class="card-header"><h3 class="card-title">Lote</h3><button data-dismiss="modal" aria-label="close" class="close"><span aria-hidden="true">&times;</span></button></div>
        </div>
        <div class="card-body">
    <form id="form-editar-lote">
        <div class="form-group">
            <label for="codigo_lote">Codigo de Lote: </label>
            <label id="codigo_lote">Codigo: </label>
        </div>
        <div class="form-group">
            <label for="stock">Stock</label>
            <input id="stock" type="number" class="form-control" placeholder="Ingrese Stock" required>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn bg-gradient-primary float-right">Editar Lote</button>
        </div>
        <input type="hidden" id="id_lote_prod">
    </form>
</div>
    </div>
  </div>
</div>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Gestión de Lote</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="adm_catalogo.php">INICIO</a></li>
              <li class="breadcrumb-item active">Gestión de Lotes</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>
    <!-- Main content -->
    <section>
        <div class="container-fluid">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Buscar Lotes</h3>
                    <div class="input-group">
                        <input type="text" id="buscar_lotes" class="form-control float-left" placeholder="Ingrese Nombre de producto">
                        <div class="input-group-append"><button class="btn btn-default"><i class="fas fa-search"></i></button></div>
                    </div>
                </div>
                <div class="card-body"></div>
                <div id="lotes" class="row d-flex align-items-stretch">

                </div>

                <div class="card-footer"></div>
            </div>
        </div>
    </section>
  </div>
  <!-- /.content-wrapper -->
<?php include_once 'layouts/footer.php'; } else { header('Location: ../../index.php'); } ?>
<script src="../libs/js/lote.js"></script>