from PIL import Image, ImageDraw

# Crear una imagen de 200x200 píxeles
imagen = Image.new("RGB", (200, 200), color="white")
dibujo = ImageDraw.Draw(imagen)

# Dibujar algunas formas para tener una imagen con detalles
dibujo.rectangle([50, 50, 150, 150], fill="red", outline="black")
dibujo.ellipse([75, 75, 125, 125], fill="blue")
dibujo.line([0, 0, 200, 200], fill="green", width=3)
dibujo.line([0, 200, 200, 0], fill="green", width=3)

# Guardar la imagen
imagen.save("josevicente.jpg")
print("Imagen de prueba creada como josevicente.jpg")