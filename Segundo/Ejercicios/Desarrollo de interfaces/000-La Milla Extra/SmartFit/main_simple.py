#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
SmartFit - Versión simplificada y funcional
Desarrollado por Fran - DAM 2024
Ejercicio de la Milla Extra - Desarrollo de Interfaces
"""

import os
import sys
import tkinter as tk
from datetime import datetime
from tkinter import messagebox, ttk

# Añadir el directorio src al path
sys.path.append(os.path.join(os.path.dirname(__file__), "src"))

# Importar modelos
from src.gui.help_section import HelpSection
from src.gui.nutrition_section import NutritionSection
from src.gui.reports_section import ReportsSection

# Importar secciones
from src.gui.user_section import UserSection
from src.gui.workout_section import WorkoutSection
from src.models.database import DatabaseManager
from src.models.user import UserManager


class SmartFitApp:
    """
    Aplicación principal de SmartFit - Versión simplificada
    Gestiona la inicialización y coordinación de todos los componentes
    """

    def __init__(self):
        """Inicializa la aplicación"""
        self.db_manager = None
        self.user_manager = None
        self.root = None
        self.current_user = None
        self.notebook = None

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
            self.root.title("SmartFit - Tu Entrenador Personal Digital")
            self.root.geometry("1000x700")
            self.root.minsize(800, 600)

            # Configurar estilo
            style = ttk.Style()
            style.theme_use("clam")

            # Configurar cierre de aplicación
            self.root.protocol("WM_DELETE_WINDOW", self.on_closing)

            # Crear interfaz principal
            self.create_main_interface()
            print("✅ Interfaz principal creada")

            # Verificar si hay usuarios
            usuarios = self.user_manager.listar_usuarios()
            if not usuarios:
                print("👤 No hay usuarios, creando primer usuario...")
                self.show_user_creation_dialog()
            else:
                # Usar el primer usuario
                self.current_user = usuarios[0]
                print(f"👤 Usando usuario: {self.current_user['nombre']}")

            print("🏃‍♂️ SmartFit iniciado correctamente - ¡Listo para usar!")
            return True

        except Exception as e:
            print(f"❌ Error al inicializar SmartFit: {e}")
            import traceback

            traceback.print_exc()
            messagebox.showerror(
                "Error de Inicialización",
                f"No se pudo iniciar la aplicación:\n\n{e}\n\n"
                "Verifica que tienes los permisos necesarios.",
            )
            return False

    def create_main_interface(self):
        """Crea la interfaz principal de la aplicación"""
        # Frame principal
        main_frame = ttk.Frame(self.root, padding="10")
        main_frame.pack(fill=tk.BOTH, expand=True)

        # Header con título y información del usuario
        self.create_header(main_frame)

        # Crear notebook (pestañas)
        self.notebook = ttk.Notebook(main_frame)
        self.notebook.pack(fill=tk.BOTH, expand=True, pady=(10, 0))

        # Crear las secciones
        self.create_sections()

        # Bind cambio de pestaña
        self.notebook.bind("<<NotebookTabChanged>>", self.on_tab_changed)

    def create_header(self, parent):
        """Crea el header con título e información del usuario"""
        header_frame = ttk.Frame(parent)
        header_frame.pack(fill=tk.X, pady=(0, 10))

        # Título principal
        title_label = ttk.Label(
            header_frame,
            text="🏃‍♂️ SmartFit",
            font=("Arial", 20, "bold"),
            foreground="#4A90E2",
        )
        title_label.pack(side=tk.LEFT)

        # Información del usuario y controles
        user_frame = ttk.Frame(header_frame)
        user_frame.pack(side=tk.RIGHT)

        # Label de usuario actual
        self.user_label = ttk.Label(
            user_frame, text="Usuario: No seleccionado", font=("Arial", 10)
        )
        self.user_label.pack(side=tk.RIGHT, padx=(0, 10))

        # Botón cambiar usuario
        ttk.Button(
            user_frame, text="👤 Cambiar Usuario", command=self.show_user_selection
        ).pack(side=tk.RIGHT, padx=(0, 5))

        # Botón crear usuario
        ttk.Button(
            user_frame, text="➕ Nuevo Usuario", command=self.show_user_creation_dialog
        ).pack(side=tk.RIGHT, padx=(0, 5))

    def create_sections(self):
        """Crea todas las secciones de la aplicación"""
        # Verificar que hay usuario actual
        if not self.current_user:
            print("⚠️ No hay usuario actual, saltando creación de secciones")
            return

        try:
            # Sección de Usuarios
            self.user_section = UserSection(
                self.notebook, self.db_manager, self.user_manager, self
            )
            self.notebook.add(self.user_section.frame, text="👤 Usuarios")
            print("✅ Sección Usuarios creada")

            # Cargar datos de usuarios
            if hasattr(self.user_section, "load_user_data"):
                self.user_section.load_user_data()

            # Sección de Entrenamientos
            self.workout_section = WorkoutSection(
                self.notebook, self.db_manager, self.user_manager, self
            )
            self.notebook.add(self.workout_section.frame, text="💪 Entrenamientos")
            print("✅ Sección Entrenamientos creada")

            # Cargar datos de entrenamientos
            if hasattr(self.workout_section, "load_user_data"):
                self.workout_section.load_user_data()

            # Sección de Nutrición
            self.nutrition_section = NutritionSection(
                self.notebook, self.db_manager, self.user_manager, self
            )
            self.notebook.add(self.nutrition_section.frame, text="🥗 Nutrición")
            print("✅ Sección Nutrición creada")

            # Cargar datos de nutrición
            if hasattr(self.nutrition_section, "load_user_data"):
                self.nutrition_section.load_user_data()
                print("✅ Datos de nutrición cargados")

            # Sección de Informes
            self.reports_section = ReportsSection(
                self.notebook, self.db_manager, self.user_manager, self
            )
            self.notebook.add(self.reports_section.frame, text="📊 Informes")
            print("✅ Sección Informes creada")

            # Cargar datos de informes
            if hasattr(self.reports_section, "load_user_data"):
                self.reports_section.load_user_data()

            # Sección de Ayuda
            self.help_section = HelpSection(
                self.notebook, self.db_manager, self.user_manager, self
            )
            self.notebook.add(self.help_section.frame, text="❓ Ayuda")
            print("✅ Sección Ayuda creada")

        except Exception as e:
            print(f"❌ Error creando secciones: {e}")
            import traceback

            traceback.print_exc()

            # Crear sección de error
            error_frame = ttk.Frame(self.notebook)
            self.notebook.add(error_frame, text="❌ Error")

            error_label = ttk.Label(
                error_frame,
                text=f"Error al cargar las secciones:\n{e}",
                font=("Arial", 12),
                foreground="red",
            )
            error_label.pack(expand=True)

    def update_user_info(self):
        """Actualiza la información del usuario en el header"""
        if self.current_user:
            self.user_label.config(text=f"Usuario: {self.current_user['nombre']}")
        else:
            self.user_label.config(text="Usuario: No seleccionado")

    def show_user_creation_dialog(self):
        """Muestra el diálogo de creación de usuario"""
        dialog = UserCreationDialog(self.root, self.user_manager, self.db_manager)
        self.root.wait_window(dialog.dialog)

        if dialog.user_created:
            self.current_user = self.user_manager.obtener_usuario_por_id(
                dialog.created_user_id
            )
            self.update_user_info()

            # Recargar las secciones con el nuevo usuario
            self.reload_sections()

            messagebox.showinfo(
                "Éxito",
                f"¡Usuario '{self.current_user['nombre']}' creado correctamente!\n"
                "Ya puedes comenzar a usar todas las funcionalidades.",
            )

    def show_user_selection(self):
        """Muestra el diálogo de selección de usuario"""
        usuarios = self.user_manager.listar_usuarios()
        if not usuarios:
            messagebox.showinfo("Info", "No hay usuarios registrados. Crea el primero.")
            self.show_user_creation_dialog()
            return

        # Crear diálogo simple de selección
        dialog = tk.Toplevel(self.root)
        dialog.title("Seleccionar Usuario")
        dialog.geometry("400x300")
        dialog.transient(self.root)
        dialog.grab_set()

        # Centrar ventana
        dialog.update_idletasks()
        x = (dialog.winfo_screenwidth() // 2) - (400 // 2)
        y = (dialog.winfo_screenheight() // 2) - (300 // 2)
        dialog.geometry(f"400x300+{x}+{y}")

        # Frame principal
        main_frame = ttk.Frame(dialog, padding="20")
        main_frame.pack(fill=tk.BOTH, expand=True)

        # Título
        ttk.Label(
            main_frame, text="👤 Seleccionar Usuario", font=("Arial", 14, "bold")
        ).pack(pady=(0, 20))

        # Lista de usuarios
        listbox = tk.Listbox(main_frame, font=("Arial", 10))
        listbox.pack(fill=tk.BOTH, expand=True, pady=(0, 10))

        # Llenar lista
        for i, usuario in enumerate(usuarios):
            listbox.insert(tk.END, f"{usuario['nombre']} (ID: {usuario['id']})")

        # Botones
        buttons_frame = ttk.Frame(main_frame)
        buttons_frame.pack(fill=tk.X)

        def select_user():
            selection = listbox.curselection()
            if selection:
                index = selection[0]
                self.current_user = usuarios[index]
                self.update_user_info()
                self.reload_sections()
                dialog.destroy()
                messagebox.showinfo(
                    "Éxito", f"Usuario seleccionado: {self.current_user['nombre']}"
                )
            else:
                messagebox.showwarning("Advertencia", "Selecciona un usuario")

        ttk.Button(buttons_frame, text="Seleccionar", command=select_user).pack(
            side=tk.RIGHT, padx=(5, 0)
        )
        ttk.Button(buttons_frame, text="Cancelar", command=dialog.destroy).pack(
            side=tk.RIGHT
        )
        ttk.Button(
            buttons_frame,
            text="➕ Nuevo",
            command=lambda: [dialog.destroy(), self.show_user_creation_dialog()],
        ).pack(side=tk.LEFT)

    def reload_sections(self):
        """Recarga las secciones con el usuario actual"""
        # Limpiar notebook
        for tab in self.notebook.tabs():
            self.notebook.forget(tab)

        # Recrear secciones
        self.create_sections()

        # Seleccionar primera pestaña
        if self.notebook.tabs():
            self.notebook.select(0)

    def on_tab_changed(self, event):
        """Maneja el cambio de pestaña"""
        # Actualizar contenido si es necesario
        pass

    def on_closing(self):
        """Maneja el cierre de la aplicación"""
        if messagebox.askokcancel(
            "Salir",
            "¿Estás seguro de que quieres salir de SmartFit?\n\n"
            "Todos los datos se guardarán automáticamente.",
        ):
            self.cleanup()
            self.root.destroy()

    def cleanup(self):
        """Limpia los recursos al cerrar"""
        print("🔄 Cerrando SmartFit...")
        if self.db_manager:
            self.db_manager.close()
        print("👋 SmartFit cerrado correctamente")

    def run(self):
        """Ejecuta la aplicación"""
        if self.initialize():
            try:
                self.root.mainloop()
            except KeyboardInterrupt:
                print("\n🛑 Aplicación interrumpida por el usuario")
            except Exception as e:
                print(f"❌ Error durante la ejecución: {e}")
                import traceback

                traceback.print_exc()
                messagebox.showerror("Error", f"Error durante la ejecución:\n{e}")
            finally:
                self.cleanup()


class UserCreationDialog:
    """Diálogo para crear usuarios"""

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
        self.dialog.title("Crear Nuevo Usuario - SmartFit")
        self.dialog.geometry("450x500")
        self.dialog.resizable(False, False)
        self.dialog.transient(parent)
        self.dialog.grab_set()

        # Centrar ventana
        self.dialog.update_idletasks()
        x = (self.dialog.winfo_screenwidth() // 2) - (450 // 2)
        y = (self.dialog.winfo_screenheight() // 2) - (500 // 2)
        self.dialog.geometry(f"450x500+{x}+{y}")

        # Frame principal
        main_frame = ttk.Frame(self.dialog, padding="20")
        main_frame.pack(fill=tk.BOTH, expand=True)

        # Título
        title_label = ttk.Label(
            main_frame, text="👤 Bienvenido a SmartFit", font=("Arial", 16, "bold")
        )
        title_label.pack(pady=(0, 20))

        subtitle_label = ttk.Label(
            main_frame, text="Vamos a crear tu perfil para comenzar", font=("Arial", 10)
        )
        subtitle_label.pack(pady=(0, 20))

        # Formulario
        form_frame = ttk.Frame(main_frame)
        form_frame.pack(fill=tk.BOTH, expand=True, pady=(0, 20))

        # Campo nombre (obligatorio)
        ttk.Label(form_frame, text="Nombre *:", font=("Arial", 10, "bold")).pack(
            anchor=tk.W, pady=(0, 5)
        )
        nombre_entry = ttk.Entry(
            form_frame, textvariable=self.nombre_var, width=40, font=("Arial", 10)
        )
        nombre_entry.pack(fill=tk.X, pady=(0, 15))

        # Campo edad
        ttk.Label(form_frame, text="Edad (años):", font=("Arial", 10)).pack(
            anchor=tk.W, pady=(0, 5)
        )
        edad_entry = ttk.Entry(
            form_frame, textvariable=self.edad_var, width=40, font=("Arial", 10)
        )
        edad_entry.pack(fill=tk.X, pady=(0, 15))

        # Campo peso
        ttk.Label(form_frame, text="Peso (kg):", font=("Arial", 10)).pack(
            anchor=tk.W, pady=(0, 5)
        )
        peso_entry = ttk.Entry(
            form_frame, textvariable=self.peso_var, width=40, font=("Arial", 10)
        )
        peso_entry.pack(fill=tk.X, pady=(0, 15))

        # Campo altura
        ttk.Label(form_frame, text="Altura (cm):", font=("Arial", 10)).pack(
            anchor=tk.W, pady=(0, 5)
        )
        altura_entry = ttk.Entry(
            form_frame, textvariable=self.altura_var, width=40, font=("Arial", 10)
        )
        altura_entry.pack(fill=tk.X, pady=(0, 15))

        # Campo objetivo
        ttk.Label(form_frame, text="Objetivo:", font=("Arial", 10)).pack(
            anchor=tk.W, pady=(0, 5)
        )
        objetivo_combo = ttk.Combobox(
            form_frame,
            textvariable=self.objetivo_var,
            values=[
                "Perder peso",
                "Ganar músculo",
                "Mantenerse en forma",
                "Mejorar resistencia",
                "Aumentar flexibilidad",
                "Rehabilitación",
            ],
            width=38,
            state="readonly",
            font=("Arial", 10),
        )
        objetivo_combo.pack(fill=tk.X, pady=(0, 20))
        objetivo_combo.set("Mantenerse en forma")

        # Botones
        buttons_frame = ttk.Frame(main_frame)
        buttons_frame.pack(fill=tk.X)

        ttk.Button(
            buttons_frame, text="Cancelar", command=self.dialog.destroy, width=12
        ).pack(side=tk.RIGHT, padx=(10, 0))

        ttk.Button(
            buttons_frame, text="Crear Perfil", command=self.create_user, width=15
        ).pack(side=tk.RIGHT)

        # Enfocar primer campo
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
        import traceback

        traceback.print_exc()

        if "tkinter" in sys.modules:
            try:
                messagebox.showerror(
                    "Error Crítico", f"Error crítico al iniciar la aplicación:\n\n{e}"
                )
            except:
                pass


if __name__ == "__main__":
    main()
