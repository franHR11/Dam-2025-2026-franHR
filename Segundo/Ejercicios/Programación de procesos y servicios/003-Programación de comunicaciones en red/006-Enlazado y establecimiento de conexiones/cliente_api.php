<?php
// Imagina que esta es la API de pesca a la que queremos consultar
$url = "http://api.pesca-ejemplo.local/v1/especies";

// Credenciales para la autenticación
$usuario = "pescador_pro";
$password = "trucha123";

// Datos que queremos enviar al servidor (filtrar por lago)
$datos_busqueda = [
    'lago' => 'Lago Baikal',
    'profundidad_min' => 20
];

// 1. Conectar con el servidor: Iniciar sesión cURL
$ch = curl_init();

// 2. Configurar la solicitud
// Definimos la URL de destino
curl_setopt($ch, CURLOPT_URL, $url);

// Configuramos para que sea una petición POST
curl_setopt($ch, CURLOPT_POST, true);

// Adjuntamos los datos a enviar
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($datos_busqueda));

// Importante: Queremos que curl_exec devuelva el resultado en vez de imprimirlo
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// 3. Autenticación manual
// Codificamos usuario:password en base64
$credenciales_base64 = base64_encode($usuario . ":" . $password);

// Añadimos el encabezado de autorización HTTP
$cabeceras = [
    "Authorization: Basic " . $credenciales_base64,
    "Content-Type: application/x-www-form-urlencoded" // Buena práctica al enviar post fields
];
curl_setopt($ch, CURLOPT_HTTPHEADER, $cabeceras);

// 4. Enviar la solicitud y obtener la respuesta
echo "Conectando a la API de pesca...\n";
$respuesta = curl_exec($ch);

// Comprobamos si hubo errores en la conexión cURL
if (curl_errno($ch)) {
    echo "Error en cURL: " . curl_error($ch);
} else {
    // 5. Procesar la respuesta
    // Obtenemos el código de respuesta HTTP para verificar si fue exitoso (ej. 200 OK)
    $codigo_http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    if ($codigo_http == 200) {
        // Decodificamos el JSON recibido
        $datos_peces = json_decode($respuesta, true);
        
        if ($datos_peces === null) {
            echo "Error: La respuesta no es un JSON válido.\n";
            echo "Respuesta cruda: " . $respuesta;
        } else {
            echo "¡Conexión exitosa! Especies encontradas:\n";
            print_r($datos_peces);
        }
    } else {
        echo "La API respondió con un error HTTP: " . $codigo_http . "\n";
        // En un caso real, aquí veríamos por qué falló (401, 404, 500...)
    }
}

// Cerrar la sesión cURL
curl_close($ch);
?>
