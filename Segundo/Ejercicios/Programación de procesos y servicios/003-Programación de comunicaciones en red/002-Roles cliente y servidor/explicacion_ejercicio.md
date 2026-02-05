
### 🧩 1. Encabezado informativo

### 🧠 2. Explicación personal del ejercicio
En este ejercicio tenía que ayudar a Jose Vicente a montar su blog de caza y pesca usando sus propios correos electrónicos como fuente de noticias. Para ello, he creado un sistema en PHP que se conecta a su cuenta mediante el protocolo IMAP.

La idea principal es que el script lea la bandeja de entrada, coja los últimos correos y los "transforme" en entradas de blog HTML, mostrando el texto y también las fotos si las hubiese. He procurado separar los datos de conexión en un archivo aparte (`imap_config.php`) para no tener contraseñas mezcladas con el código lógico. Además, le he dado un estilo visual sencillo pero resultón con CSS para que parezca un blog de verdad.

### 💻 3. Código de programación

**Archivo de configuración: `imap_config.php`**
```php
<?php
// Configuración del servidor IMAP
define('IMAP_SERVER', '{imap.gmail.com:993/imap/ssl}INBOX'); // Ejemplo con Gmail
define('IMAP_USER', 'tu_correo@gmail.com');
define('IMAP_PASS', 'tu_contraseña_de_aplicacion');
?>
```

**Lógica principal: `index.php`**
```php
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
            // Es texto plano o HTML
            $body = imap_fetchbody($inbox, $emailNumber, $partNumber);
             if ($part->encoding == 3) $body = base64_decode($body);
            elseif ($part->encoding == 4) $body = quoted_printable_decode($body);
            
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
<!-- HTML de visualización (incluido en el mismo archivo para simplificar la entrega) -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
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
            </div>
            <!-- Mock visual -->
             <div class="article-card">
                <div class="article-header">
                    <h2 class="article-title">Ejemplo: Gran día de pesca (Mock)</h2>
                    <span class="article-meta">21 Oct 2025 | De: Jose Vicente</span>
                </div>
                <div class="article-content">
                    <p>Este es un ejemplo de cómo se vería una entrada. Configura imap_config.php con tus datos reales.</p>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($emails as $email): ?>
                <article class="article-card">
                    <div class="article-header">
                        <h2 class="article-title"><?php echo htmlspecialchars($email['subject']); ?></h2>
                        <span class="article-meta"><?php echo htmlspecialchars($email['date']); ?> | <?php echo htmlspecialchars($email['from']); ?></span>
                    </div>
                    <div class="article-content">
                        <div class="article-body"><?php echo $email['body']; ?></div>
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
```

**Estilos CSS: `estilo.css`**
```css
:root {
    --primary-color: #2c3e50;
    --secondary-color: #556b2f;
    --accent-color: #3498db;
    --bg-color: #f4f7f6;
    --card-bg: #ffffff;
}
body { font-family: 'Segoe UI', sans-serif; background-color: var(--bg-color); color: #333; margin: 0; padding: 20px; }
header { text-align: center; margin-bottom: 40px; }
h1.blog-title { color: var(--secondary-color); font-size: 3em; border-bottom: 3px solid var(--accent-color); display: inline-block; }
.article-card { background: var(--card-bg); border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); margin-bottom: 30px; transition: transform 0.2s; }
.article-card:hover { transform: translateY(-5px); }
.article-header { background-color: var(--secondary-color); color: white; padding: 15px 20px; }
.article-content { padding: 20px; }
.image-gallery img { max-width: 100%; border-radius: 5px; border: 1px solid #ddd; max-height: 300px; }
.error-message { background-color: #ffdddd; color: #a94442; padding: 15px; text-align: center; }
```

### 📊 4. Rúbrica de evaluación cumplida

1.  **Introducción breve y contextualización:**
    *   Explicado el objetivo de leer correos para usarlos como contenido de un CMS personal (Blog).
2.  **Desarrollo detallado y preciso:**
    *   Uso de funciones IMAP (`imap_open`, `imap_search`, `imap_fetchstructure`).
    *   Algoritmo para recorrer correos multipart y extraer imágenes en Base64.
3.  **Aplicación práctica:**
    *   Código funcional separado en configuración, lógica y vista.
    *   Manejo de errores con bloques `try-catch`.
4.  **Conclusión breve:**
    *   Resumen del aprendizaje sobre protocolos de correo.

### 🧾 5. Cierre
Me ha parecido un ejercicio muy completo. Al principio me costó entender la estructura de los correos multipart con `imap_fetchstructure`, pero al usar una función recursiva pude sacar tanto el texto como las fotos adjuntas. Es muy útil integrar servicios reales como el correo en aplicaciones web.
