### 1.1 Introducción breve y contextualización

En este ejercicio nos adentramos en el mundo de las **Bases de Datos Vectoriales** y la **Inteligencia Artificial Generativa**. El objetivo es entender cómo las máquinas pueden "comprender" el significado semántico de las frases mediante el uso de **embeddings**.

Los embeddings son representaciones numéricas (vectores) de un texto, donde frases con significados similares tienen vectores cercanos matemáticamente. Para gestionar estos vectores de forma eficiente, utilizamos una base de datos vectorial como **ChromaDB**. Estas herramientas son fundamentales hoy en día para crear sistemas de búsqueda semántica, recomendadores y asistentes inteligentes que van más allá de la simple búsqueda por palabras clave.

### 2. Desarrollo detallado y preciso

Para realizar esta práctica, he seguido un proceso lógico paso a paso:

1.  **Generación de Embeddings con Ollama**:
    He utilizado **Ollama**, una herramienta que permite ejecutar modelos de lenguaje (LLMs) como `llama3` localmente. Mediante la función `ollama.embeddings`, convierto cualquier frase de texto en una lista de números (vector) que captura su esencia semántica.

2.  **Almacenamiento en ChromaDB**:
    He conectado con **ChromaDB** utilizando su funcionalidad de `PersistentClient`. Esto permite que los datos que guardamos no se pierdan al cerrar el programa, simulando una base de datos real. He creado una colección llamada `frases_celebres` configurada para usar la **distancia coseno**, que es la métrica estándar para medir similitud entre vectores (cuanto menor es la distancia, mayor es la similitud).

3.  **Proceso de Búsqueda**:
    El flujo de trabajo que he implementado en el script es el siguiente:
    *   Conecto a la base de datos.
    *   Si está vacía, inserto unas frases de ejemplo para tener datos con los que trabajar.
    *   Defino una "frase nueva" que actúa como mi consulta (query).
    *   Calculo el embedding de esta nueva frase usando el mismo modelo (`llama3`).
    *   Le pido a Chroma que busque en la colección los vectores más cercanos al vector de mi nueva frase.

### 3. Aplicación práctica

A continuación presento el código Python que he desarrollado para la práctica. He procurado que sea claro y directo, manejando la conexión, la generación de embeddings y la consulta.

He decidido usar frases relacionadas con tecnología y aprendizaje para ver cómo el sistema es capaz de relacionar conceptos como "programar" con "desarrollo de software" o "inteligencia artificial".

```python
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
        # La distancia coseno: cuanto más cercana a 0, más similar.
        print(f"Resultado {i+1}:")
        print(f"  Frase: '{docs[i]}'")
        print(f"  Distancia (similitud): {distancias[i]:.4f}")
        print("-" * 30)

if __name__ == "__main__":
    main()
```

#### Resultados Observados
Al ejecutar el script con la frase *"Me interesa mucho el desarrollo de software y la IA"*, el sistema me devolvió correctamente como resultados más cercanos las frases relacionadas con "inteligencia artificial" y "aprender a programar", dejando las frases sobre el clima o la lectura como menos relevantes (con mayor distancia). Esto confirma que la búsqueda semántica está funcionando.

### 4. Conclusión breve

Me ha parecido un ejercicio muy interesante para ver en la práctica cómo funciona el "cerebro" detrás de las aplicaciones modernas de IA. Es sorprendente ver cómo, con pocas líneas de código gracias a librerías como `ollama` y `chromadb`, podemos implementar un sistema capaz de "entender" lo que le preguntamos, algo que con bases de datos SQL tradicionales sería muy complejo de lograr usando solo `LIKE` o coincidencias exactas. Sin duda es una tecnología clave para el futuro del desarrollo de aplicaciones.
