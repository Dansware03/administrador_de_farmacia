<?php session_start(); if ($_SESSION['us_tipo']==1||$_SESSION['us_tipo']==3) { include_once 'layouts/header.php'; ?>
<title><?php echo $_SESSION['nombre_us']; ?> | Atributos</title>
<?php include_once 'layouts/nav.php'; ?>
<!-- Modal Laboratorio-->
<div class="modal fade" id="crear-laboratorio" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
        <div class="card card-primary">
            <div class="card-header"><h3 class="card-title">Crear Laboratorio</h3><button data-dismiss="modal" aria-label="close" class="close"><span aria-hidden="true">&times;</span></button></div>
        </div>
        <div class="card-body">
            <div class="alert alert-primary text-center" id="add-lab" style="display: none;">
              <span><i class="fas fa-check m-1"></i>Laboratorio Agregado Con Exito!!</span>
            </div>
            <div class="alert alert-danger text-center" id="noadd-lab" style="display: none;">
              <span><i class="fas fa-times m-1"></i>Error !!</span>
            </div>
            <form id="form-crear-laboratorio">
                <div class="from-group">
                    <label for="nombre-laboratorio">Nombre</label>
                    <input id="nombre-laboratorio" type="text" class="form-control" placeholder="Ingrese Nombre de Laboratorio" required>
                </div>
        </div>
                <div class="card-footer">
                    <button type="submit" class="btn bg-gradient-primary float-right">Crear Laboratorio</button>
                </div>
            </form>
    </div>
  </div>
</div>
<!-- Modal Cambiar Foto de Laboratorio-->
<div class="modal fade" id="cambiar-foto-lab" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Cambiar Imagen</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      <div class="alert alert-primary text-center" id="edit" style="display: none;">
            <span><i class="fas fa-check m-1"></i>Avatar Cambiada Con Exito</span>
        </div>
        <div class="alert alert-danger text-center" id="noedit" style="display: none;">
            <span><i class="fas fa-times m-1"></i>Error al Subir Imagen!!</span>
        </div>
        <div class="text-center">
            <img id="lab-actual" src="../libs/img/laboratory/LabDefault.png" class="profile-user-img img-fluid img-circle">
        </div>
          <div class="text-center">
            <b id="nombre_logo"></b>
          </div>
        <form id="form-cambiar-foto-laboratorio" enctype="multipart/form-data">
            <div class="input-group mb-3 ml-5 mt-2">
                    <input type="file" class="input-group" name="foto">
                    <input type="hidden" name="id_logo_lab" id="id_logo_lab">
                    <input type="hidden" name="funcion" value="cambiar_logo">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cerrar</button>
                <button type="submit" class="btn bg-gradient-primary">Guardar</button>
            </div>
        </form>
      </div>
    </div>
  </div>
</div>
<!-- Modal Tipo-->
<div class="modal fade" id="crear-tipo" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
        <div class="card card-primary">
            <div class="card-header"><h3 class="card-title">Crear Tipo</h3><button data-dismiss="modal" aria-label="close" class="close"><span aria-hidden="true">&times;</span></button></div>
        </div>
        <div class="card-body">
            <div class="alert alert-primary text-center" id="add-type" style="display: none;">
              <span><i class="fas fa-check m-1"></i>Tipo Agregado Con Exito!!</span>
            </div>
            <div class="alert alert-danger text-center" id="noadd-type" style="display: none;">
              <span><i class="fas fa-times m-1"></i>Error !!</span>
            </div>
            <form id="form-crear-tipo">
                <div class="from-group">
                    <label for="nombre-tipo">Nombre</label>
                    <input id="nombre-tipo" type="text" class="form-control" placeholder="Ingrese Nombre de tipo" required>
                </div>
        </div>
                <div class="card-footer">
                    <button type="submit" class="btn bg-gradient-primary float-right">Crear tipo</button>
                </div>
            </form>
    </div>
  </div>
</div>
<!-- Modal Presentacion-->
<div class="modal fade" id="crear-presentacion" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
        <div class="card card-primary">
            <div class="card-header"><h3 class="card-title">Crear Presentación</h3><button data-dismiss="modal" aria-label="close" class="close"><span aria-hidden="true">&times;</span></button></div>
        </div>
        <div class="card-body">
            <div class="alert alert-primary text-center" id="add-presentacion" style="display: none;">
              <span><i class="fas fa-check m-1"></i>Presentación Agregado Con Exito!!</span>
            </div>
            <div class="alert alert-danger text-center" id="noadd-presentacion" style="display: none;">
              <span><i class="fas fa-times m-1"></i>Error !!</span>
            </div>
            <form id="form-crear-presentacion">
                <div class="from-group">
                    <label for="nombre-presentacion">Nombre</label>
                    <input id="nombre-presentacion" type="text" class="form-control" placeholder="Ingrese Nombre de Presentación" required>
                </div>
        </div>
                <div class="card-footer">
                    <button type="submit" class="btn bg-gradient-primary float-right">Crear Presentación</button>
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
            <h1>Gestion de Atributos</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="adm_catalogo.php">INICIO</a></li>
              <li class="breadcrumb-item active">Gestion de Atributos</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>
    <!-- Main content -->
    <section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <ul class="nav nav-pills">
                            <li class="nav-item"><a href="#laboratory" class="nav-link active" data-toggle="tab"><i class="fa-solid fa-flask mr-2"></i>Laboratorio</a></li>
                            <li class="nav-item"><a href="#tipo" class="nav-link" data-toggle="tab"><i class="fa-solid fa-file-medical mr-2"></i>Tipo</a></li>
                            <li class="nav-item"><a href="#presentacion" class="nav-link" data-toggle="tab"><i class="fa-solid fa-prescription-bottle-medical mr-2"></i>Presentación</a></li>
                        </ul>
                    </div>
                    <div class="card-bodyp-0">
                        <div class="tab-content">
                            <div class="tab-pane active" id="laboratory">
                                <div class="card card-primary">
                                    <div class="card-header">
                                        <div class="card-title">Buscar Laboratorio <button type="button" data-toggle="modal" data-target="#crear-laboratorio" class="btn bg-gradient-success btn-sm m-2"><i class="fa-solid fa-flask mr-2"></i>Crear Laboratorio</button></div>
                                        <div class="input-group">
                                            <input id="buscar-laboratory" type="text" class="form-control float left" placeholder="Ingrese Nombre">
                                            <div class="input-group-append">
                                                <button class="btn btn-default"><i class="fas fa-search"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body p-0 table-responsive">
                                      <table class="table table-hover text-nowrap">
                                        <thead class="table-primary">
                                          <tr>
                                            <th>Laboratorio</th>
                                            <th>Logo</th>
                                            <th>Acciones</th>
                                          </tr>
                                        </thead>
                                        <tbody class="table-active" id="laboratorios">
                                        </tbody>
                                      </table>
                                    </div>
                                    <div class="card-footer"></div>
                                </div>
                            </div>
                            <div class="tab-pane" id="tipo">
                                <div class="card card-primary">
                                    <div class="card-header">
                                        <div class="card-title">Buscar Tipo<button type="button" data-toggle="modal" data-target="#crear-tipo" class="btn bg-gradient-success btn-sm m-2"><i class="fa-solid fa-file-medical mr-2"></i>Crear Tipo</button></div>
                                        <div class="input-group">
                                            <input id="buscar-tipo" type="text" class="form-control float left" placeholder="Ingrese Nombre">
                                            <div class="input-group-append">
                                                <button class="btn btn-default"><i class="fas fa-search"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body p-0 table-responsive">
                                      <table class="table table-hover text-nowrap">
                                        <thead class="table-primary">
                                          <tr>
                                            <th>Tipos</th>
                                            <th>Acciones</th>
                                          </tr>
                                        </thead>
                                        <tbody class="table-active" id="tipos">
                                        </tbody>
                                      </table>
                                    </div>
                                    <div class="card-footer"></div>
                                </div>
                            </div>
                            <div class="tab-pane" id="presentacion">
                                <div class="card card-primary">
                                    <div class="card-header">
                                        <div class="card-title">Buscar Presentación<button type="button" data-toggle="modal" data-target="#crear-presentacion" class="btn bg-gradient-success btn-sm m-2"><i class="fa-solid fa-prescription-bottle-medical mr-2"></i>Crear Presentación</button></div>
                                        <div class="input-group">
                                            <input id="buscar-presentacion" type="text" class="form-control float left" placeholder="Ingrese Nombre">
                                            <div class="input-group-append">
                                                <button class="btn btn-default"><i class="fas fa-search"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body p-0 table-responsive">
                                      <table class="table table-hover text-nowrap">
                                        <thead class="table-primary">
                                          <tr>
                                            <th>Presentaciones</th>
                                            <th>Acciones</th>
                                          </tr>
                                        </thead>
                                        <tbody class="table-active" id="presentaciones">
                                        </tbody>
                                      </table>
                                    </div>
                                    <div class="card-footer"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                    </div>
                </div>
            </div>
        </div>
    </div>
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
<?php include_once 'layouts/footer.php'; } else { header('Location: ../../index.php'); } ?>
<script src="../libs/js/laboratory.js"></script>
<script src="../libs/js/type.js"></script>
<script src="../libs/js/presentaciones.js"></script>