# database.py - Gestor de base de datos SmartFit
# Fran - Desarrollo de interfaces

import json
import os
import sqlite3
from datetime import datetime
from typing import Any, Dict, List, Optional


class DatabaseManager:
    """
    Gestor de base de datos SQLite para SmartFit
    Maneja todas las operaciones de base de datos
    """

    def __init__(self, db_path: str = "smartfit.db"):
        """Inicializa el gestor de base de datos"""
        self.db_path = db_path
        self.connection = None

    def check_connection(self) -> bool:
        """Verifica si se puede conectar a la base de datos"""
        try:
            self.connection = sqlite3.connect(self.db_path)
            self.connection.execute("SELECT 1")
            return True
        except Exception as e:
            print(f"Error de conexión: {e}")
            return False

    def close(self):
        """Cierra la conexión a la base de datos"""
        if self.connection:
            self.connection.close()

    def create_tables(self):
        """Crea todas las tablas necesarias para SmartFit"""
        if not self.connection:
            self.check_connection()

        cursor = self.connection.cursor()

        # Tabla de usuarios
        cursor.execute("""
            CREATE TABLE IF NOT EXISTS usuarios (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                nombre TEXT NOT NULL,
                edad INTEGER,
                peso REAL,
                altura REAL,
                objetivo TEXT,
                fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        """)

        # Tabla de rutinas de entrenamiento
        cursor.execute("""
            CREATE TABLE IF NOT EXISTS rutinas (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                usuario_id INTEGER,
                nombre TEXT NOT NULL,
                descripcion TEXT,
                duracion_minutos INTEGER,
                dificultad TEXT CHECK (dificultad IN ('principiante', 'intermedio', 'avanzado')),
                fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (usuario_id) REFERENCES usuarios (id)
            )
        """)

        # Tabla de ejercicios
        cursor.execute("""
            CREATE TABLE IF NOT EXISTS ejercicios (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                nombre TEXT NOT NULL UNIQUE,
                categoria TEXT,
                musculo_principal TEXT,
                equipamiento TEXT,
                instrucciones TEXT,
                calorias_por_minuto REAL
            )
        """)

        # Tabla de relación rutina-ejercicio
        cursor.execute("""
            CREATE TABLE IF NOT EXISTS rutina_ejercicios (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                rutina_id INTEGER,
                ejercicio_id INTEGER,
                series INTEGER DEFAULT 3,
                repeticiones INTEGER DEFAULT 10,
                descanso_segundos INTEGER DEFAULT 60,
                orden INTEGER,
                FOREIGN KEY (rutina_id) REFERENCES rutinas (id),
                FOREIGN KEY (ejercicio_id) REFERENCES ejercicios (id)
            )
        """)

        # Tabla de progreso de entrenamiento
        cursor.execute("""
            CREATE TABLE IF NOT EXISTS entrenamientos (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                usuario_id INTEGER,
                rutina_id INTEGER,
                fecha_entrenamiento TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                duracion_real INTEGER,
                calorias_quemadas REAL,
                completado BOOLEAN DEFAULT FALSE,
                notas TEXT,
                FOREIGN KEY (usuario_id) REFERENCES usuarios (id),
                FOREIGN KEY (rutina_id) REFERENCES rutinas (id)
            )
        """)

        # Tabla de nutrición
        cursor.execute("""
            CREATE TABLE IF NOT EXISTS alimentos (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                nombre TEXT NOT NULL UNIQUE,
                calorias_por_100g REAL,
                proteinas REAL,
                carbohidratos REAL,
                grasas REAL
            )
        """)

        # Tabla de consumo diario
        cursor.execute("""
            CREATE TABLE IF NOT EXISTS consumo_diario (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                usuario_id INTEGER,
                fecha DATE,
                alimento_id INTEGER,
                cantidad_gramos REAL,
                FOREIGN KEY (usuario_id) REFERENCES usuarios (id),
                FOREIGN KEY (alimento_id) REFERENCES alimentos (id)
            )
        """)

        self.connection.commit()

    def initialize_data(self):
        """Inserta datos de ejemplo si no existen"""
        cursor = self.connection.cursor()

        # Verificar si ya hay datos
        cursor.execute("SELECT COUNT(*) FROM usuarios")
        if cursor.fetchone()[0] > 0:
            print("📊 Datos de ejemplo ya existen, saltando inicialización")
            return

        print("🔧 Insertando datos de ejemplo...")

        # Insertar ejercicios de ejemplo usando INSERT OR IGNORE para evitar duplicados
        ejercicios_ejemplo = [
            (
                "Flexiones",
                "Fuerza",
                "Pecho",
                "Ninguno",
                "Tumbarse boca abajo y empujar el cuerpo",
                8.0,
            ),
            (
                "Sentadillas",
                "Fuerza",
                "Piernas",
                "Ninguno",
                "Bajar y subir manteniendo la espalda recta",
                6.0,
            ),
            (
                "Plancha",
                "Fuerza",
                "Core",
                "Ninguno",
                "Mantener posición horizontal",
                5.0,
            ),
            (
                "Burpees",
                "Cardio",
                "Cuerpo completo",
                "Ninguno",
                "Movimiento completo de cuerpo",
                10.0,
            ),
            (
                "Mountain Climbers",
                "Cardio",
                "Core",
                "Ninguno",
                "Corrida en posición de plancha",
                9.0,
            ),
            (
                "Peso Muerto",
                "Fuerza",
                "Espalda",
                "Barra",
                "Levantar barra desde el suelo",
                12.0,
            ),
            (
                "Press de Banca",
                "Fuerza",
                "Pecho",
                "Barra",
                "Acostado, empujar barra hacia arriba",
                9.0,
            ),
            (
                "Dominadas",
                "Fuerza",
                "Espalda",
                "Barra",
                "Colgarse y tirar hacia arriba",
                8.0,
            ),
            (
                "Bicicleta",
                "Cardio",
                "Piernas",
                "Bicicleta",
                "Ejercicio cardiovascular",
                7.0,
            ),
            (
                "Elíptica",
                "Cardio",
                "Cuerpo completo",
                "Elíptica",
                "Movimiento fluido全身",
                8.0,
            ),
        ]

        cursor.executemany(
            """
            INSERT OR IGNORE INTO ejercicios (nombre, categoria, musculo_principal, equipamiento, instrucciones, calorias_por_minuto)
            VALUES (?, ?, ?, ?, ?, ?)
        """,
            ejercicios_ejemplo,
        )

        # Insertar alimentos de ejemplo usando INSERT OR IGNORE
        alimentos_ejemplo = [
            ("Pollo", 165, 31, 0, 3.6),
            ("Arroz", 130, 2.7, 28, 0.3),
            ("Brócoli", 25, 3, 5, 0.3),
            ("Plátano", 89, 1.1, 23, 0.3),
            ("Salmón", 208, 20, 0, 13),
            ("Avena", 68, 2.4, 12, 1.4),
            ("Huevos", 155, 13, 1.1, 11),
            ("Quinoa", 120, 4.4, 22, 1.9),
            ("Almendras", 576, 21, 22, 50),
            ("Yogur", 59, 10, 3.6, 0.4),
        ]

        cursor.executemany(
            """
            INSERT OR IGNORE INTO alimentos (nombre, calorias_por_100g, proteinas, carbohidratos, grasas)
            VALUES (?, ?, ?, ?, ?)
        """,
            alimentos_ejemplo,
        )

        self.connection.commit()
        print("✅ Datos de ejemplo insertados correctamente")

    def ejecutar_consulta(self, consulta: str, parametros: tuple = ()) -> List[Dict]:
        """Ejecuta una consulta SELECT y devuelve los resultados"""
        if not self.connection:
            self.check_connection()

        cursor = self.connection.cursor()
        cursor.execute(consulta, parametros)
        columnas = [descripcion[0] for descripcion in cursor.description]
        filas = cursor.fetchall()

        return [dict(zip(columnas, fila)) for fila in filas]

    def ejecutar_comando(self, comando: str, parametros: tuple = ()) -> int:
        """Ejecuta un comando INSERT, UPDATE o DELETE y devuelve el ID del último registro"""
        if not self.connection:
            self.check_connection()

        cursor = self.connection.cursor()
        cursor.execute(comando, parametros)
        self.connection.commit()

        return cursor.lastrowid

    # Métodos CRUD para usuarios
    def crear_usuario(
        self,
        nombre: str,
        edad: int = None,
        peso: float = None,
        altura: float = None,
        objetivo: str = None,
    ) -> int:
        """Crea un nuevo usuario"""
        return self.ejecutar_comando(
            """
            INSERT INTO usuarios (nombre, edad, peso, altura, objetivo)
            VALUES (?, ?, ?, ?, ?)
        """,
            (nombre, edad, peso, altura, objetivo),
        )

    def obtener_usuarios(self) -> List[Dict]:
        """Obtiene todos los usuarios"""
        return self.ejecutar_consulta(
            "SELECT * FROM usuarios ORDER BY fecha_registro DESC"
        )

    def obtener_usuario(self, usuario_id: int) -> Optional[Dict]:
        """Obtiene un usuario específico"""
        resultado = self.ejecutar_consulta(
            "SELECT * FROM usuarios WHERE id = ?", (usuario_id,)
        )
        return resultado[0] if resultado else None

    # Métodos CRUD para rutinas
    def crear_rutina(
        self,
        usuario_id: int,
        nombre: str,
        descripcion: str = None,
        duracion_minutos: int = 30,
        dificultad: str = "principiante",
    ) -> int:
        """Crea una nueva rutina de entrenamiento"""
        return self.ejecutar_comando(
            """
            INSERT INTO rutinas (usuario_id, nombre, descripcion, duracion_minutos, dificultad)
            VALUES (?, ?, ?, ?, ?)
        """,
            (usuario_id, nombre, descripcion, duracion_minutos, dificultad),
        )

    def obtener_rutinas_usuario(self, usuario_id: int) -> List[Dict]:
        """Obtiene todas las rutinas de un usuario"""
        return self.ejecutar_consulta(
            """
            SELECT * FROM rutinas WHERE usuario_id = ? ORDER BY fecha_creacion DESC
        """,
            (usuario_id,),
        )

    # Métodos para ejercicios
    def obtener_ejercicios(self, categoria: str = None) -> List[Dict]:
        """Obtiene todos los ejercicios, filtrados por categoría si se especifica"""
        if categoria:
            return self.ejecutar_consulta(
                "SELECT * FROM ejercicios WHERE categoria = ?", (categoria,)
            )
        return self.ejecutar_consulta("SELECT * FROM ejercicios")

    def obtener_ejercicios_rutina(self, rutina_id: int) -> List[Dict]:
        """Obtiene todos los ejercicios de una rutina específica"""
        return self.ejecutar_consulta(
            """
            SELECT e.*, re.series, re.repeticiones, re.descanso_segundos, re.orden
            FROM ejercicios e
            JOIN rutina_ejercicios re ON e.id = re.ejercicio_id
            WHERE re.rutina_id = ?
            ORDER BY re.orden
        """,
            (rutina_id,),
        )

    def agregar_ejercicio_a_rutina(
        self,
        rutina_id: int,
        ejercicio_id: int,
        series: int = 3,
        repeticiones: int = 10,
        descanso_segundos: int = 60,
        orden: int = 1,
    ) -> int:
        """Agrega un ejercicio a una rutina"""
        return self.ejecutar_comando(
            """
            INSERT INTO rutina_ejercicios (rutina_id, ejercicio_id, series, repeticiones, descanso_segundos, orden)
            VALUES (?, ?, ?, ?, ?, ?)
        """,
            (rutina_id, ejercicio_id, series, repeticiones, descanso_segundos, orden),
        )

    # Métodos para entrenamientos
    def registrar_entrenamiento(
        self,
        usuario_id: int,
        rutina_id: int,
        duracion_real: int = None,
        calorias_quemadas: float = None,
        completado: bool = False,
        notas: str = None,
    ) -> int:
        """Registra un entrenamiento completado"""
        return self.ejecutar_comando(
            """
            INSERT INTO entrenamientos (usuario_id, rutina_id, duracion_real, calorias_quemadas, completado, notas)
            VALUES (?, ?, ?, ?, ?, ?)
        """,
            (
                usuario_id,
                rutina_id,
                duracion_real,
                calorias_quemadas,
                completado,
                notas,
            ),
        )

    def obtener_estadisticas_usuario(self, usuario_id: int) -> Dict:
        """Obtiene las estadísticas generales de un usuario"""
        cursor = self.connection.cursor()

        # Total de entrenamientos
        cursor.execute(
            "SELECT COUNT(*) FROM entrenamientos WHERE usuario_id = ?", (usuario_id,)
        )
        total_entrenamientos = cursor.fetchone()[0]

        # Total de calorías
        cursor.execute(
            "SELECT SUM(calorias_quemadas) FROM entrenamientos WHERE usuario_id = ? AND calorias_quemadas IS NOT NULL",
            (usuario_id,),
        )
        total_calorias = cursor.fetchone()[0] or 0

        # Tiempo total
        cursor.execute(
            "SELECT SUM(duracion_real) FROM entrenamientos WHERE usuario_id = ? AND duracion_real IS NOT NULL",
            (usuario_id,),
        )
        tiempo_total = cursor.fetchone()[0] or 0

        return {
            "total_entrenamientos": total_entrenamientos,
            "total_calorias": round(total_calorias, 2),
            "tiempo_total_minutos": tiempo_total,
            "rutinas_creadas": self.contar_rutinas_usuario(usuario_id),
        }

    def contar_rutinas_usuario(self, usuario_id: int) -> int:
        """Cuenta las rutinas creadas por un usuario"""
        cursor = self.connection.cursor()
        cursor.execute(
            "SELECT COUNT(*) FROM rutinas WHERE usuario_id = ?", (usuario_id,)
        )
        return cursor.fetchone()[0]

    # Métodos para alimentos y nutrición
    def obtener_alimentos(self) -> List[Dict]:
        """Obtiene todos los alimentos"""
        return self.ejecutar_consulta("SELECT * FROM alimentos ORDER BY nombre")

    def registrar_consumo_alimento(
        self,
        usuario_id: int,
        alimento_id: int,
        cantidad_gramos: float,
        fecha: str = None,
    ) -> int:
        """Registra el consumo de un alimento"""
        if fecha is None:
            fecha = datetime.now().strftime("%Y-%m-%d")

        return self.ejecutar_comando(
            """
            INSERT INTO consumo_diario (usuario_id, fecha, alimento_id, cantidad_gramos)
            VALUES (?, ?, ?, ?)
        """,
            (usuario_id, fecha, alimento_id, cantidad_gramos),
        )

    def obtener_consumo_diario(self, usuario_id: int, fecha: str = None) -> List[Dict]:
        """Obtiene el consumo de alimentos de un día específico"""
        if fecha is None:
            fecha = datetime.now().strftime("%Y-%m-%d")

        return self.ejecutar_consulta(
            """
            SELECT a.nombre, a.calorias_por_100g, a.proteinas, a.carbohidratos, a.grasas,
                   cd.cantidad_gramos, (cd.cantidad_gramos * a.calorias_por_100g / 100) as calorias_consumidas
            FROM consumo_diario cd
            JOIN alimentos a ON cd.alimento_id = a.id
            WHERE cd.usuario_id = ? AND cd.fecha = ?
            ORDER BY a.nombre
        """,
            (usuario_id, fecha),
        )

    def obtener_calorias_diarias(self, usuario_id: int, fecha: str = None) -> float:
        """Obtiene el total de calorías consumidas en un día"""
        if fecha is None:
            fecha = datetime.now().strftime("%Y-%m-%d")

        cursor = self.connection.cursor()
        cursor.execute(
            """
            SELECT SUM(cd.cantidad_gramos * a.calorias_por_100g / 100)
            FROM consumo_diario cd
            JOIN alimentos a ON cd.alimento_id = a.id
            WHERE cd.usuario_id = ? AND cd.fecha = ?
        """,
            (usuario_id, fecha),
        )

        resultado = cursor.fetchone()[0]
        return round(resultado or 0, 2)
