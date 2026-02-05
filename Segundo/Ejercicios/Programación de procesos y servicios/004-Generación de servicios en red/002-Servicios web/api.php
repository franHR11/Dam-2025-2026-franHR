<?php
// Configuración de usuario para la API (Autenticación Básica)
$auth_user = "admin";
$auth_pass = "admin123";

// Verificar credenciales
if (!isset($_SERVER['PHP_AUTH_USER']) || $_SERVER['PHP_AUTH_USER'] != $auth_user || $_SERVER['PHP_AUTH_PW'] != $auth_pass) {
    header('WWW-Authenticate: Basic realm="Mi Servidor Monitor"');
    header('HTTP/1.0 401 Unauthorized');
    echo 'Error: Acceso denegado. Credenciales incorrectas.';
    exit;
}

// Función simple para leer el último dato del CSV
function obtener_ultimo_dato($archivo) {
    if (!file_exists($archivo)) {
        return ["error" => "Archivo de datos no encontrado ($archivo)"];
    }
    
    $filas = array();
    if (($handle = fopen($archivo, "r")) !== FALSE) {
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $filas[] = $data;
        }
        fclose($handle);
    }
    
    // Devolvemos la última fila (asumiendo que tiene datos)
    // La fila 0 es cabecera, así que necesitamos al menos 2 filas
    if (count($filas) > 1) {
        $cabecera = $filas[0];
        $ultimo = end($filas);
        
        // Combinar cabecera con valores
        return array_combine($cabecera, $ultimo);
    }
    
    return ["mensaje" => "No hay datos suficientes aún"];
}

// Determinar qué recurso se pide simple (ram, disco, cpu)
// Usamos un parámetro GET 'recurso'
header('Content-Type: application/json');

$recurso = isset($_GET['recurso']) ? $_GET['recurso'] : '';

switch ($recurso) {
    case 'ram':
        $datos = obtener_ultimo_dato('ram_usage_history.csv');
        echo json_encode(["recurso" => "RAM", "datos" => $datos]);
        break;
        
    case 'disco':
        $datos = obtener_ultimo_dato('disk_usage_history.csv');
        echo json_encode(["recurso" => "Disco", "datos" => $datos]);
        break;
        
    case 'cpu':
        // Extra: Como los scripts de py solo guardan RAM y Disco, simulo respuesta o aviso
        // En un caso real añadiríamos un cpu_monitor.py
        echo json_encode(["recurso" => "CPU", "mensaje" => "Monitor de CPU no configurado en los scripts actuales"]);
        break;
        
    default:
        // Si no especifica, devolver todo lo disponible
        echo json_encode([
            "estado" => "online",
            "mensaje" => "Usa ?recurso=ram o ?recurso=disco para ver detalles",
            "ram_ultima" => obtener_ultimo_dato('ram_usage_history.csv'),
            "disco_ultimo" => obtener_ultimo_dato('disk_usage_history.csv')
        ]);
        break;
}
?>
