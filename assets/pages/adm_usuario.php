<?php session_start(); if ($_SESSION['us_tipo']==1||$_SESSION['us_tipo']==3) { include_once 'layouts/header.php'; ?>
<title><?php echo $_SESSION['nombre_us']; ?> | Usuarios</title>
<?php include_once 'layouts/nav.php'; ?>
<!-- Modal Rol-->
<div class="modal fade" id="check" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel"><i class="fa-solid fa-key m-1"></i>Permsos Acender/Descender</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="text-center">
            <img id="avatar3" src="../libs/img/avatars/user-default.png" class="profile-user-img img-fluid img-circle">
        </div>
        <div class="text-center"><b><?php echo $_SESSION['nombre_us']?></b></div><span>Escribe Tus Credenciales</span>
    <form id="form-check">
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-unlock-alt"></i></span>
                </div>
                    <input class="form-control" type="password" id="oldpass" placeholder="Ingrese Contraseña Actual">
                    <input type="hidden" id="id_user_rol"> <input type="hidden" id="funcion">
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
<!-- Modal Usuario-->
<div class="modal fade" id="newuser" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
        <div class="card card-primary">
            <div class="card-header"><h3 class="card-title">Crear Usuario</h3><button data-dismiss="modal" aria-label="close" class="close"><span aria-hidden="true">&times;</span></button></div>
        </div>
        <div class="card-body">
            <form id="form-crear">
                <div class="from-group">
                    <label for="nombre">Nombre</label>
                    <input id="nombre" type="text" class="form-control" placeholder="Ingrese su Nombre" required>
                </div>
                <div class="from-group">
                    <label for="apellido">Apellidos</label>
                    <input id="apellido" type="text" class="form-control" placeholder="Ingrese su Apellido" required>
                </div>
                <div class="from-group">
                    <label for="edad">Fecha de Nacimiento</label>
                    <input id="edad" type="date" class="form-control" placeholder="Ingrese su Fecha de Nacimiento" required>
                </div>
                <div class="from-group">
                    <label for="ci">Cedula de Identidad</label>
                    <input id="ci" type="text" class="form-control" placeholder="Ingrese su Cedula de Identidad" required>
                </div>
                <div class="form-group">
                  <label for="genero">Género</label>
                  <select id="genero" class="form-control" required>
                    <option value="hombre">Hombre</option>
                    <option value="mujer">Mujer</option>
                    <option value="otro">Otro</option>
                  </select>
                </div>
                <div class="from-group">
                    <label for="pass">Contraseña</label>
                    <input id="pass" type="password" class="form-control" placeholder="Cree una Contraseña" required>
                </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn bg-gradient-primary float-right">Crear Usuario</button>
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
            <h1>Gestor de Usuarios<button  id="button_crear" type="button" data-toggle="modal" data-target="#newuser" class="btn bg-gradient-primary ml-2">Crear Usuario</button></h1>
            <input type="hidden" id="tipo_usuario" value="<?php echo $_SESSION['us_tipo']?>">
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="adm_catalogo.php">INICIO</a></li>
              <li class="breadcrumb-item active">Gestor de Usuarios</li>
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
                    <h3 class="card-title">Buscar Usuarios</h3>
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
<script src="../libs/js/gestion_user.js"></script>