# test_system.py - Script de prueba completo del sistema
import os
import subprocess
import sys
import threading
import time
from datetime import datetime


def print_header(title):
    """Imprime un encabezado decorativo"""
    print("\n" + "=" * 60)
    print(f"🧪 {title}")
    print("=" * 60)


def check_python_version():
    """Verifica la versión de Python"""
    print("🐍 Verificando versión de Python...")
    if sys.version_info >= (3, 8):
        print(f"✅ Python {sys.version.split()[0]} - Compatible")
        return True
    else:
        print(f"❌ Python {sys.version.split()[0]} - Se requiere 3.8+")
        return False


def check_dependencies():
    """Verifica las dependencias necesarias"""
    print("📦 Verificando dependencias...")

    dependencies = {
        "tkinter": "Interfaz gráfica",
        "psutil": "Monitorización de procesos",
        "Crypto": "Criptografía (PyCryptodome)",
    }

    all_ok = True
    for module, description in dependencies.items():
        try:
            __import__(module)
            print(f"✅ {module} - {description}")
        except ImportError:
            print(f"❌ {module} - {description} (FALTANTE)")
            all_ok = False

    return all_ok


def check_file_structure():
    """Verifica la estructura de archivos"""
    print("📁 Verificando estructura de archivos...")

    required_files = [
        "server/server_main.py",
        "server/server_thread.py",
        "server/crypto_utils.py",
        "server/process_monitor.py",
        "server/log_service.py",
        "client/client_main.py",
        "client/client_thread.py",
        "config/settings.json",
        "requirements.txt",
        "README.md",
    ]

    all_ok = True
    for file_path in required_files:
        if os.path.exists(file_path):
            print(f"✅ {file_path}")
        else:
            print(f"❌ {file_path} (FALTANTE)")
            all_ok = False

    return all_ok


def test_crypto_system():
    """Prueba el sistema de criptografía"""
    print("🔐 Probando sistema de criptografía...")

    try:
        # Importo el módulo de criptografía
        sys.path.append("server")
        from crypto_utils import (
            decrypt_aes,
            decrypt_rsa,
            encrypt_aes,
            encrypt_rsa,
            generate_aes_key,
            generate_rsa_key_pair,
        )

        # Prueba RSA
        private_key, public_key = generate_rsa_key_pair()
        test_data = b"Mensaje de prueba RSA"
        encrypted_rsa = encrypt_rsa(test_data, public_key)
        decrypted_rsa = decrypt_rsa(encrypted_rsa, private_key)

        if decrypted_rsa == test_data:
            print("✅ RSA - Funciona correctamente")
        else:
            print("❌ RSA - Error en cifrado/descifrado")
            return False

        # Prueba AES
        aes_key = generate_aes_key()
        test_message = "Mensaje de prueba AES"
        encrypted_aes = encrypt_aes(test_message, aes_key)
        decrypted_aes = decrypt_aes(encrypted_aes, aes_key)

        if decrypted_aes == test_message:
            print("✅ AES - Funciona correctamente")
        else:
            print("❌ AES - Error en cifrado/descifrado")
            return False

        print("✅ Sistema criptográfico - Verificado exitosamente")
        return True

    except Exception as e:
        print(f"❌ Error en sistema criptográfico: {e}")
        return False


def test_config_loading():
    """Prueba la carga de configuración"""
    print("⚙️ Probando configuración...")

    try:
        import json

        with open("config/settings.json", "r") as f:
            config = json.load(f)

        # Verifico campos principales
        required_fields = ["server", "security", "logging", "monitoring"]
        for field in required_fields:
            if field in config:
                print(f"✅ Configuración '{field}' - Cargada")
            else:
                print(f"❌ Configuración '{field}' - Faltante")
                return False

        print("✅ Configuración - Cargada correctamente")
        return True

    except Exception as e:
        print(f"❌ Error cargando configuración: {e}")
        return False


def test_log_directory():
    """Prueba la creación del directorio de logs"""
    print("📝 Probando sistema de logs...")

    try:
        # Creo el directorio de logs
        os.makedirs("logs", exist_ok=True)

        # Prueba de escritura
        test_log_path = "logs/test.log"
        with open(test_log_path, "w") as f:
            f.write(f"Test log - {datetime.now()}\n")

        if os.path.exists(test_log_path):
            print("✅ Directorio de logs - Funciona correctamente")
            # Limpio el archivo de prueba
            os.remove(test_log_path)
            return True
        else:
            print("❌ Directorio de logs - Error de escritura")
            return False

    except Exception as e:
        print(f"❌ Error en sistema de logs: {e}")
        return False


def run_comprehensive_test():
    """Ejecuta todas las pruebas del sistema"""
    print_header("CHAT SERVER SEGURO - PRUEBA COMPLETA DEL SISTEMA")

    print(f"🕐 Iniciando pruebas - {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}")

    tests = [
        ("Versión de Python", check_python_version),
        ("Dependencias", check_dependencies),
        ("Estructura de archivos", check_file_structure),
        ("Sistema de criptografía", test_crypto_system),
        ("Carga de configuración", test_config_loading),
        ("Sistema de logs", test_log_directory),
    ]

    results = []
    for test_name, test_func in tests:
        print(f"\n🔍 Ejecutando: {test_name}")
        result = test_func()
        results.append((test_name, result))
        time.sleep(0.5)  # Pequeña pausa entre pruebas

    # Resumen de resultados
    print_header("RESUMEN DE PRUEBAS")

    passed = 0
    total = len(results)

    for test_name, result in results:
        status = "✅ PASÓ" if result else "❌ FALLÓ"
        print(f"{status:<8} {test_name}")
        if result:
            passed += 1

    print(f"\n📊 Resultado final: {passed}/{total} pruebas pasadas")

    if passed == total:
        print("🎉 ¡Todas las pruebas pasaron! El sistema está listo para usar.")
        print("\n📋 Próximos pasos:")
        print("1. Ejecuta: python run_server.py (para iniciar el servidor)")
        print("2. Ejecuta: python run_client.py (para iniciar clientes)")
        print("3. ¡Disfruta del chat seguro!")
    else:
        print("⚠️ Algunas pruebas fallaron. Revisa los errores antes de continuar.")

    return passed == total


def show_system_info():
    """Muestra información del sistema"""
    print_header("INFORMACIÓN DEL SISTEMA")

    print(f"🐍 Versión de Python: {sys.version.split()[0]}")
    print(f"🖥️  Sistema operativo: {os.name}")
    print(f"📂 Directorio actual: {os.getcwd()}")
    print(f"🕐 Fecha y hora: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}")

    try:
        import psutil

        print(
            f"💾 Memoria disponible: {psutil.virtual_memory().available / 1024 / 1024:.1f} MB"
        )
        print(f"🔥 Uso de CPU: {psutil.cpu_percent()}%")
    except ImportError:
        print("⚠️ psutil no disponible para estadísticas del sistema")


def main():
    """Función principal del script de prueba"""
    print("🚀 ChatServer Seguro - Sistema de Pruebas")
    print("🛡️ Verificación completa del sistema antes del uso")

    # Muestro información del sistema
    show_system_info()

    # Ejecuto las pruebas
    success = run_comprehensive_test()

    if success:
        print("\n✨ ¡El sistema está perfectamente configurado!")
        return 0
    else:
        print("\n❌ Hay problemas que deben resolverse antes de usar el sistema.")
        return 1


if __name__ == "__main__":
    exit_code = main()
    sys.exit(exit_code)
