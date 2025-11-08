# Introducción breve y contextualización

Soy Francisco José, un programador apasionado por la pesca y la caza. Me encanta documentar mis experiencias para compartirlas con amigos. Para esto, he elegido MongoDB porque es una base de datos documental que me permite almacenar información de manera flexible y eficiente, sin la rigidez de las bases de datos relacionales.

# Desarrollo detallado y preciso

MongoDB es una base de datos NoSQL que utiliza documentos en formato BSON para almacenar datos. Es ideal para datos no estructurados o semi-estructurados, como mis experiencias de pesca.

En este ejercicio, realizo las siguientes operaciones:

1. Crear la base de datos 'pesca'.
2. Insertar tres documentos en la colección 'experiencias' con campos fecha, lugar y peces_capturados.
3. Seleccionar y mostrar todos los documentos.
4. Actualizar la fecha de una experiencia específica.
5. Eliminar un documento por fecha.

Uso terminología como colección (equivalente a tabla), documento (equivalente a fila), y operaciones CRUD (Create, Read, Update, Delete).

# Aplicación práctica

Aquí aplico los conceptos con un ejemplo real. He creado un script en JavaScript usando Node.js y el driver de MongoDB. Todo está comentado paso a paso como si lo estuviera haciendo yo mismo.

```javascript
const MongoClient = require('mongodb').MongoClient;

// Aquí defino la URL de conexión, usando una variable de entorno para no hardcodear
const url = process.env.MONGODB_URL || 'mongodb://localhost:27017';

async function main() {
    const client = new MongoClient(url);
    
    try {
        // Me conecto a MongoDB
        await client.connect();
        console.log('Conectado a MongoDB');
        
        // Creo la base de datos 'pesca' accediendo a ella
        const db = client.db('pesca');
        
        // Inserto tres experiencias en la colección 'experiencias'
        const experiencias = [
            { fecha: "2023-10-05", lugar: "Lago de Utrera", peces_capturados: ["Peixe Rojo", "Salmon"] },
            { fecha: "2023-09-15", lugar: "Río Guadalquivir", peces_capturados: ["Trucha", "Carpa"] },
            { fecha: "2023-09-01", lugar: "Embalse de Zahara", peces_capturados: ["Lucio", "Black Bass"] }
        ];
        await db.collection('experiencias').insertMany(experiencias);
        console.log('Insertadas las experiencias');
        
        // Selecciono y muestro todas las experiencias
        const allExperiencias = await db.collection('experiencias').find({}).toArray();
        console.log('Todas las experiencias:', allExperiencias);
        
        // Actualizo la fecha donde lugar es "Lago de Utrera"
        await db.collection('experiencias').updateOne(
            { lugar: "Lago de Utrera" },
            { $set: { fecha: "2023-10-06" } }
        );
        console.log('Actualizada la fecha de Lago de Utrera');
        
        // Elimino la experiencia donde fecha es "2023-09-01"
        await db.collection('experiencias').deleteOne({ fecha: "2023-09-01" });
        console.log('Eliminada la experiencia del 2023-09-01');
        
    } catch (error) {
        console.error('Error:', error);
    } finally {
        // Cierro la conexión
        await client.close();
        console.log('Conexión cerrada');
    }
}

// Ejecuto la función principal
main().catch(console.error);
```

Errores comunes: Olvidar cerrar la conexión, usar fechas sin comillas, no manejar errores. Para evitarlos, siempre uso try-catch y verifico la sintaxis.

# Conclusión breve

He completado el ejercicio practicando operaciones básicas en MongoDB, lo que refuerza mis habilidades en bases de datos documentales. Esto se relaciona con la unidad de acceso a datos, donde aprendemos a manejar datos no relacionales de forma eficiente.
