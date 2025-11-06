# info_card.py - Widget InfoCard para SmartFit
# Fran - Desarrollo de interfaces

import tkinter as tk
from tkinter import ttk
from typing import Any, Optional, Union


class InfoCard(ttk.Frame):
    """
    Widget personalizado para mostrar información en formato de tarjeta.
    Proporciona un diseño moderno y atractivo para mostrar datos, estadísticas o cualquier información relevante.

    Características:
    - Diseño de tarjeta con bordes y colores personalizables
    - Título personalizable
    - Contenido dinámico (texto o widgets)
    - Iconos y colores personalizables
    - Soporte para diferentes estados (normal, hover, click)
    - Posibilidad de mostrar acciones (botones)
    """

    def __init__(
        self,
        parent,
        title: str = "",
        content: str = "",
        icon: str = "",
        color: str = "#4A90E2",
        width: int = 300,
        height: int = 150,
        **kwargs,
    ):
        """
        Inicializa el InfoCard

        Args:
            parent: Widget padre
            title: Título de la tarjeta
            content: Contenido de la tarjeta (texto o widget)
            icon: Emoji o texto para el icono
            color: Color principal de la tarjeta
            width: Ancho de la tarjeta
            height: Alto de la tarjeta
            **kwargs: Argumentos adicionales para el frame padre
        """
        super().__init__(parent, **kwargs)

        # Configuración de la tarjeta
        self.title = title
        self.content = content
        self.icon = icon
        self.color = color
        self.width = width
        self.height = height

        # Configurar tamaño
        self.config(width=width, height=height)

        # Variables de estado
        self._is_hover = False
        self._action_callback = None
        self._action_button = None

        # Crear la interfaz
        self.create_widgets()

        # Configurar eventos
        self.bind_events()

    def create_widgets(self):
        """Crea todos los widgets de la tarjeta"""
        # Frame principal de la tarjeta con estilo personalizado
        self.card_frame = tk.Frame(
            self, bg=self.color, relief=tk.RAISED, bd=2, cursor="hand2"
        )
        self.card_frame.pack(fill=tk.BOTH, expand=True, padx=2, pady=2)

        # Frame interno para el contenido
        self.content_frame = tk.Frame(self.card_frame, bg=self.color, padx=15, pady=15)
        self.content_frame.pack(fill=tk.BOTH, expand=True)

        # Crear contenido
        self.create_content()

    def create_content(self):
        """Crea el contenido de la tarjeta"""
        # Frame para el título
        if self.title or self.icon:
            title_frame = tk.Frame(self.content_frame, bg=self.color)
            title_frame.pack(fill=tk.X, pady=(0, 10))

            # Mostrar icono si existe
            if self.icon:
                icon_label = tk.Label(
                    title_frame,
                    text=self.icon,
                    font=("Arial", 16),
                    bg=self.color,
                    fg="white",
                )
                icon_label.pack(side=tk.LEFT, padx=(0, 8))

            # Mostrar título si existe
            if self.title:
                title_label = tk.Label(
                    title_frame,
                    text=self.title,
                    font=("Arial", 12, "bold"),
                    bg=self.color,
                    fg="white",
                    anchor="w",
                )
                title_label.pack(side=tk.LEFT, fill=tk.X, expand=True)

        # Contenido de la tarjeta
        if isinstance(self.content, str):
            # Si el contenido es texto
            content_label = tk.Label(
                self.content_frame,
                text=self.content,
                font=("Arial", 10),
                bg=self.color,
                fg="white",
                justify=tk.LEFT,
                wraplength=self.width - 30,
            )
            content_label.pack(fill=tk.BOTH, expand=True)
        else:
            # Si el contenido es otro widget
            self.content.pack(fill=tk.BOTH, expand=True)

    def bind_events(self):
        """Configura los eventos de la tarjeta"""
        # Eventos de mouse
        self.card_frame.bind("<Enter>", self.on_enter)
        self.card_frame.bind("<Leave>", self.on_leave)
        self.card_frame.bind("<Button-1>", self.on_click)

    def on_enter(self, event):
        """Maneja el evento de mouse sobre la tarjeta"""
        self._is_hover = True
        self.card_frame.config(relief=tk.SUNKEN, bd=3)
        self.card_frame.config(cursor="hand2")

    def on_leave(self, event):
        """Maneja el evento de mouse fuera de la tarjeta"""
        self._is_hover = False
        self.card_frame.config(relief=tk.RAISED, bd=2)
        self.card_frame.config(cursor="hand2")

    def on_click(self, event):
        """Maneja el evento de click en la tarjeta"""
        if self._action_callback:
            self._action_callback()

        # Efecto visual de click
        self.card_frame.config(relief=tk.FLAT, bd=1)
        self.after(100, lambda: self.card_frame.config(relief=tk.SUNKEN, bd=3))

    def set_action(self, callback: callable):
        """
        Configura una acción para la tarjeta

        Args:
            callback: Función a ejecutar al hacer click
        """
        self._action_callback = callback

    def set_title(self, title: str):
        """
        Actualiza el título de la tarjeta

        Args:
            title: Nuevo título
        """
        self.title = title
        self.refresh_content()

    def set_content(self, content: Union[str, tk.Widget]):
        """
        Actualiza el contenido de la tarjeta

        Args:
            content: Nuevo contenido (texto o widget)
        """
        self.content = content
        self.refresh_content()

    def set_icon(self, icon: str):
        """
        Actualiza el icono de la tarjeta

        Args:
            icon: Nuevo icono (emoji o texto)
        """
        self.icon = icon
        self.refresh_content()

    def set_color(self, color: str):
        """
        Actualiza el color de la tarjeta

        Args:
            color: Nuevo color (formato #RRGGBB o nombre)
        """
        self.color = color
        self.refresh_all()

    def add_action_button(
        self, text: str, callback: callable, position: str = "bottom"
    ):
        """
        Añade un botón de acción a la tarjeta

        Args:
            text: Texto del botón
            callback: Función a ejecutar
            position: Posición del botón ("bottom" o "top")
        """
        if not self._action_button:
            # Crear el botón
            self._action_button = tk.Button(
                self.content_frame,
                text=text,
                command=callback,
                font=("Arial", 9, "bold"),
                bg="white",
                fg=self.color,
                relief=tk.FLAT,
                padx=10,
                pady=5,
                cursor="hand2",
            )

            # Posicionar el botón
            if position == "bottom":
                self._action_button.pack(fill=tk.X, pady=(10, 0))
            else:
                self._action_button.pack(fill=tk.X, pady=(0, 10))
        else:
            # Actualizar botón existente
            self._action_button.config(text=text, command=callback)

    def remove_action_button(self):
        """Elimina el botón de acción si existe"""
        if self._action_button:
            self._action_button.destroy()
            self._action_button = None

    def refresh_content(self):
        """Actualiza solo el contenido de la tarjeta"""
        # Limpiar contenido anterior
        for widget in self.content_frame.winfo_children():
            if widget != self._action_button:  # Preservar botón de acción
                widget.destroy()

        # Recrear contenido
        self.create_content()

    def refresh_all(self):
        """Actualiza toda la tarjeta (colores, contenido, etc.)"""
        # Actualizar colores
        self.card_frame.config(bg=self.color)
        self.content_frame.config(bg=self.color)

        # Actualizar contenido
        self.refresh_content()

    def get_dimensions(self) -> tuple[int, int]:
        """
        Obtiene las dimensiones actuales de la tarjeta

        Returns:
            Tuple con (ancho, alto)
        """
        return (self.width, self.height)

    def is_hovered(self) -> bool:
        """
        Verifica si la tarjeta está en estado hover

        Returns:
            True si está siendo señalada por el mouse
        """
        return self._is_hover


class StatCard(InfoCard):
    """
    Widget especializado para mostrar estadísticas.
    Extiende InfoCard con funcionalidades específicas para números y estadísticas.
    """

    def __init__(
        self,
        parent,
        title: str = "",
        value: Union[int, float, str] = 0,
        unit: str = "",
        icon: str = "",
        color: str = "#4A90E2",
        width: int = 300,
        height: int = 150,
        **kwargs,
    ):
        """
        Inicializa el StatCard

        Args:
            parent: Widget padre
            title: Título de la estadística
            value: Valor numérico o texto
            unit: Unidad de medida
            icon: Emoji o icono
            color: Color de la tarjeta
            width: Ancho de la tarjeta
            height: Alto de la tarjeta
            **kwargs: Argumentos adicionales
        """
        self.value = value
        self.unit = unit
        self.value_label = None

        # Crear contenido formateado
        content = self._format_value()

        super().__init__(parent, title, content, icon, color, width, height, **kwargs)

    def _format_value(self) -> str:
        """
        Formatea el valor para mostrar

        Returns:
            String formateado con valor y unidad
        """
        if isinstance(self.value, (int, float)):
            if self.unit:
                return f"{self.value} {self.unit}"
            else:
                return str(self.value)
        else:
            return str(self.value)

    def set_value(self, value: Union[int, float, str], unit: str = ""):
        """
        Actualiza el valor de la estadística

        Args:
            value: Nuevo valor
            unit: Nueva unidad (opcional)
        """
        self.value = value
        if unit:
            self.unit = unit

        # Actualizar display
        formatted_value = self._format_value()
        if self.value_label:
            self.value_label.config(text=formatted_value)

    def add_trend(self, trend: str, is_positive: bool = True):
        """
        Añade una indicación de tendencia al valor

        Args:
            trend: Texto de la tendencia (ej: "+5%", "↗", "↘")
            is_positive: True si la tendencia es positiva
        """
        color = "#28a745" if is_positive else "#dc3545"  # Verde o rojo

        trend_label = tk.Label(
            self.content_frame,
            text=f"{trend}",
            font=("Arial", 9, "bold"),
            bg=self.color,
            fg=color,
        )
        trend_label.pack(anchor=tk.E)

    def animate_value(self, target_value: Union[int, float], duration: int = 1000):
        """
        Anima el cambio de valor

        Args:
            target_value: Valor objetivo
            duration: Duración de la animación en ms
        """
        try:
            start_value = float(self.value)
            target = float(target_value)
            steps = 20
            step_duration = duration // steps
            step_value = (target - start_value) / steps

            def animate_step(step):
                if step < steps:
                    current_value = start_value + (step_value * step)
                    self.set_value(current_value)
                    self.after(step_duration, lambda: animate_step(step + 1))
                else:
                    self.set_value(target)

            animate_step(0)
        except (ValueError, TypeError):
            # Si no se puede animar, simplemente actualizar
            self.set_value(target_value)


class ProgressCard(InfoCard):
    """
    Widget especializado para mostrar progreso.
    Extiende InfoCard con una barra de progreso integrada.
    """

    def __init__(
        self,
        parent,
        title: str = "",
        progress: float = 0,
        target: float = 100,
        unit: str = "%",
        icon: str = "📊",
        color: str = "#4A90E2",
        width: int = 300,
        height: int = 150,
        **kwargs,
    ):
        """
        Inicializa el ProgressCard

        Args:
            parent: Widget padre
            title: Título del progreso
            progress: Progreso actual
            target: Objetivo/total
            unit: Unidad de medida
            icon: Icono de la tarjeta
            color: Color de la tarjeta
            width: Ancho de la tarjeta
            height: Alto de la tarjeta
            **kwargs: Argumentos adicionales
        """
        self.progress = progress
        self.target = target
        self.unit = unit
        self.progress_bar = None
        self.progress_label = None

        # Calcular porcentaje
        percentage = (progress / target) * 100 if target > 0 else 0
        content = f"{percentage:.1f}{unit}"

        super().__init__(parent, title, content, icon, color, width, height, **kwargs)

    def create_content(self):
        """Crea el contenido con barra de progreso"""
        # Frame para el título
        if self.title or self.icon:
            title_frame = tk.Frame(self.content_frame, bg=self.color)
            title_frame.pack(fill=tk.X, pady=(0, 10))

            # Mostrar icono
            if self.icon:
                icon_label = tk.Label(
                    title_frame,
                    text=self.icon,
                    font=("Arial", 16),
                    bg=self.color,
                    fg="white",
                )
                icon_label.pack(side=tk.LEFT, padx=(0, 8))

            # Mostrar título
            if self.title:
                title_label = tk.Label(
                    title_frame,
                    text=self.title,
                    font=("Arial", 12, "bold"),
                    bg=self.color,
                    fg="white",
                    anchor="w",
                )
                title_label.pack(side=tk.LEFT, fill=tk.X, expand=True)

        # Barra de progreso
        progress_frame = tk.Frame(self.content_frame, bg=self.color)
        progress_frame.pack(fill=tk.X, pady=(10, 5))

        # Fondo de la barra
        bg_frame = tk.Frame(progress_frame, height=20, bg="#ffffff", relief=tk.FLAT)
        bg_frame.pack(fill=tk.X, pady=2)
        bg_frame.pack_propagate(False)

        # Barra de progreso
        self.progress_bar = tk.Frame(bg_frame, height=16, bg=self.color, relief=tk.FLAT)
        self.progress_bar.pack(side=tk.LEFT, fill=tk.Y, padx=2, pady=2)

        # Texto del progreso
        self.progress_label = tk.Label(
            progress_frame,
            text="",
            font=("Arial", 10, "bold"),
            bg=self.color,
            fg="white",
        )
        self.progress_label.pack(anchor=tk.E)

        # Actualizar visualización
        self.update_progress_display()

    def update_progress_display(self):
        """Actualiza la visualización del progreso"""
        percentage = (self.progress / self.target) * 100 if self.target > 0 else 0

        # Ajustar ancho de la barra
        bar_width = max(2, int((percentage / 100) * (self.width - 40)))
        self.progress_bar.config(width=bar_width)

        # Actualizar texto
        if self.unit == "%":
            self.progress_label.config(text=f"{percentage:.1f}%")
            if hasattr(self, "content_label"):
                self.content_label.config(
                    text=f"{self.progress:.0f} / {self.target:.0f}"
                )
        else:
            self.progress_label.config(
                text=f"{self.progress} / {self.target} {self.unit}"
            )

    def set_progress(self, progress: float, target: float = None):
        """
        Actualiza el progreso

        Args:
            progress: Nuevo progreso
            target: Nuevo objetivo (opcional)
        """
        self.progress = progress
        if target is not None:
            self.target = target

        self.update_progress_display()


# Función de utilidad para crear tarjetas rápidamente
def create_info_card(parent, **kwargs) -> InfoCard:
    """
    Función de utilidad para crear un InfoCard

    Args:
        **kwargs: Argumentos para el InfoCard

    Returns:
        Instancia de InfoCard
    """
    return InfoCard(parent, **kwargs)


def create_stat_card(parent, **kwargs) -> StatCard:
    """
    Función de utilidad para crear un StatCard

    Args:
        **kwargs: Argumentos para el StatCard

    Returns:
        Instancia de StatCard
    """
    return StatCard(parent, **kwargs)


def create_progress_card(parent, **kwargs) -> ProgressCard:
    """
    Función de utilidad para crear un ProgressCard

    Args:
        **kwargs: Argumentos para el ProgressCard

    Returns:
        Instancia de ProgressCard
    """
    return ProgressCard(parent, **kwargs)


# Ejemplo de uso
if __name__ == "__main__":
    # Crear ventana de prueba
    root = tk.Tk()
    root.title("InfoCard Demo")
    root.geometry("800x600")
    root.configure(bg="#f0f0f0")

    # Frame principal
    main_frame = tk.Frame(root, bg="#f0f0f0")
    main_frame.pack(fill=tk.BOTH, expand=True, padx=20, pady=20)

    # Título
    tk.Label(
        main_frame, text="Demo de InfoCard", font=("Arial", 20, "bold"), bg="#f0f0f0"
    ).pack(pady=(0, 20))

    # Crear tarjetas de ejemplo
    cards_frame = tk.Frame(main_frame, bg="#f0f0f0")
    cards_frame.pack(fill=tk.BOTH, expand=True)

    # InfoCard básica
    info_card = InfoCard(
        cards_frame,
        title="Información del Usuario",
        content="Usuario: Juan Pérez\nEdad: 25 años\nObjetivo: Perder peso",
        icon="👤",
        color="#4A90E2",
    )
    info_card.grid(row=0, column=0, padx=10, pady=10)

    # StatCard
    stat_card = StatCard(
        cards_frame,
        title="Calorías Quemadas",
        value=350,
        unit="kcal",
        icon="🔥",
        color="#ff6b6b",
    )
    stat_card.grid(row=0, column=1, padx=10, pady=10)

    # ProgressCard
    progress_card = ProgressCard(
        cards_frame,
        title="Progreso del Objetivo",
        progress=75,
        target=100,
        unit="%",
        icon="🎯",
        color="#28a745",
    )
    progress_card.grid(row=1, column=0, padx=10, pady=10)

    # Tarjeta interactiva
    def on_card_click():
        print("¡Tarjeta clickeada!")

    interactive_card = InfoCard(
        cards_frame,
        title="Entrenamiento",
        content="Haz click para iniciar\nun nuevo entrenamiento",
        icon="💪",
        color="#6c5ce7",
    )
    interactive_card.set_action(on_card_click)
    interactive_card.grid(row=1, column=1, padx=10, pady=10)

    # Botones de prueba
    buttons_frame = tk.Frame(main_frame, bg="#f0f0f0")
    buttons_frame.pack(fill=tk.X, pady=20)

    def update_stat():
        import random

        new_value = random.randint(100, 500)
        stat_card.set_value(new_value)

    def update_progress():
        import random

        new_progress = random.randint(20, 95)
        progress_card.set_progress(new_progress)

    tk.Button(
        buttons_frame,
        text="Actualizar Estadística",
        command=update_stat,
        font=("Arial", 10),
        bg="#4A90E2",
        fg="white",
        relief=tk.FLAT,
        padx=20,
        pady=5,
    ).pack(side=tk.LEFT, padx=10)

    tk.Button(
        buttons_frame,
        text="Actualizar Progreso",
        command=update_progress,
        font=("Arial", 10),
        bg="#28a745",
        fg="white",
        relief=tk.FLAT,
        padx=20,
        pady=5,
    ).pack(side=tk.LEFT, padx=10)

    root.mainloop()
