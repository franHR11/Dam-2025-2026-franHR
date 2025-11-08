# server/process_monitor.py
import os
import sys
import threading
import time

import psutil

# Añado el path del servidor al sys.path para importar los módulos
sys.path.append(os.path.dirname(os.path.dirname(os.path.abspath(__file__))))


def start_monitor():
    # Inicio el monitor de procesos en segundo plano
    monitor_thread = threading.Thread(target=monitor_loop)
    monitor_thread.daemon = True
    monitor_thread.start()
    print("Monitor de procesos iniciado")


def get_python_processes():
    # Obtengo todos los procesos Python activos
    python_processes = []
    try:
        for proc in psutil.process_iter(
            ["pid", "name", "cpu_percent", "memory_percent"]
        ):
            try:
                if "python" in proc.info["name"].lower():
                    python_processes.append(proc.info)
            except (psutil.NoSuchProcess, psutil.AccessDenied):
                continue
    except Exception as e:
        print(f"Error obteniendo procesos: {e}")
    return python_processes


def monitor_loop():
    # Bucle principal del monitor
    while True:
        try:
            print("\n" + "=" * 50)
            print("         MONITOR DE PROCESOS DEL SERVIDOR")
            print("=" * 50)

            # Monitorizo CPU y memoria del sistema
            cpu_percent = psutil.cpu_percent(interval=1)
            memory = psutil.virtual_memory()
            disk = psutil.disk_usage("/")

            print(f"\n📊 ESTADO DEL SISTEMA:")
            print(f"   CPU: {cpu_percent}%")
            print(
                f"   Memoria: {memory.percent}% ({memory.used / 1024 / 1024:.1f}MB / {memory.total / 1024 / 1024:.1f}MB)"
            )
            print(
                f"   Disco: {disk.percent}% ({disk.used / 1024 / 1024:.1f}MB / {disk.total / 1024 / 1024:.1f}MB)"
            )

            # Procesos Python activos
            python_processes = get_python_processes()
            print(f"\n🐍 PROCESOS PYTHON ({len(python_processes)} activos):")

            if python_processes:
                print(f"   {'PID':<8} {'CPU%':<6} {'MEM%':<6} {'NOMBRE'}")
                print(f"   {'-' * 8} {'-' * 6} {'-' * 6} {'-' * 20}")
                for proc in python_processes:
                    print(
                        f"   {proc['pid']:<8} {proc['cpu_percent']:<6.1f} {proc['memory_percent']:<6.1f} {proc['name']}"
                    )
            else:
                print("   No hay procesos Python activos")

            # Conexiones de red
            connections = psutil.net_connections()
            active_connections = [
                conn for conn in connections if conn.status == "ESTABLISHED"
            ]
            print(
                f"\n🌐 CONEXIONES DE RED: {len(active_connections)} conexiones establecidas"
            )

            # Información del proceso actual
            current_process = psutil.Process()
            print(f"\n⚡ PROCESO ACTUAL (PID: {current_process.pid}):")
            print(f"   CPU: {current_process.cpu_percent():.1f}%")
            print(f"   Memoria: {current_process.memory_percent():.1f}%")
            print(f"   Hilos: {current_process.num_threads()}")
            print(f"   Estado: {current_process.status()}")

            print(f"\n📅 Última actualización: {time.strftime('%H:%M:%S')}")
            print("=" * 50)

        except Exception as e:
            print(f"Error en el monitor: {e}")

        # Espero 10 segundos antes de la siguiente monitorización
        time.sleep(10)


def get_system_stats():
    # Función para obtener estadísticas del sistema (puede ser usada externamente)
    try:
        cpu_percent = psutil.cpu_percent(interval=1)
        memory = psutil.virtual_memory()
        python_processes = get_python_processes()

        return {
            "cpu_percent": cpu_percent,
            "memory_percent": memory.percent,
            "memory_used_mb": memory.used / 1024 / 1024,
            "python_processes_count": len(python_processes),
            "python_processes": python_processes,
        }
    except Exception as e:
        print(f"Error obteniendo estadísticas: {e}")
        return None


if __name__ == "__main__":
    # Si ejecuto este archivo directamente, inicio el monitor
    print("Iniciando monitor de procesos en modo standalone...")
    start_monitor()

    # Mantengo el programa corriendo
    try:
        while True:
            time.sleep(1)
    except KeyboardInterrupt:
        print("\nMonitor detenido")
