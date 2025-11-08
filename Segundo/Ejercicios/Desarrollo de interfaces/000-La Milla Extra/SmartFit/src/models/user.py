# user.py - Modelo de usuario para SmartFit
# Fran - Desarrollo de interfaces

from datetime import datetime
from typing import Dict, List, Optional


class UserManager:
    """
    Gestor de usuarios para SmartFit
    Maneja todas las operaciones relacionadas con usuarios
    """

    def __init__(self, db_manager):
        """Inicializa el gestor de usuarios"""
        self.db = db_manager

    def crear_usuario(
        self,
        nombre: str,
        edad: int = None,
        peso: float = None,
        altura: float = None,
        objetivo: str = None,
    ) -> int:
        """
        Crea un nuevo usuario en el sistema

        Args:
            nombre: Nombre completo del usuario
            edad: Edad del usuario
            peso: Peso en kilogramos
            altura: Altura en centímetros
            objetivo: Objetivo fitness (perder peso, ganar músculo, etc.)

        Returns:
            ID del usuario creado o 0 si hubo error
        """
        if not nombre or not nombre.strip():
            raise ValueError("El nombre es obligatorio")

        try:
            return self.db.crear_usuario(nombre, edad, peso, altura, objetivo)
        except Exception as e:
            print(f"Error al crear usuario: {e}")
            return 0

    def obtener_usuario_actual(self) -> Optional[Dict]:
        """
        Obtiene el usuario actualmente seleccionado
        En una implementación real, esto vendría de un archivo de configuración
        """
        usuarios = self.db.obtener_usuarios()
        return usuarios[0] if usuarios else None

    def obtener_usuario_por_id(self, usuario_id: int) -> Optional[Dict]:
        """Obtiene un usuario específico por su ID"""
        return self.db.obtener_usuario(usuario_id)

    def listar_usuarios(self) -> List[Dict]:
        """Lista todos los usuarios registrados"""
        return self.db.obtener_usuarios()

    def calcular_imc(self, peso: float, altura: float) -> float:
        """
        Calcula el Índice de Masa Corporal

        Args:
            peso: Peso en kilogramos
            altura: Altura en metros

        Returns:
            Valor del IMC
        """
        if altura <= 0 or peso <= 0:
            return 0.0
        return round(peso / (altura**2), 2)

    def interpretar_imc(self, imc: float) -> str:
        """
        Interpreta el valor del IMC

        Args:
            imc: Valor del índice de masa corporal

        Returns:
            Categoría del IMC
        """
        if imc < 18.5:
            return "Bajo peso"
        elif 18.5 <= imc < 25:
            return "Peso normal"
        elif 25 <= imc < 30:
            return "Sobrepeso"
        else:
            return "Obesidad"

    def calcular_calorias_base(
        self, peso: float, altura: float, edad: int, sexo: str = "M"
    ) -> float:
        """
        Calcula las calorías basales usando la fórmula de Harris-Benedict

        Args:
            peso: Peso en kilogramos
            altura: Altura en centímetros
            edad: Edad en años
            sexo: "M" para masculino, "F" para femenino

        Returns:
            Calorías basales diarias
        """
        if sexo.upper() == "M":
            # Fórmula para hombres
            return round(
                88.362 + (13.397 * peso) + (4.799 * altura) - (5.677 * edad), 0
            )
        else:
            # Fórmula para mujeres
            return round(
                447.593 + (9.247 * peso) + (3.098 * altura) - (4.330 * edad), 0
            )

    def calcular_calorias_objetivo(
        self, calorias_base: float, objetivo: str, actividad: str = "moderado"
    ) -> float:
        """
        Calcula las calorías objetivo según el objetivo fitness

        Args:
            calorias_base: Calorías basales
            objetivo: Objetivo (perder_peso, ganar_musculo, mantenimiento)
            actividad: Nivel de actividad (bajo, moderado, alto)

        Returns:
            Calorías objetivo diarias
        """
        # Factores de actividad
        factores = {"bajo": 1.2, "moderado": 1.55, "alto": 1.9}

        factor = factores.get(actividad.lower(), 1.55)
        calorias_con_actividad = calorias_base * factor

        # Ajustes según objetivo
        ajustes = {
            "perder_peso": 0.85,  # 15% menos
            "ganar_musculo": 1.15,  # 15% más
            "mantenimiento": 1.0,  # Sin cambio
        }

        ajuste = ajustes.get(objetivo.lower(), 1.0)
        return round(calorias_con_actividad * ajuste, 0)

    def obtener_perfil_completo(self, usuario_id: int) -> Optional[Dict]:
        """
        Obtiene un perfil completo del usuario con estadísticas

        Args:
            usuario_id: ID del usuario

        Returns:
            Diccionario con el perfil completo
        """
        usuario = self.db.obtener_usuario(usuario_id)
        if not usuario:
            return None

        # Calcular IMC
        imc = 0.0
        categoria_imc = "N/A"
        if usuario.get("peso") and usuario.get("altura"):
            altura_metros = usuario["altura"] / 100
            imc = self.calcular_imc(usuario["peso"], altura_metros)
            categoria_imc = self.interpretar_imc(imc)

        # Calcular calorías
        calorias_base = 0.0
        calorias_objetivo = 0.0
        if usuario.get("peso") and usuario.get("altura") and usuario.get("edad"):
            calorias_base = self.calcular_calorias_base(
                usuario["peso"], usuario["altura"], usuario["edad"]
            )
            objetivo = usuario.get("objetivo", "mantenimiento").lower()
            calorias_objetivo = self.calcular_calorias_objetivo(calorias_base, objetivo)

        # Obtener estadísticas
        estadisticas = self.db.obtener_estadisticas_usuario(usuario_id)

        # Compilar perfil completo
        perfil = {
            **usuario,
            "imc": imc,
            "categoria_imc": categoria_imc,
            "calorias_base": calorias_base,
            "calorias_objetivo": calorias_objetivo,
            "estadisticas": estadisticas,
        }

        return perfil

    def validar_datos_usuario(
        self, nombre: str, edad: int, peso: float, altura: float
    ) -> List[str]:
        """
        Valida los datos de un usuario

        Args:
            nombre: Nombre del usuario
            edad: Edad en años
            peso: Peso en kilogramos
            altura: Altura en centímetros

        Returns:
            Lista de errores encontrados
        """
        errores = []

        # Validar nombre
        if not nombre or len(nombre.strip()) < 2:
            errores.append("El nombre debe tener al menos 2 caracteres")

        # Validar edad
        if edad and (edad < 13 or edad > 100):
            errores.append("La edad debe estar entre 13 y 100 años")

        # Validar peso
        if peso and (peso < 30 or peso > 300):
            errores.append("El peso debe estar entre 30 y 300 kg")

        # Validar altura
        if altura and (altura < 120 or altura > 250):
            errores.append("La altura debe estar entre 120 y 250 cm")

        return errores

    def obtener_rutinas_usuario(self, usuario_id: int) -> List[Dict]:
        """Obtiene todas las rutinas de un usuario"""
        return self.db.obtener_rutinas_usuario(usuario_id)

    def obtener_entrenamientos_recientes(
        self, usuario_id: int, limite: int = 5
    ) -> List[Dict]:
        """Obtiene los entrenamientos más recientes de un usuario"""
        # En una implementación completa, esto consultaría la base de datos
        # Por ahora, retornamos datos de ejemplo
        entrenamientos_ejemplo = []
        for i in range(limite):
            fecha = datetime.now().replace(day=datetime.now().day - i)
            entrenamientos_ejemplo.append(
                {
                    "id": i + 1,
                    "rutina_nombre": f"Rutina {i + 1}",
                    "fecha": fecha.strftime("%Y-%m-%d %H:%M"),
                    "duracion": 30 + (i * 5),
                    "calorias": 200 + (i * 25),
                    "completado": True,
                }
            )
        return entrenamientos_ejemplo

    def es_nuevo_usuario(self, usuario_id: int) -> bool:
        """
        Determina si un usuario es nuevo (tiene pocos entrenamientos)

        Args:
            usuario_id: ID del usuario

        Returns:
            True si es un usuario nuevo
        """
        estadisticas = self.db.obtener_estadisticas_usuario(usuario_id)
        return estadisticas.get("total_entrenamientos", 0) < 3

    def obtener_recomendaciones(self, usuario_id: int) -> List[str]:
        """
        Genera recomendaciones personalizadas para el usuario

        Args:
            usuario_id: ID del usuario

        Returns:
            Lista de recomendaciones
        """
        usuario = self.db.obtener_usuario(usuario_id)
        if not usuario:
            return []

        recomendaciones = []

        # Recomendaciones basadas en el objetivo
        objetivo = usuario.get("objetivo", "").lower()
        if "perder" in objetivo or "quemar" in objetivo:
            recomendaciones.append("Incluye ejercicios cardiovasculares en tu rutina")
            recomendaciones.append("Considera aumentar la frecuencia de entrenamiento")
        elif "ganar" in objetivo or "músculo" in objetivo:
            recomendaciones.append("Enfócate en ejercicios de fuerza")
            recomendaciones.append("Aumenta el consumo de proteínas")
        else:
            recomendaciones.append("Mantén un equilibrio entre cardio y fuerza")

        # Recomendaciones basadas en IMC
        if usuario.get("peso") and usuario.get("altura"):
            imc = self.calcular_imc(usuario["peso"], usuario["altura"] / 100)
            if imc > 25:
                recomendaciones.append(
                    "Considera combinar dieta y ejercicio para mejor resultado"
                )
            elif imc < 18.5:
                recomendaciones.append("Consulta con un profesional sobre nutrición")

        # Recomendaciones basadas en edad
        if usuario.get("edad"):
            if usuario["edad"] > 50:
                recomendaciones.append(
                    "Incluye ejercicios de flexibilidad y equilibrio"
                )
            elif usuario["edad"] < 25:
                recomendaciones.append(
                    "Es un buen momento para construir masa muscular"
                )

        return recomendaciones
