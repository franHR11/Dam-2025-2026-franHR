# run_server.py - Script fácil para iniciar el servidor
import os
import sys


def main():
    print("🚀 Iniciando ChatServer Seguro...")

    # Verifico que estamos en el directorio correcto
    if not os.path.exists("server"):
        print("❌ Error: No se encuentra el directorio 'server'")
        print("Ejecuta este script desde la raíz del proyecto")
        return

    # Verifico Python version
    if sys.version_info < (3, 8):
        print("❌ Error: Se requiere Python 3.8 o superior")
        print(f"Versión actual: {sys.version}")
        return

    # Verifico dependencias
    try:
        import psutil
        from Crypto import Random

        print("✅ Dependencias verificadas")
    except ImportError as e:
        print(f"❌ Error: Falta dependencia - {e}")
        print("Ejecuta: pip install -r requirements.txt")
        return

    # Añado el path del proyecto al sys.path
    project_root = os.path.dirname(os.path.abspath(__file__))
    server_path = os.path.join(project_root, "server")

    if project_root not in sys.path:
        sys.path.insert(0, project_root)
    if server_path not in sys.path:
        sys.path.insert(0, server_path)

    print("📂 Cambiado al directorio del servidor")
    print("🔧 Iniciando servidor principal...")
    print("📝 Los logs se guardarán en ../logs/")
    print("🖥️  El monitor mostrará estadísticas cada 10 segundos")
    print("⏹️  Presiona Ctrl+C para detener el servidor")
    print("-" * 50)

    try:
        # Importo e inicio el servidor
        from server_main import main

        main()
    except KeyboardInterrupt:
        print("\n🛑 Servidor detenido por el usuario")
    except Exception as e:
        print(f"❌ Error ejecutando el servidor: {e}")
    finally:
        print("👋 Saliendo...")


if __name__ == "__main__":
    main()
