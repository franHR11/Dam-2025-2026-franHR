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
