# Flower - Clase que representa las flores del jardín
# Autor: Fran
# Las flores crecen cuando hago clic en ellas y tienen animación

import math
import random

import pygame


class Flower:
    """Clase que representa una flor animada en el jardín"""

    def __init__(self, x, y, color):
        self.x = x
        self.y = y
        self.color = color
        self.size = random.randint(8, 15)  # Tamaño inicial pequeño como brote
        self.max_size = random.randint(35, 50)  # Tamaño máximo aleatorio
        self.growth_speed = 0.5
        self.sway_angle = 0  # Ángulo para la animación de balanceo
        self.sway_speed = random.uniform(0.02, 0.04)
        self.petals = 6  # Número de pétalos
        self.stem_height = random.randint(10, 25)  # Altura inicial del tallo más corta
        self.max_stem_height = random.randint(80, 120)
        self.growing = False  # Si está creciendo

    def update(self):
        """Actualizo la animación de la flor"""
        # Animación de balanceo suave
        self.sway_angle += self.sway_speed

        # Si está creciendo, aumento el tamaño
        if self.growing:
            if self.size < self.max_size:
                self.size += self.growth_speed
            if self.stem_height < self.max_stem_height:
                self.stem_height += 2
            else:
                self.growing = False

    def draw(self, screen):
        """Dibujo la flor en la pantalla"""
        # Calculo el balanceo
        sway_offset = math.sin(self.sway_angle) * 5

        # Dibujo una pequeña base de tierra alrededor de la flor
        pygame.draw.ellipse(
            screen,
            (60, 40, 20),  # Color tierra oscura
            (self.x - 15, self.y - 5, 30, 10),
        )

        # Dibujo el tallo creciendo hacia arriba desde el suelo
        if self.stem_height > 0:
            stem_start = (self.x + sway_offset, self.y)
            stem_end = (self.x + sway_offset * 2, self.y - self.stem_height)
            pygame.draw.line(screen, (34, 139, 34), stem_start, stem_end, 3)

            # Dibujo las hojas
            if self.stem_height > 30:
                leaf_y = self.y - self.stem_height // 2
                pygame.draw.ellipse(
                    screen,
                    (50, 150, 50),
                    (self.x + sway_offset - 15, leaf_y, 20, 10),
                )
                pygame.draw.ellipse(
                    screen,
                    (50, 150, 50),
                    (self.x + sway_offset - 5, leaf_y + 5, 20, 10),
                )

        # Dibujo los pétalos de la flor (solo si ha crecido lo suficiente)
        if self.size > 12:
            center_x = self.x + sway_offset * 2
            center_y = self.y - self.stem_height

            for i in range(self.petals):
                angle = (2 * math.pi * i) / self.petals
                petal_x = center_x + math.cos(angle) * self.size * 0.7
                petal_y = center_y + math.sin(angle) * self.size * 0.7
                pygame.draw.circle(
                    screen,
                    self.color,
                    (int(petal_x), int(petal_y)),
                    int(self.size * 0.4),
                )

            # Dibujo el centro de la flor
            pygame.draw.circle(
                screen,
                (255, 255, 0),
                (int(center_x), int(center_y)),
                int(self.size * 0.3),
            )
        else:
            # Si es muy pequeña, dibujo solo un botón/capullo
            bud_x = self.x + sway_offset * 2
            bud_y = self.y - self.stem_height
            pygame.draw.circle(
                screen,
                self.color,
                (int(bud_x), int(bud_y)),
                int(self.size * 0.5),
            )

    def check_click(self, pos):
        """Verifico si hicieron clic en la flor"""
        mouse_x, mouse_y = pos
        # Considero también el área donde están los pétalos
        flower_top = self.y - self.stem_height
        flower_bottom = self.y + self.max_size

        # Verifico si el clic está en el área de la flor (con un margen más grande)
        if (
            self.x - self.max_size <= mouse_x <= self.x + self.max_size
            and flower_top - self.max_size <= mouse_y <= flower_bottom
        ):
            return True
        return False

    def grow(self):
        """Hago que la flor crezca"""
        self.growing = True

    def reset(self):
        """Reinicio la flor a su estado inicial"""
        self.size = random.randint(8, 15)
        self.stem_height = random.randint(10, 25)
        self.growing = False
        self.sway_angle = 0
