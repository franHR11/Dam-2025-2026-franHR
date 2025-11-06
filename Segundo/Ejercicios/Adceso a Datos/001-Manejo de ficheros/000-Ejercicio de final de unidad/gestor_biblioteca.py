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
