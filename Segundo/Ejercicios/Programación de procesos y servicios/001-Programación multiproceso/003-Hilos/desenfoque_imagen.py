from PIL import Image
import time

# Abrir la imagen original
imagen = Image.open("josevicente.jpg")
pixeles = imagen.load()

# Obtener dimensiones
ancho, alto = imagen.size

# Crear una nueva imagen para el resultado
imagen_desenfocada = Image.new("RGB", (ancho, alto))
pixeles_nuevos = imagen_desenfocada.load()

# Iniciar temporizador
inicio = time.time()

# Recorrer cada píxel y aplicar desenfoque simple
for y in range(1, alto - 1):
    for x in range(1, ancho - 1):
        # Obtener valores RGB de los píxeles vecinos
        r_total = 0
        g_total = 0
        b_total = 0
        count = 0
        
        # Recorrer los 8 píxeles vecinos más el actual
        for dy in [-1, 0, 1]:
            for dx in [-1, 0, 1]:
                nx, ny = x + dx, y + dy
                if 0 <= nx < ancho and 0 <= ny < alto:
                    r, g, b = pixeles[nx, ny]
                    r_total += r
                    g_total += g
                    b_total += b
                    count += 1
        
        # Calcular el promedio
        r_promedio = r_total // count
        g_promedio = g_total // count
        b_promedio = b_total // count
        
        # Asignar el nuevo valor
        pixeles_nuevos[x, y] = (r_promedio, g_promedio, b_promedio)

# Detener temporizador
fin = time.time()
tiempo_total = fin - inicio

# Guardar la imagen desenfocada
imagen_desenfocada.save("josevicente2.jpg")

print(f"Proceso completado en {tiempo_total:.2f} segundos")
print("Imagen guardada como josevicente2.jpg")