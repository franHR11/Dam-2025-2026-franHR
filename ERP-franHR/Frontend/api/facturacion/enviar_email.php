<?php
require_once __DIR__ . '/facturacion_utils.php';

header('Content-Type: application/json; charset=utf-8');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['ok' => false, 'error' => 'Método no permitido']);
        exit;
    }

    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if (!$data || !isset($data['id']) || !isset($data['email'])) {
        http_response_code(400);
        echo json_encode(['ok' => false, 'error' => 'ID de factura y email son requeridos']);
        exit;
    }

    $id = (int) $data['id'];
    $email = filter_var($data['email'], FILTER_VALIDATE_EMAIL);
    $mensaje = $data['mensaje'] ?? '';

    if ($id <= 0) {
        http_response_code(400);
        echo json_encode(['ok' => false, 'error' => 'ID de factura inválido']);
        exit;
    }

    if (!$email) {
        http_response_code(400);
        echo json_encode(['ok' => false, 'error' => 'Email inválido']);
        exit;
    }

    $db = facturacion_db();
    $factura = factura_obtener($db, $id);

    if (!$factura) {
        http_response_code(404);
        echo json_encode(['ok' => false, 'error' => 'Factura no encontrada']);
        exit;
    }

    // Generar PDF adjunto
    $pdfContenido = generarPDFAdjunto($factura);

    // Preparar email
    $asunto = "Factura {$factura['numero_factura']} - " . ($factura['cliente_nombre'] ?? $factura['proveedor_nombre'] ?? 'Cliente');

    $cuerpoEmail = generarCuerpoEmail($factura, $mensaje);

    // Enviar email usando PHPMailer o mail() nativo
    $enviado = enviarEmailFactura($email, $asunto, $cuerpoEmail, $pdfContenido, $factura['numero_factura']);

    if ($enviado) {
        // Actualizar registro de envío
        $stmt = $db->prepare("UPDATE facturas SET enviada_email = 1, fecha_envio_email = NOW() WHERE id = ?");
        $stmt->execute([$id]);

        echo json_encode([
            'ok' => true,
            'message' => 'Factura enviada correctamente',
            'email' => $email,
            'fecha_envio' => date('Y-m-d H:i:s')
        ]);
    } else {
        throw new Exception('Error al enviar el email');
    }

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
}

function generarPDFAdjunto($factura)
{
    // Generar HTML para PDF
    $html = generarHTMLFactura($factura);

    // Si está disponible DOMPDF, generarlo como string
    if (class_exists('Dompdf\Dompdf')) {
        $options = new \Dompdf\Options();
        $options->set('defaultFont', 'Arial');
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $dompdf->output();
    }

    // Método alternativo simple
    return $html;
}

function generarCuerpoEmail($factura, $mensajePersonalizado)
{
    $terceroNombre = $factura['cliente_nombre'] ?? $factura['proveedor_nombre'] ?? 'Cliente';
    $tipoFactura = ucfirst($factura['tipo_factura']);

    $cuerpo = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Factura {$factura['numero_factura']}</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px; }
        .content { background: #ffffff; padding: 20px; border: 1px solid #dee2e6; border-radius: 8px; }
        .footer { margin-top: 30px; padding-top: 20px; border-top: 1px solid #dee2e6; font-size: 12px; color: #6c757d; }
        .btn { display: inline-block; padding: 12px 24px; background: #007bff; color: white; text-decoration: none; border-radius: 6px; margin: 20px 0; }
        .totales { background: #f8f9fa; padding: 15px; border-radius: 6px; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Factura {$tipoFactura} {$factura['numero_factura']}</h2>
        <p>Estimado/a {$terceroNombre},</p>
    </div>
    
    <div class="content">
        <p>Adjuntamos su {$tipoFactura} número <strong>{$factura['numero_factura']}</strong> correspondiente a:</p>
        
        <ul>
            <li><strong>Fecha:</strong> {$factura['fecha']}</li>
            <li><strong>Vencimiento:</strong> {$factura['fecha_vencimiento']}</li>
            <li><strong>Importe total:</strong> {$factura['total']} {$factura['moneda']}</li>
        </ul>
        
        <div class="totales">
            <p><strong>Resumen:</strong></p>
            <ul>
                <li>Base imponible: {$factura['base_imponible']} {$factura['moneda']}</li>
                <li>IVA: {$factura['importe_iva']} {$factura['moneda']}</li>
                <li><strong>Total: {$factura['total']} {$factura['moneda']}</strong></li>
            </ul>
        </div>
        
        $mensajeAdicionalHTML = '';
        if ($mensajePersonalizado) {
            $mensajeAdicionalHTML = "<p><strong>Mensaje adicional:</strong></p><p>{$mensajePersonalizado}</p>";
        }
        
        $cuerpo .= $mensajeAdicionalHTML;
        
        <p>Para cualquier consulta, no dude en contactarnos.</p>
        
        <a href="#" class="btn">Ver factura en línea</a>
    </div>
    
    <div class="footer">
        <p>Este es un email automático. Por favor, no responda a este mensaje.</p>
        <p>Si tiene problemas para ver la factura adjunta, contacte con soporte.</p>
    </div>
</body>
</html>
HTML;

    return $cuerpo;
}

function enviarEmailFactura($email, $asunto, $cuerpo, $adjunto, $numeroFactura)
{
    // Intentar usar PHPMailer si está disponible
    if (class_exists('PHPMailer\PHPMailer')) {
        return enviarEmailPHPMailer($email, $asunto, $cuerpo, $adjunto, $numeroFactura);
    }

    // Método alternativo usando mail() nativo
    return enviarEmailNativo($email, $asunto, $cuerpo, $adjunto, $numeroFactura);
}

function enviarEmailPHPMailer($email, $asunto, $cuerpo, $adjunto, $numeroFactura)
{
    if (!class_exists('PHPMailer\PHPMailer')) {
        return false;
    }

    try {
        $mail = new \PHPMailer\PHPMailer(true);

        // Configuración del servidor (debería estar en configuración)
        $mail->isSMTP();
        $mail->Host = 'smtp.example.com'; // Configurar
        $mail->SMTPAuth = true;
        $mail->Username = 'user@example.com'; // Configurar
        $mail->Password = 'password'; // Configurar
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Remitente y destinatario
        $mail->setFrom('facturas@empresa.com', 'Sistema de Facturación');
        $mail->addAddress($email);

        // Contenido
        $mail->isHTML(true);
        $mail->Subject = $asunto;
        $mail->Body = $cuerpo;

        // Adjunto
        $mail->addStringAttachment(
            $adjunto,
            "factura_{$numeroFactura}.pdf",
            'base64',
            'application/pdf'
        );

        $mail->send();
        return true;

    } catch (Exception $e) {
        error_log("Error PHPMailer: " . $e->getMessage());
        return false;
    }
}

function enviarEmailNativo($email, $asunto, $cuerpo, $adjunto, $numeroFactura)
{
    // Para el método nativo, necesitamos guardar el archivo temporalmente
    $tempFile = sys_get_temp_dir() . "/factura_{$numeroFactura}_" . time() . ".pdf";
    file_put_contents($tempFile, $adjunto);

    // Headers para email HTML con adjunto
    $boundary = md5(time());
    $headers = [
        'From: facturas@empresa.com',
        'Reply-To: facturas@empresa.com',
        'MIME-Version: 1.0',
        "Content-Type: multipart/mixed; boundary=\"{$boundary}\"",
        'X-Mailer: PHP/' . phpversion()
    ];

    // Cuerpo del mensaje
    $message = "--{$boundary}\r\n";
    $message .= "Content-Type: text/html; charset=UTF-8\r\n";
    $message .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
    $message .= $cuerpo . "\r\n\r\n";

    // Adjunto
    if (file_exists($tempFile)) {
        $fileContent = file_get_contents($tempFile);
        $fileContent = chunk_split(base64_encode($fileContent));

        $message .= "--{$boundary}\r\n";
        $message .= "Content-Type: application/pdf; name=\"factura_{$numeroFactura}.pdf\"\r\n";
        $message .= "Content-Transfer-Encoding: base64\r\n";
        $message .= "Content-Disposition: attachment; filename=\"factura_{$numeroFactura}.pdf\"\r\n\r\n";
        $message .= $fileContent . "\r\n\r\n";

        // Eliminar archivo temporal
        unlink($tempFile);
    }

    $message .= "--{$boundary}--";

    // Enviar email
    $enviado = mail($email, $asunto, $message, implode("\r\n", $headers));

    if (!$enviado) {
        error_log("Error al enviar email nativo a: $email");
    }

    return $enviado;
}