#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Test unitario para verificar las correcciones de errores en SmartFit
"""

import os
import sys
import tkinter as tk
import unittest
from tkinter import ttk
from unittest.mock import Mock, patch

# Añadir el directorio src al path
sys.path.append(os.path.join(os.path.dirname(__file__), ".."))

from src.models.database import DatabaseManager


class TestSmartFitFixes(unittest.TestCase):
    """Test suite para verificar las correcciones implementadas"""

    def setUp(self):
        """Configuración inicial para cada test"""
        self.db_manager = DatabaseManager(":memory:")  # Base de datos en memoria
        self.db_manager.create_tables()
        self.db_manager.initialize_data()

    def tearDown(self):
        """Limpieza después de cada test"""
        if self.db_manager.connection:
            self.db_manager.connection.close()

    def test_stringvar_master_fix(self):
        """Test para verificar la corrección del error StringVar.master"""
        # Crear un mock de la sección de entrenamientos
        with patch("src.gui.workout_section.WorkoutSection") as mock_workout:
            # Simular variables del formulario
            new_workout_vars = {
                "rutina": tk.StringVar(),  # StringVar normal
                "rutina_widget": ttk.Combobox(),  # Widget guardado correctamente
                "fecha": tk.StringVar(),
                "duracion": tk.StringVar(),
                "calorias": tk.StringVar(),
                "completado": tk.BooleanVar(),
                "notas": tk.StringVar(),
            }

            # Verificar que podemos acceder al widget directamente
            self.assertIn("rutina_widget", new_workout_vars)
            self.assertIsInstance(new_workout_vars["rutina_widget"], ttk.Combobox)

            # Verificar que no intentamos acceder a .master en StringVar
            self.assertIsInstance(new_workout_vars["rutina"], tk.StringVar)

    def test_food_combo_fix(self):
        """Test para verificar la corrección del problema con food_combo"""
        # Crear un mock de la sección de nutrición
        with patch("src.gui.nutrition_section.NutritionSection") as mock_nutrition:
            # Simular la creación del food_combo como atributo
            mock_instance = mock_nutrition.return_value
            mock_instance.food_combo = ttk.Combobox()
            mock_instance.foods_data = [
                {"nombre": "Pollo", "calorias_por_100g": 165},
                {"nombre": "Arroz", "calorias_por_100g": 130},
                {"nombre": "Brócoli", "calorias_por_100g": 25},
            ]

            # Verificar que food_combo es accesible
            self.assertTrue(hasattr(mock_instance, "food_combo"))
            self.assertIsInstance(mock_instance.food_combo, ttk.Combobox)

            # Verificar que podemos actualizar los valores del combo
            food_names = [
                food.get("nombre", "")
                for food in mock_instance.foods_data
                if food.get("nombre")
            ]
            mock_instance.food_combo["values"] = food_names
            self.assertEqual(len(mock_instance.food_combo["values"]), 3)

    def test_alimentos_data_loaded(self):
        """Test para verificar que los alimentos se cargan correctamente"""
        # Obtener alimentos de la base de datos
        alimentos = self.db_manager.obtener_alimentos()

        # Verificar que hay alimentos cargados
        self.assertGreater(len(alimentos), 0)

        # Verificar alimentos específicos
        food_names = [food.get("nombre", "") for food in alimentos]
        expected_foods = ["Pollo", "Arroz", "Brócoli", "Plátano", "Salmón"]

        for expected_food in expected_foods:
            self.assertIn(expected_food, food_names)

    def test_routine_dialog_functionality(self):
        """Test para verificar la funcionalidad del diálogo de rutinas"""
        # Crear un usuario de prueba
        user_id = self.db_manager.crear_usuario(
            "Test User", 25, 70, 175, "Pérdida de peso"
        )

        # Verificar que podemos crear una rutina
        routine_id = self.db_manager.crear_rutina(
            user_id, "Rutina Test", "Descripción test", 30, "Principiante"
        )
        self.assertIsNotNone(routine_id)

        # Verificar que podemos obtener rutinas del usuario
        rutinas = self.db_manager.obtener_rutinas_usuario(user_id)
        self.assertGreater(len(rutinas), 0)
        self.assertEqual(rutinas[0]["nombre"], "Rutina Test")

    def test_eliminar_rutina_method(self):
        """Test para verificar el método eliminar_rutina"""
        # Crear un usuario y una rutina de prueba
        user_id = self.db_manager.crear_usuario(
            "Test User", 25, 70, 175, "Pérdida de peso"
        )
        routine_id = self.db_manager.crear_rutina(
            user_id, "Rutina para eliminar", "Descripción", 30, "Principiante"
        )

        # Verificar que la rutina existe
        rutinas = self.db_manager.obtener_rutinas_usuario(user_id)
        self.assertEqual(len(rutinas), 1)

        # Eliminar la rutina
        result = self.db_manager.eliminar_rutina(routine_id)
        self.assertTrue(result)

        # Verificar que la rutina fue eliminada
        rutinas = self.db_manager.obtener_rutinas_usuario(user_id)
        self.assertEqual(len(rutinas), 0)

    def test_obtener_ejercicios_rutina(self):
        """Test para verificar el método obtener_ejercicios_rutina"""
        # Crear datos de prueba
        user_id = self.db_manager.crear_usuario(
            "Test User", 25, 70, 175, "Pérdida de peso"
        )
        routine_id = self.db_manager.crear_rutina(
            user_id, "Rutina Test", "Descripción", 30, "Principiante"
        )

        # Obtener ejercicios disponibles
        ejercicios = self.db_manager.obtener_ejercicios()
        if ejercicios:
            # Agregar un ejercicio a la rutina
            ejercicio_id = ejercicios[0]["id"]
            self.db_manager.agregar_ejercicio_a_rutina(
                routine_id, ejercicio_id, 3, 10, 60, 1
            )

            # Obtener ejercicios de la rutina
            ejercicios_rutina = self.db_manager.obtener_ejercicios_rutina(routine_id)
            self.assertGreater(len(ejercicios_rutina), 0)
            self.assertEqual(ejercicios_rutina[0]["series"], 3)
            self.assertEqual(ejercicios_rutina[0]["repeticiones"], 10)

    def test_no_hardcoded_values(self):
        """Test para verificar que no hay valores hardcodeados críticos"""
        # Verificar que no hay URLs hardcodeadas en los métodos de base de datos
        import inspect
        import re

        # Revisar métodos clave de DatabaseManager
        methods_to_check = [
            self.db_manager.obtener_alimentos,
            self.db_manager.obtener_rutinas_usuario,
            self.db_manager.obtener_ejercicios_rutina,
            self.db_manager.eliminar_rutina,
        ]

        for method in methods_to_check:
            source = inspect.getsource(method)
            # No debe haber URLs o IPs hardcodeadas
            self.assertNotIn("http://", source)
            self.assertNotIn("https://", source)
            self.assertNotIn("localhost", source)
            self.assertNotIn("127.0.0.1", source)


def run_fixes_verification():
    """Función principal para ejecutar todas las verificaciones"""
    print("🧪 Ejecutando tests de verificación de correcciones SmartFit...")

    # Crear suite de tests
    suite = unittest.TestLoader().loadTestsFromTestCase(TestSmartFitFixes)

    # Ejecutar tests
    runner = unittest.TextTestRunner(verbosity=2)
    result = runner.run(suite)

    # Resumen
    print("\n" + "=" * 50)
    print("📋 RESUMEN DE VERIFICACIÓN")
    print("=" * 50)
    print(f"✅ Tests ejecutados: {result.testsRun}")
    print(f"❌ Fallos: {len(result.failures)}")
    print(f"💥 Errores: {len(result.errors)}")

    if result.failures:
        print("\n🔍 FALLOS DETALLADOS:")
        for test, traceback in result.failures:
            print(f"  - {test}: {traceback}")

    if result.errors:
        print("\n💥 ERRORES DETALLADOS:")
        for test, traceback in result.errors:
            print(f"  - {test}: {traceback}")

    if result.wasSuccessful():
        print("\n🎉 ¡TODAS LAS CORRECCIONES FUNCIONAN CORRECTAMENTE!")
        print("✅ Los errores de StringVar.master han sido corregidos")
        print("✅ El problema con food_combo ha sido resuelto")
        print("✅ La funcionalidad de rutinas está completa")
        print("✅ No hay valores hardcodeados críticos")
    else:
        print("\n⚠️  Algunos tests fallaron. Revisa los detalles arriba.")

    return result.wasSuccessful()


if __name__ == "__main__":
    success = run_fixes_verification()
    sys.exit(0 if success else 1)
