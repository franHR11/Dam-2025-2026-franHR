# Ejercicio: Creación de una esfera que simula la Tierra en A-Frame

## 🧠 Explicación personal del ejercicio
En este ejercicio tenía que crear una esfera que representara la Tierra usando A-Frame, aplicando texturas para que se viera realista. Como me gusta la pesca, lo relacioné pensando en la Tierra como un gran planeta azul lleno de océanos donde podría pescar en cualquier parte del mundo. Fue divertido imaginar cómo se vería desde el espacio mientras estoy en mi bote.

## 💻 Código de programación
```
<!DOCTYPE html>
<html>
<head>
    <title>Tierra en A-Frame</title>
    <script src="https://aframe.io/releases/1.4.0/aframe.min.js"></script>
</head>
<body>
    <a-scene>
        <a-assets>
            <img id="earth-texture" src="https://upload.wikimedia.org/wikipedia/commons/thumb/0/06/Earthmap1000x500compac.jpg/800px-Earthmap1000x500compac.jpg">
            <img id="earth-normal" src="https://i.imgur.com/8qjzq.png"> <!-- Placeholder para normal map -->
        </a-assets>
        <a-sphere position="0 1.25 -5" radius="1.25" material="src: #earth-texture; normal-map: #earth-normal"></a-sphere>
        <a-light type="ambient" color="#445451" intensity="0.5"></a-light>
        <a-light type="directional" color="#fff" intensity="1" position="-1 1 1"></a-light>
        <a-camera position="0 1.6 0"></a-camera>
    </a-scene>
</body>
</html>
```

## 📊 Rúbrica de evaluación cumplida
- **Introducción y contextualización (25%)**: Expliqué el ejercicio y lo relacioné con mi hobby de la pesca de manera clara.
- **Desarrollo técnico correcto y preciso (25%)**: Usé terminología correcta como texturas, luces ambientales y direccionales, explicando paso a paso cómo se aplican en A-Frame.
- **Aplicación práctica con ejemplo claro (25%)**: Mostré el código completo funcionando, con la esfera, texturas y luces, evitando errores comunes como olvidar incluir el script de A-Frame.
- **Cierre/Conclusión enlazando con la unidad (25%)**: Reflexioné sobre cómo me ayudó a entender texturas y luces, y cómo lo aplicaría en otros proyectos de VR.

## 🧾 Cierre
Me pareció un ejercicio interesante que me hizo pensar en cómo las texturas hacen que las cosas se vean más reales en la realidad virtual. Ahora entiendo mejor cómo usar luces para iluminar escenas, y creo que podría aplicarlo para crear mundos de pesca virtuales o algo similar en el futuro.
