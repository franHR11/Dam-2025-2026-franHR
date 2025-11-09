<?php
// Script de prueba para validación de NIF/CIF
require_once __DIR__ . '/../config.php';

header('Content-Type: text/html; charset=utf-8');

echo "<!DOCTYPE html>";
echo "<html lang='es'>";
echo "<head>";
echo "    <meta charset='UTF-8'>";
echo "    <meta name='viewport' content='width=device-width, initial-scale=1.0'>";
echo "    <title>Test Validación NIF/CIF</title>";
echo "    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>";
echo "</head>";
echo "<body class='bg-light'>";
echo "<div class='container mt-4'>";

echo "<h1 class='mb-4'>🧪 Test Validación NIF/CIF</h1>";

// Casos de prueba
$testCases = [
    ['nif' => '04623514P', 'tipo' => 'NIF/DNI válido', 'esperado' => true],
    ['nif' => '04623514p', 'tipo' => 'NIF/DNI válido (minúscula)', 'esperado' => true],
    ['nif' => '12345678Z', 'tipo' => 'NIF/DNI válido', 'esperado' => true],
    ['nif' => 'X1234567L', 'tipo' => 'NIE válido', 'esperado' => true],
    ['nif' => 'Y1234567L', 'tipo' => 'NIE válido', 'esperado' => true],
    ['nif' => 'Z1234567L', 'tipo' => 'NIE válido', 'esperado' => true],
    ['nif' => 'B12345674', 'tipo' => 'CIF válido', 'esperado' => true],
    ['nif' => 'A1234567', 'tipo' => 'NIF incompleto', 'esperado' => false],
    ['nif' => '123456789', 'tipo' => 'Solo números', 'esperado' => false],
    ['nif' => 'ABCDEFGHI', 'tipo' => 'Solo letras', 'esperado' => false],
    ['nif' => '04623514A', 'tipo' => 'NIF con letra incorrecta', 'esperado' => false],
    ['nif' => '', 'tipo' => 'Vacío', 'esperado' => true], // Vacío debe ser válido (opcional)
];

echo "<div class='row'>";
echo "<div class='col-md-8'>";
echo "<div class='card'>";
echo "<div class='card-header'><h5>🔍 Casos de Prueba</h5></div>";
echo "<div class='card-body'>";
echo "<table class='table table-striped'>";
echo "<thead><tr><th>NIF/CIF</th><th>Tipo</th><th>Esperado</th><th>Resultado</th><th>Estado</th></tr></thead>";

foreach ($testCases as $case) {
    $resultado = validarNIFCompleto($case['nif']);
    $correcto = $resultado === $case['esperado'];

    $icono = $correcto ? '✅' : '❌';
    $badgeClass = $correcto ? 'success' : 'danger';
    $resultadoTexto = $resultado ? 'Válido' : 'Inválido';

    echo "<tr>";
    echo "<td><code>{$case['nif']}</code></td>";
    echo "<td>{$case['tipo']}</td>";
    echo "<td>" . ($case['esperado'] ? 'Válido' : 'Inválido') . "</td>";
    echo "<td>{$resultadoTexto}</td>";
    echo "<td><span class='badge bg-{$badgeClass}'>{$icono}</span></td>";
    echo "</tr>";
}

echo "</table>";
echo "</div>";
echo "</div>";
echo "</div>";

// Formulario de prueba
echo "<div class='col-md-4'>";
echo "<div class='card'>";
echo "<div class='card-header'><h5>🧪 Prueba Manual</h5></div>";
echo "<div class='card-body'>";
echo "<form method='post'>";
echo "<div class='mb-3'>";
echo "<label for='nif_input' class='form-label'>Introduce NIF/CIF:</label>";
echo "<input type='text' class='form-control' id='nif_input' name='nif_test' value='" . ($_POST['nif_test'] ?? '') . "' placeholder='Ej: 04623514P'>";
echo "</div>";
echo "<button type='submit' class='btn btn-primary'>Validar</button>";
echo "</form>";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nif_test'])) {
    $nifTest = trim($_POST['nif_test']);
    $esValido = validarNIFCompleto($nifTest);
    $alertClass = $esValido ? 'success' : 'danger';
    $mensaje = $esValido ? '✅ VÁLIDO' : '❌ INVÁLIDO';

    echo "<div class='alert alert-{$alertClass} mt-3'>";
    echo "<strong>NIF/CIF: {$nifTest}</strong><br>";
    echo "<strong>Resultado: {$mensaje}</strong>";
    echo "</div>";
}
echo "</div>";
echo "</div>";
echo "</div>";

// Documentación
echo "<div class='row mt-4'>";
echo "<div class='col-12'>";
echo "<div class='card'>";
echo "<div class='card-header'><h5>📋 Formatos Válidos</h5></div>";
echo "<div class='card-body'>";
echo "<div class='row'>";
echo "<div class='col-md-6'>";
echo "<h6>NIF/DNI</h6>";
echo "<ul>";
echo "<li>8 dígitos + letra de control</li>";
echo "<li>Ej: <code>12345678Z</code></li>";
echo "<li>Acepta mayúsculas y minúsculas</li>";
echo "</ul>";

echo "<h6>NIE</h6>";
echo "<ul>";
echo "<li>X/Y/Z + 7 dígitos + letra</li>";
echo "<li>Ej: <code>X1234567L</code>, <code>Y1234567L</code></li>";
echo "</ul>";
echo "</div>";

echo "<div class='col-md-6'>";
echo "<h6>CIF</h6>";
echo "<ul>";
echo "<li>Letra + 7 dígitos + control</li>";
echo "<li>Ej: <code>B12345674</code>, <code>A1234567</code></li>";
echo "<li>Letra inicial: A-H, J-N, P, Q, R, S, U, V, W</li>";
echo "</ul>";

echo "<h6>Casos especiales</h6>";
echo "<ul>";
echo "<li>Campo vacío: considerado válido (opcional)</li>";
echo "<li>Corrige automáticamente minúsculas a mayúsculas</li>";
echo "<li>Valida letra de control en NIF/DNI/NIE</li>";
echo "</ul>";
echo "</div>";
echo "</div>";
echo "</div>";
echo "</div>";
echo "</div>";

// Función de validación completa (simulando la de JavaScript)
function validarNIFCompleto($nif) {
    if (empty(trim($nif))) {
        return true; // Campo vacío es válido (opcional)
    }

    $nifUpper = strtoupper(trim($nif));

    // Validación NIF/DNI español (8 dígitos + letra) o NIE (7 dígitos + letra)
    $nifRegex = '/^[XYZ]?\d{7,8}[A-HJ-NP-TV-Z]$/';

    // Validación CIF español (letra + 7 dígitos + letra/número)
    $cifRegex = '/^[ABCDEFGHJKLMNPQRSUVW]\d{7}[0-9A-J]$/';

    if (preg_match($nifRegex, $nifUpper)) {
        return validarLetraNIF($nifUpper);
    } else if (preg_match($cifRegex, $nifUpper)) {
        return true; // Validación CIF básica
    }

    return false;
}

function validarLetraNIF($nif) {
    $letras = "TRWAGMYFPDXBNJZSQVHLCKE";
    $numero = '';
    $letra = '';

    if (strpos($nif, 'X') === 0 || strpos($nif, 'Y') === 0 || strpos($nif, 'Z') === 0) {
        // NIE
        $numero = substr($nif, 1, 7);
        $letra = substr($nif, 8, 1);
    } else {
        // NIF/DNI
        $numero = substr($nif, 0, 8);
        $letra = substr($nif, 8, 1);
    }

    // Para NIE, X=0, Y=1, Z=2
    if ($nif[0] === 'Y') $numero = '1' . $numero;
    if ($nif[0] === 'Z') $numero = '2' . $numero;

    $resto = $numero % 23;
    return $letra === $letras[$resto];
}

echo "<script src='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js'></script>";
echo "</body>";
echo "</html>";
?>
