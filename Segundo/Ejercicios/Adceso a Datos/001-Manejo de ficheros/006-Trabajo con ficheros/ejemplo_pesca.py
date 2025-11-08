"""
Ejemplo de uso de la clase GestorCSV para gestionar registros de pesca

Este script demuestra cómo utilizar la clase GestorCSV para:
1. Escribir datos de pesca en un archivo CSV
2. Leer los datos almacenados en el archivo CSV

Author: FranHR
Date: 2025
"""

from gestor_csv import GestorCSV

def main():
    """
    Función principal que demuestra el uso de la clase GestorCSV
    """
    print("=== GESTIÓN DE REGISTROS DE PESCA ===\n")
    
    # 1. Crear datos de pesca como tupla
    datos_pesca = ("Peixe Rojo", "10/05/23", "Lago del Lobo")
    print(f"Datos de pesca a guardar: {datos_pesca}")
    
    # 2. Crear instancia de GestorCSV y escribir datos
    print("\n--- ESCRIBIENDO DATOS EN ARCHIVO CSV ---")
    gestor = GestorCSV("pesca.csv")
    
    # Escribir los datos de pesca
    if gestor.escribir(datos_pesca):
        print("[OK] Datos escritos correctamente")
    else:
        print("[ERROR] Error al escribir los datos")
        return
    
    # Añadir más registros de pesca para demostrar
    mas_datos = [
        ("Trucha Arcoíris", "15/05/23", "Río Ebro"),
        ("Lucio", "20/05/23", "Embalse de Mequinenza"),
        ("Carpa Común", "25/05/23", "Estanque de la Albufera")
    ]
    
    for datos in mas_datos:
        gestor.escribir(datos)
    
    # 3. Leer la primera línea del archivo CSV
    print("\n--- LEYENDO PRIMERA LÍNEA DEL ARCHIVO CSV ---")
    primer_registro = gestor.leer()
    
    if primer_registro:
        print(f"Primer registro leído: {primer_registro}")
        print(f"Tipo de pez: {primer_registro[0]}")
        print(f"Fecha: {primer_registro[1]}")
        print(f"Lugar: {primer_registro[2]}")
    else:
        print("[ERROR] No se pudieron leer los datos")
    
    # 4. Leer todos los registros
    print("\n--- LEYENDO TODOS LOS REGISTROS DEL ARCHIVO CSV ---")
    todos_registros = gestor.leer_todas_lineas()
    
    if todos_registros:
        print(f"Total de registros encontrados: {len(todos_registros)}")
        for i, registro in enumerate(todos_registros, 1):
            print(f"Registro {i}: {registro}")
    else:
        print("[ERROR] No se pudieron leer los registros")

if __name__ == "__main__":
    main()