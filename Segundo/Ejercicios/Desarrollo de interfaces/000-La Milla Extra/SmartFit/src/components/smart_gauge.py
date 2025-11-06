# smart_gauge.py - Componente visual SmartGauge personalizado para SmartFit
# Fran - Desarrollo de interfaces

import json
import math
import os
import tkinter as tk
from tkinter import ttk
from typing import Any, Callable, Dict, Optional


class SmartGauge(ttk.Frame):
    """
    Componente visual personalizado SmartGauge
    Un dial circular para mostrar progreso de calorías, repeticiones, etc.
    """

    def __init__(
        self,
        parent,
        max_value: float = 100,
        current_value: float = 0,
        title: str = "Progreso",
        unit: str = "",
        color: str = "#4A90E2",
        size: int = 200,
        **kwargs,
    ):
        """
        Inicializa el SmartGauge

        Args:
            parent: Widget padre
            max_value: Valor máximo del gauge
            current_value: Valor actual
            title: Título del gauge
            unit: Unidad de medida
            color: Color del gauge
            size: Tamaño del gauge en píxeles
        """
        super().__init__(parent, **kwargs)

        # Propiedades del gauge
        self.max_value = float(max_value)
        self.current_value = float(current_value)
        self.title = title
        self.unit = unit
        self.color = color
        self.size = size

        # Callbacks de eventos
        self.on_click_callback: Optional[Callable] = None
        self.on_value_change_callback: Optional[Callable] = None

        # Archivo de persistencia
        self.state_file = f"gauge_{self.title.lower().replace(' ', '_')}.json"

        # Configurar canvas
        self.setup_canvas()

        # Cargar estado guardado
        self.load_state()

        # Dibujar el gauge inicial
        self.draw_gauge()

    def setup_canvas(self):
        """Configura el canvas del gauge"""
        # Canvas para dibujar el gauge
        self.canvas = tk.Canvas(
            self, width=self.size, height=self.size, bg="white", highlightthickness=0
        )
        self.canvas.pack()

        # Vincular eventos
        self.canvas.bind("<Button-1>", self._on_click)

    def draw_gauge(self):
        """Dibuja el gauge completo"""
        # Limpiar canvas
        self.canvas.delete("all")

        # Calcular ángulos y valores
        center_x = self.size // 2
        center_y = self.size // 2
        radius = (self.size - 40) // 2

        # Calcular ángulo basado en el valor actual
        if self.max_value > 0:
            progress = min(self.current_value / self.max_value, 1.0)
        else:
            progress = 0.0

        # Ángulos (de -120° a 120° = 240° total)
        start_angle = math.radians(120)
        end_angle = math.radians(120 - (240 * progress))

        # Dibujar fondo del gauge (círculo completo)
        self.canvas.create_arc(
            center_x - radius,
            center_y - radius,
            center_x + radius,
            center_y + radius,
            start=120,
            extent=240,
            style="arc",
            outline="#E0E0E0",
            width=15,
        )

        # Dibujar progreso (arco coloreado)
        if progress > 0:
            self.canvas.create_arc(
                center_x - radius,
                center_y - radius,
                center_x + radius,
                center_y + radius,
                start=120,
                extent=-(240 * progress),
                style="arc",
                outline=self.color,
                width=15,
            )

        # Añadir efecto de gradiente simulando con múltiples arcos
        if progress > 0:
            for i in range(5):
                alpha = (5 - i) / 5
                alpha_color = self._adjust_color_brightness(self.color, 1 - alpha * 0.3)
                extent = -(240 * progress * (1 - i * 0.1))
                if extent < 0:
                    self.canvas.create_arc(
                        center_x - radius + i,
                        center_y - radius + i,
                        center_x + radius - i,
                        center_y + radius - i,
                        start=120,
                        extent=extent,
                        style="arc",
                        outline=alpha_color,
                        width=10,
                    )

        # Dibujar marca central
        self.canvas.create_oval(
            center_x - 30,
            center_y - 30,
            center_x + 30,
            center_y + 30,
            fill="white",
            outline=self.color,
            width=3,
        )

        # Texto del valor actual en el centro
        value_text = f"{self.current_value:.0f}"
        if self.unit:
            value_text += f"\n{self.unit}"

        self.canvas.create_text(
            center_x,
            center_y,
            text=value_text,
            font=("Arial", 14, "bold"),
            fill=self.color,
            justify="center",
        )

        # Título del gauge
        self.canvas.create_text(
            center_x,
            center_y - radius - 20,
            text=self.title,
            font=("Arial", 12, "bold"),
            fill="#333333",
            justify="center",
        )

        # Texto de progreso (% completado)
        progress_text = f"{progress * 100:.1f}%"
        self.canvas.create_text(
            center_x,
            center_y + radius + 20,
            text=progress_text,
            font=("Arial", 10),
            fill="#666666",
            justify="center",
        )

        # Marcas de escala (opcional)
        self._draw_scale_marks(center_x, center_y, radius)

    def _draw_scale_marks(self, center_x, center_y, radius):
        """Dibuja las marcas de escala del gauge"""
        mark_count = 10

        for i in range(mark_count + 1):
            # Calcular ángulo de la marca
            angle = math.radians(120 - (240 * i / mark_count))

            # Calcular posición
            start_x = center_x + (radius - 25) * math.cos(angle)
            start_y = center_y - (radius - 25) * math.sin(angle)
            end_x = center_x + (radius - 15) * math.cos(angle)
            end_y = center_y - (radius - 15) * math.sin(angle)

            # Dibujar marca
            self.canvas.create_line(
                start_x, start_y, end_x, end_y, fill="#CCCCCC", width=2
            )

            # Añadir valor numérico cada 2 marcas
            if i % 2 == 0 and i <= mark_count:
                value = self.max_value * i / mark_count
                text_x = center_x + (radius - 35) * math.cos(angle)
                text_y = center_y - (radius - 35) * math.sin(angle)

                self.canvas.create_text(
                    text_x,
                    text_y,
                    text=f"{value:.0f}",
                    font=("Arial", 8),
                    fill="#999999",
                    justify="center",
                )

    def set_value(self, new_value: float, animate: bool = True):
        """
        Establece un nuevo valor para el gauge

        Args:
            new_value: Nuevo valor
            animate: Si animar el cambio de valor
        """
        old_value = self.current_value
        self.current_value = max(0, min(new_value, self.max_value))

        # Llamar callback si existe
        if self.on_value_change_callback:
            self.on_value_change_callback(old_value, self.current_value)

        if animate:
            # Animación simple del cambio de valor
            steps = 20
            step_value = (self.current_value - old_value) / steps

            def animate_step(step=0):
                if step < steps:
                    temp_value = old_value + (step_value * (step + 1))
                    temp_value = max(0, min(temp_value, self.max_value))

                    # Actualizar solo el valor temporal
                    self._animate_to_value(temp_value, animate_step, step + 1)
                else:
                    self.draw_gauge()
                    self.save_state()

            self._animate_to_value(old_value + step_value, animate_step, 1)
        else:
            self.draw_gauge()
            self.save_state()

    def _animate_to_value(self, target_value: float, callback: Callable, step: int):
        """Ayuda en la animación del gauge"""
        self.current_value = target_value
        self.draw_gauge()
        self.after(20, lambda: callback(step))

    def get_value(self) -> float:
        """Obtiene el valor actual del gauge"""
        return self.current_value

    def get_percentage(self) -> float:
        """Obtiene el porcentaje de progreso"""
        if self.max_value == 0:
            return 0.0
        return min((self.current_value / self.max_value) * 100, 100.0)

    def reset(self):
        """Resetea el gauge a cero"""
        self.set_value(0)

    def set_color(self, color: str):
        """Cambia el color del gauge"""
        self.color = color
        self.draw_gauge()

    def set_title(self, title: str):
        """Cambia el título del gauge"""
        self.title = title
        self.draw_gauge()

    def set_max_value(self, max_value: float):
        """Cambia el valor máximo del gauge"""
        self.max_value = max(1, max_value)
        self.draw_gauge()

    def bind_click(self, callback: Callable):
        """Vincula un callback al evento de clic"""
        self.on_click_callback = callback

    def bind_value_change(self, callback: Callable):
        """Vincula un callback al cambio de valor"""
        self.on_value_change_callback = callback

    def _on_click(self, event):
        """Maneja el evento de clic"""
        if self.on_click_callback:
            self.on_click_callback(event, self.current_value)

    def save_state(self):
        """Guarda el estado del gauge en archivo JSON"""
        try:
            state = {
                "current_value": self.current_value,
                "max_value": self.max_value,
                "title": self.title,
                "unit": self.unit,
                "color": self.color,
                "size": self.size,
            }

            with open(self.state_file, "w", encoding="utf-8") as f:
                json.dump(state, f, indent=2)

        except Exception as e:
            print(f"Error al guardar estado del gauge: {e}")

    def load_state(self):
        """Carga el estado del gauge desde archivo JSON"""
        try:
            if os.path.exists(self.state_file):
                with open(self.state_file, "r", encoding="utf-8") as f:
                    state = json.load(f)

                self.current_value = state.get("current_value", self.current_value)
                self.max_value = state.get("max_value", self.max_value)
                self.title = state.get("title", self.title)
                self.unit = state.get("unit", self.unit)
                self.color = state.get("color", self.color)
                self.size = state.get("size", self.size)

                print(f"Estado del gauge '{self.title}' cargado correctamente")

        except Exception as e:
            print(f"Error al cargar estado del gauge: {e}")

    def export_data(self) -> Dict[str, Any]:
        """Exporta los datos del gauge"""
        return {
            "title": self.title,
            "current_value": self.current_value,
            "max_value": self.max_value,
            "percentage": self.get_percentage(),
            "unit": self.unit,
            "color": self.color,
            "size": self.size,
        }

    def _adjust_color_brightness(self, hex_color: str, factor: float) -> str:
        """
        Ajusta el brillo de un color hexadecimal

        Args:
            hex_color: Color en formato #RRGGBB
            factor: Factor de ajuste (0.0 a 2.0)

        Returns:
            Color ajustado en formato #RRGGBB
        """
        try:
            # Remover # si existe
            hex_color = hex_color.lstrip("#")

            # Convertir a RGB
            r = int(hex_color[0:2], 16)
            g = int(hex_color[2:4], 16)
            b = int(hex_color[4:6], 16)

            # Ajustar brillo
            r = min(int(r * factor), 255)
            g = min(int(g * factor), 255)
            b = min(int(b * factor), 255)

            # Convertir de vuelta a hexadecimal
            return f"#{r:02x}{g:02x}{b:02x}"

        except:
            return hex_color

    def flash_color(self, flash_color: str = "#FF6B6B", duration: int = 500):
        """
        Hace parpadear el gauge con un color específico

        Args:
            flash_color: Color para el parpadeo
            duration: Duración del parpadeo en milisegundos
        """
        original_color = self.color

        # Cambiar a color de parpadeo
        self.set_color(flash_color)

        # Volver al color original
        self.after(duration, lambda: self.set_color(original_color))

    def pulse(self, pulses: int = 3, interval: int = 200):
        """
        Hace pulsar el gauge

        Args:
            pulses: Número de pulsos
            interval: Intervalo entre pulsos en milisegundos
        """
        if pulses <= 0:
            return

        # Expandir ligeramente
        self.canvas.scale("all", self.size // 2, self.size // 2, 1.1, 1.1)

        # Volver al tamaño normal después del intervalo
        self.after(interval, lambda: self._pulse_restore(pulses - 1, interval))

    def _pulse_restore(self, pulses: int, interval: int):
        """Restaura el tamaño normal después del pulso"""
        self.canvas.scale("all", self.size // 2, self.size // 2, 1 / 1.1, 1 / 1.1)

        if pulses > 0:
            self.pulse(pulses, interval)

    def set_warning_threshold(self, threshold: float, warning_color: str = "#FF9800"):
        """
        Establece un umbral de advertencia

        Args:
            threshold: Umbral de porcentaje (0-100)
            warning_color: Color para mostrar la advertencia
        """
        if self.get_percentage() >= threshold:
            self.flash_color(warning_color, 1000)

    def set_critical_threshold(self, threshold: float, critical_color: str = "#F44336"):
        """
        Establece un umbral crítico

        Args:
            threshold: Umbral de porcentaje (0-100)
            critical_color: Color para mostrar el estado crítico
        """
        if self.get_percentage() >= threshold:
            self.flash_color(critical_color, 500)


# Ejemplo de uso y prueba
if __name__ == "__main__":
    import time

    # Crear ventana de prueba
    root = tk.Tk()
    root.title("SmartGauge - Prueba de Componente")
    root.geometry("600x400")

    # Crear gauge de prueba
    gauge = SmartGauge(
        root,
        max_value=2000,
        current_value=1500,
        title="Calorías Quemadas",
        unit="cal",
        color="#4CAF50",
        size=250,
    )

    gauge.pack(pady=20)

    # Callback de ejemplo
    def on_gauge_click(event, value):
        print(f"Gauge clickeado! Valor actual: {value}")

    def on_value_change(old_value, new_value):
        print(f"Valor cambiado de {old_value:.0f} a {new_value:.0f}")

    gauge.bind_click(on_gauge_click)
    gauge.bind_value_change(on_value_change)

    # Botones de prueba
    button_frame = ttk.Frame(root)
    button_frame.pack(pady=10)

    ttk.Button(
        button_frame,
        text="Aumentar 100",
        command=lambda: gauge.set_value(gauge.get_value() + 100),
    ).pack(side=tk.LEFT, padx=5)

    ttk.Button(
        button_frame,
        text="Disminuir 100",
        command=lambda: gauge.set_value(gauge.get_value() - 100),
    ).pack(side=tk.LEFT, padx=5)

    ttk.Button(button_frame, text="Reset", command=gauge.reset).pack(
        side=tk.LEFT, padx=5
    )

    ttk.Button(
        button_frame, text="Flash", command=lambda: gauge.flash_color("#FF5722")
    ).pack(side=tk.LEFT, padx=5)

    ttk.Button(button_frame, text="Pulse", command=lambda: gauge.pulse(2, 300)).pack(
        side=tk.LEFT, padx=5
    )

    # Probar cambios automáticos
    def auto_test():
        for i in range(5):
            time.sleep(1)
            gauge.set_value(gauge.get_value() + 200)

        # Finalizar con valor completo
        gauge.set_value(gauge.max_value)

    ttk.Button(button_frame, text="Test Automático", command=auto_test).pack(
        side=tk.LEFT, padx=5
    )

    root.mainloop()
