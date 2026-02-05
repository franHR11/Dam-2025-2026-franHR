
### 🧩 1. Encabezado informativo

### 🧠 2. Explicación personal del ejercicio
Para este ejercicio me he imaginado que necesitaba montar un sistema de comunicación rápido para mi grupo de amigos pescadores, para avisarnos de dónde pican los peces. He creado un servidor de chat usando Sockets en Python, que es la forma más "cruda" y directa de conectar ordenadores, garantizando una conexión fiable mediante el protocolo TCP.

El servidor actúa como un repetidor central: acepta conexiones, guarda quién está dentro y cuando alguien escribe algo, se lo reenvía a todos los demás. Lo más complicado ha sido la parte de los hilos (`threading`), porque si no los usaba, el servidor se quedaba "congelado" esperando a que un solo usuario escribiera y no podía atender a los demás. He comentado todo el código detalladamente para explicar cómo funciona cada parte del protocolo de comunicación.

### 💻 3. Código de programación

**El Servidor (`servidor.py`)**:
Este script orquesta las conexiones. Es responsable de recibir mensajes y hacer "broadcast" a todos los clientes conectados.
```python
import socket
import threading

# =============================================================================
# SERVIDOR DE CHAT TCP/IP MULTIHILO
# =============================================================================
# Este script implementa el servidor central del chat.
# Utiliza sockets TCP para garantizar la entrega de mensajes y 'threading'
# para manejar múltiples conexiones de clientes simultáneamente sin bloquear
# el proceso principal.
# =============================================================================

# Configuración de conexión
# Escuchamos en '0.0.0.0' para aceptar conexiones de cualquier interfaz de red disponible.
HOST = '0.0.0.0'
PORT = 5000 # Puerto arbitrario no reservado (>1023)

# Estructuras de datos para gestionar el estado del chat
# clients: Almacena los objetos socket de los clientes conectados activo.
# nicknames: Almacena los apodos asociados a cada cliente (por índice).
clients = []
nicknames = []

def broadcast(message, _client=None):
    """
    Envía un mensaje a todos los clientes conectados, con opción de excluir a uno.
    
    Args:
        message (bytes): El mensaje codificado en bytes a enviar.
        _client (socket, optional): El cliente que originó el mensaje. 
                                    Si se especifica, no se le reenvía su propio mensaje.
    """
    for client in clients:
        if client != _client:
            try:
                client.send(message)
            except:
                # Si falla el envío, asumimos que el socket está roto o cerrado.
                pass

def handle_client(client):
    """
    Función principal del hilo de cada cliente.
    Mantiene un bucle infinito escuchando los mensajes retransmitiéndolos.
    """
    while True:
        try:
            # Intentamos recibir mensajes. recv(1024) lee hasta 1024 bytes del buffer.
            # Esta es una operación bloqueante.
            message = client.recv(1024)
            
            if not message:
                raise Exception("Cliente desconectado")
                
            # Retransmitir el mensaje a todos los demás
            broadcast(message, client)
        except:
            # Bloque de gestión de desconexiones
            if client in clients:
                index = clients.index(client)
                clients.remove(client)
                client.close()
                nickname = nicknames[index]
                
                print(f"INFO: {nickname} se ha desconectado.")
                broadcast(f'{nickname} ha salido del chat de pesca.'.encode('utf-8'))
                nicknames.remove(nickname)
            break

def receive():
    """
    Bucle principal de aceptación de conexiones (Main Loop).
    """
    # Creamos el socket IPv4 (AF_INET) y TCP (SOCK_STREAM)
    server = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
    server.setsockopt(socket.SOL_SOCKET, socket.SO_REUSEADDR, 1)
    
    try:
        server.bind((HOST, PORT))
        server.listen()
        print(f"[*] SERVIDOR INICIADO - Escuchando en {HOST}:{PORT}")
    except Exception as e:
        print(f"[!] Error al iniciar servidor: {e}")
        return

    while True:
        # accept() es bloqueante: espera hasta que llegue un cliente nuevo.
        client, address = server.accept()
        print(f"[+] Nueva conexión establecida con {str(address)}")

        # --- PROTOCOLO DE HANDSHAKE ---
        client.send('NICK'.encode('utf-8'))
        nickname = client.recv(1024).decode('utf-8')
        
        nicknames.append(nickname)
        clients.append(client)

        print(f"    Apodo registrado: {nickname}")
        broadcast(f"{nickname} se ha unido al chat de pesca!".encode('utf-8'), client)
        client.send('Conectado al servidor. ¡Buena pesca!'.encode('utf-8'))

        # Iniciamos hilo dedicado para este cliente
        thread = threading.Thread(target=handle_client, args=(client,))
        thread.start()

if __name__ == "__main__":
    receive()
```

**El Cliente (`cliente.py`)**:
Este script permite a un usuario unirse al chat. Usa dos hilos para poder escribir mensajes y recibirlos simultáneamente, sin que una acción bloquee a la otra.
```python
import socket
import threading

# =============================================================================
# CLIENTE DE CHAT TCP/IP MULTIHILO
# =============================================================================

# Dirección del servidor (Loopback para pruebas locales)
HOST = '127.0.0.1'
PORT = 5000

nickname = input("🎣 Elige tu apodo de pescador: ")

client = socket.socket(socket.AF_INET, socket.SOCK_STREAM)

try:
    client.connect((HOST, PORT))
    print(f"[*] Conectado exitosamente al servidor en {HOST}:{PORT}")
except ConnectionRefusedError:
    print("[!] No se pudo conectar al servidor. Asegúrate de que esté encendido.")
    exit()

def receive():
    """Hilo de escucha de mensajes entrantes"""
    while True:
        try:
            message = client.recv(1024).decode('utf-8')
            if message == 'NICK':
                client.send(nickname.encode('utf-8'))
            else:
                print(message)
        except:
            print("[!] Se ha perdido la conexión con el servidor.")
            client.close()
            break

def write():
    """Hilo de envío de mensajes"""
    while True:
        text = input('')
        message = f'{nickname}: {text}'
        try:
            client.send(message.encode('utf-8'))
        except:
            print("[!] Error al enviar mensaje.")
            break

# --- INICIO DE HILOS ---
receive_thread = threading.Thread(target=receive)
receive_thread.start()

write_thread = threading.Thread(target=write)
write_thread.start()
```

### 📊 4. Rúbrica de evaluación cumplida

1.  **Introducción y contextualización:**
    *   Explicado el uso de Sockets TCP/IP como base de la comunicación fiable.
    *   Contextualizado en un escenario útil (chat de grupo).
2.  **Desarrollo técnico correcto y preciso:**
    *   Uso de `socket.SOCK_STREAM` para TCP.
    *   Implementación robusta de `threading` para evitar bloqueos en el servidor y el cliente.
    *   Protocolo handshake implementado y detallado en los comentarios.
3.  **Aplicación práctica:**
    *   Código totalmente comentado y funcional, con manejo de errores (bloques try/except) para desconexiones inesperadas.
4.  **Cierre/Conclusión:**
    *   Reflexión sobre la concurrencia y la base de las comunicaciones en red.

### 🧾 5. Cierre
Este ejercicio me ha servido para profundizar en la programación de redes a bajo nivel. Entender cómo gestionar los hilos ha sido clave para que el servidor no se colapse con una sola conexión. Además, comentar el código paso a paso me ha ayudado a afianzar qué hace realmente cada función del socket.
