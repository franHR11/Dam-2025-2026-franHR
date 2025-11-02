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
