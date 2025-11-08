# 🧠 Determinación de Recursos para un Proyecto Informático

## 1. Introducción y contextualización

En este ejercicio tengo que analizar los recursos necesarios para desarrollar un sistema informático que requiere soporte 24 horas al día. Los recursos se dividen en tres categorías principales: materiales (hardware y software), económicos (costos asociados) y humanos (personal técnico). Es fundamental identificar correctamente estos recursos para garantizar el éxito del proyecto, ya que cualquier deficiencia en la planificación puede resultar en fallos del sistema, sobrecostos o incapacidad para mantener el servicio continuo que requiere un sistema 24/7.

## 2. Desarrollo técnico correcto y preciso

### Recursos Materiales

Para un sistema que funciona 24/7, necesito componentes hardware específicos:

- **Procesador**: Debe ser de alto rendimiento para soportar carga constante. Un Intel Xeon o AMD EPYC con múltiples núcleos.
- **Memoria RAM**: Mínimo 32GB DDR4 ECC (Error Correcting Code) para detectar y corregir errores de memoria.
- **Almacenamiento**: Discos SSD en configuración RAID 1 (espejo) para redundancia de datos.
- **Conectividad**: Doble conexión a internet con proveedores diferentes (failover automático).
- **Sistema de alimentación ininterrumpida (SAI/UPS)**: Para mantener el sistema funcionando durante cortes de luz.
- **Sistema de refrigeración**: Adecuado para operación continua sin sobrecalentamiento.

### Recursos Económicos

Los costos asociados se dividen en:

- **Costos de compra inicial**:
  - Servidor: 3.000-5.000€
  - Equipamiento de red: 500-800€
  - SAI: 300-500€
  - Licencias software: 1.000-2.000€

- **Costos de mantenimiento anual**:
  - Electricidad: 600-800€
  - Conexión a internet: 600-1.200€
  - Mantenimiento hardware: 300-500€
  - Actualizaciones software: 200-400€

- **Costos de personal**: Calculados en la sección de recursos humanos.

### Recursos Humanos

Para soporte 24/7 necesito:

- **Personal de desarrollo**:
  - 1 desarrollador senior a tiempo completo durante 6 meses = 960 horas
  - 1 desarrollador junior a tiempo completo durante 4 meses = 640 horas

- **Personal de soporte técnico**:
  - Para cubrir 24 horas al día, 7 días a la semana:
  - 3 técnicos trabajando en turnos de 8 horas
  - Cada técnico necesita 2 días libres por semana
  - Total mínimo: 4-5 técnicos para cubrir bajas y vacaciones

## 3. Aplicación práctica con ejemplo claro

Voy a crear un programa simple que calcula los recursos necesarios y costos totales para un proyecto informático 24/7:

```python
# Calculadora de recursos para proyecto informático 24/7

def calcular_recursos():
    # Recursos materiales (costos en euros)
    costos_hardware = {
        "servidor": 4000,
        "red": 650,
        "sai": 400,
        "licencias": 1500
    }
    
    # Recursos económicos (costos anuales)
    costos_anuales = {
        "electricidad": 700,
        "internet": 900,
        "mantenimiento": 400,
        "actualizaciones": 300
    }
    
    # Recursos humanos (costos por hora)
    costos_horas = {
        "desarrollador_senior": 35,
        "desarrollador_junior": 20,
        "tecnico_soporte": 25
    }
    
    # Horas necesarias
    horas_desarrollo = {
        "senior": 960,
        "junior": 640
    }
    
    # Cálculo de costos
    costo_hardware_total = sum(costos_hardware.values())
    costo_anual_total = sum(costos_anuales.values())
    costo_desarrollo = (horas_desarrollo["senior"] * costos_horas["desarrollador_senior"] + 
                       horas_desarrollo["junior"] * costos_horas["desarrollador_junior"])
    
    # Personal para soporte 24/7 (5 técnicos)
    horas_soporte_anual = 5 * 8 * 365  # 5 técnicos, 8 horas diarias, 365 días
    costo_soporte_anual = horas_soporte_anual * costos_horas["tecnico_soporte"]
    
    # Resultados
    print("=== RECURSOS MATERIALES ===")
    for item, costo in costos_hardware.items():
        print(f"{item}: {costo}€")
    print(f"Total hardware: {costo_hardware_total}€")
    
    print("\n=== RECURSOS ECONÓMICOS (ANUALES) ===")
    for item, costo in costos_anuales.items():
        print(f"{item}: {costo}€")
    print(f"Total anual: {costo_anual_total}€")
    
    print("\n=== RECURSOS HUMANOS ===")
    print(f"Desarrollo (senior): {horas_desarrollo['senior']}h = {horas_desarrollo['senior'] * costos_horas['desarrollador_senior']}€")
    print(f"Desarrollo (junior): {horas_desarrollo['junior']}h = {horas_desarrollo['junior'] * costos_horas['desarrollador_junior']}€")
    print(f"Total desarrollo: {costo_desarrollo}€")
    print(f"Soporte 24/7 (5 técnicos): {horas_soporte_anual}h = {costo_soporte_anual}€")
    
    print("\n=== RESUMEN DE COSTOS ===")
    print(f"Inversión inicial: {costo_hardware_total + costo_desarrollo}€")
    print(f"Costo anual operación: {costo_anual_total + costo_soporte_anual}€")
    print(f"Personal necesario para soporte 24/7: 5 técnicos")

# Ejecutar la función
calcular_recursos()
```

### Errores comunes y cómo evitarlos

1. **Subestimar los costos de mantenimiento**: Muchos proyectos solo calculan el costo inicial y olvidan los gastos continuos. Solución: incluir siempre un presupuesto anual de mantenimiento.

2. **No planificar redundancia**: Para un sistema 24/7 es crucial tener componentes duplicados. Solución: usar configuraciones RAID y conexiones dobles.

3. **Calcular mal el personal para 24/7**: Se suele olvidar que se necesitan más personas para cubrir vacaciones y bajas. Solución: calcular con un 20-25% adicional de personal.

## 4. Cierre/Conclusión

La determinación de recursos es un aspecto fundamental en cualquier proyecto informático, especialmente en sistemas que requieren disponibilidad continua. Como he podido analizar, es necesario considerar tres categorías principales de recursos: materiales, económicos y humanos, cada uno con sus factores específicos a considerar.

Este análisis me ha permitido comprender que un sistema 24/7 no solo requiere hardware robusto, sino también una planificación cuidadosa de los recursos humanos y económicos. La redundancia en todos los niveles (hardware, conectividad, personal) es clave para garantizar la continuidad del servicio.

Los conocimientos adquiridos en este ejercicio serán aplicables en futuros proyectos, ya que la metodología para determinar recursos es similar independientemente de la escala del sistema. La correcta planificación de recursos desde el inicio del proyecto marca la diferencia entre el éxito y el fracaso en la implementación de sistemas críticos.