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