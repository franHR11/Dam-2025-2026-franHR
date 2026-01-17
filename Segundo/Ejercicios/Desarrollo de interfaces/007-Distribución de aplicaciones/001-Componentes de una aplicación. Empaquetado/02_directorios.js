const fs = require('fs');

// Crear un directorio y un subdirectorio (recursive: true permite crear ambos a la vez)
fs.mkdir('nuevo_directorio/subdirectorio', { recursive: true }, (err) => {
    if (err) throw err;
    console.log('Estructura de directorios creada: nuevo_directorio/subdirectorio');
});
