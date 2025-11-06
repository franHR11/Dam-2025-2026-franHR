# user_section.py - Sección de usuario para SmartFit
# Fran - Desarrollo de interfaces

import tkinter as tk
from datetime import datetime
from tkinter import messagebox, ttk
from typing import Any, Dict, Optional


class UserSection:
    """
    Sección de gestión de usuarios en la interfaz gráfica
    Muestra y permite editar el perfil del usuario, estadísticas y datos personales
    """

    def __init__(self, parent_notebook, db_manager, user_manager, main_window):
        """
        Inicializa la sección de usuario

        Args:
            parent_notebook: Notebook (pestañas) padre
            db_manager: Gestor de base de datos
            user_manager: Gestor de usuarios
            main_window: Ventana principal
        """
        self.db = db_manager
        self.user_manager = user_manager
        self.main_window = main_window
        self.current_user = None

        # Crear el frame principal
        self.frame = ttk.Frame(parent_notebook)

        # Variables para los campos del formulario
        self.nombre_var = tk.StringVar()
        self.edad_var = tk.StringVar()
        self.peso_var = tk.StringVar()
        self.altura_var = tk.StringVar()
        self.objetivo_var = tk.StringVar()

        # Crear la interfaz
        self.create_widgets()

    def create_widgets(self):
        """Crea todos los widgets de la sección"""
        # Frame principal con scroll
        self.create_scrollable_frame()

        # Crear secciones de la interfaz
        self.create_user_profile_section()
        self.create_statistics_section()
        self.create_health_metrics_section()
        self.create_goals_section()

    def create_scrollable_frame(self):
        """Crea un frame con scroll para manejar mucho contenido"""
        # Canvas para scroll
        self.canvas = tk.Canvas(self.frame)
        scrollbar = ttk.Scrollbar(
            self.frame, orient="vertical", command=self.canvas.yview
        )
        self.scrollable_frame = ttk.Frame(self.canvas)

        self.scrollable_frame.bind(
            "<Configure>",
            lambda e: self.canvas.configure(scrollregion=self.canvas.bbox("all")),
        )

        self.canvas.create_window((0, 0), window=self.scrollable_frame, anchor="nw")
        self.canvas.configure(yscrollcommand=scrollbar.set)

        # Empaquetar canvas y scrollbar
        self.canvas.pack(side="left", fill="both", expand=True)
        scrollbar.pack(side="right", fill="y")

        # Bind mousewheel
        self.canvas.bind("<MouseWheel>", self._on_mousewheel)

    def _on_mousewheel(self, event):
        """Maneja el scroll con la rueda del mouse"""
        self.canvas.yview_scroll(int(-1 * (event.delta / 120)), "units")

    def create_user_profile_section(self):
        """Crea la sección del perfil de usuario"""
        # Frame del perfil
        profile_frame = ttk.LabelFrame(
            self.scrollable_frame, text="👤 Perfil de Usuario", padding="20"
        )
        profile_frame.pack(fill=tk.X, padx=20, pady=10)

        # Frame para foto de perfil (placeholder)
        photo_frame = ttk.Frame(profile_frame)
        photo_frame.pack(side=tk.LEFT, padx=(0, 20))

        # Placeholder para foto
        photo_label = ttk.Label(
            photo_frame,
            text="👤",
            font=("Arial", 48),
            width=8,
            relief="solid",
            anchor="center",
        )
        photo_label.pack()

        # Cambiar foto button
        ttk.Button(photo_frame, text="Cambiar Foto", command=self.change_photo).pack(
            pady=(5, 0)
        )

        # Frame para información del usuario
        info_frame = ttk.Frame(profile_frame)
        info_frame.pack(side=tk.LEFT, fill=tk.BOTH, expand=True)

        # Título de bienvenida
        self.welcome_label = ttk.Label(
            info_frame, text="¡Bienvenido a SmartFit!", font=("Arial", 16, "bold")
        )
        self.welcome_label.pack(anchor=tk.W, pady=(0, 10))

        # Información básica
        basic_info_frame = ttk.Frame(info_frame)
        basic_info_frame.pack(fill=tk.X, pady=5)

        # Labels de información
        self.info_labels = {}
        info_items = [
            ("Nombre:", "nombre"),
            ("Edad:", "edad"),
            ("Peso:", "peso"),
            ("Altura:", "altura"),
            ("Objetivo:", "objetivo"),
        ]

        for i, (label_text, key) in enumerate(info_items):
            ttk.Label(
                basic_info_frame, text=label_text, font=("Arial", 10, "bold")
            ).grid(row=i, column=0, sticky=tk.W, pady=2)

            value_label = ttk.Label(
                basic_info_frame, text="No especificado", font=("Arial", 10)
            )
            value_label.grid(row=i, column=1, sticky=tk.W, padx=(10, 0), pady=2)
            self.info_labels[key] = value_label

        # Botones de acción
        button_frame = ttk.Frame(info_frame)
        button_frame.pack(fill=tk.X, pady=(10, 0))

        ttk.Button(
            button_frame, text="✏️ Editar Perfil", command=self.edit_profile
        ).pack(side=tk.LEFT, padx=(0, 10))

        ttk.Button(
            button_frame, text="📊 Ver Estadísticas", command=self.show_detailed_stats
        ).pack(side=tk.LEFT)

    def create_statistics_section(self):
        """Crea la sección de estadísticas"""
        # Frame de estadísticas
        stats_frame = ttk.LabelFrame(
            self.scrollable_frame, text="📊 Estadísticas de Entrenamiento", padding="20"
        )
        stats_frame.pack(fill=tk.X, padx=20, pady=10)

        # Grid para estadísticas
        stats_grid = ttk.Frame(stats_frame)
        stats_grid.pack(fill=tk.X)

        # Tarjetas de estadísticas
        self.stat_cards = {}
        stats_data = [
            ("💪", "Total Entrenamientos", "entrenamientos", "0"),
            ("🔥", "Calorías Quemadas", "calorias", "0 cal"),
            ("⏱️", "Tiempo Total", "tiempo", "0 min"),
            ("📋", "Rutinas Creadas", "rutinas", "0"),
        ]

        for i, (icon, title, key, default_value) in enumerate(stats_data):
            card = self.create_stat_card(stats_grid, icon, title, key, default_value, i)
            self.stat_cards[key] = card

    def create_stat_card(self, parent, icon, title, key, default_value, index):
        """Crea una tarjeta de estadística individual"""
        # Frame de la tarjeta
        card = ttk.Frame(parent, relief="solid", borderwidth=1)
        card.grid(row=0, column=index, padx=10, pady=5, sticky="ew")

        # Icono
        icon_label = ttk.Label(card, text=icon, font=("Arial", 24))
        icon_label.pack(pady=(10, 5))

        # Título
        title_label = ttk.Label(card, text=title, font=("Arial", 9, "bold"))
        title_label.pack()

        # Valor
        value_label = ttk.Label(card, text=default_value, font=("Arial", 12, "bold"))
        value_label.pack(pady=(5, 10))

        # Configurar grid para expandir
        parent.columnconfigure(index, weight=1)

        return {"value_label": value_label, "title": title}

    def create_health_metrics_section(self):
        """Crea la sección de métricas de salud"""
        # Frame de métricas de salud
        health_frame = ttk.LabelFrame(
            self.scrollable_frame, text="❤️ Métricas de Salud", padding="20"
        )
        health_frame.pack(fill=tk.X, padx=20, pady=10)

        # Grid para métricas
        health_grid = ttk.Frame(health_frame)
        health_grid.pack(fill=tk.X)

        # IMC
        imc_frame = ttk.Frame(health_grid)
        imc_frame.pack(side=tk.LEFT, fill=tk.BOTH, expand=True, padx=10)

        ttk.Label(
            imc_frame, text="IMC (Índice de Masa Corporal)", font=("Arial", 10, "bold")
        ).pack()
        self.imc_value_label = ttk.Label(
            imc_frame, text="--", font=("Arial", 16), foreground="#4A90E2"
        )
        self.imc_value_label.pack()
        self.imc_category_label = ttk.Label(
            imc_frame, text="No calculado", font=("Arial", 9)
        )
        self.imc_category_label.pack()

        # Calorías recomendadas
        calories_frame = ttk.Frame(health_grid)
        calories_frame.pack(side=tk.LEFT, fill=tk.BOTH, expand=True, padx=10)

        ttk.Label(
            calories_frame, text="Calorías Diarias", font=("Arial", 10, "bold")
        ).pack()
        self.calories_base_label = ttk.Label(
            calories_frame, text="--", font=("Arial", 16), foreground="#28A745"
        )
        self.calories_base_label.pack()
        ttk.Label(calories_frame, text="Metabolismo Basal", font=("Arial", 9)).pack()

        # Calorías objetivo
        target_frame = ttk.Frame(health_grid)
        target_frame.pack(side=tk.LEFT, fill=tk.BOTH, expand=True, padx=10)

        ttk.Label(
            target_frame, text="Calorías Objetivo", font=("Arial", 10, "bold")
        ).pack()
        self.calories_target_label = ttk.Label(
            target_frame, text="--", font=("Arial", 16), foreground="#FF9800"
        )
        self.calories_target_label.pack()
        ttk.Label(target_frame, text="Según tu objetivo", font=("Arial", 9)).pack()

    def create_goals_section(self):
        """Crea la sección de objetivos"""
        # Frame de objetivos
        goals_frame = ttk.LabelFrame(
            self.scrollable_frame, text="🎯 Seguimiento de Objetivos", padding="20"
        )
        goals_frame.pack(fill=tk.X, padx=20, pady=10)

        # Frame para objetivo actual
        current_goal_frame = ttk.Frame(goals_frame)
        current_goal_frame.pack(fill=tk.X, pady=(0, 15))

        ttk.Label(
            current_goal_frame, text="Objetivo Actual:", font=("Arial", 10, "bold")
        ).pack(anchor=tk.W)

        self.current_goal_label = ttk.Label(
            current_goal_frame, text="No definido", font=("Arial", 11)
        )
        self.current_goal_label.pack(anchor=tk.W, pady=(2, 0))

        # Progreso del objetivo
        progress_frame = ttk.Frame(goals_frame)
        progress_frame.pack(fill=tk.X)

        ttk.Label(
            progress_frame, text="Progreso del Mes:", font=("Arial", 10, "bold")
        ).pack(anchor=tk.W)

        # Barra de progreso
        self.progress_bar = ttk.Progressbar(
            progress_frame, length=300, mode="determinate", maximum=100
        )
        self.progress_bar.pack(fill=tk.X, pady=(5, 5))

        self.progress_label = ttk.Label(
            progress_frame, text="0% completado", font=("Arial", 9)
        )
        self.progress_label.pack(anchor=tk.W)

        # Botones de objetivos
        goals_buttons_frame = ttk.Frame(goals_frame)
        goals_buttons_frame.pack(fill=tk.X, pady=(10, 0))

        ttk.Button(
            goals_buttons_frame, text="➕ Nuevo Objetivo", command=self.create_new_goal
        ).pack(side=tk.LEFT, padx=(0, 10))

        ttk.Button(
            goals_buttons_frame,
            text="📈 Ver Progreso",
            command=self.view_detailed_progress,
        ).pack(side=tk.LEFT)

    def load_user_data(self):
        """Carga los datos del usuario actual"""
        try:
            # Obtener usuario actual
            self.current_user = self.user_manager.obtener_usuario_actual()

            if not self.current_user:
                # Si no hay usuario, mostrar mensaje
                self.show_no_user_message()
                return

            # Actualizar información del perfil
            self.update_profile_info()

            # Actualizar estadísticas
            self.update_statistics()

            # Actualizar métricas de salud
            self.update_health_metrics()

            # Actualizar objetivos
            self.update_goals()

        except Exception as e:
            messagebox.showerror("Error", f"Error al cargar datos del usuario: {e}")

    def show_no_user_message(self):
        """Muestra mensaje cuando no hay usuario seleccionado"""
        # Limpiar widgets
        for widget in self.scrollable_frame.winfo_children():
            widget.destroy()

        # Crear mensaje de no usuario
        no_user_frame = ttk.Frame(self.scrollable_frame)
        no_user_frame.pack(expand=True, fill=tk.BOTH)

        ttk.Label(no_user_frame, text="👤", font=("Arial", 48), anchor="center").pack(
            expand=True
        )

        ttk.Label(
            no_user_frame,
            text="No hay usuario seleccionado",
            font=("Arial", 16, "bold"),
        ).pack(pady=(10, 5))

        ttk.Label(
            no_user_frame,
            text="Selecciona o crea un usuario para ver su información",
            font=("Arial", 10),
        ).pack()

        ttk.Button(
            no_user_frame,
            text="Crear Usuario",
            command=self.create_new_user_from_section,
        ).pack(pady=(20, 0))

    def update_profile_info(self):
        """Actualiza la información del perfil"""
        if not self.current_user:
            return

        # Actualizar bienvenida
        self.welcome_label.config(text=f"¡Hola, {self.current_user['nombre']}!")

        # Actualizar labels de información
        self.info_labels["nombre"].config(
            text=self.current_user.get("nombre", "No especificado")
        )
        self.info_labels["edad"].config(
            text=f"{self.current_user.get('edad', 'N/A')} años"
            if self.current_user.get("edad")
            else "No especificado"
        )
        self.info_labels["peso"].config(
            text=f"{self.current_user.get('peso', 'N/A')} kg"
            if self.current_user.get("peso")
            else "No especificado"
        )
        self.info_labels["altura"].config(
            text=f"{self.current_user.get('altura', 'N/A')} cm"
            if self.current_user.get("altura")
            else "No especificado"
        )
        self.info_labels["objetivo"].config(
            text=self.current_user.get("objetivo", "No especificado")
        )

    def update_statistics(self):
        """Actualiza las estadísticas del usuario"""
        if not self.current_user:
            return

        try:
            # Obtener estadísticas del usuario
            stats = self.user_manager.db.obtener_estadisticas_usuario(
                self.current_user["id"]
            )

            # Actualizar tarjetas de estadísticas
            self.stat_cards["entrenamientos"]["value_label"].config(
                text=str(stats.get("total_entrenamientos", 0))
            )
            self.stat_cards["calorias"]["value_label"].config(
                text=f"{stats.get('total_calorias', 0):.0f} cal"
            )
            self.stat_cards["tiempo"]["value_label"].config(
                text=f"{stats.get('tiempo_total_minutos', 0)} min"
            )
            self.stat_cards["rutinas"]["value_label"].config(
                text=str(stats.get("rutinas_creadas", 0))
            )

        except Exception as e:
            print(f"Error al actualizar estadísticas: {e}")

    def update_health_metrics(self):
        """Actualiza las métricas de salud"""
        if not self.current_user:
            return

        try:
            # Calcular IMC
            imc = 0.0
            categoria_imc = "No calculable"

            if self.current_user.get("peso") and self.current_user.get("altura"):
                altura_metros = self.current_user["altura"] / 100
                imc = self.user_manager.calcular_imc(
                    self.current_user["peso"], altura_metros
                )
                categoria_imc = self.user_manager.interpretar_imc(imc)

                # Cambiar color según categoría
                color = "#4A90E2"  # Normal por defecto
                if categoria_imc == "Bajo peso":
                    color = "#FF9800"
                elif categoria_imc == "Sobrepeso":
                    color = "#FF9800"
                elif categoria_imc == "Obesidad":
                    color = "#F44336"

                self.imc_value_label.config(text=f"{imc:.1f}", foreground=color)

            else:
                self.imc_value_label.config(text="--")

            self.imc_category_label.config(text=categoria_imc)

            # Calcular calorías
            if (
                self.current_user.get("peso")
                and self.current_user.get("altura")
                and self.current_user.get("edad")
            ):
                calorias_base = self.user_manager.calcular_calorias_base(
                    self.current_user["peso"],
                    self.current_user["altura"],
                    self.current_user["edad"],
                )

                objetivo = self.current_user.get("objetivo", "mantenimiento").lower()
                calorias_objetivo = self.user_manager.calcular_calorias_objetivo(
                    calorias_base, objetivo
                )

                self.calories_base_label.config(text=f"{calorias_base:.0f}")
                self.calories_target_label.config(text=f"{calorias_objetivo:.0f}")

            else:
                self.calories_base_label.config(text="--")
                self.calories_target_label.config(text="--")

        except Exception as e:
            print(f"Error al actualizar métricas de salud: {e}")

    def update_goals(self):
        """Actualiza la información de objetivos"""
        if not self.current_user:
            return

        # Mostrar objetivo actual
        objetivo = self.current_user.get("objetivo", "No definido")
        self.current_goal_label.config(text=objetivo)

        # Calcular progreso del mes (simulado)
        # En una implementación real, esto vendría de la base de datos
        progress_percentage = self.calculate_monthly_progress()
        self.progress_bar["value"] = progress_percentage
        self.progress_label.config(text=f"{progress_percentage}% completado")

    def calculate_monthly_progress(self) -> int:
        """Calcula el progreso del mes actual (simulado)"""
        # Simulación de progreso basado en estadísticas
        try:
            stats = self.user_manager.db.obtener_estadisticas_usuario(
                self.current_user["id"]
            )
            entrenamientos = stats.get("total_entrenamientos", 0)

            # Lógica simple: cada 4 entrenamientos = 25% del objetivo mensual
            progress = min((entrenamientos * 25) // 4, 100)
            return progress

        except:
            return 0

    def change_photo(self):
        """Cambia la foto de perfil del usuario"""
        messagebox.showinfo(
            "Info", "Funcionalidad de cambio de foto estará disponible pronto"
        )

    def edit_profile(self):
        """Abre el diálogo para editar el perfil del usuario"""
        if not self.current_user:
            messagebox.showwarning("Advertencia", "Selecciona un usuario primero")
            return

        # Crear diálogo de edición
        dialog = ProfileEditDialog(
            self.main_window.root, self.current_user, self.user_manager
        )
        self.main_window.root.wait_window(dialog.window)

        # Recargar datos si se guardaron cambios
        if dialog.saved:
            self.load_user_data()
            self.main_window.update_user_info()

    def show_detailed_stats(self):
        """Muestra estadísticas detalladas del usuario"""
        if not self.current_user:
            messagebox.showwarning("Advertencia", "Selecciona un usuario primero")
            return

        # Crear diálogo de estadísticas detalladas
        dialog = DetailedStatsDialog(
            self.main_window.root, self.current_user, self.user_manager
        )

    def create_new_goal(self):
        """Crea un nuevo objetivo"""
        if not self.current_user:
            messagebox.showwarning("Advertencia", "Selecciona un usuario primero")
            return

        messagebox.showinfo(
            "Info", "Funcionalidad de objetivos estará disponible pronto"
        )

    def view_detailed_progress(self):
        """Muestra el progreso detallado"""
        if not self.current_user:
            messagebox.showwarning("Advertencia", "Selecciona un usuario primero")
            return

        messagebox.showinfo(
            "Info", "Vista de progreso detallado estará disponible pronto"
        )

    def create_new_user_from_section(self):
        """Crea un nuevo usuario desde la sección"""
        # Llamar al método de la ventana principal
        self.main_window.create_new_user()


class ProfileEditDialog:
    """Diálogo para editar el perfil del usuario"""

    def __init__(self, parent, user_data, user_manager):
        self.user_data = user_data
        self.user_manager = user_manager
        self.saved = False

        # Crear ventana
        self.window = tk.Toplevel(parent)
        self.window.title("Editar Perfil")
        self.window.geometry("450x400")
        self.window.resizable(False, False)
        self.window.transient(parent)
        self.window.grab_set()

        # Centrar ventana
        self.center_window()

        self.create_dialog()

    def center_window(self):
        """Centra la ventana en la pantalla"""
        self.window.update_idletasks()
        x = (self.window.winfo_screenwidth() // 2) - (450 // 2)
        y = (self.window.winfo_screenheight() // 2) - (400 // 2)
        self.window.geometry(f"450x400+{x}+{y}")

    def create_dialog(self):
        """Crea la interfaz del diálogo"""
        # Frame principal
        main_frame = ttk.Frame(self.window, padding="20")
        main_frame.pack(fill=tk.BOTH, expand=True)

        # Título
        title_label = ttk.Label(
            main_frame, text="Editar Perfil de Usuario", font=("Arial", 14, "bold")
        )
        title_label.pack(pady=(0, 20))

        # Variables del formulario
        self.nombre_var = tk.StringVar(value=self.user_data.get("nombre", ""))
        self.edad_var = tk.StringVar(value=str(self.user_data.get("edad", "")))
        self.peso_var = tk.StringVar(value=str(self.user_data.get("peso", "")))
        self.altura_var = tk.StringVar(value=str(self.user_data.get("altura", "")))
        self.objetivo_var = tk.StringVar(value=self.user_data.get("objetivo", ""))

        # Formulario
        form_frame = ttk.Frame(main_frame)
        form_frame.pack(fill=tk.X, pady=(0, 20))

        # Campos del formulario
        fields = [
            ("Nombre *:", self.nombre_var),
            ("Edad:", self.edad_var),
            ("Peso (kg):", self.peso_var),
            ("Altura (cm):", self.altura_var),
            ("Objetivo:", self.objetivo_var),
        ]

        for i, (label_text, var) in enumerate(fields):
            ttk.Label(form_frame, text=label_text).grid(
                row=i, column=0, sticky=tk.W, pady=8
            )

            if label_text == "Objetivo:":
                combo = ttk.Combobox(
                    form_frame,
                    textvariable=var,
                    values=[
                        "Perder peso",
                        "Ganar músculo",
                        "Mantenimiento",
                        "Mejorar resistencia",
                    ],
                    state="readonly",
                    width=30,
                )
                combo.grid(row=i, column=1, padx=(10, 0), pady=8)
            else:
                ttk.Entry(form_frame, textvariable=var, width=32).grid(
                    row=i, column=1, padx=(10, 0), pady=8
                )

        # Mensaje de error
        self.error_label = ttk.Label(
            main_frame, text="", foreground="red", font=("Arial", 9)
        )
        self.error_label.pack()

        # Botones
        button_frame = ttk.Frame(main_frame)
        button_frame.pack(fill=tk.X)

        ttk.Button(button_frame, text="Cancelar", command=self.cancel).pack(
            side=tk.RIGHT, padx=(5, 0)
        )

        ttk.Button(button_frame, text="Guardar", command=self.save_profile).pack(
            side=tk.RIGHT
        )

    def save_profile(self):
        """Guarda los cambios del perfil"""
        try:
            # Validar datos
            nombre = self.nombre_var.get().strip()
            if not nombre:
                self.error_label.config(text="El nombre es obligatorio")
                return

            # Obtener valores
            edad = int(self.edad_var.get()) if self.edad_var.get() else None
            peso = float(self.peso_var.get()) if self.peso_var.get() else None
            altura = float(self.altura_var.get()) if self.altura_var.get() else None
            objetivo = self.objetivo_var.get()

            # Validaciones
            if edad and (edad < 13 or edad > 100):
                self.error_label.config(text="La edad debe estar entre 13 y 100 años")
                return

            if peso and (peso < 30 or peso > 300):
                self.error_label.config(text="El peso debe estar entre 30 y 300 kg")
                return

            if altura and (altura < 120 or altura > 250):
                self.error_label.config(text="La altura debe estar entre 120 y 250 cm")
                return

            # Guardar cambios
            success = self.user_manager.db.ejecutar_comando(
                """
                UPDATE usuarios SET nombre = ?, edad = ?, peso = ?, altura = ?, objetivo = ?
                WHERE id = ?
                """,
                (nombre, edad, peso, altura, objetivo, self.user_data["id"]),
            )

            if success:
                self.saved = True
                self.window.destroy()
                messagebox.showinfo("Éxito", "Perfil actualizado correctamente")
            else:
                self.error_label.config(text="Error al guardar los cambios")

        except ValueError as e:
            self.error_label.config(text="Verifica los datos numéricos")
        except Exception as e:
            self.error_label.config(text=f"Error: {str(e)}")

    def cancel(self):
        """Cancela la edición"""
        self.window.destroy()


class DetailedStatsDialog:
    """Diálogo para mostrar estadísticas detalladas"""

    def __init__(self, parent, user_data, user_manager):
        self.user_data = user_data
        self.user_manager = user_manager

        # Crear ventana
        self.window = tk.Toplevel(parent)
        self.window.title(f"Estadísticas Detalladas - {user_data['nombre']}")
        self.window.geometry("600x500")
        self.window.transient(parent)

        # Centrar ventana
        self.center_window()

        self.create_dialog()

    def center_window(self):
        """Centra la ventana en la pantalla"""
        self.window.update_idletasks()
        x = (self.window.winfo_screenwidth() // 2) - (600 // 2)
        y = (self.window.winfo_screenheight() // 2) - (500 // 2)
        self.window.geometry(f"600x500+{x}+{y}")

    def create_dialog(self):
        """Crea la interfaz del diálogo"""
        # Frame principal con scroll
        main_frame = ttk.Frame(self.window, padding="20")
        main_frame.pack(fill=tk.BOTH, expand=True)

        # Título
        ttk.Label(
            main_frame, text="Estadísticas Detalladas", font=("Arial", 14, "bold")
        ).pack(pady=(0, 20))

        # Obtener perfil completo
        perfil = self.user_manager.obtener_perfil_completo(self.user_data["id"])

        if perfil:
            # Mostrar información
            info_text = f"""
Nombre: {perfil.get("nombre", "N/A")}
Edad: {perfil.get("edad", "N/A")} años
Peso: {perfil.get("peso", "N/A")} kg
Altura: {perfil.get("altura", "N/A")} cm
IMC: {perfil.get("imc", "N/A")} ({perfil.get("categoria_imc", "N/A")})
Calorías Basales: {perfil.get("calorias_base", "N/A")}
Calorías Objetivo: {perfil.get("calorias_objetivo", "N/A")}

ENTRENAMIENTOS:
Total: {perfil.get("estadisticas", {}).get("total_entrenamientos", 0)}
Calorías Quemadas: {perfil.get("estadisticas", {}).get("total_calorias", 0):.0f}
Tiempo Total: {perfil.get("estadisticas", {}).get("tiempo_total_minutos", 0)} minutos
Rutinas Creadas: {perfil.get("estadisticas", {}).get("rutinas_creadas", 0)}
            """

            text_widget = tk.Text(main_frame, wrap=tk.WORD, font=("Arial", 10))
            text_widget.pack(fill=tk.BOTH, expand=True, pady=(0, 20))
            text_widget.insert(tk.END, info_text)
            text_widget.config(state=tk.DISABLED)

        # Botón cerrar
        ttk.Button(main_frame, text="Cerrar", command=self.window.destroy).pack()
