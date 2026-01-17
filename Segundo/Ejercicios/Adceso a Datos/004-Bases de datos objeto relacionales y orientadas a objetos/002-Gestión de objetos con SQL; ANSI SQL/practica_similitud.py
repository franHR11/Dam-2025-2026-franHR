import chromadb
import ollama

def main():
    print("--- Práctica de Búsqueda por Similitud con Ollama y ChromaDB ---")

    # 1. Conexión a la base de datos Chroma existente
    # Se utiliza PersistentClient para conectar a la base de datos guardada en local
    print("Conectando a la base de datos Chroma existente...")
    client = chromadb.PersistentClient(path="./chroma_db")

    # Recuperamos la colección 'frases_celebres' o creamos una nueva si no existe
    collection = client.get_or_create_collection(
        name="frases_celebres",
        metadata={"hnsw:space": "cosine"} # Importante: usar distancia coseno para similitud
    )

    # Si la colección está vacía, insertamos datos de ejemplo (como en la clase)
    if collection.count() == 0:
        print("La colección está vacía. Insertando frases iniciales...")
        frases_iniciales = [
            "La tecnología avanza a pasos agigantados",
            "La inteligencia artificial es el futuro de la computación",
            "Aprender a programar abre muchas puertas profesionales",
            "El clima está cambiando drásticamente en todo el mundo",
            "La lectura es fundamental para el desarrollo del pensamiento crítico"
        ]
        
        ids = []
        embeddings = []
        documents = []

        for i, frase in enumerate(frases_iniciales):
            print(f"Generando embedding para: '{frase}'")
            # Generamos embedding con Ollama (modelo llama3)
            response = ollama.embeddings(model='llama3', prompt=frase)
            
            ids.append(str(i))
            embeddings.append(response['embedding'])
            documents.append(frase)
        
        collection.add(ids=ids, embeddings=embeddings, documents=documents)
        print("Datos iniciales insertados.")

    # 3. Introducir una nueva frase para comparar
    nueva_frase = "Me interesa mucho el desarrollo de software y la IA"
    print(f"\nFrase nueva a comparar: '{nueva_frase}'")

    # 4. Generar embedding de la nueva frase
    print("Generando embedding de la frase nueva...")
    response_nueva = ollama.embeddings(model='llama3', prompt=nueva_frase)
    embedding_nuevo = response_nueva['embedding']

    # 5. Buscar coincidencias en la base de datos
    print("Buscando las 3 frases más similares en la base de datos...")
    resultados = collection.query(
        query_embeddings=[embedding_nuevo],
        n_results=3
    )

    # 6. Mostrar los resultados
    print("\n--- Resultados de la Búsqueda ---")
    # 'resultados' devuelve listas, iteramos sobre la primera (y única) query
    docs = resultados['documents'][0]
    distancias = resultados['distances'][0]

    for i in range(len(docs)):
        # La distancia coseno: cuanto más cercana a 0, más similar (en Chroma funciona así por defecto para cosine distance en algunas versiones, 
        # aunque matemáticamente cosine similarity es 1 para idénticos. Chroma devuelve 'distance', donde 0 es idéntico).
        print(f"Resultado {i+1}:")
        print(f"  Frase: '{docs[i]}'")
        print(f"  Distancia (similitud): {distancias[i]:.4f}")
        print("-" * 30)

if __name__ == "__main__":
    main()
