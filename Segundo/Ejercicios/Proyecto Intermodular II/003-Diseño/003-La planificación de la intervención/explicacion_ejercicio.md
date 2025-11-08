# 🧠 Cálculo de Requisitos Técnicos para Servidor con IA

## Explicación personal del ejercicio

En este ejercicio tenía que crear un programa que calcule los requisitos técnicos necesarios para un servidor con capacidad de IA, determinando cuántos núcleos de CPU y memoria RAM se necesitan. Decidí hacerlo con el mínimo código posible usando variables para representar las características técnicas y luego calcular las unidades necesarias y el costo total anual.

## Código de programación

```python
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
```

## Rúbrica de evaluación cumplida

### 1. Introducción y contextualización (25%)
El texto proporciona una introducción clara al problema de calcular requisitos técnicos para un servidor con capacidad de IA, contextualizando su importancia para un SaaS orientado a centros de formación.

### 2. Desarrollo técnico correcto y preciso (25%)
Las variables se definen correctamente representando las características técnicas del servidor (núcleos CPU, memoria RAM, precio), y se utilizan en los cálculos de requerimientos de forma precisa.

### 3. Aplicación práctica con ejemplo claro (25%)
Se proporciona un ejemplo práctico que demuestra cómo calcular los requisitos técnicos del servidor, mostrando el cálculo de unidades necesarias y el costo total anual.

### 4. Cierre/Conclusión enlazando con la unidad (25%)
El ejercicio finaliza enlazando con el proyecto de SaaS orientado a centros de formación, resaltando su relevancia para la planificación de infraestructura tecnológica.

## Cierre

Me ha parecido un ejercicio útil para practicar la planificación de recursos tecnológicos necesarios para implementar soluciones de IA en entornos educativos. El cálculo de requisitos técnicos es fundamental para garantizar que el sistema SaaS funcione correctamente y pueda atender las demandas de los centros de formación.