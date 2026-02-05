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
                # Si falla el envío (client.send lanza excepción), asumimos que
                # el socket está roto o cerrado. No lo eliminamos aquí para evitar
                # problemas de concurrencia al modificar la lista mientras iteramos,
                # pero el manejo de errores en 'handle_client' se encargará de la limpieza.
                pass

def handle_client(client):
    """
    Función principal del hilo de cada cliente.
    Mantiene un bucle infinito escuchando los mensajes de un cliente específico
    y retransmitiéndolos al resto (broadcast).
    
    Args:
        client (socket): El objeto socket conectado del cliente.
    """
    while True:
        try:
            # Intentamos recibir mensajes. recv(1024) lee hasta 1024 bytes del buffer.
            # Esta es una operación bloqueante.
            message = client.recv(1024)
            
            # Si message está vacío (b''), significa que el cliente cerró la conexión ordenadamente.
            if not message:
                raise Exception("Cliente desconectado")
                
            # Retransmitir el mensaje a todos los demás
            broadcast(message, client)
        except:
            # Bloque de gestión de desconexiones (errores o cierre de socket).
            # Se ejecuta si recv falla o si detectamos desconexión explícita.
            if client in clients:
                index = clients.index(client)
                
                # Eliminamos al cliente de las listas de seguimiento
                clients.remove(client)
                client.close()
                nickname = nicknames[index]
                
                # Avisamos al resto de usuarios
                print(f"INFO: {nickname} se ha desconectado.")
                broadcast(f'{nickname} ha salido del chat de pesca.'.encode('utf-8'))
                
                # Eliminamos el apodo de la lista
                nicknames.remove(nickname)
            break # Rompemos el bucle while para terminar el hilo

def receive():
    """
    Bucle principal de aceptación de conexiones (Main Loop).
    Configura el socket del servidor y espera constantemente nuevas conexiones entrantes.
    """
    # Creamos el socket IPv4 (AF_INET) y TCP (SOCK_STREAM)
    server = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
    
    # Opción SO_REUSEADDR para poder reiniciar el servidor inmediatamente sin esperar al timeout del puerto
    server.setsockopt(socket.SOL_SOCKET, socket.SO_REUSEADDR, 1)
    
    # Vinculamos el socket a la dirección y puerto especificados
    try:
        server.bind((HOST, PORT))
        server.listen() # Ponemos el socket en modo escucha
        print(f"[*] SERVIDOR INICIADO - Escuchando en {HOST}:{PORT}")
    except Exception as e:
        print(f"[!] Error al iniciar servidor: {e}")
        return

    while True:
        # accept() es bloqueante: espera hasta que llegue un cliente nuevo.
        # Devuelve el objeto socket del cliente y su dirección (IP, puerto).
        client, address = server.accept()
        print(f"[+] Nueva conexión establecida con {str(address)}")

        # --- PROTOCOLO DE HANDSHAKE (Saludo inicial) ---
        # 1. Pedimos el apodo
        client.send('NICK'.encode('utf-8'))
        # 2. Recibimos el apodo
        nickname = client.recv(1024).decode('utf-8')
        
        # Guardamos la información del cliente
        nicknames.append(nickname)
        clients.append(client)

        print(f"    Apodo registrado: {nickname}")
        
        # 3. Notificamos a todos la nueva incorporación
        broadcast(f"{nickname} se ha unido al chat de pesca!".encode('utf-8'), client)
        # 4. Confirmamos al cliente su conexión exitosa
        client.send('Conectado al servidor. ¡Buena pesca!'.encode('utf-8'))

        # --- GESTIÓN DE HILOS ---
        # Iniciamos un hilo dedicado para gestionar la comunicación con este cliente.
        # Esto permite que el bucle principal vuelva arriba inmediatamente para aceptar
        # más conexiones mientras este cliente es atendido en paralelo.
        thread = threading.Thread(target=handle_client, args=(client,))
        thread.start()

if __name__ == "__main__":
    receive()
