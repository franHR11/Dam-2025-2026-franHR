"""
GestorCSV - Clase para manejar archivos CSV

Esta clase permite escribir y leer tuplas en archivos CSV sin utilizar librerías externas.
Author: FranHR
Date: 2025
"""

class GestorCSV:
    """
    Clase para gestionar operaciones básicas con archivos CSV.
    Permite escribir y leer tuplas de datos en formato CSV.
    """
    
    def __init__(self, nombre_archivo):
        """
        Constructor de la clase GestorCSV.
        
        Args:
            nombre_archivo (str): Nombre del archivo CSV a gestionar
        """
        self.nombre_archivo = nombre_archivo
    
    def escribir(self, tupla_datos):
        """
        Escribe una tupla de datos en el archivo CSV.
        Cada elemento de la tupla se separará por comas.
        
        Args:
            tupla_datos (tuple): Tupla con los datos a escribir en el CSV
            
        Returns:
            bool: True si la escritura fue exitosa, False en caso contrario
        """
        try:
            # Convertir cada elemento de la tupla a string y unir con comas
            linea_csv = ','.join(str(dato) for dato in tupla_datos)
            
            # Escribir en el archivo (modo append para añadir al final)
            with open(self.nombre_archivo, 'a', encoding='utf-8') as archivo:
                archivo.write(linea_csv + '\n')
            
            print(f"Datos escritos correctamente en {self.nombre_archivo}")
            return True
            
        except Exception as e:
            print(f"Error al escribir en el archivo: {e}")
            return False
    
    def leer(self):
        """
        Lee la primera línea del archivo CSV y la devuelve como tupla.
        
        Returns:
            tuple: Tupla con los datos leídos del archivo CSV
            None: Si hay un error al leer el archivo
        """
        try:
            # Leer el archivo (modo lectura)
            with open(self.nombre_archivo, 'r', encoding='utf-8') as archivo:
                # Leer la primera línea
                primera_linea = archivo.readline().strip()
                
                # Si el archivo está vacío
                if not primera_linea:
                    print("El archivo está vacío")
                    return ()
                
                # Separar por comas y convertir a tupla
                datos = tuple(primera_linea.split(','))
                return datos
                
        except FileNotFoundError:
            print(f"Error: El archivo {self.nombre_archivo} no existe")
            return None
        except Exception as e:
            print(f"Error al leer el archivo: {e}")
            return None
    
    def leer_todas_lineas(self):
        """
        Lee todas las líneas del archivo CSV y las devuelve como lista de tuplas.
        
        Returns:
            list: Lista de tuplas con los datos leídos del archivo CSV
            None: Si hay un error al leer el archivo
        """
        try:
            # Leer el archivo (modo lectura)
            with open(self.nombre_archivo, 'r', encoding='utf-8') as archivo:
                lineas = archivo.readlines()
                
                # Si el archivo está vacío
                if not lineas:
                    print("El archivo está vacío")
                    return []
                
                # Convertir cada línea a tupla
                datos = []
                for linea in lineas:
                    linea = linea.strip()
                    if linea:  # Ignorar líneas vacías
                        datos.append(tuple(linea.split(',')))
                
                return datos
                
        except FileNotFoundError:
            print(f"Error: El archivo {self.nombre_archivo} no existe")
            return None
        except Exception as e:
            print(f"Error al leer el archivo: {e}")
            return None