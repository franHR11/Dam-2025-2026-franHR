# Introducción a los Sistemas ERP y su Aplicación en la Pesca

## Introducción y contextualización (25%)
Como empresario de pesca local, tengo un negocio que abarca desde la caza de peces hasta la venta directa al público. Para mejorar la eficiencia operativa en todas estas actividades, he decidido implementar un sistema ERP. Este sistema me permite gestionar todas las operaciones de mi negocio de manera digital y centralizada.

Definición del Sistema ERP: Un ERP, o Enterprise Resource Planning, es un software que integra y automatiza los procesos clave de mi negocio de pesca, como la gestión financiera, el control de inventario, las compras y las ventas, todo en una sola plataforma. En mi caso, esto significa tener un control total sobre la cadena de suministro de pescado, desde la captura hasta la venta, reduciendo errores y ahorrando tiempo.

## Desarrollo técnico correcto y preciso (25%)
Los módulos principales que incluiría en mi sistema ERP para el negocio de pesca son los siguientes, adaptados a mis operaciones de caza y venta:

- **Financiero**: Gestiona los ingresos por ventas de pescado, los gastos en equipos de pesca y combustible, y genera facturas automáticamente.
- **RRHH**: Maneja el horario de mis empleados, calcula salarios y registra ausencias, ya que tengo un equipo pequeño para la caza y venta.
- **Compras**: Controla los pedidos a proveedores de equipos de pesca y alimentos para el pescado, asegurando que no falten suministros.
- **Marketing**: Ayuda a promocionar mis productos frescos, gestionando campañas para atraer más clientes locales.
- **Ventas**: Registra las ventas diarias al público, incluyendo tipos de pescado y precios, para tener un historial claro.
- **Control de Almacén**: Gestiona el inventario de pescado capturado, controlando cantidades, fechas de caducidad y almacenamiento.
- **Contabilidad**: Lleva los registros financieros precisos, como balances y declaraciones de impuestos, integrados con el módulo financiero.

Estos módulos están relacionados directamente con las operaciones de pesca y caza, permitiendo una gestión precisa y eficiente.

## Aplicación práctica con ejemplo claro (25%)
Para demostrar cómo funcionaría mi sistema ERP, he creado una interfaz simple en HTML, CSS y JavaScript. Incluye al menos 5 pantallas o formularios relevantes para mi negocio: Dashboard principal, Gestión de Inventario, Registro de Ventas, Control Financiero y Gestión de Empleados. El código es minimalista y comentado en español, como si lo estuviera explicando yo mismo.

```html
<!-- index.html - Mi interfaz simple del ERP para pesca -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi ERP de Pesca</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .pantalla { display: none; }
        .activa { display: block; }
        nav { margin-bottom: 20px; }
        button { margin: 5px; padding: 10px; }
        form { margin: 10px 0; }
        ul { list-style-type: none; }
    </style>
</head>
<body>
    <h1>Mi Sistema ERP para el Negocio de Pesca</h1>
    <nav>
        <button onclick="mostrarPantalla('dashboard')">Dashboard</button>
        <button onclick="mostrarPantalla('inventario')">Inventario</button>
        <button onclick="mostrarPantalla('ventas')">Ventas</button>
        <button onclick="mostrarPantalla('financiero')">Financiero</button>
        <button onclick="mostrarPantalla('empleados')">Empleados</button>
    </nav>

    <!-- Pantalla 1: Dashboard Principal -->
    <div id="dashboard" class="pantalla activa">
        <h2>Dashboard Principal</h2>
        <p>Hola, aquí veo un resumen de mi negocio. Hoy he capturado 50 kg de pescado y vendido 30 kg.</p>
        <p>Próximas tareas: Revisar inventario y registrar ventas.</p>
    </div>

    <!-- Pantalla 2: Gestión de Inventario (Control de Almacén) -->
    <div id="inventario" class="pantalla">
        <h2>Control de Almacén</h2>
        <p>Aquí controlo el pescado que tengo almacenado.</p>
        <form onsubmit="event.preventDefault(); agregarProducto();">
            <label>Producto (ej: Salmón): <input type="text" id="producto" required></label><br>
            <label>Cantidad (kg): <input type="number" id="cantidad" required></label><br>
            <button type="submit">Agregar al Inventario</button>
        </form>
        <ul id="listaProductos"></ul>
    </div>

    <!-- Pantalla 3: Registro de Ventas -->
    <div id="ventas" class="pantalla">
        <h2>Registro de Ventas</h2>
        <p>Registro las ventas diarias al público.</p>
        <form onsubmit="event.preventDefault(); registrarVenta();">
            <label>Producto vendido: <input type="text" id="productoVendido" required></label><br>
            <label>Cantidad vendida (kg): <input type="number" id="cantidadVendida" required></label><br>
            <label>Precio total (€): <input type="number" id="precioVenta" required></label><br>
            <button type="submit">Registrar Venta</button>
        </form>
        <ul id="listaVentas"></ul>
    </div>

    <!-- Pantalla 4: Control Financiero -->
    <div id="financiero" class="pantalla">
        <h2>Control Financiero</h2>
        <p>Veo mis ingresos y gastos.</p>
        <form onsubmit="event.preventDefault(); agregarGasto();">
            <label>Descripción del gasto: <input type="text" id="descripcionGasto" required></label><br>
            <label>Monto (€): <input type="number" id="montoGasto" required></label><br>
            <button type="submit">Agregar Gasto</button>
        </form>
        <p>Ingresos totales: <span id="ingresosTotales">0</span> €</p>
        <p>Gastos totales: <span id="gastosTotales">0</span> €</p>
        <ul id="listaGastos"></ul>
    </div>

    <!-- Pantalla 5: Gestión de Empleados (RRHH) -->
    <div id="empleados" class="pantalla">
        <h2>Gestión de Empleados</h2>
        <p>Manejo el horario y salarios de mi equipo.</p>
        <form onsubmit="event.preventDefault(); agregarEmpleado();">
            <label>Nombre del empleado: <input type="text" id="nombreEmpleado" required></label><br>
            <label>Horas trabajadas: <input type="number" id="horasTrabajadas" required></label><br>
            <button type="submit">Agregar Empleado</button>
        </form>
        <ul id="listaEmpleados"></ul>
    </div>

    <script>
        // Función para cambiar entre pantallas
        function mostrarPantalla(id) {
            document.querySelectorAll('.pantalla').forEach(p => p.classList.remove('activa'));
            document.getElementById(id).classList.add('activa');
        }

        // Para Inventario
        function agregarProducto() {
            const producto = document.getElementById('producto').value;
            const cantidad = document.getElementById('cantidad').value;
            const li = document.createElement('li');
            li.textContent = `${producto}: ${cantidad} kg`;
            document.getElementById('listaProductos').appendChild(li);
            document.getElementById('producto').value = '';
            document.getElementById('cantidad').value = '';
        }

        // Para Ventas
        function registrarVenta() {
            const producto = document.getElementById('productoVendido').value;
            const cantidad = document.getElementById('cantidadVendida').value;
            const precio = document.getElementById('precioVenta').value;
            const li = document.createElement('li');
            li.textContent = `Vendido ${cantidad} kg de ${producto} por ${precio} €`;
            document.getElementById('listaVentas').appendChild(li);
            // Actualizar ingresos
            const ingresos = parseFloat(document.getElementById('ingresosTotales').textContent) + parseFloat(precio);
            document.getElementById('ingresosTotales').textContent = ingresos;
            document.getElementById('productoVendido').value = '';
            document.getElementById('cantidadVendida').value = '';
            document.getElementById('precioVenta').value = '';
        }

        // Para Financiero
        function agregarGasto() {
            const descripcion = document.getElementById('descripcionGasto').value;
            const monto = document.getElementById('montoGasto').value;
            const li = document.createElement('li');
            li.textContent = `${descripcion}: ${monto} €`;
            document.getElementById('listaGastos').appendChild(li);
            // Actualizar gastos
            const gastos = parseFloat(document.getElementById('gastosTotales').textContent) + parseFloat(monto);
            document.getElementById('gastosTotales').textContent = gastos;
            document.getElementById('descripcionGasto').value = '';
            document.getElementById('montoGasto').value = '';
        }

        // Para Empleados
        function agregarEmpleado() {
            const nombre = document.getElementById('nombreEmpleado').value;
            const horas = document.getElementById('horasTrabajadas').value;
            const li = document.createElement('li');
            li.textContent = `${nombre}: ${horas} horas trabajadas`;
            document.getElementById('listaEmpleados').appendChild(li);
            document.getElementById('nombreEmpleado').value = '';
            document.getElementById('horasTrabajadas').value = '';
        }
    </script>
</body>
</html>
```

Este código crea una interfaz web simple que simula mi ERP. Cada pantalla representa un módulo clave, y puedo navegar entre ellas. Los formularios permiten ingresar datos básicos, y se actualizan las listas y totales en tiempo real.

## Cierre/Conclusión enlazando con la unidad (25%)
En resumen, implementar un sistema ERP en mi negocio de pesca y caza me ayuda a mejorar la eficiencia operativa al centralizar todas las actividades en una plataforma digital. Esto reduce errores manuales, acelera procesos como el control de inventario y las ventas, y me permite tomar decisiones basadas en datos precisos. Al final, esto se traduce en más tiempo para enfocarme en lo que amo: la pesca, y en un negocio más rentable.