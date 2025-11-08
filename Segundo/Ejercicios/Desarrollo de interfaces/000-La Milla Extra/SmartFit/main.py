#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
SmartFit - Gestor Multiplataforma de Fitness
Archivo principal - Versión completa con todas las funcionalidades

Desarrollado por Fran - DAM 2024
Ejercicio de la Milla Extra - Desarrollo de Interfaces
"""

import os
import sys
import tkinter as tk
from tkinter import messagebox, ttk

# Añadir el directorio src al path
sys.path.append(os.path.join(os.path.dirname(__file__), "src"))

# Importar modelos
from src.gui.help_section import HelpSection

# Importar interfaz principal
from src.gui.main_window import MainWindow
from src.gui.nutrition_section import NutritionSection
from src.gui.reports_section import ReportsSection

# Importar secciones individuales
from src.gui.user_section import UserSection
from src.gui.workout_section import WorkoutSection
from src.models.database import DatabaseManager
from src.models.user import UserManager


class SmartFitApp:
    """
    Clase principal de la aplicación SmartFit
    Gestiona la inicialización y coordinación de todos los componentes
    """

    def __init__(self):
        """Inicializa la aplicación"""
        self.db_manager = None
        self.user_manager = None
        self.main_window = None
        self.root = None

    def initialize(self):
        """Inicializa todos los componentes de la aplicación"""
        try:
            print("🚀 Inicializando SmartFit...")

            # Inicializar base de datos
            self.db_manager = DatabaseManager("smartfit.db")
            if not self.db_manager.check_connection():
                raise Exception("No se pudo conectar a la base de datos")

            # Crear tablas e insertar datos de ejemplo
            self.db_manager.create_tables()
            self.db_manager.initialize_data()
            print("✅ Base de datos inicializada correctamente")

            # Inicializar gestor de usuarios
            self.user_manager = UserManager(self.db_manager)
            print("✅ Gestor de usuarios inicializado")

            # Crear ventana raíz
            self.root = tk.Tk()
            self.root.withdraw()  # Ocultar ventana mientras se configura

            # Configurar ventana principal
            self.setup_main_window()
            print("✅ Interfaz principal configurada")

            # Mostrar selector de usuario si no hay usuarios
            if not self.user_manager.listar_usuarios():
                self.show_user_creation_dialog()
            else:
                # Mostrar ventana principal
                self.root.deiconify()
                self.main_window.show_user_selection()

            print("🏃‍♂️ SmartFit iniciado correctamente - ¡Listo para usar!")
            return True

        except Exception as e:
            print(f"❌ Error al inicializar SmartFit: {e}")
            messagebox.showerror(
                "Error de Inicialización",
                f"No se pudo iniciar la aplicación:\n\n{e}\n\n"
                "Verifica que tienes los permisos necesarios y que no hay otro "
                "proceso usando la base de datos.",
            )
            return False

    def setup_main_window(self):
        """Configura la ventana principal"""
        # Configurar ventana
        self.root.title("SmartFit - Tu Entrenador Personal Digital")
        self.root.geometry("1200x800")
        self.root.minsize(1000, 700)

        # Configurar estilo
        style = ttk.Style()
        style.theme_use("clam")

        # Configurar cierre de aplicación
        self.root.protocol("WM_DELETE_WINDOW", self.on_closing)

        # Crear ventana principal
        self.main_window = MainWindow(self.root, self.db_manager, self.user_manager)

    def show_user_creation_dialog(self):
        """Muestra el diálogo de creación de usuario si no hay usuarios"""
        dialog = UserCreationDialog(self.root, self.user_manager, self.db_manager)
        self.root.wait_window(dialog.dialog)

        # Si se creó un usuario, mostrar la ventana principal
        if dialog.user_created:
            self.root.deiconify()
            if self.main_window:
                self.main_window.current_user = dialog.created_user_id
                self.main_window.refresh_current_view()

    def run(self):
        """Ejecuta la aplicación"""
        if self.initialize():
            try:
                self.root.mainloop()
            except KeyboardInterrupt:
                print("\n🛑 Aplicación interrumpida por el usuario")
            except Exception as e:
                print(f"❌ Error durante la ejecución: {e}")
                messagebox.showerror("Error", f"Error durante la ejecución:\n{e}")
            finally:
                self.cleanup()

    def cleanup(self):
        """Limpia los recursos al cerrar"""
        print("🔄 Cerrando SmartFit...")
        if self.db_manager:
            self.db_manager.close()
        print("👋 SmartFit cerrado correctamente")

    def on_closing(self):
        """Maneja el cierre de la aplicación"""
        if messagebox.askokcancel(
            "Salir",
            "¿Estás seguro de que quieres salir de SmartFit?\n\n"
            "Todos los datos se guardarán automáticamente.",
        ):
            self.cleanup()
            self.root.destroy()


class UserCreationDialog:
    """Diálogo para crear el primer usuario"""

    def __init__(self, parent, user_manager, db_manager):
        self.user_manager = user_manager
        self.db_manager = db_manager
        self.user_created = False
        self.created_user_id = None

        # Variables del formulario
        self.nombre_var = tk.StringVar()
        self.edad_var = tk.StringVar()
        self.peso_var = tk.StringVar()
        self.altura_var = tk.StringVar()
        self.objetivo_var = tk.StringVar()

        # Crear diálogo
        self.create_dialog(parent)

    def create_dialog(self, parent):
        """Crea el diálogo de creación de usuario"""
        self.dialog = tk.Toplevel(parent)
        self.dialog.title("Crear Primer Usuario")
        self.dialog.geometry("500x400")
        self.dialog.transient(parent)
        self.dialog.grab_set()

        # Centrar ventana
        self.dialog.update_idletasks()
        x = (self.dialog.winfo_screenwidth() // 2) - (500 // 2)
        y = (self.dialog.winfo_screenheight() // 2) - (400 // 2)
        self.dialog.geometry(f"500x400+{x}+{y}")

        # Frame principal
        main_frame = ttk.Frame(self.dialog, padding="20")
        main_frame.pack(fill=tk.BOTH, expand=True)

        # Título
        title_label = ttk.Label(
            main_frame, text="👤 Bienvenido a SmartFit", font=("Arial", 16, "bold")
        )
        title_label.pack(pady=(0, 10))

        subtitle_label = ttk.Label(
            main_frame, text="Vamos a crear tu perfil para comenzar", font=("Arial", 10)
        )
        subtitle_label.pack(pady=(0, 20))

        # Formulario
        form_frame = ttk.Frame(main_frame)
        form_frame.pack(fill=tk.BOTH, expand=True, pady=(0, 20))

        # Campo nombre (obligatorio)
        ttk.Label(form_frame, text="Nombre *:").pack(anchor=tk.W, pady=(0, 5))
        nombre_entry = ttk.Entry(form_frame, textvariable=self.nombre_var, width=30)
        nombre_entry.pack(fill=tk.X, pady=(0, 15))

        # Campo edad
        ttk.Label(form_frame, text="Edad:").pack(anchor=tk.W, pady=(0, 5))
        edad_entry = ttk.Entry(form_frame, textvariable=self.edad_var, width=30)
        edad_entry.pack(fill=tk.X, pady=(0, 15))

        # Campo peso
        ttk.Label(form_frame, text="Peso (kg):").pack(anchor=tk.W, pady=(0, 5))
        peso_entry = ttk.Entry(form_frame, textvariable=self.peso_var, width=30)
        peso_entry.pack(fill=tk.X, pady=(0, 15))

        # Campo altura
        ttk.Label(form_frame, text="Altura (cm):").pack(anchor=tk.W, pady=(0, 5))
        altura_entry = ttk.Entry(form_frame, textvariable=self.altura_var, width=30)
        altura_entry.pack(fill=tk.X, pady=(0, 15))

        # Campo objetivo
        ttk.Label(form_frame, text="Objetivo:").pack(anchor=tk.W, pady=(0, 5))
        objetivo_combo = ttk.Combobox(
            form_frame,
            textvariable=self.objetivo_var,
            values=[
                "Perder peso",
                "Ganar músculo",
                "Mantenerse en forma",
                "Mejorar resistencia",
                "Aumentar flexibilidad",
            ],
            width=28,
            state="readonly",
        )
        objetivo_combo.pack(fill=tk.X, pady=(0, 20))
        objetivo_combo.set("Mantenerse en forma")

        # Botones
        buttons_frame = ttk.Frame(main_frame)
        buttons_frame.pack(fill=tk.X)

        ttk.Button(buttons_frame, text="Cancelar", command=self.dialog.destroy).pack(
            side=tk.RIGHT, padx=(10, 0)
        )

        ttk.Button(buttons_frame, text="Crear Perfil", command=self.create_user).pack(
            side=tk.RIGHT
        )

        # Enfocar campo nombre
        nombre_entry.focus()

        # Bind Enter key
        self.dialog.bind("<Return>", lambda e: self.create_user())
        self.dialog.bind("<Escape>", lambda e: self.dialog.destroy())

    def create_user(self):
        """Crea el usuario con los datos ingresados"""
        try:
            # Validar datos
            nombre = self.nombre_var.get().strip()
            if not nombre:
                messagebox.showerror("Error", "El nombre es obligatorio")
                return

            # Convertir datos opcionales
            edad = None
            peso = None
            altura = None

            if self.edad_var.get().strip():
                try:
                    edad = int(self.edad_var.get())
                except ValueError:
                    messagebox.showerror("Error", "La edad debe ser un número")
                    return

            if self.peso_var.get().strip():
                try:
                    peso = float(self.peso_var.get())
                except ValueError:
                    messagebox.showerror("Error", "El peso debe ser un número")
                    return

            if self.altura_var.get().strip():
                try:
                    altura = float(self.altura_var.get())
                except ValueError:
                    messagebox.showerror("Error", "La altura debe ser un número")
                    return

            objetivo = self.objetivo_var.get() or "Mantenerse en forma"

            # Crear usuario
            user_id = self.user_manager.crear_usuario(
                nombre=nombre, edad=edad, peso=peso, altura=altura, objetivo=objetivo
            )

            if user_id > 0:
                self.user_created = True
                self.created_user_id = user_id
                messagebox.showinfo(
                    "¡Éxito!",
                    f"¡Bienvenido a SmartFit, {nombre}!\n\n"
                    "Tu perfil ha sido creado correctamente.\n"
                    "Ya puedes comenzar a usar todas las funcionalidades.",
                )
                self.dialog.destroy()
            else:
                messagebox.showerror("Error", "No se pudo crear el usuario")

        except Exception as e:
            messagebox.showerror("Error", f"Error al crear el usuario:\n{e}")


def main():
    """Función principal de la aplicación"""
    try:
        # Crear y ejecutar aplicación
        app = SmartFitApp()
        app.run()

    except Exception as e:
        print(f"❌ Error crítico: {e}")
        if "tkinter" in sys.modules:
            try:
                messagebox.showerror(
                    "Error Crítico", f"Error crítico al iniciar la aplicación:\n\n{e}"
                )
            except:
                pass


if __name__ == "__main__":
    main()
