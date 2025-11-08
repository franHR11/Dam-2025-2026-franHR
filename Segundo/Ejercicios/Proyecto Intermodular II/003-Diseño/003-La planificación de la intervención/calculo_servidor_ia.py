# Variables para las características del servidor
num_nucleos_cpu = 6
memoria_ram_gb = 32
precio_mensual = 60

# Cálculo de unidades necesarias y costos
unidades_necesarias = 3
costo_total_anual = precio_mensual * 12 * unidades_necesarias

# Mostrar resultados
print(f"Núcleos de CPU por servidor: {num_nucleos_cpu}")
print(f"Memoria RAM por servidor: {memoria_ram_gb} GB")
print(f"Precio mensual por servidor: {precio_mensual}€")
print(f"Unidades necesarias: {unidades_necesarias}")
print(f"Costo total anual: {costo_total_anual}€")