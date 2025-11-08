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
