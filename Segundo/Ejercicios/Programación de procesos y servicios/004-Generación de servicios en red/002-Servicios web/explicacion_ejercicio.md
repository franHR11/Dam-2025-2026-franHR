
### 1. Encabezado informativo
**Ejercicio:** Monitorización de Servidor con Python y API PHP  
**Asignatura:** Programación de procesos y servicios  
**Tema:** Generación de servicios en red  

### 2. Explicación personal del ejercicio
En este ejercicio he creado un pequeño sistema para vigilar la salud de mi servidor. La idea era registrar cuánta memoria RAM y espacio en disco se está usando en cada momento. Para ello, he escrito dos scripts en Python que usan la librería `psutil` para sacar los datos y los guardan en archivos CSV sencillos. Luego, para poder consultar estos datos desde cualquier sitio, hice una API en PHP muy simple. Esta API está protegida con contraseña (autenticación básica) y lee los CSV para devolverme la última lectura en formato JSON.

### 3. Código de programación

**ram_monitor.py** (Monitor de RAM)
```python
import psutil
import csv
import os
from datetime import datetime

# Nombre del archivo CSV
archivo_csv = 'ram_usage_history.csv'

# Obtener datos de la RAM
memoria = psutil.virtual_memory()

# Datos a guardar: Fecha, Porcentaje usado, Total (en GB)
fecha_actual = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
porcentaje_ram = memoria.percent
total_ram_gb = round(memoria.total / (1024 ** 3), 2)

# Verificar si el archivo existe para escribir la cabecera
archivo_existe = os.path.isfile(archivo_csv)

with open(archivo_csv, mode='a', newline='') as file:
    writer = csv.writer(file)
    
    # Escribir cabecera si es nuevo
    if not archivo_existe:
        writer.writerow(['Fecha', 'Porcentaje_Uso', 'Total_GB'])
        
    # Escribir los datos
    writer.writerow([fecha_actual, porcentaje_ram, total_ram_gb])

print(f"Datos de RAM guardados: {porcentaje_ram}% de {total_ram_gb}GB")
```

**disk_monitor.py** (Monitor de Disco)
```python
import psutil
import csv
import os
from datetime import datetime

# Nombre del archivo CSV
archivo_csv = 'disk_usage_history.csv'

# Obtener datos del disco (Directorio actual)
disco = psutil.disk_usage('.')

# Datos a guardar
fecha_actual = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
porcentaje_disco = disco.percent
total_disco_gb = round(disco.total / (1024 ** 3), 2)
libre_disco_gb = round(disco.free / (1024 ** 3), 2)

# Verificar si el archivo existe
archivo_existe = os.path.isfile(archivo_csv)

with open(archivo_csv, mode='a', newline='') as file:
    writer = csv.writer(file)
    
    if not archivo_existe:
        writer.writerow(['Fecha', 'Porcentaje_Uso', 'Total_GB', 'Libre_GB'])
        
    writer.writerow([fecha_actual, porcentaje_disco, total_disco_gb, libre_disco_gb])

print(f"Datos de Disco guardados: {porcentaje_disco}% usado de {total_disco_gb}GB")
```

**api.php** (API de consulta)
```php
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

// Bisca el último dato del CSV
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
    
    if (count($filas) > 1) {
        return array_combine($filas[0], end($filas));
    }
    
    return ["mensaje" => "No hay datos suficientes aún"];
}

header('Content-Type: application/json');
$recurso = isset($_GET['recurso']) ? $_GET['recurso'] : '';

if ($recurso === 'ram') {
    echo json_encode(["recurso" => "RAM", "datos" => obtener_ultimo_dato('ram_usage_history.csv')]);
} elseif ($recurso === 'disco') {
    echo json_encode(["recurso" => "Disco", "datos" => obtener_ultimo_dato('disk_usage_history.csv')]);
} else {
    echo json_encode(["mensaje" => "Bienvenido. Usa ?recurso=ram o ?recurso=disco"]);
}
?>
```

### 4. Rúbrica de evaluación cumplida
- **Introducción:** He dejado claro que el objetivo es monitorizar recursos vitales del servidor (RAM, Disco) usando scripts automatizados.
- **Desarrollo técnico:** He usado `psutil` correctamente para la extracción de datos y he implementado una API REST simple en PHP con autenticación básica.
- **Aplicación práctica:** Los scripts generan archivos reales (`.csv`) y la API permite consultarlos remotamente, simulando un entorno de monitorización real.
- **Cierre:** Este ejercicio conecta la gestión del sistema operativo (procesos y servicios) con la web, mostrando cómo exponer información del sistema de forma segura.

### 5. Cierre
Me ha gustado ver cómo con pocas líneas de Python se puede sacar tanta información del PC. Lo de conectar Python con PHP a través de archivos CSV es una solución sencilla pero efectiva para aprender cómo interactúan diferentes lenguajes en un mismo servidor.
