<?php session_start(); if ($_SESSION['us_tipo']==1||$_SESSION['us_tipo']==3) { include_once 'layouts/header.php'; ?>
<title><?php echo $_SESSION['nombre_us']; ?> | Gestión de Productos</title>
<?php include_once 'layouts/nav.php'; ?>
<!-- Modal producto-->
<div class="modal fade" id="crearproducto" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
        <div class="card card-primary">
            <div class="card-header"><h3 class="card-title">Crear producto</h3><button data-dismiss="modal" aria-label="close" class="close"><span aria-hidden="true">&times;</span></button></div>
        </div>
        <div class="card-body">
    <form id="form-crear-producto">
        <div class="form-group">
            <label for="nombre-producto">Nombre del Producto</label>
            <input id="nombre-producto" type="text" class="form-control" placeholder="Ingrese el nombre del producto" required>
        </div>
        <div class="form-group">
            <label for="concentracion">Concentración</label>
            <div class="input-group">
                <input id="concentracion" type="text" class="form-control" placeholder="Ingresa la concentración" required>
                <select class="form-control" id="unidad" name="unidad">
                    <option value="mg/ml">mg/ml (miligramos por mililitro)</option>
                    <option value="mcg/ml">mcg/ml (microgramos por mililitro)</option>
                    <option value="g/l">g/l (gramos por litro)</option>
                    <option value="%">%(porcentaje)</option>
                    <option value="M">M (Molaridad)</option>
                    <option value="N">N (Normalidad)</option>
                    <option value="Osmol">Osmol (Osmolaridad)</option>
                    <option value="UI/ml">UI/ml (Unidades Internacionales por mililitro)</option>
                    <option value="ppm">ppm (Partes por millón)</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="adicional">Compuestos o Información Adicional</label>
            <textarea id="adicional" class="form-control" rows="3" placeholder="Ingrese Información del Producto"></textarea>
        </div>
        <div class="form-group">
            <label for="precio">Precio</label>
            <div class="input-group">
                <input id="precio" type="number" class="form-control" placeholder="Ingrese Su Precio" required step="0.01">
                <div class="input-group-append">
                    <div class="input-group-text">
                        <input type="checkbox" id="noVenta" name="noVenta"> No a la venta
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label for="laboratorio">Laboratorio</label>
            <select id="laboratorio" class="form-control select2" style="width: 100%;"></select>
        </div>
        <div class="form-group">
            <label for="tipo">Tipo</label>
            <select name="tipo" id="tipo" class="form-control select2" style="width: 100%"></select>
        </div>
        <div class="form-group">
            <label for="presentacion">Presentación</label>
            <select name="presentacion" id="presentacion" class="form-control select2" style="width: 100%"></select>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn bg-gradient-primary float-right">Crear producto</button>
        </div>
        <input type="hidden" id="id_edit_prod">
    </form>
</div>
    </div>
  </div>
</div>
<!-- Modal Lote-->
<div class="modal fade" id="crearlote" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
        <div class="card card-primary">
            <div class="card-header"><h3 class="card-title">Lote</h3><button data-dismiss="modal" aria-label="close" class="close"><span aria-hidden="true">&times;</span></button></div>
        </div>
        <div class="card-body">
    <form id="form-crear-lote">
        <div class="form-group">
            <label for="nombre_producto_lote">Producto: </label>
            <label id="nombre_producto_lote">Nombre de Producto: </label>
        </div>
        <div class="form-group">
            <label for="proveedor">Proveedor</label>
            <select name="proveedor" id="proveedor" class="form-control select2" style="width: 100%" required></select>
        </div>
        <div class="form-group">
            <label for="cod_lote">Codigo de Lote</label>
            <input id="cod_lote" type="text" class="form-control" placeholder="Ingrese Lote" required>
        </div>
        <div class="form-group">
            <label for="stock">Stock</label>
            <input id="stock" type="number" class="form-control" placeholder="Ingrese Stock" required>
        </div>
        <div class="form-group">
            <label for="Vencimiento">Vencimiento</label>
            <input id="vencimiento" type="date" class="form-control" placeholder="Seleccione Vencimiento"></input>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn bg-gradient-primary float-right">Crear Lote</button>
        </div>
        <input type="hidden" id="id_lote_prod">
    </form>
</div>
    </div>
  </div>
</div>
<!-- Modal Foto-->
<div class="modal fade" id="cambiarlogo" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
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
            <img id="logoactual1" src="../libs/img/product/ProductDefault.png" class="profile-user-img img-fluid img-circle">
            </div>
            <div class="text-center">
            <b id="nombre_logo"></b>
            </div>
            <form id="form-logo" enctype="multipart/form-data">
                <div class="input-group mb-3 ml-5 mt-2">
                    <input type="file" id="foto" name="foto" class="form-control-file">
                    <input type="hidden" name="funcion" id="funcion">
                    <input type="hidden" name="id_logo_prod" id="id_logo_prod">
                    <input type="hidden" name="avatar" id="avatar">
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
            <h1>Gestor de productos<button  id="button_crear" type="button" data-toggle="modal" data-target="#crearproducto" class="crearpd btn bg-gradient-primary ml-2">Crear Producto</button></h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="adm_catalogo.php">INICIO</a></li>
              <li class="breadcrumb-item active">Gestor de Productos</li>
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
                    <h3 class="card-title">Buscar productos</h3>
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
  </div>
  <!-- /.content-wrapper -->
<?php include_once 'layouts/footer.php'; } else { header('Location: ../../index.php'); } ?>
<script src="../libs/js/producto.js"></script>