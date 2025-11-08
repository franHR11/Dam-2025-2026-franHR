# modern_frame.py - Widget de marco moderno para SmartFit
# Fran - Desarrollo de interfaces

import tkinter as tk
from tkinter import ttk
from typing import Callable, Optional


class ModernFrame(tk.Frame):
    """
    Marco moderno con estilo personalizado y efectos visuales
    Extiende tk.Frame con funcionalidades avanzadas
    """

    def __init__(
        self,
        parent,
        bg_color: str = "#f8f9fa",
        border_color: str = "#dee2e6",
        border_width: int = 1,
        corner_radius: int = 0,
        padding: int = 0,
        hover_effect: bool = False,
        clickable: bool = False,
        shadow: bool = False,
        **kwargs,
    ):
        """
        Inicializa el marco moderno

        Args:
            parent: Widget padre
            bg_color: Color de fondo
            border_color: Color del borde
            border_width: Ancho del borde
            corner_radius: Radio de las esquinas (para diseño redondeado)
            padding: Padding interno
            hover_effect: Si debe tener efecto hover
            clickable: Si debe ser clickeable
            shadow: Si debe tener sombra
        """
        # Configurar frame base
        frame_options = {
            "bg": bg_color,
            "relief": tk.RAISED if border_width > 0 else tk.FLAT,
            "bd": border_width,
        }
        frame_options.update(kwargs)

        super().__init__(parent, **frame_options)

        # Configuración visual
        self.bg_color = bg_color
        self.border_color = border_color
        self.border_width = border_width
        self.corner_radius = corner_radius
        self.padding = padding
        self.hover_effect = hover_effect
        self.clickable = clickable
        self.shadow = shadow

        # Estado
        self._is_hover = False
        self._is_pressed = False
        self._original_bg = bg_color
        self._hover_bg = self._lighten_color(bg_color, 0.1)
        self._active_bg = self._darken_color(bg_color, 0.1)

        # Callbacks
        self._click_callback = None
        self._hover_callback = None

        # Configurar eventos
        self._setup_events()

        # Crear efectos visuales
        if self.shadow:
            self._create_shadow()

        # Aplicar padding
        if self.padding > 0:
            self._apply_padding()

    def _setup_events(self):
        """Configura los eventos del marco"""
        if self.hover_effect or self.clickable:
            self.bind("<Enter>", self._on_enter)
            self.bind("<Leave>", self._on_leave)

        if self.clickable:
            self.bind("<Button-1>", self._on_press)
            self.bind("<ButtonRelease-1>", self._on_release)
            self.config(cursor="hand2")

    def _on_enter(self, event):
        """Maneja el evento de entrada del mouse"""
        if self.hover_effect:
            self._is_hover = True
            self._update_appearance()

        if self._hover_callback:
            self._hover_callback("enter")

    def _on_leave(self, event):
        """Maneja el evento de salida del mouse"""
        self._is_hover = False
        self._is_pressed = False
        self._update_appearance()

        if self._hover_callback:
            self._hover_callback("leave")

    def _on_press(self, event):
        """Maneja el evento de press del mouse"""
        if self.clickable:
            self._is_pressed = True
            self._update_appearance()

            if self._click_callback:
                self._click_callback()

    def _on_release(self, event):
        """Maneja el evento de release del mouse"""
        if self.clickable:
            self._is_pressed = False
            self._update_appearance()

    def _update_appearance(self):
        """Actualiza la apariencia según el estado"""
        if self._is_pressed:
            self.config(bg=self._active_bg, relief=tk.SUNKEN, bd=self.border_width + 1)
        elif self._is_hover:
            self.config(bg=self._hover_bg, relief=tk.RAISED, bd=self.border_width)
        else:
            self.config(
                bg=self.bg_color,
                relief=tk.RAISED if self.border_width > 0 else tk.FLAT,
                bd=self.border_width,
            )

    def _lighten_color(self, color: str, factor: float) -> str:
        """Aclara un color"""
        try:
            # Convertir hex a RGB
            hex_color = color.lstrip("#")
            r = int(hex_color[0:2], 16)
            g = int(hex_color[2:4], 16)
            b = int(hex_color[4:6], 16)

            # Aclarar
            r = int(r + (255 - r) * factor)
            g = int(g + (255 - g) * factor)
            b = int(b + (255 - b) * factor)

            return f"#{r:02x}{g:02x}{b:02x}"
        except:
            return color

    def _darken_color(self, color: str, factor: float) -> str:
        """Oscurece un color"""
        try:
            # Convertir hex a RGB
            hex_color = color.lstrip("#")
            r = int(hex_color[0:2], 16)
            g = int(hex_color[2:4], 16)
            b = int(hex_color[4:6], 16)

            # Oscurecer
            r = int(r * (1 - factor))
            g = int(g * (1 - factor))
            b = int(b * (1 - factor))

            return f"#{r:02x}{g:02x}{b:02x}"
        except:
            return color

    def _create_shadow(self):
        """Crea un efecto de sombra (implementación básica)"""
        # En una implementación más avanzada, se crearían múltiples frames para simular sombra
        # Por ahora, cambiamos el color del borde para simular sombra
        shadow_color = self._darken_color(self.bg_color, 0.3)
        self.config(relief=tk.RAISED, bd=2)

    def _apply_padding(self):
        """Aplica padding al marco"""
        # Crear frame interno para padding
        self._padding_frame = tk.Frame(self, bg=self.bg_color)
        self._padding_frame.pack(
            fill=tk.BOTH, expand=True, padx=self.padding, pady=self.padding
        )

    def configure_colors(self, bg_color: str = None, border_color: str = None):
        """Configura los colores del marco"""
        if bg_color:
            self.bg_color = bg_color
            self._original_bg = bg_color
            self._hover_bg = self._lighten_color(bg_color, 0.1)
            self._active_bg = self._darken_color(bg_color, 0.1)

        if border_color:
            self.border_color = border_color

        self._update_appearance()

    def set_click_callback(self, callback: Callable):
        """Establece el callback para clicks"""
        self._click_callback = callback
        if not self.clickable:
            self.clickable = True
            self._setup_events()

    def set_hover_callback(self, callback: Callable):
        """Establece el callback para hover"""
        self._hover_callback = callback
        if not self.hover_effect:
            self.hover_effect = True
            self._setup_events()

    def set_enabled(self, enabled: bool):
        """Habilita/deshabilita el marco"""
        if enabled:
            self.config(state="normal")
            if self.clickable:
                self.config(cursor="hand2")
        else:
            self.config(state="disabled", cursor="arrow")
            # Cambiar a colores deshabilitados
            disabled_color = self._lighten_color(self.bg_color, 0.3)
            self.config(bg=disabled_color)

    def add_border(self, width: int = 2, color: str = "#000000", style: str = "raised"):
        """Añade un borde al marco"""
        relief_map = {
            "raised": tk.RAISED,
            "sunken": tk.SUNKEN,
            "flat": tk.FLAT,
            "ridge": tk.RIDGE,
            "groove": tk.GROOVE,
            "solid": tk.SOLID,
        }

        relief = relief_map.get(style, tk.RAISED)
        self.config(relief=relief, bd=width)

    def remove_border(self):
        """Quita el borde del marco"""
        self.config(relief=tk.FLAT, bd=0)

    def set_padding(self, padding: int):
        """Establece el padding del marco"""
        self.padding = padding
        if hasattr(self, "_padding_frame"):
            self._padding_frame.pack_forget()
        self._apply_padding()

    def get_inner_frame(self) -> Optional[tk.Frame]:
        """Obtiene el frame interno si existe padding"""
        return getattr(self, "_padding_frame", None)

    def animate_color_change(self, new_bg_color: str, duration: int = 500):
        """Anima el cambio de color"""
        import math

        # Obtener colores RGB actuales
        def hex_to_rgb(hex_color):
            hex_color = hex_color.lstrip("#")
            return tuple(int(hex_color[i : i + 2], 16) for i in (0, 2, 4))

        def rgb_to_hex(rgb):
            return f"#{int(rgb[0]):02x}{int(rgb[1]):02x}{int(rgb[2]):02x}"

        def interpolate_color(color1, color2, factor):
            return tuple(
                int(color1[i] + (color2[i] - color1[i]) * factor) for i in range(3)
            )

        start_rgb = hex_to_rgb(self.bg_color)
        end_rgb = hex_to_rgb(new_bg_color)

        steps = 20
        step_duration = duration // steps

        def animate_step(step):
            if step <= steps:
                factor = step / steps
                current_color = interpolate_color(start_rgb, end_rgb, factor)
                current_hex = rgb_to_hex(current_color)
                self.config(bg=current_hex)
                self.after(step_duration, lambda: animate_step(step + 1))
            else:
                self.bg_color = new_bg_color

        animate_step(0)

    def flash(self, flash_color: str = "#ff6b6b", duration: int = 200, times: int = 2):
        """Hace que el marco parpadee"""
        original_color = self.bg_color

        def flash_once(count=0):
            if count < times * 2:
                if count % 2 == 0:
                    self.config(bg=flash_color)
                else:
                    self.config(bg=original_color)
                self.after(duration, lambda: flash_once(count + 1))
            else:
                self.config(bg=original_color)
                self.bg_color = original_color

        flash_once()


class PanelFrame(ModernFrame):
    """
    Panel especializado para contenido con estilo moderno
    Ideal para agrupar elementos relacionados
    """

    def __init__(
        self,
        parent,
        title: str = "",
        icon: str = "",
        title_bg: str = "#e9ecef",
        title_color: str = "#495057",
        content_bg: str = "#ffffff",
        **kwargs,
    ):
        """
        Inicializa el panel con título

        Args:
            parent: Widget padre
            title: Título del panel
            icon: Icono del título
            title_bg: Color de fondo del título
            title_color: Color del texto del título
            content_bg: Color de fondo del contenido
        """
        # Configurar estilos
        panel_bg = content_bg
        super().__init__(
            self, bg_color=panel_bg, border_color="#dee2e6", border_width=1, **kwargs
        )

        self.title = title
        self.icon = icon
        self.title_bg = title_bg
        self.title_color = title_color
        self.content_bg = content_bg

        # Crear estructura
        self._create_structure()

    def _create_structure(self):
        """Crea la estructura del panel"""
        # Frame de título (si hay título)
        if self.title or self.icon:
            self.title_frame = tk.Frame(self, bg=self.title_bg, height=30)
            self.title_frame.pack(fill=tk.X, padx=1, pady=(1, 0))
            self.title_frame.pack_propagate(False)

            # Contenido del título
            title_content = f"{self.icon} {self.title}" if self.icon else self.title
            self.title_label = tk.Label(
                self.title_frame,
                text=title_content,
                bg=self.title_bg,
                fg=self.title_color,
                font=("Arial", 10, "bold"),
                anchor="w",
            )
            self.title_label.pack(fill=tk.X, padx=10, pady=5)

        # Frame de contenido
        self.content_frame = tk.Frame(self, bg=self.content_bg)
        if self.title or self.icon:
            self.content_frame.pack(fill=tk.BOTH, expand=True, padx=1, pady=(0, 1))
        else:
            self.content_frame.pack(fill=tk.BOTH, expand=True, padx=1, pady=1)

    def set_title(self, title: str, icon: str = ""):
        """Establece el título del panel"""
        self.title = title
        self.icon = icon

        if hasattr(self, "title_label"):
            content = f"{self.icon} {self.title}" if self.icon else self.title
            self.title_label.config(text=content)

    def get_content_frame(self) -> tk.Frame:
        """Obtiene el frame de contenido para añadir widgets"""
        return self.content_frame

    def add_widget(self, widget, **pack_options):
        """Añade un widget al contenido del panel"""
        widget.pack(**pack_options)


class CardFrame(ModernFrame):
    """
    Tarjeta moderna para mostrar información
    Ideal para mostrar datos, estadísticas o contenido
    """

    def __init__(
        self, parent, card_type: str = "default", elevation: int = 2, **kwargs
    ):
        """
        Inicializa la tarjeta

        Args:
            parent: Widget padre
            card_type: Tipo de tarjeta ("default", "primary", "success", "warning", "danger")
            elevation: Nivel de elevación (afecta la sombra)
        """
        # Configurar colores según tipo
        type_styles = {
            "default": {"bg": "#ffffff", "border": "#dee2e6"},
            "primary": {"bg": "#e3f2fd", "border": "#2196f3"},
            "success": {"bg": "#e8f5e8", "border": "#4caf50"},
            "warning": {"bg": "#fff3e0", "border": "#ff9800"},
            "danger": {"bg": "#ffebee", "border": "#f44336"},
        }

        style = type_styles.get(card_type, type_styles["default"])

        super().__init__(
            parent,
            bg_color=style["bg"],
            border_color=style["border"],
            border_width=1,
            shadow=elevation > 0,
            **kwargs,
        )

        self.card_type = card_type
        self.elevation = elevation

    def add_header(self, title: str, icon: str = "", subtitle: str = ""):
        """Añade un encabezado a la tarjeta"""
        header_frame = tk.Frame(self, bg=self.bg_color, height=60)
        header_frame.pack(fill=tk.X, padx=10, pady=(10, 5))
        header_frame.pack_propagate(False)

        # Título principal
        if title:
            title_label = tk.Label(
                header_frame,
                text=title,
                bg=self.bg_color,
                font=("Arial", 12, "bold"),
                anchor="w",
            )
            title_label.pack(fill=tk.X)

        # Subtítulo e icono
        if subtitle or icon:
            content = f"{icon} {subtitle}" if icon and subtitle else (icon or subtitle)
            subtitle_label = tk.Label(
                header_frame,
                text=content,
                bg=self.bg_color,
                font=("Arial", 9),
                anchor="w",
                fg="#6c757d",
            )
            subtitle_label.pack(fill=tk.X, pady=(2, 0))

        # Separador
        separator = tk.Frame(self, bg=self.bg_color, height=1)
        separator.pack(fill=tk.X, padx=10, pady=(0, 5))
        separator_frame = tk.Frame(separator, bg="#dee2e6", height=1)
        separator_frame.pack(fill=tk.X)

    def add_content(self, **pack_options):
        """Añade un área de contenido"""
        content_frame = tk.Frame(self, bg=self.bg_color)
        content_frame.pack(
            fill=tk.BOTH, expand=True, padx=10, pady=(5, 10), **pack_options
        )
        return content_frame

    def add_footer(self, **pack_options):
        """Añade un pie de tarjeta"""
        footer_frame = tk.Frame(self, bg=self.bg_color, height=40)
        footer_frame.pack(fill=tk.X, side=tk.BOTTOM, padx=10, pady=(5, 10))
        footer_frame.pack_propagate(False)

        separator = tk.Frame(footer_frame, bg="#dee2e6", height=1)
        separator.pack(fill=tk.X, pady=(5, 0))

        content_frame = tk.Frame(footer_frame, bg=self.bg_color)
        content_frame.pack(fill=tk.BOTH, expand=True, pady=(5, 5))

        return content_frame


# Función de utilidad
def create_modern_frame(parent, **kwargs) -> ModernFrame:
    """Función de utilidad para crear un marco moderno"""
    return ModernFrame(parent, **kwargs)


def create_panel_frame(parent, **kwargs) -> PanelFrame:
    """Función de utilidad para crear un panel"""
    return PanelFrame(parent, **kwargs)


def create_card_frame(parent, **kwargs) -> CardFrame:
    """Función de utilidad para crear una tarjeta"""
    return CardFrame(parent, **kwargs)
