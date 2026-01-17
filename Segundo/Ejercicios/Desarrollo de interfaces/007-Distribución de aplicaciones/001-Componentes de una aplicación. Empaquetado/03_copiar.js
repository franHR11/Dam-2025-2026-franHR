const fs = require('fs');

// Copiar el archivo creado anteriormente a uno nuevo
fs.copyFile('archivo_origen.txt', 'archivo_destino.txt', (err) => {
    if (err) throw err;
    console.log('El archivo se ha copiado correctamente a "archivo_destino.txt"');
});
