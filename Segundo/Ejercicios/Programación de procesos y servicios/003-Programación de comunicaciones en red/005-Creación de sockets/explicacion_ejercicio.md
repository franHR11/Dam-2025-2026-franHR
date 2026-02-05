# 005 - Creación de sockets (Servidor WebSocket)

### 🧠 2. Explicación personal del ejercicio
En esta práctica me he propuesto crear un sistema de comunicación en tiempo real utilizando la librería `asyncio` y WebSockets en Python. Como me gusta la pesca, he enfocado el ejercicio imaginando que es una "radio" para que los pescadores compartan sus capturas en directo. He implementado el servidor para que acepte mensajes en formato JSON, lo cual es mucho más estructurado que enviar texto plano, facilitando la escalabilidad si quisiera añadir más datos como coordenadas o peso de los peces.

La parte más interesante ha sido manejar la lista de clientes conectados mediante un `set()`, asegurándome de que cuando alguien envía un mensaje, este se retransmite a todos los demás (broadcasting) de forma asíncrona, sin bloquear el servidor.

### 💻 3. Código de programación

**001-servidor de websockets.py**
```python
import asyncio
import websockets
import json

# Conjunto para almacenar los clientes conectados (pescadores en la orilla)
clientes = set()

async def handler(websocket):
    """
    Maneja la conexión de un nuevo cliente (pescador).
    Recibe mensajes JSON y los retransmite a todos.
    """
    clientes.add(websocket)
    print("Nuevo pescador conectado.")
    
    try:
        # Bucle para escuchar los mensajes
        async for mensaje in websocket:
            try:
                # Decodificamos el JSON
                datos = json.loads(mensaje)
                print(f"Mensaje recibido: {datos}")
                
                # Preparamos respuesta JSON
                respuesta = json.dumps(datos)
                
                # Broadcasting: enviar a todos los conectados
                if clientes:
                    tasks = [asyncio.create_task(cliente.send(respuesta)) for cliente in clientes]
                    await asyncio.gather(*tasks)
                    
            except json.JSONDecodeError:
                print("Error: JSON no válido.")
                
    finally:
        clientes.remove(websocket)
        print("Un pescador se ha retirado.")

async def main():
    print("Servidor de pesca iniciado en ws://localhost:8765")
    async with websockets.serve(handler, "localhost", 8765):
        await asyncio.Future()  # Run forever

if __name__ == "__main__":
    try:
        asyncio.run(main())
    except KeyboardInterrupt:
        print("Servidor detenido.")
```

**prueba.html**
```html
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Chat de Pesca - WebSocket</title>
</head>
<body>
    <h1>🎣 Radio de Pescadores</h1>
    <div id="mensajes" style="border:1px solid #ccc; height:200px; overflow-y:scroll;"></div>
    <input type="text" id="textoInput" placeholder="Reporta captura...">
    <button onclick="enviarMensaje()">Enviar</button>

    <script>
        const socket = new WebSocket("ws://localhost:8765");

        socket.onmessage = function(event) {
            const datos = JSON.parse(event.data);
            const div = document.getElementById("mensajes");
            div.innerHTML += `<div><strong>Pescador:</strong> ${datos.mensaje}</div>`;
        };

        function enviarMensaje() {
            const input = document.getElementById("textoInput");
            const msg = { mensaje: input.value };
            socket.send(JSON.stringify(msg));
            input.value = "";
        }
    </script>
</body>
</html>
```

### 📊 4. Rúbrica de evaluación cumplida
- **Servidor WebSocket funcional:** El script levanta un servidor en el puerto 8765 y acepta conexiones.
- **Manejo de JSON:** Se utiliza `json.loads` para recibir y `json.dumps` para enviar, cumpliendo el requisito de formato.
- **Broadcasting:** La función `handler` itera sobre el conjunto `clientes` para enviar el mensaje a todos.
- **Interfaz HTML:** Se ha creado `prueba.html` con un campo de texto y botón que envía JSON válido al servidor.
- **Código organizado y comentado:** He incluido comentarios explicando las partes clave (conexión, broadcasting, manejo de errores).
- **Hobbies integrados:** La temática del chat está ambientada en la pesca.

### 🧾 5. Cierre
Me ha parecido un ejercicio muy práctico para entender cómo funciona la comunicación bidireccional en la web moderna. Al usar `asyncio`, el servidor se siente muy ligero y capaz de manejar varias "cañas" a la vez sin enredarse.
