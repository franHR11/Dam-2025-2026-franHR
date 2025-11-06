#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Archivo de prueba para diagnosticar problemas en SmartFit
Este archivo ayudará a identificar dónde está fallando la aplicación
"""

import os
import sys
import tkinter as tk
import traceback
from tkinter import messagebox, ttk

# Añadir el directorio src al path
sys.path.append(os.path.join(os.path.dirname(__file__), "src"))


def test_imports():
    """Prueba todas las importaciones necesarias"""
    print("🔍 Probando importaciones...")

    try:
        from src.models.database import DatabaseManager

        print("✅ DatabaseManager importado correctamente")
    except Exception as e:
        print(f"❌ Error importando DatabaseManager: {e}")
        return False

    try:
        from src.models.user import UserManager

        print("✅ UserManager importado correctamente")
    except Exception as e:
        print(f"❌ Error importando UserManager: {e}")
        return False

    try:
        from src.gui.main_window import MainWindow

        print("✅ MainWindow importado correctamente")
    except Exception as e:
        print(f"❌ Error importando MainWindow: {e}")
        return False

    try:
        from src.gui.dialogs.user_dialog import UserDialog

        print("✅ UserDialog importado correctamente")
    except Exception as e:
        print(f"❌ Error importando UserDialog: {e}")
        return False

    try:
        from src.gui.widgets.info_card import InfoCard

        print("✅ InfoCard importado correctamente")
    except Exception as e:
        print(f"❌ Error importando InfoCard: {e}")
        return False

    print("✅ Todas las importaciones OK")
    return True


def test_database():
    """Prueba la inicialización de la base de datos"""
    print("\n🗄️ Probando base de datos...")

    try:
        from src.models.database import DatabaseManager

        db_manager = DatabaseManager("test_smartfit.db")
        print("✅ DatabaseManager creado correctamente")

        # Probar conexión
        if db_manager.check_connection():
            print("✅ Conexión a BD exitosa")
        else:
            print("❌ Error en conexión a BD")
            return False

        # Crear tablas
        db_manager.create_tables()
        print("✅ Tablas creadas correctamente")

        # Inicializar datos
        db_manager.initialize_data()
        print("✅ Datos de ejemplo inicializados")

        return db_manager
    except Exception as e:
        print(f"❌ Error en base de datos: {e}")
        traceback.print_exc()
        return False


def test_user_manager(db_manager):
    """Prueba el gestor de usuarios"""
    print("\n👤 Probando gestor de usuarios...")

    try:
        from src.models.user import UserManager

        user_manager = UserManager(db_manager)
        print("✅ UserManager creado correctamente")

        # Probar creación de usuario
        user_id = user_manager.crear_usuario(
            "Usuario Test", 25, 70, 175, "Test objetivo"
        )
        if user_id > 0:
            print(f"✅ Usuario creado con ID: {user_id}")

            # Probar obtener usuario
            user = user_manager.obtener_usuario_por_id(user_id)
            if user:
                print(f"✅ Usuario obtenido: {user['nombre']}")
            else:
                print("❌ No se pudo obtener el usuario creado")
                return False
        else:
            print("❌ No se pudo crear el usuario")
            return False

        return user_manager
    except Exception as e:
        print(f"❌ Error en UserManager: {e}")
        traceback.print_exc()
        return False


def test_tkinter():
    """Prueba la inicialización de Tkinter"""
    print("\n🖼️ Probando Tkinter...")

    try:
        root = tk.Tk()
        print("✅ Ventana Tkinter creada")

        # Probar widgets básicos
        label = ttk.Label(root, text="Prueba")
        print("✅ Widget Label creado")

        button = ttk.Button(root, text="Prueba")
        print("✅ Widget Button creado")

        # No mostrar la ventana, solo probar
        root.withdraw()
        root.destroy()
        print("✅ Tkinter funciona correctamente")

        return True
    except Exception as e:
        print(f"❌ Error con Tkinter: {e}")
        traceback.print_exc()
        return False


def test_main_window_creation(db_manager, user_manager):
    """Prueba la creación de la ventana principal"""
    print("\n🪟 Probando creación de ventana principal...")

    try:
        from src.gui.main_window import MainWindow

        # Crear ventana raíz
        root = tk.Tk()
        root.withdraw()  # Ocultar mientras probamos
        print("✅ Ventana raíz creada")

        # Intentar crear MainWindow
        main_window = MainWindow(root, db_manager, user_manager)
        print("✅ MainWindow creado correctamente")

        # Limpiar
        root.destroy()
        print("✅ Ventana principal funciona")

        return True
    except Exception as e:
        print(f"❌ Error creando MainWindow: {e}")
        traceback.print_exc()
        return False


def test_simple_gui():
    """Prueba una GUI simple"""
    print("\n🎨 Probando GUI simple...")

    try:
        root = tk.Tk()
        root.title("Prueba SmartFit")
        root.geometry("400x300")

        # Frame principal
        main_frame = ttk.Frame(root, padding="20")
        main_frame.pack(fill=tk.BOTH, expand=True)

        # Título
        title_label = ttk.Label(
            main_frame, text="🏃‍♂️ SmartFit - Prueba", font=("Arial", 16, "bold")
        )
        title_label.pack(pady=(0, 20))

        # Mensaje
        message_label = ttk.Label(
            main_frame,
            text="Si ves este mensaje, Tkinter funciona correctamente",
            font=("Arial", 10),
        )
        message_label.pack(pady=(0, 20))

        # Botón de prueba
        def test_button_click():
            messagebox.showinfo("Prueba", "¡Los botones funcionan!")

        test_button = ttk.Button(
            main_frame, text="🧪 Probar Botón", command=test_button_click
        )
        test_button.pack(pady=10)

        # Botón cerrar
        close_button = ttk.Button(main_frame, text="❌ Cerrar", command=root.destroy)
        close_button.pack(pady=(20, 0))

        print("✅ GUI simple creada - mostrando ventana...")
        print("   (Cierra la ventana para continuar con las pruebas)")

        root.mainloop()
        print("✅ GUI simple funciona correctamente")

        return True
    except Exception as e:
        print(f"❌ Error en GUI simple: {e}")
        traceback.print_exc()
        return False


def run_comprehensive_test():
    """Ejecuta una prueba comprehensiva del sistema"""
    print("🧪 INICIANDO PRUEBAS COMPREHENSIVAS DE SMARTFIT")
    print("=" * 50)

    # Prueba 1: Importaciones
    if not test_imports():
        print("\n❌ FALLO EN IMPORTACIONES - No se puede continuar")
        return False

    # Prueba 2: Tkinter
    if not test_tkinter():
        print("\n❌ FALLO EN TKINTER - No se puede continuar")
        return False

    # Prueba 3: Base de datos
    db_manager = test_database()
    if not db_manager:
        print("\n❌ FALLO EN BASE DE DATOS - No se puede continuar")
        return False

    # Prueba 4: Gestor de usuarios
    user_manager = test_user_manager(db_manager)
    if not user_manager:
        print("\n❌ FALLO EN USER MANAGER - No se puede continuar")
        return False

    # Prueba 5: GUI simple
    if not test_simple_gui():
        print("\n❌ FALLO EN GUI SIMPLE")
        return False

    # Prueba 6: Ventana principal (sin mostrar)
    if not test_main_window_creation(db_manager, user_manager):
        print("\n❌ FALLO EN MAIN WINDOW")
        return False

    print("\n" + "=" * 50)
    print("🎉 TODAS LAS PRUEBAS COMPLETADAS")
    print("✅ El sistema parece funcionar correctamente")
    print("\n💡 Si main.py aún no funciona, el problema podría estar en:")
    print("   - Alguna lógica específica en el flujo principal")
    print("   - Algún widget o diálogo específico")
    print("   - Algún evento o callback")

    # Limpiar archivo de prueba
    try:
        if os.path.exists("test_smartfit.db"):
            os.remove("test_smartfit.db")
            print("🧹 Archivo de prueba de BD eliminado")
    except:
        pass

    return True


def main():
    """Función principal de las pruebas"""
    try:
        print("🏃‍♂️ DIAGNÓSTICO DE SMARTFIT")
        print("Este script ayudará a identificar problemas en la aplicación")
        print()

        # Preguntar qué prueba ejecutar
        print("Opciones de prueba:")
        print("1. Prueba comprehensiva (recomendada)")
        print("2. Solo GUI simple")
        print("3. Solo importaciones")

        try:
            choice = input("\nSelecciona una opción (1-3): ").strip()
        except KeyboardInterrupt:
            print("\n🛑 Prueba cancelada por el usuario")
            return

        if choice == "2":
            test_simple_gui()
        elif choice == "3":
            test_imports()
        else:
            # Por defecto, prueba comprehensiva
            run_comprehensive_test()

    except Exception as e:
        print(f"❌ Error inesperado en las pruebas: {e}")
        traceback.print_exc()
    finally:
        print("\n🏁 Pruebas finalizadas")


if __name__ == "__main__":
    main()
