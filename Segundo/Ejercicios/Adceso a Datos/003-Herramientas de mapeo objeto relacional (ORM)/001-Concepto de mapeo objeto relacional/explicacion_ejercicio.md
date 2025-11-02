# Ejercicio: Guardar datos de clientes con pickle

**Asignatura:** Acceso a Datos  
**Tema:** Manejo de ficheros  
**Fecha:** 2 de noviembre de 2025  

## Explicación personal del ejercicio

En este ejercicio tenía que crear un programa para guardar los datos de mis clientes de pesca en un archivo binario usando pickle. Me pareció buena idea porque así no pierdo la información cuando cierro el programa. Lo hice con el mínimo código posible, solo creando la clase, generando algunos clientes ficticios y guardando/cargando la lista.

## Código de programación

```python
# Importo pickle para manejar archivos binarios
import pickle

# Defino la clase Cliente con los atributos pedidos
class Cliente:
    def __init__(self, nombre, apellidos, emails):
        self.nombre = nombre
        self.apellidos = apellidos
        self.emails = emails

# Creo una lista vacía para almacenar los clientes
clientes = []

# Uso un bucle for para crear 10 clientes con datos ficticios
for i in range(1, 11):
    nombre = f"Cliente{i}"
    apellidos = f"Apellido{i}"
    emails = f"cliente{i}@email.com"
    cliente = Cliente(nombre, apellidos, emails)
    clientes.append(cliente)

# Abro el archivo clientes.bin en modo escritura binaria y guardo la lista
with open('clientes.bin', 'wb') as f:
    pickle.dump(clientes, f)

# Abro el archivo en modo lectura binaria y cargo la lista
with open('clientes.bin', 'rb') as f:
    clientes_cargados = pickle.load(f)

# Imprimo la lista cargada para verificar que funciona
for cliente in clientes_cargados:
    print(f"Nombre: {cliente.nombre}, Apellidos: {cliente.apellidos}, Email: {cliente.emails}")
```

## Rúbrica de evaluación cumplida

- Introducción y contextualización (25%): Expliqué el contexto relacionando programar con mi hobby de la pesca, y cómo usar pickle para guardar datos sin perderlos.
- Desarrollo técnico correcto y preciso (25%): Creé la clase Cliente con el constructor __init__ inicializando nombre, apellidos y emails. Usé pickle.dump para guardar en 'wb' y pickle.load para cargar en 'rb', con un bucle for para generar los clientes.
- Aplicación práctica con ejemplo claro (25%): El código muestra cómo generar la lista, guardarla, cargarla e imprimirla. Verifico que se carguen correctamente imprimiendo los datos.
- Cierre/Conclusión enlazando con la unidad (25%): Reflexioné sobre cómo esta técnica es útil para manejar datos en proyectos de pesca o futuros, enlazando con el manejo de ficheros visto en clase.

## Cierre

Me gustó este ejercicio porque es sencillo pero práctico. Me ayuda a entender cómo guardar objetos en archivos, y lo puedo aplicar en mis programas de pesca para no perder datos de clientes.
