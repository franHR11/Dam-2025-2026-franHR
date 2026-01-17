<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitor del Servidor</title>
    <!-- Incluimos Chart.js desde CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: sans-serif; text-align: center; padding: 20px; }
        .container { width: 80%; margin: 0 auto; }
    </style>
</head>
<body>

    <h1>Monitor de Recursos del Servidor</h1>
    <p>Estado en tiempo real de CPU y RAM</p>

    <div class="container">
        <canvas id="serverChart"></canvas>
    </div>

    <!-- Incluimos la configuración de la gráfica -->
    <?php include 'grafica3.php'; ?>

    <script>
        // Función para actualizar los datos
        function actualizarDatos() {
            // Llamada al endpoint API protegido por clave
            fetch('api.php?clave=segura123')
                .then(response => response.json())
                .then(data => {
                    // Limpiamos datos anteriores para evitar duplicados visuales en este ejemplo simple
                    // O mejor, extraemos arrays
                    const etiquetas = data.map(item => item.tiempo);
                    const datosCpu = data.map(item => item.cpu);
                    const datosRam = data.map(item => item.ram);

                    // Actualizamos la gráfica
                    serverChart.data.labels = etiquetas;
                    serverChart.data.datasets[0].data = datosCpu;
                    serverChart.data.datasets[1].data = datosRam;
                    
                    serverChart.update();
                })
                .catch(error => console.error('Error cargando datos:', error));
        }

        // Actualizar cada 2 segundos (el enunciado pedía 10, pero 2 es mejor para ver cambios live)
        // Corrijo a 10 segundos según el enunciado:
        setInterval(actualizarDatos, 10000); // 10000 ms = 10 segundos
        
        // Primera carga inmediata
        actualizarDatos();
    </script>
</body>
</html>
