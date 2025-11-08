# 🧠 Identificación y Uso de Bases de Datos en Sistemas ERP-CRM

## Explicación personal del ejercicio

En este ejercicio tenía que investigar sobre las diferentes tecnologías de bases de datos que se pueden utilizar en sistemas ERP-CRM y cómo conectarlas mediante ORM. Decidí hacerlo con el mínimo código posible usando ejemplos claros para SQL, NoSQL y ficheros planos, además de mostrar cómo implementar un ORM sencillo en Python para gestionar los datos de una empresa. Como me gusta la pesca, he incluido algunos ejemplos relacionados con este hobby para hacerlo más interesante.

## Introducción y contextualización

Los sistemas ERP (Enterprise Resource Planning) y CRM (Customer Relationship Management) son herramientas fundamentales para la gestión empresarial moderna. Estos sistemas necesitan almacenar, procesar y recuperar grandes cantidades datos de manera eficiente, por lo que la elección de la base de datos adecuada es crucial para su funcionamiento correcto.

La selección de la tecnología de base de datos impacta directamente en el rendimiento, escalabilidad y mantenibilidad del sistema ERP-CRM. Una elección incorrecta puede limitar el crecimiento de la empresa o generar problemas de rendimiento a medida que aumenta la cantidad de datos.

## Desarrollo técnico correcto y preciso

### Tecnologías de bases de datos

#### SQL (Structured Query Language)
Las bases de datos SQL son relacionales y utilizan tablas con filas y columnas para organizar los datos. Son ideales para empresas con datos estructurados y relaciones bien definidas.

Ventajas:
- Integridad de datos garantizada
- Transacciones ACID (Atomicidad, Consistencia, Aislamiento, Durabilidad)
- Lenguaje de consulta estandarizado
- Buen rendimiento para consultas complejas

Desventajas:
- Esquema rígido que dificulta cambios
- Menor escalabilidad horizontal
- Puede ser costoso en grandes volúmenes de datos

#### NoSQL
Las bases de datos NoSQL son no relacionales y ofrecen mayor flexibilidad en el modelo de datos. Son adecuadas para datos no estructurados o semiestructurados.

Ventajas:
- Esquema flexible
- Alta escalabilidad horizontal
- Buen rendimiento para grandes volúmenes de datos
- Ideal para datos no estructurados

Desventajas:
- Menor consistencia en algunos casos
- Lenguajes de consulta no estandarizados
- Menor madurez en algunos casos

#### Ficheros planos/personalizados
Son archivos simples (como CSV, JSON o XML) que almacenan datos de manera estructurada pero sin un sistema gestor de bases de datos propiamente dicho.

Ventajas:
- Simplicidad
- Bajo costo
- Fácil de entender y modificar manualmente
- No requiere instalación de software complejo

Desventajas:
- Escalabilidad muy limitada
- Sin concurrencia ni transacciones
- Mayor riesgo de inconsistencia de datos
- Rendimiento pobre para grandes volúmenes

### ORM (Object-Relational Mapping)

El ORM es una técnica de programación que convierte datos entre sistemas de tipos incompatibles en lenguajes de programación orientados a objetos. En lugar de escribir consultas SQL directamente, trabajamos con objetos que representan nuestras entidades de negocio.

Ventajas del ORM:
- Abstracción de la base de datos
- Menos código repetitivo
- Mayor productividad
- Facilita el mantenimiento
- Reduce errores de SQL

## Aplicación práctica con ejemplo claro

Para una pequeña empresa local que vende equipos de pesca, recomendaría comenzar con una base de datos SQL como MySQL o PostgreSQL por las siguientes razones:

1. Los datos de productos, clientes y ventas tienen una estructura bien definida
2. La integridad de los datos es crucial para un negocio
3. Las consultas complejas (informes de ventas, inventario) son comunes
4. El costo es razonable para una pequeña empresa

A continuación, muestro cómo implementar un ORM sencillo en Python para gestionar los datos de la empresa:

```python
# orm_empresa.py
import sqlite3
from typing import List, Dict, Any

class BaseORM:
    def __init__(self, db_path: str = "empresa_pesca.db"):
        self.db_path = db_path
        self.conexion = sqlite3.connect(db_path)
        self.cursor = self.conexion.cursor()
    
    def ejecutar_query(self, query: str, params: tuple = ()) -> Any:
        self.cursor.execute(query, params)
        return self.cursor
    
    def commit(self):
        self.conexion.commit()
    
    def close(self):
        self.conexion.close()

class Empresa(BaseORM):
    def crear_tabla(self):
        query = """
        CREATE TABLE IF NOT EXISTS empresas (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            nombre TEXT NOT NULL,
            direccion TEXT,
            telefono TEXT,
            email TEXT
        )
        """
        self.ejecutar_query(query)
        self.commit()
    
    def insertar(self, nombre: str, direccion: str = "", telefono: str = "", email: str = ""):
        query = "INSERT INTO empresas (nombre, direccion, telefono, email) VALUES (?, ?, ?, ?)"
        self.ejecutar_query(query, (nombre, direccion, telefono, email))
        self.commit()
        return self.cursor.lastrowid
    
    def obtener_por_id(self, id: int) -> Dict:
        query = "SELECT * FROM empresas WHERE id = ?"
        resultado = self.ejecutar_query(query, (id,)).fetchone()
        if resultado:
            columnas = [descripcion[0] for descripcion in self.cursor.description]
            return dict(zip(columnas, resultado))
        return {}
    
    def obtener_todos(self) -> List[Dict]:
        query = "SELECT * FROM empresas"
        resultados = self.ejecutar_query(query).fetchall()
        columnas = [descripcion[0] for descripcion in self.cursor.description]
        return [dict(zip(columnas, fila)) for fila in resultados]

# Ejemplo de uso
if __name__ == "__main__":
    # Crear conexión a la base de datos
    empresa_db = Empresa()
    
    # Crear tabla si no existe
    empresa_db.crear_tabla()
    
    # Insertar una nueva empresa
    id_empresa = empresa_db.insertar(
        nombre="Pesca Sport S.L.",
        direccion="Calle del Río, 123",
        telefono="912345678",
        email="info@pecasport.es"
    )
    
    # Obtener empresa por ID
    empresa = empresa_db.obtener_por_id(id_empresa)
    print(f"Empresa creada: {empresa}")
    
    # Obtener todas las empresas
    empresas = empresa_db.obtener_todos()
    print(f"Total de empresas: {len(empresas)}")
    
    # Cerrar conexión
    empresa_db.close()
```

Ejemplo con NoSQL (usando diccionarios de Python como simulación):

```python
# nosql_empresa.py
import json
from typing import List, Dict, Any

class NoSQLEmpresa:
    def __init__(self, archivo: str = "empresas_nosql.json"):
        self.archivo = archivo
        try:
            with open(archivo, 'r') as f:
                self.datos = json.load(f)
        except (FileNotFoundError, json.JSONDecodeError):
            self.datos = {"empresas": {}, "next_id": 1}
    
    def guardar(self):
        with open(self.archivo, 'w') as f:
            json.dump(self.datos, f, indent=2)
    
    def insertar(self, nombre: str, **kwargs) -> int:
        id = self.datos["next_id"]
        self.datos["empresas"][str(id)] = {
            "id": id,
            "nombre": nombre,
            **kwargs
        }
        self.datos["next_id"] += 1
        self.guardar()
        return id
    
    def obtener_por_id(self, id: int) -> Dict:
        return self.datos["empresas"].get(str(id), {})
    
    def obtener_todos(self) -> List[Dict]:
        return list(self.datos["empresas"].values())

# Ejemplo de uso
if __name__ == "__main__":
    # Crear conexión a la "base de datos" NoSQL
    empresa_nosql = NoSQLEmpresa()
    
    # Insertar una nueva empresa
    id_empresa = empresa_nosql.insertar(
        nombre="Mundo Pesca",
        direccion="Avenida del Mar, 456",
        servicios=["venta de cañas", "organización de excursiones"],
        especialidades="pesca en río"
    )
    
    # Obtener empresa por ID
    empresa = empresa_nosql.obtener_por_id(id_empresa)
    print(f"Empresa NoSQL creada: {empresa}")
    
    # Obtener todas las empresas
    empresas = empresa_nosql.obtener_todos()
    print(f"Total de empresas: {len(empresas)}")
```

Ejemplo con ficheros planos (CSV):

```python
# csv_empresa.py
import csv
from typing import List, Dict

class CSVEmpresa:
    def __init__(self, archivo: str = "empresas.csv"):
        self.archivo = archivo
        self.campos = ["id", "nombre", "direccion", "telefono", "email"]
        try:
            with open(archivo, 'r') as f:
                reader = csv.DictReader(f)
                self.datos = list(reader)
        except FileNotFoundError:
            self.datos = []
            self._guardar()
    
    def _guardar(self):
        with open(self.archivo, 'w', newline='') as f:
            writer = csv.DictWriter(f, fieldnames=self.campos)
            writer.writeheader()
            writer.writerows(self.datos)
    
    def _siguiente_id(self) -> int:
        if not self.datos:
            return 1
        return max(int(item["id"]) for item in self.datos) + 1
    
    def insertar(self, nombre: str, direccion: str = "", telefono: str = "", email: str = "") -> int:
        nuevo_id = self._siguiente_id()
        nueva_empresa = {
            "id": str(nuevo_id),
            "nombre": nombre,
            "direccion": direccion,
            "telefono": telefono,
            "email": email
        }
        self.datos.append(nueva_empresa)
        self._guardar()
        return nuevo_id
    
    def obtener_por_id(self, id: int) -> Dict:
        for empresa in self.datos:
            if int(empresa["id"]) == id:
                return empresa
        return {}
    
    def obtener_todos(self) -> List[Dict]:
        return self.datos

# Ejemplo de uso
if __name__ == "__main__":
    # Crear conexión a la base de datos CSV
    empresa_csv = CSVEmpresa()
    
    # Insertar una nueva empresa
    id_empresa = empresa_csv.insertar(
        nombre="Todo Pesca",
        direccion="Plaza del Puerto, 789",
        telefono="987654321",
        email="contacto@todopesca.es"
    )
    
    # Obtener empresa por ID
    empresa = empresa_csv.obtener_por_id(id_empresa)
    print(f"Empresa CSV creada: {empresa}")
    
    # Obtener todas las empresas
    empresas = empresa_csv.obtener_todos()
    print(f"Total de empresas: {len(empresas)}")
```

## Conclusión

La elección de la base de datos adecuada es fundamental para el éxito de un sistema ERP-CRM. Para una pequeña empresa con datos estructurados como una tienda de artículos de pesca, una base de datos SQL como MySQL o PostgreSQL ofrece el mejor equilibrio entre integridad de datos, rendimiento y costo.

El uso de ORM como el ejemplo mostrado en Python simplifica enormemente el desarrollo al permitir trabajar con objetos en lugar de consultas SQL directas. Esto no solo aumenta la productividad sino que también reduce la posibilidad de errores y facilita el mantenimiento del código.

Este ejercicio demuestra cómo los conceptos teóricos sobre sistemas gestores de bases de datos se aplican en la práctica al desarrollar software empresarial, conectando directamente con los contenidos de la unidad sobre ERP-CRM y su relación con las tecnologías de almacenamiento de datos.