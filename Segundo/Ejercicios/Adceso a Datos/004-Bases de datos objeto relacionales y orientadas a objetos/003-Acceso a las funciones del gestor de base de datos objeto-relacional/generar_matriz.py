def crear_matriz_rectangular(origen, repeticiones_x, repeticiones_y, repeticiones_z, intervalo_x, intervalo_y, intervalo_z):
    """
    Simula la creación de una matriz rectangular de objetos en un espacio 3D.
    
    :param origen: Tupla (x, y, z) con la posición inicial.
    :param repeticiones_x: Número de elementos en el eje X.
    :param repeticiones_y: Número de elementos en el eje Y.
    :param repeticiones_z: Número de elementos en el eje Z.
    :param intervalo_x: Distancia entre elementos en el eje X.
    :param intervalo_y: Distancia entre elementos en el eje Y.
    :param intervalo_z: Distancia entre elementos en el eje Z.
    """
    print(f"--- Iniciando Generación de Matriz en {origen} ---")
    print(f"Configuración: {repeticiones_x}x{repeticiones_y}x{repeticiones_z}")
    
    contador = 1
    
    # Recorremos el eje Z (altura)
    for z in range(repeticiones_z):
        pos_z = origen[2] + (z * intervalo_z)
        
        # Recorremos el eje Y (profundidad)
        for y in range(repeticiones_y):
            pos_y = origen[1] + (y * intervalo_y)
            
            # Recorremos el eje X (anchura)
            for x in range(repeticiones_x):
                pos_x = origen[0] + (x * intervalo_x)
                
                # 'Creamos' el objeto en esta posición
                print(f"[{contador}] Creando objeto en coordenadas: ({pos_x}, {pos_y}, {pos_z})")
                contador += 1
                
    print("--- Matriz Generada Correctamente ---")

# Bloque principal de ejecución
if __name__ == "__main__":
    # Definimos los parámetros de nuestra matriz
    punto_inicio = (0, 0, 0)
    
    # Queremos una matriz de 3 de ancho, 2 de fondo y 2 de alto
    num_x = 3
    num_y = 2
    num_z = 2
    
    # Distancias entre bloques
    dist_x = 10
    dist_y = 15
    dist_z = 20
    
    crear_matriz_rectangular(punto_inicio, num_x, num_y, num_z, dist_x, dist_y, dist_z)
