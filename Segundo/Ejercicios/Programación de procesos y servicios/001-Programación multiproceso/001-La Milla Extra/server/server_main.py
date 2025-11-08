# server/server_main.py
import json
import multiprocessing
import os
import socket
import sys
import threading

# Añado el path del servidor al sys.path para importar los módulos
sys.path.append(os.path.dirname(os.path.dirname(os.path.abspath(__file__))))

from log_service import start_log_service
from process_monitor import start_monitor
from server_thread import handle_client


def load_config():
    # Cargo la configuración desde el archivo JSON
    with open("config/settings.json", "r") as f:
        return json.load(f)


def main():
    print("Iniciando servidor de chat seguro...")

    # Inicio el servicio de logs en un proceso separado
    log_queue = multiprocessing.Queue()
    log_process = multiprocessing.Process(target=start_log_service, args=(log_queue,))
    log_process.start()

    # Inicio el monitor de procesos en otro proceso
    monitor_process = multiprocessing.Process(target=start_monitor)
    monitor_process.start()

    # Cargo la configuración del servidor
    config = load_config()
    host = config["server"]["host"]
    port = config["server"]["port"]

    # Creo el socket del servidor
    server_socket = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
    server_socket.setsockopt(socket.SOL_SOCKET, socket.SO_REUSEADDR, 1)

    try:
        server_socket.bind((host, port))
        server_socket.listen(5)

        print(f"Servidor iniciado en {host}:{port}")

        # Registro el inicio del servidor
        log_queue.put(f"Servidor iniciado en {host}:{port}")

        while True:
            # Acepto nuevas conexiones
            client_socket, addr = server_socket.accept()
            print(f"Nueva conexión de {addr}")

            # Creo un hilo para manejar al cliente
            client_thread = threading.Thread(
                target=handle_client, args=(client_socket, addr, log_queue)
            )
            client_thread.daemon = True
            client_thread.start()

    except KeyboardInterrupt:
        print("\nServidor detenido")
        log_queue.put("Servidor detenido")
    except Exception as e:
        print(f"Error en el servidor: {e}")
        log_queue.put(f"Error en el servidor: {e}")
    finally:
        server_socket.close()
        log_process.terminate()
        monitor_process.terminate()


if __name__ == "__main__":
    main()
