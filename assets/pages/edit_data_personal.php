<?php session_start(); if ($_SESSION['us_tipo']==1||$_SESSION['us_tipo']==3) { include_once 'layouts/header.php'; ?>
<title><?php echo $_SESSION['nombre_us']; ?> | Editar Datos</title>
<?php include_once 'layouts/nav.php'; ?>
<!-- Modal Contraseña-->
<div class="modal fade" id="cambiarcontrasena" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel"><i class="fa-solid fa-key m-1"></i>Cambiar Contraseña</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="text-center">
            <img id="avatar3" src="../libs/img/avatars/user-default.png" class="profile-user-img img-fluid img-circle">
        </div>
        <div class="text-center"><b><?php echo $_SESSION['nombre_us']?></b></div>
    <form id="form-pass">
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-unlock-alt"></i></span>
                </div>
                    <input class="form-control" type="password" id="oldpass" placeholder="Ingrese Contraseña Actual">
            </div>
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                </div>
                <input class="form-control" type="password" id="newpass" placeholder="Ingrese Contraseña Nueva">
            </div>
        </div>
        <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn bg-gradient-primary">Cambiar</button>
        </div>
    </form>
    </div>
  </div>
</div>
<!-- Modal Foto-->
<div class="modal fade" id="cambiofoto" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Cambiar Imagen</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="text-center">
            <img id="avatar1" src="../libs/img/avatars/user-default.png" class="profile-user-img img-fluid img-circle">
            </div>
        <form id="form-foto" enctype="multipart/form-data">
            <div class="input-group mb-3 ml-5 mt-2">
                    <input type="file" class="form-control-file" name="foto">
                    <input type="hidden" name="funcion" value="cambiar_foto">
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
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Datos Personales</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="adm_catalogo.php">INICIO</a></li>
              <li class="breadcrumb-item active">Editar Datos</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>
    <!-- Main content -->
    <section>
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-3">
                        <div class="card card-primary card-outline">
                            <div class="card-body box-profile">
                                <div class="text-center">
                                    <img id="avatar2" src="../libs/img/avatars/user-default.png" class="profile-user-img img-fluid img-circle">
                                </div>
                                <div class="text-center mt-1">
                                    <button type="button" data-toggle="modal" data-target="#cambiofoto" class="btn bg-gradient-primary btn-sm"><i class="fa-solid fa-image m-1"></i>Cambiar Avatar</button>
                                </div>
                                <input id="id_usuario"type="hidden" value="<?php echo $_SESSION['usuario']?>">
                            <h3 id="nombre_us" class="profile-username text-center text-primary"></h3>
                                <p id="apellidos_us" class="text-muted text-center"></p>
                                <ul class="list-group list-group-unbordered md-3">
                                    <li class="list-group-item">
                                        <b style="color:#007bff">Edad</b><a id="edad" class="float-right"></a>
                                    </li>
                                    <li class="list-group-item">
                                        <b style="color:#007bff">C.i</b><a id="ci_us" class="float-right"></a>
                                    </li>
                                    <li class="list-group-item">
                                        <b style="color:#007bff">Tipo de Usuario</b><span id="tipo_us" class="float-right"></span>
                                    </li>
                                    <!-- Button trigger modal -->
                                    <button data-toggle="modal" data-target="#cambiarcontrasena" type="button" class="btn btn-block btn-outline-warning btn-sm" ><i class="fa-solid fa-key m-1"></i>Cambiar Contraseña</button>
                                </ul>
                            </div>
                        </div>
                        <div class="card card-primary collapsed-card">
                            <div class="card-header">
                                <h3 class="card-title">Información</h3>
                                <button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip" title="Collapse">
                                    <i class="fas fa-plus"></i></button>
                            </div>
                            <div class="card-body">
                                <strong style="color:#007bff">
                                    <i class="fas fa-phone mr-1"></i>Telefono
                                </strong>
                                <p id="telefono_us" class="text-muted">1234567890</p>
                                <strong style="color:#007bff">
                                    <i class="fas fa-at mr-1"></i>Correo
                                </strong>
                                <p id="correo_us" class="text-muted">hola@correo.com</p>
                                <strong style="color:#007bff">
                                    <i class="fas fa-smile-wink mr-1"></i>Genero
                                </strong>
                                <p id="genero_us" class="text-muted"></p>
                                <strong style="color:#007bff">
                                    <i class="fas fa-pencil-alt mr-1"></i>Informqción Adicional
                                </strong>
                                <p id="info_us" class="text-muted">Información</p>
                                <button class="edit btn btn-block bg-gradient-danger">Editar</button>
                            </div>
                            <div class="card-footer">
                                <p class="text-muted">Haz Click Si Quieres Editar</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="card card-primary ">
                            <div class="card-header">
                                <h3 class="card-title">Editar Información</h3>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-primary text-center" id="editado" style="display: none;">
                                    <span><i class="fas fa-check m-1"></i>Editado Con Exito</span>
                                </div>
                                <div class="alert alert-danger text-center" id="noeditado" style="display: none;">
                                    <span><i class="fas fa-times m-1"></i>Ha Ocurrido un Error!!</span>
                                </div>
                                <form id="form-usuario" class="form-horizontal">
                                    <div class="form-group row">
                                        <label for="telefono" class="col-sm-2 col-form-label"><i class="fas fa-phone mr-1"></i>Telefono</label>
                                        <div class="col-sm-10">
                                            <input type="tel" id="telefono" class="form-control">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="correo" class="col-sm-2 col-form-label"><i class="fas fa-at mr-1"></i>Correo</label>
                                        <div class="col-sm-10">
                                            <input type="email" id="email" class="form-control">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="genero" class="col-sm-2 col-form-label"><i class="fas fa-smile-wink mr-1"></i>Género</label>
                                        <div class="col-sm-10">
                                            <select id="genero" class="form-control">
                                                <option value="hombre">Hombre</option>
                                                <option value="mujer">Mujer</option>
                                                <option value="otro">Otro</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="info-adicional" class="col-sm-2 col-form-label"><i class="fas fa-pencil-alt mr-1"></i>Información Adicional</label>
                                        <div class="col-sm-10">
                                            <textarea class="form-control" id="info-adicional" cols="30" rows="5"></textarea>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="offset-sm-2 col-sm-10 float-right">
                                            <button class="btn btn-block btn-outline-primary">Guardar</button>
                                            <p class="text-muted text-center">Verificar La Información Antes de Guardar</p>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
  </div>
  <!-- /.content-wrapper -->
<?php include_once 'layouts/footer.php'; } else { header('Location: ../../index.php'); } ?>
<script src="../libs/js/usuario.js"></script>