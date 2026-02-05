import socket
import threading

# =============================================================================
# CLIENTE DE CHAT TCP/IP MULTIHILO
# =============================================================================
# Este script implementa el cliente de chat.
# Se conecta al servidor y utiliza dos hilos separados:
# 1. Hilo de escucha (receive): Para recibir mensajes del servidor en cualquier momento.
# 2. Hilo de escritura (write): Para capturar la entrada del teclado y enviarla.
# Esta separación es crucial para que el input() no bloquee la recepción de mensajes.
# =============================================================================

# Dirección del servidor (Loopback para pruebas locales)
HOST = '127.0.0.1' 
PORT = 5000

# Solicitamos el apodo al usuario antes de conectar
nickname = input("🎣 Elige tu apodo de pescador: ")

# Creamos el socket IPv4 (AF_INET) y TCP (SOCK_STREAM)
client = socket.socket(socket.AF_INET, socket.SOCK_STREAM)

# Intentamos conectar al servidor
try:
    client.connect((HOST, PORT))
    print(f"[*] Conectado exitosamente al servidor en {HOST}:{PORT}")
except ConnectionRefusedError:
    print("[!] No se pudo conectar al servidor. Asegúrate de que esté encendido.")
    exit()

def receive():
    """
    Función encargada de escuchar los mensajes entrantes del servidor.
    Se ejecuta en un hilo secundario independiente.
    """
    while True:
        try:
            # Intentamos recibir datos del servidor (bloqueante)
            message = client.recv(1024).decode('utf-8')
            
            # --- PROTOCOLO DE HANDSHAKE ---
            if message == 'NICK':
                # Si el servidor pide el apodo (comando 'NICK'), se lo enviamos
                client.send(nickname.encode('utf-8'))
            else:
                # Si es un mensaje normal, simplemente lo mostramos por pantalla
                print(message)
        except:
            # Si ocurre cualquier error (ej: servidor cierra conexión),
            # cerramos recursos y terminamos.
            print("[!] Se ha perdido la conexión con el servidor.")
            client.close()
            break

def write():
    """
    Función encargada de enviar mensajes.
    Se ejecuta en un hilo secundario (o podría ser el principal).
    Lee constantemente del teclado.
    """
    while True:
        # input() detiene la ejecución hasta que el usuario pulsa Enter.
        # Gracias a los hilos, esto no impide que sigamos recibiendo mensajes en 'receive()'.
        text = input('')
        
        # Formateamos el mensaje: "Apodo: Mensaje"
        message = f'{nickname}: {text}'
        
        try:
            client.send(message.encode('utf-8'))
        except:
            print("[!] Error al enviar mensaje.")
            break

# --- INICIO DE HILOS ---

# Hilo para recibir mensajes
receive_thread = threading.Thread(target=receive)
receive_thread.start()

# Hilo para enviar mensajes
# Nota: Podríamos haber usado el hilo principal para esto, pero separar ambos
# en hilos dedicados es una estructura limpia y simétrica.
write_thread = threading.Thread(target=write)
write_thread.start()
