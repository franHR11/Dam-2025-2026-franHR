# The Magic Garden - Juego interactivo de jardín mágico
# Autor: Fran
# Este es el archivo principal que inicia el juego

import sys

import pygame
from audio_manager import AudioManager
from garden_scene import GardenScene

# Inicializo Pygame
pygame.init()
pygame.mixer.init()

# Configuración básica de la ventana
SCREEN_WIDTH = 1024
SCREEN_HEIGHT = 768
screen = pygame.display.set_mode((SCREEN_WIDTH, SCREEN_HEIGHT))
pygame.display.set_caption("El Jardin Mágico - FranHR Project")

# Creo el reloj para controlar los FPS
clock = pygame.time.Clock()
FPS = 60

# Creo las instancias principales del juego
audio_manager = AudioManager()
garden_scene = GardenScene(screen, audio_manager)

# Variable para controlar si el juego está en pausa
paused = False


def main():
    """Función principal del bucle del juego"""
    global paused

    running = True

    while running:
        # Control de eventos
        for event in pygame.event.get():
            if event.type == pygame.QUIT:
                running = False
            elif event.type == pygame.KEYDOWN:
                if event.key == pygame.K_ESCAPE:
                    running = False
                elif event.key == pygame.K_SPACE:
                    # Pauso o reanudo el juego
                    paused = not paused
                    if paused:
                        audio_manager.pause_music()
                    else:
                        audio_manager.resume_music()
                elif event.key == pygame.K_d:
                    # Cambio entre día y noche
                    garden_scene.toggle_day_night()
                elif event.key == pygame.K_r:
                    # Reinicio la escena
                    garden_scene.reset()
            elif event.type == pygame.MOUSEBUTTONDOWN:
                # Hago clic para interactuar con flores
                garden_scene.handle_click(event.pos)

        if not paused:
            # Actualizo la lógica del juego
            garden_scene.update()

        # Renderizo todo en pantalla
        garden_scene.draw()

        # Si está en pausa, muestro el texto
        if paused:
            font = pygame.font.Font(None, 74)
            text = font.render("PAUSA", True, (255, 255, 255))
            text_rect = text.get_rect(center=(SCREEN_WIDTH // 2, SCREEN_HEIGHT // 2))
            screen.blit(text, text_rect)

        # Actualizo la pantalla
        pygame.display.flip()

        # Mantengo 60 FPS
        clock.tick(FPS)

    # Salgo del juego limpiamente
    pygame.quit()
    sys.exit()


if __name__ == "__main__":
    main()
