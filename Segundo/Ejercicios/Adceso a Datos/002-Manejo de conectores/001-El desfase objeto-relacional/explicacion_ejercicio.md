# Explicación del Ejercicio: Introducción al Manejo de Conectores en Aplicaciones

## Introducción y Contextualización (25%)
Como alguien apasionado por la pesca, siempre he querido tener una forma organizada de registrar mis capturas. Este ejercicio me ayuda a entender cómo las aplicaciones pueden interactuar con bases de datos para almacenar información valiosa. Imagina que después de un día en el río, quiero guardar detalles como el nombre científico del pez, su peso, medida y la fecha de captura. Esto no solo me permite recordar mis mejores pescas, sino que también me enseña conceptos fundamentales de programación que puedo aplicar en mi hobby para crear una app personalizada de registro de pesca.

## Desarrollo Técnico Correcto y Preciso (25%)
En este script, he utilizado Python con la biblioteca mysql.connector para conectar a una base de datos MySQL. Primero, definí una clase `Pez` que representa la estructura de datos necesaria, con atributos para nombre científico, peso, medida y fecha de captura. Esto sigue buenas prácticas de programación orientada a objetos, manteniendo el código organizado y reutilizable.

Para la conexión a la base de datos, creé una función `conectar_bd()` que establece la conexión de manera segura, especificando el host, usuario, contraseña y base de datos. Luego, la función `insertar_pez()` maneja la inserción de datos usando consultas SQL preparadas con placeholders para evitar inyecciones SQL. El código cierra correctamente las conexiones y cursores para liberar recursos, lo que es esencial para el rendimiento y la estabilidad de la aplicación.

## Aplicación Práctica con Ejemplo Claro (25%)
El código completo explicado es el siguiente:

```
# Importo la biblioteca mysql.connector para poder conectar con la base de datos MySQL
import mysql.connector

# Defino una clase Pez para representar la información de cada pez capturado
# Esto me ayuda a organizar los datos de manera estructurada
class Pez:
    def __init__(self, nombre_cientifico, peso, medida, fecha_captura):
        self.nombre_cientifico = nombre_cientifico  # El nombre científico del pez
        self.peso = peso  # El peso en kilogramos
        self.medida = medida  # La medida en centímetros
        self.fecha_captura = fecha_captura  # La fecha cuando lo capturé

# Función para conectar a la base de datos
# Aquí configuro la conexión usando mis credenciales
def conectar_bd():
    return mysql.connector.connect(
        host="localhost",  # El servidor donde está mi base de datos
        user="root",  # Mi usuario de MySQL
        password="",  # Mi contraseña (vacía en este caso)
        database="pesca"  # La base de datos que uso para almacenar los peces
    )

# Función para insertar un pez en la base de datos
# Esta función toma un objeto Pez y lo guarda en la tabla 'peces'
def insertar_pez(pez):
    conexion = conectar_bd()  # Me conecto a la base de datos
    cursor = conexion.cursor()  # Creo un cursor para ejecutar consultas

    # Preparo la consulta SQL para insertar los datos
    # Uso placeholders (?) para evitar problemas de seguridad
    consulta = "INSERT INTO peces (nombre_cientifico, peso, medida, fecha_captura) VALUES (%s, %s, %s, %s)"
    valores = (pez.nombre_cientifico, pez.peso, pez.medida, pez.fecha_captura)

    cursor.execute(consulta, valores)  # Ejecuto la consulta con los valores
    conexion.commit()  # Confirmo los cambios en la base de datos

    cursor.close()  # Cierro el cursor
    conexion.close()  # Cierro la conexión

    print("He insertado el pez en la base de datos exitosamente.")  # Mensaje de confirmación

# Ejemplo de uso: creo un pez y lo inserto
# Esto es para probar que todo funciona
if __name__ == "__main__":
    # Creo un objeto Pez con datos de ejemplo
    mi_pez = Pez("Salmo trutta", 2.5, 45.0, "2023-08-15")
    insertar_pez(mi_pez)  # Lo inserto en la base de datos
```

Al ejecutar este código, obtendríamos un mensaje de confirmación de que el pez se insertó correctamente en la base de datos, asumiendo que la tabla 'peces' existe con las columnas adecuadas.
Para mostrar cómo funciona en la práctica, incluí un ejemplo al final del script. Creo un objeto `Pez` con datos reales: un salmón (Salmo trutta) de 2.5 kg, 45 cm de largo, capturado el 15 de agosto de 2023. Luego, llamo a la función `insertar_pez()` para guardarlo en la base de datos. Esto demuestra claramente cómo transformar datos de una captura real en registros persistentes que puedo consultar más tarde. En mi app de pesca, podría tener un formulario donde ingreso estos datos y automáticamente se guardan, permitiéndome llevar un diario digital de mis aventuras.

## Cierre/Conclusión Enlazando con la Unidad (25%)
Este ejercicio me ha enseñado los fundamentos del manejo de conectores en aplicaciones, específicamente cómo usar conectores de bases de datos para interactuar con MySQL desde Python. Los conceptos de conexión, consultas SQL y manejo de transacciones que aprendí aquí son cruciales para cualquier aplicación que necesite persistir datos. En el contexto de mi hobby de pesca, puedo aplicar esto para crear una aplicación que no solo registre capturas, sino que también analice tendencias, como qué especies pesco más o en qué épocas del año. Esto enlaza directamente con los temas de acceso a datos que estamos estudiando, mostrándome cómo la teoría se convierte en herramientas prácticas para resolver problemas reales en mi vida cotidiana.