// Archivo: AplicacionPesca.java
import java.awt.*;
import java.awt.event.*;
import java.util.ArrayList;
import java.util.List;

// Clase principal que representa la aplicación de pesca
public class AplicacionPesca extends Frame implements ActionListener {
    // Componentes para la sección de planificación
    private TextField txtRio, txtFecha, txtDuracion; // Campos de texto para datos de la excursión
    private Button btnGuardar; // Botón para guardar la excursión

    // Componentes para la sección de registro de peces
    private List listaPeces; // Lista para mostrar los registros de peces
    private TextField txtBuscarEspecie; // Campo para buscar por especie
    private Button btnEditar, btnEliminar, btnBuscar; // Botones para acciones

    // Lista para almacenar los registros de peces
    private List<String> registros = new ArrayList<>();

    // Constructor de la aplicación
    public AplicacionPesca() {
        // Configurar la ventana principal
        setTitle("Aplicación de Pesca - María"); // Título de la ventana
        setSize(600, 400); // Tamaño de la ventana
        setLayout(new BorderLayout()); // Layout principal

        // Crear panel para planificación de excursiones
        Panel panelPlan = new Panel();
        panelPlan.setLayout(new GridLayout(4, 2)); // Layout en cuadrícula

        // Agregar etiquetas y campos para río
        panelPlan.add(new Label("Río:")); // Etiqueta
        txtRio = new TextField(); // Campo de texto
        panelPlan.add(txtRio);

        // Agregar etiquetas y campos para fecha
        panelPlan.add(new Label("Fecha:"));
        txtFecha = new TextField();
        panelPlan.add(txtFecha);

        // Agregar etiquetas y campos para duración
        panelPlan.add(new Label("Duración (horas):"));
        txtDuracion = new TextField();
        panelPlan.add(txtDuracion);

        // Botón para guardar la excursión
        btnGuardar = new Button("Guardar Excursión");
        btnGuardar.addActionListener(this); // Escuchar clics
        panelPlan.add(btnGuardar);

        // Agregar panel de planificación a la parte superior
        add(panelPlan, BorderLayout.NORTH);

        // Crear panel para registro de peces
        Panel panelReg = new Panel();
        panelReg.setLayout(new BorderLayout());

        // Etiqueta y lista para registros
        panelReg.add(new Label("Registro de Peces:"), BorderLayout.NORTH);
        listaPeces = new List(); // Lista para mostrar peces
        panelReg.add(listaPeces, BorderLayout.CENTER);

        // Panel de controles para búsqueda y acciones
        Panel panelControles = new Panel();
        panelControles.setLayout(new GridLayout(1, 4)); // Una fila, cuatro columnas

        // Campo y botón para búsqueda
        txtBuscarEspecie = new TextField(); // Campo para especie a buscar
        panelControles.add(txtBuscarEspecie);
        btnBuscar = new Button("Buscar por Especie");
        btnBuscar.addActionListener(this);
        panelControles.add(btnBuscar);

        // Botones para editar y eliminar
        btnEditar = new Button("Editar");
        btnEditar.addActionListener(this);
        panelControles.add(btnEditar);
        btnEliminar = new Button("Eliminar");
        btnEliminar.addActionListener(this);
        panelControles.add(btnEliminar);

        // Agregar controles al panel de registro
        panelReg.add(panelControles, BorderLayout.SOUTH);

        // Agregar panel de registro al centro
        add(panelReg, BorderLayout.CENTER);

        // Manejar cierre de ventana
        addWindowListener(new WindowAdapter() {
            public void windowClosing(WindowEvent we) {
                System.exit(0); // Cerrar aplicación
            }
        });

        // Hacer visible la ventana
        setVisible(true);
    }

    // Método para manejar eventos de botones
    public void actionPerformed(ActionEvent ae) {
        // Si se presiona guardar excursión
        if (ae.getSource() == btnGuardar) {
            // Crear string con datos de la excursión
            String excursion = "Excursión: Río " + txtRio.getText() + " - Fecha " + txtFecha.getText() + " - Duración " + txtDuracion.getText() + " horas";
            registros.add(excursion); // Agregar a lista de registros
            actualizarLista(); // Actualizar visualización

            // Limpiar campos después de guardar
            txtRio.setText("");
            txtFecha.setText("");
            txtDuracion.setText("");
        }
        // Si se presiona buscar
        else if (ae.getSource() == btnBuscar) {
            String especie = txtBuscarEspecie.getText().toLowerCase(); // Obtener especie en minúsculas
            listaPeces.removeAll(); // Limpiar lista

            // Filtrar registros que contengan la especie
            for (String reg : registros) {
                if (reg.toLowerCase().contains(especie)) {
                    listaPeces.add(reg); // Agregar coincidencias
                }
            }
        }
        // Si se presiona editar (simplificado)
        else if (ae.getSource() == btnEditar) {
            int index = listaPeces.getSelectedIndex(); // Obtener índice seleccionado
            if (index >= 0) {
                // En una versión completa aquí se abriría un diálogo para editar
                // Por simplicidad, solo muestro en consola
                System.out.println("Editar registro: " + listaPeces.getSelectedItem());
            }
        }
        // Si se presiona eliminar
        else if (ae.getSource() == btnEliminar) {
            int index = listaPeces.getSelectedIndex(); // Obtener índice seleccionado
            if (index >= 0) {
                registros.remove(index); // Eliminar de la lista de datos
                actualizarLista(); // Actualizar visualización
            }
        }
    }

    // Método para actualizar la lista visual
    private void actualizarLista() {
        listaPeces.removeAll(); // Limpiar lista
        for (String reg : registros) {
            listaPeces.add(reg); // Agregar todos los registros
        }
    }

    // Método main para ejecutar la aplicación
    public static void main(String[] args) {
        new AplicacionPesca(); // Crear instancia de la aplicación
    }
}
