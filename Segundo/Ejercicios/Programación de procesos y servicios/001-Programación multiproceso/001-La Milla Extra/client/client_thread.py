# client/client_thread.py
import json
import os
import socket
import sys
import threading
import time

# Añado el path del servidor al sys.path para importar los módulos
sys.path.append(os.path.dirname(os.path.dirname(os.path.abspath(__file__))))
sys.path.append(
    os.path.join(os.path.dirname(os.path.dirname(os.path.abspath(__file__))), "server")
)

from crypto_utils import decrypt_aes, decrypt_rsa, encrypt_aes


class ClientThread(threading.Thread):
    def __init__(self, server, port, public_key, private_key):
        super().__init__()
        self.server = server
        self.port = port
        self.public_key = public_key
        self.private_key = private_key
        self.socket = None
        self.aes_key = None
        self.running = False
        self.message_callback = None
        self.status_callback = None

    def set_callbacks(self, message_callback, status_callback):
        # Configuro los callbacks para comunicación con la GUI
        self.message_callback = message_callback
        self.status_callback = status_callback

    def connect(self):
        try:
            # Creo el socket TCP
            self.socket = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
            self.socket.settimeout(10)  # Timeout de 10 segundos

            # Me conecto al servidor
            self.socket.connect((self.server, self.port))

            # Envío mi clave pública al servidor
            self.socket.send(self.public_key)

            # Recibo la clave AES cifrada del servidor
            encrypted_aes_key = self.socket.recv(256)

            # Descifro la clave AES con mi clave privada
            self.aes_key = decrypt_rsa(encrypted_aes_key, self.private_key)

            return True

        except socket.timeout:
            print("Tiempo de conexión agotado")
            return False
        except ConnectionRefusedError:
            print("Conexión rechazada - ¿El servidor está activo?")
            return False
        except Exception as e:
            print(f"Error al conectar: {e}")
            return False

    def send_message(self, message):
        try:
            if not self.running or not self.socket:
                return False

            # Preparo el mensaje JSON
            message_data = {
                "type": "chat",
                "content": message,
                "timestamp": time.time(),
            }

            # Cifro el mensaje con AES
            json_message = json.dumps(message_data)
            encrypted_message = encrypt_aes(json_message, self.aes_key)

            # Envío el mensaje cifrado
            self.socket.send(encrypted_message)
            return True

        except Exception as e:
            print(f"Error enviando mensaje: {e}")
            return False

    def run(self):
        self.running = True

        if self.status_callback:
            self.status_callback("Conectando al servidor...")

        try:
            while self.running:
                try:
                    # Recibo mensaje cifrado del servidor
                    encrypted_message = self.socket.recv(1024)

                    if not encrypted_message:
                        # El servidor cerró la conexión
                        break

                    # Descifro el mensaje
                    message = decrypt_aes(encrypted_message, self.aes_key)
                    message_data = json.loads(message)

                    # Proceso el mensaje según su tipo
                    if message_data["type"] == "chat":
                        # Mensaje de chat normal
                        if self.message_callback:
                            self.message_callback(message_data["content"])
                    elif message_data["type"] == "system":
                        # Mensaje del sistema
                        if self.message_callback:
                            self.message_callback(f"Sistema: {message_data['content']}")

                except socket.timeout:
                    # Timeout normal, continúo el bucle
                    continue
                except json.JSONDecodeError:
                    print("Error: mensaje JSON inválido recibido")
                    continue
                except Exception as e:
                    print(f"Error procesando mensaje: {e}")
                    break

        except Exception as e:
            print(f"Error en el hilo del cliente: {e}")
        finally:
            self.running = False
            if self.status_callback:
                self.status_callback("Desconectado del servidor")

    def disconnect(self):
        # Desconecto del servidor de forma segura
        self.running = False

        if self.socket:
            try:
                # Envío mensaje de desconexión si estoy conectado
                if self.aes_key:
                    disconnect_msg = json.dumps(
                        {"type": "disconnect", "timestamp": time.time()}
                    )
                    encrypted_msg = encrypt_aes(disconnect_msg, self.aes_key)
                    self.socket.send(encrypted_msg)
            except:
                pass  # Ignoro errores al enviar desconexión

            try:
                self.socket.close()
            except:
                pass  # Ignoro errores al cerrar el socket

        print("Cliente desconectado")

    def is_connected(self):
        # Verifico si estoy conectado
        return self.running and self.socket is not None
