<?php
// 007-maestro.php

// Contenido específico de esta página que se inyectará en el layout
$contenido_central = "
    <h1>Panel de Control</h1>
    <p>Bienvenido al sistema de gestión empresarial.</p>
    <p>Seleccione una opción del menú lateral para comenzar a trabajar.</p>
    
    <div style='background: white; padding: 20px; border-radius: 5px; margin-top: 20px;'>
        <h2>Resumen del día</h2>
        <p>No hay alertas pendientes.</p>
    </div>
";

// Incluimos el layout principal
include 'componentes/layout.php';
?>
    </main> <!-- Cierre del main abierto en layout.php -->
</body>
</html>
