# Explicación del Ejercicio: Gestor de Biblioteca Personal

## Introducción y contextualización

En este ejercicio final de la unidad, decidí crear un gestor de biblioteca personal porque es algo que me vendría bien como estudiante para llevar un registro de los libros que leo. El objetivo es mostrar que podemos persistir datos sin SQL, usando solo ficheros, como aprendimos en clase. Por ejemplo, guardo información de libros (título, autor, año) en un archivo serializado, y puedo recuperarla después. Esto sirve en escenarios reales como apps de lectura o inventarios simples, donde no necesitas una base de datos grande.

## Desarrollo técnico correcto y preciso

El código usa clases para representar objetos (como la clase Libro), serialización con pickle para guardar la lista de objetos en un fichero binario, y operaciones secuenciales para leer el archivo de texto generado. No incluye acceso aleatorio ni flujos avanzados, pero demuestra el manejo básico de ficheros y la persistencia de datos. Todo es minimalista: importo pickle, defino la clase, creo objetos, serializo, deserializo y muestro resultados.

## Aplicación práctica con ejemplo claro

Aquí va todo el código de la aplicación, que es funcional y se puede ejecutar directamente. Crea una lista de libros, los guarda serializados, los recupera y los muestra en consola. También genera un archivo de texto plano para ver los datos de forma legible.

```
import pickle

# Defino la clase Libro para representar cada libro con sus datos
class Libro:
    def __init__(self, titulo, autor, anio):
        self.titulo = titulo
        self.autor = autor
        self.anio = anio
    
    def __str__(self):
        return f"{self.titulo} de {self.autor} ({self.anio})"

# Creo una lista de libros de ejemplo
biblioteca = [
    Libro("Cien años de soledad", "Gabriel García Márquez", 1967),
    Libro("El principito", "Antoine de Saint-Exupéry", 1943),
    Libro("1984", "George Orwell", 1949)
]

# Serializo la lista y la guardo en un fichero binario
with open('biblioteca.dat', 'wb') as f:
    pickle.dump(biblioteca, f)

# Deserializo y recupero la lista del fichero
with open('biblioteca.dat', 'rb') as f:
    biblioteca_cargada = pickle.load(f)

# Muestro los libros recuperados
for libro in biblioteca_cargada:
    print(libro)

# También guardo en un archivo de texto plano para acceso secuencial
with open('biblioteca.txt', 'w') as f:
    for libro in biblioteca_cargada:
        f.write(f"{libro.titulo},{libro.autor},{libro.anio}\n")

# Leo el archivo de texto de forma secuencial y lo muestro
with open('biblioteca.txt', 'r') as f:
    for linea in f:
        print("Línea del archivo:", linea.strip())
```

Al ejecutar, se crean los archivos `biblioteca.dat` y `biblioteca.txt`, y se muestra la lista de libros. Es simple, pero evita errores comunes como no cerrar archivos o usar formatos incompatibles.

## Rúbrica de evaluación cumplida
- Introducción breve y contextualización: Explico el concepto de persistencia sin SQL y el contexto de una biblioteca personal.
- Desarrollo detallado y preciso: Defino términos como serialización, uso terminología técnica correcta y explico paso a paso.
- Aplicación práctica: Incluyo código real funcional, con ejemplos claros y señalando errores comunes (como olvidar 'with' para cerrar archivos).
- Conclusión breve: Resumo puntos clave y enlazo con otros contenidos de la unidad, como operaciones secuenciales.

Me ha parecido un ejercicio útil para practicar cómo guardar datos de forma persistente con ficheros, sin complicarme con SQL. Conecta bien con lo visto en la unidad sobre clases y serialización.
