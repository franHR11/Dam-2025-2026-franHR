const MongoClient = require('mongodb').MongoClient;

const url = process.env.MONGODB_URL || 'mongodb://localhost:27017';

async function main() {
    const client = new MongoClient(url);
    
    try {
        await client.connect();
        
        const db = client.db('pesca');
        
        const experiencias = [
            { fecha: "2023-10-05", lugar: "Lago de Utrera", peces_capturados: ["Peixe Rojo", "Salmon"] },
            { fecha: "2023-09-15", lugar: "Río Guadalquivir", peces_capturados: ["Trucha", "Carpa"] },
            { fecha: "2023-09-01", lugar: "Embalse de Zahara", peces_capturados: ["Lucio", "Black Bass"] }
        ];
        await db.collection('experiencias').insertMany(experiencias);
        
        const allExperiencias = await db.collection('experiencias').find({}).toArray();
        console.log(allExperiencias);
        
        await db.collection('experiencias').updateOne(
            { lugar: "Lago de Utrera" },
            { $set: { fecha: "2023-10-06" } }
        );
        
        await db.collection('experiencias').deleteOne({ fecha: "2023-09-01" });
        
    } finally {
        await client.close();
    }
}

main().catch(console.error);
