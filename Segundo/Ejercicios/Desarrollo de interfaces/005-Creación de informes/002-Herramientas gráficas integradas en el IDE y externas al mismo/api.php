<?php
// Cabecera para indicar que devolvemos JSON
header('Content-Type: application/json');

// Validación de seguridad simple
if (!isset($_GET['clave']) || $_GET['clave'] != 'segura123') {
    echo json_encode(["error" => "No autorizado"]);
    exit;
}

$archivo = 'datos.csv';
$datos = [];

// Leemos el archivo CSV si existe
if (file_exists($archivo)) {
    if (($handle = fopen($archivo, "r")) !== FALSE) {
        // Leemos solo las últimas 10 líneas para la gráfica
        $lineas = file($archivo);
        $ultimas = array_slice($lineas, -10);
        
        foreach ($ultimas as $linea) {
            $csvData = str_getcsv($linea);
            if (count($csvData) == 3) {
                $datos[] = [
                    "tiempo" => $csvData[0],
                    "cpu" => $csvData[1],
                    "ram" => $csvData[2]
                ];
            }
        }
    }
}

echo json_encode($datos);
?>
