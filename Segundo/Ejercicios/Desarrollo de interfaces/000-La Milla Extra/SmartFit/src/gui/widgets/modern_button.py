# modern_button.py - Widget de botón moderno para SmartFit
# Fran - Desarrollo de interfaces

import tkinter as tk
from tkinter import ttk
from typing import Callable, Optional


class ModernButton(tk.Button):
    """
    Botón moderno con estilo personalizado
    Extiende tk.Button con funcionalidades adicionales y diseño moderno
    """

    def __init__(
        self,
        parent,
        text: str = "",
        command: Optional[Callable] = None,
        style_type: str = "default",
        icon: str = "",
        bg_color: str = "#4A90E2",
        fg_color: str = "white",
        hover_color: str = "#357ABD",
        active_color: str = "#2E5984",
        font: tuple = ("Arial", 10, "bold"),
        width: int = 15,
        height: int = 1,
        **kwargs,
    ):
        """
        Inicializa el botón moderno

        Args:
            parent: Widget padre
            text: Texto del botón
            command: Función a ejecutar
            style_type: Tipo de estilo (default, primary, secondary, success, danger, warning)
            icon: Emoji o icono
            bg_color: Color de fondo
            fg_color: Color del texto
            hover_color: Color en hover
            active_color: Color activo
            font: Fuente del texto
            width: Ancho del botón
            height: Alto del botón
        """
        # Configurar estilo según el tipo
        if style_type != "default":
            bg_color, fg_color, hover_color, active_color = self._get_style_colors(
                style_type
            )

        # Formatear texto con icono
        if icon:
            formatted_text = f"{icon} {text}"
        else:
            formatted_text = text

        # Configurar opciones del botón
        button_options = {
            "text": formatted_text,
            "command": command,
            "bg": bg_color,
            "fg": fg_color,
            "activebackground": active_color,
            "activeforeground": fg_color,
            "font": font,
            "width": width,
            "height": height,
            "relief": tk.FLAT,
            "bd": 0,
            "cursor": "hand2",
            "padx": 10,
            "pady": 5,
        }

        # Actualizar con opciones proporcionadas
        button_options.update(kwargs)

        super().__init__(parent, **button_options)

        # Configurar colores para hover
        self.bg_color = bg_color
        self.hover_color = hover_color
        self.active_color = active_color
        self.original_bg = bg_color

        # Configurar eventos
        self.bind_events()

    def _get_style_colors(self, style_type: str) -> tuple:
        """Obtiene los colores según el tipo de estilo"""
        styles = {
            "primary": ("#4A90E2", "white", "#357ABD", "#2E5984"),
            "secondary": ("#6C757D", "white", "#5A6268", "#4E555B"),
            "success": ("#28A745", "white", "#218838", "#1E7E34"),
            "danger": ("#DC3545", "white", "#C82333", "#BD2130"),
            "warning": ("#FFC107", "black", "#E0A800", "#D39E00"),
            "info": ("#17A2B8", "white", "#138496", "#117A8B"),
            "light": ("#F8F9FA", "#212529", "#E2E6EA", "#D6D8DB"),
            "dark": ("#343A40", "white", "#2C3136", "#252B30"),
        }
        return styles.get(style_type, styles["primary"])

    def bind_events(self):
        """Configura los eventos del botón"""
        self.bind("<Enter>", self.on_enter)
        self.bind("<Leave>", self.on_leave)
        self.bind("<Button-1>", self.on_click)
        self.bind("<ButtonRelease-1>", self.on_release)

    def on_enter(self, event):
        """Maneja el evento de mouse sobre el botón"""
        self.config(bg=self.hover_color)

    def on_leave(self, event):
        """Maneja el evento de mouse fuera del botón"""
        self.config(bg=self.original_bg)

    def on_click(self, event):
        """Maneja el evento de click en el botón"""
        self.config(bg=self.active_color)

    def on_release(self, event):
        """Maneja el evento de liberación del botón"""
        self.config(bg=self.hover_color)

    def set_text(self, text: str, icon: str = ""):
        """Actualiza el texto del botón"""
        if icon:
            formatted_text = f"{icon} {text}"
        else:
            formatted_text = text
        self.config(text=formatted_text)

    def set_command(self, command: Callable):
        """Actualiza la función del botón"""
        self.config(command=command)

    def set_style(self, style_type: str):
        """Cambia el estilo del botón"""
        bg_color, fg_color, hover_color, active_color = self._get_style_colors(
            style_type
        )

        self.bg_color = bg_color
        self.hover_color = hover_color
        self.active_color = active_color
        self.original_bg = bg_color

        self.config(
            bg=bg_color,
            fg=fg_color,
            activebackground=active_color,
            activeforeground=fg_color,
        )

    def set_colors(
        self,
        bg_color: str,
        fg_color: str = "white",
        hover_color: str = None,
        active_color: str = None,
    ):
        """Configura colores personalizados"""
        if hover_color is None:
            hover_color = bg_color
        if active_color is None:
            active_color = bg_color

        self.bg_color = bg_color
        self.hover_color = hover_color
        self.active_color = active_color
        self.original_bg = bg_color

        self.config(
            bg=bg_color,
            fg=fg_color,
            activebackground=active_color,
            activeforeground=fg_color,
        )

    def disable(self):
        """Deshabilita el botón"""
        self.config(state="disabled", cursor="not_allowed")

    def enable(self):
        """Habilita el botón"""
        self.config(state="normal", cursor="hand2")

    def flash(self, color: str = "white", duration: int = 100):
        """Hace que el botón parpadee"""
        original_bg = self.cget("bg")
        self.config(bg=color)
        self.after(duration, lambda: self.config(bg=original_bg))

    def pulse(self, intensity: float = 0.1, duration: int = 200):
        """Efecto de pulso en el botón"""
        # Implementación básica del efecto pulso
        self.flash(self.hover_color, duration)
        self.after(duration, lambda: self.config(bg=self.bg_color))


class IconButton(ModernButton):
    """
    Botón especializado solo para iconos
    Extiende ModernButton para casos de uso de solo iconos
    """

    def __init__(
        self,
        parent,
        icon: str,
        command: Optional[Callable] = None,
        size: int = 30,
        style_type: str = "default",
        **kwargs,
    ):
        """
        Inicializa el botón de icono

        Args:
            parent: Widget padre
            icon: Emoji o icono
            command: Función a ejecutar
            size: Tamaño del botón (cuadrado)
            style_type: Tipo de estilo
            **kwargs: Argumentos adicionales
        """
        # Calcular tamaño del texto basado en el tamaño del botón
        font_size = max(8, size // 3)
        font = ("Arial", font_size, "normal")

        super().__init__(
            parent,
            text=icon,
            command=command,
            style_type=style_type,
            font=font,
            width=size // 10,
            height=size // 20,
            **kwargs,
        )

    def set_icon(self, icon: str):
        """Actualiza el icono del botón"""
        current_font = self.cget("font")
        font_size = current_font[1] if len(current_font) > 1 else 12
        font = ("Arial", font_size, "normal")
        self.config(text=icon, font=font)


class ToggleButton(ModernButton):
    """
    Botón de alternancia (toggle)
    Tiene dos estados: activo e inactivo
    """

    def __init__(
        self,
        parent,
        text_on: str = "",
        text_off: str = "",
        icon_on: str = "✓",
        icon_off: str = "",
        command: Optional[Callable] = None,
        style_on: str = "success",
        style_off: str = "secondary",
        initial_state: bool = False,
        **kwargs,
    ):
        """
        Inicializa el botón de alternancia

        Args:
            parent: Widget padre
            text_on: Texto cuando está activo
            text_off: Texto cuando está inactivo
            icon_on: Icono cuando está activo
            icon_off: Icono cuando está inactivo
            command: Función a ejecutar
            style_on: Estilo cuando está activo
            style_off: Estilo cuando está inactivo
            initial_state: Estado inicial
            **kwargs: Argumentos adicionales
        """
        self.text_on = text_on
        self.text_off = text_off
        self.icon_on = icon_on
        self.icon_off = icon_off
        self.style_on = style_on
        self.style_off = style_off
        self.is_on = initial_state

        # Texto inicial
        if initial_state:
            initial_text = f"{icon_on} {text_on}" if icon_on else text_on
        else:
            initial_text = f"{icon_off} {text_off}" if icon_off else text_off

        super().__init__(parent, text=initial_text, command=self.toggle, **kwargs)

        # Configurar estilo inicial
        self.set_state(initial_state)

    def toggle(self):
        """Alterna el estado del botón"""
        self.is_on = not self.is_on
        self.set_state(self.is_on)

        # Ejecutar comando si existe
        if self.cget("command"):
            self.cget("command")(self.is_on)

    def set_state(self, is_on: bool):
        """Establece el estado del botón"""
        self.is_on = is_on

        if is_on:
            # Estado activo
            text = f"{self.icon_on} {self.text_on}" if self.icon_on else self.text_on
            self.set_style(self.style_on)
        else:
            # Estado inactivo
            text = (
                f"{self.icon_off} {self.text_off}" if self.icon_off else self.text_off
            )
            self.set_style(self.style_off)

        self.config(text=text)

    def set_on(self):
        """Activa el botón"""
        self.set_state(True)

    def set_off(self):
        """Desactiva el botón"""
        self.set_state(False)

    def is_active(self) -> bool:
        """Verifica si el botón está activo"""
        return self.is_on


# Función de utilidad para crear botones rápidamente
def create_modern_button(
    parent,
    text: str = "",
    command: Optional[Callable] = None,
    style: str = "primary",
    icon: str = "",
    **kwargs,
) -> ModernButton:
    """
    Función de utilidad para crear un botón moderno

    Args:
        parent: Widget padre
        text: Texto del botón
        command: Función a ejecutar
        style: Estilo del botón
        icon: Icono del botón
        **kwargs: Argumentos adicionales

    Returns:
        Instancia de ModernButton
    """
    return ModernButton(
        parent, text=text, command=command, style_type=style, icon=icon, **kwargs
    )


def create_icon_button(
    parent,
    icon: str,
    command: Optional[Callable] = None,
    size: int = 30,
    style: str = "default",
    **kwargs,
) -> IconButton:
    """
    Función de utilidad para crear un botón de icono

    Args:
        parent: Widget padre
        icon: Icono del botón
        command: Función a ejecutar
        size: Tamaño del botón
        style: Estilo del botón
        **kwargs: Argumentos adicionales

    Returns:
        Instancia de IconButton
    """
    return IconButton(
        parent, icon=icon, command=command, size=size, style_type=style, **kwargs
    )


def create_toggle_button(
    parent,
    text_on: str = "",
    text_off: str = "",
    command: Optional[Callable] = None,
    **kwargs,
) -> ToggleButton:
    """
    Función de utilidad para crear un botón de alternancia

    Args:
        parent: Widget padre
        text_on: Texto cuando está activo
        text_off: Texto cuando está inactivo
        command: Función a ejecutar
        **kwargs: Argumentos adicionales

    Returns:
        Instancia de ToggleButton
    """
    return ToggleButton(
        parent, text_on=text_on, text_off=text_off, command=command, **kwargs
    )
