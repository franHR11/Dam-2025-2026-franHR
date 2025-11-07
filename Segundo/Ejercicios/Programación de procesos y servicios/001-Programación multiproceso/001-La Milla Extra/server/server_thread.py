# server/server_thread.py
import json
import os
import socket
import sys
import threading
import time

# Añado el path del servidor al sys.path para importar los módulos
sys.path.append(os.path.dirname(os.path.dirname(os.path.abspath(__file__))))

from crypto_utils import decrypt_aes, encrypt_aes, encrypt_rsa, generate_aes_key

# Lista global de clientes conectados
clients = []
clients_lock = threading.Lock()


def broadcast_message(message, sender_socket=None):
    # Envío un mensaje a todos los clientes conectados
    with clients_lock:
        for client_info in clients[:]:  # Copia para evitar problemas durante iteración
            if client_info["socket"] != sender_socket:
                try:
                    # Cifro el mensaje con la clave AES del cliente
                    encrypted_msg = encrypt_aes(message, client_info["aes_key"])
                    client_info["socket"].send(encrypted_msg)
                except Exception as e:
                    # Si hay error, elimino al cliente
                    print(f"Error enviando mensaje a cliente: {e}")
                    try:
                        client_info["socket"].close()
                    except:
                        pass
                    if client_info in clients:
                        clients.remove(client_info)


def handle_client(client_socket, addr, log_queue):
    # Manejo la conexión de un cliente
    aes_key = None

    try:
        # Recibo la clave pública del cliente
        client_public_key = client_socket.recv(2048)

        # Genero una clave AES para este cliente
        aes_key = generate_aes_key()

        # Cifro la clave AES con la clave pública del cliente
        encrypted_aes_key = encrypt_rsa(aes_key, client_public_key)

        # Envío la clave AES cifrada al cliente
        client_socket.send(encrypted_aes_key)

        # Añado el cliente a la lista
        with clients_lock:
            clients.append(
                {
                    "socket": client_socket,
                    "addr": addr,
                    "aes_key": aes_key,
                    "connected_at": time.time(),
                }
            )

        # Registro la conexión
        log_queue.put(f"Cliente conectado: {addr}")
        print(f"Cliente {addr} conectado correctamente")

        # Envío mensaje de bienvenida
        welcome_msg = json.dumps(
            {
                "type": "system",
                "content": f"Bienvenido al chat seguro. Hay {len(clients)} usuarios conectados.",
            }
        )
        encrypted_welcome = encrypt_aes(welcome_msg, aes_key)
        client_socket.send(encrypted_welcome)

        # Bucle principal del cliente
        while True:
            try:
                # Recibo mensaje cifrado
                encrypted_message = client_socket.recv(1024)
                if not encrypted_message:
                    break

                # Descifro el mensaje
                message = decrypt_aes(encrypted_message, aes_key)

                # Proceso el mensaje
                message_data = json.loads(message)

                if message_data["type"] == "chat":
                    # Mensaje de chat
                    formatted_message = (
                        f"[{time.strftime('%H:%M')}] {message_data['content']}"
                    )
                    print(f"Mensaje de {addr}: {message_data['content']}")

                    # Envío a todos los clientes
                    broadcast_message(
                        json.dumps(
                            {
                                "type": "chat",
                                "content": formatted_message,
                                "sender": str(addr),
                            }
                        )
                    )

                    # Registro el mensaje
                    log_queue.put(f"Chat de {addr}: {message_data['content']}")

            except json.JSONDecodeError:
                print(f"Mensaje JSON inválido de {addr}")
                continue
            except Exception as e:
                print(f"Error procesando mensaje de {addr}: {e}")
                break

    except Exception as e:
        print(f"Error en la conexión con {addr}: {e}")
        log_queue.put(f"Error en conexión con {addr}: {e}")
    finally:
        # Limpio la conexión
        with clients_lock:
            for i, client_info in enumerate(clients):
                if client_info["socket"] == client_socket:
                    clients.pop(i)
                    break

        try:
            client_socket.close()
        except:
            pass

        log_queue.put(f"Cliente desconectado: {addr}")
        print(f"Cliente {addr} desconectado")
