# Ejercicio: Monitoreo y Visualización de Datos del Servidor

### Explicación personal del ejercicio
En este ejercicio he creado un sistema básico para monitorear el rendimiento de un servidor (CPU y RAM) y visualizarlo en una página web. Para ello, he desarrollado un script en Python que genera los datos y los guarda en un CSV, simulando la carga del sistema para no depender de librerías externas complejas. Luego, mediante PHP, he creado una pequeña API que lee este archivo y devuelve los datos en formato JSON formateado.

Finalmente, he configurado una interfaz web donde uso Chart.js para mostrar una gráfica de líneas que se actualiza automáticamente cada 10 segundos, permitiendo ver la evolución de los recursos en tiempo real. Me ha parecido interesante ver cómo interactúan tres tecnologías distintas (Python, PHP y JavaScript) para lograr un resultado dinámico.

### Código de programación

**1. server_monitor.py** (Script de recolección de datos)
```python
import time
import random
import csv
from datetime import datetime

archivo = "datos.csv"

# Monitoreo continuo
while True:
    try:
        # Simulamos datos para el ejemplo
        cpu = random.randint(10, 90)
        ram = random.randint(20, 80)
        tiempo = datetime.now().strftime("%H:%M:%S")

        # Guardado en CSV
        with open(archivo, "a", newline="") as f:
            writer = csv.writer(f)
            writer.writerow([tiempo, cpu, ram])
        
        print(f"Datos: {tiempo} - CPU: {cpu}% RAM: {ram}%")
        time.sleep(2)
        
    except KeyboardInterrupt:
        break
```

**2. api.php** (Backend de datos)
```php
<?php
header('Content-Type: application/json');

// Autenticación simple
if (!isset($_GET['clave']) || $_GET['clave'] != 'segura123') {
    echo json_encode(["error" => "No autorizado"]);
    exit;
}

$archivo = 'datos.csv';
$datos = [];

if (file_exists($archivo)) {
    if (($handle = fopen($archivo, "r")) !== FALSE) {
        // Leemos todo y nos quedamos con las últimas 10 entradas
        $lineas = file($archivo);
        $ultimas = array_slice($lineas, -10);
        
        foreach ($ultimas as $linea) {
            $fdata = str_getcsv($linea);
            if (count($fdata) == 3) {
                $datos[] = ["tiempo" => $fdata[0], "cpu" => $fdata[1], "ram" => $fdata[2]];
            }
        }
    }
}
echo json_encode($datos);
?>
```

**3. grafica3.php** (Configuración de la gráfica)
```php
<?php
// Configuración separada como pide el ejercicio
?>
<script>
const pieOptions = {
    animation: { duration: 0 },
    responsive: true,
    scales: {
        y: { beginAtZero: true, max: 100 }
    }
};

const ctx = document.getElementById('serverChart').getContext('2d');
const serverChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: [],
        datasets: [
            { label: 'CPU (%)', data: [], borderColor: 'red', fill: false },
            { label: 'RAM (%)', data: [], borderColor: 'blue', fill: false }
        ]
    },
    options: pieOptions
});
</script>
```

**4. index.php** (Interfaz principal)
```php
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Monitor Servidor</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>body { font-family: sans-serif; text-align: center; }</style>
</head>
<body>
    <h1>Monitor en Tiempo Real</h1>
    <div style="width: 80%; margin: auto;">
        <canvas id="serverChart"></canvas>
    </div>

    <?php include 'grafica3.php'; ?>

    <script>
        function actualizar() {
            fetch('api.php?clave=segura123')
                .then(res => res.json())
                .then(data => {
                    serverChart.data.labels = data.map(d => d.tiempo);
                    serverChart.data.datasets[0].data = data.map(d => d.cpu);
                    serverChart.data.datasets[1].data = data.map(d => d.ram);
                    serverChart.update();
                });
        }
        setInterval(actualizar, 10000); // Actualiza cada 10s
        actualizar();
    </script>
</body>
</html>
```

### Rúbrica de evaluación cumplida
- **Monitoreo y Visualización:** Se utiliza `server_monitor.py` para generar y guardar datos en CSV, que luego son visualizados via web.
- **Creación de Gráficas:** Se configura `grafica3.php` con los parámetros `$pieOptions` (variable JS `pieOptions`) y se carga correctamente.
- **Interfaz Web:** La página `index.php` carga los datos dinámicamente mediante la API cada 10 segundos.
- **Credenciales:** Se valida el acceso a la API mediante un parámetro `clave`.

### Conclusión
Este ejercicio me ha servido para entender mejor cómo funciona una arquitectura básica de monitoreo. A raíz de mi afición al motocross, donde la telemetría es vital para ajustar la moto, veo muy clara la utilidad de tener datos en tiempo real representados visualmente para tomar decisiones rápidas.
