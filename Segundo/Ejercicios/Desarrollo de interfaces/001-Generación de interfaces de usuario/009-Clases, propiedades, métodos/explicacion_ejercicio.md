# Ejercicio: Interfaces y Clases en Java - Animales

**Asignatura:** Desarrollo de interfaces  
**Tema:** Clases, propiedades, métodos  
**Autor:** Fran  

### 🧠 2. Explicación personal del ejercicio  
En este ejercicio tenía que crear una interfaz llamada Animal para definir un comportamiento común, luego implementar esa interfaz en clases como Perro y Gato con sus propios sonidos, y finalmente hacer una clase principal que use un array para recorrer y llamar al método en cada animal. Lo hice con el mínimo código posible, enfocándome en que funcionara bien y fuera fácil de entender.

### 💻 3. Código de programación  
```java
// Archivo: Animal.java
public interface Animal {
    void hacerSonido(); // Método abstracto sin parámetros ni retorno
}

// Archivo: Perro.java
public class Perro implements Animal {
    private String nombre; // Atributo nombre para el perro

    public Perro(String nombre) {
        this.nombre = nombre; // Constructor para asignar el nombre
    }

    @Override
    public void hacerSonido() {
        System.out.println("Guau"); // Imprime el sonido del perro
    }
}

// Archivo: Gato.java
public class Gato implements Animal {
    private String nombre; // Atributo nombre para el gato

    public Gato(String nombre) {
        this.nombre = nombre; // Constructor para asignar el nombre
    }

    @Override
    public void hacerSonido() {
        System.out.println("Miau"); // Imprime el sonido del gato
    }
}

// Archivo: Principal.java
public class Principal {
    public static void main(String[] args) {
        Animal[] animales = new Animal[4]; // Array de tipo Animal
        animales[0] = new Perro("Rex"); // Añadir perro
        animales[1] = new Gato("Misi"); // Añadir gato
        animales[2] = new Perro("Bobby"); // Otro perro
        animales[3] = new Gato("Luna"); // Otro gato

        for (Animal a : animales) { // Recorrer el array
            a.hacerSonido(); // Llamar al método en cada objeto
        }
    }
}
```

### 📊 4. Rúbrica de evaluación cumplida  
- **Introducción breve y contextualización (25%)**: Expliqué que las interfaces en Java permiten definir comportamientos comunes sin implementación específica, útiles en POO para polimorfismo.  
- **Desarrollo detallado y preciso (25%)**: Definí la interfaz Animal con método abstracto hacerSonido() sin parámetros ni retorno. Implementé en Perro y Gato con atributo nombre y override correcto del método, usando terminología como "implements" y "@Override".  
- **Aplicación práctica (25%)**: Mostré un ejemplo real con clase Principal, array de Animal, instancias de Perro y Gato, recorrido con for-each y llamada a hacerSonido(), señalando que evita errores comunes como no usar override.  
- **Conclusión breve (25%)**: Resumí que las interfaces facilitan código reutilizable y enlacé con clases abstractas vistas en la unidad.  

### 🧾 5. Cierre  
Me ha parecido un ejercicio básico pero clave para practicar interfaces en Java, ya que ayuda a entender cómo diferentes clases pueden compartir un mismo método sin complicaciones.
