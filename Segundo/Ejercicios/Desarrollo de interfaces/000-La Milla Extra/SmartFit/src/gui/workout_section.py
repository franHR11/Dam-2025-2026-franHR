# workout_section.py - Sección de entrenamientos para SmartFit
# Fran - Desarrollo de interfaces

import json
import tkinter as tk
from datetime import datetime, timedelta
from tkinter import messagebox, ttk
from typing import Any, Dict, List, Optional


class WorkoutSection:
    """
    Sección de interfaz para gestión de entrenamientos
    Unidad: Generación de interfaces de usuario
    Subunidad: Creación de interfaces gráficas

    Esta clase maneja toda la funcionalidad relacionada con:
    - Visualización de entrenamientos históricos
    - Creación de nuevos entrenamientos
    - Seguimiento de progreso
    - Gestión de rutinas
    """

    def __init__(self, parent_notebook, db_manager, user_manager, main_window):
        """Inicializa la sección de entrenamientos"""
        self.notebook = parent_notebook
        self.db = db_manager
        self.user_manager = user_manager
        self.main_window = main_window

        # Variables de la sección
        self.current_user = None
        self.workouts_data = []
        self.routines_data = []
        self.selected_workout = None

        # Crear frame principal
        self.frame = ttk.Frame(self.notebook)

        # Crear la interfaz
        self.create_workout_interface()

    def create_workout_interface(self):
        """Crea la interfaz principal de entrenamientos"""
        # Frame principal
        main_frame = ttk.Frame(self.frame)
        main_frame.pack(fill=tk.BOTH, expand=True, padx=10, pady=10)

        # Título de la sección
        title_label = ttk.Label(
            main_frame, text="💪 Gestión de Entrenamientos", font=("Arial", 16, "bold")
        )
        title_label.pack(pady=(0, 20))

        # Crear notebook interno para subsecciones
        self.workout_notebook = ttk.Notebook(main_frame)
        self.workout_notebook.pack(fill=tk.BOTH, expand=True)

        # Crear subsecciones
        self.create_historical_subtab()
        self.create_new_workout_subtab()
        self.create_routines_subtab()
        self.create_progress_subtab()

    def create_historical_subtab(self):
        """Crea la pestaña de entrenamientos históricos"""
        # Frame de entrenamientos históricos
        historical_frame = ttk.Frame(self.workout_notebook)
        self.workout_notebook.add(historical_frame, text="📚 Historial")

        # Frame de filtros
        filter_frame = ttk.LabelFrame(historical_frame, text="Filtros de Búsqueda")
        filter_frame.pack(fill=tk.X, padx=20, pady=10)

        # Filtros
        filter_grid = ttk.Frame(filter_frame)
        filter_grid.pack(fill=tk.X, pady=5)

        # Filtro por fecha
        ttk.Label(filter_grid, text="Desde:").grid(row=0, column=0, padx=5)
        self.date_from_var = tk.StringVar()
        self.date_from_entry = ttk.Entry(
            filter_grid, textvariable=self.date_from_var, width=12
        )
        self.date_from_entry.grid(row=0, column=1, padx=5)

        ttk.Label(filter_grid, text="Hasta:").grid(row=0, column=2, padx=5)
        self.date_to_var = tk.StringVar()
        self.date_to_entry = ttk.Entry(
            filter_grid, textvariable=self.date_to_var, width=12
        )
        self.date_to_entry.grid(row=0, column=3, padx=5)

        # Botones de filtros
        filter_buttons = ttk.Frame(filter_frame)
        filter_buttons.pack(fill=tk.X, pady=5)

        ttk.Button(filter_buttons, text="Filtrar", command=self.apply_filters).pack(
            side=tk.LEFT, padx=5
        )
        ttk.Button(filter_buttons, text="Limpiar", command=self.clear_filters).pack(
            side=tk.LEFT, padx=5
        )
        ttk.Button(
            filter_buttons, text="Hoy", command=lambda: self.set_date_filter("today")
        ).pack(side=tk.LEFT, padx=5)
        ttk.Button(
            filter_buttons, text="Semana", command=lambda: self.set_date_filter("week")
        ).pack(side=tk.LEFT, padx=5)
        ttk.Button(
            filter_buttons, text="Mes", command=lambda: self.set_date_filter("month")
        ).pack(side=tk.LEFT, padx=5)

        # Frame para lista de entrenamientos
        list_frame = ttk.LabelFrame(historical_frame, text="Entrenamientos Registrados")
        list_frame.pack(fill=tk.BOTH, expand=True, padx=20, pady=10)

        # Treeview para mostrar entrenamientos
        columns = ("Fecha", "Rutina", "Duración", "Calorías", "Completado", "Notas")
        self.workouts_tree = ttk.Treeview(
            list_frame, columns=columns, show="headings", height=12
        )

        # Configurar columnas
        column_widths = {
            "Fecha": 120,
            "Rutina": 150,
            "Duración": 80,
            "Calorías": 80,
            "Completado": 100,
            "Notas": 200,
        }
        for col in columns:
            self.workouts_tree.heading(col, text=col)
            self.workouts_tree.column(col, width=column_widths.get(col, 100))

        # Scrollbar para la lista
        workouts_scrollbar = ttk.Scrollbar(
            list_frame, orient=tk.VERTICAL, command=self.workouts_tree.yview
        )
        self.workouts_tree.configure(yscrollcommand=workouts_scrollbar.set)

        # Empaquetar
        self.workouts_tree.pack(side=tk.LEFT, fill=tk.BOTH, expand=True)
        workouts_scrollbar.pack(side=tk.RIGHT, fill=tk.Y)

        # Evento de doble clic
        self.workouts_tree.bind("<Double-1>", self.on_workout_double_click)

        # Botones de gestión
        management_buttons = ttk.Frame(historical_frame)
        management_buttons.pack(fill=tk.X, pady=10)

        ttk.Button(
            management_buttons, text="👁️ Ver Detalles", command=self.view_workout_details
        ).pack(side=tk.LEFT, padx=5)

        ttk.Button(
            management_buttons, text="✏️ Editar", command=self.edit_selected_workout
        ).pack(side=tk.LEFT, padx=5)

        ttk.Button(
            management_buttons, text="🗑️ Eliminar", command=self.delete_selected_workout
        ).pack(side=tk.LEFT, padx=5)

        ttk.Button(
            management_buttons,
            text="📊 Estadísticas",
            command=self.show_workout_statistics,
        ).pack(side=tk.RIGHT, padx=5)

    def create_new_workout_subtab(self):
        """Crea la pestaña para crear nuevos entrenamientos"""
        # Frame de nuevo entrenamiento
        new_workout_frame = ttk.Frame(self.workout_notebook)
        self.workout_notebook.add(new_workout_frame, text="➕ Nuevo")

        # Frame principal con scroll
        canvas = tk.Canvas(new_workout_frame, height=500)
        scrollbar = ttk.Scrollbar(
            new_workout_frame, orient="vertical", command=canvas.yview
        )
        scrollable_frame = ttk.Frame(canvas)

        scrollable_frame.bind(
            "<Configure>", lambda e: canvas.configure(scrollregion=canvas.bbox("all"))
        )
        canvas.create_window((0, 0), window=scrollable_frame, anchor="nw")
        canvas.configure(yscrollcommand=scrollbar.set)

        canvas.pack(side="left", fill="both", expand=True)
        scrollbar.pack(side="right", fill="y")

        # Formulario de nuevo entrenamiento
        form_frame = ttk.LabelFrame(
            scrollable_frame, text="Datos del Entrenamiento", padding="20"
        )
        form_frame.pack(fill=tk.X, padx=20, pady=10)

        # Variables del formulario
        self.new_workout_vars = {
            "rutina": tk.StringVar(),
            "fecha": tk.StringVar(value=datetime.now().strftime("%Y-%m-%d")),
            "duracion": tk.StringVar(),
            "calorias": tk.StringVar(),
            "completado": tk.BooleanVar(value=True),
            "notas": tk.StringVar(),
        }

        # Campos del formulario
        fields = [
            ("Rutina *:", self.new_workout_vars["rutina"], "combo"),
            ("Fecha *:", self.new_workout_vars["fecha"], "date"),
            ("Duración (min):", self.new_workout_vars["duracion"], "number"),
            ("Calorías quemadas:", self.new_workout_vars["calorias"], "number"),
            ("Completado:", self.new_workout_vars["completado"], "boolean"),
            ("Notas:", self.new_workout_vars["notas"], "text"),
        ]

        for i, (label, var, field_type) in enumerate(fields):
            ttk.Label(form_frame, text=label).grid(
                row=i, column=0, sticky=tk.W, padx=10, pady=8
            )

            if field_type == "combo":
                widget = ttk.Combobox(
                    form_frame, textvariable=var, state="readonly", width=30
                )
                widget.grid(row=i, column=1, padx=10, pady=8, sticky=tk.W)
            elif field_type == "boolean":
                widget = ttk.Checkbutton(form_frame, variable=var)
                widget.grid(row=i, column=1, padx=10, pady=8, sticky=tk.W)
            elif field_type == "text":
                widget = tk.Text(form_frame, width=30, height=4)
                widget.grid(row=i, column=1, padx=10, pady=8, sticky=tk.W)
                # Guardar referencia al widget de texto
                self.new_workout_vars["notas_widget"] = widget
            else:
                widget = ttk.Entry(form_frame, textvariable=var, width=30)
                widget.grid(row=i, column=1, padx=10, pady=8, sticky=tk.W)

        # Frame para botones
        buttons_frame = ttk.Frame(scrollable_frame)
        buttons_frame.pack(fill=tk.X, padx=20, pady=20)

        ttk.Button(
            buttons_frame,
            text="💾 Guardar Entrenamiento",
            command=self.save_new_workout,
        ).pack(side=tk.LEFT, padx=5)

        ttk.Button(
            buttons_frame,
            text="🗑️ Limpiar Formulario",
            command=self.clear_new_workout_form,
        ).pack(side=tk.LEFT, padx=5)

        ttk.Button(
            buttons_frame, text="🔄 Cargar Rutinas", command=self.load_routines_for_form
        ).pack(side=tk.RIGHT, padx=5)

    def create_routines_subtab(self):
        """Crea la pestaña de gestión de rutinas"""
        # Frame de rutinas
        routines_frame = ttk.Frame(self.workout_notebook)
        self.workout_notebook.add(routines_frame, text="📋 Rutinas")

        # Frame de acciones de rutinas
        actions_frame = ttk.Frame(routines_frame)
        actions_frame.pack(fill=tk.X, padx=20, pady=10)

        ttk.Button(
            actions_frame, text="➕ Nueva Rutina", command=self.create_new_routine
        ).pack(side=tk.LEFT, padx=5)

        ttk.Button(
            actions_frame,
            text="🏃 Iniciar Entrenamiento",
            command=self.start_workout_from_routine,
        ).pack(side=tk.LEFT, padx=5)

        ttk.Button(
            actions_frame, text="🔄 Actualizar", command=self.refresh_routines_list
        ).pack(side=tk.RIGHT, padx=5)

        # Frame para lista de rutinas
        routines_list_frame = ttk.LabelFrame(routines_frame, text="Rutinas Disponibles")
        routines_list_frame.pack(fill=tk.BOTH, expand=True, padx=20, pady=10)

        # Treeview para rutinas
        routine_columns = (
            "Nombre",
            "Descripción",
            "Duración",
            "Dificultad",
            "Ejercicios",
        )
        self.routines_tree = ttk.Treeview(
            routines_list_frame, columns=routine_columns, show="headings", height=10
        )

        for col in routine_columns:
            self.routines_tree.heading(col, text=col)
            self.routines_tree.column(col, width=150)

        # Scrollbar para rutinas
        routines_scrollbar = ttk.Scrollbar(
            routines_list_frame, orient=tk.VERTICAL, command=self.routines_tree.yview
        )
        self.routines_tree.configure(yscrollcommand=routines_scrollbar.set)

        # Empaquetar
        self.routines_tree.pack(side=tk.LEFT, fill=tk.BOTH, expand=True)
        routines_scrollbar.pack(side=tk.RIGHT, fill=tk.Y)

        # Botones de gestión de rutinas
        routine_buttons = ttk.Frame(routines_frame)
        routine_buttons.pack(fill=tk.X, pady=10)

        ttk.Button(
            routine_buttons, text="👁️ Ver Detalles", command=self.view_routine_details
        ).pack(side=tk.LEFT, padx=5)

        ttk.Button(
            routine_buttons, text="✏️ Editar", command=self.edit_selected_routine
        ).pack(side=tk.LEFT, padx=5)

        ttk.Button(
            routine_buttons, text="🗑️ Eliminar", command=self.delete_selected_routine
        ).pack(side=tk.LEFT, padx=5)

    def create_progress_subtab(self):
        """Crea la pestaña de seguimiento de progreso"""
        # Frame de progreso
        progress_frame = ttk.Frame(self.workout_notebook)
        self.workout_notebook.add(progress_frame, text="📈 Progreso")

        # Frame de período de análisis
        period_frame = ttk.LabelFrame(progress_frame, text="Período de Análisis")
        period_frame.pack(fill=tk.X, padx=20, pady=10)

        period_buttons = ttk.Frame(period_frame)
        period_buttons.pack(pady=10)

        ttk.Button(
            period_buttons,
            text="Última Semana",
            command=lambda: self.analyze_progress("week"),
        ).pack(side=tk.LEFT, padx=5)

        ttk.Button(
            period_buttons,
            text="Último Mes",
            command=lambda: self.analyze_progress("month"),
        ).pack(side=tk.LEFT, padx=5)

        ttk.Button(
            period_buttons,
            text="Último Trimestre",
            command=lambda: self.analyze_progress("quarter"),
        ).pack(side=tk.LEFT, padx=5)

        ttk.Button(
            period_buttons,
            text="Todo el Historial",
            command=lambda: self.analyze_progress("all"),
        ).pack(side=tk.LEFT, padx=5)

        # Frame para gráficos y estadísticas
        stats_frame = ttk.LabelFrame(progress_frame, text="Estadísticas del Período")
        stats_frame.pack(fill=tk.BOTH, expand=True, padx=20, pady=10)

        # Crear subframes para diferentes tipos de estadísticas
        self.create_progress_statistics(stats_frame)

    def create_progress_statistics(self, parent):
        """Crea las estadísticas de progreso"""
        # Grid para estadísticas
        stats_grid = ttk.Frame(parent)
        stats_grid.pack(fill=tk.BOTH, expand=True)

        # Tarjetas de estadísticas principales
        self.progress_stats = {}
        stats_items = [
            ("entrenamientos_count", "💪 Entrenamientos", "Total realizados", "0"),
            ("calorias_total", "🔥 Calorías", "Total quemadas", "0 cal"),
            ("tiempo_total", "⏱️ Tiempo", "Total invertido", "0 min"),
            ("frecuencia", "📅 Frecuencia", "Por semana", "0"),
            ("mejor_rutina", "🏆 Mejor Rutina", "Más exitosa", "N/A"),
            ("progreso", "📈 Progreso", "Mejora general", "+0%"),
        ]

        for i, (key, icon, title, default) in enumerate(stats_items):
            row = i // 3
            col = i % 3

            card = self.create_progress_card(
                stats_grid, icon, title, default, key, row, col
            )
            self.progress_stats[key] = card

    def create_progress_card(self, parent, icon, title, default, key, row, col):
        """Crea una tarjeta de estadística de progreso"""
        card_frame = ttk.Frame(parent, relief="ridge", borderwidth=2)
        card_frame.grid(row=row, column=col, padx=15, pady=10, sticky="ew")

        # Icono
        icon_label = ttk.Label(card_frame, text=icon, font=("Arial", 20))
        icon_label.pack(pady=(10, 5))

        # Título
        title_label = ttk.Label(card_frame, text=title, font=("Arial", 10, "bold"))
        title_label.pack()

        # Valor
        value_label = ttk.Label(
            card_frame, text=default, font=("Arial", 14, "bold"), foreground="#4A90E2"
        )
        value_label.pack(pady=(5, 10))

        parent.columnconfigure(col, weight=1)

        return {"card_frame": card_frame, "value_label": value_label, "title": title}

    def load_user_data(self):
        """Carga los datos del usuario actual"""
        self.current_user = self.main_window.current_user

        if not self.current_user:
            self.clear_sections()
            return

        try:
            # Cargar entrenamientos
            self.load_workouts_data()

            # Cargar rutinas
            self.load_routines_data()

            # Cargar datos de progreso
            self.analyze_progress("week")  # Análisis inicial

        except Exception as e:
            messagebox.showerror(
                "Error", f"Error al cargar datos de entrenamientos: {e}"
            )

    def load_workouts_data(self):
        """Carga los datos de entrenamientos del usuario"""
        try:
            # Obtener entrenamientos del usuario
            self.workouts_data = self.user_manager.obtener_entrenamientos_recientes(
                self.current_user["id"]
            )

            # Actualizar la vista de entrenamientos
            self.update_workouts_view()

        except Exception as e:
            print(f"Error al cargar entrenamientos: {e}")

    def load_routines_data(self):
        """Carga las rutinas del usuario"""
        try:
            # Obtener rutinas del usuario
            self.routines_data = self.user_manager.obtener_rutinas_usuario(
                self.current_user["id"]
            )

            # Actualizar la vista de rutinas
            self.update_routines_view()

            # Actualizar combo de rutinas en el formulario
            self.load_routines_for_form()

        except Exception as e:
            print(f"Error al cargar rutinas: {e}")

    def update_workouts_view(self):
        """Actualiza la vista de entrenamientos históricos"""
        # Limpiar lista actual
        for item in self.workouts_tree.get_children():
            self.workouts_tree.delete(item)

        # Agregar entrenamientos
        for workout in self.workouts_data:
            completado_text = "✓ Sí" if workout.get("completado", True) else "✗ No"
            notas = (
                workout.get("notas", "")[:50] + "..."
                if len(workout.get("notas", "")) > 50
                else workout.get("notas", "")
            )

            self.workouts_tree.insert(
                "",
                tk.END,
                values=(
                    workout.get("fecha", ""),
                    workout.get("rutina_nombre", ""),
                    f"{workout.get('duracion', 0)} min",
                    f"{workout.get('calorias', 0):.0f} cal",
                    completado_text,
                    notas,
                ),
            )

    def update_routines_view(self):
        """Actualiza la vista de rutinas"""
        # Limpiar lista actual
        for item in self.routines_tree.get_children():
            self.routines_tree.delete(item)

        # Agregar rutinas
        for routine in self.routines_data:
            # Contar ejercicios en la rutina
            ejercicios_count = 0  # En implementación real, contar desde la BD
            if "ejercicios" in routine:
                ejercicios_count = len(routine["ejercicios"])

            self.routines_tree.insert(
                "",
                tk.END,
                values=(
                    routine.get("nombre", ""),
                    routine.get("descripcion", "")[:30] + "..."
                    if len(routine.get("descripcion", "")) > 30
                    else routine.get("descripcion", ""),
                    f"{routine.get('duracion_minutos', 0)} min",
                    routine.get("dificultad", "").title(),
                    str(ejercicios_count),
                ),
            )

    def apply_filters(self):
        """Aplica filtros a la lista de entrenamientos"""
        try:
            # Obtener fechas de filtro
            date_from = self.date_from_var.get()
            date_to = self.date_to_var.get()

            # Filtrar datos (en implementación real sería en la base de datos)
            filtered_data = self.workouts_data.copy()

            if date_from:
                # Filtrar por fecha desde
                filtered_data = [
                    w for w in filtered_data if w.get("fecha", "") >= date_from
                ]

            if date_to:
                # Filtrar por fecha hasta
                filtered_data = [
                    w for w in filtered_data if w.get("fecha", "") <= date_to
                ]

            # Actualizar vista
            temp_data = self.workouts_data
            self.workouts_data = filtered_data
            self.update_workouts_view()
            self.workouts_data = temp_data  # Restaurar datos originales

        except Exception as e:
            messagebox.showerror("Error", f"Error al aplicar filtros: {e}")

    def clear_filters(self):
        """Limpia los filtros"""
        self.date_from_var.set("")
        self.date_to_var.set("")
        self.update_workouts_view()

    def set_date_filter(self, period):
        """Establece filtros de fecha predefinidos"""
        today = datetime.now()

        if period == "today":
            date_str = today.strftime("%Y-%m-%d")
            self.date_from_var.set(date_str)
            self.date_to_var.set(date_str)
        elif period == "week":
            week_ago = today - timedelta(days=7)
            self.date_from_var.set(week_ago.strftime("%Y-%m-%d"))
            self.date_to_var.set(today.strftime("%Y-%m-%d"))
        elif period == "month":
            month_ago = today - timedelta(days=30)
            self.date_from_var.set(month_ago.strftime("%Y-%m-%d"))
            self.date_to_var.set(today.strftime("%Y-%m-%d"))

        self.apply_filters()

    def save_new_workout(self):
        """Guarda un nuevo entrenamiento"""
        try:
            # Validar datos requeridos
            rutina = self.new_workout_vars["rutina"].get()
            fecha = self.new_workout_vars["fecha"].get()

            if not rutina:
                messagebox.showerror("Error", "El campo 'Rutina' es obligatorio")
                return

            if not fecha:
                messagebox.showerror("Error", "El campo 'Fecha' es obligatorio")
                return

            # Obtener valores
            duracion = (
                float(self.new_workout_vars["duracion"].get())
                if self.new_workout_vars["duracion"].get()
                else 0
            )
            calorias = (
                float(self.new_workout_vars["calorias"].get())
                if self.new_workout_vars["calorias"].get()
                else 0
            )
            completado = self.new_workout_vars["completado"].get()

            # Obtener notas del widget de texto
            notas = ""
            if "notas_widget" in self.new_workout_vars:
                notas = self.new_workout_vars["notas_widget"].get(1.0, tk.END).strip()

            # Registrar entrenamiento
            entrenamiento_id = self.db.registrar_entrenamiento(
                usuario_id=self.current_user["id"],
                rutina_id=1,  # En implementación real, obtener el ID real
                duracion_real=int(duracion) if duracion else None,
                calorias_quemadas=calorias if calorias else None,
                completado=completado,
                notas=notas,
            )

            if entrenamiento_id:
                messagebox.showinfo("Éxito", "Entrenamiento registrado correctamente")
                self.clear_new_workout_form()
                self.load_workouts_data()  # Recargar datos
            else:
                messagebox.showerror("Error", "No se pudo registrar el entrenamiento")

        except ValueError:
            messagebox.showerror("Error", "Los datos numéricos no son válidos")
        except Exception as e:
            messagebox.showerror("Error", f"Error inesperado: {e}")

    def clear_new_workout_form(self):
        """Limpia el formulario de nuevo entrenamiento"""
        self.new_workout_vars["rutina"].set("")
        self.new_workout_vars["fecha"].set(datetime.now().strftime("%Y-%m-%d"))
        self.new_workout_vars["duracion"].set("")
        self.new_workout_vars["calorias"].set("")
        self.new_workout_vars["completado"].set(True)

        if "notas_widget" in self.new_workout_vars:
            self.new_workout_vars["notas_widget"].delete(1.0, tk.END)

    def load_routines_for_form(self):
        """Carga las rutinas en el combo del formulario"""
        try:
            # Obtener nombres de rutinas
            routine_names = [
                r.get("nombre", "") for r in self.routines_data if r.get("nombre")
            ]

            # Actualizar combo
            if "rutina" in self.new_workout_vars:
                combo = None
                for child in self.new_workout_vars["rutina"].master.winfo_children():
                    if isinstance(child, ttk.Combobox):
                        combo = child
                        break

                if combo:
                    combo["values"] = routine_names

        except Exception as e:
            print(f"Error al cargar rutinas en formulario: {e}")

    def analyze_progress(self, period):
        """Analiza el progreso del usuario en un período específico"""
        if not self.current_user:
            return

        try:
            # Calcular estadísticas según el período
            filtered_workouts = self.get_workouts_by_period(period)

            # Calcular métricas
            total_entrenamientos = len(filtered_workouts)
            total_calorias = sum(w.get("calorias", 0) for w in filtered_workouts)
            total_tiempo = sum(w.get("duracion", 0) for w in filtered_workouts)

            # Calcular frecuencia semanal
            if period == "week":
                frecuencia = total_entrenamientos
            elif period == "month":
                frecuencia = round(
                    total_entrenamientos / 4.33, 1
                )  # Semanas promedio por mes
            else:
                frecuencia = round(total_entrenamientos / 12, 1)  # Aproximación

            # Rutina más frecuente
            rutina_counts = {}
            for workout in filtered_workouts:
                rutina = workout.get("rutina_nombre", "N/A")
                rutina_counts[rutina] = rutina_counts.get(rutina, 0) + 1

            mejor_rutina = (
                max(rutina_counts.items(), key=lambda x: x[1])[0]
                if rutina_counts
                else "N/A"
            )

            # Calcular progreso (simulado - en implementación real sería comparando con período anterior)
            progreso = min(total_entrenamientos * 2, 50)  # Simulación simple

            # Actualizar tarjetas de estadísticas
            self.progress_stats["entrenamientos_count"]["value_label"].config(
                text=str(total_entrenamientos)
            )
            self.progress_stats["calorias_total"]["value_label"].config(
                text=f"{total_calorias:.0f} cal"
            )
            self.progress_stats["tiempo_total"]["value_label"].config(
                text=f"{total_tiempo} min"
            )
            self.progress_stats["frecuencia"]["value_label"].config(
                text=str(frecuencia)
            )
            self.progress_stats["mejor_rutina"]["value_label"].config(
                text=mejor_rutina[:15]
            )
            self.progress_stats["progreso"]["value_label"].config(text=f"+{progreso}%")

        except Exception as e:
            print(f"Error al analizar progreso: {e}")

    def get_workouts_by_period(self, period):
        """Obtiene entrenamientos filtrados por período"""
        today = datetime.now()

        if period == "week":
            start_date = today - timedelta(days=7)
        elif period == "month":
            start_date = today - timedelta(days=30)
        elif period == "quarter":
            start_date = today - timedelta(days=90)
        else:  # all
            start_date = datetime.min

        # Filtrar entrenamientos
        filtered = []
        for workout in self.workouts_data:
            workout_date_str = workout.get("fecha", "")
            if workout_date_str:
                try:
                    workout_date = datetime.strptime(workout_date_str, "%Y-%m-%d")
                    if workout_date >= start_date:
                        filtered.append(workout)
                except:
                    continue

        return filtered

    def clear_sections(self):
        """Limpia todas las secciones cuando no hay usuario"""
        # Limpiar entrenamientos
        for item in self.workouts_tree.get_children():
            self.workouts_tree.delete(item)

        # Limpiar rutinas
        for item in self.routines_tree.get_children():
            self.routines_tree.delete(item)

        # Limpiar progreso
        for key, card in self.progress_stats.items():
            card["value_label"].config(text="0")

    # Métodos de gestión de entrenamientos
    def on_workout_double_click(self, event):
        """Maneja el doble clic en un entrenamiento"""
        self.view_workout_details()

    def view_workout_details(self):
        """Muestra los detalles del entrenamiento seleccionado"""
        selection = self.workouts_tree.selection()
        if not selection:
            messagebox.showwarning(
                "Advertencia", "Selecciona un entrenamiento para ver detalles"
            )
            return

        # Implementar vista de detalles
        messagebox.showinfo("Info", "Vista de detalles en desarrollo")

    def edit_selected_workout(self):
        """Edita el entrenamiento seleccionado"""
        selection = self.workouts_tree.selection()
        if not selection:
            messagebox.showwarning(
                "Advertencia", "Selecciona un entrenamiento para editar"
            )
            return

        # Implementar edición
        messagebox.showinfo("Info", "Edición de entrenamientos en desarrollo")

    def delete_selected_workout(self):
        """Elimina el entrenamiento seleccionado"""
        selection = self.workouts_tree.selection()
        if not selection:
            messagebox.showwarning(
                "Advertencia", "Selecciona un entrenamiento para eliminar"
            )
            return

        if messagebox.askyesno(
            "Confirmar", "¿Estás seguro de que quieres eliminar este entrenamiento?"
        ):
            # Implementar eliminación
            messagebox.showinfo("Info", "Eliminación de entrenamientos en desarrollo")

    def show_workout_statistics(self):
        """Muestra estadísticas detalladas de entrenamientos"""
        messagebox.showinfo("Info", "Estadísticas detalladas en desarrollo")

    # Métodos de gestión de rutinas
    def create_new_routine(self):
        """Crea una nueva rutina"""
        if not self.current_user:
            messagebox.showwarning("Advertencia", "Selecciona un usuario primero")
            return

        messagebox.showinfo("Info", "Creación de rutinas en desarrollo")

    def start_workout_from_routine(self):
        """Inicia un entrenamiento desde una rutina"""
        if not self.current_user:
            messagebox.showwarning("Advertencia", "Selecciona un usuario primero")
            return

        selection = self.routines_tree.selection()
        if not selection:
            messagebox.showwarning(
                "Advertencia", "Selecciona una rutina para iniciar entrenamiento"
            )
            return

        messagebox.showinfo(
            "Info", "Inicio de entrenamiento desde rutina en desarrollo"
        )

    def refresh_routines_list(self):
        """Actualiza la lista de rutinas"""
        self.load_routines_data()

    def view_routine_details(self):
        """Muestra los detalles de la rutina seleccionada"""
        selection = self.routines_tree.selection()
        if not selection:
            messagebox.showwarning(
                "Advertencia", "Selecciona una rutina para ver detalles"
            )
            return

        messagebox.showinfo("Info", "Detalles de rutina en desarrollo")

    def edit_selected_routine(self):
        """Edita la rutina seleccionada"""
        selection = self.routines_tree.selection()
        if not selection:
            messagebox.showwarning("Advertencia", "Selecciona una rutina para editar")
            return

        messagebox.showinfo("Info", "Edición de rutinas en desarrollo")

    def delete_selected_routine(self):
        """Elimina la rutina seleccionada"""
        selection = self.routines_tree.selection()
        if not selection:
            messagebox.showwarning("Advertencia", "Selecciona una rutina para eliminar")
            return

        if messagebox.askyesno(
            "Confirmar", "¿Estás seguro de que quieres eliminar esta rutina?"
        ):
            messagebox.showinfo("Info", "Eliminación de rutinas en desarrollo")
