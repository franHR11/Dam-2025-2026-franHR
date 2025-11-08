import asyncio
import websockets

async def handler(websocket, path):
    async for message in websocket:
        # Generar el eco del mensaje
        echo_message = f"echo: {message}"
        # Enviar el eco de vuelta al cliente
        await websocket.send(echo_message)

async def main():
    # Iniciar el servidor WebSocket en localhost:8765
    server = await websockets.serve(handler, "127.0.0.1", 8765)
    print("Servidor WebSocket iniciado en ws://127.0.0.1:8765")
    await server.wait_closed()

if __name__ == "__main__":
    asyncio.run(main())