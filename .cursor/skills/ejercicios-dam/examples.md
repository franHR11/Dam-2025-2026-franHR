# Ejemplos de Ejercicios DAM

## Ejemplo 1: Cálculo de Área de Triángulo (Python)

**explicacion_ejercicio.md:**

```markdown
# Cálculo del Área de un Triángulo

**Fecha:** 15/01/2026  
**Módulo:** Programación I  
**Objetivo:** Crear un programa que calcule el área de un triángulo dadas su base y altura

## Explicación

En este ejercicio tenía que crear un programa que calcule el área de un triángulo. Decidí hacerlo con el mínimo código posible usando una sola línea para el cálculo y otra para la entrada de datos. Me ha parecido un ejercicio sencillo pero útil para practicar operaciones básicas y simplificar código sin perder legibilidad.

## Código

```python
base = float(input("Base: "))
altura = float(input("Altura: "))
print("Área:", (base*altura)/2)
```

## Rúbrica cumplida

- Código mínimo ✓ Uso de solo 3 líneas para la funcionalidad completa
- Funcionalidad correcta ✓ Calcula correctamente el área con la fórmula (base*altura)/2
- Claridad ✓ Nombres de variables descriptivos y lógica simple
- Uso correcto del lenguaje ✓ Uso apropiado de input(), print() y operadores matemáticos
- Manejo de datos ✓ Conversión correcta de string a float

## Conclusión

Creo que he logrado simplificar el código sin perder legibilidad. Aunque parecía muy sencillo, me ha servido para practicar las operaciones matemáticas básicas en Python y entender cómo manejar entradas del usuario de forma eficiente.
```

---

## Ejemplo 2: Verificar Número Par (JavaScript)

**explicacion_ejercicio.md:**

```markdown
# Verificar si un Número es Par

**Fecha:** 20/01/2026  
**Módulo:** Desarrollo Web  
**Objetivo:** Crear una función que determine si un número es par

## Explicación

Para este ejercicio tenía que hacer una función que me dijera si un número es par o no. He optado por usar el operador módulo (%) que me devuelve el resto de la división. Si el resto es 0, significa que el número es par. Es la forma más simple que conozco para resolverlo.

## Código

```javascript
function esPar(numero) {
    return numero % 2 === 0;
}

let num = parseInt(prompt("Ingresa un número:"));
console.log(esPar(num) ? "Es par" : "Es impar");
```

## Rúbrica cumplida

- Código mínimo ✓ Función de una sola línea usando el operador módulo
- Funcionalidad correcta ✓ Devuelve true para pares, false para impares
- Claridad ✓ Lógica directa y fácil de entender
- Uso correcto del lenguaje ✓ Uso apropiado de %, === y operador ternario
- Interacción con usuario ✓ Uso de prompt() y console.log()

## Conclusión

Me ha parecido un buen ejercicio para reforzar el uso del operador módulo. Al principio pensé en usar un if tradicional, pero el operador ternario me permite hacer el código mucho más conciso sin que sea difícil de leer.

```

---

## Ejemplo 3: Suma de Array (Java)

**explicacion_ejercicio.md:**

```markdown
# Sumar Elementos de un Array

**Fecha:** 22/01/2026  
**Módulo:** Programación II  
**Objetivo:** Sumar todos los elementos de un array de enteros

## Explicación

Este ejercicio consistía en sumar todos los números de un array. He usado un bucle for que recorre cada elemento y lo va acumulando en una variable suma. Es la solución más directa que se me ocurrió y no requiere nada más complejo.

## Código

```java
public class SumaArray {
    public static void main(String[] args) {
        int[] numeros = {1, 2, 3, 4, 5};
        int suma = 0;
        
        for (int num : numeros) {
            suma += num;
        }
        
        System.out.println("Suma: " + suma);
    }
}
```

## Rúbrica cumplida

- Código mínimo ✓ Uso de for-each para recorrer el array de forma eficiente
- Funcionalidad correcta ✓ Suma correctamente todos los elementos (15 en total)
- Claridad ✓ Bucle for-each es más legible que el for tradicional con índice
- Uso correcto del lenguaje ✓ Sintaxis correcta de Java y uso apropiado de operadores
- Estructura del programa ✓ Class y main() correctamente definidos

## Conclusión

He aprendido que el for-each es más limpio cuando solo necesito leer los elementos del array. Aunque también podría hacerlo con un for normal, creo que esta versión es más fácil de entender y requiere menos código.

```

---

## Ejemplo 4: Conversión de Temperatura (C++)

**explicacion_ejercicio.md:**

```markdown
# Conversión de Celsius a Fahrenheit

**Fecha:** 25/01/2026  
**Módulo:** Fundamentos de Programación  
**Objetivo:** Crear un programa que convierta grados Celsius a Fahrenheit

## Explicación

Tenía que hacer un conversor de temperatura de Celsius a Fahrenheit. La fórmula es F = (C × 9/5) + 32. He creado un programa que pide los grados Celsius y muestra el resultado en Fahrenheit. Es un ejercicio básico pero me ha servido para practicar las operaciones matemáticas en C++.

## Código

```cpp
#include <iostream>
using namespace std;

int main() {
    float celsius;
    cout << "Grados Celsius: ";
    cin >> celsius;
    cout << "Fahrenheit: " << (celsius * 9/5) + 32 << endl;
    return 0;
}
```

## Rúbrica cumplida

- Código mínimo ✓ Solo las líneas necesarias para la conversión
- Funcionalidad correcta ✓ Aplica correctamente la fórmula de conversión
- Claridad ✓ Código directo sin complicaciones innecesarias
- Uso correcto del lenguaje ✓ Include iostream, cin y cout apropiadamente
- Manejo de entrada/salida ✓ Uso correcto de cin para entrada y cout para salida

## Conclusión

Aunque la fórmula parecía sencilla, me costó un poco entender cómo aplicar los operadores en el orden correcto. Al final me di cuenta de que (celsius * 9/5) + 32 funciona bien porque C++ respeta el orden de las operaciones.
```

---

## Ejemplo 5: Invertir Cadena (Python)

**explicacion_ejercicio.md:**

```markdown
# Invertir una Cadena de Texto

**Fecha:** 28/01/2026  
**Módulo:** Programación Avanzada  
**Objetivo:** Crear una función que invierta una cadena de texto

## Explicación

Para este ejercicio tenía que invertir una cadena de texto. En Python es muy sencillo porque puedes usar slicing con [::-1] que invierte la cadena automáticamente. Es la forma más concisa que he encontrado y no requiere bucles ni nada complejo.

## Código

```python
def invertir(texto):
    return texto[::-1]

frase = input("Frase: ")
print("Invertida:", invertir(frase))
```

## Rúbrica cumplida

- Código mínimo ✓ Función de una sola línea usando slicing
- Funcionalidad correcta ✓ Invierte correctamente cualquier cadena de texto
- Claridad ✓ Slicing es una característica muy clara de Python
- Uso correcto del lenguaje ✓ Uso apropiado de slicing [::-1]
- Modularidad ✓ Función reutilizable y bien definida

## Conclusión

Me ha sorprendido lo fácil que es invertir una cadena en Python. He aprendido que el slicing es muy potente y puede hacer muchas cosas útiles en una sola línea. Es un buen ejemplo de cómo Python permite escribir código muy conciso.
```

---

## Ejemplo 6: Validar Email (PHP)

**explicacion_ejercicio.md:**

```markdown
# Validación de Email

**Fecha:** 02/02/2026  
**Módulo:** Desarrollo Web Server-Side  
**Objetivo:** Crear una función que valide si un email tiene formato correcto

## Explicación

En este ejercicio tenía que validar un email. He usado filter_var con el filtro FILTER_VALIDATE_EMAIL que ya viene en PHP y hace toda la validación automáticamente. Es mucho mejor que intentar crear mi propia expresión regular porque ya está probada y cubre todos los casos.

## Código

```php
<?php
$email = $_POST['email'] ?? '';

if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "Email válido: " . htmlspecialchars($email);
} else {
    echo "Email inválido";
}
?>
```

## Rúbrica cumplida

- Código mínimo ✓ Uso de función nativa de PHP en lugar de validación manual
- Funcionalidad correcta ✓ Valida correctamente el formato de email según estándares
- Claridad ✓ filter_var hace muy clara la intención del código
- Uso correcto del lenguaje ✓ Uso apropiado de filter_var y FILTER_VALIDATE_EMAIL
- Seguridad ✓ Uso de htmlspecialchars para evitar XSS

## Conclusión

Al principio pensé en usar una expresión regular propia, pero luego me di cuenta de que PHP ya tiene una función para esto. Me ha servido para entender que siempre conviene buscar si ya existe una solución probada antes de intentar reinventar la rueda.
```
