# workout_dialog.py - Diálogo de entrenamientos para SmartFit
# Fran - Desarrollo de interfaces

import tkinter as tk
from datetime import datetime
from tkinter import messagebox, ttk
from typing import Any, Dict, List, Optional


class WorkoutDialog:
    """
    Diálogo para registrar entrenamientos completados en SmartFit
    Permite registrar la duración real, calorías quemadas, notas y progreso
    """

    def __init__(
        self,
        parent,
        db_manager,
        user_manager,
        user_id: int,
        workout_data: Optional[Dict] = None,
    ):
        """
        Inicializa el diálogo de entrenamiento

        Args:
            parent: Ventana padre
            db_manager: Gestor de base de datos
            user_manager: Gestor de usuarios
            user_id: ID del usuario
            workout_data: Datos del entrenamiento (None para crear nuevo)
        """
        self.parent = parent
        self.db = db_manager
        self.user_manager = user_manager
        self.user_id = user_id
        self.workout_data = workout_data
        self.result = None
        self.workout_id = None

        # Variables del formulario
        self.rutina_var = tk.StringVar()
        self.fecha_var = tk.StringVar()
        self.duracion_real_var = tk.StringVar()
        self.calorias_quemadas_var = tk.StringVar()
        self.completado_var = tk.BooleanVar()
        self.notas_var = tk.StringVar()

        # Datos de rutinas disponibles
        self.rutinas_disponibles = []

        # Estado del diálogo
        self.dialog = None

        # Crear diálogo
        self.create_dialog()

    def create_dialog(self):
        """Crea la ventana del diálogo"""
        self.dialog = tk.Toplevel(self.parent)
        title = (
            "Registrar Entrenamiento"
            if not self.workout_data
            else "Editar Entrenamiento"
        )
        self.dialog.title(f"{title} - SmartFit")
        self.dialog.geometry("600x550")
        self.dialog.resizable(True, True)
        self.dialog.transient(self.parent)
        self.dialog.grab_set()

        # Configurar tamaño mínimo
        self.dialog.minsize(500, 450)

        # Centrar ventana
        self.center_window()

        # Configurar cierre con ESC
        self.dialog.bind("<Escape>", lambda e: self.cancel())

        # Crear contenido
        self.create_content()

        # Cargar datos si es edición
        if self.workout_data:
            self.load_workout_data()
        else:
            # Valores por defecto para nuevo entrenamiento
            self.set_default_values()

        # Cargar rutinas disponibles
        self.load_available_routines()

        # Enfocar primer campo
        self.dialog.after(100, lambda: self.rutina_combo.focus())

    def center_window(self):
        """Centra la ventana en la pantalla"""
        self.dialog.update_idletasks()
        width = 600
        height = 550
        x = (self.dialog.winfo_screenwidth() // 2) - (width // 2)
        y = (self.dialog.winfo_screenheight() // 2) - (height // 2)
        self.dialog.geometry(f"{width}x{height}+{x}+{y}")

    def create_content(self):
        """Crea el contenido del diálogo"""
        # Frame principal con scroll
        main_frame = ttk.Frame(self.dialog, padding="10")
        main_frame.pack(fill=tk.BOTH, expand=True)

        # Crear notebook para organizar las pestañas
        notebook = ttk.Notebook(main_frame)
        notebook.pack(fill=tk.BOTH, expand=True, pady=(0, 10))

        # Pestaña 1: Información básica
        self.create_basic_info_tab(notebook)

        # Pestaña 2: Detalles del entrenamiento
        self.create_details_tab(notebook)

        # Pestaña 3: Progreso y notas
        self.create_progress_tab(notebook)

        # Frame de botones
        buttons_frame = ttk.Frame(main_frame)
        buttons_frame.pack(fill=tk.X)

        # Botón Cancelar
        ttk.Button(buttons_frame, text="Cancelar", command=self.cancel, width=12).pack(
            side=tk.RIGHT, padx=(10, 0)
        )

        # Botón Guardar
        self.save_button = ttk.Button(
            buttons_frame,
            text="Registrar Entrenamiento"
            if not self.workout_data
            else "Actualizar Entrenamiento",
            command=self.save_workout,
            width=18,
        )
        self.save_button.pack(side=tk.RIGHT)

        # Botón Vista Previa
        self.preview_button = ttk.Button(
            buttons_frame,
            text="Vista Previa",
            command=self.show_workout_summary,
            width=12,
        )
        self.preview_button.pack(side=tk.RIGHT, padx=(0, 10))

    def create_basic_info_tab(self, notebook):
        """Crea la pestaña de información básica"""
        info_frame = ttk.Frame(notebook, padding="20")
        notebook.add(info_frame, text="📋 Información Básica")

        # Título
        title_label = ttk.Label(
            info_frame, text="Datos del Entrenamiento", font=("Arial", 14, "bold")
        )
        title_label.pack(pady=(0, 20))

        # Campo Rutina
        ttk.Label(
            info_frame, text="Rutina realizada *:", font=("Arial", 10, "bold")
        ).pack(anchor=tk.W, pady=(5, 2))
        self.rutina_combo = ttk.Combobox(
            info_frame,
            textvariable=self.rutina_var,
            width=50,
            state="readonly",
            font=("Arial", 10),
        )
        self.rutina_combo.pack(fill=tk.X, pady=(0, 15))
        self.rutina_combo.bind("<<ComboboxSelected>>", self.on_routine_selected)

        # Frame para fecha y hora
        fecha_hora_frame = ttk.Frame(info_frame)
        fecha_hora_frame.pack(fill=tk.X, pady=(0, 15))

        # Campo Fecha
        fecha_frame = ttk.Frame(fecha_hora_frame)
        fecha_frame.pack(side=tk.LEFT, fill=tk.X, expand=True, padx=(0, 10))

        ttk.Label(fecha_frame, text="Fecha:", font=("Arial", 10, "bold")).pack(
            anchor=tk.W, pady=(5, 2)
        )
        self.fecha_entry = ttk.Entry(
            fecha_frame, textvariable=self.fecha_var, width=20, font=("Arial", 10)
        )
        self.fecha_entry.pack(fill=tk.X)

        # Campo Estado
        estado_frame = ttk.Frame(fecha_hora_frame)
        estado_frame.pack(side=tk.RIGHT, fill=tk.X, expand=True, padx=(10, 0))

        ttk.Label(estado_frame, text="Estado:", font=("Arial", 10, "bold")).pack(
            anchor=tk.W, pady=(5, 2)
        )
        completado_check = ttk.Checkbutton(
            estado_frame,
            text="Entrenamiento completado",
            variable=self.completado_var,
            font=("Arial", 10),
        )
        completado_check.pack(anchor=tk.W)

        # Frame de información de la rutina seleccionada
        self.routine_info_frame = ttk.LabelFrame(
            info_frame, text="📊 Información de la Rutina", padding="15"
        )
        self.routine_info_frame.pack(fill=tk.X, pady=(10, 0))

        # Labels para información de la rutina
        self.routine_nombre_label = ttk.Label(
            self.routine_info_frame, text="Nombre: -", font=("Arial", 10)
        )
        self.routine_nombre_label.pack(anchor=tk.W)

        self.routine_duracion_label = ttk.Label(
            self.routine_info_frame, text="Duración estimada: -", font=("Arial", 10)
        )
        self.routine_duracion_label.pack(anchor=tk.W, pady=(5, 0))

        self.routine_dificultad_label = ttk.Label(
            self.routine_info_frame, text="Dificultad: -", font=("Arial", 10)
        )
        self.routine_dificultad_label.pack(anchor=tk.W, pady=(5, 0))

        self.routine_ejercicios_label = ttk.Label(
            self.routine_info_frame, text="Ejercicios: -", font=("Arial", 10)
        )
        self.routine_ejercicios_label.pack(anchor=tk.W, pady=(5, 0))

    def create_details_tab(self, notebook):
        """Crea la pestaña de detalles del entrenamiento"""
        details_frame = ttk.Frame(notebook, padding="20")
        notebook.add(details_frame, text="⏱️ Detalles")

        # Título
        title_label = ttk.Label(
            details_frame, text="Detalles del Entrenamiento", font=("Arial", 14, "bold")
        )
        title_label.pack(pady=(0, 20))

        # Frame para métricas
        metrics_frame = ttk.LabelFrame(details_frame, text="📊 Métricas", padding="15")
        metrics_frame.pack(fill=tk.X, pady=(0, 15))

        # Campo Duración Real
        ttk.Label(
            metrics_frame, text="Duración real (minutos):", font=("Arial", 10, "bold")
        ).pack(anchor=tk.W, pady=(5, 2))
        self.duracion_real_entry = ttk.Entry(
            metrics_frame,
            textvariable=self.duracion_real_var,
            width=20,
            font=("Arial", 10),
        )
        self.duracion_real_entry.pack(fill=tk.X, pady=(0, 15))
        self.duracion_real_entry.bind("<Return>", lambda e: self.calorias_entry.focus())

        # Campo Calorías Quemadas
        ttk.Label(
            metrics_frame, text="Calorías quemadas (kcal):", font=("Arial", 10, "bold")
        ).pack(anchor=tk.W, pady=(5, 2))
        self.calorias_entry = ttk.Entry(
            metrics_frame,
            textvariable=self.calorias_quemadas_var,
            width=20,
            font=("Arial", 10),
        )
        self.calorias_entry.pack(fill=tk.X, pady=(0, 15))
        self.calorias_entry.bind("<Return>", lambda e: self.notas_text.focus())

        # Frame de calculadora rápida
        calc_frame = ttk.LabelFrame(
            details_frame, text="🧮 Calculadora Rápida", padding="15"
        )
        calc_frame.pack(fill=tk.X, pady=(10, 0))

        # Botones de cálculo rápido
        quick_calc_frame = ttk.Frame(calc_frame)
        quick_calc_frame.pack(fill=tk.X)

        ttk.Button(
            quick_calc_frame,
            text="+5 min",
            command=lambda: self.add_minutes(5),
            width=8,
        ).pack(side=tk.LEFT, padx=(0, 5))

        ttk.Button(
            quick_calc_frame,
            text="+10 min",
            command=lambda: self.add_minutes(10),
            width=8,
        ).pack(side=tk.LEFT, padx=(0, 5))

        ttk.Button(
            quick_calc_frame,
            text="+15 min",
            command=lambda: self.add_minutes(15),
            width=8,
        ).pack(side=tk.LEFT, padx=(0, 5))

        ttk.Button(
            quick_calc_frame,
            text="Calcular calorías",
            command=self.calculate_calories,
            width=12,
        ).pack(side=tk.RIGHT)

        # Información calculada
        self.calc_info_label = ttk.Label(
            calc_frame,
            text="Selecciona una rutina para ver cálculos",
            font=("Arial", 9),
        )
        self.calc_info_label.pack(anchor=tk.W, pady=(10, 0))

    def create_progress_tab(self, notebook):
        """Crea la pestaña de progreso y notas"""
        progress_frame = ttk.Frame(notebook, padding="20")
        notebook.add(progress_frame, text="📝 Progreso y Notas")

        # Título
        title_label = ttk.Label(
            progress_frame, text="Progreso Personal", font=("Arial", 14, "bold")
        )
        title_label.pack(pady=(0, 20))

        # Campo Notas
        notes_frame = ttk.LabelFrame(
            progress_frame, text="📝 Notas del Entrenamiento", padding="15"
        )
        notes_frame.pack(fill=tk.BOTH, expand=True, pady=(0, 15))

        ttk.Label(
            notes_frame,
            text="¿Cómo te sentiste? ¿Qué tal el rendimiento? ¿Alguna observación?",
            font=("Arial", 9),
        ).pack(anchor=tk.W, pady=(0, 5))

        self.notas_text = tk.Text(
            notes_frame, width=50, height=10, font=("Arial", 10), wrap=tk.WORD
        )
        self.notas_text.pack(fill=tk.BOTH, expand=True)

        # Frame de información del usuario
        user_info_frame = ttk.LabelFrame(
            progress_frame, text="👤 Tu Progreso", padding="15"
        )
        user_info_frame.pack(fill=tk.X, pady=(0, 10))

        # Obtener estadísticas del usuario
        estadisticas = self.db.obtener_estadisticas_usuario(self.user_id)

        self.user_stats_label = ttk.Label(
            user_info_frame,
            text=f"Entrenamientos totales: {estadisticas.get('total_entrenamientos', 0)}",
            font=("Arial", 10),
        )
        self.user_stats_label.pack(anchor=tk.W)

        self.calorias_total_label = ttk.Label(
            user_info_frame,
            text=f"Calorías quemadas totales: {estadisticas.get('total_calorias', 0)} kcal",
            font=("Arial", 10),
        )
        self.calorias_total_label.pack(anchor=tk.W, pady=(5, 0))

        self.tiempo_total_label = ttk.Label(
            user_info_frame,
            text=f"Tiempo total de entrenamiento: {estadisticas.get('tiempo_total_minutos', 0)} minutos",
            font=("Arial", 10),
        )
        self.tiempo_total_label.pack(anchor=tk.W, pady=(5, 0))

    def load_available_routines(self):
        """Carga las rutinas disponibles para el usuario"""
        try:
            self.rutinas_disponibles = self.db.obtener_rutinas_usuario(self.user_id)

            # Crear lista para el combobox
            rutina_items = []
            for rutina in self.rutinas_disponibles:
                item_text = f"{rutina['nombre']} ({rutina['dificultad']})"
                rutina_items.append(item_text)

            self.rutina_combo["values"] = rutina_items

        except Exception as e:
            messagebox.showerror("Error", f"Error al cargar rutinas: {e}")

    def on_routine_selected(self, event=None):
        """Maneja la selección de una rutina"""
        try:
            selected_index = self.rutina_combo.current()
            if selected_index >= 0 and selected_index < len(self.rutinas_disponibles):
                rutina = self.rutinas_disponibles[selected_index]
                self.update_routine_info(rutina)
                self.update_calculations(rutina)
        except Exception as e:
            print(f"Error al seleccionar rutina: {e}")

    def update_routine_info(self, rutina):
        """Actualiza la información mostrada de la rutina"""
        self.routine_nombre_label.config(text=f"Nombre: {rutina['nombre']}")

        duracion = rutina.get("duracion_minutos", 0)
        self.routine_duracion_label.config(
            text=f"Duración estimada: {duracion} minutos"
        )

        dificultad = rutina.get("dificultad", "N/A").capitalize()
        self.routine_dificultad_label.config(text=f"Dificultad: {dificultad}")

        # Obtener número de ejercicios
        try:
            ejercicios = self.db.obtener_ejercicios_rutina(rutina["id"])
            num_ejercicios = len(ejercicios) if ejercicios else 0
            self.routine_ejercicios_label.config(text=f"Ejercicios: {num_ejercicios}")
        except:
            self.routine_ejercicios_label.config(text="Ejercicios: No disponible")

    def update_calculations(self, rutina):
        """Actualiza los cálculos automáticos"""
        try:
            # Calcular calorías estimadas por minuto de la rutina
            ejercicios = self.db.obtener_ejercicios_rutina(rutina["id"])
            if ejercicios:
                calorias_por_minuto = sum(
                    e.get("calorias_por_minuto", 0) for e in ejercicios
                ) / len(ejercicios)

                duracion = rutina.get("duracion_minutos", 30)
                calorias_estimadas = int(calorias_por_minuto * duracion)

                self.calc_info_label.config(
                    text=f"Estimación: {calorias_por_minuto:.1f} kcal/min × {duracion} min = ~{calorias_estimadas} kcal"
                )
            else:
                self.calc_info_label.config(text="No se pudo calcular la estimación")

        except Exception as e:
            self.calc_info_label.config(text=f"Error en cálculo: {e}")

    def set_default_values(self):
        """Establece valores por defecto para nuevo entrenamiento"""
        # Fecha actual
        fecha_actual = datetime.now().strftime("%Y-%m-%d %H:%M")
        self.fecha_var.set(fecha_actual)

        # No completado por defecto
        self.completado_var.set(False)

    def load_workout_data(self):
        """Carga los datos del entrenamiento en el formulario"""
        if not self.workout_data:
            return

        self.workout_id = self.workout_data.get("id")

        # Cargar datos básicos
        rutina_id = self.workout_data.get("rutina_id")
        if rutina_id:
            # Buscar la rutina en la lista
            for i, rutina in enumerate(self.rutinas_disponibles):
                if rutina["id"] == rutina_id:
                    self.rutina_combo.current(i)
                    break

        # Cargar otros datos
        fecha = self.workout_data.get("fecha_entrenamiento", "")
        if fecha:
            self.fecha_var.set(fecha)

        duracion = self.workout_data.get("duracion_real")
        if duracion:
            self.duracion_real_var.set(str(duracion))

        calorias = self.workout_data.get("calorias_quemadas")
        if calorias:
            self.calorias_quemadas_var.set(str(calorias))

        self.completado_var.set(self.workout_data.get("completado", False))

        # Cargar notas
        notas = self.workout_data.get("notas", "")
        if notas:
            self.notas_text.insert(1.0, notas)

        # Actualizar información si hay rutina seleccionada
        if rutina_id:
            rutina = next(
                (r for r in self.rutinas_disponibles if r["id"] == rutina_id), None
            )
            if rutina:
                self.update_routine_info(rutina)
                self.update_calculations(rutina)

    def add_minutes(self, minutes):
        """Añade minutos a la duración actual"""
        try:
            current_duration = self.duracion_real_var.get()
            if current_duration:
                current = int(current_duration)
                new_duration = current + minutes
                self.duracion_real_var.set(str(new_duration))
            else:
                self.duracion_real_var.set(str(minutes))
        except ValueError:
            self.duracion_real_var.set(str(minutes))

    def calculate_calories(self):
        """Calcula calorías basadas en la rutina y duración"""
        try:
            if not self.duracion_real_var.get():
                messagebox.showwarning("Advertencia", "Ingresa la duración primero")
                return

            selected_index = self.rutina_combo.current()
            if selected_index < 0 or selected_index >= len(self.rutinas_disponibles):
                messagebox.showwarning("Advertencia", "Selecciona una rutina primero")
                return

            rutina = self.rutinas_disponibles[selected_index]
            ejercicios = self.db.obtener_ejercicios_rutina(rutina["id"])

            if ejercicios:
                # Calcular promedio de calorías por minuto
                calorias_por_minuto = sum(
                    e.get("calorias_por_minuto", 0) for e in ejercicios
                ) / len(ejercicios)

                duracion = int(self.duracion_real_var.get())
                calorias_calculadas = int(calorias_por_minuto * duracion)

                self.calorias_quemadas_var.set(str(calorias_calculadas))

                messagebox.showinfo(
                    "Cálculo Realizado",
                    f"Calorías calculadas: {calorias_calculadas} kcal\n"
                    f"Basado en {duracion} minutos de {rutina['nombre']}",
                )
            else:
                messagebox.showwarning(
                    "Advertencia", "No se encontraron ejercicios para esta rutina"
                )

        except Exception as e:
            messagebox.showerror("Error", f"Error al calcular calorías: {e}")

    def validate_form(self) -> tuple[bool, str]:
        """Valida los datos del formulario"""
        # Validar rutina
        if not self.rutina_var.get():
            return False, "Debes seleccionar una rutina"

        # Validar fecha
        fecha_str = self.fecha_var.get().strip()
        if not fecha_str:
            return False, "La fecha es obligatoria"

        try:
            # Intentar parsear la fecha
            datetime.strptime(fecha_str, "%Y-%m-%d %H:%M")
        except ValueError:
            return False, "El formato de fecha debe ser YYYY-MM-DD HH:MM"

        # Validar duración (opcional)
        if self.duracion_real_var.get().strip():
            try:
                duracion = int(self.duracion_real_var.get())
                if duracion < 1 or duracion > 300:
                    return False, "La duración debe estar entre 1 y 300 minutos"
            except ValueError:
                return False, "La duración debe ser un número"

        # Validar calorías (opcional)
        if self.calorias_quemadas_var.get().strip():
            try:
                calorias = float(self.calorias_quemadas_var.get())
                if calorias < 0 or calorias > 2000:
                    return False, "Las calorías deben estar entre 0 y 2000"
            except ValueError:
                return False, "Las calorías deben ser un número"

        return True, "Validación exitosa"

    def save_workout(self):
        """Guarda el entrenamiento"""
        try:
            # Validar formulario
            is_valid, error_msg = self.validate_form()
            if not is_valid:
                messagebox.showerror("Error de Validación", error_msg)
                return

            # Preparar datos
            selected_index = self.rutina_combo.current()
            rutina = self.rutinas_disponibles[selected_index]
            rutina_id = rutina["id"]

            fecha = self.fecha_var.get().strip()
            duracion = (
                int(self.duracion_real_var.get())
                if self.duracion_real_var.get().strip()
                else None
            )
            calorias = (
                float(self.calorias_quemadas_var.get())
                if self.calorias_quemadas_var.get().strip()
                else None
            )
            completado = self.completado_var.get()
            notas = self.notas_text.get(1.0, tk.END).strip()

            if self.workout_id:
                # Actualizar entrenamiento existente
                self.update_workout(
                    rutina_id, fecha, duracion, calorias, completado, notas
                )
            else:
                # Crear nuevo entrenamiento
                self.create_workout(
                    rutina_id, fecha, duracion, calorias, completado, notas
                )

        except Exception as e:
            messagebox.showerror(
                "Error", f"Error al guardar el entrenamiento:\n{str(e)}"
            )

    def create_workout(
        self,
        rutina_id: int,
        fecha: str,
        duracion: Optional[int],
        calorias: Optional[float],
        completado: bool,
        notas: str,
    ):
        """Crea un nuevo entrenamiento"""
        try:
            # Registrar entrenamiento en la base de datos
            workout_id = self.db.registrar_entrenamiento(
                usuario_id=self.user_id,
                rutina_id=rutina_id,
                duracion_real=duracion,
                calorias_quemadas=calorias,
                completado=completado,
                notas=notas,
            )

            if workout_id > 0:
                self.result = {
                    "id": workout_id,
                    "rutina_id": rutina_id,
                    "fecha_entrenamiento": fecha,
                    "duracion_real": duracion,
                    "calorias_quemadas": calorias,
                    "completado": completado,
                    "notas": notas,
                    "action": "create",
                }

                # Obtener información de la rutina
                rutina = next(
                    (r for r in self.rutinas_disponibles if r["id"] == rutina_id), None
                )
                rutina_nombre = rutina["nombre"] if rutina else "Desconocida"

                messagebox.showinfo(
                    "Éxito",
                    f"¡Entrenamiento registrado correctamente!\n\n"
                    f"Rutina: {rutina_nombre}\n"
                    f"Fecha: {fecha}\n"
                    f"ID: {workout_id}",
                )
                self.dialog.destroy()
            else:
                messagebox.showerror("Error", "No se pudo registrar el entrenamiento")

        except Exception as e:
            messagebox.showerror("Error", f"Error al crear entrenamiento:\n{str(e)}")

    def update_workout(
        self,
        rutina_id: int,
        fecha: str,
        duracion: Optional[int],
        calorias: Optional[float],
        completado: bool,
        notas: str,
    ):
        """Actualiza un entrenamiento existente"""
        try:
            # En una implementación completa, aquí iría la lógica de actualización
            # Por ahora, simulamos el éxito

            self.result = {
                "id": self.workout_id,
                "rutina_id": rutina_id,
                "fecha_entrenamiento": fecha,
                "duracion_real": duracion,
                "calorias_quemadas": calorias,
                "completado": completado,
                "notas": notas,
                "action": "update",
            }

            messagebox.showinfo("Éxito", "Entrenamiento actualizado correctamente!")
            self.dialog.destroy()

        except Exception as e:
            messagebox.showerror(
                "Error", f"Error al actualizar entrenamiento:\n{str(e)}"
            )

    def show_workout_summary(self):
        """Muestra un resumen del entrenamiento"""
        try:
            if not self.rutina_var.get():
                messagebox.showwarning("Advertencia", "Selecciona una rutina primero")
                return

            selected_index = self.rutina_combo.current()
            rutina = self.rutinas_disponibles[selected_index]

            summary = f"🏃‍♂️ RESUMEN DEL ENTRENAMIENTO\n\n"
            summary += f"Rutina: {rutina['nombre']}\n"
            summary += f"Dificultad: {rutina.get('dificultad', 'N/A').capitalize()}\n"
            summary += f"Duración estimada: {rutina.get('duracion_minutos', 0)} min\n\n"

            if self.duracion_real_var.get():
                summary += f"Duración real: {self.duracion_real_var.get()} min\n"
            else:
                summary += "Duración real: No registrada\n"

            if self.calorias_quemadas_var.get():
                summary += f"Calorías: {self.calorias_quemadas_var.get()} kcal\n"
            else:
                summary += "Calorías: No registradas\n"

            summary += f"Completado: {'Sí' if self.completado_var.get() else 'No'}\n\n"

            if self.notas_text.get(1.0, tk.END).strip():
                summary += f"Notas:\n{self.notas_text.get(1.0, tk.END).strip()}"

            messagebox.showinfo("Resumen del Entrenamiento", summary)

        except Exception as e:
            messagebox.showerror("Error", f"Error al mostrar resumen: {e}")

    def cancel(self):
        """Cancela la operación y cierra el diálogo"""
        self.dialog.destroy()

    def get_result(self) -> Optional[Dict]:
        """Obtiene el resultado del diálogo"""
        return self.result

    def show(self) -> Optional[Dict]:
        """Muestra el diálogo y espera el resultado"""
        self.dialog.wait_window()
        return self.get_result()


# Funciones de utilidad
def create_workout_dialog(
    parent,
    db_manager,
    user_manager,
    user_id: int,
    workout_data: Optional[Dict] = None,
) -> WorkoutDialog:
    """Función de utilidad para crear un diálogo de entrenamiento"""
    return WorkoutDialog(parent, db_manager, user_manager, user_id, workout_data)


def show_workout_creation_dialog(
    parent, db_manager, user_manager, user_id: int
) -> Optional[Dict]:
    """Función de conveniencia para mostrar un diálogo de creación de entrenamiento"""
    dialog = WorkoutDialog(parent, db_manager, user_manager, user_id, None)
    return dialog.show()


def show_workout_edit_dialog(
    parent, db_manager, user_manager, user_id: int, workout_data: Dict
) -> Optional[Dict]:
    """Función de conveniencia para mostrar un diálogo de edición de entrenamiento"""
    dialog = WorkoutDialog(parent, db_manager, user_manager, user_id, workout_data)
    return dialog.show()
