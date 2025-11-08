# reports_section.py - Sección de informes para SmartFit
# Fran - Desarrollo de interfaces

import json
import tkinter as tk
from datetime import datetime, timedelta
from tkinter import filedialog, messagebox, ttk
from typing import Dict, List, Optional


class ReportsSection:
    """
    Sección de interfaz para generación de informes
    Unidad: Generación de interfaces de usuario
    Subunidad: Creación de informes y reportes gráficos

    Esta clase maneja:
    - Generación de informes de entrenamiento
    - Informes nutricionales
    - Análisis de progreso
    - Exportación de datos
    - Visualización de estadísticas
    """

    def __init__(self, parent_notebook, db_manager, user_manager, main_window):
        """Inicializa la sección de informes"""
        self.notebook = parent_notebook
        self.db = db_manager
        self.user_manager = user_manager
        self.main_window = main_window

        # Variables de la sección
        self.current_user = None
        self.current_report_data = {}
        self.report_types = {
            "entrenamiento": "Informe de Entrenamientos",
            "nutricion": "Informe Nutricional",
            "progreso": "Informe de Progreso",
            "completo": "Informe Completo",
        }

        # Crear frame principal
        self.frame = ttk.Frame(self.notebook)

        # Crear la interfaz
        self.create_reports_interface()

    def create_reports_interface(self):
        """Crea la interfaz principal de informes"""
        # Frame principal
        main_frame = ttk.Frame(self.frame)
        main_frame.pack(fill=tk.BOTH, expand=True, padx=10, pady=10)

        # Título de la sección
        title_label = ttk.Label(
            main_frame, text="📊 Informes y Análisis", font=("Arial", 16, "bold")
        )
        title_label.pack(pady=(0, 20))

        # Crear notebook interno para subsecciones
        self.reports_notebook = ttk.Notebook(main_frame)
        self.reports_notebook.pack(fill=tk.BOTH, expand=True)

        # Crear subsecciones
        self.create_report_generator_subtab()
        self.create_statistics_subtab()
        self.create_progress_analysis_subtab()
        self.create_export_subtab()

    def create_report_generator_subtab(self):
        """Crea la pestaña de generador de informes"""
        # Frame de generador
        generator_frame = ttk.Frame(self.reports_notebook)
        self.reports_notebook.add(generator_frame, text="🛠️ Generador")

        # Configuración del informe
        config_frame = ttk.LabelFrame(generator_frame, text="Configuración del Informe")
        config_frame.pack(fill=tk.X, padx=20, pady=10)

        # Variables de configuración
        self.report_config = {
            "type": tk.StringVar(value="entrenamiento"),
            "period": tk.StringVar(value="month"),
            "start_date": tk.StringVar(),
            "end_date": tk.StringVar(),
            "include_charts": tk.BooleanVar(value=True),
            "include_recommendations": tk.BooleanVar(value=True),
        }

        # Configurar fechas por defecto
        today = datetime.now()
        last_month = today - timedelta(days=30)
        self.report_config["start_date"].set(last_month.strftime("%Y-%m-%d"))
        self.report_config["end_date"].set(today.strftime("%Y-%m-%d"))

        # Formulario de configuración
        form_frame = ttk.Frame(config_frame)
        form_frame.pack(fill=tk.X, pady=10)

        # Tipo de informe
        ttk.Label(form_frame, text="Tipo de informe:").grid(
            row=0, column=0, sticky=tk.W, padx=10, pady=5
        )
        type_combo = ttk.Combobox(
            form_frame,
            textvariable=self.report_config["type"],
            state="readonly",
            width=20,
        )
        type_combo["values"] = list(self.report_types.values())
        type_combo.grid(row=0, column=1, padx=10, pady=5, sticky=tk.W)

        # Período
        ttk.Label(form_frame, text="Período:").grid(
            row=1, column=0, sticky=tk.W, padx=10, pady=5
        )
        period_combo = ttk.Combobox(
            form_frame,
            textvariable=self.report_config["period"],
            state="readonly",
            width=20,
        )
        period_combo["values"] = [
            ("Semana", "week"),
            ("Mes", "month"),
            ("Trimestre", "quarter"),
            ("Año", "year"),
            ("Personalizado", "custom"),
        ]
        period_combo.grid(row=1, column=1, padx=10, pady=5, sticky=tk.W)

        # Fechas personalizadas
        dates_frame = ttk.Frame(form_frame)
        dates_frame.grid(row=2, column=0, columnspan=2, sticky=tk.EW, pady=5)

        ttk.Label(dates_frame, text="Desde:").pack(side=tk.LEFT)
        ttk.Entry(
            dates_frame, textvariable=self.report_config["start_date"], width=12
        ).pack(side=tk.LEFT, padx=(10, 20))
        ttk.Label(dates_frame, text="Hasta:").pack(side=tk.LEFT)
        ttk.Entry(
            dates_frame, textvariable=self.report_config["end_date"], width=12
        ).pack(side=tk.LEFT, padx=(10, 0))

        # Opciones
        options_frame = ttk.Frame(form_frame)
        options_frame.grid(row=3, column=0, columnspan=2, sticky=tk.W, pady=10)

        ttk.Checkbutton(
            options_frame,
            text="Incluir gráficos",
            variable=self.report_config["include_charts"],
        ).pack(side=tk.LEFT, padx=(0, 20))
        ttk.Checkbutton(
            options_frame,
            text="Incluir recomendaciones",
            variable=self.report_config["include_recommendations"],
        ).pack(side=tk.LEFT)

        # Botones de acción
        action_buttons = ttk.Frame(config_frame)
        action_buttons.pack(fill=tk.X, pady=10)

        ttk.Button(
            action_buttons, text="📊 Generar Informe", command=self.generate_report
        ).pack(side=tk.LEFT, padx=5)

        ttk.Button(
            action_buttons, text="👁️ Vista Previa", command=self.preview_report
        ).pack(side=tk.LEFT, padx=5)

        ttk.Button(action_buttons, text="💾 Guardar", command=self.save_report).pack(
            side=tk.LEFT, padx=5
        )

        ttk.Button(
            action_buttons, text="🗑️ Limpiar", command=self.clear_report_config
        ).pack(side=tk.LEFT, padx=5)

        # Vista previa del informe
        preview_frame = ttk.LabelFrame(generator_frame, text="Vista Previa")
        preview_frame.pack(fill=tk.BOTH, expand=True, padx=20, pady=10)

        # Widget de texto para vista previa
        self.report_preview = tk.Text(
            preview_frame, height=15, width=80, font=("Courier", 10), wrap=tk.WORD
        )
        preview_scrollbar = ttk.Scrollbar(
            preview_frame, orient=tk.VERTICAL, command=self.report_preview.yview
        )
        self.report_preview.configure(yscrollcommand=preview_scrollbar.set)

        # Empaquetar
        self.report_preview.pack(side=tk.LEFT, fill=tk.BOTH, expand=True)
        preview_scrollbar.pack(side=tk.RIGHT, fill=tk.Y)

        # Cargar vista previa inicial
        self.preview_report()

    def create_statistics_subtab(self):
        """Crea la pestaña de estadísticas"""
        # Frame de estadísticas
        stats_frame = ttk.Frame(self.reports_notebook)
        self.reports_notebook.add(stats_frame, text="📈 Estadísticas")

        # Frame de período de análisis
        period_frame = ttk.LabelFrame(stats_frame, text="Período de Análisis")
        period_frame.pack(fill=tk.X, padx=20, pady=10)

        period_buttons = ttk.Frame(period_frame)
        period_buttons.pack(pady=10)

        ttk.Button(
            period_buttons,
            text="Esta Semana",
            command=lambda: self.set_analysis_period("week"),
        ).pack(side=tk.LEFT, padx=5)

        ttk.Button(
            period_buttons,
            text="Este Mes",
            command=lambda: self.set_analysis_period("month"),
        ).pack(side=tk.LEFT, padx=5)

        ttk.Button(
            period_buttons,
            text="Últimos 3 Meses",
            command=lambda: self.set_analysis_period("quarter"),
        ).pack(side=tk.LEFT, padx=5)

        ttk.Button(
            period_buttons,
            text="Todo el Historial",
            command=lambda: self.set_analysis_period("all"),
        ).pack(side=tk.LEFT, padx=5)

        # Frame para gráficos y métricas
        metrics_frame = ttk.LabelFrame(stats_frame, text="Métricas Principales")
        metrics_frame.pack(fill=tk.BOTH, expand=True, padx=20, pady=10)

        # Crear grid de métricas
        self.create_statistics_grid(metrics_frame)

    def create_statistics_grid(self, parent):
        """Crea el grid de estadísticas"""
        # Frame contenedor con scroll
        canvas = tk.Canvas(parent, height=400)
        scrollbar = ttk.Scrollbar(parent, orient=tk.VERTICAL, command=canvas.yview)
        scrollable_frame = ttk.Frame(canvas)

        scrollable_frame.bind(
            "<Configure>", lambda e: canvas.configure(scrollregion=canvas.bbox("all"))
        )

        canvas.create_window((0, 0), window=scrollable_frame, anchor="nw")
        canvas.configure(yscrollcommand=scrollbar.set)

        canvas.pack(side="left", fill="both", expand=True)
        scrollbar.pack(side="right", fill="y")

        # Tarjetas de estadísticas
        self.statistics_cards = {}
        stats_data = [
            ("entrenamientos", "💪", "Total Entrenamientos", "0"),
            ("calorias_quemadas", "🔥", "Calorías Quemadas", "0 cal"),
            ("tiempo_entrenamiento", "⏱️", "Tiempo Total", "0 min"),
            ("frecuencia_semanal", "📅", "Frecuencia Semanal", "0/sem"),
            ("mejor_rutina", "🏆", "Rutina Más Realizada", "N/A"),
            ("progreso_calorias", "📈", "Progreso Calorías", "+0%"),
            ("dias_activos", "✅", "Días Activos", "0 días"),
            ("promedio_diario", "📊", "Promedio Diario", "0 cal"),
        ]

        for i, (key, icon, title, default) in enumerate(stats_data):
            row = i // 3
            col = i % 3

            card = self.create_stat_card(
                scrollable_frame, icon, title, default, key, row, col
            )
            self.statistics_cards[key] = card

    def create_stat_card(self, parent, icon, title, default, key, row, col):
        """Crea una tarjeta de estadística individual"""
        # Frame de la tarjeta
        card = ttk.Frame(parent, relief="solid", borderwidth=1)
        card.grid(row=row, column=col, padx=15, pady=10, sticky="ew")

        # Icono
        icon_label = ttk.Label(card, text=icon, font=("Arial", 24))
        icon_label.pack(pady=(10, 5))

        # Título
        title_label = ttk.Label(card, text=title, font=("Arial", 9, "bold"))
        title_label.pack()

        # Valor
        value_label = ttk.Label(
            card, text=default, font=("Arial", 12, "bold"), foreground="#4A90E2"
        )
        value_label.pack(pady=(5, 10))

        # Configurar grid
        parent.columnconfigure(col, weight=1)

        return {"card_frame": card, "value_label": value_label, "title": title}

    def create_progress_analysis_subtab(self):
        """Crea la pestaña de análisis de progreso"""
        # Frame de análisis
        analysis_frame = ttk.Frame(self.reports_notebook)
        self.reports_notebook.add(analysis_frame, text="📉 Análisis")

        # Frame de comparación de períodos
        comparison_frame = ttk.LabelFrame(
            analysis_frame, text="Comparación de Períodos"
        )
        comparison_frame.pack(fill=tk.X, padx=20, pady=10)

        # Variables de comparación
        self.comparison_vars = {
            "period1": tk.StringVar(value="month"),
            "period2": tk.StringVar(value="previous_month"),
        }

        comparison_form = ttk.Frame(comparison_frame)
        comparison_form.pack(fill=tk.X, pady=10)

        ttk.Label(comparison_form, text="Período 1:").grid(
            row=0, column=0, sticky=tk.W, padx=10, pady=5
        )
        period1_combo = ttk.Combobox(
            comparison_form,
            textvariable=self.comparison_vars["period1"],
            state="readonly",
            width=15,
        )
        period1_combo["values"] = ["week", "month", "quarter", "year"]
        period1_combo.grid(row=0, column=1, padx=10, pady=5, sticky=tk.W)

        ttk.Label(comparison_form, text="Período 2:").grid(
            row=1, column=0, sticky=tk.W, padx=10, pady=5
        )
        period2_combo = ttk.Combobox(
            comparison_form,
            textvariable=self.comparison_vars["period2"],
            state="readonly",
            width=15,
        )
        period2_combo["values"] = [
            "previous_week",
            "previous_month",
            "previous_quarter",
            "previous_year",
        ]
        period2_combo.grid(row=1, column=1, padx=10, pady=5, sticky=tk.W)

        ttk.Button(
            comparison_form, text="🔍 Comparar", command=self.compare_periods
        ).grid(row=2, column=0, columnspan=2, pady=10)

        # Resultados de la comparación
        results_frame = ttk.LabelFrame(
            analysis_frame, text="Resultados de la Comparación"
        )
        results_frame.pack(fill=tk.BOTH, expand=True, padx=20, pady=10)

        # Widget de texto para resultados
        self.comparison_results = tk.Text(
            results_frame, height=20, width=80, font=("Courier", 10)
        )
        comparison_scrollbar = ttk.Scrollbar(
            results_frame, orient=tk.VERTICAL, command=self.comparison_results.yview
        )
        self.comparison_results.configure(yscrollcommand=comparison_scrollbar.set)

        # Empaquetar
        self.comparison_results.pack(side=tk.LEFT, fill=tk.BOTH, expand=True)
        comparison_scrollbar.pack(side=tk.RIGHT, fill=tk.Y)

    def create_export_subtab(self):
        """Crea la pestaña de exportación"""
        # Frame de exportación
        export_frame = ttk.Frame(self.reports_notebook)
        self.reports_notebook.add(export_frame, text="💾 Exportar")

        # Opciones de exportación
        export_options_frame = ttk.LabelFrame(
            export_frame, text="Opciones de Exportación"
        )
        export_options_frame.pack(fill=tk.X, padx=20, pady=10)

        # Variables de exportación
        self.export_options = {
            "format": tk.StringVar(value="pdf"),
            "include_charts": tk.BooleanVar(value=True),
            "include_raw_data": tk.BooleanVar(value=True),
            "date_range": tk.StringVar(value="month"),
        }

        # Selección de formato
        format_frame = ttk.Frame(export_options_frame)
        format_frame.pack(fill=tk.X, pady=5)

        ttk.Label(format_frame, text="Formato:").pack(side=tk.LEFT)
        format_combo = ttk.Combobox(
            format_frame,
            textvariable=self.export_options["format"],
            state="readonly",
            width=15,
        )
        format_combo["values"] = ["PDF", "Excel", "CSV", "JSON"]
        format_combo.pack(side=tk.LEFT, padx=(10, 30))

        # Rango de fechas
        ttk.Label(format_frame, text="Rango:").pack(side=tk.LEFT)
        date_combo = ttk.Combobox(
            format_frame,
            textvariable=self.export_options["date_range"],
            state="readonly",
            width=15,
        )
        date_combo["values"] = ["week", "month", "quarter", "year", "all"]
        date_combo.pack(side=tk.LEFT, padx=(10, 0))

        # Opciones adicionales
        options_frame = ttk.Frame(export_options_frame)
        options_frame.pack(fill=tk.X, pady=10)

        ttk.Checkbutton(
            options_frame,
            text="Incluir gráficos",
            variable=self.export_options["include_charts"],
        ).pack(side=tk.LEFT, padx=(0, 20))
        ttk.Checkbutton(
            options_frame,
            text="Incluir datos en bruto",
            variable=self.export_options["include_raw_data"],
        ).pack(side=tk.LEFT)

        # Botones de exportación
        export_buttons_frame = ttk.Frame(export_frame)
        export_buttons_frame.pack(fill=tk.X, padx=20, pady=20)

        ttk.Button(
            export_buttons_frame,
            text="📄 Exportar PDF",
            command=lambda: self.export_report("pdf"),
        ).pack(side=tk.LEFT, padx=5)

        ttk.Button(
            export_buttons_frame,
            text="📊 Exportar Excel",
            command=lambda: self.export_report("excel"),
        ).pack(side=tk.LEFT, padx=5)

        ttk.Button(
            export_buttons_frame,
            text="📋 Exportar CSV",
            command=lambda: self.export_report("csv"),
        ).pack(side=tk.LEFT, padx=5)

        ttk.Button(
            export_buttons_frame,
            text="🔧 Exportar JSON",
            command=lambda: self.export_report("json"),
        ).pack(side=tk.LEFT, padx=5)

    def load_user_data(self):
        """Carga los datos del usuario actual"""
        if not self.main_window.current_user:
            return

        self.current_user = self.main_window.current_user

        # Cargar datos iniciales
        self.update_statistics()
        self.load_initial_comparison()

    def update_statistics(self):
        """Actualiza las estadísticas mostradas"""
        if not self.current_user:
            return

        try:
            # Obtener estadísticas del usuario
            stats = self.user_manager.db.obtener_estadisticas_usuario(
                self.current_user["id"]
            )

            # Simular algunas estadísticas adicionales
            entrenamiento_data = self.user_manager.obtener_entrenamientos_recientes(
                self.current_user["id"]
            )

            # Calcular métricas
            total_entrenamientos = stats.get("total_entrenamientos", 0)
            total_calorias = stats.get("total_calorias", 0)
            tiempo_total = stats.get("tiempo_total_minutos", 0)

            # Actualizar tarjetas
            self.statistics_cards["entrenamientos"]["value_label"].config(
                text=str(total_entrenamientos)
            )
            self.statistics_cards["calorias_quemadas"]["value_label"].config(
                text=f"{total_calorias:.0f} cal"
            )
            self.statistics_cards["tiempo_entrenamiento"]["value_label"].config(
                text=f"{tiempo_total} min"
            )

            # Frecuencia semanal (simulada)
            frecuencia = (
                min(total_entrenamientos // 4, 7) if total_entrenamientos > 0 else 0
            )
            self.statistics_cards["frecuencia_semanal"]["value_label"].config(
                text=f"{frecuencia}/sem"
            )

            # Rutina más realizada (simulada)
            mejor_rutina = "Rutina Básica" if total_entrenamientos > 0 else "N/A"
            self.statistics_cards["mejor_rutina"]["value_label"].config(
                text=mejor_rutina[:15]
            )

            # Progreso de calorías (simulado)
            progreso_calorias = min(total_entrenamientos * 5, 50)
            self.statistics_cards["progreso_calorias"]["value_label"].config(
                text=f"+{progreso_calorias}%"
            )

            # Días activos (simulado)
            dias_activos = min(total_entrenamientos, 30)
            self.statistics_cards["dias_activos"]["value_label"].config(
                text=f"{dias_activos} días"
            )

            # Promedio diario (simulado)
            promedio_diario = total_calorias // max(total_entrenamientos, 1)
            self.statistics_cards["promedio_diario"]["value_label"].config(
                text=f"{promedio_diario} cal"
            )

        except Exception as e:
            print(f"Error al actualizar estadísticas: {e}")

    def load_initial_comparison(self):
        """Carga la comparación inicial"""
        self.compare_periods()

    def set_analysis_period(self, period):
        """Establece el período de análisis"""
        if period == "week":
            start_date = datetime.now() - timedelta(days=7)
        elif period == "month":
            start_date = datetime.now() - timedelta(days=30)
        elif period == "quarter":
            start_date = datetime.now() - timedelta(days=90)
        else:  # all
            start_date = datetime.min

        # Actualizar estadísticas para el período
        self.update_statistics()
        messagebox.showinfo("Info", f"Análisis actualizado para el período: {period}")

    def generate_report(self):
        """Genera un informe completo"""
        if not self.current_user:
            messagebox.showwarning("Advertencia", "Selecciona un usuario primero")
            return

        try:
            report_type = self.report_config["type"].get()
            period = self.report_config["period"].get()
            start_date = self.report_config["start_date"].get()
            end_date = self.report_config["end_date"].get()
            include_charts = self.report_config["include_charts"].get()
            include_recommendations = self.report_config[
                "include_recommendations"
            ].get()

            # Generar el contenido del informe
            report_content = self.build_report_content(
                report_type,
                period,
                start_date,
                end_date,
                include_charts,
                include_recommendations,
            )

            # Mostrar en vista previa
            self.report_preview.delete(1.0, tk.END)
            self.report_preview.insert(1.0, report_content)

            messagebox.showinfo("Éxito", "Informe generado correctamente")

        except Exception as e:
            messagebox.showerror("Error", f"Error al generar informe: {e}")

    def build_report_content(
        self,
        report_type,
        period,
        start_date,
        end_date,
        include_charts,
        include_recommendations,
    ):
        """Construye el contenido del informe"""
        # Obtener estadísticas
        stats = (
            self.user_manager.db.obtener_estadisticas_usuario(self.current_user["id"])
            if self.current_user
            else {}
        )

        # Encabezado del informe
        report = f"""
{"=" * 60}
{self.report_types.get(report_type, "Informe Personalizado")}
{"=" * 60}

Usuario: {self.current_user.get("nombre", "N/A") if self.current_user else "N/A"}
Fecha de generación: {datetime.now().strftime("%d/%m/%Y %H:%M")}
Período: {period}
Fechas: {start_date} a {end_date}

{"-" * 60}
RESUMEN EJECUTIVO
{"-" * 60}

En este período se registraron {stats.get("total_entrenamientos", 0)} entrenamientos,
quedando un total de {stats.get("total_calorias", 0):.0f} calorías quemadas
y {stats.get("tiempo_total_minutos", 0)} minutos de actividad física.

{"-" * 60}
ESTADÍSTICAS DETALLADAS
{"-" * 60}

📊 MÉTRICAS PRINCIPALES:
• Total de entrenamientos: {stats.get("total_entrenamientos", 0)}
• Calorías quemadas: {stats.get("total_calorias", 0):.0f} cal
• Tiempo total invertido: {stats.get("tiempo_total_minutos", 0)} minutos
• Rutinas creadas: {stats.get("rutinas_creadas", 0)}

🎯 ANÁLISIS DE RENDIMIENTO:
• Promedio de calorías por entrenamiento: {stats.get("total_calorias", 0) // max(stats.get("total_entrenamientos", 1), 1):.0f} cal
• Duración promedio por sesión: {stats.get("tiempo_total_minutos", 0) // max(stats.get("total_entrenamientos", 1), 1)} min
• Frecuencia semanal estimada: {min(stats.get("total_entrenamientos", 0) // 4, 7)} entrenamientos/semana

"""

        if include_charts:
            report += """
📈 VISUALIZACIÓN DE DATOS:
[Gráfico de entrenamientos por día]
[Gráfico de calorías quemadas por semana]
[Gráfico de progreso mensual]

"""

        if include_recommendations:
            report += self.generate_recommendations(stats)

        report += f"""
{"-" * 60}
CONCLUSIONES
{"-" * 60}

Basado en el análisis de los datos, el usuario muestra un {"nivel consistente" if stats.get("total_entrenamientos", 0) > 10 else "nivel iniciante"}
de actividad física. Se recomienda {"mantener la frecuencia actual" if stats.get("total_entrenamientos", 0) > 15 else "aumentar la frecuencia de entrenamiento"}.

Para el próximo período, se sugiere:
• {"Incrementar la intensidad de los entrenamientos" if stats.get("total_entrenamientos", 0) > 20 else "Establecer una rutina regular"}
• {"Diversificar los tipos de ejercicios" if stats.get("rutinas_creadas", 0) > 5 else "Crear rutinas personalizadas"}
• {"Monitorear el progreso semanal" if stats.get("total_calorias", 0) > 500 else "Comenzar con objetivos modestos"}

{"-" * 60}
INFORMACIÓN TÉCNICA
{"-" * 60}

• Base de datos: SmartFit SQLite
• Generación automática: Activada
• Formato de exportación: Texto estructurado
• Última actualización: {datetime.now().strftime("%d/%m/%Y %H:%M")}

{"=" * 60}
Fin del Informe
{"=" * 60}
        """

        return report

    def generate_recommendations(self, stats):
        """Genera recomendaciones personalizadas"""
        recommendations = """
🎯 RECOMENDACIONES PERSONALIZADAS:

"""

        if stats.get("total_entrenamientos", 0) < 5:
            recommendations += """
• Como principiante, te recomiendo comenzar con 2-3 entrenamientos por semana
• Enfócate en ejercicios básicos y técnica correcta
• Establece metas alcanzables a corto plazo
• Considera consultar con un profesional para crear un plan personalizado
"""
        elif stats.get("total_entrenamientos", 0) < 20:
            recommendations += """
• Ya tienes una base sólida, considera aumentar a 4-5 entrenamientos por semana
• Introduce ejercicios de mayor intensidad progresivamente
• Varía tus rutinas para evitar la monotonía
• Monitorea tu progreso semanalmente
"""
        else:
            recommendations += """
• Excelente nivel de actividad, considera rutinas especializadas
• Podrías beneficiarte de un plan periodizado
• Explora nuevas modalidades de entrenamiento
• Considera participar en eventos deportivos
"""

        if stats.get("total_calorias", 0) > 1000:
            recommendations += """
🔥 SOBRE CALORÍAS:
• Tu nivel de calorías quemadas es excelente
• Asegúrate de mantener una nutrición adecuada
• Considera el equilibrio entre cardio y fuerza
"""
        else:
            recommendations += """
💡 SOBRE CALORÍAS:
• Aumenta gradualmente la intensidad de los entrenamientos
• Combina ejercicios cardiovasculares con fuerza
• Establece objetivos calóricos específicos
"""

        return recommendations

    def preview_report(self):
        """Muestra una vista previa del informe"""
        # Generar vista previa simple
        preview_content = f"""
VISTA PREVIA DEL INFORME
========================

Tipo: {self.report_types.get(self.report_config["type"].get(), "Personalizado")}
Período: {self.report_config["period"].get()}
Fechas: {self.report_config["start_date"].get()} a {self.report_config["end_date"].get()}

Opciones seleccionadas:
• Incluir gráficos: {"Sí" if self.report_config["include_charts"].get() else "No"}
• Incluir recomendaciones: {"Sí" if self.report_config["include_recommendations"].get() else "No"}

Esta es una vista previa del contenido del informe.
Haz clic en "Generar Informe" para crear el informe completo.
"""

        self.report_preview.delete(1.0, tk.END)
        self.report_preview.insert(1.0, preview_content)

    def save_report(self):
        """Guarda el informe actual"""
        content = self.report_preview.get(1.0, tk.END)
        if not content.strip():
            messagebox.showwarning("Advertencia", "No hay contenido para guardar")
            return

        # Dialogo para guardar
        filename = filedialog.asksaveasfilename(
            defaultextension=".txt",
            filetypes=[("Archivos de texto", "*.txt"), ("Todos los archivos", "*.*")],
            title="Guardar informe",
        )

        if filename:
            try:
                with open(filename, "w", encoding="utf-8") as f:
                    f.write(content)
                messagebox.showinfo("Éxito", f"Informe guardado en: {filename}")
            except Exception as e:
                messagebox.showerror("Error", f"Error al guardar: {e}")

    def clear_report_config(self):
        """Limpia la configuración del informe"""
        self.report_config["type"].set("entrenamiento")
        self.report_config["period"].set("month")

        today = datetime.now()
        last_month = today - timedelta(days=30)
        self.report_config["start_date"].set(last_month.strftime("%Y-%m-%d"))
        self.report_config["end_date"].set(today.strftime("%Y-%m-%d"))

        self.report_config["include_charts"].set(True)
        self.report_config["include_recommendations"].set(True)

        self.preview_report()

    def compare_periods(self):
        """Compara dos períodos diferentes"""
        if not self.current_user:
            self.comparison_results.delete(1.0, tk.END)
            self.comparison_results.insert(
                1.0, "No hay usuario seleccionado para comparar"
            )
            return

        period1 = self.comparison_vars["period1"].get()
        period2 = self.comparison_vars["period2"].get()

        # Simular comparación (en implementación real consultaría la BD)
        comparison_content = f"""
COMPARACIÓN DE PERÍODOS
=======================

Período 1: {period1.replace("_", " ").title()}
Período 2: {period2.replace("_", " ").title()}

ANÁLISIS COMPARATIVO:

📊 ENTRENAMIENTOS:
• Período 1: 12 entrenamientos
• Período 2: 15 entrenamientos
• Cambio: +3 entrenamientos (+25%)
• Tendencia: 📈 Creciente

🔥 CALORÍAS:
• Período 1: 1,200 calorías
• Período 2: 1,500 calorías
• Cambio: +300 calorías (+25%)
• Tendencia: 📈 Mejora significativa

⏱️ TIEMPO:
• Período 1: 360 minutos
• Período 2: 450 minutos
• Cambio: +90 minutos (+25%)
• Tendencia: 📈 Mayor dedicación

🎯 EVALUACIÓN GENERAL:
El análisis muestra una mejora constante en todos los indicadores.
El usuario está progresando adecuadamente y mantiene una tendencia positiva.
Se recomienda continuar con el plan actual.

📈 PROYECCIÓN:
Basado en la tendencia actual, se estima que el próximo período
podría alcanzar:
• 18-20 entrenamientos
• 1,800-2,000 calorías quemadas
• 540-600 minutos de actividad

🔍 RECOMENDACIONES:
1. Mantener la frecuencia actual de entrenamiento
2. Considerar aumentar la intensidad gradualmente
3. Diversificar las modalidades de ejercicio
4. Monitorear el progreso semanalmente
"""

        self.comparison_results.delete(1.0, tk.END)
        self.comparison_results.insert(1.0, comparison_content)

    def export_report(self, format_type):
        """Exporta el informe en el formato especificado"""
        content = self.report_preview.get(1.0, tk.END)
        if not content.strip():
            messagebox.showwarning("Advertencia", "No hay contenido para exportar")
            return

        # Configurar filtros según el formato
        if format_type == "pdf":
            filetypes = [("Archivos PDF", "*.pdf"), ("Todos los archivos", "*.*")]
            defaultextension = ".pdf"
        elif format_type == "excel":
            filetypes = [("Archivos Excel", "*.xlsx"), ("Todos los archivos", "*.*")]
            defaultextension = ".xlsx"
        elif format_type == "csv":
            filetypes = [("Archivos CSV", "*.csv"), ("Todos los archivos", "*.*")]
            defaultextension = ".csv"
        else:  # json
            filetypes = [("Archivos JSON", "*.json"), ("Todos los archivos", "*.*")]
            defaultextension = ".json"

        filename = filedialog.asksaveasfilename(
            defaultextension=defaultextension,
            filetypes=filetypes,
            title=f"Exportar como {format_type.upper()}",
        )

        if filename:
            try:
                if format_type == "json":
                    # Convertir a JSON
                    report_data = {
                        "usuario": self.current_user.get("nombre", "N/A")
                        if self.current_user
                        else "N/A",
                        "fecha_generacion": datetime.now().isoformat(),
                        "contenido": content,
                        "estadisticas": self.user_manager.db.obtener_estadisticas_usuario(
                            self.current_user["id"]
                        )
                        if self.current_user
                        else {},
                    }
                    with open(filename, "w", encoding="utf-8") as f:
                        json.dump(report_data, f, indent=2, ensure_ascii=False)
                else:
                    # Para otros formatos, guardar como texto
                    with open(filename, "w", encoding="utf-8") as f:
                        f.write(content)

                messagebox.showinfo(
                    "Éxito", f"Informe exportado como {format_type.upper()}"
                )
            except Exception as e:
                messagebox.showerror("Error", f"Error al exportar: {e}")
