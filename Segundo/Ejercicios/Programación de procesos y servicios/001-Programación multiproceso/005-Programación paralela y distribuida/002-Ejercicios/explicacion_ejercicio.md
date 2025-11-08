

#### 🧩 1. Encabezado informativo

**Nombre:** Fran  
**Apellidos:** [Apellido]  
**Curso:** 2º DAM  
**Fecha:** 11/10/2025  
**Tema:** Programación de procesos y servicios  
**Subtema:** Programación paralela y distribuida  

#### 🧠 2. Explicación personal del ejercicio

> En este ejercicio tenía que crear una aplicación para pesca en línea que utilice múltiples núcleos de procesador para realizar cálculos intensivos sin bloquear la interfaz. Para ello, he creado tres archivos: uno que muestra el número de núcleos disponibles, otro que define un worker para realizar cálculos intensivos y un tercero que asigna tareas a múltiples workers según el número de núcleos.

#### 💻 3. Código de programación

**numero_de_nucleos.html**
```html
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Número de Núcleos</title>
</head>
<body>
    <h1>Obtener Número de Núcleos del Procesador</h1>
    <p>Abre la consola del navegador para ver el resultado.</p>

    <script>
        // Obtener el número de núcleos disponibles
        const numCores = navigator.hardwareConcurrency;
        console.log("Número de núcleos disponibles: " + numCores);
    </script>
</body>
</html>
```

**006worker.js**
```javascript
// Worker que realiza un cálculo intensivo
self.onmessage = function(event) {
    const { taskId, numbers } = event.data;
    
    // Simular cálculo intensivo (multiplicación de números)
    let result = 1;
    for (let i = 0; i < numbers.length; i++) {
        result *= numbers[i];
    }
    
    // Enviar resultado al hilo principal
    self.postMessage({
        taskId: taskId,
        result: result
    });
};
```

**asignacion_workers.html**
```html
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asignación de Workers</title>
</head>
<body>
    <h1>Asignación de Workers a Núcleos</h1>
    <p>Abre la consola del navegador para ver los resultados.</p>

    <script>
        // Obtener el número de núcleos disponibles
        const numCores = navigator.hardwareConcurrency;
        console.log("Número de núcleos disponibles: " + numCores);
        
        // Crear array para almacenar los workers
        const workers = [];
        const results = [];
        
        // Crear tantos workers como núcleos disponibles
        for (let i = 0; i < numCores; i++) {
            workers[i] = new Worker('006worker.js');
            
            // Asignar función para recibir mensajes de los workers
            workers[i].onmessage = function(event) {
                const { taskId, result } = event.data;
                results[taskId] = result;
                console.log(`Resultado del worker ${taskId}: ${result}`);
                
                // Verificar si todos los workers han terminado
                if (results.filter(r => r !== undefined).length === numCores) {
                    console.log("Todos los resultados:", results);
                }
            };
            
            // Enviar tarea al worker
            workers[i].postMessage({
                taskId: i,
                numbers: [2, 3, 4, 5, 6, 7, 8, 9]
            });
        }
    </script>
</body>
</html>
```


#### 🧾 5. Cierre

> Este ejercicio me ha parecido muy interesante ya que he aprendido a utilizar Web Workers para aprovechar al máximo los recursos del procesador sin bloquear la interfaz de usuario. Es una técnica muy útil para aplicaciones que requieren realizar cálculos pesados, como podría ser una aplicación de pesca en línea que procese grandes cantidades de datos.