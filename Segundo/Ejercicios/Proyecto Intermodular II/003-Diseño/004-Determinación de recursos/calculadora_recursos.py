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
if __name__ == "__main__":
    calcular_recursos()