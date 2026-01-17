import time
import random
import csv
from datetime import datetime

# Archivo donde guardaremos los datos
archivo = "datos.csv"

# Bucle infinito para monitorear cada 2 segundos
while True:
    try:
        # Simulamos datos de CPU y RAM (para no depender de librerías externas como psutil)
        cpu = random.randint(10, 90)
        ram = random.randint(20, 80)
        tiempo = datetime.now().strftime("%H:%M:%S")

        # Guardamos en el CSV
        with open(archivo, "a", newline="") as f:
            writer = csv.writer(f)
            writer.writerow([tiempo, cpu, ram])
        
        print(f"Datos guardados: {tiempo} - CPU: {cpu}% - RAM: {ram}%")
        
        # Esperamos 2 segundos
        time.sleep(2)
        
    except KeyboardInterrupt:
        print("\nMonitoreo detenido.")
        break
