# server/log_service.py
import os
import sys
import time
from datetime import datetime

# Añado el path del servidor al sys.path para importar los módulos
sys.path.append(os.path.dirname(os.path.dirname(os.path.abspath(__file__))))


def setup_log_directory():
    # Creo el directorio de logs si no existe
    log_dir = "logs"
    if not os.path.exists(log_dir):
        os.makedirs(log_dir)
        print(f"Directorio de logs creado: {log_dir}")


def start_log_service(log_queue):
    # Función principal del servicio de logging
    setup_log_directory()

    # Abro el archivo de logs principal
    log_file_path = "logs/server.log"

    try:
        with open(log_file_path, "a", encoding="utf-8") as log_file:
            # Escribo encabezado de inicio
            start_message = f"\n\n{'=' * 60}\n"
            start_message += (
                f"SERVIDOR INICIADO - {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}\n"
            )
            start_message += f"{'=' * 60}\n"
            log_file.write(start_message)
            log_file.flush()

            print(f"Servicio de logging iniciado - Logs en: {log_file_path}")

            while True:
                try:
                    # Espero mensajes en la cola
                    message = log_queue.get()

                    if message is None:  # Señal para terminar
                        break

                    # Formateo el mensaje con timestamp
                    timestamp = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
                    log_entry = f"[{timestamp}] {message}\n"

                    # Escribo en el archivo de logs
                    log_file.write(log_entry)
                    log_file.flush()  # Forzado de escritura inmediata

                    # También muestro en consola para depuración
                    print(f"LOG: {message}")

                except Exception as e:
                    print(f"Error procesando mensaje de log: {e}")

    except Exception as e:
        print(f"Error iniciando servicio de logging: {e}")


def log_connection_details(addr, action, log_queue):
    # Función helper para registrar detalles de conexión
    if log_queue:
        timestamp = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
        message = f"CONEXIÓN {action.upper()}: {addr[0]}:{addr[1]} - {timestamp}"
        log_queue.put(message)


def log_error(error_msg, log_queue):
    # Función helper para registrar errores
    if log_queue:
        message = f"ERROR: {error_msg}"
        log_queue.put(message)


def log_system_event(event_msg, log_queue):
    # Función helper para registrar eventos del sistema
    if log_queue:
        message = f"SISTEMA: {event_msg}"
        log_queue.put(message)


def get_log_stats():
    # Función para obtener estadísticas de los logs
    try:
        log_file_path = "logs/server.log"
        if os.path.exists(log_file_path):
            stat = os.stat(log_file_path)
            return {
                "file_size": stat.st_size,
                "last_modified": datetime.fromtimestamp(stat.st_mtime),
                "file_path": log_file_path,
            }
    except Exception as e:
        print(f"Error obteniendo estadísticas de logs: {e}")
    return None


def cleanup_old_logs(days_to_keep=7):
    # Función para limpiar logs antiguos (opcional)
    try:
        log_dir = "logs"
        if os.path.exists(log_dir):
            current_time = time.time()
            for filename in os.listdir(log_dir):
                file_path = os.path.join(log_dir, filename)
                if os.path.isfile(file_path):
                    file_age = current_time - os.path.getmtime(file_path)
                    if file_age > (days_to_keep * 24 * 60 * 60):  # días a segundos
                        os.remove(file_path)
                        print(f"Log antiguo eliminado: {filename}")
    except Exception as e:
        print(f"Error limpiando logs antiguos: {e}")


if __name__ == "__main__":
    # Si ejecuto este archivo directamente, hago una prueba del servicio
    print("Probando servicio de logging...")

    # Creo una cola de prueba
    import multiprocessing

    test_queue = multiprocessing.Queue()

    # Inicio el servicio en un hilo separado
    import threading

    test_thread = threading.Thread(target=start_log_service, args=(test_queue,))
    test_thread.daemon = True
    test_thread.start()

    # Envío mensajes de prueba
    test_queue.put("Mensaje de prueba 1")
    test_queue.put("Mensaje de prueba 2")
    test_queue.put("Sistema iniciado correctamente")

    print("Mensajes de prueba enviados")

    # Espero un poco
    time.sleep(2)

    print("Prueba completada")
