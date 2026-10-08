<?php
// SIMAP - Acta Oficial de Entrega / Despacho Institucional
// C.P.E. La Fría - Inventario de Materiales e Insumos de Protección

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die('Error: Identificador de despacho no especificado.');
}

require_once '../db/despacho.php';

$despacho_model = new Despacho();
$id_despacho = intval($_GET['id']);

$despacho = $despacho_model->obtener_despacho($id_despacho);
if (!$despacho) {
    die('Error: No se encontró el registro de entrega solicitada.');
}

$detalles = $despacho_model->ver_detalle_despacho($id_despacho);
$fecha_formateada = date('d/m/Y h:i A', strtotime($despacho['fecha']));
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Acta de Despacho #<?php echo htmlspecialchars($id_despacho); ?> | SIMAP</title>
  <link rel="stylesheet" href="../libs/css/bootstrap.min.css">
  <link rel="stylesheet" href="../libs/css/bootstrap-icons.min.css">
  <style>
    body {
      background-color: #f8fafc;
      color: #1e293b;
      font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
    }
    .document-page {
      max-width: 820px;
      margin: 30px auto;
      background: #ffffff;
      padding: 40px;
      border-radius: 8px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
    }
    .inst-header {
      border-bottom: 2px solid #0284c7;
      padding-bottom: 15px;
      margin-bottom: 25px;
    }
    .badge-riesgo {
      font-size: 0.75rem;
      padding: 4px 8px;
      border-radius: 4px;
    }
    .firmas-section {
      margin-top: 50px;
      padding-top: 20px;
    }
    .firma-box {
      border-top: 1px solid #64748b;
      padding-top: 8px;
      text-align: center;
      margin-top: 60px;
      font-size: 0.85rem;
    }
    @media print {
      body {
        background: #ffffff;
        color: #000000;
        margin: 0;
        padding: 0;
      }
      .document-page {
        box-shadow: none;
        padding: 0;
        margin: 0;
        max-width: 100%;
        border-radius: 0;
      }
      .no-print {
        display: none !important;
      }
    }
  </style>
</head>
<body>

<!-- Barra Superior Flotante para Acciones (No imprimible) -->
<div class="no-print bg-dark text-white py-2 px-3 sticky-top shadow-sm d-flex justify-content-between align-items-center">
  <div class="d-flex align-items-center gap-2">
    <i class="bi bi-file-earmark-medical-fill text-info fs-5"></i>
    <span class="fw-semibold">SIMAP - Acta Oficial de Despacho #<?php echo htmlspecialchars($id_despacho); ?></span>
  </div>
  <div class="d-flex gap-2">
    <button onclick="window.print()" class="btn btn-primary btn-sm fw-semibold">
      <i class="bi bi-printer me-1"></i>Imprimir / Guardar PDF
    </button>
    <button onclick="window.close()" class="btn btn-outline-light btn-sm">
      <i class="bi bi-x-lg me-1"></i>Cerrar
    </button>
  </div>
</div>

<div class="document-page">
  <!-- Encabezado Institucional -->
  <div class="inst-header d-flex justify-content-between align-items-start">
    <div class="d-flex align-items-center gap-3">
      <img src="../libs/img/logo.png" alt="Logo Hospital" style="max-height: 65px;" onerror="this.style.display='none'">
      <div>
        <h5 class="fw-bold mb-0 text-primary">CLÍNICA POPULAR ESPECIALIZADA LA FRÍA</h5>
        <div class="small text-secondary fw-semibold">Sistema de Inventario de Materiales e Insumos de Protección (SIMAP)</div>
        <small class="text-muted">Municipio García de Hevia, Estado Táchira - República Bolivariana de Venezuela</small>
      </div>
    </div>
    <div class="text-end">
      <div class="badge bg-light text-dark border fs-6 fw-bold px-3 py-2 mb-1">
        ACTA DE ENTREGA #<?php echo htmlspecialchars($id_despacho); ?>
      </div>
      <div class="small text-muted d-block">Fecha: <?php echo $fecha_formateada; ?></div>
    </div>
  </div>

  <!-- Información de la Entrega y Receptor -->
  <div class="card border-0 bg-light rounded-3 p-3 mb-4">
    <div class="row g-3 small">
      <div class="col-md-6">
        <span class="text-muted d-block">Área Hospitalaria de Destino:</span>
        <strong class="text-primary fs-6"><?php echo htmlspecialchars($despacho['nombre_area'] ?? 'Área Asistencial General'); ?></strong>
        <span class="badge bg-secondary ms-1 badge-riesgo">Riesgo: <?php echo htmlspecialchars($despacho['nivel_riesgo'] ?? 'Bajo'); ?></span>
      </div>
      <div class="col-md-6">
        <span class="text-muted d-block">Funcionario Receptor:</span>
        <strong class="text-dark fs-6"><?php echo htmlspecialchars($despacho['receptor']); ?></strong>
        <?php if (!empty($despacho['cargo_receptor'])): ?>
          <span class="text-muted d-block">(<?php echo htmlspecialchars($despacho['cargo_receptor']); ?>)</span>
        <?php endif; ?>
      </div>
      <div class="col-md-6">
        <span class="text-muted d-block">Cédula de Identidad:</span>
        <strong class="text-dark"><?php echo htmlspecialchars($despacho['ci_receptor']); ?></strong>
      </div>
      <div class="col-md-6">
        <span class="text-muted d-block">Responsable de Despacho (SIMAP):</span>
        <strong class="text-dark"><?php echo htmlspecialchars($despacho['responsable_nombre']); ?></strong>
      </div>
      <?php if (!empty($despacho['observacion'])): ?>
      <div class="col-12 border-top pt-2 mt-2">
        <span class="text-muted d-block">Motivo / Observaciones:</span>
        <span class="text-dark italic"><?php echo nl2br(htmlspecialchars($despacho['observacion'])); ?></span>
      </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- Tabla de Insumos y Materiales Despachados -->
  <div class="table-responsive mb-4">
    <table class="table table-bordered align-middle mb-0" style="font-size: 0.88rem;">
      <thead class="table-light">
        <tr>
          <th style="width: 5%;">#</th>
          <th style="width: 38%;">Insumo / Material</th>
          <th style="width: 15%;">Unidad / Talla</th>
          <th style="width: 14%;">Lote</th>
          <th style="width: 14%;">Vencimiento</th>
          <th style="width: 14%;" class="text-center">Cantidad</th>
        </tr>
      </thead>
      <tbody>
        <?php 
        $i = 1;
        $total_piezas = 0;
        foreach ($detalles as $det): 
          $total_piezas += intval($det['cantidad']);
          $unidad_codigo = !empty($det['unidad_codigo']) ? $det['unidad_codigo'] : 'und';
          $espec = !empty($det['especificacion_talla']) ? $det['especificacion_talla'] : '-';
        ?>
        <tr>
          <td class="text-center text-muted"><?php echo $i++; ?></td>
          <td>
            <strong class="text-dark"><?php echo htmlspecialchars($det['producto']); ?></strong>
          </td>
          <td>
            <span class="fw-semibold text-primary"><?php echo htmlspecialchars($unidad_codigo); ?></span>
            <small class="text-muted d-block"><?php echo htmlspecialchars($espec); ?></small>
          </td>
          <td>
            <span class="badge bg-light text-dark border"><?php echo htmlspecialchars($det['lote'] ?: 'LOTE-UNICO'); ?></span>
          </td>
          <td class="small text-muted">
            <?php echo htmlspecialchars($det['vencimiento'] ?: 'No aplica'); ?>
          </td>
          <td class="text-center fw-bold fs-6 text-primary">
            <?php echo htmlspecialchars($det['cantidad']); ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
      <tfoot class="table-light fw-bold">
        <tr>
          <td colspan="5" class="text-end">Total de Unidades / Insumos Despachados:</td>
          <td class="text-center fs-6 text-primary"><?php echo $total_piezas; ?></td>
        </tr>
      </tfoot>
    </table>
  </div>

  <div class="small text-muted mb-4">
    <i class="bi bi-info-circle me-1"></i>
    <strong>Régimen Institucional:</strong> Entrega formal de materiales e insumos de protección y bioseguridad para uso estricto en las instalaciones y servicios de la Clínica Popular Especializada La Fría.
  </div>

  <!-- Sección de Firmas y Validación Institucional -->
  <div class="firmas-section">
    <div class="row text-center">
      <div class="col-4">
        <div class="firma-box">
          <strong>ENTREGADO POR</strong><br>
          <?php echo htmlspecialchars($despacho['responsable_nombre']); ?><br>
          <span class="text-muted">Depósito de Insumos SIMAP</span>
        </div>
      </div>
      <div class="col-4">
        <div class="firma-box">
          <strong>RECIBIDO CONFORME</strong><br>
          <?php echo htmlspecialchars($despacho['receptor']); ?><br>
          <span class="text-muted">C.I: <?php echo htmlspecialchars($despacho['ci_receptor']); ?></span>
        </div>
      </div>
      <div class="col-4">
        <div class="firma-box">
          <strong>SELLO Y CONFORMIDAD</strong><br>
          <span>Coordinación Asistencial</span><br>
          <span class="text-muted">C.P.E. La Fría</span>
        </div>
      </div>
    </div>
  </div>

</div>

</body>
</html>
