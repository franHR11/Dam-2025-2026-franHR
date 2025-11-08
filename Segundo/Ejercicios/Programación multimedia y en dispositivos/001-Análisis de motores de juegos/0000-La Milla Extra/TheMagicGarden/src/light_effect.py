# LightEffect - Clase que representa efectos de luz mágica
# Autor: Fran
# Las luces parpadean y dan un ambiente mágico al jardín

import math
import random

import pygame


class LightEffect:
    """Clase que representa un efecto de luz flotante y parpadeante"""

    def __init__(self, screen_width, screen_height):
        self.screen_width = screen_width
        self.screen_height = screen_height

        # Posición inicial aleatoria
        self.x = random.randint(50, screen_width - 50)
        self.y = random.randint(50, screen_height - 200)

        # Movimiento flotante
        self.float_amplitude = random.uniform(10, 20)
        self.float_speed = random.uniform(0.02, 0.05)
        self.float_time = random.uniform(0, math.pi * 2)

        # Propiedades de la luz
        self.base_size = random.randint(3, 8)
        self.current_size = self.base_size
        self.max_size = self.base_size * 2

        # Color amarillo/dorado cálido
        self.color = (
            random.randint(200, 255),
            random.randint(180, 220),
            random.randint(50, 150),
        )

        # Parpadeo
        self.pulse_speed = random.uniform(0.1, 0.2)
        self.pulse_time = random.uniform(0, math.pi * 2)

        # Transparencia
        self.alpha = random.randint(100, 200)

        # Velocidad de movimiento horizontal
        self.vx = random.uniform(-0.5, 0.5)

    def update(self):
        """Actualizo la animación del efecto de luz"""
        # Movimiento flotante
        self.float_time += self.float_speed
        self.y += math.sin(self.float_time) * self.float_amplitude * 0.1
        self.x += self.vx

        # Movimiento horizontal con rebote
        if self.x < 20:
            self.x = 20
            self.vx = abs(self.vx)
        elif self.x > self.screen_width - 20:
            self.x = self.screen_width - 20
            self.vx = -abs(self.vx)

        # Parpadeo
        self.pulse_time += self.pulse_speed
        pulse = (math.sin(self.pulse_time) + 1) / 2  # Valor entre 0 y 1
        self.current_size = self.base_size + pulse * (self.max_size - self.base_size)

        # Movimiento vertical suave
        if self.y < 20:
            self.y = 20
        elif self.y > self.screen_height - 100:
            self.y = self.screen_height - 100

    def draw(self, screen):
        """Dibujo el efecto de luz de forma simple"""
        # Dibujo un círculo principal con color amarillo/dorado
        pygame.draw.circle(
            screen,
            self.color,
            (int(self.x), int(self.y)),
            int(self.current_size),
        )

        # Dibujo un centro más brillante
        pygame.draw.circle(
            screen,
            (255, 255, 200),  # Color blanco amarillento
            (int(self.x), int(self.y)),
            int(self.current_size // 2),
        )

        # Dibujo un borde brillante
        pygame.draw.circle(
            screen,
            (255, 255, 255),  # Blanco puro
            (int(self.x), int(self.y)),
            int(self.current_size),
            1,  # Grosor de 1 píxel
        )

    def reset(self):
        """Reinicio el efecto a valores aleatorios"""
        self.x = random.randint(50, self.screen_width - 50)
        self.y = random.randint(50, self.screen_height - 200)
        self.float_time = random.uniform(0, math.pi * 2)
        self.pulse_time = random.uniform(0, math.pi * 2)
        self.vx = random.uniform(-0.5, 0.5)
