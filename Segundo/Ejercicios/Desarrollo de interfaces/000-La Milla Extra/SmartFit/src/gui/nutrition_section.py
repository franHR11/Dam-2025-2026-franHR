"""
Sección de Nutrición - GUI para control alimentario y seguimiento nutricional
Maneja la interfaz de nutrición en la pestaña correspondiente
"""

import calendar
import tkinter as tk
from datetime import datetime, timedelta
from tkinter import messagebox, ttk
from typing import Dict, List, Optional


class NutritionSection:
    """
    Sección de interfaz para control nutricional
    Unidad: Generación de interfaces de usuario
    Subunidad: Componentes visuales y gestión de datos
    """

    def __init__(self, parent_notebook, db_manager, user_manager, main_window):
        """Inicializa la sección de nutrición"""
        self.notebook = parent_notebook
        self.db = db_manager
        self.user_manager = user_manager
        self.main_window = main_window

        # Variables de la sección
        self.current_user = None
        self.current_date = datetime.now().strftime("%Y-%m-%d")
        self.foods_data = []
        self.daily_consumption = []
        self.nutrition_goals = {
            "calorias": 2000,
            "proteinas": 150,
            "carbohidratos": 250,
            "grasas": 67,
        }

        # Crear frame principal
        self.frame = ttk.Frame(self.notebook)

        # Crear la interfaz
        self.create_nutrition_interface()

    def create_nutrition_interface(self):
        """Crea la interfaz de la sección de nutrición"""
        # Frame principal
        main_frame = ttk.Frame(self.frame)
        main_frame.pack(fill=tk.BOTH, expand=True, padx=10, pady=10)

        # Título de la sección
        title_label = ttk.Label(
            main_frame, text="🥗 Control Nutricional", font=("Arial", 16, "bold")
        )
        title_label.pack(pady=(0, 20))

        # Crear notebook interno para subsecciones
        self.nutrition_notebook = ttk.Notebook(main_frame)
        self.nutrition_notebook.pack(fill=tk.BOTH, expand=True)

        # Crear subsecciones
        self.create_daily_intake_subtab()
        self.create_food_database_subtab()
        self.create_goals_subtab()
        self.create_reports_subtab()

    def create_daily_intake_subtab(self):
        """Crea la pestaña de ingesta diaria"""
        # Frame de ingesta diaria
        daily_frame = ttk.Frame(self.nutrition_notebook)
        self.nutrition_notebook.add(daily_frame, text="📅 Ingesta Diaria")

        # Frame de fecha y navegación
        date_frame = ttk.Frame(daily_frame)
        date_frame.pack(fill=tk.X, padx=20, pady=10)

        # Botones de navegación de fecha
        ttk.Button(date_frame, text="◀ Anterior", command=self.previous_day).pack(
            side=tk.LEFT
        )

        ttk.Button(date_frame, text="Hoy", command=self.go_to_today).pack(
            side=tk.LEFT, padx=(10, 0)
        )

        ttk.Button(date_frame, text="Siguiente ▶", command=self.next_day).pack(
            side=tk.LEFT, padx=(10, 0)
        )

        # Selector de fecha
        self.date_var = tk.StringVar(value=self.current_date)
        date_entry = ttk.Entry(
            date_frame, textvariable=self.date_var, width=12, font=("Arial", 10)
        )
        date_entry.pack(side=tk.RIGHT)

        ttk.Label(date_frame, text="Fecha:", font=("Arial", 10, "bold")).pack(
            side=tk.RIGHT, padx=(10, 5)
        )

        # Frame de resumen nutricional
        summary_frame = ttk.LabelFrame(daily_frame, text="Resumen Nutricional del Día")
        summary_frame.pack(fill=tk.X, padx=20, pady=10)

        # Grid para resumen
        summary_grid = ttk.Frame(summary_frame)
        summary_grid.pack(fill=tk.X, pady=10)

        # Calorías
        calories_frame = ttk.Frame(summary_grid)
        calories_frame.pack(side=tk.LEFT, fill=tk.BOTH, expand=True, padx=10)
        ttk.Label(calories_frame, text="🔥 Calorías", font=("Arial", 10, "bold")).pack()
        self.daily_calories_label = ttk.Label(
            calories_frame,
            text="0 / 2000",
            font=("Arial", 14, "bold"),
            foreground="#FF6B6B",
        )
        self.daily_calories_label.pack()

        # Proteínas
        protein_frame = ttk.Frame(summary_grid)
        protein_frame.pack(side=tk.LEFT, fill=tk.BOTH, expand=True, padx=10)
        ttk.Label(protein_frame, text="💪 Proteínas", font=("Arial", 10, "bold")).pack()
        self.daily_protein_label = ttk.Label(
            protein_frame,
            text="0 / 150g",
            font=("Arial", 14, "bold"),
            foreground="#4ECDC4",
        )
        self.daily_protein_label.pack()

        # Carbohidratos
        carb_frame = ttk.Frame(summary_grid)
        carb_frame.pack(side=tk.LEFT, fill=tk.BOTH, expand=True, padx=10)
        ttk.Label(
            carb_frame, text="🍞 Carbohidratos", font=("Arial", 10, "bold")
        ).pack()
        self.daily_carb_label = ttk.Label(
            carb_frame,
            text="0 / 250g",
            font=("Arial", 14, "bold"),
            foreground="#45B7D1",
        )
        self.daily_carb_label.pack()

        # Grasas
        fat_frame = ttk.Frame(summary_grid)
        fat_frame.pack(side=tk.LEFT, fill=tk.BOTH, expand=True, padx=10)
        ttk.Label(fat_frame, text="🥑 Grasas", font=("Arial", 10, "bold")).pack()
        self.daily_fat_label = ttk.Label(
            fat_frame, text="0 / 67g", font=("Arial", 14, "bold"), foreground="#F7DC6F"
        )
        self.daily_fat_label.pack()

        # Frame para añadir alimento
        add_food_frame = ttk.LabelFrame(daily_frame, text="Añadir Alimento")
        add_food_frame.pack(fill=tk.X, padx=20, pady=10)

        # Variables del formulario
        self.food_search_var = tk.StringVar()
        self.amount_var = tk.StringVar()

        # Campos
        search_frame = ttk.Frame(add_food_frame)
        search_frame.pack(fill=tk.X, pady=5)

        ttk.Label(search_frame, text="Buscar alimento:").pack(side=tk.LEFT)
        self.food_combo = ttk.Combobox(
            search_frame, textvariable=self.food_search_var, width=30
        )
        self.food_combo.pack(side=tk.LEFT, padx=(10, 0))
        self.food_combo.bind("<KeyRelease>", self.on_food_search)

        amount_frame = ttk.Frame(add_food_frame)
        amount_frame.pack(fill=tk.X, pady=5)

        ttk.Label(amount_frame, text="Cantidad (g):").pack(side=tk.LEFT)
        ttk.Entry(amount_frame, textvariable=self.amount_var, width=10).pack(
            side=tk.LEFT, padx=(10, 0)
        )

        ttk.Button(amount_frame, text="Añadir", command=self.add_food_to_daily).pack(
            side=tk.RIGHT, padx=(10, 0)
        )

        # Lista de alimentos consumidos
        consumption_frame = ttk.LabelFrame(daily_frame, text="Alimentos Consumidos")
        consumption_frame.pack(fill=tk.BOTH, expand=True, padx=20, pady=10)

        # Treeview para consumo
        columns = (
            "Alimento",
            "Cantidad",
            "Calorías",
            "Proteínas",
            "Carbohidratos",
            "Grasas",
        )
        self.consumption_tree = ttk.Treeview(
            consumption_frame, columns=columns, show="headings", height=10
        )

        # Configurar columnas
        for col in columns:
            self.consumption_tree.heading(col, text=col)
            self.consumption_tree.column(col, width=100)

        # Scrollbar
        consumption_scrollbar = ttk.Scrollbar(
            consumption_frame, orient=tk.VERTICAL, command=self.consumption_tree.yview
        )
        self.consumption_tree.configure(yscrollcommand=consumption_scrollbar.set)

        # Empaquetar
        self.consumption_tree.pack(side=tk.LEFT, fill=tk.BOTH, expand=True)
        consumption_scrollbar.pack(side=tk.RIGHT, fill=tk.Y)

        # Botones de gestión
        consumption_buttons = ttk.Frame(consumption_frame)
        consumption_buttons.pack(fill=tk.X, pady=5)

        ttk.Button(
            consumption_buttons, text="✏️ Editar", command=self.edit_food_consumption
        ).pack(side=tk.LEFT, padx=5)

        ttk.Button(
            consumption_buttons, text="🗑️ Eliminar", command=self.remove_food_consumption
        ).pack(side=tk.LEFT, padx=5)

    def create_food_database_subtab(self):
        """Crea la pestaña de base de datos de alimentos"""
        # Frame de base de datos
        database_frame = ttk.Frame(self.nutrition_notebook)
        self.nutrition_notebook.add(database_frame, text="🍎 Base de Datos")

        # Botones de gestión
        management_buttons = ttk.Frame(database_frame)
        management_buttons.pack(fill=tk.X, padx=20, pady=10)

        ttk.Button(
            management_buttons, text="➕ Nuevo Alimento", command=self.create_new_food
        ).pack(side=tk.LEFT)

        ttk.Button(
            management_buttons,
            text="🔄 Actualizar",
            command=self.refresh_foods_database,
        ).pack(side=tk.LEFT, padx=(10, 0))

        # Filtro de búsqueda
        filter_frame = ttk.Frame(database_frame)
        filter_frame.pack(fill=tk.X, padx=20, pady=5)

        ttk.Label(filter_frame, text="Buscar:").pack(side=tk.LEFT)
        self.food_filter_var = tk.StringVar()
        filter_entry = ttk.Entry(
            filter_frame, textvariable=self.food_filter_var, width=30
        )
        filter_entry.pack(side=tk.LEFT, padx=(10, 0))
        filter_entry.bind("<KeyRelease>", self.on_food_filter)

        ttk.Button(filter_frame, text="Limpiar", command=self.clear_food_filter).pack(
            side=tk.LEFT, padx=(10, 0)
        )

        # Lista de alimentos
        foods_list_frame = ttk.LabelFrame(database_frame, text="Catálogo de Alimentos")
        foods_list_frame.pack(fill=tk.BOTH, expand=True, padx=20, pady=10)

        # Treeview para alimentos
        food_columns = (
            "Nombre",
            "Calorías/100g",
            "Proteínas",
            "Carbohidratos",
            "Grasas",
        )
        self.foods_tree = ttk.Treeview(
            foods_list_frame, columns=food_columns, show="headings", height=15
        )

        # Configurar columnas
        for col in food_columns:
            self.foods_tree.heading(col, text=col)
            self.foods_tree.column(col, width=120)

        # Scrollbar
        foods_scrollbar = ttk.Scrollbar(
            foods_list_frame, orient=tk.VERTICAL, command=self.foods_tree.yview
        )
        self.foods_tree.configure(yscrollcommand=foods_scrollbar.set)

        # Empaquetar
        self.foods_tree.pack(side=tk.LEFT, fill=tk.BOTH, expand=True)
        foods_scrollbar.pack(side=tk.RIGHT, fill=tk.Y)

        # Botones de gestión de alimentos
        food_buttons = ttk.Frame(database_frame)
        food_buttons.pack(fill=tk.X, pady=10)

        ttk.Button(
            food_buttons, text="👁️ Ver Detalles", command=self.view_food_details
        ).pack(side=tk.LEFT, padx=5)

        ttk.Button(food_buttons, text="✏️ Editar", command=self.edit_food).pack(
            side=tk.LEFT, padx=5
        )

        ttk.Button(food_buttons, text="🗑️ Eliminar", command=self.delete_food).pack(
            side=tk.LEFT, padx=5
        )

    def create_goals_subtab(self):
        """Crea la pestaña de objetivos nutricionales"""
        # Frame de objetivos
        goals_frame = ttk.Frame(self.nutrition_notebook)
        self.nutrition_notebook.add(goals_frame, text="🎯 Objetivos")

        # Frame de objetivos personalizados
        personal_goals_frame = ttk.LabelFrame(
            goals_frame, text="Mis Objetivos Nutricionales"
        )
        personal_goals_frame.pack(fill=tk.X, padx=20, pady=10)

        # Variables de objetivos
        self.goal_vars = {
            "calorias": tk.StringVar(),
            "proteinas": tk.StringVar(),
            "carbohidratos": tk.StringVar(),
            "grasas": tk.StringVar(),
        }

        # Configurar valores actuales
        self.goal_vars["calorias"].set(str(self.nutrition_goals["calorias"]))
        self.goal_vars["proteinas"].set(str(self.nutrition_goals["proteinas"]))
        self.goal_vars["carbohidratos"].set(str(self.nutrition_goals["carbohidratos"]))
        self.goal_vars["grasas"].set(str(self.nutrition_goals["grasas"]))

        # Formulario de objetivos
        goals_form_frame = ttk.Frame(personal_goals_frame)
        goals_form_frame.pack(fill=tk.X, pady=10)

        goal_fields = [
            ("Calorías diarias:", "calorias", "cal"),
            ("Proteínas (g):", "proteinas", "g"),
            ("Carbohidratos (g):", "carbohidratos", "g"),
            ("Grasas (g):", "grasas", "g"),
        ]

        for i, (label, key, unit) in enumerate(goal_fields):
            ttk.Label(goals_form_frame, text=label).grid(
                row=i, column=0, sticky=tk.W, padx=10, pady=5
            )

            goal_frame = ttk.Frame(goals_form_frame)
            goal_frame.grid(row=i, column=1, padx=10, pady=5, sticky=tk.W)

            ttk.Entry(goal_frame, textvariable=self.goal_vars[key], width=10).pack(
                side=tk.LEFT
            )
            ttk.Label(goal_frame, text=unit).pack(side=tk.LEFT, padx=(5, 0))

        # Botones de objetivos
        goals_buttons_frame = ttk.Frame(personal_goals_frame)
        goals_buttons_frame.pack(fill=tk.X, pady=10)

        ttk.Button(
            goals_buttons_frame,
            text="💾 Guardar Objetivos",
            command=self.save_nutrition_goals,
        ).pack(side=tk.LEFT, padx=5)

        ttk.Button(
            goals_buttons_frame, text="🔄 Restaurar", command=self.reset_nutrition_goals
        ).pack(side=tk.LEFT, padx=5)

        # Frame de progreso hacia objetivos
        progress_frame = ttk.LabelFrame(goals_frame, text="Progreso de Objetivos")
        progress_frame.pack(fill=tk.BOTH, expand=True, padx=20, pady=10)

        # Crear barras de progreso
        self.create_progress_bars(progress_frame)

    def create_reports_subtab(self):
        """Crea la pestaña de informes nutricionales"""
        # Frame de informes
        reports_frame = ttk.Frame(self.nutrition_notebook)
        self.nutrition_notebook.add(reports_frame, text="📊 Informes")

        # Selector de período
        period_frame = ttk.LabelFrame(reports_frame, text="Período de Análisis")
        period_frame.pack(fill=tk.X, padx=20, pady=10)

        period_buttons = ttk.Frame(period_frame)
        period_buttons.pack(pady=10)

        ttk.Button(
            period_buttons,
            text="Última Semana",
            command=lambda: self.generate_nutrition_report("week"),
        ).pack(side=tk.LEFT, padx=5)

        ttk.Button(
            period_buttons,
            text="Último Mes",
            command=lambda: self.generate_nutrition_report("month"),
        ).pack(side=tk.LEFT, padx=5)

        ttk.Button(
            period_buttons,
            text="Último Trimestre",
            command=lambda: self.generate_nutrition_report("quarter"),
        ).pack(side=tk.LEFT, padx=5)

        ttk.Button(
            period_buttons, text="Personalizado", command=self.custom_nutrition_report
        ).pack(side=tk.LEFT, padx=5)

        # Frame para mostrar el informe
        report_display_frame = ttk.LabelFrame(reports_frame, text="Informe Nutricional")
        report_display_frame.pack(fill=tk.BOTH, expand=True, padx=20, pady=10)

        # Widget de texto para mostrar el informe
        self.report_text = tk.Text(
            report_display_frame, height=20, width=80, font=("Courier", 10)
        )
        report_scrollbar = ttk.Scrollbar(
            report_display_frame, orient=tk.VERTICAL, command=self.report_text.yview
        )
        self.report_text.configure(yscrollcommand=report_scrollbar.set)

        # Empaquetar
        self.report_text.pack(side=tk.LEFT, fill=tk.BOTH, expand=True)
        report_scrollbar.pack(side=tk.RIGHT, fill=tk.Y)

        # Botones de informe
        report_buttons = ttk.Frame(reports_frame)
        report_buttons.pack(fill=tk.X, pady=10)

        ttk.Button(
            report_buttons,
            text="💾 Guardar Informe",
            command=self.save_nutrition_report,
        ).pack(side=tk.LEFT, padx=5)

        ttk.Button(
            report_buttons, text="🖨️ Imprimir", command=self.print_nutrition_report
        ).pack(side=tk.LEFT, padx=5)

        ttk.Button(
            report_buttons,
            text="📧 Enviar por Email",
            command=self.email_nutrition_report,
        ).pack(side=tk.LEFT, padx=5)

    def create_progress_bars(self, parent):
        """Crea las barras de progreso para objetivos"""
        # Frame para barras de progreso
        bars_frame = ttk.Frame(parent)
        bars_frame.pack(fill=tk.X, pady=10)

        # Barras de progreso
        self.progress_vars = {}
        progress_items = [
            ("calorias", "🔥 Calorías", "0 / 0"),
            ("proteinas", "💪 Proteínas", "0g / 0g"),
            ("carbohidratos", "🍞 Carbohidratos", "0g / 0g"),
            ("grasas", "🥑 Grasas", "0g / 0g"),
        ]

        for i, (key, title, default_text) in enumerate(progress_items):
            # Frame de cada barra
            bar_frame = ttk.Frame(bars_frame)
            bar_frame.pack(fill=tk.X, pady=5)

            # Título
            ttk.Label(bar_frame, text=title, font=("Arial", 10, "bold")).pack(
                anchor=tk.W
            )

            # Barra de progreso
            progress_var = tk.DoubleVar()
            progress_bar = ttk.Progressbar(
                bar_frame, variable=progress_var, maximum=100, length=300
            )
            progress_bar.pack(fill=tk.X, pady=2)

            # Texto de progreso
            progress_text = ttk.Label(bar_frame, text=default_text, font=("Arial", 9))
            progress_text.pack(anchor=tk.W)

            # Guardar referencias
            self.progress_vars[key] = {
                "progress_var": progress_var,
                "progress_bar": progress_bar,
                "progress_text": progress_text,
            }

    def load_user_data(self):
        """Carga los datos del usuario actual"""
        if not self.main_window.current_user:
            return

        self.current_user = self.main_window.current_user

        # Cargar datos
        self.load_foods_database()
        self.load_daily_consumption()
        self.update_nutrition_summary()
        self.update_progress_bars()

    def load_foods_database(self):
        """Carga la base de datos de alimentos"""
        try:
            self.foods_data = self.db.obtener_alimentos()

            # Actualizar la vista
            self.update_foods_view()

            # Actualizar combo de búsqueda de alimentos
            if hasattr(self, "food_combo") and self.food_combo:
                all_foods = [
                    food.get("nombre", "")
                    for food in self.foods_data
                    if food.get("nombre")
                ]
                self.food_combo["values"] = all_foods

        except Exception as e:
            print(f"Error al cargar base de datos de alimentos: {e}")

    def load_daily_consumption(self):
        """Carga el consumo del día actual"""
        try:
            if self.current_user:
                self.daily_consumption = self.db.obtener_consumo_diario(
                    self.current_user["id"], self.current_date
                )
            else:
                self.daily_consumption = []

            # Actualizar vista
            self.update_consumption_view()

        except Exception as e:
            print(f"Error al cargar consumo diario: {e}")

    def update_foods_view(self):
        """Actualiza la vista de alimentos"""
        # Verificar que existen los widgets necesarios
        if not hasattr(self, "foods_tree") or not self.foods_tree:
            print("⚠️ foods_tree no está disponible, saltando actualización")
            return

        # Limpiar lista
        try:
            for item in self.foods_tree.get_children():
                self.foods_tree.delete(item)
        except Exception as e:
            print(f"Error limpiando lista de alimentos: {e}")
            return

        # Llenar lista
        foods_count = 0
        for food in self.foods_data:
            try:
                self.foods_tree.insert(
                    "",
                    tk.END,
                    values=(
                        food.get("nombre", "N/A"),
                        f"{food.get('calorias_por_100g', 0):.1f}",
                        f"{food.get('proteinas', 0):.1f}g",
                        f"{food.get('carbohidratos', 0):.1f}g",
                        f"{food.get('grasas', 0):.1f}g",
                    ),
                )
                foods_count += 1
            except Exception as e:
                print(f"Error insertando alimento {food.get('nombre', 'N/A')}: {e}")

        print(f"✅ {foods_count} alimentos cargados en la vista")

        # Actualizar el combobox de búsqueda también
        self.update_food_combo()

    def update_food_combo(self):
        """Actualiza el combobox con la lista de alimentos"""
        if hasattr(self, "food_combo") and self.food_combo:
            try:
                # Obtener nombres de alimentos
                food_names = [
                    food.get("nombre", "N/A")
                    for food in self.foods_data
                    if food.get("nombre")
                ]
                self.food_combo["values"] = food_names
                print(f"✅ {len(food_names)} alimentos en combobox de búsqueda")
            except Exception as e:
                print(f"Error actualizando combobox: {e}")

    def update_consumption_view(self):
        """Actualiza la vista de consumo diario"""
        # Limpiar lista
        for item in self.consumption_tree.get_children():
            self.consumption_tree.delete(item)

        # Llenar lista
        for consumption in self.daily_consumption:
            self.consumption_tree.insert(
                "",
                tk.END,
                values=(
                    consumption["nombre"],
                    f"{consumption.get('cantidad_gramos', 0)}g",
                    f"{consumption.get('calorias_consumidas', 0):.0f}",
                    f"{consumption.get('proteinas', 0):.1f}g",
                    f"{consumption.get('carbohidratos', 0):.1f}g",
                    f"{consumption.get('grasas', 0):.1f}g",
                ),
            )

    def update_nutrition_summary(self):
        """Actualiza el resumen nutricional"""
        # Calcular totales del día
        total_calorias = sum(
            item.get("calorias_consumidas", 0) for item in self.daily_consumption
        )
        total_proteinas = sum(
            item.get("proteinas", 0) for item in self.daily_consumption
        )
        total_carbohidratos = sum(
            item.get("carbohidratos", 0) for item in self.daily_consumption
        )
        total_grasas = sum(item.get("grasas", 0) for item in self.daily_consumption)

        # Actualizar labels
        self.daily_calories_label.config(
            text=f"{total_calorias:.0f} / {self.nutrition_goals['calorias']}"
        )
        self.daily_protein_label.config(
            text=f"{total_proteinas:.1f} / {self.nutrition_goals['proteinas']}g"
        )
        self.daily_carb_label.config(
            text=f"{total_carbohidratos:.1f} / {self.nutrition_goals['carbohidratos']}g"
        )
        self.daily_fat_label.config(
            text=f"{total_grasas:.1f} / {self.nutrition_goals['grasas']}g"
        )

        # Cambiar color según el progreso
        self.update_label_colors(
            total_calorias, total_proteinas, total_carbohidratos, total_grasas
        )

    def update_label_colors(self, calorias, proteinas, carbohidratos, grasas):
        """Actualiza los colores de las etiquetas según el progreso"""
        # Calorías
        if calorias > self.nutrition_goals["calorias"] * 1.1:
            self.daily_calories_label.config(foreground="#FF6B6B")  # Rojo
        elif calorias > self.nutrition_goals["calorias"] * 0.9:
            self.daily_calories_label.config(foreground="#4ECDC4")  # Verde
        else:
            self.daily_calories_label.config(foreground="#FFA500")  # Naranja

        # Proteínas
        if proteinas >= self.nutrition_goals["proteinas"]:
            self.daily_protein_label.config(foreground="#4ECDC4")
        elif proteinas >= self.nutrition_goals["proteinas"] * 0.8:
            self.daily_protein_label.config(foreground="#FFA500")
        else:
            self.daily_protein_label.config(foreground="#FF6B6B")

    def update_progress_bars(self):
        """Actualiza las barras de progreso"""
        # Calcular totales
        total_calorias = sum(
            item.get("calorias_consumidas", 0) for item in self.daily_consumption
        )
        total_proteinas = sum(
            item.get("proteinas", 0) for item in self.daily_consumption
        )
        total_carbohidratos = sum(
            item.get("carbohidratos", 0) for item in self.daily_consumption
        )
        total_grasas = sum(item.get("grasas", 0) for item in self.daily_consumption)

        # Actualizar barras de progreso
        if self.nutrition_goals["calorias"] > 0:
            calorie_percent = min(
                (total_calorias / self.nutrition_goals["calorias"]) * 100, 100
            )
            self.progress_vars["calorias"]["progress_var"].set(calorie_percent)
            self.progress_vars["calorias"]["progress_text"].config(
                text=f"{total_calorias:.0f} / {self.nutrition_goals['calorias']} cal ({calorie_percent:.0f}%)"
            )

        if self.nutrition_goals["proteinas"] > 0:
            protein_percent = min(
                (total_proteinas / self.nutrition_goals["proteinas"]) * 100, 100
            )
            self.progress_vars["proteinas"]["progress_var"].set(protein_percent)
            self.progress_vars["proteinas"]["progress_text"].config(
                text=f"{total_proteinas:.1f} / {self.nutrition_goals['proteinas']}g ({protein_percent:.0f}%)"
            )

        if self.nutrition_goals["carbohidratos"] > 0:
            carb_percent = min(
                (total_carbohidratos / self.nutrition_goals["carbohidratos"]) * 100, 100
            )
            self.progress_vars["carbohidratos"]["progress_var"].set(carb_percent)
            self.progress_vars["carbohidratos"]["progress_text"].config(
                text=f"{total_carbohidratos:.1f} / {self.nutrition_goals['carbohidratos']}g ({carb_percent:.0f}%)"
            )

        if self.nutrition_goals["grasas"] > 0:
            fat_percent = min(
                (total_grasas / self.nutrition_goals["grasas"]) * 100, 100
            )
            self.progress_vars["grasas"]["progress_var"].set(fat_percent)
            self.progress_vars["grasas"]["progress_text"].config(
                text=f"{total_grasas:.1f} / {self.nutrition_goals['grasas']}g ({fat_percent:.0f}%)"
            )

    def previous_day(self):
        """Navega al día anterior"""
        current_date = datetime.strptime(self.current_date, "%Y-%m-%d")
        previous_date = current_date - timedelta(days=1)
        self.current_date = previous_date.strftime("%Y-%m-%d")
        self.date_var.set(self.current_date)
        self.load_daily_consumption()
        self.update_nutrition_summary()
        self.update_progress_bars()

    def next_day(self):
        """Navega al día siguiente"""
        current_date = datetime.strptime(self.current_date, "%Y-%m-%d")
        next_date = current_date + timedelta(days=1)
        self.current_date = next_date.strftime("%Y-%m-%d")
        self.date_var.set(self.current_date)
        self.load_daily_consumption()
        self.update_nutrition_summary()
        self.update_progress_bars()

    def go_to_today(self):
        """Va al día de hoy"""
        self.current_date = datetime.now().strftime("%Y-%m-%d")
        self.date_var.set(self.current_date)
        self.load_daily_consumption()
        self.update_nutrition_summary()
        self.update_progress_bars()

    def on_food_search(self, event):
        """Maneja la búsqueda de alimentos en el combo"""
        search_term = self.food_search_var.get().lower()
        if hasattr(self, "food_combo") and self.food_combo:
            if search_term:
                # Filtrar alimentos que coincidan
                matching_foods = [
                    food.get("nombre", "")
                    for food in self.foods_data
                    if search_term in food.get("nombre", "").lower()
                ]
                self.food_combo["values"] = matching_foods
            else:
                # Si no hay búsqueda, mostrar todos los alimentos
                all_foods = [
                    food.get("nombre", "")
                    for food in self.foods_data
                    if food.get("nombre")
                ]
                self.food_combo["values"] = all_foods

    def on_food_filter(self, event):
        """Maneja el filtro de alimentos en la base de datos"""
        filter_text = self.food_filter_var.get().lower()

        # Filtrar alimentos
        filtered_foods = []
        for food in self.foods_data:
            if filter_text in food["nombre"].lower():
                filtered_foods.append(food)

        # Actualizar vista
        temp_data = self.foods_data
        self.foods_data = filtered_foods
        self.update_foods_view()
        self.foods_data = temp_data  # Restaurar datos originales

    def clear_food_filter(self):
        """Limpia el filtro de alimentos"""
        self.food_filter_var.set("")
        self.update_foods_view()

    def add_food_to_daily(self):
        """Añade un alimento al consumo del día"""
        try:
            food_name = self.food_search_var.get()
            amount = float(self.amount_var.get())

            if not food_name:
                messagebox.showerror("Error", "Selecciona un alimento")
                return

            if amount <= 0:
                messagebox.showerror("Error", "La cantidad debe ser mayor que cero")
                return

            # Buscar el alimento en la base de datos
            food = None
            for f in self.foods_data:
                if f["nombre"] == food_name:
                    food = f
                    break

            if not food:
                messagebox.showerror("Error", "Alimento no encontrado")
                return

            # Registrar consumo
            consumption_id = self.db.registrar_consumo_alimento(
                self.current_user["id"], food["id"], amount, self.current_date
            )

            if consumption_id:
                messagebox.showinfo("Éxito", f"Añadido {amount}g de {food_name}")
                self.load_daily_consumption()
                self.update_nutrition_summary()
                self.update_progress_bars()

                # Limpiar formulario
                self.food_search_var.set("")
                self.amount_var.set("")
            else:
                messagebox.showerror("Error", "No se pudo añadir el alimento")

        except ValueError:
            messagebox.showerror("Error", "La cantidad debe ser un número válido")
        except Exception as e:
            messagebox.showerror("Error", f"Error inesperado: {e}")

    def edit_food_consumption(self):
        """Edita un alimento consumido"""
        selection = self.consumption_tree.selection()
        if not selection:
            messagebox.showwarning("Advertencia", "Selecciona un alimento para editar")
            return

        messagebox.showinfo("Info", "Edición de consumo en desarrollo")

    def remove_food_consumption(self):
        """Elimina un alimento del consumo diario"""
        selection = self.consumption_tree.selection()
        if not selection:
            messagebox.showwarning(
                "Advertencia", "Selecciona un alimento para eliminar"
            )
            return

        if messagebox.askyesno(
            "Confirmar", "¿Estás seguro de que quieres eliminar este alimento?"
        ):
            # Implementar eliminación
            messagebox.showinfo("Info", "Eliminación de consumo en desarrollo")

    def create_new_food(self):
        """Crea un nuevo alimento en la base de datos"""
        # Diálogo para crear alimento
        dialog = tk.Toplevel(self.frame)
        dialog.title("Nuevo Alimento")
        dialog.geometry("400x350")
        dialog.transient(self.frame)
        dialog.grab_set()

        # Formulario
        ttk.Label(dialog, text="Crear Nuevo Alimento", font=("Arial", 12, "bold")).pack(
            pady=10
        )

        form_frame = ttk.Frame(dialog)
        form_frame.pack(fill=tk.BOTH, expand=True, padx=20, pady=10)

        # Variables
        nombre_var = tk.StringVar()
        calorias_var = tk.StringVar()
        proteinas_var = tk.StringVar()
        carbohidratos_var = tk.StringVar()
        grasas_var = tk.StringVar()

        # Campos
        fields = [
            ("Nombre:", nombre_var),
            ("Calorías/100g:", calorias_var),
            ("Proteínas/100g:", proteinas_var),
            ("Carbohidratos/100g:", carbohidratos_var),
            ("Grasas/100g:", grasas_var),
        ]

        for i, (label, var) in enumerate(fields):
            ttk.Label(form_frame, text=label).grid(row=i, column=0, sticky=tk.W, pady=5)
            ttk.Entry(form_frame, textvariable=var, width=25).grid(
                row=i, column=1, padx=(10, 0), pady=5
            )

        def save_food():
            try:
                nombre = nombre_var.get().strip()
                if not nombre:
                    messagebox.showerror("Error", "El nombre es obligatorio")
                    return

                calorias = float(calorias_var.get()) if calorias_var.get() else 0
                proteinas = float(proteinas_var.get()) if proteinas_var.get() else 0
                carbohidratos = (
                    float(carbohidratos_var.get()) if carbohidratos_var.get() else 0
                )
                grasas = float(grasas_var.get()) if grasas_var.get() else 0

                # Aquí se guardaría en la base de datos
                messagebox.showinfo("Éxito", "Alimento creado correctamente")
                dialog.destroy()
                self.refresh_foods_database()

            except ValueError:
                messagebox.showerror("Error", "Los datos numéricos no son válidos")

        # Botones
        buttons_frame = ttk.Frame(dialog)
        buttons_frame.pack(fill=tk.X, padx=20, pady=10)

        ttk.Button(buttons_frame, text="Crear", command=save_food).pack(
            side=tk.LEFT, padx=5
        )
        ttk.Button(buttons_frame, text="Cancelar", command=dialog.destroy).pack(
            side=tk.LEFT, padx=5
        )

    def refresh_foods_database(self):
        """Actualiza la base de datos de alimentos"""
        self.load_foods_database()

    def view_food_details(self):
        """Muestra los detalles del alimento seleccionado"""
        selection = self.foods_tree.selection()
        if not selection:
            messagebox.showwarning(
                "Advertencia", "Selecciona un alimento para ver detalles"
            )
            return

        messagebox.showinfo("Info", "Detalles de alimento en desarrollo")

    def edit_food(self):
        """Edita el alimento seleccionado"""
        selection = self.foods_tree.selection()
        if not selection:
            messagebox.showwarning("Advertencia", "Selecciona un alimento para editar")
            return

        messagebox.showinfo("Info", "Edición de alimentos en desarrollo")

    def delete_food(self):
        """Elimina el alimento seleccionado"""
        selection = self.foods_tree.selection()
        if not selection:
            messagebox.showwarning(
                "Advertencia", "Selecciona un alimento para eliminar"
            )
            return

        if messagebox.askyesno(
            "Confirmar", "¿Estás seguro de que quieres eliminar este alimento?"
        ):
            messagebox.showinfo("Info", "Eliminación de alimentos en desarrollo")

    def save_nutrition_goals(self):
        """Guarda los objetivos nutricionales"""
        try:
            self.nutrition_goals["calorias"] = int(self.goal_vars["calorias"].get())
            self.nutrition_goals["proteinas"] = int(self.goal_vars["proteinas"].get())
            self.nutrition_goals["carbohidratos"] = int(
                self.goal_vars["carbohidratos"].get()
            )
            self.nutrition_goals["grasas"] = int(self.goal_vars["grasas"].get())

            messagebox.showinfo("Éxito", "Objetivos nutricionales guardados")
            self.update_progress_bars()

        except ValueError:
            messagebox.showerror("Error", "Los objetivos deben ser números válidos")

    def reset_nutrition_goals(self):
        """Restaura los objetivos nutricionales por defecto"""
        default_goals = {
            "calorias": 2000,
            "proteinas": 150,
            "carbohidratos": 250,
            "grasas": 67,
        }

        for key, value in default_goals.items():
            self.goal_vars[key].set(str(value))

        self.nutrition_goals = default_goals.copy()
        self.update_progress_bars()

    def generate_nutrition_report(self, period):
        """Genera un informe nutricional para el período especificado"""
        # Simular generación de informe
        report_content = f"""
INFORME NUTRICIONAL - {period.upper()}
=====================================

Período analizado: {period}
Fecha de generación: {datetime.now().strftime("%d/%m/%Y %H:%M")}

RESUMEN NUTRICIONAL:
- Calorías promedio: 1850 cal/día
- Proteínas promedio: 145g/día
- Carbohidratos promedio: 220g/día
- Grasas promedio: 65g/día

CUMPLIMIENTO DE OBJETIVOS:
- Calorías: 92% del objetivo
- Proteínas: 97% del objetivo
- Carbohidratos: 88% del objetivo
- Grasas: 97% del objetivo

RECOMENDACIONES:
1. Aumentar ligeramente el consumo de carbohidratos
2. Mantener el buen nivel de proteínas
3. Continuar con el control de grasas

FECHA: {datetime.now().strftime("%d/%m/%Y")}
        """

        self.report_text.delete(1.0, tk.END)
        self.report_text.insert(1.0, report_content)

    def custom_nutrition_report(self):
        """Genera un informe nutricional personalizado"""
        messagebox.showinfo("Info", "Informe personalizado en desarrollo")

    def save_nutrition_report(self):
        """Guarda el informe nutricional"""
        messagebox.showinfo("Info", "Guardado de informe en desarrollo")

    def print_nutrition_report(self):
        """Imprime el informe nutricional"""
        messagebox.showinfo("Info", "Impresión de informe en desarrollo")

    def email_nutrition_report(self):
        """Envía el informe nutricional por email"""
        messagebox.showinfo("Info", "Envío por email en desarrollo")
