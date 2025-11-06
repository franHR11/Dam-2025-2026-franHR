# user_dialog.py - Diálogo de usuario para SmartFit
# Fran - Desarrollo de interfaces

import tkinter as tk
from tkinter import messagebox, ttk
from typing import Any, Dict, Optional


class UserDialog:
    """
    Diálogo modal para crear y editar usuarios en SmartFit
    Proporciona una interfaz intuitiva para gestionar información del usuario
    """

    def __init__(
        self, parent, db_manager, user_manager, user_data: Optional[Dict] = None
    ):
        """
        Inicializa el diálogo de usuario

        Args:
            parent: Ventana padre
            db_manager: Gestor de base de datos
            user_manager: Gestor de usuarios
            user_data: Datos del usuario a editar (None para crear nuevo)
        """
        self.parent = parent
        self.db = db_manager
        self.user_manager = user_manager
        self.user_data = user_data
        self.result = None
        self.user_id = None

        # Variables del formulario
        self.nombre_var = tk.StringVar()
        self.edad_var = tk.StringVar()
        self.peso_var = tk.StringVar()
        self.altura_var = tk.StringVar()
        self.objetivo_var = tk.StringVar()

        # Estado del diálogo
        self.dialog = None

        # Crear diálogo
        self.create_dialog()

    def create_dialog(self):
        """Crea la ventana del diálogo"""
        self.dialog = tk.Toplevel(self.parent)
        self.dialog.title("Crear Usuario" if not self.user_data else "Editar Usuario")
        self.dialog.geometry("450x500")
        self.dialog.resizable(False, False)
        self.dialog.transient(self.parent)
        self.dialog.grab_set()

        # Centrar ventana
        self.center_window()

        # Configurar cierre con ESC
        self.dialog.bind("<Escape>", lambda e: self.cancel())

        # Crear contenido
        self.create_content()

        # Cargar datos si es edición
        if self.user_data:
            self.load_user_data()

        # Enfocar primer campo
        self.dialog.after(100, lambda: self.nombre_entry.focus())

    def center_window(self):
        """Centra la ventana en la pantalla"""
        self.dialog.update_idletasks()
        x = (self.dialog.winfo_screenwidth() // 2) - (450 // 2)
        y = (self.dialog.winfo_screenheight() // 2) - (500 // 2)
        self.dialog.geometry(f"450x500+{x}+{y}")

    def create_content(self):
        """Crea el contenido del diálogo"""
        # Frame principal con padding
        main_frame = ttk.Frame(self.dialog, padding="20")
        main_frame.pack(fill=tk.BOTH, expand=True)

        # Título
        title_text = (
            "👤 Crear Nuevo Usuario" if not self.user_data else "✏️ Editar Usuario"
        )
        title_label = ttk.Label(main_frame, text=title_text, font=("Arial", 14, "bold"))
        title_label.pack(pady=(0, 20))

        # Frame de formulario
        form_frame = ttk.Frame(main_frame)
        form_frame.pack(fill=tk.BOTH, expand=True, pady=(0, 20))

        # Campo Nombre (obligatorio)
        ttk.Label(
            form_frame, text="Nombre completo *:", font=("Arial", 10, "bold")
        ).pack(anchor=tk.W, pady=(5, 2))
        self.nombre_entry = ttk.Entry(
            form_frame, textvariable=self.nombre_var, width=40, font=("Arial", 10)
        )
        self.nombre_entry.pack(fill=tk.X, pady=(0, 15))
        self.nombre_entry.bind("<Return>", lambda e: self.edad_entry.focus())

        # Campo Edad
        ttk.Label(form_frame, text="Edad (años):", font=("Arial", 10)).pack(
            anchor=tk.W, pady=(5, 2)
        )
        self.edad_entry = ttk.Entry(
            form_frame, textvariable=self.edad_var, width=40, font=("Arial", 10)
        )
        self.edad_entry.pack(fill=tk.X, pady=(0, 15))
        self.edad_entry.bind("<Return>", lambda e: self.peso_entry.focus())

        # Frame para peso y altura en la misma fila
        weight_height_frame = ttk.Frame(form_frame)
        weight_height_frame.pack(fill=tk.X, pady=(0, 15))

        # Campo Peso
        weight_frame = ttk.Frame(weight_height_frame)
        weight_frame.pack(side=tk.LEFT, fill=tk.X, expand=True, padx=(0, 10))

        ttk.Label(weight_frame, text="Peso (kg):", font=("Arial", 10)).pack(
            anchor=tk.W, pady=(5, 2)
        )
        self.peso_entry = ttk.Entry(
            weight_frame, textvariable=self.peso_var, width=20, font=("Arial", 10)
        )
        self.peso_entry.pack(fill=tk.X)
        self.peso_entry.bind("<Return>", lambda e: self.altura_entry.focus())

        # Campo Altura
        height_frame = ttk.Frame(weight_height_frame)
        height_frame.pack(side=tk.RIGHT, fill=tk.X, expand=True, padx=(10, 0))

        ttk.Label(height_frame, text="Altura (cm):", font=("Arial", 10)).pack(
            anchor=tk.W, pady=(5, 2)
        )
        self.altura_entry = ttk.Entry(
            height_frame, textvariable=self.altura_var, width=20, font=("Arial", 10)
        )
        self.altura_entry.pack(fill=tk.X)
        self.altura_entry.bind("<Return>", lambda e: self.objetivo_combo.focus())

        # Campo Objetivo
        ttk.Label(form_frame, text="Objetivo fitness:", font=("Arial", 10)).pack(
            anchor=tk.W, pady=(15, 2)
        )
        self.objetivo_combo = ttk.Combobox(
            form_frame,
            textvariable=self.objetivo_var,
            values=[
                "Perder peso",
                "Ganar músculo",
                "Mantenerme en forma",
                "Mejorar resistencia",
                "Aumentar flexibilidad",
                "Rehabilitación",
                "Otro",
            ],
            width=38,
            state="readonly",
            font=("Arial", 10),
        )
        self.objetivo_combo.pack(fill=tk.X, pady=(0, 15))
        self.objetivo_combo.bind(
            "<<ComboboxSelected>>", lambda e: self.save_button.focus()
        )

        # Información adicional
        info_frame = ttk.LabelFrame(form_frame, text="ℹ️ Información", padding="10")
        info_frame.pack(fill=tk.X, pady=(10, 0))

        info_text = "• Los campos marcados con * son obligatorios\n"
        info_text += "• Puedes completar los datos gradualmente\n"
        info_text += "• Los datos se guardarán automáticamente"

        ttk.Label(
            info_frame, text=info_text, font=("Arial", 8), foreground="gray"
        ).pack(anchor=tk.W)

        # Frame de botones
        buttons_frame = ttk.Frame(main_frame)
        buttons_frame.pack(fill=tk.X, pady=(10, 0))

        # Botón Cancelar
        ttk.Button(buttons_frame, text="Cancelar", command=self.cancel, width=12).pack(
            side=tk.RIGHT, padx=(10, 0)
        )

        # Botón Guardar
        self.save_button = ttk.Button(
            buttons_frame,
            text="Guardar Usuario" if not self.user_data else "Actualizar Usuario",
            command=self.save_user,
            width=15,
        )
        self.save_button.pack(side=tk.RIGHT)

        # Botón Limpiar (solo para nuevos usuarios)
        if not self.user_data:
            ttk.Button(
                buttons_frame, text="Limpiar", command=self.clear_form, width=10
            ).pack(side=tk.LEFT)

    def load_user_data(self):
        """Carga los datos del usuario en el formulario"""
        if not self.user_data:
            return

        self.user_id = self.user_data.get("id")

        # Cargar datos en los campos
        self.nombre_var.set(self.user_data.get("nombre", ""))
        self.edad_var.set(
            str(self.user_data.get("edad", "")) if self.user_data.get("edad") else ""
        )
        self.peso_var.set(
            str(self.user_data.get("peso", "")) if self.user_data.get("peso") else ""
        )
        self.altura_var.set(
            str(self.user_data.get("altura", ""))
            if self.user_data.get("altura")
            else ""
        )
        self.objetivo_var.set(self.user_data.get("objetivo", "Mantenerme en forma"))

    def clear_form(self):
        """Limpia todos los campos del formulario"""
        self.nombre_var.set("")
        self.edad_var.set("")
        self.peso_var.set("")
        self.altura_var.set("")
        self.objetivo_var.set("Mantenerme en forma")
        self.nombre_entry.focus()

    def validate_form(self) -> tuple[bool, str]:
        """
        Valida los datos del formulario

        Returns:
            Tuple con (es_válido, mensaje_error)
        """
        nombre = self.nombre_var.get().strip()

        # Validar nombre (obligatorio)
        if not nombre:
            return False, "El nombre es obligatorio"

        if len(nombre) < 2:
            return False, "El nombre debe tener al menos 2 caracteres"

        if len(nombre) > 50:
            return False, "El nombre no puede tener más de 50 caracteres"

        # Validar edad (opcional)
        edad_str = self.edad_var.get().strip()
        if edad_str:
            try:
                edad = int(edad_str)
                if edad < 13 or edad > 100:
                    return False, "La edad debe estar entre 13 y 100 años"
            except ValueError:
                return False, "La edad debe ser un número entero"

        # Validar peso (opcional)
        peso_str = self.peso_var.get().strip()
        if peso_str:
            try:
                peso = float(peso_str)
                if peso < 30 or peso > 300:
                    return False, "El peso debe estar entre 30 y 300 kg"
            except ValueError:
                return False, "El peso debe ser un número"

        # Validar altura (opcional)
        altura_str = self.altura_var.get().strip()
        if altura_str:
            try:
                altura = float(altura_str)
                if altura < 120 or altura > 250:
                    return False, "La altura debe estar entre 120 y 250 cm"
            except ValueError:
                return False, "La altura debe ser un número"

        return True, "Validación exitosa"

    def save_user(self):
        """Guarda el usuario (crear nuevo o actualizar existente)"""
        try:
            # Validar formulario
            is_valid, error_msg = self.validate_form()
            if not is_valid:
                messagebox.showerror("Error de Validación", error_msg)
                return

            # Preparar datos
            nombre = self.nombre_var.get().strip()
            edad = int(self.edad_var.get()) if self.edad_var.get().strip() else None
            peso = float(self.peso_var.get()) if self.peso_var.get().strip() else None
            altura = (
                float(self.altura_var.get()) if self.altura_var.get().strip() else None
            )
            objetivo = self.objetivo_var.get() or "Mantenerme en forma"

            if self.user_id:
                # Actualizar usuario existente
                self.update_user(nombre, edad, peso, altura, objetivo)
            else:
                # Crear nuevo usuario
                self.create_user(nombre, edad, peso, altura, objetivo)

        except Exception as e:
            messagebox.showerror("Error", f"Error al guardar el usuario:\n{str(e)}")

    def create_user(
        self,
        nombre: str,
        edad: Optional[int],
        peso: Optional[float],
        altura: Optional[float],
        objetivo: str,
    ):
        """Crea un nuevo usuario"""
        try:
            user_id = self.user_manager.crear_usuario(
                nombre=nombre, edad=edad, peso=peso, altura=altura, objetivo=objetivo
            )

            if user_id > 0:
                self.result = {
                    "id": user_id,
                    "nombre": nombre,
                    "edad": edad,
                    "peso": peso,
                    "altura": altura,
                    "objetivo": objetivo,
                    "action": "create",
                }

                messagebox.showinfo(
                    "Éxito",
                    f"¡Usuario '{nombre}' creado correctamente!\n\n"
                    f"ID: {user_id}\n"
                    f"Ya puedes comenzar a usar SmartFit.",
                )
                self.dialog.destroy()
            else:
                messagebox.showerror("Error", "No se pudo crear el usuario")

        except Exception as e:
            messagebox.showerror("Error", f"Error al crear usuario:\n{str(e)}")

    def update_user(
        self,
        nombre: str,
        edad: Optional[int],
        peso: Optional[float],
        altura: Optional[float],
        objetivo: str,
    ):
        """Actualiza un usuario existente"""
        try:
            # En una implementación completa, aquí iría la lógica de actualización
            # Por ahora, simulamos el éxito

            self.result = {
                "id": self.user_id,
                "nombre": nombre,
                "edad": edad,
                "peso": peso,
                "altura": altura,
                "objetivo": objetivo,
                "action": "update",
            }

            messagebox.showinfo(
                "Éxito", f"Usuario '{nombre}' actualizado correctamente!"
            )
            self.dialog.destroy()

        except Exception as e:
            messagebox.showerror("Error", f"Error al actualizar usuario:\n{str(e)}")

    def cancel(self):
        """Cancela la operación y cierra el diálogo"""
        self.dialog.destroy()

    def get_result(self) -> Optional[Dict]:
        """Obtiene el resultado del diálogo"""
        return self.result

    def show(self) -> Optional[Dict]:
        """
        Muestra el diálogo y espera el resultado

        Returns:
            Dict con los datos del usuario o None si se canceló
        """
        self.dialog.wait_window()
        return self.get_result()


class UserEditDialog(UserDialog):
    """
    Diálogo especializado para editar usuarios existentes
    Hereda de UserDialog y personaliza la funcionalidad
    """

    def __init__(self, parent, db_manager, user_manager, user_data: Dict):
        """Inicializa el diálogo de edición"""
        super().__init__(parent, db_manager, user_manager, user_data)
        self.dialog.title("Editar Usuario - SmartFit")


class UserCreateDialog(UserDialog):
    """
    Diálogo especializado para crear nuevos usuarios
    Hereda de UserDialog y personaliza la funcionalidad
    """

    def __init__(self, parent, db_manager, user_manager):
        """Inicializa el diálogo de creación"""
        super().__init__(parent, db_manager, user_manager, None)
        self.dialog.title("Crear Usuario - SmartFit")


# Funciones de utilidad para crear diálogos
def create_user_dialog(
    parent, db_manager, user_manager, user_data: Optional[Dict] = None
) -> UserDialog:
    """
    Función de utilidad para crear un diálogo de usuario

    Args:
        parent: Ventana padre
        db_manager: Gestor de base de datos
        user_manager: Gestor de usuarios
        user_data: Datos del usuario (None para crear nuevo)

    Returns:
        Instancia de UserDialog configurada
    """
    if user_data:
        return UserEditDialog(parent, db_manager, user_manager, user_data)
    else:
        return UserCreateDialog(parent, db_manager, user_manager)


def show_user_creation_dialog(parent, db_manager, user_manager) -> Optional[Dict]:
    """
    Función de conveniencia para mostrar y manejar un diálogo de creación de usuario

    Args:
        parent: Ventana padre
        db_manager: Gestor de base de datos
        user_manager: Gestor de usuarios

    Returns:
        Dict con los datos del usuario creado o None
    """
    dialog = UserCreateDialog(parent, db_manager, user_manager)
    return dialog.show()


def show_user_edit_dialog(
    parent, db_manager, user_manager, user_data: Dict
) -> Optional[Dict]:
    """
    Función de conveniencia para mostrar y manejar un diálogo de edición de usuario

    Args:
        parent: Ventana padre
        db_manager: Gestor de base de datos
        user_manager: Gestor de usuarios
        user_data: Datos del usuario a editar

    Returns:
        Dict con los datos actualizados o None
    """
    dialog = UserEditDialog(parent, db_manager, user_manager, user_data)
    return dialog.show()
