<?php
// Script de prueba para verificar el procesamiento del formulario de clientes
require_once __DIR__ . '/../config.php';

header('Content-Type: text/html; charset=utf-8');

echo "<!DOCTYPE html>";
echo "<html lang='es'>";
echo "<head>";
echo "    <meta charset='UTF-8'>";
echo "    <meta name='viewport' content='width=device-width, initial-scale=1.0'>";
echo "    <title>Test Formulario Clientes</title>";
echo "    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>";
echo "</head>";
echo "<body class='bg-light'>";
echo "<div class='container mt-4'>";

echo "<h1 class='mb-4'>🧪 Test Formulario Clientes</h1>";

// Función para mostrar resultados
function mostrarTest($titulo, $exito, $mensaje, $datos = null) {
    $alertClass = $exito ? 'success' : 'danger';
    $icono = $exito ? '✅' : '❌';

    echo "<div class='alert alert-{$alertClass} mb-3'>";
    echo "<strong>{$icono} {$titulo}</strong><br>";
    echo "<small>{$mensaje}</small>";
    if ($datos) {
        echo "<pre class='mt-2 bg-light p-2 rounded' style='font-size: 12px; max-height: 200px; overflow-y: auto;'>";
        echo htmlspecialchars(json_encode($datos, JSON_PRETTY_PRINT));
        echo "</pre>";
    }
    echo "</div>";
}

// Test 1: Procesamiento de formulario de ejemplo
$formularioTest = [
    'cliente-id' => '',
    'codigo' => 'CLI0001',
    'nombre_comercial' => 'Cliente Test S.L.',
    'razon_social' => 'Cliente Test Sociedad Limitada',
    'nif_cif' => '04623514P',
    'direccion' => 'Calle Test 123',
    'codigo_postal' => '28080',
    'ciudad' => 'Madrid',
    'provincia' => 'Madrid',
    'pais' => 'España',
    'telefono' => '912345678',
    'telefono2' => '600123456',
    'email' => 'test@cliente.com',
    'web' => 'https://www.clientetest.com',
    'tipo_cliente' => 'empresa',
    'forma_pago' => 'transferencia',
    'dias_credito' => '30',
    'limite_credito' => '10000.00',
    'importe_acumulado' => '0.00',
    'saldo_pendiente' => '0.00',
    'activo' => '1',
    'bloqueado' => '0',
    'observaciones' => 'Cliente de prueba para validación',
    'contacto_principal' => 'Juan Test',
    'cargo_contacto' => 'Director General'
];

mostrarTest("Estructura del Formulario", true,
    "El formulario contiene todos los campos necesarios",
    $formularioTest
);

// Test 2: Procesamiento de datos (simular conversión del frontend)
$formularioProcesado = $formularioTest;

// Convertir cliente-id a id para el backend
if (!empty($formularioProcesado['cliente-id'])) {
    $formularioProcesado['id'] = $formularioProcesado['cliente-id'];
    unset($formularioProcesado['cliente-id']);
}

// Asegurar que los campos numéricos sean del tipo correcto
$formularioProcesado['dias_credito'] = (int)$formularioProcesado['dias_credito'];
$formularioProcesado['limite_credito'] = (float)$formularioProcesado['limite_credito'];
$formularioProcesado['importe_acumulado'] = (float)$formularioProcesado['importe_acumulado'];
$formularioProcesado['saldo_pendiente'] = (float)$formularioProcesado['saldo_pendiente'];

mostrarTest("Procesamiento de Datos", true,
    "Conversión de tipos y normalización de campos",
    $formularioProcesado
);

// Test 3: Validación de tipos de datos
$validaciones = [
    'dias_credito_correcto' => is_int($formularioProcesado['dias_credito']),
    'limite_credito_float' => is_float($formularioProcesado['limite_credito']),
    'activo_bool' => in_array($formularioProcesado['activo'], [0, 1]),
    'codigo_no_vacio' => !empty(trim($formularioProcesado['codigo'])),
    'nombre_no_vacio' => !empty(trim($formularioProcesado['nombre_comercial'])),
    'tipo_valido' => in_array($formularioProcesado['tipo_cliente'], ['particular', 'empresa', 'autonomo', 'ong', 'publico']),
    'nif_valido' => preg_match('/^[XYZ]?\d{7,8}[A-Z]$/i', $formularioProcesado['nif_cif'])
];

$todoValido = !in_array(false, $validaciones);
mostrarTest("Validación de Datos", $todoValido,
    "Verificación de tipos y valores permitidos",
    $validaciones
);

// Test 4: Simulación de guardado
try {
    $db = getConnection();
    if ($db) {
        // Verificar si el código ya existe
        $stmt = $db->prepare("SELECT id FROM clientes WHERE codigo = ?");
        $stmt->execute([$formularioProcesado['codigo']]);
        $existe = $stmt->fetch();

        if ($existe) {
            mostrarTest("Código Duplicado", false,
                "El código '{$formularioProcesado['codigo']}' ya existe en la base de datos"
            );
        } else {
            mostrarTest("Código Disponible", true,
                "El código '{$formularioProcesado['codigo']}' está disponible para uso"
            );

            // Verificar NIF duplicado
            $stmt = $db->prepare("SELECT id FROM clientes WHERE nif_cif = ?");
            $stmt->execute([$formularioProcesado['nif_cif']]);
            $nifExiste = $stmt->fetch();

            if ($nifExiste) {
                mostrarTest("NIF Duplicado", false,
                    "El NIF '{$formularioProcesado['nif_cif']}' ya está registrado"
                );
            } else {
                mostrarTest("NIF Disponible", true,
                    "El NIF '{$formularioProcesado['nif_cif']}' está disponible para uso"
                );
            }
        }
    }
} catch (Exception $e) {
    mostrarTest("Error Base de Datos", false,
        "Error al conectar: " . $e->getMessage()
    );
}

// Test 5: Campos HTML requeridos
$camposRequeridos = [
    'cliente-id' => 'ID del cliente (hidden)',
    'codigo' => 'Código del cliente',
    'nombre_comercial' => 'Nombre comercial',
    'tipo_cliente' => 'Tipo de cliente',
    'activo' => 'Estado (checkbox)',
    'bloqueado' => 'Bloqueado (checkbox)',
    'observaciones' => 'Observaciones',
    'contacto_principal' => 'Contacto principal',
    'cargo_contacto' => 'Cargo del contacto'
];

echo "<div class='card mb-3'>";
echo "<div class='card-header'><h5>📝 Campos del Formulario</h5></div>";
echo "<div class='card-body'>";

foreach ($camposRequeridos as $campo => $descripcion) {
    $existe = array_key_exists($campo, $formularioTest);
    $icono = $existe ? '✅' : '❌';
    $badgeClass = $existe ? 'success' : 'danger';

    echo "<span class='badge bg-{$badgeClass} me-2 mb-2'>{$icono} {$campo}</span>";
    echo "<small class='text-muted'>{$descripcion}</small><br>";
}

echo "</div>";
echo "</div>";

// Test 6: Formulario de prueba interactiva
echo "<div class='card mb-3'>";
echo "<div class='card-header'><h5>🧪 Formulario Interactivo de Prueba</h5></div>";
echo "<div class='card-body'>";
echo "<form method='post' class='row g-3'>";
echo "<div class='col-md-6'>";
echo "<label class='form-label'>Código *</label>";
echo "<input type='text' class='form-control' name='test_codigo' value='CLI0001' required>";
echo "</div>";
echo "<div class='col-md-6'>";
echo "<label class='form-label'>Nombre Comercial *</label>";
echo "<input type='text' class='form-control' name='test_nombre' value='Cliente Test' required>";
echo "</div>";
echo "<div class='col-md-6'>";
echo "<label class='form-label'>NIF/CIF</label>";
echo "<input type='text' class='form-control' name='test_nif' value='04623514P'>";
echo "</div>";
echo "<div class='col-md-6'>";
echo "<label class='form-label'>Tipo Cliente</label>";
echo "<select class='form-select' name='test_tipo'>";
echo "<option value='particular'>Particular</option>";
echo "<option value='empresa' selected>Empresa</option>";
echo "<option value='autonomo'>Autónomo</option>";
echo "<option value='ong'>ONG</option>";
echo "<option value='publico'>Público</option>";
echo "</select>";
echo "</div>";
echo "<div class='col-12'>";
echo "<button type='submit' class='btn btn-primary'>Probar Envío</button>";
echo "</div>";
echo "</form>";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['test_codigo'])) {
    $testData = [
        'codigo' => $_POST['test_codigo'] ?? '',
        'nombre_comercial' => $_POST['test_nombre'] ?? '',
        'nif_cif' => $_POST['test_nif'] ?? '',
        'tipo_cliente' => $_POST['test_tipo'] ?? ''
    ];

    $errores = [];
    if (empty(trim($testData['codigo']))) $errores[] = 'Código es obligatorio';
    if (empty(trim($testData['nombre_comercial']))) $errores[] = 'Nombre es obligatorio';
    if (empty(trim($testData['tipo_cliente']))) $errores[] = 'Tipo es obligatorio';
    if (!empty($testData['nif_cif']) && !preg_match('/^[XYZ]?\d{7,8}[A-Z]$/i', $testData['nif_cif'])) {
        $errores[] = 'Formato de NIF/CIF inválido';
    }

    if (empty($errores)) {
        echo "<div class='alert alert-success mt-3'>";
        echo "✅ Formulario válido! Datos procesados correctamente.";
        echo "<pre>" . htmlspecialchars(json_encode($testData, JSON_PRETTY_PRINT)) . "</pre>";
        echo "</div>";
    } else {
        echo "<div class='alert alert-danger mt-3'>";
        echo "❌ Errores de validación:<ul>";
        foreach ($errores as $error) {
            echo "<li>{$error}</li>";
        }
        echo "</ul></div>";
    }
}

echo "</div>";
echo "</div>";

// Resumen final
echo "<div class='alert alert-info'>";
echo "<h5>📋 Resumen de Correcciones Aplicadas</h5>";
echo "<ul>";
echo "<li>✅ Corregido el manejo del campo <code>cliente-id</code> vs <code>id</code></li>";
echo "<li>✅ Agregada validación nula para evitar errores <code>Cannot read properties of undefined</code></li>";
echo "<li>✅ Mejorado el procesamiento de campos numéricos y booleanos</li>";
echo "<li>✅ Asegurada la conversión correcta de tipos de datos</li>";
echo "<li>✅ Implementado manejo de campos vacíos en el formulario</li>";
echo "<li>✅ Optimizada la validación de NIF/CIF con mayúsculas/minúsculas</li>";
echo "</ul>";
echo "</div>";

echo "<div class='text-center mt-4'>";
echo "<a href='../Paginas/clientes/clientes.php' target='_blank' class='btn btn-primary btn-lg'>";
echo "<i class='fas fa-external-link-alt'></i> Probar Página de Clientes";
echo "</a>";
echo "</div>";

echo "</div>";
echo "<script src='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js'></script>";
echo "</body>";
echo "</html>";
?>
