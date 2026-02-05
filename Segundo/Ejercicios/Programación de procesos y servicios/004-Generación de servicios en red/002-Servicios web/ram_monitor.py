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
