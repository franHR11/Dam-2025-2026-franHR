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
