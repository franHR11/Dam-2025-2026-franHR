<?php
/**
 * Blog de Caza y Pesca de Jose Vicente
 * Script para leer correos IMAP y mostrarlos como artículos.
 */

include 'imap_config.php';

// Función para decodificar texto MIME (asuntos, nombres)
function decodeText($text) {
    $elements = imap_mime_header_decode($text);
    $decoded = '';
    foreach ($elements as $element) {
        $decoded .= $element->text;
    }
    return $decoded;
}

// Función auxiliar para obtener el contenido del mensaje y las imágenes
function getEmailContent($inbox, $emailNumber) {
    $structure = imap_fetchstructure($inbox, $emailNumber);
    $content = ['body' => '', 'images' => []];

    if (isset($structure->parts)) {
        // Mensaje multipart
        flattenParts($inbox, $emailNumber, $structure->parts, '', $content);
    } else {
        // Mensaje simple
        $body = imap_fetchbody($inbox, $emailNumber, 1);
        // Decodificar según la codificación
        if ($structure->encoding == 3) $body = base64_decode($body);
        elseif ($structure->encoding == 4) $body = quoted_printable_decode($body);
        $content['body'] = nl2br($body);
    }
    return $content;
}

// Función recursiva para recorrer las partes del mensaje
function flattenParts($inbox, $emailNumber, $parts, $prefix, &$content) {
    foreach ($parts as $section => $part) {
        $partNumber = $prefix . ($section + 1);
        
        // Verificar si es una imagen adjunta o inline
        $isImage = false;
        if ($part->subtype == 'JPEG' || $part->subtype == 'PNG' || $part->subtype == 'GIF' || $part->subtype == 'JPG') {
            $isImage = true;
        }

        if ($isImage) {
            // Es una imagen, la procesamos
            $imageData = imap_fetchbody($inbox, $emailNumber, $partNumber);
            if ($part->encoding == 3) $imageData = base64_decode($imageData);
            elseif ($part->encoding == 4) $imageData = quoted_printable_decode($imageData);
            
            $mimeType = "image/" . strtolower($part->subtype);
            $base64 = base64_encode($imageData);
            $content['images'][] = "data:$mimeType;base64,$base64";
        } elseif ($part->type == 0 && empty($content['body'])) {
            // Es texto plano o HTML y aún no hemos obtenido cuerpo
            // Priorizamos HTML si existe, pero por simplicidad cogemos el primero texto que veamos
            // o mejor, buscamos específicamente PLAIN o HTML.
            // Para este ejercicio simple, tomamos el contenido texto.
            $body = imap_fetchbody($inbox, $emailNumber, $partNumber);
             if ($part->encoding == 3) $body = base64_decode($body);
            elseif ($part->encoding == 4) $body = quoted_printable_decode($body);
            
            // Si es HTML, lo dejamos, si es PLAIN, nl2br
            if ($part->subtype == 'PLAIN') {
                $content['body'] .= nl2br($body);
            } else {
                $content['body'] .= $body;
            }
        }

        // Si tiene subpartes, recursividad
        if (isset($part->parts)) {
            flattenParts($inbox, $emailNumber, $part->parts, $partNumber . '.', $content);
        }
    }
}

$emails = [];
$error = '';

try {
    // 1. Conexión al servidor IMAP
    $inbox = @imap_open(IMAP_SERVER, IMAP_USER, IMAP_PASS);

    if (!$inbox) {
        throw new Exception("No se pudo conectar: " . imap_last_error());
    }

    // 2. Buscar correos (todos)
    $emailsFunc = imap_search($inbox, 'ALL');

    if ($emailsFunc) {
        // 3. Ordenar para obtener los más recientes (descendente)
        rsort($emailsFunc);

        // Limitar a los últimos 10
        $emailsFunc = array_slice($emailsFunc, 0, 10);

        foreach ($emailsFunc as $emailNumber) {
            // Obtener cabecera
            $overview = imap_fetch_overview($inbox, $emailNumber, 0);
            $header = $overview[0];

            // Obtener contenido
            $emailContent = getEmailContent($inbox, $emailNumber);

            $emails[] = [
                'subject' => decodeText($header->subject),
                'from' => decodeText($header->from),
                'date' => $header->date,
                'body' => $emailContent['body'],
                'images' => $emailContent['images']
            ];
        }
    }

    imap_close($inbox);

} catch (Exception $e) {
    $error = $e->getMessage();
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog de Pesca y Caza - Jose Vicente</title>
    <link rel="stylesheet" href="estilo.css">
</head>
<body>
    <header>
        <h1 class="blog-title">Aventuras de Caza y Pesca</h1>
        <div class="subtitle">El blog personal de Jose Vicente Carratalá</div>
    </header>

    <div class="container">
        <?php if ($error): ?>
            <div class="error-message">
                <h3>Error de Conexión</h3>
                <p><?php echo htmlspecialchars($error); ?></p>
                <p><small>Por favor verifica verifica el archivo <code>imap_config.php</code>.</small></p>
            </div>
            <!-- Mock visual para que el usuario vea cómo quedaría si no tiene credenciales reales -->
             <div class="article-card">
                <div class="article-header">
                    <h2 class="article-title">Ejemplo: Gran día de pesca en el río (Mock)</h2>
                    <span class="article-meta">21 Oct 2025 | De: Jose Vicente</span>
                </div>
                <div class="article-content">
                    <div class="article-body">
                        <p>Este es un ejemplo de cómo se vería una entrada si tuvieras conexión al servidor de correo. Configura tus credenciales reales para ver tus correos aquí.</p>
                        <p>Hoy ha sido un día fantástico en el río. Hemos capturado varias truchas hermosas.</p>
                    </div>
                </div>
            </div>
        <?php elseif (empty($emails)): ?>
            <div class="no-emails">
                <p>No se encontraron correos o la bandeja de entrada está vacía.</p>
            </div>
        <?php else: ?>
            <?php foreach ($emails as $email): ?>
                <article class="article-card">
                    <div class="article-header">
                        <h2 class="article-title"><?php echo htmlspecialchars($email['subject']); ?></h2>
                        <span class="article-meta"><?php echo htmlspecialchars($email['date']); ?> | <?php echo htmlspecialchars($email['from']); ?></span>
                    </div>
                    <div class="article-content">
                        <div class="article-body">
                            <?php echo $email['body']; // Permitimos HTML básico del correo ?>
                        </div>
                        
                        <?php if (!empty($email['images'])): ?>
                            <div class="attachments">
                                <h3>Fotos adjuntas:</h3>
                                <div class="image-gallery">
                                    <?php foreach ($email['images'] as $imgSrc): ?>
                                        <img src="<?php echo $imgSrc; ?>" alt="Imagen adjunta">
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>
