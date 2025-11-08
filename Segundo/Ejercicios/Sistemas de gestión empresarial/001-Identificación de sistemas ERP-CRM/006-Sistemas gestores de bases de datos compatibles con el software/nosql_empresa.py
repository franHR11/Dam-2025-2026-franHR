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