<?php
// Verificar que se recibió el ID de venta
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die('Error: ID de venta no especificado');
}

require_once '../../vendor/autoload.php';
require_once '../../db/venta.php';
require_once '../../controlador/EmpresaController.php'; // Asumiendo que existe un controlador de datos de empresa

// Inicializar objetos
$venta_model = new Venta();
$empresa_controller = new EmpresaController();

// Obtener ID de venta
$id_venta = $_GET['id'];

try {
    // Obtener datos de la venta
    $venta = $venta_model->obtener_venta($id_venta);
    
    if (!$venta) {
        die('Error: Venta no encontrada');
    }
    
    // Obtener detalles de la venta
    $detalles = $venta_model->ver_detalle_venta($id_venta);
    
    // Obtener datos de la empresa
    $empresa = $empresa_controller->obtener_datos();
    
    // Configurar TCPDF
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    
    // Establecer información del documento
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Sistema de Ventas');
    $pdf->SetTitle('Recibo de Venta #' . $id_venta);
    $pdf->SetSubject('Recibo de Venta');
    
    // Eliminar cabeceras y pies de página predeterminados
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);
    
    // Establecer márgenes
    $pdf->SetMargins(15, 15, 15);
    
    // Establecer salto de página automático
    $pdf->SetAutoPageBreak(TRUE, 15);
    
    // Establecer fuente
    $pdf->SetFont('helvetica', '', 10);
    
    // Añadir página
    $pdf->AddPage();
    
    // Encabezado con logo y datos de la empresa
    $html_header = '
    <table cellspacing="0" cellpadding="1" border="0">
        <tr>
            <td style="width:30%;">
                <img src="../../img/logo.png" style="width:100px;">
            </td>
            <td style="width:70%; text-align:right;">
                <h2>' . $empresa['nombre'] . '</h2>
                <p>' . $empresa['direccion'] . '<br>
                Tel: ' . $empresa['telefono'] . '<br>
                Email: ' . $empresa['email'] . '</p>
            </td>
        </tr>
    </table>
    <hr>';
    
    $pdf->writeHTML($html_header, true, false, true, false, '');
    
    // Información de la venta
    $fecha_venta = date('d/m/Y H:i', strtotime($venta['fecha']));
    $html_info = '
    <table cellspacing="0" cellpadding="1" border="0">
        <tr>
            <td style="width:60%;">
                <h3>RECIBO DE VENTA #' . $id_venta . '</h3>
                <p><strong>Fecha:</strong> ' . $fecha_venta . '</p>
                <p><strong>Cliente:</strong> ' . $venta['cliente'] . '</p>
                <p><strong>CI/NIT:</strong> ' . $venta['ci'] . '</p>
            </td>
            <td style="width:40%; text-align:right;">
                <p><strong>Vendedor:</strong> ' . $venta['vendedor'] . '</p>
            </td>
        </tr>
    </table>
    <br>';
    
    $pdf->writeHTML($html_info, true, false, true, false, '');
    
    // Tabla de productos
    $html_productos = '
    <h4>DETALLE DE PRODUCTOS</h4>
    <table cellspacing="0" cellpadding="5" border="1">
        <thead>
            <tr style="background-color:#f8f9fa; font-weight:bold;">
                <th style="width:40%;">Producto</th>
                <th style="width:15%;">Lote</th>
                <th style="width:15%;">Precio</th>
                <th style="width:15%;">Cantidad</th>
                <th style="width:15%;">Subtotal</th>
            </tr>
        </thead>
        <tbody>';
    
    $total = 0;
    foreach ($detalles as $detalle) {
        $subtotal = $detalle['precio'] * $detalle['cantidad'];
        $total += $subtotal;
        
        $html_productos .= '
        <tr>
            <td>' . $detalle['producto'] . '</td>
            <td>' . ($detalle['lote'] ?: 'N/A') . '</td>
            <td align="right">' . number_format($detalle['precio'], 2) . '</td>
            <td align="center">' . $detalle['cantidad'] . '</td>
            <td align="right">' . number_format($subtotal, 2) . '</td>
        </tr>';
    }
    
    $html_productos .= '
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" align="right"><strong>TOTAL:</strong></td>
                <td align="right"><strong>' . number_format($total, 2) . '</strong></td>
            </tr>
        </tfoot>
    </table>';
    
    $pdf->writeHTML($html_productos, true, false, true, false, '');
    
    // Términos y condiciones, firmas
    $html_footer = '
    <br><br>
    <table cellspacing="0" cellpadding="1" border="0">
        <tr>
            <td style="width:50%; text-align:center; border-top: 1px solid #000000;">
                <p>Firma del Cliente</p>
            </td>
            <td style="width:50%; text-align:center; border-top: 1px solid #000000;">
                <p>Sello y Firma</p>
            </td>
        </tr>
    </table>
    <br><br>
    <p style="font-size:8pt; text-align:center;">
        Este documento no tiene valor fiscal, es un comprobante de venta.<br>
        Gracias por su compra.
    </p>';
    
    $pdf->writeHTML($html_footer, true, false, true, false, '');
    
    // Generar PDF
    $pdf->Output('recibo_venta_' . $id_venta . '.pdf', 'I');
    
} catch (Exception $e) {
    die('Error: ' . $e->getMessage());
}
?>