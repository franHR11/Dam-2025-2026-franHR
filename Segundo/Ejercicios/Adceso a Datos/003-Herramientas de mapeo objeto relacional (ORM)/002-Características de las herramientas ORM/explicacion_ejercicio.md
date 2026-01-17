
### Actividad: Gestión de Pescales con Pescador

### 🧠 Explicación personal del ejercicio
Para realizar esta actividad, me he puesto en la piel de un pescador que necesita organizar sus capturas de forma digital. El objetivo era crear una aplicación segura y eficiente que conecte con una base de datos MySQL, sin usar frameworks complejos, pero aplicando buenas prácticas de desarrollo.

**Desarrollo paso a paso:**
1.  **Establecimiento de la conexión:** Lo primero que hice fue crear la clase `Pescador`. En su constructor `__init__`, definí las credenciales de acceso (host, usuario, contraseña) como atributos de la clase para mantener el orden. Implementé el método `conectar` utilizando el conector estándar `mysql.connector`. Es fundamental controlar los posibles fallos de red o autenticación, por lo que envolví la conexión en un bloque `try-except` para capturar cualquier `Error` y notificarlo sin que el programa se cuelgue bruscamente.

2.  **Obtención de datos (Listado):** Para el método `listar_peces`, necesitaba recuperar la información y mostrarla ordenada. Abrí un cursor configurado con `dictionary=True`. Esto es un detalle técnico importante: en lugar de recibir tuplas numéricas (que son difíciles de leer), el cursor me devuelve objetos tipo diccionario (clave-valor), lo que simula un mapeo objeto-relacional (ORM) básico y facilita trabajar con los datos como si fueran objetos JSON. La consulta SQL incluye explícitamente `ORDER BY nombre ASC` para cumplir con el requisito de ordenación.

3.  **Seguridad en la búsqueda:** El punto más crítico fue el método `buscar_pez`. Sabía que concatenar cadenas directamente en la consulta (ej. `"SELECT ... WHERE nombre LIKE '" + variable + "'"`) es una vulnerabilidad grave de inyección SQL. Por eso, utilicé **consultas preparadas**. Definí el marcador de posición `%s` en la sentencia SQL y pasé el parámetro como una tupla separada `(patron,)` al método `execute`. Esto obliga al driver de la base de datos a tratar la entrada estrictamente como datos literales y no como código ejecutable, garantizando la seguridad de mi aplicación de pesca.

### 💻 Código de programación

```python
import mysql.connector

class Pescador:
    def __init__(self):
        # Inicializo las credenciales como atributos de la instancia
        self.host = "localhost"
        self.user = "pesca_user"
        self.password = "pescador123"
        self.database = "peces_capturados"
        self.conexion = None

    def conectar(self):
        # Establezco la conexión dentro de un bloque try-except para manejo de errores
        try:
            self.conexion = mysql.connector.connect(
                host=self.host,
                user=self.user,
                password=self.password,
                database=self.database
            )
            print("Conexión exitosa a la base de datos.")
        except mysql.connector.Error as err:
            print(f"Error crítico al conectar: {err}")

    def listar_peces(self):
        # Verifico el estado de la conexión antes de operar
        if self.conexion and self.conexion.is_connected():
            # dictionary=True mapea los resultados a diccionarios (simulando objetos/JSON)
            cursor = self.conexion.cursor(dictionary=True)
            sql = "SELECT * FROM peces ORDER BY nombre ASC"
            cursor.execute(sql)
            resultados = cursor.fetchall()
            
            print("\n--- Lista de Peces Capturados (Ordenados) ---")
            for pez in resultados:
                print(pez) # Cada 'pez' es un diccionario con sus atributos
            
            cursor.close()
            return resultados
        else:
            print("Error: No hay conexión activa para listar.")
            return []

    def buscar_pez(self, nombre_parcial):
        if self.conexion and self.conexion.is_connected():
            cursor = self.conexion.cursor(dictionary=True)
            # CONSULTA PARAMETRIZADA: Uso %s para delegar el escapado de datos al conector
            sql = "SELECT * FROM peces WHERE nombre LIKE %s"
            # Preparo el patrón con comodines para búsqueda parcial
            patron = f"%{nombre_parcial}%"
            # Paso el parámetro como tupla para evitar Inyección SQL
            cursor.execute(sql, (patron,))
            resultados = cursor.fetchall()
            
            print(f"\n--- Resultados de búsqueda segura para '{nombre_parcial}' ---")
            for pez in resultados:
                print(pez)
                
            cursor.close()
            return resultados
        else:
            print("Error: No hay conexión activa para buscar.")
            return []

    def cerrar(self):
        # Cierre ordenado de recursos
        if self.conexion and self.conexion.is_connected():
            self.conexion.close()
            print("\nConexión cerrada correctamente.")

# --- Bloque de ejecución principal para demostrar funcionalidad ---
if __name__ == "__main__":
    mi_pescador = Pescador()
    mi_pescador.conectar()
    
    # 1. Listado completo
    mi_pescador.listar_peces()
    
    # 2. Búsqueda parcial segura
    mi_pescador.buscar_pez("trucha")
    
    mi_pescador.cerrar()
```

### 📊 Rúbrica de evaluación cumplida (Detalle)

A continuación, detallo cómo mi ejercicio cumple escrupulosamente con cada punto de la rúbrica:

1.  **Conexión a la Base de Datos (25%):**
    *   **Cumplimiento:** He creado una clase `Pescador` dedicada.
    *   **Detalle:** No me limité a poner las credenciales variables sueltas; las encapsulé en el constructor `__init__`. El manejo de errores de conexión (`mysql.connector.Error`) asegura que si el servidor MySQL está caído o las credenciales cambian, el programa informa al usuario limpiamente en lugar de lanzar una traza de error incomprensible. Esto demuestra comprensión del ciclo de vida de la conexión.

2.  **Listado de Peces Capturados (25%):**
    *   **Cumplimiento:** Implementé el método `listar_peces`.
    *   **Detalle:** La rúbrica pedía "orden correcto" y "formato JSON/objeto". Usé la cláusula SQL `ORDER BY nombre ASC` para garantizar el orden alfabético desde el motor de base de datos (más eficiente que ordenar en Python). Además, al configurar `cursor(dictionary=True)`, cada fila se convierte automáticamente en una estructura clave-valor (compatible con JSON), cumpliendo el requisito de representar cada pez con sus atributos nombrados.

3.  **Búsqueda por Especie (25%):**
    *   **Cumplimiento:** Método `buscar_pez` con búsqueda parcial.
    *   **Detalle técnico:** La búsqueda parcial requiere el operador `LIKE`. Lo más importante aquí es la seguridad. En lugar de concatenar el string, usé **Prepared Statements** (parámetros `%s`). Esto filtra cualquier intento de inyección SQL, protegiendo mi base de datos de ataques malintencionados, lo cual es vital en cualquier desarrollo profesional.

4.  **Cierre/Conclusión y Contexto (25%):**
    *   **Cumplimiento:** Aplicación en contexto real.
    *   **Detalle:** El código no es un script abstracto; está estructurado como una herramienta útil para un hobby real (la pesca). Permite al usuario (el pescador) consultar rápidamente qué especies tiene registradas o buscar una concreta si no recuerda el nombre exacto. La estructura de clase `Pescador` permite que este código se pueda reutilizar o ampliar fácilmente en el futuro (por ejemplo, para añadir un método `insertar_captura`), demostrando visión de desarrollo de software escalable.

### 🧾 Cierre
Este ejercicio me ha permitido consolidar mis conocimientos sobre el acceso a datos. He aprendido que no basta con que el código "funcione"; debe ser robusto (try-except) y seguro (consultas parametrizadas). Como aficionado a la pesca, veo claramente la utilidad de este software: podría llevarlo en un portátil o Raspberry Pi para llevar mi registro de capturas al día, asegurándome de que mis datos están ordenados y seguros. Misión cumplida.
