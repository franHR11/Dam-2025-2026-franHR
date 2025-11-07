# Butterfly - Clase que representa las mariposas voladoras
# Autor: Fran
# Las mariposas vuelan por el jardín con movimiento sinusoidal

import math
import random

import pygame


class Butterfly:
    """Clase que representa una mariposa animada"""

    def __init__(self, screen_width, screen_height):
        self.screen_width = screen_width
        self.screen_height = screen_height

        # Posición inicial
        self.x = random.randint(50, screen_width - 50)
        self.y = random.randint(100, screen_height - 200)

        # Velocidad y dirección
        self.vx = random.uniform(1, 3) * random.choice([-1, 1])
        self.vy = random.uniform(0.5, 2)

        # Parámetros para el movimiento sinusoidal
        self.wave_amplitude = random.uniform(20, 40)
        self.wave_frequency = random.uniform(0.05, 0.1)
        self.time = random.uniform(0, math.pi * 2)

        # Color aleatorio para la mariposa
        self.color = (
            random.randint(100, 255),
            random.randint(100, 255),
            random.randint(100, 255),
        )

        # Tamaño
        self.size = random.randint(15, 25)

        # Ángulo de rotación de las alas
        self.wing_angle = 0
        self.wing_speed = 0.3

    def update(self):
        """Actualizo la posición y animación de la mariposa"""
        # Actualizo el tiempo para el movimiento sinusoidal
        self.time += self.wave_frequency

        # Muevo la mariposa
        self.x += self.vx
        self.y += math.sin(self.time) * self.wave_amplitude * 0.1

        # Animación de las alas
        self.wing_angle += self.wing_speed

        # Si sale de la pantalla, la devuelvo por el otro lado
        if self.x < -50:
            self.x = self.screen_width + 50
        elif self.x > self.screen_width + 50:
            self.x = -50

        # Mantengo la mariposa en los límites verticales
        if self.y < 50:
            self.y = 50
        elif self.y > self.screen_height - 100:
            self.y = self.screen_height - 100

    def draw(self, screen):
        """Dibujo la mariposa en la pantalla"""
        # Calculo el movimiento de las alas
        wing_offset = math.sin(self.wing_angle) * 10

        # Dibujo el cuerpo
        pygame.draw.ellipse(
            screen,
            (50, 50, 50),
            (self.x - 3, self.y - self.size // 2, 6, self.size),
        )

        # Dibujo las alas superiores
        pygame.draw.ellipse(
            screen,
            self.color,
            (
                self.x - self.size - wing_offset,
                self.y - self.size // 2,
                self.size,
                self.size // 2,
            ),
        )
        pygame.draw.ellipse(
            screen,
            self.color,
            (
                self.x + wing_offset,
                self.y - self.size // 2,
                self.size,
                self.size // 2,
            ),
        )

        # Dibujo las alas inferiores
        pygame.draw.ellipse(
            screen,
            tuple(max(0, c - 50) for c in self.color),  # Color más oscuro
            (
                self.x - self.size // 2 - wing_offset // 2,
                self.y,
                self.size // 2,
                self.size // 3,
            ),
        )
        pygame.draw.ellipse(
            screen,
            tuple(max(0, c - 50) for c in self.color),  # Color más oscuro
            (
                self.x + wing_offset // 2,
                self.y,
                self.size // 2,
                self.size // 3,
            ),
        )

        # Dibujo las antenas
        pygame.draw.line(
            screen,
            (0, 0, 0),
            (self.x, self.y - self.size // 2),
            (self.x - 5, self.y - self.size // 2 - 8),
            1,
        )
        pygame.draw.line(
            screen,
            (0, 0, 0),
            (self.x, self.y - self.size // 2),
            (self.x + 5, self.y - self.size // 2 - 8),
            1,
        )

    def randomize_position(self):
        """Cambio la posición de la mariposa aleatoriamente"""
        self.x = random.randint(50, self.screen_width - 50)
        self.y = random.randint(100, self.screen_height - 200)
        self.vx = random.uniform(1, 3) * random.choice([-1, 1])
        self.time = random.uniform(0, math.pi * 2)
