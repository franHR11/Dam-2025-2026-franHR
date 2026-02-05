# Encriptación y Desencriptación de Datos para una Aplicación Web

**Fecha:** 02/02/2026
**Módulo:** Programación de Servicios y Procesos - Seguridad en Programación
**Objetivo:** Implementar un sistema de encriptación de datos para una aplicación web que maneja información sensible de clientes

## Introducción y contextualización

En este ejercicio tenía que desarrollar una solución para José Vicente, un programador que quiere crear una aplicación web para mostrar información confidencial de sus clientes de forma segura. El problema principal es que los datos sensibles como nombres y direcciones no deben exponerse en texto plano cuando se transmiten o almacenan.

La solución que he implementado utiliza un algoritmo de encriptación basado en código ASCII que desplaza cada carácter 5 posiciones, lo que permite encriptar y desencriptar cadenas de forma reversible. Este tipo de encriptación simétrica es útil para proteger datos en tránsito entre el servidor y el cliente, aunque no es tan robusta como otros algoritmos criptográficos modernos como AES o RSA.

El contexto de uso es una aplicación de gestión de clientes donde José Vicente necesita mostrar información protegida, y la encriptación le da una capa adicional de seguridad frente a posibles intercepciones o accesos no autorizados a la base de datos.

## Desarrollo técnico correcto y preciso

Para resolver el problema, he creado una clase `Encriptador` con dos métodos principales:

### Método encripta()

El algoritmo de encriptación funciona siguiendo estos pasos:
1. Recibe una cadena de texto como parámetro
2. Recorre cada carácter de la cadena utilizando un bucle `for`
3. Para cada carácter, obtiene su valor ASCII con la función `ord()`
4. Aumenta este valor en 5 unidades (desplazamiento)
5. Convierte el nuevo valor ASCII a carácter con la función `chr()`
6. Concatena todos los caracteres encriptados y devuelve el resultado

### Método desencripta()

El proceso de desencriptación es el inverso:
1. Recibe una cadena encriptada
2. Recorre cada carácter
3. Obtiene el valor ASCII con `ord()`
4. Disminuye el valor en 5 unidades (invierte el desplazamiento)
5. Convierte de nuevo a carácter con `chr()`
6. Devuelve la cadena original

### Conexión a base de datos y codificación JSON

La aplicación se conecta a la base de datos MySQL `tienda2526` usando `mysqli_connect()`, ejecuta una consulta `SELECT * FROM clientes` y para cada registro aplica el método `encripta()` a todos los campos. Luego utiliza `json_encode()` con la bandera `JSON_UNESCAPED_UNICODE` para generar un JSON válido que preserve caracteres especiales y acentos, y establece la cabecera HTTP `Content-Type: application/json`.

### Manejo de errores

Aunque no he implementado bloques `try-catch` (no los hemos visto en clase), el código sigue las mejores prácticas utilizando `mysqli_close()` al final para cerrar la conexión, lo que evita fugas de recursos.

## Aplicación práctica con ejemplo claro

El código PHP implementa la funcionalidad completa en aproximadamente 25 líneas, demostrando cómo se aplica encriptación en un entorno real:

1. **Clase Encriptador**: Define los dos métodos necesarios para manipular cadenas
2. **Conexión MySQL**: Establece conexión con la base de datos tienda2526
3. **Consulta SQL**: Obtiene todos los registros de la tabla clientes
4. **Encriptación de campos**: Aplica encriptación a cada campo de cada registro
5. **Codificación JSON**: Prepara los datos para transmitirlos en formato web
6. **Respuesta HTTP**: Envía los datos con el tipo de contenido correcto

### Errores comunes y cómo evitarlos

- **No usar la misma lógica inversa**: Si desencriptas sumando en lugar de restar, no obtendrás el texto original. La solución es asegurarse de que el método `desencripta()` reste exactamente lo que `encripta()` sumó.

- **Olvidar la codificación JSON sin escaping**: Si no usas `JSON_UNESCAPED_UNICODE`, los caracteres especiales como tildes se convertirán en secuencias `\uXXXX` que complican la lectura. Solución: siempre incluir la bandera cuando trabajemos con texto en español.

- **Cerrar la conexión**: Dejar la conexión abierta puede causar problemas de rendimiento. La solución es siempre llamar a `mysqli_close()` al final del script.

## Conclusión

Me ha parecido un ejercicio muy interesante para entender cómo funciona la encriptación a nivel básico y cómo se integra con bases de datos y APIs web. Aunque el algoritmo de desplazamiento ASCII (+5/-5) es sencillo y no ofrece la misma seguridad que algoritmos modernos como AES o RSA, sirve como base para comprender los conceptos fundamentales de la criptografía simétrica.

En un proyecto real de aplicación web, este tipo de encriptación puede ser útil para proteger datos sensibles durante el transporte, aunque siempre deberíamos complementarlo con medidas adicionales como HTTPS, autenticación robusta y, dependiendo del nivel de seguridad requerido, utilizar algoritmos criptográficos más avanzados. Las ventajas de este método son su simplicidad, bajo costo computacional y reversibilidad sin perder información. Las desventajas principales son que es fácil de descifrar si alguien conoce el algoritmo y el desplazamiento, por lo que no es adecuado para datos altamente confidenciales como contraseñas o información financiera.

Este ejercicio se relaciona con el contenido de la unidad sobre técnicas de programación segura, específicamente con el tema de encriptación y protección de datos, que es fundamental para desarrollar aplicaciones web seguras en el entorno profesional actual.
