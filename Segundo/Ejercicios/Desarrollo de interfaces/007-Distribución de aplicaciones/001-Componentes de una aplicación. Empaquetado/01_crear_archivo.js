const fs = require('fs');

// Crear un nuevo archivo de texto con contenido simple
fs.writeFile('archivo_origen.txt', 'Hola, este es el contenido de mi archivo creado con Node.js.', (err) => {
    if (err) throw err;
    console.log('El archivo "archivo_origen.txt" ha sido creado correctamente.');
});
