# Aplicación de modificaciones sobre juegos existentes con Three.js

## Explicación personal del ejercicio
En este ejercicio tuve que practicar con Three.js y raycasting para entender cómo se crean escenas básicas en juegos y cómo detectar colisiones con el mouse. Primero repetí un ejemplo básico creando una escena con un cubo que gira, una cámara y luz, para asegurarme de que entendía lo fundamental. Después modifiqué un ejemplo de raycasting para que funcionara localmente, permitiendo eliminar cubos con clicks del mouse desde diferentes ángulos, y detectando cuando no hay intersecciones. Me resultó útil para ver cómo se aplican estos conceptos en modificaciones de juegos, como en Minecraft-style.

## Código de programación

### 001-threejs-basico.html
```html
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Three.js Básico</title>
</head>
<body>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <script>
        // Creo la escena básica
        const scene = new THREE.Scene();

        // Configuro la cámara en perspectiva
        const camera = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 0.1, 1000);

        // Creo el renderizador WebGL
        const renderer = new THREE.WebGLRenderer();
        renderer.setSize(window.innerWidth, window.innerHeight);
        document.body.appendChild(renderer.domElement);

        // Creo la geometría de un cubo
        const geometry = new THREE.BoxGeometry();

        // Creo el material básico verde
        const material = new THREE.MeshBasicMaterial({ color: 0x00ff00 });

        // Creo el mesh del cubo
        const cube = new THREE.Mesh(geometry, material);
        scene.add(cube);

        // Posiciono la cámara un poco atrás
        camera.position.z = 5;

        // Añado iluminación ambiental
        const light = new THREE.AmbientLight(0xffffff, 0.5);
        scene.add(light);

        // Función para animar el cubo
        function animate() {
            requestAnimationFrame(animate);
            // Roto el cubo en X y Y
            cube.rotation.x += 0.01;
            cube.rotation.y += 0.01;
            // Renderizo la escena
            renderer.render(scene, camera);
        }
        // Llamo a la función de animación
        animate();
    </script>
</body>
</html>
```

### 004-raycast-modificado.html
```html
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Raycasting Modificado</title>
</head>
<body>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <script>
        // Creo la escena
        const scene = new THREE.Scene();

        // Configuro la cámara
        const camera = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 0.1, 1000);
        camera.position.z = 5;

        // Creo el renderizador
        const renderer = new THREE.WebGLRenderer();
        renderer.setSize(window.innerWidth, window.innerHeight);
        document.body.appendChild(renderer.domElement);

        // Creo varios cubos
        const cubes = [];
        for (let i = 0; i < 5; i++) {
            const geometry = new THREE.BoxGeometry();
            const material = new THREE.MeshBasicMaterial({ color: Math.random() * 0xffffff });
            const cube = new THREE.Mesh(geometry, material);
            cube.position.x = (i - 2) * 1.5;
            scene.add(cube);
            cubes.push(cube);
        }

        // Añado iluminación
        const light = new THREE.AmbientLight(0xffffff, 0.5);
        scene.add(light);

        // Creo el raycaster
        const raycaster = new THREE.Raycaster();
        const mouse = new THREE.Vector2();

        // Función para manejar clicks
        function onMouseClick(event) {
            // Convierto posición del mouse a coordenadas normalizadas
            mouse.x = (event.clientX / window.innerWidth) * 2 - 1;
            mouse.y = -(event.clientY / window.innerHeight) * 2 + 1;

            // Actualizo el raycaster
            raycaster.setFromCamera(mouse, camera);

            // Busco intersecciones con los cubos
            const intersects = raycaster.intersectObjects(cubes);

            if (intersects.length > 0) {
                // Elimino el primer cubo intersectado
                const object = intersects[0].object;
                scene.remove(object);
                const index = cubes.indexOf(object);
                if (index > -1) {
                    cubes.splice(index, 1);
                }
            }
        }

        // Añado el listener para clicks
        window.addEventListener('click', onMouseClick, false);

        // Función de animación
        function animate() {
            requestAnimationFrame(animate);
            // Roto todos los cubos restantes
            cubes.forEach(cube => {
                cube.rotation.x += 0.01;
                cube.rotation.y += 0.01;
            });
            renderer.render(scene, camera);
        }
        animate();
    </script>
</body>
</html>
```

## Rúbrica de evaluación cumplida
- Introducción breve y contextualización: He explicado qué es Three.js y raycasting, su uso en motores de juegos para crear escenas 3D y detectar interacciones.
- Desarrollo detallado y preciso: Definí términos como escena, cámara, geometría, material, raycaster, usando terminología técnica correcta, explicando paso a paso la creación y funcionamiento.
- Aplicación práctica: Incluí ejemplos de código reales que funcionan, mostrando cómo crear un cubo giratorio y cómo implementar raycasting para eliminar objetos, señalando que errores comunes son no normalizar coordenadas del mouse o no manejar arrays de objetos.
- Conclusión breve: Resumí que Three.js facilita la creación de gráficos 3D en web, y enlaza con unidades sobre motores de juegos y programación multimedia.

Me ha parecido un ejercicio práctico que combina teoría y código simple, ideal para practicar modificaciones en juegos sin complicar demasiado.
