import os

class YourSQL:
    current_db = None  # Para recordar la base de datos seleccionada

    @staticmethod
    def peticion(consulta):
        consulta = consulta.strip().upper()  # Hago la consulta en mayúsculas para simplificar
        if consulta == "SHOW DATABASES;":
            # Listo las carpetas en db, que son las bases de datos
            try:
                bases = os.listdir('db')
                print("Bases de datos:")
                for base in bases:
                    if os.path.isdir(os.path.join('db', base)):
                        print(base)
            except FileNotFoundError:
                print("No se encontró la carpeta db")
        elif consulta.startswith("USE "):
            # Selecciono la base de datos
            db_name = consulta[4:-1].strip()  # Quito USE y ;
            if os.path.isdir(os.path.join('db', db_name)):
                YourSQL.current_db = db_name
                print(f"Base de datos '{db_name}' seleccionada")
            else:
                print(f"Base de datos '{db_name}' no existe")
        elif consulta == "SHOW TABLES;":
            # Listo los archivos en la base actual, que son las tablas
            if YourSQL.current_db:
                try:
                    tablas = os.listdir(os.path.join('db', YourSQL.current_db))
                    print("Tablas:")
                    for tabla in tablas:
                        if os.path.isfile(os.path.join('db', YourSQL.current_db, tabla)):
                            print(tabla.replace('.txt', ''))  # Quito .txt para mostrar solo nombre
                except FileNotFoundError:
                    print("Error al listar tablas")
            else:
                print("No hay base de datos seleccionada")
        elif consulta.startswith("INSERT INTO "):
            # Simulo insertar, añadiendo una línea al archivo tabla.txt
            partes = consulta[12:-1].split(" VALUES ")  # Separo tabla y valores
            if len(partes) == 2:
                tabla = partes[0].strip()
                valores = partes[1].strip()
                if YourSQL.current_db and os.path.isfile(os.path.join('db', YourSQL.current_db, tabla + '.txt')):
                    with open(os.path.join('db', YourSQL.current_db, tabla + '.txt'), 'a') as f:
                        f.write(valores + '\n')
                    print(f"Insertado en {tabla}")
                else:
                    print("Tabla no existe o no hay DB seleccionada")
            else:
                print("Sintaxis incorrecta para INSERT")
        else:
            print("Consulta no soportada")

# Pruebas
if __name__ == "__main__":
    YourSQL.peticion("SHOW DATABASES;")
    YourSQL.peticion("USE database1;")
    YourSQL.peticion("SHOW TABLES;")
    YourSQL.peticion("INSERT INTO table1 VALUES ('dato1', 'dato2');")
    YourSQL.peticion("INSERT INTO table1 VALUES ('dato3', 'dato4');")
