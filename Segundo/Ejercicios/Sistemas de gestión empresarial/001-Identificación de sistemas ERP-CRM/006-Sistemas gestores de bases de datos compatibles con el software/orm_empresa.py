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