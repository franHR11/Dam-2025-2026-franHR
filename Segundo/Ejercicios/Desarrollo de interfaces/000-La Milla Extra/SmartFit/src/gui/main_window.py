# main_window.py - Ventana principal de SmartFit
# Fran - Desarrollo de interfaces

import json
import os
import tkinter as tk
from datetime import datetime, timedelta
from tkinter import messagebox, ttk

# Componentes de la aplicación
from src.components.smart_gauge import SmartGauge
from src.gui.dialogs import RoutineDialog, UserDialog, WorkoutDialog
from src.gui.widgets import InfoCard, ModernButton, ModernFrame


class MainWindow:
    """
    Ventana principal de la aplicación SmartFit
    Implementa la interfaz gráfica principal con patrón MVC
    """

    def __init__(self, root, db_manager, user_manager):
        """Inicializa la ventana principal"""
        self.root = root
        self.db = db_manager
        self.user_manager = user_manager

        # Variables de estado
        self.current_user = None
        self.current_view = "dashboard"

        # Configurar la interfaz
        self.setup_styles()
        self.create_layout()
        self.bind_events()

        # Cargar datos iniciales
        self.load_initial_data()

    def setup_styles(self):
        """Configura los estilos visuales de la aplicación"""
        self.style = ttk.Style()

        # Configurar tema base
        self.style.theme_use("clam")

        # Colores personalizados
        self.colors = {
            "primary": "#4A90E2",
            "secondary": "#7B68EE",
            "success": "#28A745",
            "warning": "#FFC107",
            "danger": "#DC3545",
            "light": "#F8F9FA",
            "dark": "#343A40",
            "text": "#212529",
            "text_light": "#6C757D",
        }

        # Configurar estilos personalizados
        self.style.configure(
            "Title.TLabel",
            font=("Segoe UI", 16, "bold"),
            foreground=self.colors["dark"],
        )

        self.style.configure(
            "Heading.TLabel",
            font=("Segoe UI", 12, "bold"),
            foreground=self.colors["dark"],
        )

        self.style.configure("Card.TFrame", relief="flat", borderwidth=1)

    def create_layout(self):
        """Crea el diseño principal de la ventana"""
        # Frame principal
        self.main_frame = ttk.Frame(self.root)
        self.main_frame.pack(fill=tk.BOTH, expand=True, padx=10, pady=10)

        # Crear barra superior
        self.create_header()

        # Crear contenido principal
        self.create_content_area()

        # Crear barra lateral
        self.create_sidebar()

        # Inicializar la vista de dashboard
        self.show_dashboard()

    def create_header(self):
        """Crea la barra superior con título y acciones"""
        header_frame = ttk.Frame(self.main_frame, style="Card.TFrame")
        header_frame.pack(fill=tk.X, pady=(0, 10))
        header_frame.configure(height=60)

        # Título de la aplicación
        title_label = ttk.Label(
            header_frame, text="SmartFit - Tu Entrenador Personal", style="Title.TLabel"
        )
        title_label.pack(side=tk.LEFT, padx=20, pady=10)

        # Frame para información del usuario
        user_frame = ttk.Frame(header_frame)
        user_frame.pack(side=tk.RIGHT, padx=20, pady=10)

        # Botón de usuario
        self.user_button = ttk.Button(
            user_frame, text="👤 Usuario", command=self.show_user_profile
        )
        self.user_button.pack(side=tk.RIGHT, padx=5)

        # Información del usuario actual
        self.user_info_label = ttk.Label(
            user_frame, text="No hay usuario seleccionado", style="Heading.TLabel"
        )
        self.user_info_label.pack(side=tk.RIGHT, padx=5)

    def create_sidebar(self):
        """Crea la barra lateral de navegación"""
        self.sidebar = ttk.Frame(self.main_frame, width=200, style="Card.TFrame")
        self.sidebar.pack(side=tk.LEFT, fill=tk.Y, padx=(0, 10))
        self.sidebar.pack_propagate(False)

        # Título de navegación
        nav_title = ttk.Label(self.sidebar, text="Navegación", style="Heading.TLabel")
        nav_title.pack(pady=10)

        # Botones de navegación
        self.nav_buttons = {}

        nav_items = [
            ("🏠 Inicio", "dashboard"),
            ("💪 Rutinas", "routines"),
            ("📊 Entrenamientos", "workouts"),
            ("🥗 Nutrición", "nutrition"),
            ("📈 Informes", "reports"),
            ("⚙️ Configuración", "settings"),
        ]

        for text, view in nav_items:
            btn = ttk.Button(
                self.sidebar, text=text, command=lambda v=view: self.change_view(v)
            )
            btn.pack(fill=tk.X, padx=10, pady=5)
            self.nav_buttons[view] = btn

    def create_content_area(self):
        """Crea el área de contenido principal"""
        self.content_frame = ttk.Frame(self.main_frame)
        self.content_frame.pack(side=tk.RIGHT, fill=tk.BOTH, expand=True)

        # Frame contenedor para las vistas
        self.views_container = ttk.Frame(self.content_frame)
        self.views_container.pack(fill=tk.BOTH, expand=True)

    def change_view(self, view_name):
        """Cambia la vista actual"""
        self.current_view = view_name

        # Actualizar botones de navegación
        for view, btn in self.nav_buttons.items():
            if view == view_name:
                btn.configure(style="Accent.TButton")
            else:
                btn.configure(style="TButton")

        # Mostrar la vista correspondiente
        if view_name == "dashboard":
            self.show_dashboard()
        elif view_name == "routines":
            self.show_routines()
        elif view_name == "workouts":
            self.show_workouts()
        elif view_name == "nutrition":
            self.show_nutrition()
        elif view_name == "reports":
            self.show_reports()
        elif view_name == "settings":
            self.show_settings()

    def show_dashboard(self):
        """Muestra la vista del dashboard principal"""
        self.clear_content()

        # Título de la sección
        title = ttk.Label(self.views_container, text="Dashboard", style="Title.TLabel")
        title.pack(pady=10)

        # Frame para estadísticas
        stats_frame = ttk.Frame(self.views_container)
        stats_frame.pack(fill=tk.X, pady=10)

        # Tarjetas de estadísticas
        if self.current_user:
            stats = self.user_manager.db.obtener_estadisticas_usuario(
                self.current_user["id"]
            )
            self.create_stats_cards(stats_frame, stats)
        else:
            # Mensaje si no hay usuario
            no_user_frame = ttk.Frame(self.views_container, style="Card.TFrame")
            no_user_frame.pack(fill=tk.X, pady=20, padx=20)

            no_user_label = ttk.Label(
                no_user_frame,
                text="No hay usuario seleccionado",
                style="Heading.TLabel",
            )
            no_user_label.pack(pady=20)

            select_user_btn = ttk.Button(
                no_user_frame,
                text="Seleccionar Usuario",
                command=self.show_user_selection,
            )
            select_user_btn.pack(pady=10)

        # Accesos rápidos
        self.create_quick_actions()

    def create_stats_cards(self, parent, stats):
        """Crea las tarjetas de estadísticas"""
        cards_frame = ttk.Frame(parent)
        cards_frame.pack(fill=tk.X, pady=10)

        # Tarjeta de entrenamientos
        self.create_info_card(
            cards_frame,
            "💪 Entrenamientos",
            f"{stats.get('total_entrenamientos', 0)}",
            "Completados",
        )

        # Tarjeta de calorías
        self.create_info_card(
            cards_frame,
            "🔥 Calorías",
            f"{stats.get('total_calorias', 0):.0f}",
            "Quemadas",
        )

        # Tarjeta de tiempo
        self.create_info_card(
            cards_frame,
            "⏱️ Tiempo",
            f"{stats.get('tiempo_total_minutos', 0)}",
            "Minutos",
        )

        # Tarjeta de rutinas
        self.create_info_card(
            cards_frame, "📋 Rutinas", f"{stats.get('rutinas_creadas', 0)}", "Creadas"
        )

    def create_info_card(self, parent, title, value, subtitle):
        """Crea una tarjeta de información"""
        card = ttk.Frame(parent, style="Card.TFrame")
        card.pack(side=tk.LEFT, fill=tk.BOTH, expand=True, padx=5)

        # Título
        title_label = ttk.Label(card, text=title, style="Heading.TLabel")
        title_label.pack(pady=(10, 5))

        # Valor
        value_label = ttk.Label(card, text=value, font=("Segoe UI", 20, "bold"))
        value_label.pack()

        # Subtítulo
        subtitle_label = ttk.Label(card, text=subtitle, style="TLabel")
        subtitle_label.pack(pady=(5, 10))

    def create_quick_actions(self):
        """Crea los botones de acciones rápidas"""
        actions_frame = ttk.LabelFrame(
            self.views_container, text="Acciones Rápidas", style="Card.TFrame"
        )
        actions_frame.pack(fill=tk.X, pady=20, padx=20)

        # Botones de acción
        actions = [
            ("➕ Nueva Rutina", self.create_routine),
            ("🏃 Iniciar Entrenamiento", self.start_workout),
            ("📊 Ver Informes", self.show_reports),
            ("👤 Perfil Usuario", self.show_user_profile),
        ]

        for text, command in actions:
            btn = ttk.Button(actions_frame, text=text, command=command)
            btn.pack(side=tk.LEFT, padx=10, pady=10)

    def show_routines(self):
        """Muestra la vista de rutinas"""
        self.clear_content()

        # Título
        title = ttk.Label(
            self.views_container, text="Gestión de Rutinas", style="Title.TLabel"
        )
        title.pack(pady=10)

        # Botón para nueva rutina
        new_routine_btn = ttk.Button(
            self.views_container, text="➕ Nueva Rutina", command=self.create_routine
        )
        new_routine_btn.pack(pady=10)

        # Lista de rutinas
        if self.current_user:
            routines = self.user_manager.obtener_rutinas_usuario(
                self.current_user["id"]
            )
            self.display_routines_list(routines)
        else:
            ttk.Label(
                self.views_container, text="Selecciona un usuario para ver las rutinas"
            ).pack(pady=20)

    def display_routines_list(self, routines):
        """Muestra la lista de rutinas"""
        # Frame con scroll
        canvas = tk.Canvas(self.views_container, height=400)
        scrollbar = ttk.Scrollbar(
            self.views_container, orient="vertical", command=canvas.yview
        )
        scrollable_frame = ttk.Frame(canvas)

        scrollable_frame.bind(
            "<Configure>", lambda e: canvas.configure(scrollregion=canvas.bbox("all"))
        )

        canvas.create_window((0, 0), window=scrollable_frame, anchor="nw")
        canvas.configure(yscrollcommand=scrollbar.set)

        canvas.pack(side="left", fill="both", expand=True)
        scrollbar.pack(side="right", fill="y")

        # Mostrar rutinas
        for routine in routines:
            routine_frame = ttk.Frame(scrollable_frame, style="Card.TFrame")
            routine_frame.pack(fill=tk.X, pady=5, padx=20)

            # Información de la rutina
            info_frame = ttk.Frame(routine_frame)
            info_frame.pack(side=tk.LEFT, fill=tk.X, expand=True, padx=10, pady=10)

            ttk.Label(info_frame, text=routine["nombre"], style="Heading.TLabel").pack(
                anchor=tk.W
            )
            ttk.Label(
                info_frame, text=f"Duración: {routine['duracion_minutos']} min"
            ).pack(anchor=tk.W)
            ttk.Label(info_frame, text=f"Dificultad: {routine['dificultad']}").pack(
                anchor=tk.W
            )

            # Botones de acción
            buttons_frame = ttk.Frame(routine_frame)
            buttons_frame.pack(side=tk.RIGHT, padx=10, pady=10)

            ttk.Button(
                buttons_frame,
                text="Editar",
                command=lambda r=routine: self.edit_routine(r),
            ).pack(pady=2)
            ttk.Button(
                buttons_frame,
                text="Eliminar",
                command=lambda r=routine: self.delete_routine(r),
            ).pack(pady=2)

    def show_workouts(self):
        """Muestra la vista de entrenamientos"""
        self.clear_content()

        # Título
        title = ttk.Label(
            self.views_container,
            text="Historial de Entrenamientos",
            style="Title.TLabel",
        )
        title.pack(pady=10)

        # Contenido de entrenamientos
        if self.current_user:
            workouts = self.user_manager.obtener_entrenamientos_recientes(
                self.current_user["id"]
            )
            self.display_workouts_list(workouts)
        else:
            ttk.Label(
                self.views_container, text="Selecciona un usuario para ver el historial"
            ).pack(pady=20)

    def display_workouts_list(self, workouts):
        """Muestra la lista de entrenamientos"""
        # Tabla de entrenamientos
        columns = ("Fecha", "Rutina", "Duración", "Calorías", "Estado")
        tree = ttk.Treeview(
            self.views_container, columns=columns, show="headings", height=15
        )

        # Configurar columnas
        for col in columns:
            tree.heading(col, text=col)
            tree.column(col, width=120)

        # Insertar datos
        for workout in workouts:
            tree.insert(
                "",
                tk.END,
                values=(
                    workout["fecha"],
                    workout["rutina_nombre"],
                    f"{workout['duracion']} min",
                    f"{workout['calorias']} cal",
                    "✓ Completado" if workout["completado"] else "⏸️ Pendiente",
                ),
            )

        tree.pack(fill=tk.BOTH, expand=True, padx=20, pady=20)

    def show_nutrition(self):
        """Muestra la vista de nutrición"""
        self.clear_content()

        # Título
        title = ttk.Label(
            self.views_container, text="Control de Nutrición", style="Title.TLabel"
        )
        title.pack(pady=10)

        # Contenido de nutrición
        if self.current_user:
            nutrition_data = self.user_manager.get_user_nutrition()
            self.display_nutrition_dashboard(nutrition_data)
        else:
            ttk.Label(
                self.views_container, text="Selecciona un usuario para ver la nutrición"
            ).pack(pady=20)

    def display_nutrition_dashboard(self, nutrition_data):
        """Muestra el panel de nutrición"""
        # Frame principal
        nutrition_frame = ttk.Frame(self.views_container)
        nutrition_frame.pack(fill=tk.BOTH, expand=True, padx=20, pady=20)

        # Calorías consumidas hoy
        calories_frame = ttk.Frame(nutrition_frame, style="Card.TFrame")
        calories_frame.pack(fill=tk.X, pady=10)

        ttk.Label(calories_frame, text="Calorías de Hoy", style="Heading.TLabel").pack(
            pady=10
        )

        # Medidor de calorías
        calorie_gauge = SmartGauge(
            calories_frame,
            max_value=2000,
            current_value=nutrition_data.get("calorias_total", 0),
            title="Calorías",
        )
        calorie_gauge.pack(pady=20)

        # Lista de alimentos consumidos
        foods_frame = ttk.LabelFrame(nutrition_frame, text="Alimentos Consumidos Hoy")
        foods_frame.pack(fill=tk.BOTH, expand=True, pady=10)

        if nutrition_data.get("consumo"):
            for food in nutrition_data["consumo"]:
                food_frame = ttk.Frame(foods_frame)
                food_frame.pack(fill=tk.X, pady=2, padx=10)

                ttk.Label(food_frame, text=food["nombre"]).pack(side=tk.LEFT)
                ttk.Label(
                    food_frame, text=f"{food['calorias_consumidas']:.0f} cal"
                ).pack(side=tk.RIGHT)
        else:
            ttk.Label(foods_frame, text="No hay alimentos registrados hoy").pack(
                pady=20
            )

    def show_reports(self):
        """Muestra la vista de informes"""
        self.clear_content()

        # Título
        title = ttk.Label(
            self.views_container, text="Informes y Estadísticas", style="Title.TLabel"
        )
        title.pack(pady=10)

        # Contenido de informes
        if self.current_user:
            self.display_reports_content()
        else:
            ttk.Label(
                self.views_container, text="Selecciona un usuario para ver los informes"
            ).pack(pady=20)

    def display_reports_content(self):
        """Muestra el contenido de informes"""
        # Frame de selección de período
        period_frame = ttk.LabelFrame(self.views_container, text="Seleccionar Período")
        period_frame.pack(fill=tk.X, padx=20, pady=10)

        period_buttons = ttk.Frame(period_frame)
        period_buttons.pack(pady=10)

        ttk.Button(
            period_buttons,
            text="Última Semana",
            command=lambda: self.generate_report("week"),
        ).pack(side=tk.LEFT, padx=5)
        ttk.Button(
            period_buttons,
            text="Último Mes",
            command=lambda: self.generate_report("month"),
        ).pack(side=tk.LEFT, padx=5)
        ttk.Button(
            period_buttons,
            text="Todo el Historial",
            command=lambda: self.generate_report("all"),
        ).pack(side=tk.LEFT, padx=5)

        # Área de informe
        self.report_frame = ttk.Frame(self.views_container)
        self.report_frame.pack(fill=tk.BOTH, expand=True, padx=20, pady=20)

        # Generar informe inicial
        self.generate_report("week")

    def generate_report(self, period):
        """Genera un informe para el período especificado"""
        # Limpiar frame anterior
        for widget in self.report_frame.winfo_children():
            widget.destroy()

        # Título del informe
        ttk.Label(
            self.report_frame,
            text=f"Informe - {period.title()}",
            style="Heading.TLabel",
        ).pack(pady=10)

        # Obtener datos del período
        if self.current_user:
            stats = self.user_manager.db.obtener_estadisticas_usuario(
                self.current_user["id"]
            )

            # Mostrar estadísticas
            ttk.Label(
                self.report_frame,
                text=f"Entrenamientos: {stats.get('total_entrenamientos', 0)}",
            ).pack()
            ttk.Label(
                self.report_frame,
                text=f"Calorías totales: {stats.get('total_calorias', 0):.0f}",
            ).pack()
            ttk.Label(
                self.report_frame,
                text=f"Tiempo total: {stats.get('tiempo_total_minutos', 0)} minutos",
            ).pack()

    def show_settings(self):
        """Muestra la vista de configuración"""
        self.clear_content()

        # Título
        title = ttk.Label(
            self.views_container, text="Configuración", style="Title.TLabel"
        )
        title.pack(pady=10)

        # Configuración general
        settings_frame = ttk.LabelFrame(
            self.views_container, text="Configuración General"
        )
        settings_frame.pack(fill=tk.X, padx=20, pady=10)

        # Opciones de configuración
        ttk.Label(
            settings_frame,
            text="Esta funcionalidad estará disponible en futuras versiones",
        ).pack(pady=20)

    def show_user_selection(self):
        """Muestra el diálogo de selección de usuario"""
        users = self.user_manager.listar_usuarios()

        if not users:
            # Crear nuevo usuario si no hay ninguno
            self.create_user()
            return

        # Diálogo de selección
        dialog = tk.Toplevel(self.root)
        dialog.title("Seleccionar Usuario")
        dialog.geometry("400x300")
        dialog.transient(self.root)
        dialog.grab_set()

        # Centrar diálogo
        dialog.geometry(
            "+%d+%d" % (self.root.winfo_rootx() + 50, self.root.winfo_rooty() + 50)
        )

        ttk.Label(
            dialog, text="Selecciona un usuario:", font=("Segoe UI", 12, "bold")
        ).pack(pady=10)

        # Lista de usuarios
        listbox = tk.Listbox(dialog, font=("Segoe UI", 10))
        listbox.pack(fill=tk.BOTH, expand=True, padx=20, pady=10)

        for user in users:
            listbox.insert(tk.END, f"{user['nombre']} (ID: {user['id']})")

        # Botones
        buttons_frame = ttk.Frame(dialog)
        buttons_frame.pack(fill=tk.X, padx=20, pady=10)

        def select_user():
            selection = listbox.curselection()
            if selection:
                selected_user = users[selection[0]]
                self.current_user = selected_user
                self.user_info_label.config(text=selected_user["nombre"])
                dialog.destroy()
                self.refresh_current_view()

        def create_new_user():
            dialog.destroy()
            self.create_user()

        ttk.Button(buttons_frame, text="Seleccionar", command=select_user).pack(
            side=tk.LEFT, padx=5
        )
        ttk.Button(buttons_frame, text="Nuevo Usuario", command=create_new_user).pack(
            side=tk.LEFT, padx=5
        )
        ttk.Button(buttons_frame, text="Cancelar", command=dialog.destroy).pack(
            side=tk.RIGHT, padx=5
        )

    def create_user(self):
        """Abre el diálogo para crear un nuevo usuario"""
        dialog = tk.Toplevel(self.root)
        dialog.title("Nuevo Usuario")
        dialog.geometry("400x350")
        dialog.transient(self.root)
        dialog.grab_set()

        # Centrar diálogo
        dialog.geometry(
            "+%d+%d" % (self.root.winfo_rootx() + 50, self.root.winfo_rooty() + 50)
        )

        ttk.Label(
            dialog, text="Crear Nuevo Usuario", font=("Segoe UI", 12, "bold")
        ).pack(pady=10)

        # Formulario
        form_frame = ttk.Frame(dialog)
        form_frame.pack(fill=tk.BOTH, expand=True, padx=20, pady=10)

        # Variables del formulario
        nombre_var = tk.StringVar()
        edad_var = tk.StringVar()
        peso_var = tk.StringVar()
        altura_var = tk.StringVar()
        objetivo_var = tk.StringVar()

        # Campos
        fields = [
            ("Nombre:", nombre_var),
            ("Edad:", edad_var),
            ("Peso (kg):", peso_var),
            ("Altura (cm):", altura_var),
            ("Objetivo:", objetivo_var),
        ]

        for i, (label_text, var) in enumerate(fields):
            ttk.Label(form_frame, text=label_text).grid(
                row=i, column=0, sticky=tk.W, pady=5
            )
            if label_text == "Objetivo:":
                objetivo_combo = ttk.Combobox(
                    form_frame,
                    textvariable=var,
                    values=["Perder peso", "Ganar músculo", "Mantenimiento"],
                )
                objetivo_combo.grid(row=i, column=1, sticky=tk.EW, pady=5)
            else:
                ttk.Entry(form_frame, textvariable=var).grid(
                    row=i, column=1, sticky=tk.EW, pady=5
                )

        form_frame.columnconfigure(1, weight=1)

        def save_user():
            try:
                # Validar y crear usuario
                nombre = nombre_var.get().strip()
                edad = int(edad_var.get()) if edad_var.get() else None
                peso = float(peso_var.get()) if peso_var.get() else None
                altura = float(altura_var.get()) if altura_var.get() else None
                objetivo = objetivo_var.get()

                if not nombre:
                    messagebox.showerror("Error", "El nombre es obligatorio")
                    return

                user_id = self.user_manager.crear_usuario(
                    nombre, edad, peso, altura, objetivo
                )

                if user_id > 0:
                    self.current_user = self.user_manager.obtener_usuario_por_id(
                        user_id
                    )
                    self.user_info_label.config(text=self.current_user["nombre"])
                    dialog.destroy()
                    self.refresh_current_view()
                    messagebox.showinfo("Éxito", "Usuario creado correctamente")
                else:
                    messagebox.showerror("Error", "No se pudo crear el usuario")

            except ValueError as e:
                messagebox.showerror("Error", "Los datos numéricos no son válidos")
            except Exception as e:
                messagebox.showerror("Error", f"Error inesperado: {e}")

        # Botones
        buttons_frame = ttk.Frame(dialog)
        buttons_frame.pack(fill=tk.X, padx=20, pady=10)

        ttk.Button(buttons_frame, text="Crear", command=save_user).pack(
            side=tk.LEFT, padx=5
        )
        ttk.Button(buttons_frame, text="Cancelar", command=dialog.destroy).pack(
            side=tk.LEFT, padx=5
        )

    def show_user_profile(self):
        """Muestra el perfil detallado del usuario actual"""
        if not self.current_user:
            messagebox.showinfo("Info", "Selecciona un usuario primero")
            return

        # Obtener perfil completo
        perfil = self.user_manager.obtener_perfil_completo(self.current_user["id"])

        if not perfil:
            messagebox.showerror("Error", "No se pudo obtener el perfil del usuario")
            return

        # Crear diálogo de perfil
        dialog = tk.Toplevel(self.root)
        dialog.title(f"Perfil - {perfil['nombre']}")
        dialog.geometry("500x600")
        dialog.transient(self.root)

        # Área de scroll
        canvas = tk.Canvas(dialog, height=550)
        scrollbar = ttk.Scrollbar(dialog, orient="vertical", command=canvas.yview)
        scrollable_frame = ttk.Frame(canvas)

        scrollable_frame.bind(
            "<Configure>", lambda e: canvas.configure(scrollregion=canvas.bbox("all"))
        )

        canvas.create_window((0, 0), window=scrollable_frame, anchor="nw")
        canvas.configure(yscrollcommand=scrollbar.set)

        canvas.pack(side="left", fill="both", expand=True)
        scrollbar.pack(side="right", fill="y")

        # Información del perfil
        y_pos = 10
        for key, value in perfil.items():
            if key not in ["estadisticas"]:  # Mostrar estadísticas por separado
                ttk.Label(
                    scrollable_frame, text=f"{key.replace('_', ' ').title()}: {value}"
                ).pack(anchor=tk.W, padx=20, pady=2)
                y_pos += 25

        # Estadísticas
        ttk.Label(
            scrollable_frame, text="ESTADÍSTICAS", font=("Segoe UI", 12, "bold")
        ).pack(pady=(20, 10))

        for key, value in perfil["estadisticas"].items():
            ttk.Label(
                scrollable_frame, text=f"{key.replace('_', ' ').title()}: {value}"
            ).pack(anchor=tk.W, padx=20, pady=2)

    def create_routine(self):
        """Crea una nueva rutina"""
        if not self.current_user:
            messagebox.showinfo("Info", "Selecciona un usuario primero")
            return

        messagebox.showinfo(
            "Info", "Funcionalidad de crear rutina estará disponible pronto"
        )

    def start_workout(self):
        """Inicia un nuevo entrenamiento"""
        if not self.current_user:
            messagebox.showinfo("Info", "Selecciona un usuario primero")
            return

        messagebox.showinfo(
            "Info", "Funcionalidad de iniciar entrenamiento estará disponible pronto"
        )

    def edit_routine(self, routine):
        """Edita una rutina existente"""
        messagebox.showinfo("Info", f"Editar rutina: {routine['nombre']}")

    def delete_routine(self, routine):
        """Elimina una rutina"""
        if messagebox.askyesno(
            "Confirmar", f"¿Eliminar la rutina '{routine['nombre']}'?"
        ):
            messagebox.showinfo("Info", f"Rutina {routine['nombre']} eliminada")

    def clear_content(self):
        """Limpia el área de contenido"""
        for widget in self.views_container.winfo_children():
            widget.destroy()

    def refresh_current_view(self):
        """Actualiza la vista actual"""
        self.change_view(self.current_view)

    def load_initial_data(self):
        """Carga los datos iniciales de la aplicación"""
        # Cargar primer usuario si existe
        users = self.user_manager.listar_usuarios()
        if users:
            self.current_user = users[0]
            self.user_info_label.config(text=self.current_user["nombre"])

    def bind_events(self):
        """Asocia los eventos de la ventana"""
        # Atajos de teclado
        self.root.bind("<F1>", lambda e: self.change_view("dashboard"))
        self.root.bind("<F2>", lambda e: self.change_view("routines"))
        self.root.bind("<F3>", lambda e: self.change_view("workouts"))
        self.root.bind("<F4>", lambda e: self.change_view("nutrition"))
        self.root.bind("<F5>", lambda e: self.change_view("reports"))

        # Evento de cierre
        self.root.protocol("WM_DELETE_WINDOW", self.on_closing)

    def on_closing(self):
        """Maneja el cierre de la aplicación"""
        if messagebox.askokcancel("Salir", "¿Quieres salir de SmartFit?"):
            # Cerrar conexión de base de datos
            if hasattr(self, "db"):
                self.db.close()
            self.root.destroy()

    def show(self):
        """Muestra la ventana principal"""
        self.root.deiconify()
