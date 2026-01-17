import chromadb
import ollama

def main():
    print("--- Inicio de la Práctica de Búsqueda Semántica ---")

    # 1. Conexión a ChromaDB
    # Usamos PersistentClient para guardar los datos en disco localmente
    print("Conectando a ChromaDB...")
    client = chromadb.PersistentClient(path="./chroma_db")
    
    # Creamos (o recuperamos) una colección. 
    # 'cosine' es la distancia por defecto en muchas configuraciones, pero explicitamos si fuera necesario.
    # Chroma maneja la distancia automáticamente si no se especifica, usando L2 usualmente, 
    # pero para similitud semántica la distancia coseno es ideal. 
    # Nota: Chroma usa 'metadata={"hnsw:space": "cosine"}' al crear la colección para esto.
    collection = client.get_or_create_collection(
        name="frases_celebres",
        metadata={"hnsw:space": "cosine"}
    )
    
    # 2. Datos de ejemplo (Si la colección está vacía)
    if collection.count() == 0:
        print("La colección está vacía. Insertando frases de ejemplo...")
        frases = [
            "La inteligencia artificial transformará el mundo",
            "El aprendizaje automático es una rama de la IA",
            "Me encanta programar en Python por su sencillez",
            "Los gatos son animales muy independientes",
            "El ejercicio físico es vital para la salud"
        ]
        
        ids = []
        embeddings = []
        documents = []
        
        for i, frase in enumerate(frases):
            print(f"Generando embedding para: {frase}")
            # Generamos el embedding usando Ollama (modelo llama3 o el que tengas descargado)
            # Asegúrate de tener el modelo corriendo o disponible: `ollama pull llama3`
            response = ollama.embeddings(model='llama3', prompt=frase)
            
            ids.append(str(i))
            embeddings.append(response['embedding'])
            documents.append(frase)
            
        collection.add(
            ids=ids,
            embeddings=embeddings,
            documents=documents
        )
        print("Frases insertadas correctamente.")
    else:
        print(f"La colección ya contiene {collection.count()} documentos.")

    # 3. Introducir nueva frase para comparar
    frase_nueva = "Python es genial para ciencia de datos"
    print(f"\nFrase nueva a comparar: '{frase_nueva}'")
    
    # 4. Generar embedding de la nueva frase
    print("Generando embedding de consulta...")
    response_query = ollama.embeddings(model='llama3', prompt=frase_nueva)
    embedding_query = response_query['embedding']
    
    # 5. Buscar coincidencias
    print("Buscando las 3 frases más similares...")
    results = collection.query(
        query_embeddings=[embedding_query],
        n_results=3
    )
    
    # 6. Mostrar resultados
    print("\nResultados de similitud (Distancia Coseno - menor es mejor):")
    # results['documents'] es una lista de listas (una por cada query)
    for i in range(len(results['documents'][0])):
        doc = results['documents'][0][i]
        dist = results['distances'][0][i]
        print(f"{i+1}. Frase: '{doc}' | Distancia: {dist:.4f}")

if __name__ == "__main__":
    main()
