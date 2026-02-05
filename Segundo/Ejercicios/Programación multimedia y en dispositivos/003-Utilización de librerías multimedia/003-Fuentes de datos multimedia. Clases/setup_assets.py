from PIL import Image, ImageDraw
import os
import random

# Crear carpeta de galería
if not os.path.exists('galeria'):
    os.makedirs('galeria')

# Crear un logo de ejemplo (círculo rojo con texto)
print("Generando logo.png...")
logo = Image.new('RGBA', (400, 400), (0, 0, 0, 0))
draw = ImageDraw.Draw(logo)
draw.ellipse((50, 50, 350, 350), fill=(200, 50, 50, 255), outline=(255, 255, 255, 255), width=10)
logo.save('logo.png')

# Crear imágenes de muestra para la galería
print("Generando imágenes de muestra en 'galeria/'...")
for i in range(1, 4):
    # Imagen con colores aleatorios y formas simples
    bg_color = (random.randint(0, 255), random.randint(0, 255), random.randint(0, 255))
    img = Image.new('RGB', (800, 600), bg_color)
    draw = ImageDraw.Draw(img)
    
    # Dibujar algunas formas aleatorias
    for _ in range(5):
        shape_color = (random.randint(0, 255), random.randint(0, 255), random.randint(0, 255))
        # Generar coordenadas y ordenarlas para evitar error x1 < x0
        x1 = random.randint(0, 800)
        x2 = random.randint(0, 800)
        y1 = random.randint(0, 600)
        y2 = random.randint(0, 600)
        
        draw.rectangle([min(x1, x2), min(y1, y2), max(x1, x2), max(y1, y2)], fill=shape_color)
        
    img.save(f'galeria/sample_{i}.jpg')

print("¡Assets generados! Ahora puedes ejecutar 'python main.py' sin advertencias.")
