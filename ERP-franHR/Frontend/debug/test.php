<?php
// Script de depuración para identificar el problema
session_start();

// Debug: Mostrar variables globales
echo "<h1>DEBUG: Estado Actual del Sistema</h1>";

echo "<h2>1. Variables de Sesión:</h2>";
echo "<pre>";
var_dump($_SESSION);
echo "</pre>";

echo "<h2>2. Variables Globales POST/GET:</h2>";
echo "<pre>";
echo "POST: ";
var_dump($_POST);
echo "GET: ";
var_dump($_GET);
echo "</pre>";

echo "<h2>3. Headers de la Petición:</h2>";
echo "<pre>";
foreach (getallheaders() as $name => $value) {
    echo "$name: $value\n";
}
echo "</pre>";

echo "<h2>4. Comprobar si ModulosManager existe:</h2>";
echo "<script>";
echo "console.log('Verificando ModulosManager:', typeof ModulosManager);";
echo "console.log('Verificando modulosManager:', typeof modulosManager);";
echo "console.log('Verificando window.ModulosManager:', typeof window.ModulosManager);";
echo "console.log('Verificando window.modulosManager:', typeof window.modulosManager);";
echo "</script>";

echo "<h2>5. Scripts cargados:</h2>";
echo "<script>";
echo "console.log('Scripts en la página:');";
echo "console.log(document.scripts);";
echo "</script>";

echo "<h2>6. Funciones disponibles:</h2>";
echo "<script>";
echo "console.log('Función gestionarModulo:', typeof gestionarModulo);";
echo "console.log('Función window.gestionarModulo:', typeof window.gestionarModulo);";
echo "console.log('Clase ModulosManager:', typeof ModulosManager);";
echo "</script>";

echo "<h2>7. Botones del DOM:</h2>";
echo "<script>";
echo "document.addEventListener('DOMContentLoaded', function() {";
echo "    console.log('DOM cargado, buscando botones...');";
echo "    const botones = document.querySelectorAll('button[onclick]');";
echo "    console.log('Botones encontrados:', botones.length);";
echo "    botones.forEach((btn, index) => {";
echo "        console.log(\`Botón \${index}:\`, btn.getAttribute('onclick'));";
echo "    });";
echo "});";
echo "</script>";

echo "<h2>8. Botón de prueba:</h2>";
echo "<button onclick='testDebug()'>Probar función JavaScript</button>";

echo "<script>";
echo "function testDebug() {";
echo "    console.log('=== INICIO DE PRUEBA ===');";
echo "    console.log('typeof gestionarModulo:', typeof gestionarModulo);";
echo "    console.log('typeof window.gestionarModulo:', typeof window.gestionarModulo);";
echo "    ";
echo "    if (typeof gestionarModulo === 'function') {";
echo "        console.log('✓ gestionarModulo existe como función global');";
echo "        try {";
echo "            gestionarModulo(4, 'desactivar');";
echo "        } catch (error) {";
echo "            console.error('Error llamando a gestionarModulo:', error);";
echo "        }";
echo "    } else if (typeof window.gestionarModulo === 'function') {";
echo "        console.log('✓ window.gestionarModulo existe');";
echo "        try {";
echo "            window.gestionarModulo(4, 'desactivar');";
echo "        } catch (error) {";
echo "            console.error('Error llamando a window.gestionarModulo:', error);";
echo "        }";
echo "    } else {";
echo "        console.error('❌ Ninguna función gestionarModulo encontrada');";
echo "    }";
echo "    console.log('=== FIN DE PRUEBA ===');";
echo "}";
echo "</script>";

echo "<style>";
echo "body { font-family: monospace; padding: 20px; background: #f5f5f5; }";
echo "h1 { color: #e74c3c; }";
echo "h2 { color: #3498db; }";
echo "pre { background: white; padding: 10px; border: 1px solid #ddd; margin: 10px 0; }";
echo "button { padding: 10px 20px; background: #3498db; color: white; border: none; cursor: pointer; }";
echo "button:hover { background: #2980b9; }";
echo "</style>";
?>
