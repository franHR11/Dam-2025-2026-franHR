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
    # Añadimos el nuevo cliente al conjunto
    clientes.add(websocket)
    print("Nuevo pescador conectado.")
    
    try:
        # Bucle para escuchar los mensajes (esperando que piquen)
        async for mensaje in websocket:
            try:
                # Intentamos decodificar el mensaje JSON
                datos = json.loads(mensaje)
                print(f"Mensaje recibido: {datos}")
                
                # Preparamos el mensaje para reenviar (JSON)
                respuesta = json.dumps(datos)
                
                # Enviamos el mensaje a todos los clientes conectados
                # Usamos websockets.broadcast si la versión lo permite, o un bucle
                if clientes:
                    # Creamos tareas para enviar a todos los sockets simultáneamente
                    tasks = [asyncio.create_task(cliente.send(respuesta)) for cliente in clientes]
                    await asyncio.gather(*tasks)
                    
            except json.JSONDecodeError:
                print("Error: El mensaje no es un JSON válido.")
            except Exception as e:
                print(f"Error procesando mensaje: {e}")
                
    finally:
        # Si el cliente se desconecta, lo sacamos del conjunto
        clientes.remove(websocket)
        print("Un pescador se ha retirado.")

async def main():
    # Iniciamos el servidor en localhost puerto 8765
    print("Servidor de pesca iniciado en ws://localhost:8765")
    async with websockets.serve(handler, "localhost", 8765):
        await asyncio.Future()  # Mantiene el servidor corriendo indefinidamente

if __name__ == "__main__":
    try:
        asyncio.run(main())
    except KeyboardInterrupt:
        print("Servidor detenido.")
