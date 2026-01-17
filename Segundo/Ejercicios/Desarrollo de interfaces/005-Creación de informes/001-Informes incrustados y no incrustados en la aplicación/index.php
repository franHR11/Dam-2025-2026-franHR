<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informe de Capturas de Pesca 2024</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f2f5;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
            color: #333;
        }
        .container {
            width: 80%;
            max-width: 800px;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        h1 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 20px;
        }
        .controls {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-bottom: 25px;
        }
        button {
            padding: 10px 20px;
            border: none;
            border-radius: 25px;
            background-color: #3498db;
            color: white;
            font-weight: bold;
            cursor: pointer;
            transition: transform 0.2s, background-color 0.2s;
        }
        button:hover {
            background-color: #2980b9;
            transform: translateY(-2px);
        }
        button.active {
            background-color: #1abc9c;
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.2);
        }
        .chart-container {
            position: relative;
            height: 400px;
            width: 100%;
        }
        .chart-label {
            text-align: center;
            font-style: italic;
            margin-top: 15px;
            color: #7f8c8d;
            font-weight: 500;
        }
    </style>
</head>
<body>

    <div class="container">
        <h1>🎣 Mis Capturas de Pesca - Temporada 2024</h1>
        
        <div class="controls">
            <a href="?tipo=bar"><button class="<?php echo (!isset($_GET['tipo']) || $_GET['tipo'] == 'bar') ? 'active' : ''; ?>">📊 Barras</button></a>
            <a href="?tipo=line"><button class="<?php echo (isset($_GET['tipo']) && $_GET['tipo'] == 'line') ? 'active' : ''; ?>">📈 Línea</button></a>
            <a href="?tipo=pie"><button class="<?php echo (isset($_GET['tipo']) && $_GET['tipo'] == 'pie') ? 'active' : ''; ?>">🍰 Pastel</button></a>
        </div>

        <div class="chart-container">
            <canvas id="miGrafica"></canvas>
        </div>

        <?php
            // Definir el tipo de gráfico por defecto o recogerlo de la URL
            $tipoGrafico = isset($_GET['tipo']) ? $_GET['tipo'] : 'bar';
            
            // Datos de pesca (Meses vs Número de peces)
            // Utilizo datos realistas de una temporada de pesca variada
            $meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
            $capturas = [12, 19, 3, 5, 25, 45, 60, 55, 30, 15, 8, 10];
            
            // Configurar etiquetas y tooltips según el tipo
            $mensajeTipo = "";
            switch($tipoGrafico) {
                case 'pie': $mensajeTipo = "Visualización en Gráfico de Pastel: Distribución Proporcional"; break;
                case 'line': $mensajeTipo = "Visualización en Gráfico de Línea: Tendencia Anual"; break;
                default: $mensajeTipo = "Visualización en Gráfico de Barras: Comparativa Mensual"; break;
            }
        ?>

        <div class="chart-label">
            <?php echo $mensajeTipo; ?>
        </div>
    </div>

    <script>
        // Obtener contexto del canvas
        const ctx = document.getElementById('miGrafica').getContext('2d');

        // Datos desde PHP a JS
        const etiquetas = <?php echo json_encode($meses); ?>;
        const datos = <?php echo json_encode($capturas); ?>;
        const tipoGrafico = '<?php echo $tipoGrafico; ?>';

        // Configuración específica de colores para hacerla bonita
        const fondoBarras = [
            'rgba(255, 99, 132, 0.6)', 'rgba(54, 162, 235, 0.6)', 'rgba(255, 206, 86, 0.6)', 
            'rgba(75, 192, 192, 0.6)', 'rgba(153, 102, 255, 0.6)', 'rgba(255, 159, 64, 0.6)',
            'rgba(199, 199, 199, 0.6)', 'rgba(83, 102, 255, 0.6)', 'rgba(40, 159, 64, 0.6)',
            'rgba(210, 99, 132, 0.6)', 'rgba(100, 162, 235, 0.6)', 'rgba(200, 206, 86, 0.6)'
        ];

        new Chart(ctx, {
            type: tipoGrafico,
            data: {
                labels: etiquetas,
                datasets: [{
                    label: 'Número de Capturas',
                    data: datos,
                    backgroundColor: tipoGrafico === 'line' ? 'rgba(54, 162, 235, 0.2)' : fondoBarras,
                    borderColor: tipoGrafico === 'line' ? 'rgba(54, 162, 235, 1)' : fondoBarras.map(c => c.replace('0.6', '1')),
                    borderWidth: 1,
                    fill: tipoGrafico === 'line' // Rellenar área bajo la línea si es gráfico de línea
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: 'Rendimiento de Pesca 2024',
                        font: { size: 18 }
                    },
                    tooltip: {
                        enabled: true,
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += context.parsed.y + ' peces';
                                }
                                return label;
                            },
                            afterLabel: function(context) {
                                return "¡Excelente jornada!"; // Ejemplo de tooltip personalizado extra
                            }
                        }
                    },
                    legend: {
                        display: tipoGrafico !== 'bar', // Ocultar leyenda en barras si es redundante
                        position: 'bottom'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        display: tipoGrafico !== 'pie' // Ocultar ejes en gráfico de pastel
                    },
                    x: {
                        display: tipoGrafico !== 'pie'
                    }
                },
                animation: {
                    duration: 1500,
                    easing: 'easeOutQuart'
                }
            }
        });
    </script>
</body>
</html>
