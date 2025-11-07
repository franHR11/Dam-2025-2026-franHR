# run_client.py - Script fácil para iniciar el cliente
import os
import sys


def main():
    print("🚀 Iniciando Cliente de Chat Seguro...")

    # Verifico que estamos en el directorio correcto
    if not os.path.exists("client"):
        print("❌ Error: No se encuentra el directorio 'client'")
        print("Ejecuta este script desde la raíz del proyecto")
        return

    # Verifico Python version
    if sys.version_info < (3, 8):
        print("❌ Error: Se requiere Python 3.8 o superior")
        print(f"Versión actual: {sys.version}")
        return

    # Verifico dependencias
    try:
        from Crypto import Random

        print("✅ Dependencias verificadas")
    except ImportError as e:
        print(f"❌ Error: Falta dependencia - {e}")
        print("Ejecuta: pip install -r requirements.txt")
        return

    # Verifico tkinter
    try:
        import tkinter as tk

        print("✅ Tkinter disponible")
    except ImportError:
        print("❌ Error: Tkinter no está disponible")
        if sys.platform.startswith("linux"):
            print("En Linux, instala: sudo apt-get install python3-tk")
        return

    # Añado el path del proyecto al sys.path
    project_root = os.path.dirname(os.path.abspath(__file__))
    client_path = os.path.join(project_root, "client")

    if project_root not in sys.path:
        sys.path.insert(0, project_root)
    if client_path not in sys.path:
        sys.path.insert(0, client_path)

    print("📂 Cambiado al directorio del cliente")
    print("🖥️  Iniciando interfaz gráfica del cliente...")
    print("🔒 La comunicación estará cifrada con RSA+AES")
    print("⏹️  Cierra la ventana para desconectar")
    print("-" * 50)

    try:
        # Importo e inicio el cliente
        import client_main

        client = client_main.ChatClient()
        client.run()
    except KeyboardInterrupt:
        print("\n🛑 Cliente interrumpido por el usuario")
    except Exception as e:
        print(f"❌ Error ejecutando el cliente: {e}")
    finally:
        print("👋 Saliendo...")


if __name__ == "__main__":
    main()
