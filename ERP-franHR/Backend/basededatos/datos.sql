INSERT INTO `usuarios` (`Identificador`, `usuario`, `contrasena`, `nombrecompleto`, `email`, `telefono`) VALUES (NULL, 'franHR', 'franHR', 'Francisco Jose', 'franhr1113@gmail.com', '666666666');

-- Insertamos 10 categorías
INSERT INTO `categorias_aplicaciones` (`nombre`) VALUES
('Ventas'),
('Compras'),
('Inventario'),
('Contabilidad'),
('Recursos Humanos'),
('Proyectos'),
('CRM'),
('Marketing'),
('Soporte'),
('Administración');

-- Insertamos 10 aplicaciones con iconos de Font Awesome
INSERT INTO `aplicaciones` (`nombre`, `descripcion`, `icono`, `categoria`) VALUES
('Gestión de Ventas', 'Módulo para gestionar presupuestos, pedidos y facturación de clientes.', 'fa-solid fa-cart-shopping', 1),
('Gestión de Compras', 'Control de pedidos a proveedores, facturas y recepciones de mercancías.', 'fa-solid fa-truck', 2),
('Inventario Avanzado', 'Gestión de almacenes, stock, entradas y salidas de productos.', 'fa-solid fa-boxes-stacked', 3),
('Contabilidad General', 'Registro contable, balances y conciliación bancaria.', 'fa-solid fa-calculator', 4),
('Gestión de Nóminas', 'Administración de nóminas, empleados y contratos.', 'fa-solid fa-users', 5),
('Proyectos', 'Planificación de tareas, hitos y seguimiento de proyectos.', 'fa-solid fa-diagram-project', 6),
('CRM Comercial', 'Gestión de oportunidades, clientes potenciales y pipeline de ventas.', 'fa-solid fa-handshake', 7),
('Campañas de Marketing', 'Planificación de campañas, newsletters y segmentación de clientes.', 'fa-solid fa-bullhorn', 8),
('Soporte Técnico', 'Sistema de tickets para incidencias y soporte al cliente.', 'fa-solid fa-headset', 9),
('Panel de Administración', 'Gestión global del sistema, seguridad y configuración.', 'fa-solid fa-gears', 10);

