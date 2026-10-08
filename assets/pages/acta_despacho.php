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
  <title>Acta de Despacho #<?php echo str_pad($id_despacho, 5, '0', STR_PAD_LEFT); ?> | SIMAP</title>
  <link rel="stylesheet" href="../libs/css/bootstrap.min.css">
  <link rel="stylesheet" href="../libs/css/bootstrap-icons.min.css">
  <style>
    @page {
      size: letter portrait;
      margin: 12mm 15mm;
    }
    :root {
      --primary: #0f172a;
      --secondary: #475569;
      --muted: #64748b;
      --border: #e2e8f0;
      --border-dark: #cbd5e1;
      --surface: #f8fafc;
      --accent: #0284c7;
    }
    body {
      background-color: #f1f5f9;
      color: var(--primary);
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
      font-size: 13px;
      line-height: 1.45;
      -webkit-font-smoothing: antialiased;
    }
    .document-page {
      max-width: 820px;
      margin: 25px auto;
      background: #ffffff;
      padding: 42px 48px;
      border-radius: 4px;
      box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.08), 0 8px 10px -6px rgba(15, 23, 42, 0.04);
      border: 1px solid var(--border);
    }
    .doc-brand {
      letter-spacing: -0.02em;
    }
    .meta-grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 12px 24px;
      padding: 16px 20px;
      background-color: var(--surface);
      border: 1px solid var(--border);
      border-radius: 4px;
    }
    .meta-item .meta-label {
      font-size: 10px;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      color: var(--muted);
      font-weight: 600;
      margin-bottom: 2px;
    }
    .meta-item .meta-val {
      font-size: 13px;
      font-weight: 600;
      color: var(--primary);
    }
    .table-minimal {
      width: 100%;
      border-collapse: collapse;
      margin-top: 8px;
    }
    .table-minimal th {
      font-size: 10px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      color: var(--secondary);
      border-bottom: 1.5px solid var(--primary);
      padding: 8px 10px;
      background: transparent;
    }
    .table-minimal td {
      padding: 9px 10px;
      border-bottom: 1px solid var(--border);
      vertical-align: middle;
      font-size: 12.5px;
    }
    .table-minimal tbody tr:last-child td {
      border-bottom: 1.5px solid var(--border-dark);
    }
    .table-minimal tfoot td {
      padding: 10px;
      font-weight: 700;
      border-bottom: 2px solid var(--primary);
      background: var(--surface);
    }
    .code-tag {
      font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
      font-size: 11px;
      padding: 2px 6px;
      background-color: #f1f5f9;
      border-radius: 3px;
      color: #334155;
      border: 1px solid #e2e8f0;
    }
    .firmas-container {
      margin-top: 65px;
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 28px;
    }
    .firma-card {
      text-align: center;
      border-top: 1px solid #94a3b8;
      padding-top: 8px;
    }
    .firma-title {
      font-size: 10px;
      text-transform: uppercase;
      letter-spacing: 0.06em;
      font-weight: 700;
      color: var(--secondary);
      margin-bottom: 4px;
    }
    .firma-name {
      font-size: 12px;
      font-weight: 600;
      color: var(--primary);
    }
    .firma-sub {
      font-size: 11px;
      color: var(--muted);
    }
    .doc-stamp {
      border-top: 1px dashed var(--border);
      margin-top: 36px;
      padding-top: 12px;
      font-size: 10.5px;
      color: var(--muted);
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    @media print {
      body {
        background: #ffffff !important;
        color: #000000 !important;
        margin: 0 !important;
        padding: 0 !important;
      }
      .document-page {
        box-shadow: none !important;
        border: none !important;
        padding: 0 !important;
        margin: 0 !important;
        max-width: 100% !important;
      }
      .no-print {
        display: none !important;
      }
    }
  </style>
</head>
<body>

<!-- Barra de Controles Flotante (No imprimible) -->
<div class="no-print sticky-top bg-white border-bottom shadow-sm py-2 px-3">
  <div class="max-w-820 mx-auto d-flex justify-content-between align-items-center" style="max-width: 820px;">
    <div class="d-flex align-items-center gap-2">
      <span class="badge bg-dark px-2 py-1 font-monospace">ACTA-<?php echo str_pad($id_despacho, 5, '0', STR_PAD_LEFT); ?></span>
      <span class="small text-secondary fw-semibold">Comprobante Oficial de Despacho SIMAP</span>
    </div>
    <div class="d-flex gap-2">
      <button onclick="window.print()" class="btn btn-dark btn-sm fw-semibold d-flex align-items-center gap-1 shadow-sm">
        <i class="bi bi-printer"></i> Imprimir / PDF
      </button>
      <button onclick="window.close()" class="btn btn-outline-secondary btn-sm">
        Cerrar
      </button>
    </div>
  </div>
</div>

<div class="document-page">
  <!-- Encabezado Corporativo Minimalista -->
  <div class="d-flex justify-content-between align-items-start border-bottom pb-4 mb-4">
    <div class="d-flex align-items-center gap-3">
      <img src="../libs/img/logo.png" alt="Logo SIMAP" style="height: 52px; width: auto; object-fit: contain;" onerror="this.style.display='none'">
      <div>
        <h6 class="fw-bold mb-0 text-dark doc-brand" style="font-size: 15px; letter-spacing: -0.01em;">
          CLÍNICA POPULAR ESPECIALIZADA LA FRÍA
        </h6>
        <div class="text-secondary small fw-medium" style="font-size: 11.5px;">
          Sistema de Inventario de Materiales e Insumos de Protección (SIMAP)
        </div>
        <div class="text-muted" style="font-size: 10.5px;">
          Municipio García de Hevia, Estado Táchira &bull; República Bolivariana de Venezuela
        </div>
      </div>
    </div>
    <div class="text-end">
      <div class="text-uppercase fw-bold text-dark" style="font-size: 13px; letter-spacing: 0.05em;">
        ACTA DE ENTREGA
      </div>
      <div class="font-monospace fw-bold fs-5 text-dark" style="letter-spacing: -0.02em;">
        #<?php echo str_pad($id_despacho, 5, '0', STR_PAD_LEFT); ?>
      </div>
      <div class="text-muted" style="font-size: 11px;">
        Emisión: <span class="fw-semibold text-secondary"><?php echo $fecha_formateada; ?></span>
      </div>
    </div>
  </div>

  <!-- Rejilla de Metadatos de la Dotación -->
  <div class="meta-grid mb-4">
    <div class="meta-item">
      <div class="meta-label">Área Hospitalaria de Destino</div>
      <div class="meta-val text-dark">
        <?php echo htmlspecialchars($despacho['nombre_area'] ?? 'Área Asistencial General'); ?>
      </div>
    </div>
    <div class="meta-item">
      <div class="meta-label">Funcionario Receptor</div>
      <div class="meta-val text-dark">
        <?php echo htmlspecialchars($despacho['receptor']); ?>
        <?php if (!empty($despacho['cargo_receptor'])): ?>
          <span class="fw-normal text-muted" style="font-size: 11.5px;">— <?php echo htmlspecialchars($despacho['cargo_receptor']); ?></span>
        <?php endif; ?>
      </div>
    </div>
    <div class="meta-item">
      <div class="meta-label">Cédula de Identidad Receptor</div>
      <div class="meta-val font-monospace">
        V-<?php echo htmlspecialchars($despacho['ci_receptor']); ?>
      </div>
    </div>
    <div class="meta-item">
      <div class="meta-label">Responsable de Salida (Depósito SIMAP)</div>
      <div class="meta-val text-dark">
        <?php echo htmlspecialchars($despacho['responsable_nombre']); ?>
      </div>
    </div>
    <?php if (!empty($despacho['observacion'])): ?>
    <div class="meta-item" style="grid-column: span 2;">
      <div class="meta-label">Motivo / Justificación del Despacho</div>
      <div class="meta-val fw-normal text-secondary" style="font-size: 12px;">
        <?php echo nl2br(htmlspecialchars($despacho['observacion'])); ?>
      </div>
    </div>
    <?php endif; ?>
  </div>

  <!-- Detalle Tabular de Insumos Despachados -->
  <div class="mb-4">
    <div class="d-flex justify-content-between align-items-center mb-1">
      <span class="text-uppercase fw-bold text-secondary" style="font-size: 10.5px; letter-spacing: 0.04em;">
        Relación de Insumos y Materiales Suministrados
      </span>
      <span class="text-muted" style="font-size: 11px;">
        Total ítems: <b><?php echo count($detalles); ?></b>
      </span>
    </div>

    <table class="table-minimal">
      <thead>
        <tr>
          <th style="width: 5%; text-align: center;">#</th>
          <th style="width: 42%;">Descripción del Insumo / Material</th>
          <th style="width: 15%;">Unidad / Talla</th>
          <th style="width: 18%;">Lote / Control</th>
          <th style="width: 10%;">Caducidad</th>
          <th style="width: 10%; text-align: right;">Cant.</th>
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
          $vencimiento = !empty($det['vencimiento']) && $det['vencimiento'] !== '2035-12-31' ? date('m/Y', strtotime($det['vencimiento'])) : 'N/A';
        ?>
        <tr>
          <td style="text-align: center; color: var(--muted); font-size: 11px;"><?php echo $i++; ?></td>
          <td>
            <div class="fw-semibold text-dark"><?php echo htmlspecialchars($det['producto']); ?></div>
          </td>
          <td>
            <span class="fw-medium text-dark"><?php echo htmlspecialchars($unidad_codigo); ?></span>
            <?php if ($espec !== '-'): ?>
              <span class="text-muted" style="font-size: 11px;">(<?php echo htmlspecialchars($espec); ?>)</span>
            <?php endif; ?>
          </td>
          <td>
            <span class="code-tag"><?php echo htmlspecialchars($det['lote'] ?: 'LOTE-UNICO'); ?></span>
          </td>
          <td style="font-size: 11.5px; color: var(--muted);">
            <?php echo $vencimiento; ?>
          </td>
          <td style="text-align: right; font-weight: 700; font-size: 13.5px; color: var(--primary);">
            <?php echo number_format($det['cantidad'], 0, ',', '.'); ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
      <tfoot>
        <tr>
          <td colspan="5" style="text-align: right; font-size: 11.5px; text-transform: uppercase; color: var(--secondary);">
            Total Unidades Despachadas:
          </td>
          <td style="text-align: right; font-size: 14px; font-weight: 800; color: var(--primary);">
            <?php echo number_format($total_piezas, 0, ',', '.'); ?>
          </td>
        </tr>
      </tfoot>
    </table>
  </div>

  <!-- Declaración de Conformidad Institucional -->
  <div class="p-2 px-3 mb-4 rounded" style="background-color: var(--surface); border-left: 3px solid #0f172a; font-size: 11px; color: var(--secondary);">
    <b>Certificación de Salida:</b> Los materiales detallados han sido retirados del almacén central para uso exclusivo en la atención hospitalaria y protocolos de bioseguridad de la unidad solicitante.
  </div>

  <!-- Sección de Firmas Formales -->
  <div class="firmas-container">
    <div class="firma-card">
      <div class="firma-title">Entregado por</div>
      <div class="firma-name"><?php echo htmlspecialchars($despacho['responsable_nombre']); ?></div>
      <div class="firma-sub">Responsable SIMAP</div>
    </div>
    <div class="firma-card">
      <div class="firma-title">Recibido Conforme</div>
      <div class="firma-name"><?php echo htmlspecialchars($despacho['receptor']); ?></div>
      <div class="firma-sub">C.I. V-<?php echo htmlspecialchars($despacho['ci_receptor']); ?></div>
    </div>
    <div class="firma-card">
      <div class="firma-title">Conformidad / Sello</div>
      <div class="firma-name">Supervisión Médica</div>
      <div class="firma-sub">C.P.E. La Fría</div>
    </div>
  </div>

  <!-- Pie de Documento y Trazabilidad -->
  <div class="doc-stamp">
    <div>
      Documento Oficial emitido por SIMAP &bull; Registro #<?php echo str_pad($id_despacho, 5, '0', STR_PAD_LEFT); ?>
    </div>
    <div>
      Página 1 de 1
    </div>
  </div>

</div>

</body>
</html>
