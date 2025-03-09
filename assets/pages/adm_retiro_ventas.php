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
    
    <!-- Main Retiros Ventas -->
    <section class="content">
      <div class="container-fluid">
        <div class="card card-primary">
          <div class="card-header">
            <h3 class="card-title">Gestión de Ventas</h3>
          </div>
          <div class="card-body">
            <!-- Filtros de búsqueda -->
            <div class="row mb-3">
              <div class="col-md-3">
                <div class="form-group">
                  <label for="fecha_inicio">Fecha Inicio</label>
                  <input type="date" class="form-control" id="fecha_inicio">
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                  <label for="fecha_fin">Fecha Fin</label>
                  <input type="date" class="form-control" id="fecha_fin">
                </div>
              </div>
              <div class="col-md-3 mt-4">
                <div class="form-group">
                  <button id="btn_filtrar" class="btn btn-primary">
                    <i class="fas fa-search"></i> Filtrar
                  </button>
                  <button id="btn_limpiar" class="btn btn-secondary">
                    <i class="fas fa-broom"></i> Limpiar
                  </button>
                </div>
              </div>
            </div>
            
            <!-- Tabla de ventas -->
            <div class="row">
              <div class="col-md-12">
                <table id="tabla_ventas" class="table table-bordered table-striped">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Fecha</th>
                      <th>Cliente</th>
                      <th>CI/RIF</th>
                      <th>Total</th>
                      <th>Vendedor</th>
                      <th>Acciones</th>
                    </tr>
                  </thead>
                  <tbody>
                    <!-- Datos cargados dinámicamente -->
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    
    <!-- Modal para ver detalles de venta -->
    <div class="modal fade" id="vista_venta">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header bg-primary">
            <h4 class="modal-title">Detalles de Venta</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label>Cliente:</label>
                  <span id="cliente_detalle"></span>
                </div>
                <div class="form-group">
                  <label>CI/RIF:</label>
                  <span id="ci_detalle"></span>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label>Fecha:</label>
                  <span id="fecha_detalle"></span>
                </div>
                <div class="form-group">
                  <label>Vendedor:</label>
                  <span id="vendedor_detalle"></span>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <table class="table table-bordered table-striped">
                  <thead>
                    <tr>
                      <th>Producto</th>
                      <th>Cantidad</th>
                      <th>Precio Unitario</th>
                      <th>Subtotal</th>
                      <th>Lote</th>
                      <th>Vencimiento</th>
                    </tr>
                  </thead>
                  <tbody id="detalles_venta">
                    <!-- Detalles cargados dinámicamente -->
                  </tbody>
                  <tfoot>
                    <tr>
                      <th colspan="3" class="text-right">Total:</th>
                      <th id="total_detalle" colspan="3"></th>
                    </tr>
                  </tfoot>
                </table>
              </div>
            </div>
          </div>
          <div class="modal-footer justify-content-between">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            <button type="button" class="btn btn-primary" id="btn_imprimir">
              <i class="fas fa-print"></i> Imprimir
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal para confirmar revertir venta -->
    <div class="modal fade" id="confirmar_revertir">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header bg-danger">
            <h4 class="modal-title">Confirmar Anulación</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <p>¿Está seguro que desea anular esta venta?</p>
            <p>Esta acción devolverá los productos al inventario y eliminará el registro de venta.</p>
          </div>
          <div class="modal-footer justify-content-between">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
            <button type="button" class="btn btn-danger" id="btn_confirmar_revertir">Confirmar</button>
          </div>
        </div>
      </div>
    </div>
    
    </div>
<!-- /.content-wrapper -->
<?php include_once 'layouts/footer.php'; } else { header('Location: ../../index.php'); } ?>
<script src="../libs/js/catalogo.js"></script>
<script src="../libs/js/carrito.js"></script>
<script src="../libs/js/retiro_ventas.js"></script>