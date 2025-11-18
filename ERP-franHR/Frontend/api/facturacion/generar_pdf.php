<?php
require_once __DIR__ . '/facturacion_utils.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        http_response_code(405);
        die('Método no permitido');
    }

    $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
    $formato = $_GET['formato'] ?? 'pdf'; // 'pdf' o 'html'

    if ($id <= 0) {
        http_response_code(400);
        die('ID de factura inválido');
    }

    $db = facturacion_db();
    $factura = factura_obtener($db, $id);

    if (!$factura) {
        http_response_code(404);
        die('Factura no encontrada');
    }

    // Generar HTML para el PDF
    $html = generarHTMLFactura($factura);

    if ($formato === 'html') {
        // Devolver HTML para vista previa
        header('Content-Type: text/html; charset=utf-8');
        echo $html;
        exit;
    }

    // Generar PDF usando DOMPDF (requiere instalación)
    if (class_exists('Dompdf\Dompdf')) {
        $options = new \Dompdf\Options();
        $options->set('defaultFont', 'Arial');
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $nombreArchivo = "factura_{$factura['numero_factura']}.pdf";

        header('Content-Type: application/pdf');
        header("Content-Disposition: inline; filename=\"$nombreArchivo\"");
        header('Cache-Control: private, max-age=0, must-revalidate');
        header('Pragma: public');

        $dompdf->stream($nombreArchivo, ['Attachment' => false]);
    } else {
        // Método alternativo simple si no hay DOMPDF
        header('Content-Type: text/html; charset=utf-8');
        header("Content-Disposition: inline; filename=\"factura_{$factura['numero_factura']}.html\"");
        echo $html;
    }

} catch (Throwable $e) {
    http_response_code(500);
    die('Error al generar PDF: ' . $e->getMessage());
}

function generarHTMLFactura($factura)
{
    $terceroNombre = $factura['cliente_nombre'] ?? $factura['proveedor_nombre'] ?? 'Sin asignar';
    $tipoFactura = ucfirst($factura['tipo_factura']);
    $estadoFactura = ucfirst($factura['estado']);

    // Marca de agua para borradores
    $marcaAgua = ($factura['estado'] === 'borrador') ?
        '<div style="position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%) rotate(-45deg); font-size: 120px; color: rgba(255,0,0,0.1); font-weight: bold; z-index: -1;">BORRADOR</div>' : '';

    $html = <<<HTML
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura {$factura['numero_factura']}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 12px; line-height: 1.4; color: #333; }
        .container { max-width: 800px; margin: 0 auto; padding: 20px; }
        .header { border-bottom: 2px solid #333; padding-bottom: 20px; margin-bottom: 30px; }
        .header h1 { font-size: 24px; margin-bottom: 10px; }
        .info-empresa { float: left; width: 50%; }
        .info-factura { float: right; width: 50%; text-align: right; }
        .info-tercero { margin: 30px 0; padding: 15px; background: #f5f5f5; border-radius: 5px; }
        .tabla-lineas { width: 100%; border-collapse: collapse; margin: 20px 0; }
        .tabla-lineas th { background: #333; color: white; padding: 10px; text-align: left; font-weight: bold; }
        .tabla-lineas td { padding: 8px; border-bottom: 1px solid #ddd; }
        .tabla-lineas .numero { text-align: center; width: 40px; }
        .tabla-lineas .descripcion { text-align: left; }
        .tabla-lineas .cantidad { text-align: center; width: 60px; }
        .tabla-lineas .precio { text-align: right; width: 80px; }
        .tabla-lineas .descuento { text-align: center; width: 60px; }
        .tabla-lineas .iva { text-align: center; width: 50px; }
        .tabla-lineas .total { text-align: right; width: 100px; font-weight: bold; }
        .totales { margin-top: 30px; text-align: right; }
        .totales table { width: 300px; border-collapse: collapse; }
        .totales td { padding: 5px; }
        .totales .total-row { font-weight: bold; font-size: 14px; border-top: 2px solid #333; }
        .observaciones { margin-top: 30px; padding: 15px; background: #f9f9f9; border-radius: 5px; }
        .footer { margin-top: 50px; padding-top: 20px; border-top: 1px solid #ddd; text-align: center; font-size: 10px; color: #666; }
        .clearfix::after { content: ""; display: table; clear: both; }
        .estado-badge { padding: 5px 10px; border-radius: 3px; font-size: 10px; font-weight: bold; }
        .estado-borrador { background: #f0f0f0; color: #666; }
        .estado-pendiente { background: #fff3cd; color: #856404; }
        .estado-pagada, .estado-cobrada { background: #d4edda; color: #155724; }
        .estado-vencida { background: #f8d7da; color: #721c24; }
        .estado-cancelada { background: #e2e3e5; color: #383d41; }
    </style>
</head>
<body>
    {$marcaAgua}
    <div class="container">
        <div class="header clearfix">
            <div class="info-empresa">
                <h1>FACTURA {$tipoFactura}</h1>
                <p><strong>Número:</strong> {$factura['numero_factura']}</p>
                <p><strong>Fecha:</strong> {$factura['fecha']}</p>
                <p><strong>Vencimiento:</strong> {$factura['fecha_vencimiento']}</p>
                <p><strong>Estado:</strong> <span class="estado-badge estado-{$factura['estado']}">{$estadoFactura}</span></p>
            </div>
            <div class="info-factura">
                <p><strong>Ejercicio:</strong> {$factura['ejercicio']}</p>
                <p><strong>Serie:</strong> {$factura['numero_serie']}</p>
                <p><strong>Moneda:</strong> {$factura['moneda']}</p>
                <p><strong>Forma de pago:</strong> {$factura['forma_pago']}</p>
            </div>
        </div>

        <div class="info-tercero">
            <h3>Datos del {$tipoFactura}</h3>
            <p><strong>{$terceroNombre}</strong></p>
HTML;

    if ($factura['direccion_envio_id']) {
        $html .= '<p><strong>Dirección envío:</strong> ID ' . $factura['direccion_envio_id'] . '</p>';
    }

    if ($factura['contacto_id']) {
        $html .= '<p><strong>Contacto:</strong> ID ' . $factura['contacto_id'] . '</p>';
    }

    $html .= <<<HTML
        </div>

        <table class="tabla-lineas">
            <thead>
                <tr>
                    <th class="numero">#</th>
                    <th class="descripcion">Descripción</th>
                    <th class="cantidad">Cant.</th>
                    <th class="precio">Precio</th>
                    <th class="descuento">Dto.%</th>
                    <th class="iva">IVA%</th>
                    <th class="total">Total</th>
                </tr>
            </thead>
            <tbody>
HTML;

    foreach ($factura['lineas'] as $linea) {
        $html .= <<<HTML
                <tr>
                    <td class="numero">{$linea['linea']}</td>
                    <td class="descripcion">{$linea['descripcion']}</td>
                    <td class="cantidad">{$linea['cantidad']}</td>
                    <td class="precio">{$linea['precio_unitario']} €</td>
                    <td class="descuento">{$linea['descuento_linea']}%</td>
                    <td class="iva">{$linea['iva_tipo']}%</td>
                    <td class="total">{$linea['total_linea']} €</td>
                </tr>
HTML;
    }

    $html .= <<<HTML
            </tbody>
        </table>

        <div class="totales">
            <table>
                <tr>
                    <td>Base imponible:</td>
                    <td style="text-align: right;">{$factura['base_imponible']} €</td>
                </tr>
                <tr>
                    <td>Descuento:</td>
                    <td style="text-align: right;">{$factura['importe_descuento']} €</td>
                </tr>
                <tr>
                    <td>Base imponible descuento:</td>
                    <td style="text-align: right;">{$factura['base_imponible_descuento']} €</td>
                </tr>
                <tr>
                    <td>IVA:</td>
                    <td style="text-align: right;">{$factura['importe_iva']} €</td>
                </tr>
                <tr>
                    <td>IRPF:</td>
                    <td style="text-align: right;">{$factura['importe_irpf']} €</td>
                </tr>
                <tr class="total-row">
                    <td>TOTAL:</td>
                    <td style="text-align: right;">{$factura['total']} €</td>
                </tr>
            </table>
        </div>

HTML;

    if ($factura['importe_pagado'] > 0) {
        $pendiente = $factura['total'] - $factura['importe_pagado'];
        $html .= <<<HTML
        <div class="totales">
            <table>
                <tr>
                    <td>Importe pagado:</td>
                    <td style="text-align: right;">{$factura['importe_pagado']} €</td>
                </tr>
                <tr>
                    <td>Pendiente:</td>
                    <td style="text-align: right;">{$pendiente} €</td>
                </tr>
            </table>
        </div>
HTML;
    }

    if ($factura['terminos_condiciones']) {
        $html .= <<<HTML
        <div class="observaciones">
            <h4>Términos y condiciones</h4>
            <p>{$factura['terminos_condiciones']}</p>
        </div>
HTML;
    }

    if ($factura['motivo_rectificacion']) {
        $html .= <<<HTML
        <div class="observaciones">
            <h4>Motivo de rectificación</h4>
            <p>{$factura['motivo_rectificacion']}</p>
        </div>
HTML;
    }

    $html .= <<<HTML
        <div class="footer">
            <p>Factura generada el: {$factura['created_at']}</p>
            <p>Este documento es una {$tipoFactura} válida y tiene efectos legales.</p>
        </div>
    </div>
</body>
</html>
HTML;

    return $html;
}