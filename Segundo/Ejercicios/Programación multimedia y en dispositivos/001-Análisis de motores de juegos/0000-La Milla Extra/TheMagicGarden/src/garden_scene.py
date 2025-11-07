# GardenScene - Clase que gestiona la escena principal del jardín
# Autor: Fran
# Esta clase controla todos los elementos del jardín

import math
import random

import pygame
from butterfly import Butterfly
from flower import Flower
from light_effect import LightEffect


class GardenScene:
    """Clase principal que gestiona toda la escena del jardín mágico"""

    def __init__(self, screen, audio_manager):
        self.screen = screen
        self.audio_manager = audio_manager
        self.width = screen.get_width()
        self.height = screen.get_height()

        # Variables para controlar el día y la noche
        self.is_day = True
        self.day_color = (135, 206, 235)  # Azul cielo
        self.night_color = (25, 25, 112)  # Azul oscuro

        # Creo las flores todas en el nivel del suelo
        self.flowers = []
        ground_level = int(self.height * 0.7)  # Nivel del suelo
        for i in range(8):
            x = random.randint(100, self.width - 100)
            y = ground_level  # Todas las flores en el mismo nivel Y
            color = (
                random.randint(200, 255),
                random.randint(100, 200),
                random.randint(150, 255),
            )
            self.flowers.append(Flower(x, y, color))

        # Creo las mariposas
        self.butterflies = []
        for i in range(3):
            self.butterflies.append(Butterfly(self.width, self.height))

        # Creo efectos de luz (reducido para no saturar la pantalla)
        self.light_effects = []
        for i in range(2):
            self.light_effects.append(LightEffect(self.width, self.height))

        # Inicio la música de fondo
        self.audio_manager.play_background_music()

        # Variable para el tiempo transcurrido
        self.time_elapsed = 0

    def update(self):
        """Actualizo todos los elementos de la escena"""
        self.time_elapsed += 1

        # Actualizo las flores
        for flower in self.flowers:
            flower.update()

        # Actualizo las mariposas
        for butterfly in self.butterflies:
            butterfly.update()

        # Actualizo los efectos de luz
        for light in self.light_effects:
            light.update()

    def draw(self):
        """Dibujo toda la escena"""
        # Pinto el fondo según si es día o noche
        if self.is_day:
            self.screen.fill(self.day_color)
        else:
            self.screen.fill(self.night_color)

        # Dibujo el suelo
        ground_color = (34, 139, 34) if self.is_day else (0, 100, 0)
        pygame.draw.rect(
            self.screen,
            ground_color,
            (0, self.height * 0.7, self.width, self.height * 0.3),
        )

        # Dibujo los efectos de luz
        for light in self.light_effects:
            light.draw(self.screen)

        # Dibujo las flores
        for flower in self.flowers:
            flower.draw(self.screen)

        # Dibujo las mariposas
        for butterfly in self.butterflies:
            butterfly.draw(self.screen)

        # Dibujo información en pantalla
        self.draw_ui()

    def draw_ui(self):
        """Dibujo la interfaz de usuario"""
        font = pygame.font.Font(None, 36)

        # Muestro si es día o noche
        time_text = "DÍA" if self.is_day else "NOCHE"
        text = font.render(time_text, True, (255, 255, 255))
        self.screen.blit(text, (10, 10))

        # Muestro instrucciones
        font_small = pygame.font.Font(None, 24)
        instructions = [
            "ESPACIO: Pausa",
            "D: Cambiar día/noche",
            "R: Reiniciar",
            "Clic: Interactuar con flores",
            "ESC: Salir",
        ]

        y_pos = 50
        for instruction in instructions:
            text = font_small.render(instruction, True, (255, 255, 255))
            self.screen.blit(text, (10, y_pos))
            y_pos += 25

    def handle_click(self, pos):
        """Manejo los clics del ratón"""
        for flower in self.flowers:
            if flower.check_click(pos):
                self.audio_manager.play_click_sound()
                flower.grow()

    def toggle_day_night(self):
        """Cambio entre día y noche"""
        self.is_day = not self.is_day
        self.audio_manager.toggle_day_night_music()

    def reset(self):
        """Reinicio la escena"""
        # Reinicio las flores
        for flower in self.flowers:
            flower.reset()

        # Cambio posiciones de las mariposas
        for butterfly in self.butterflies:
            butterfly.randomize_position()
