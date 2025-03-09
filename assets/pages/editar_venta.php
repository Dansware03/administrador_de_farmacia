<?php session_start(); if ($_SESSION['us_tipo']==1||$_SESSION['us_tipo']==3) { include_once 'layouts/header.php'; ?>
<title><?php echo $_SESSION['nombre_us']; ?> | Farmacia</title>
<?php include_once 'layouts/nav.php'; $id_venta = $_GET['id']; ?>
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
    
    <!-- Main Editar Ventas -->
            <!-- Contenido principal -->
            <div class="col-md-10 bg-light pt-3">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Editar Venta #<?= $id_venta ?></h4>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info" role="alert">
                            <strong>Nota:</strong> Al modificar esta venta se actualizarán los inventarios automáticamente.
                        </div>

                        <!-- Detalles de la venta -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="cliente">Cliente:</label>
                                    <input type="text" class="form-control" id="cliente" name="cliente">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="ci">CI/NIT:</label>
                                    <input type="text" class="form-control" id="ci" name="ci">
                                </div>
                            </div>
                        </div>

                        <!-- Tabla de productos -->
                        <div class="table-responsive">
                            <table class="table table-hover" id="tabla_detalle_venta">
                                <thead class="bg-info">
                                    <tr>
                                        <th>Producto</th>
                                        <th>Lote</th>
                                        <th>Vencimiento</th>
                                        <th>Precio</th>
                                        <th>Cantidad</th>
                                        <th>Subtotal</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Los datos se cargan con JavaScript -->
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="5" class="text-right">Total:</th>
                                        <th id="total_venta">0.00</th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-12">
                                <button id="btn_actualizar_venta" class="btn btn-success float-right">
                                    <i class="fas fa-save mr-1"></i> Guardar Cambios
                                </button>
                                <a href="../pages/adm_retiro_ventas.php" class="btn btn-secondary mr-2 float-right">
                                    <i class="fas fa-arrow-left mr-1"></i> Cancelar
                                </a>
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>

    <!-- Modal para editar cantidad -->
    <div class="modal fade" id="modal_editar_cantidad" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Editar Cantidad</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="producto_editar">Producto:</label>
                        <input type="text" class="form-control" id="producto_editar" readonly>
                        <input type="hidden" id="id_detalle_editar">
                    </div>
                    <div class="form-group">
                        <label for="stock_disponible">Stock Disponible:</label>
                        <input type="number" class="form-control" id="stock_disponible" readonly>
                    </div>
                    <div class="form-group">
                        <label for="cantidad_actual">Cantidad Actual:</label>
                        <input type="number" class="form-control" id="cantidad_actual" readonly>
                    </div>
                    <div class="form-group">
                        <label for="nueva_cantidad">Nueva Cantidad:</label>
                        <input type="number" class="form-control" id="nueva_cantidad" min="1">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="btn_guardar_cantidad">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    </div>
<!-- /.content-wrapper -->
<?php include_once 'layouts/footer.php'; } else { header('Location: ../../index.php'); } ?>
<script src="../libs/js/catalogo.js"></script>
<script src="../libs/js/carrito.js"></script>
<script src="../libs/js/editar_venta.js"></script>