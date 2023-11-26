<?php session_start(); if ($_SESSION['us_tipo']==1||$_SESSION['us_tipo']==3) { include_once 'layouts/header.php'; ?>
<title><?php echo $_SESSION['nombre_us']; ?> | Gestión de Proveedor</title>
<?php include_once 'layouts/nav.php'; ?>
<!-- Modal Proveedor-->
<div class="modal fade" id="newproveedor" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
        <div class="card card-primary">
            <div class="card-header"><h3 class="card-title">Crear Proveedor</h3><button data-dismiss="modal" aria-label="close" class="close"><span aria-hidden="true">&times;</span></button></div>
        </div>
        <div class="card-body">
            <form id="form-crear-proveedor">
                <div class="from-group">
                    <label for="nombre">Nombre</label>
                    <input id="nombre" type="text" class="form-control" placeholder="Ingrese su Nombre" required>
                </div>
                <div class="from-group">
                    <label for="telefono">Telefono</label>
                    <input id="telefono" type="text" class="form-control" placeholder="Ingrese su Telefono" required>
                </div>
                <div class="from-group">
                    <label for="correo">Correo</label>
                    <input id="correo" type="email" class="form-control" placeholder="Ingrese su Correo">
                </div>
                <div class="from-group">
                    <label for="direccion">Direccion</label>
                    <input id="direccion" type="text" class="form-control" placeholder="Ingrese La direccion Del Proveedor">
                </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn bg-gradient-primary float-right">Crear Proveedor</button>
                </div>
            </form>
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
            <h1>Gestión de Proveedor<button  id="button_crear" type="button" data-toggle="modal" data-target="#newproveedor" class="btn bg-gradient-primary ml-2">Crear Proveedor</button></h1>
            <input type="hidden" id="tipo_usuario" value="<?php echo $_SESSION['us_tipo']?>">
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="adm_catalogo.php">INICIO</a></li>
              <li class="breadcrumb-item active">Gestión de Proveedor</li>
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
                    <h3 class="card-title">Buscar Proveedor</h3>
                    <div class="input-group">
                        <input type="text" id="buscar" class="form-control float-left" placeholder="Ingrese Nombre de Usuario">
                        <div class="input-group-append"><button class="btn btn-default"><i class="fas fa-search"></i></button></div>
                    </div>
                </div>
                <div class="card-body"></div>
                <div id="usuarios" class="row d-flex align-items-stretch">

                </div>

                <div class="card-footer"></div>
            </div>
        </div>
    </section>
  </div>
  <!-- /.content-wrapper -->
<?php include_once 'layouts/footer.php'; } else { header('Location: ../../index.php'); } ?>
<script src="../libs/js/proveedor.js"></script>