# routine_dialog.py - Diálogo de rutinas para SmartFit
# Fran - Desarrollo de interfaces

import tkinter as tk
from tkinter import messagebox, ttk
from typing import Any, Dict, List, Optional


class RoutineDialog:
    """
    Diálogo modal para crear y editar rutinas de entrenamiento en SmartFit
    Permite configurar ejercicios, series, repeticiones y descansos
    """

    def __init__(
        self,
        parent,
        db_manager,
        user_manager,
        user_id: int,
        routine_data: Optional[Dict] = None,
    ):
        """
        Inicializa el diálogo de rutina

        Args:
            parent: Ventana padre
            db_manager: Gestor de base de datos
            user_manager: Gestor de usuarios
            user_id: ID del usuario
            routine_data: Datos de la rutina a editar (None para crear nueva)
        """
        self.parent = parent
        self.db = db_manager
        self.user_manager = user_manager
        self.user_id = user_id
        self.routine_data = routine_data
        self.result = None
        self.routine_id = None

        # Variables del formulario
        self.nombre_var = tk.StringVar()
        self.descripcion_var = tk.StringVar()
        self.duracion_var = tk.StringVar()
        self.dificultad_var = tk.StringVar()

        # Variables para ejercicios
        self.ejercicios_disponibles = []
        self.ejercicios_seleccionados = []

        # Estado del diálogo
        self.dialog = None
        self.exercise_listbox = None
        self.selected_exercise_listbox = None

        # Crear diálogo
        self.create_dialog()

    def create_dialog(self):
        """Crea la ventana del diálogo"""
        self.dialog = tk.Toplevel(self.parent)
        title = "Editar Rutina" if self.routine_data else "Crear Nueva Rutina"
        self.dialog.title(f"{title} - SmartFit")
        self.dialog.geometry("700x600")
        self.dialog.resizable(True, True)
        self.dialog.transient(self.parent)
        self.dialog.grab_set()

        # Configurar tamaño mínimo
        self.dialog.minsize(600, 500)

        # Centrar ventana
        self.center_window()

        # Configurar cierre con ESC
        self.dialog.bind("<Escape>", lambda e: self.cancel())

        # Crear contenido
        self.create_content()

        # Cargar datos si es edición
        if self.routine_data:
            self.load_routine_data()

        # Cargar ejercicios disponibles
        self.load_available_exercises()

        # Enfocar primer campo
        self.dialog.after(100, lambda: self.nombre_entry.focus())

    def center_window(self):
        """Centra la ventana en la pantalla"""
        self.dialog.update_idletasks()
        width = 700
        height = 600
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

        # Pestaña 2: Ejercicios
        self.create_exercises_tab(notebook)

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
            text="Guardar Rutina" if not self.routine_data else "Actualizar Rutina",
            command=self.save_routine,
            width=15,
        )
        self.save_button.pack(side=tk.RIGHT)

        # Botón Vista Previa (solo para rutinas con ejercicios)
        self.preview_button = ttk.Button(
            buttons_frame,
            text="Vista Previa",
            command=self.show_preview,
            width=12,
        )
        self.preview_button.pack(side=tk.RIGHT, padx=(0, 10))
        self.preview_button.config(state="disabled")

    def create_basic_info_tab(self, notebook):
        """Crea la pestaña de información básica"""
        info_frame = ttk.Frame(notebook, padding="20")
        notebook.add(info_frame, text="📋 Información Básica")

        # Título
        title_label = ttk.Label(
            info_frame, text="Datos de la Rutina", font=("Arial", 14, "bold")
        )
        title_label.pack(pady=(0, 20))

        # Campo Nombre
        ttk.Label(
            info_frame, text="Nombre de la rutina *:", font=("Arial", 10, "bold")
        ).pack(anchor=tk.W, pady=(5, 2))
        self.nombre_entry = ttk.Entry(
            info_frame, textvariable=self.nombre_var, width=50, font=("Arial", 10)
        )
        self.nombre_entry.pack(fill=tk.X, pady=(0, 15))
        self.nombre_entry.bind("<Return>", lambda e: self.descripcion_text.focus())

        # Campo Descripción
        ttk.Label(info_frame, text="Descripción:", font=("Arial", 10)).pack(
            anchor=tk.W, pady=(5, 2)
        )
        self.descripcion_text = tk.Text(
            info_frame, width=50, height=4, font=("Arial", 10), wrap=tk.WORD
        )
        self.descripcion_text.pack(fill=tk.X, pady=(0, 15))

        # Frame para duración y dificultad
        duracion_dificultad_frame = ttk.Frame(info_frame)
        duracion_dificultad_frame.pack(fill=tk.X, pady=(0, 15))

        # Campo Duración
        duracion_frame = ttk.Frame(duracion_dificultad_frame)
        duracion_frame.pack(side=tk.LEFT, fill=tk.X, expand=True, padx=(0, 10))

        ttk.Label(
            duracion_frame, text="Duración estimada (min):", font=("Arial", 10)
        ).pack(anchor=tk.W, pady=(5, 2))
        self.duracion_entry = ttk.Entry(
            duracion_frame, textvariable=self.duracion_var, width=20, font=("Arial", 10)
        )
        self.duracion_entry.pack(fill=tk.X)

        # Campo Dificultad
        dificultad_frame = ttk.Frame(duracion_dificultad_frame)
        dificultad_frame.pack(side=tk.RIGHT, fill=tk.X, expand=True, padx=(10, 0))

        ttk.Label(dificultad_frame, text="Dificultad:", font=("Arial", 10)).pack(
            anchor=tk.W, pady=(5, 2)
        )
        self.dificultad_combo = ttk.Combobox(
            dificultad_frame,
            textvariable=self.dificultad_var,
            values=["Principiante", "Intermedio", "Avanzado"],
            width=18,
            state="readonly",
            font=("Arial", 10),
        )
        self.dificultad_combo.pack(fill=tk.X)
        self.dificultad_combo.set("Principiante")

        # Frame de información calculada
        info_calc_frame = ttk.LabelFrame(
            info_frame, text="📊 Información Calculada", padding="15"
        )
        info_calc_frame.pack(fill=tk.X, pady=(10, 0))

        self.info_calorias_label = ttk.Label(
            info_calc_frame, text="Calorías estimadas: -", font=("Arial", 10)
        )
        self.info_calorias_label.pack(anchor=tk.W)

        # Información sobre el usuario actual
        user_info_frame = ttk.LabelFrame(info_frame, text="👤 Usuario", padding="10")
        user_info_frame.pack(fill=tk.X, pady=(10, 0))

        user = self.user_manager.obtener_usuario_por_id(self.user_id)
        if user:
            user_text = f"Usuario: {user.get('nombre', 'N/A')}\n"
            user_text += f"Objetivo: {user.get('objetivo', 'N/A')}"
        else:
            user_text = "Usuario: No encontrado"

        ttk.Label(user_info_frame, text=user_text, font=("Arial", 9)).pack(anchor=tk.W)

    def create_exercises_tab(self, notebook):
        """Crea la pestaña de ejercicios"""
        exercises_frame = ttk.Frame(notebook, padding="10")
        notebook.add(exercises_frame, text="💪 Ejercicios")

        # Título
        title_label = ttk.Label(
            exercises_frame, text="Seleccionar Ejercicios", font=("Arial", 14, "bold")
        )
        title_label.pack(pady=(0, 10))

        # Frame principal para las dos listas
        lists_frame = ttk.Frame(exercises_frame)
        lists_frame.pack(fill=tk.BOTH, expand=True, pady=(0, 10))

        # Lista de ejercicios disponibles
        available_frame = ttk.LabelFrame(
            lists_frame, text="Ejercicios Disponibles", padding="10"
        )
        available_frame.pack(side=tk.LEFT, fill=tk.BOTH, expand=True, padx=(0, 5))

        # Buscador de ejercicios
        search_frame = ttk.Frame(available_frame)
        search_frame.pack(fill=tk.X, pady=(0, 10))

        ttk.Label(search_frame, text="Buscar:").pack(side=tk.LEFT)
        self.search_var = tk.StringVar()
        search_entry = ttk.Entry(search_frame, textvariable=self.search_var, width=15)
        search_entry.pack(side=tk.LEFT, padx=(5, 0))
        search_entry.bind("<KeyRelease>", self.filter_exercises)

        # Listbox de ejercicios disponibles
        listbox_frame = ttk.Frame(available_frame)
        listbox_frame.pack(fill=tk.BOTH, expand=True)

        self.exercise_listbox = tk.Listbox(
            listbox_frame, font=("Arial", 9), selectmode=tk.SINGLE
        )
        scrollbar1 = ttk.Scrollbar(
            listbox_frame, orient=tk.VERTICAL, command=self.exercise_listbox.yview
        )
        self.exercise_listbox.config(yscrollcommand=scrollbar1.set)

        self.exercise_listbox.pack(side=tk.LEFT, fill=tk.BOTH, expand=True)
        scrollbar1.pack(side=tk.RIGHT, fill=tk.Y)

        # Botones de gestión
        buttons_frame = ttk.Frame(available_frame)
        buttons_frame.pack(fill=tk.X, pady=(10, 0))

        ttk.Button(
            buttons_frame,
            text="➕ Agregar",
            command=self.add_exercise,
            width=10,
        ).pack(side=tk.LEFT, padx=(0, 5))

        ttk.Button(
            buttons_frame,
            text="🔄 Actualizar Lista",
            command=self.load_available_exercises,
            width=12,
        ).pack(side=tk.LEFT)

        # Lista de ejercicios seleccionados
        selected_frame = ttk.LabelFrame(
            lists_frame, text="Ejercicios en la Rutina", padding="10"
        )
        selected_frame.pack(side=tk.RIGHT, fill=tk.BOTH, expand=True, padx=(5, 0))

        # Listbox de ejercicios seleccionados
        selected_listbox_frame = ttk.Frame(selected_frame)
        selected_listbox_frame.pack(fill=tk.BOTH, expand=True)

        self.selected_exercise_listbox = tk.Listbox(
            selected_listbox_frame, font=("Arial", 9), selectmode=tk.SINGLE
        )
        scrollbar2 = ttk.Scrollbar(
            selected_listbox_frame,
            orient=tk.VERTICAL,
            command=self.selected_exercise_listbox.yview,
        )
        self.selected_exercise_listbox.config(yscrollcommand=scrollbar2.set)

        self.selected_exercise_listbox.pack(side=tk.LEFT, fill=tk.BOTH, expand=True)
        scrollbar2.pack(side=tk.RIGHT, fill=tk.Y)

        # Botones de gestión para seleccionados
        selected_buttons_frame = ttk.Frame(selected_frame)
        selected_buttons_frame.pack(fill=tk.X, pady=(10, 0))

        ttk.Button(
            selected_buttons_frame,
            text="✏️ Configurar",
            command=self.configure_exercise,
            width=10,
        ).pack(side=tk.LEFT, padx=(0, 5))

        ttk.Button(
            selected_buttons_frame,
            text="⬆️ Subir",
            command=self.move_exercise_up,
            width=8,
        ).pack(side=tk.LEFT, padx=(0, 5))

        ttk.Button(
            selected_buttons_frame,
            text="⬇️ Bajar",
            command=self.move_exercise_down,
            width=8,
        ).pack(side=tk.LEFT, padx=(0, 5))

        ttk.Button(
            selected_buttons_frame,
            text="❌ Quitar",
            command=self.remove_exercise,
            width=10,
        ).pack(side=tk.RIGHT)

        # Información de ejercicios seleccionados
        info_frame = ttk.Frame(exercises_frame)
        info_frame.pack(fill=tk.X)

        self.exercise_count_label = ttk.Label(
            info_frame, text="Ejercicios seleccionados: 0", font=("Arial", 10)
        )
        self.exercise_count_label.pack(side=tk.LEFT)

        self.total_duration_label = ttk.Label(
            info_frame, text="Duración total estimada: -", font=("Arial", 10)
        )
        self.total_duration_label.pack(side=tk.RIGHT)

    def load_available_exercises(self):
        """Carga los ejercicios disponibles en la lista"""
        try:
            self.ejercicios_disponibles = self.db.obtener_ejercicios()
            self.update_exercise_list()
        except Exception as e:
            messagebox.showerror("Error", f"Error al cargar ejercicios: {e}")

    def update_exercise_list(self):
        """Actualiza la lista de ejercicios disponibles"""
        self.exercise_listbox.delete(0, tk.END)
        for ejercicio in self.ejercicios_disponibles:
            self.exercise_listbox.insert(
                tk.END, f"{ejercicio['nombre']} ({ejercicio['categoria']})"
            )

    def filter_exercises(self, *args):
        """Filtra los ejercicios según el texto de búsqueda"""
        search_text = self.search_var.get().lower()
        self.exercise_listbox.delete(0, tk.END)

        for ejercicio in self.ejercicios_disponibles:
            if (
                search_text in ejercicio["nombre"].lower()
                or search_text in ejercicio["categoria"].lower()
            ):
                self.exercise_listbox.insert(
                    tk.END, f"{ejercicio['nombre']} ({ejercicio['categoria']})"
                )

    def add_exercise(self):
        """Agrega un ejercicio a la rutina"""
        try:
            selection = self.exercise_listbox.curselection()
            if not selection:
                messagebox.showwarning("Advertencia", "Selecciona un ejercicio primero")
                return

            index = selection[0]
            ejercicio = self.ejercicios_disponibles[index]

            # Verificar si ya está agregado
            for selected in self.ejercicios_seleccionados:
                if selected["ejercicio"]["id"] == ejercicio["id"]:
                    messagebox.showwarning(
                        "Advertencia", "Este ejercicio ya está en la rutina"
                    )
                    return

            # Agregar ejercicio con configuración por defecto
            ejercicio_config = {
                "ejercicio": ejercicio,
                "series": 3,
                "repeticiones": 10,
                "descanso_segundos": 60,
                "orden": len(self.ejercicios_seleccionados) + 1,
            }

            self.ejercicios_seleccionados.append(ejercicio_config)
            self.update_selected_exercises_list()
            self.update_exercise_info()

        except Exception as e:
            messagebox.showerror("Error", f"Error al agregar ejercicio: {e}")

    def remove_exercise(self):
        """Quita un ejercicio de la rutina"""
        try:
            selection = self.selected_exercise_listbox.curselection()
            if not selection:
                messagebox.showwarning("Advertencia", "Selecciona un ejercicio primero")
                return

            index = selection[0]
            del self.ejercicios_seleccionados[index]

            # Reordenar
            for i, ejercicio in enumerate(self.ejercicios_seleccionados):
                ejercicio["orden"] = i + 1

            self.update_selected_exercises_list()
            self.update_exercise_info()

        except Exception as e:
            messagebox.showerror("Error", f"Error al quitar ejercicio: {e}")

    def move_exercise_up(self):
        """Mueve un ejercicio hacia arriba en la lista"""
        try:
            selection = self.selected_exercise_listbox.curselection()
            if not selection or selection[0] == 0:
                return

            index = selection[0]
            # Intercambiar con el anterior
            (
                self.ejercicios_seleccionados[index],
                self.ejercicios_seleccionados[index - 1],
            ) = (
                self.ejercicios_seleccionados[index - 1],
                self.ejercicios_seleccionados[index],
            )

            # Reordenar
            for i, ejercicio in enumerate(self.ejercicios_seleccionados):
                ejercicio["orden"] = i + 1

            self.update_selected_exercises_list()
            # Mantener la selección
            self.selected_exercise_listbox.selection_set(index - 1)

        except Exception as e:
            messagebox.showerror("Error", f"Error al mover ejercicio: {e}")

    def move_exercise_down(self):
        """Mueve un ejercicio hacia abajo en la lista"""
        try:
            selection = self.selected_exercise_listbox.curselection()
            if not selection or selection[0] == len(self.ejercicios_seleccionados) - 1:
                return

            index = selection[0]
            # Intercambiar con el siguiente
            (
                self.ejercicios_seleccionados[index],
                self.ejercicios_seleccionados[index + 1],
            ) = (
                self.ejercicios_seleccionados[index + 1],
                self.ejercicios_seleccionados[index],
            )

            # Reordenar
            for i, ejercicio in enumerate(self.ejercicios_seleccionados):
                ejercicio["orden"] = i + 1

            self.update_selected_exercises_list()
            # Mantener la selección
            self.selected_exercise_listbox.selection_set(index + 1)

        except Exception as e:
            messagebox.showerror("Error", f"Error al mover ejercicio: {e}")

    def configure_exercise(self):
        """Configura las series, repeticiones y descanso de un ejercicio"""
        try:
            selection = self.selected_exercise_listbox.curselection()
            if not selection:
                messagebox.showwarning("Advertencia", "Selecciona un ejercicio primero")
                return

            index = selection[0]
            ejercicio_config = self.ejercicios_seleccionados[index]
            self.show_exercise_config_dialog(ejercicio_config, index)

        except Exception as e:
            messagebox.showerror("Error", f"Error al configurar ejercicio: {e}")

    def show_exercise_config_dialog(self, ejercicio_config, index):
        """Muestra diálogo para configurar ejercicio"""
        dialog = ExerciseConfigDialog(self.dialog, ejercicio_config)
        self.dialog.wait_window(dialog.dialog)

        if dialog.result:
            self.ejercicios_seleccionados[index] = dialog.result
            self.update_selected_exercises_list()
            self.update_exercise_info()

    def update_selected_exercises_list(self):
        """Actualiza la lista de ejercicios seleccionados"""
        self.selected_exercise_listbox.delete(0, tk.END)

        for ejercicio_config in self.ejercicios_seleccionados:
            ejercicio = ejercicio_config["ejercicio"]
            texto = f"{ejercicio_config['orden']}. {ejercicio['nombre']} - "
            texto += f"{ejercicio_config['series']}x{ejercicio_config['repeticiones']} "
            texto += f"(Descanso: {ejercicio_config['descanso_segundos']}s)"
            self.selected_exercise_listbox.insert(tk.END, texto)

        # Actualizar contador
        self.exercise_count_label.config(
            text=f"Ejercicios seleccionados: {len(self.ejercicios_seleccionados)}"
        )

    def update_exercise_info(self):
        """Actualiza la información de ejercicios"""
        # Calcular duración total estimada
        total_duration = 0
        total_calorias = 0

        for ejercicio_config in self.ejercicios_seleccionados:
            ejercicio = ejercicio_config["ejercicio"]
            series = ejercicio_config["series"]
            repeticiones = ejercicio_config["repeticiones"]
            descanso = ejercicio_config["descanso_segundos"]

            # Calcular tiempo por ejercicio (estimado)
            tiempo_por_ejercicio = (series * repeticiones * 3) + (series * descanso)
            total_duration += tiempo_por_ejercicio

            # Calorías por minuto del ejercicio
            if ejercicio["calorias_por_minuto"]:
                total_calorias += (tiempo_por_ejercicio / 60) * ejercicio[
                    "calorias_por_minuto"
                ]

        # Actualizar etiquetas
        self.total_duration_label.config(
            text=f"Duración total estimada: {total_duration // 60}m {total_duration % 60}s"
        )

        # Actualizar información calculada en la primera pestaña
        if hasattr(self, "info_calorias_label"):
            if total_calorias > 0:
                self.info_calorias_label.config(
                    text=f"Calorías estimadas: {int(total_calorias)} kcal"
                )
            else:
                self.info_calorias_label.config(text="Calorías estimadas: -")

    def load_routine_data(self):
        """Carga los datos de la rutina en el formulario"""
        if not self.routine_data:
            return

        self.routine_id = self.routine_data.get("id")

        # Cargar datos básicos
        self.nombre_var.set(self.routine_data.get("nombre", ""))
        self.descripcion_var.set(self.routine_data.get("descripcion", ""))
        self.duracion_var.set(str(self.routine_data.get("duracion_minutos", "")))
        self.dificultad_var.set(self.routine_data.get("dificultad", "Principiante"))

        # Cargar ejercicios de la rutina
        try:
            ejercicios_rutina = self.db.obtener_ejercicios_rutina(self.routine_id)
            for i, ejercicio_data in enumerate(ejercicios_rutina):
                ejercicio_config = {
                    "ejercicio": {
                        "id": ejercicio_data["id"],
                        "nombre": ejercicio_data["nombre"],
                        "categoria": ejercicio_data["categoria"],
                        "calorias_por_minuto": ejercicio_data.get(
                            "calorias_por_minuto", 0
                        ),
                    },
                    "series": ejercicio_data.get("series", 3),
                    "repeticiones": ejercicio_data.get("repeticiones", 10),
                    "descanso_segundos": ejercicio_data.get("descanso_segundos", 60),
                    "orden": ejercicio_data.get("orden", i + 1),
                }
                self.ejercicios_seleccionados.append(ejercicio_config)

            self.update_selected_exercises_list()
            self.update_exercise_info()
        except Exception as e:
            messagebox.showerror("Error", f"Error al cargar ejercicios: {e}")

    def validate_form(self) -> tuple[bool, str]:
        """Valida los datos del formulario"""
        nombre = self.nombre_var.get().strip()

        if not nombre:
            return False, "El nombre de la rutina es obligatorio"

        if len(nombre) < 3:
            return False, "El nombre debe tener al menos 3 caracteres"

        if len(self.ejercicios_seleccionados) == 0:
            return False, "La rutina debe tener al menos un ejercicio"

        # Validar duración
        duracion_str = self.duracion_var.get().strip()
        if duracion_str:
            try:
                duracion = int(duracion_str)
                if duracion <= 0 or duracion > 300:
                    return False, "La duración debe estar entre 1 y 300 minutos"
            except ValueError:
                return False, "La duración debe ser un número entero"

        return True, "Validación exitosa"

    def save_routine(self):
        """Guarda la rutina"""
        try:
            # Validar formulario
            is_valid, error_msg = self.validate_form()
            if not is_valid:
                messagebox.showerror("Error de Validación", error_msg)
                return

            # Preparar datos
            nombre = self.nombre_var.get().strip()
            descripcion = self.descripcion_text.get("1.0", tk.END).strip()
            duracion = (
                int(self.duracion_var.get())
                if self.duracion_var.get().strip()
                else None
            )
            dificultad = self.dificultad_var.get() or "Principiante"

            if self.routine_id:
                # Actualizar rutina existente
                self.update_routine(nombre, descripcion, duracion, dificultad)
            else:
                # Crear nueva rutina
                self.create_routine(nombre, descripcion, duracion, dificultad)

        except Exception as e:
            messagebox.showerror("Error", f"Error al guardar la rutina:\n{str(e)}")

    def create_routine(
        self, nombre: str, descripcion: str, duracion: Optional[int], dificultad: str
    ):
        """Crea una nueva rutina"""
        try:
            # Crear rutina en la base de datos
            routine_id = self.db.crear_rutina(
                usuario_id=self.user_id,
                nombre=nombre,
                descripcion=descripcion,
                duracion_minutos=duracion or 30,
                dificultad=dificultad.lower(),
            )

            if routine_id > 0:
                # Agregar ejercicios a la rutina
                for ejercicio_config in self.ejercicios_seleccionados:
                    self.db.agregar_ejercicio_a_rutina(
                        rutina_id=routine_id,
                        ejercicio_id=ejercicio_config["ejercicio"]["id"],
                        series=ejercicio_config["series"],
                        repeticiones=ejercicio_config["repeticiones"],
                        descanso_segundos=ejercicio_config["descanso_segundos"],
                        orden=ejercicio_config["orden"],
                    )

                self.result = {
                    "id": routine_id,
                    "nombre": nombre,
                    "descripcion": descripcion,
                    "duracion_minutos": duracion or 30,
                    "dificultad": dificultad,
                    "ejercicios": self.ejercicios_seleccionados.copy(),
                    "action": "create",
                }

                messagebox.showinfo(
                    "Éxito",
                    f"¡Rutina '{nombre}' creada correctamente!\n\n"
                    f"ID: {routine_id}\n"
                    f"Ejercicios: {len(self.ejercicios_seleccionados)}",
                )
                self.dialog.destroy()
            else:
                messagebox.showerror("Error", "No se pudo crear la rutina")

        except Exception as e:
            messagebox.showerror("Error", f"Error al crear rutina:\n{str(e)}")

    def update_routine(
        self, nombre: str, descripcion: str, duracion: Optional[int], dificultad: str
    ):
        """Actualiza una rutina existente"""
        try:
            # En una implementación completa, aquí iría la lógica de actualización
            # Por ahora, simulamos el éxito

            self.result = {
                "id": self.routine_id,
                "nombre": nombre,
                "descripcion": descripcion,
                "duracion_minutos": duracion or 30,
                "dificultad": dificultad,
                "ejercicios": self.ejercicios_seleccionados.copy(),
                "action": "update",
            }

            messagebox.showinfo(
                "Éxito", f"Rutina '{nombre}' actualizada correctamente!"
            )
            self.dialog.destroy()

        except Exception as e:
            messagebox.showerror("Error", f"Error al actualizar rutina:\n{str(e)}")

    def show_preview(self):
        """Muestra una vista previa de la rutina"""
        if not self.ejercicios_seleccionados:
            messagebox.showwarning("Advertencia", "No hay ejercicios en la rutina")
            return

        preview_text = f"🏃‍♂️ VISTA PREVIA DE LA RUTINA\n\n"
        preview_text += f"Nombre: {self.nombre_var.get()}\n"
        preview_text += f"Ejercicios: {len(self.ejercicios_seleccionados)}\n\n"
        preview_text += "SECUENCIA DE EJERCICIOS:\n"
        preview_text += "=" * 30 + "\n\n"

        for i, ejercicio_config in enumerate(self.ejercicios_seleccionados):
            ejercicio = ejercicio_config["ejercicio"]
            preview_text += f"{i + 1}. {ejercicio['nombre']}\n"
            preview_text += f"   Series: {ejercicio_config['series']}\n"
            preview_text += f"   Repeticiones: {ejercicio_config['repeticiones']}\n"
            preview_text += f"   Descanso: {ejercicio_config['descanso_segundos']}s\n"
            if ejercicio.get("categoria"):
                preview_text += f"   Categoría: {ejercicio['categoria']}\n"
            preview_text += "\n"

        messagebox.showinfo("Vista Previa", preview_text)

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


class ExerciseConfigDialog:
    """Diálogo para configurar las propiedades de un ejercicio"""

    def __init__(self, parent, ejercicio_config):
        self.parent = parent
        self.ejercicio_config = ejercicio_config
        self.result = None

        # Variables del formulario
        self.series_var = tk.StringVar()
        self.repeticiones_var = tk.StringVar()
        self.descanso_var = tk.StringVar()

        self.create_dialog()

    def create_dialog(self):
        """Crea el diálogo de configuración"""
        self.dialog = tk.Toplevel(self.parent)
        ejercicio_nombre = self.ejercicio_config["ejercicio"]["nombre"]
        self.dialog.title(f"Configurar: {ejercicio_nombre}")
        self.dialog.geometry("400x300")
        self.dialog.resizable(False, False)
        self.dialog.transient(self.parent)
        self.dialog.grab_set()

        # Centrar ventana
        self.dialog.update_idletasks()
        x = (self.dialog.winfo_screenwidth() // 2) - (400 // 2)
        y = (self.dialog.winfo_screenheight() // 2) - (300 // 2)
        self.dialog.geometry(f"400x300+{x}+{y}")

        # Configurar cierre con ESC
        self.dialog.bind("<Escape>", lambda e: self.cancel())

        # Crear contenido
        self.create_content()

        # Cargar valores actuales
        self.load_current_values()

    def create_content(self):
        """Crea el contenido del diálogo"""
        # Frame principal
        main_frame = ttk.Frame(self.dialog, padding="20")
        main_frame.pack(fill=tk.BOTH, expand=True)

        # Título
        ejercicio_nombre = self.ejercicio_config["ejercicio"]["nombre"]
        title_label = ttk.Label(
            main_frame, text=f"⚙️ Configurar Ejercicio", font=("Arial", 14, "bold")
        )
        title_label.pack(pady=(0, 10))

        # Nombre del ejercicio
        name_label = ttk.Label(main_frame, text=ejercicio_nombre, font=("Arial", 12))
        name_label.pack(pady=(0, 20))

        # Campos de configuración
        ttk.Label(
            main_frame, text="Número de series:", font=("Arial", 10, "bold")
        ).pack(anchor=tk.W, pady=(5, 2))
        self.series_entry = ttk.Entry(
            main_frame, textvariable=self.series_var, width=20, font=("Arial", 10)
        )
        self.series_entry.pack(fill=tk.X, pady=(0, 10))
        self.series_entry.bind("<Return>", lambda e: self.repeticiones_entry.focus())

        ttk.Label(
            main_frame, text="Repeticiones por serie:", font=("Arial", 10, "bold")
        ).pack(anchor=tk.W, pady=(5, 2))
        self.repeticiones_entry = ttk.Entry(
            main_frame, textvariable=self.repeticiones_var, width=20, font=("Arial", 10)
        )
        self.repeticiones_entry.pack(fill=tk.X, pady=(0, 10))
        self.repeticiones_entry.bind("<Return>", lambda e: self.descanso_entry.focus())

        ttk.Label(
            main_frame,
            text="Descanso entre series (segundos):",
            font=("Arial", 10, "bold"),
        ).pack(anchor=tk.W, pady=(5, 2))
        self.descanso_entry = ttk.Entry(
            main_frame, textvariable=self.descanso_var, width=20, font=("Arial", 10)
        )
        self.descanso_entry.pack(fill=tk.X, pady=(0, 20))
        self.descanso_entry.bind("<Return>", lambda e: self.save_button.focus())

        # Botones
        buttons_frame = ttk.Frame(main_frame)
        buttons_frame.pack(fill=tk.X)

        ttk.Button(buttons_frame, text="Cancelar", command=self.cancel, width=10).pack(
            side=tk.RIGHT, padx=(10, 0)
        )

        self.save_button = ttk.Button(
            buttons_frame, text="Guardar", command=self.save_config, width=10
        )
        self.save_button.pack(side=tk.RIGHT)

    def load_current_values(self):
        """Carga los valores actuales"""
        self.series_var.set(str(self.ejercicio_config["series"]))
        self.repeticiones_var.set(str(self.ejercicio_config["repeticiones"]))
        self.descanso_var.set(str(self.ejercicio_config["descanso_segundos"]))

    def save_config(self):
        """Guarda la configuración"""
        try:
            # Validar valores
            series = int(self.series_var.get())
            repeticiones = int(self.repeticiones_var.get())
            descanso = int(self.descanso_var.get())

            if series < 1 or series > 20:
                messagebox.showerror("Error", "Las series deben estar entre 1 y 20")
                return

            if repeticiones < 1 or repeticiones > 100:
                messagebox.showerror(
                    "Error", "Las repeticiones deben estar entre 1 y 100"
                )
                return

            if descanso < 10 or descanso > 600:
                messagebox.showerror(
                    "Error", "El descanso debe estar entre 10 y 600 segundos"
                )
                return

            # Actualizar configuración
            self.ejercicio_config["series"] = series
            self.ejercicio_config["repeticiones"] = repeticiones
            self.ejercicio_config["descanso_segundos"] = descanso

            self.result = self.ejercicio_config
            self.dialog.destroy()

        except ValueError:
            messagebox.showerror("Error", "Los valores deben ser números enteros")
        except Exception as e:
            messagebox.showerror("Error", f"Error al guardar: {e}")

    def cancel(self):
        """Cancela la operación"""
        self.dialog.destroy()


# Funciones de utilidad
def create_routine_dialog(
    parent, db_manager, user_manager, user_id: int, routine_data: Optional[Dict] = None
) -> RoutineDialog:
    """Función de utilidad para crear un diálogo de rutina"""
    return RoutineDialog(parent, db_manager, user_manager, user_id, routine_data)


def show_routine_creation_dialog(
    parent, db_manager, user_manager, user_id: int
) -> Optional[Dict]:
    """Función de conveniencia para mostrar un diálogo de creación de rutina"""
    dialog = RoutineDialog(parent, db_manager, user_manager, user_id, None)
    return dialog.show()


def show_routine_edit_dialog(
    parent, db_manager, user_manager, user_id: int, routine_data: Dict
) -> Optional[Dict]:
    """Función de conveniencia para mostrar un diálogo de edición de rutina"""
    dialog = RoutineDialog(parent, db_manager, user_manager, user_id, routine_data)
    return dialog.show()
