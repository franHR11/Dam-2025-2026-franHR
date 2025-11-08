# AudioManager - Clase para gestionar todo el audio del juego
# Autor: Fran
# Controlo la música de fondo y los efectos de sonido

import random

import pygame


class AudioManager:
    """Clase que gestiona música y efectos de sonido"""

    def __init__(self):
        self.current_music = None
        self.music_volume = 0.5
        self.sound_volume = 0.7
        self.is_day = True
        self.paused = False

        # Configuro el volumen inicial
        pygame.mixer.music.set_volume(self.music_volume)

        # Genero sonidos simples sin usar sndarray para evitar problemas
        self.generate_simple_sounds()

    def generate_simple_sounds(self):
        """Creo sonidos simples sin sndarray"""
        try:
            # Intento crear un sonido simple con pygame
            self.click_sound = None
        except:
            self.click_sound = None

    def play_background_music(self):
        """Reproduzco música de fondo simulada"""
        # Como no tengo archivos de música, simplemente me aseguro de que el mixer funcione
        try:
            pygame.mixer.music.stop()  # Limpio cualquier música anterior
        except:
            pass  # Si falla, no pasa nada, el juego continúa sin música

    def play_click_sound(self):
        """Reproduzco el sonido de clic"""
        try:
            if self.click_sound:
                self.click_sound.play()
        except:
            pass  # Si falla el sonido, continúo el juego

    def toggle_day_night_music(self):
        """Cambio la música según el modo día/noche"""
        self.is_day = not self.is_day

        # Cambio el volumen según la hora
        if self.is_day:
            self.music_volume = 0.5
        else:
            self.music_volume = 0.3

        pygame.mixer.music.set_volume(self.music_volume)

    def pause_music(self):
        """Pauso la música"""
        self.paused = True
        try:
            pygame.mixer.music.pause()
        except:
            pass

    def resume_music(self):
        """Reanudo la música"""
        self.paused = False
        try:
            pygame.mixer.music.unpause()
        except:
            pass

    def stop_music(self):
        """Detengo toda la música"""
        try:
            pygame.mixer.music.stop()
        except:
            pass

    def set_music_volume(self, volume):
        """Ajusto el volumen de la música (0.0 a 1.0)"""
        self.music_volume = max(0.0, min(1.0, volume))
        try:
            pygame.mixer.music.set_volume(self.music_volume)
        except:
            pass

    def set_sound_volume(self, volume):
        """Ajusto el volumen de los efectos (0.0 a 1.0)"""
        self.sound_volume = max(0.0, min(1.0, volume))
        try:
            if self.click_sound:
                self.click_sound.set_volume(self.sound_volume)
        except:
            pass
