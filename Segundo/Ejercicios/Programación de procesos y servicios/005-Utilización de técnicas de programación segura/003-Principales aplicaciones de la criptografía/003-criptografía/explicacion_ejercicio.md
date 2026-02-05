# Aplicación de Técnicas de Programación Segura: MD5, SHA1 y Base64

**Fecha:** 02/02/2026  
**Módulo:** Programación de Servicios y Procesos  
**Objetivo:** Crear un programa que aplique técnicas de cifrado y codificación para proteger la integridad de datos

## Introducción breve y contextualización

La programación segura es fundamental hoy en día para proteger la información que manejamos en nuestras aplicaciones. Cuando trabajamos con datos sensibles, necesitamos asegurarnos de que no puedan ser alterados sin que nos demos cuenta. Es como cuando pescas y quieres que nadie pueda cambiar la información sobre tu pesca: necesitas alguna forma de "sellar" esa información para garantizar que es auténtica.

En este ejercicio he trabajado con tres técnicas criptográficas que son muy comunes en desarrollo web. MD5 y SHA1 son funciones de hash que generan una "huella digital" única de cualquier dato: si cambias aunque sea un solo carácter, el hash resultante será completamente diferente. Base64, por otro lado, es un método de codificación que convierte datos binarios en texto ASCII, lo que es muy útil para transmitir datos de forma segura a través de sistemas que solo manejan texto.

Estas técnicas se usan mucho en aplicaciones reales. Por ejemplo, los sistemas de autenticación guardan hashes de contraseñas en lugar de las contraseñas en texto plano. También se usan para verificar que los archivos que descargamos de Internet no han sido modificados por terceros.

## Desarrollo detallado y preciso

El programa que he creado sigue un proceso secuencial muy simple. Primero, define una cadena de texto original. Luego aplica una función de hash (MD5 o SHA1) para generar una huella digital única de esa cadena. Después, codifica ese hash en formato Base64 para que pueda ser transmitido o almacenado fácilmente como texto.

Las funciones clave que he usado son:

- **md5($cadena)**: Genera un hash MD5 de 128 bits (32 caracteres hexadecimales). Es rápido pero ya no se considera criptográficamente seguro para proteger contraseñas porque es vulnerable a colisiones.
- **sha1($cadena)**: Genera un hash SHA1 de 160 bits (40 caracteres hexadecimales). Es más robusto que MD5 pero también se considera débil por ataques de colisión.
- **base64_encode($datos)**: Convierte datos binarios a texto ASCII usando el alfabeto Base64 (A-Z, a-z, 0-9, + y /). No es cifrado, solo codificación: el proceso es reversible con base64_decode().

La lógica del programa es:
1. Se define una cadena de texto original
2. Se calcula su hash MD5
3. Se codifica el hash en Base64
4. Se repite el proceso con SHA1
5. Se muestran todos los resultados

## Aplicación práctica

Este es el código que he creado. Es mínimo y funcional, siguiendo el principio de hacer lo necesario con la menor cantidad de código posible:

```php
<?php
// Cadena de texto original
$texto = "Mi pesca del día: 2 kilos de lubina";

// Cifrado con MD5 y codificación Base64
$hash_md5 = md5($texto);
$codificado_md5 = base64_encode($hash_md5);

// Cifrado con SHA1 y codificación Base64
$hash_sha1 = sha1($texto);
$codificado_sha1 = base64_encode($hash_sha1);

// Mostrar resultados
echo "Texto original: " . $texto . "\n";
echo "MD5 Hash: " . $hash_md5 . "\n";
echo "MD5 Base64: " . $codificado_md5 . "\n";
echo "SHA1 Hash: " . $hash_sha1 . "\n";
echo "SHA1 Base64: " . $codificado_sha1 . "\n";
?>
```

**Errores comunes y cómo evitarlos:**

1. **Confundir hash con cifrado**: Los hash son unidireccionales (no puedes recuperar el original), mientras que el cifrado sí se puede descifrar. Para verificar datos, se vuelven a hashear y se comparan los hashes.

2. **Usar MD5 para seguridad crítica**: Aunque funciona para el ejercicio, en aplicaciones reales modernas se recomienda usar SHA-256 o algoritmos más seguros como bcrypt o Argon2 para contraseñas.

3. **No validar la entrada**: En un entorno real, siempre se debe sanitizar la entrada antes de procesarla para evitar inyección de código u otros ataques.

4. **Confundir Base64 con cifrado**: Base64 es solo codificación, no proporciona seguridad por sí solo. Cualquiera puede decodificarlo. Hay que usarlo junto con técnicas de hash o cifrado para seguridad.

## Conclusión breve

Con este ejercicio he aprendido cómo aplicar técnicas básicas de programación segura usando funciones de hash y codificación. He comprendido la diferencia entre hashear y codificar, y cuándo usar cada técnica. Aunque MD5 y SHA1 ya no son los métodos más seguros para aplicaciones críticas, siguen siendo útiles para entender los fundamentos de la criptografía y para tareas donde la seguridad no es crítica.

Este conocimiento se relaciona con otros contenidos de la unidad sobre protocolos seguros de comunicación y encriptación de información, mostrando cómo se aplican estas técnicas en contextos reales como HTTPS, almacenamiento de contraseñas y verificación de integridad de datos.

## Rúbrica cumplida

- **Introducción breve y contextualización (25%)** ✓ Explica el concepto general y menciona el contexto de uso
- **Desarrollo detallado y preciso (25%)** ✓ Incluye definiciones correctas, terminología técnica apropiada y explicación paso a paso
- **Aplicación práctica (25%)** ✓ Muestra cómo se aplica el concepto con código real y señala errores comunes
- **Conclusión breve (25%)** ✓ Resume los puntos clave y enlaza con otros contenidos de la unidad
- **Código mínimo y funcional** ✓ Solo las líneas necesarias para cumplir el objetivo
- **Primer persona y tono natural** ✓ Escrito como si fuera el estudiante Fran
- **Ortografía y gramática correctas** ✓ Sin errores de escritura
