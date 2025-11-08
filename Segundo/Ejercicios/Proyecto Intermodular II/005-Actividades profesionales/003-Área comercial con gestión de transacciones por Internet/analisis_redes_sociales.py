# Script para análisis básico de datos de redes sociales
# Autor: Fran

def analizar_datos_redes_sociales(archivo_datos):
    """
    Función que analiza datos de redes sociales desde un archivo de texto
    y genera estadísticas básicas sobre el engagement.
    """
    try:
        with open(archivo_datos, 'r', encoding='utf-8') as archivo:
            lineas = archivo.readlines()
            
            # Inicializar contadores
            total_publicaciones = 0
            total_likes = 0
            total_comentarios = 0
            total_shares = 0
            publicaciones_destacadas = []
            
            # Procesar cada línea (cada publicación)
            for linea in lineas[1:]:  # Omitir encabezado
                datos = linea.strip().split(',')
                if len(datos) >= 4:
                    total_publicaciones += 1
                    likes = int(datos[1])
                    comentarios = int(datos[2])
                    shares = int(datos[3])
                    
                    total_likes += likes
                    total_comentarios += comentarios
                    total_shares += shares
                    
                    # Identificar publicaciones con alto engagement
                    engagement = likes + comentarios + shares
                    if engagement > 100:
                        publicaciones_destacadas.append(datos[0])
            
            # Calcular promedios
            if total_publicaciones > 0:
                promedio_likes = total_likes / total_publicaciones
                promedio_comentarios = total_comentarios / total_publicaciones
                promedio_shares = total_shares / total_publicaciones
            else:
                promedio_likes = promedio_comentarios = promedio_shares = 0
            
            # Mostrar resultados
            print(f"Análisis de {total_publicaciones} publicaciones:")
            print(f"Promedio de likes: {promedio_likes:.2f}")
            print(f"Promedio de comentarios: {promedio_comentarios:.2f}")
            print(f"Promedio de shares: {promedio_shares:.2f}")
            print(f"Publicaciones destacadas: {len(publicaciones_destacadas)}")
            
            if publicaciones_destacadas:
                print("Publicaciones con mayor engagement:")
                for pub in publicaciones_destacadas[:3]:  # Mostrar solo las 3 primeras
                    print(f"  - {pub}")
            
            return {
                'total_publicaciones': total_publicaciones,
                'promedio_likes': promedio_likes,
                'promedio_comentarios': promedio_comentarios,
                'promedio_shares': promedio_shares,
                'publicaciones_destacadas': len(publicaciones_destacadas)
            }
    
    except FileNotFoundError:
        print(f"Error: No se encuentra el archivo {archivo_datos}")
        return None
    except Exception as e:
        print(f"Error al procesar los datos: {e}")
        return None

# Programa principal
if __name__ == "__main__":
    archivo = "datos_redes_sociales.csv"
    resultados = analizar_datos_redes_sociales(archivo)
    
    if resultados:
        print("\nAnálisis completado. Los datos han sido procesados correctamente.")