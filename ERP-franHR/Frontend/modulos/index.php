<?php
// Sistema de gestión de módulos - Versión corregida
session_start();

// Verificación de autenticación flexible
$usuarioAutenticado = false;
$esAdmin = false;

// Verificar diferentes tipos de autenticación
if (isset($_SESSION['user_id']) || isset($_SESSION['username'])) {
    $usuarioAutenticado = true;
    $esAdmin = (
        (isset($_SESSION['user_rol']) && $_SESSION['user_rol'] === 'admin') ||
        (isset($_SESSION['username']) && $_SESSION['username'] === 'admin')
    );
}

// Si no está autenticado, crear sesión temporal para desarrollo
if (!$usuarioAutenticado) {
    $_SESSION['user_id'] = 1;
    $_SESSION['username'] = 'admin';
    $_SESSION['user_rol'] = 'admin';
    $_SESSION['user_nombre'] = 'Administrador';
    $_SESSION['user_apellidos'] = 'Sistema';
    $usuarioAutenticado = true;
    $esAdmin = true;
}

// Conexión a la base de datos
$modulos = [];
$conexionExitosa = false;
$mensaje = '';

try {
    // Incluir configuración de base de datos
    $rutaConfig = __DIR__ . '/../../api/config.php';
    if (file_exists($rutaConfig)) {
        require_once $rutaConfig;

        if (function_exists('getConnection')) {
            $pdo = getConnection();
            $conexionExitosa = ($pdo !== null);

            if ($conexionExitosa) {
                // Consulta para obtener todos los módulos con su configuración
                $sql = "
                    SELECT
                        m.id,
                        m.nombre,
                        m.nombre_tecnico,
                        m.descripcion,
                        m.version,
                        m.icono,
                        m.categoria,
                        m.instalado,
                        m.activo,
                        m.autor,
                        m.fecha_instalacion,
                        m.fecha_activacion,
                        mc.menu_order AS menu_order,
                        CASE
                            WHEN m.instalado = 1 AND m.activo = 1 THEN 'activo'
                            WHEN m.instalado = 1 AND m.activo = 0 THEN 'inactivo'
                            WHEN m.instalado = 0 THEN 'no-instalado'
                        END AS estado
                    FROM modulos m
                    LEFT JOIN (
                        SELECT modulo_id, CAST(MIN(valor) AS UNSIGNED) AS menu_order
                        FROM modulo_configuracion
                        WHERE clave = 'menu_order'
                        GROUP BY modulo_id
                    ) mc ON mc.modulo_id = m.id
                    ORDER BY
                        CASE WHEN m.instalado = 0 THEN 2 ELSE 1 END,
                        CAST(COALESCE(mc.menu_order, 999) AS UNSIGNED) ASC,
                        m.nombre ASC
                ";

                $stmt = $pdo->query($sql);
                $modulosDB = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // Procesar módulos
                foreach ($modulosDB as &$modulo) {
                    $dependencias = $modulo['dependencias'] ?? '';
                    $modulo['dependencias'] = $dependencias ? json_decode($dependencias, true) : [];
                    $modulo['menu_order'] = $modulo['menu_order'] ?: 999;
                }

                $modulos = $modulosDB;
            }
        }
    }
} catch (Exception $e) {
    error_log("Error en conexión: " . $e->getMessage());
    $mensaje = "Error de conexión: " . $e->getMessage();
}

// Si no hay módulos, crear datos de ejemplo
if (empty($modulos)) {
    $modulos = [
        [
            'id' => 1,
            'nombre' => 'Dashboard',
            'nombre_tecnico' => 'dashboard',
            'descripcion' => 'Panel principal del sistema',
            'version' => '1.0.0',
            'icono' => 'fas fa-tachometer-alt',
            'categoria' => 'sistema',
            'instalado' => 1,
            'activo' => 1,
            'autor' => 'Sistema',
            'fecha_instalacion' => date('Y-m-d H:i:s'),
            'fecha_activacion' => date('Y-m-d H:i:s'),
            'menu_order' => 1,
            'estado' => 'activo',
            'dependencias' => []
        ],
        [
            'id' => 2,
            'nombre' => 'Clientes',
            'nombre_tecnico' => 'clientes',
            'descripcion' => 'Gestión completa de clientes y contactos',
            'version' => '1.0.0',
            'icono' => 'fas fa-users',
            'categoria' => 'crm',
            'instalado' => 1,
            'activo' => 1,
            'autor' => 'Sistema',
            'fecha_instalacion' => date('Y-m-d H:i:s'),
            'fecha_activacion' => date('Y-m-d H:i:s'),
            'menu_order' => 2,
            'estado' => 'activo',
            'dependencias' => []
        ],
        [
            'id' => 3,
            'nombre' => 'Proveedores',
            'nombre_tecnico' => 'proveedores',
            'descripcion' => 'Gestión de proveedores y contactos',
            'version' => '1.0.0',
            'icono' => 'fas fa-truck',
            'categoria' => 'compras',
            'instalado' => 1,
            'activo' => 1,
            'autor' => 'Sistema',
            'fecha_instalacion' => date('Y-m-d H:i:s'),
            'fecha_activacion' => date('Y-m-d H:i:s'),
            'menu_order' => 3,
            'estado' => 'activo',
            'dependencias' => []
        ],
        [
            'id' => 4,
            'nombre' => 'Productos',
            'nombre_tecnico' => 'productos',
            'descripcion' => 'Catálogo de productos y control de stock',
            'version' => '1.0.0',
            'icono' => 'fas fa-box',
            'categoria' => 'inventario',
            'instalado' => 1,
            'activo' => 0,
            'autor' => 'Sistema',
            'fecha_instalacion' => date('Y-m-d H:i:s'),
            'fecha_activacion' => null,
            'menu_order' => 4,
            'estado' => 'inactivo',
            'dependencias' => []
        ],
        [
            'id' => 5,
            'nombre' => 'Presupuestos',
            'nombre_tecnico' => 'presupuestos',
            'descripcion' => 'Gestión de presupuestos para clientes',
            'version' => '1.0.0',
            'icono' => 'fas fa-file-invoice',
            'categoria' => 'ventas',
            'instalado' => 1,
            'activo' => 0,
            'autor' => 'Sistema',
            'fecha_instalacion' => date('Y-m-d H:i:s'),
            'fecha_activacion' => null,
            'menu_order' => 5,
            'estado' => 'inactivo',
            'dependencias' => []
        ],
        [
            'id' => 6,
            'nombre' => 'Facturación',
            'nombre_tecnico' => 'facturacion',
            'descripcion' => 'Facturas de venta y compra',
            'version' => '1.0.0',
            'icono' => 'fas fa-file-invoice-dollar',
            'categoria' => 'contabilidad',
            'instalado' => 1,
            'activo' => 0,
            'autor' => 'Sistema',
            'fecha_instalacion' => date('Y-m-d H:i:s'),
            'fecha_activacion' => null,
            'menu_order' => 6,
            'estado' => 'inactivo',
            'dependencias' => []
        ]
    ];

    if (empty($mensaje)) {
        $mensaje = $conexionExitosa ? 'No hay módulos configurados en el sistema' : 'Usando datos de ejemplo (sin conexión a la base de datos)';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Módulos - ERP</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
        }

        /* Header */
        .header {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h1 {
            color: #333;
            font-size: 2.2em;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .header p {
            color: #666;
            font-size: 1.1em;
            margin-top: 5px;
        }

        .stats {
            display: flex;
            gap: 20px;
        }

        .stat {
            text-align: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 20px;
            border-radius: 10px;
            min-width: 100px;
        }

        .stat-number {
            display: block;
            font-size: 2em;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .stat-label {
            font-size: 0.9em;
            opacity: 0.9;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Mensajes */
        .message {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 10px;
            padding: 15px 20px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .message.info {
            background: rgba(209, 236, 241, 0.95);
            color: #0c5460;
            border-left: 4px solid #17a2b8;
        }

        /* Filtros */
        .filters {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .filter-btn {
            padding: 10px 20px;
            border: 2px solid #e0e0e0;
            background: white;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 14px;
            font-weight: 500;
            color: #666;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .filter-btn:hover {
            border-color: #667eea;
            color: #667eea;
            transform: translateY(-2px);
        }

        .filter-btn.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-color: #667eea;
            color: white;
        }

        /* Grid de módulos */
        .modules-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }

        .module-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            overflow: hidden;
            transition: all 0.3s ease;
            border: 2px solid transparent;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
        }

        .module-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
        }

        .module-card.active {
            border-left: 5px solid #28a745;
        }

        .module-card.inactive {
            border-left: 5px solid #ffc107;
        }

        .module-card.not-installed {
            border-left: 5px solid #6c757d;
        }

        .module-header {
            padding: 20px;
            display: flex;
            align-items: flex-start;
            gap: 15px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .module-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.8em;
            flex-shrink: 0;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .module-card.active .module-icon {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        }

        .module-card.inactive .module-icon {
            background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);
        }

        .module-card.not-installed .module-icon {
            background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
        }

        .module-info {
            flex: 1;
        }

        .module-info h3 {
            margin: 0 0 5px 0;
            color: #333;
            font-size: 1.3em;
            font-weight: 600;
        }

        .module-version {
            font-size: 0.75em;
            color: #888;
            background: #f0f0f0;
            padding: 3px 8px;
            border-radius: 12px;
            font-weight: 500;
            margin-bottom: 5px;
            display: inline-block;
        }

        .module-tech-name {
            font-size: 0.85em;
            color: #666;
            font-family: 'Courier New', monospace;
            background: #f8f9fa;
            padding: 2px 6px;
            border-radius: 4px;
            display: inline-block;
        }

        .module-status {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            border-radius: 20px;
            font-size: 0.85em;
            font-weight: 500;
            white-space: nowrap;
        }

        .module-card.active .module-status {
            background: #d4edda;
            color: #155724;
        }

        .module-card.inactive .module-status {
            background: #fff3cd;
            color: #856404;
        }

        .module-card.not-installed .module-status {
            background: #f8f9fa;
            color: #6c757d;
        }

        .module-body {
            padding: 20px;
        }

        .module-description {
            color: #555;
            line-height: 1.6;
            margin-bottom: 15px;
            font-size: 0.95em;
        }

        .module-meta {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
            flex-wrap: wrap;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 5px;
            color: #777;
            font-size: 0.85em;
            background: #f8f9fa;
            padding: 4px 8px;
            border-radius: 6px;
        }

        .meta-item i {
            color: #667eea;
        }

        .module-actions {
            padding: 20px;
            border-top: 1px solid rgba(0, 0, 0, 0.05);
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn {
            padding: 10px 15px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            line-height: 1.4;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            justify-content: center;
            flex: 1;
            min-width: 120px;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .btn-primary {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            color: white;
        }

        .btn-success {
            background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%);
            color: white;
        }

        .btn-warning {
            background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);
            color: #212529;
        }

        .btn-danger {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: white;
        }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                text-align: center;
                gap: 20px;
            }

            .stats {
                justify-content: center;
                gap: 10px;
            }

            .filters {
                justify-content: center;
            }

            .modules-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .module-header {
                flex-direction: column;
                text-align: center;
                gap: 10px;
            }

            .module-actions {
                flex-direction: column;
            }

            .btn {
                width: 100%;
            }
        }

        /* Animaciones */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .module-card {
            animation: fadeIn 0.6s ease backwards;
        }

        .module-card:nth-child(1) { animation-delay: 0.1s; }
        .module-card:nth-child(2) { animation-delay: 0.2s; }
        .module-card:nth-child(3) { animation-delay: 0.3s; }
        .module-card:nth-child(4) { animation-delay: 0.4s; }
        .module-card:nth-child(5) { animation-delay: 0.5s; }
        .module-card:nth-child(6) { animation-delay: 0.6s; }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="header-content">
                <div>
                    <h1><i class="fas fa-th-large"></i> Sistema de Módulos</h1>
                    <p>Gestiona los módulos disponibles para tu ERP</p>
                </div>
                <div class="stats">
                    <div class="stat">
                        <span class="stat-number"><?php echo count($modulos); ?></span>
                        <span class="stat-label">Total</span>
                    </div>
                    <div class="stat">
                        <span class="stat-number"><?php echo count(array_filter($modulos, fn($m) => $m['estado'] === 'activo')); ?></span>
                        <span class="stat-label">Activos</span>
                    </div>
                    <div class="stat">
                        <span class="stat-number"><?php echo count(array_filter($modulos, fn($m) => $m['estado'] === 'inactivo')); ?></span>
                        <span class="stat-label">Inactivos</span>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($mensaje): ?>
            <div class="message info">
                <i class="fas fa-info-circle"></i>
                <span><?php echo htmlspecialchars($mensaje); ?></span>
            </div>
        <?php endif; ?>

        <!-- Filtros -->
        <div class="filters">
            <button class="filter-btn active" onclick="filtrarModulos('todos')">
                <i class="fas fa-th"></i> Todos
            </button>
            <button class="filter-btn" onclick="filtrarModulos('activos')">
                <i class="fas fa-check-circle"></i> Activos
            </button>
            <button class="filter-btn" onclick="filtrarModulos('inactivos')">
                <i class="fas fa-pause-circle"></i> Inactivos
            </button>
            <button class="filter-btn" onclick="filtrarModulos('no-instalados')">
                <i class="fas fa-download"></i> Por Instalar
            </button>
        </div>

        <!-- Grid de módulos -->
        <div class="modules-grid" id="modulesGrid">
            <?php foreach ($modulos as $index => $modulo): ?>
                <div class="module-card <?php echo $modulo['estado']; ?>"
                     data-modulo-id="<?php echo $modulo['id']; ?>"
                     data-estado="<?php echo $modulo['estado']; ?>"
                     data-nombre-tecnico="<?php echo $modulo['nombre_tecnico']; ?>"
                     data-index="<?php echo $index; ?>"
                     style="animation-delay: <?php echo ($index + 1) * 0.1; ?>s;">

                    <div class="module-header">
                        <div class="module-icon">
                            <i class="<?php echo htmlspecialchars($modulo['icono']); ?>"></i>
                        </div>
                        <div class="module-info">
                            <h3><?php echo htmlspecialchars($modulo['nombre']); ?></h3>
                            <span class="module-version">v<?php echo htmlspecialchars($modulo['version']); ?></span>
                            <span class="module-tech-name"><?php echo htmlspecialchars($modulo['nombre_tecnico']); ?></span>
                        </div>
                        <div class="module-status">
                            <i class="fas <?php echo
                                $modulo['estado'] === 'activo' ? 'fa-check-circle' :
                                ($modulo['estado'] === 'inactivo' ? 'fa-pause-circle' : 'fa-times-circle'); ?>"></i>
                            <span><?php echo ucfirst(str_replace('-', ' ', $modulo['estado'])); ?></span>
                        </div>
                    </div>

                    <div class="module-body">
                        <p class="module-description"><?php echo htmlspecialchars($modulo['descripcion']); ?></p>

                        <div class="module-meta">
                            <span class="meta-item">
                                <i class="fas fa-layer-group"></i>
                                <?php echo ucfirst(htmlspecialchars($modulo['categoria'])); ?>
                            </span>
                            <?php if ($modulo['autor']): ?>
                                <span class="meta-item">
                                    <i class="fas fa-user"></i>
                                    <?php echo htmlspecialchars($modulo['autor']); ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="module-actions">
                        <?php if ($modulo['estado'] === 'no-instalado'): ?>
                            <button class="btn btn-primary" onclick="gestionarModulo(<?php echo $modulo['id']; ?>, 'instalar')">
                                <i class="fas fa-download"></i> Instalar
                            </button>
                        <?php elseif ($modulo['estado'] === 'inactivo'): ?>
                            <button class="btn btn-success" onclick="gestionarModulo(<?php echo $modulo['id']; ?>, 'activar')">
                                <i class="fas fa-play"></i> Activar
                            </button>
                            <button class="btn btn-danger" onclick="gestionarModulo(<?php echo $modulo['id']; ?>, 'desinstalar')">
                                <i class="fas fa-trash"></i> Desinstalar
                            </button>
                        <?php elseif ($modulo['estado'] === 'activo'): ?>
                            <?php if ($modulo['nombre_tecnico'] !== 'dashboard'): ?>
                                <button class="btn btn-warning" onclick="gestionarModulo(<?php echo $modulo['id']; ?>, 'desactivar')">
                                    <i class="fas fa-pause"></i> Desactivar
                                </button>
                                <button class="btn btn-danger" onclick="gestionarModulo(<?php echo $modulo['id']; ?>, 'desinstalar')">
                                    <i class="fas fa-trash"></i> Desinstalar
                                </button>
                            <?php else: ?>
                                <button class="btn btn-warning" disabled>
                                    <i class="fas fa-lock"></i> Módulo del Sistema
                                </button>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Modales -->
    <div id="modal-confirmacion" style="display: none;">
        <div class="modal-contenido">
            <div class="modal-header">
                <h3 id="modal-titulo">Confirmar Acción</h3>
                <button class="modal-cerrar" onclick="cerrarModal()">&times;</button>
            </div>
            <div class="modal-cuerpo">
                <p id="modal-mensaje">¿Estás seguro de realizar esta acción?</p>
                <div id="modal-detalles" class="modal-detalles"></div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="cerrarModal()">Cancelar</button>
                <button class="btn btn-primary" id="modal-confirmar">Confirmar</button>
            </div>
        </div>
    </div>

    <div id="modal-progreso" style="display: none;">
        <div class="modal-contenido modal-progreso">
            <div class="modal-cuerpo">
                <div class="progreso-contenido">
                    <i class="fas fa-spinner fa-spin"></i>
                    <h3 id="progreso-titulo">Procesando...</h3>
                    <p id="progreso-mensaje">Por favor, espera un momento.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Estilos adicionales para modales -->
    <style>
        .modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10000;
            animation: fadeIn 0.3s ease;
        }

        .modal-contenido {
            background: white;
            border-radius: 12px;
            max-width: 500px;
            width: 90%;
            max-height: 80vh;
            overflow: hidden;
            animation: slideIn 0.3s ease;
        }

        .modal-progreso .modal-contenido {
            max-width: 400px;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 25px;
            border-bottom: 1px solid #f0f0f0;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }

        .modal-header h3 {
            margin: 0;
            color: #333;
            font-size: 1.3em;
            font-weight: 600;
        }

        .modal-cerrar {
            background: none;
            border: none;
            font-size: 1.5em;
            cursor: pointer;
            color: #999;
            padding: 0;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: all 0.3s ease;
        }

        .modal-cerrar:hover {
            background: #f0f0f0;
            color: #333;
        }

        .modal-cuerpo {
            padding: 25px;
        }

        .modal-detalles {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-top: 15px;
            border-left: 4px solid #667eea;
        }

        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            padding: 20px 25px;
            border-top: 1px solid #f0f0f0;
            background: #f8f9fa;
        }

        .progreso-contenido {
            text-align: center;
            padding: 30px 20px;
        }

        .progreso-contenido i {
            font-size: 3em;
            color: #667eea;
            margin-bottom: 20px;
        }

        .progreso-contenido h3 {
            margin: 0 0 10px 0;
            color: #333;
            font-size: 1.2em;
        }

        .progreso-contenido p {
            margin: 0;
            color: #666;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: scale(0.9);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }
    </style>

    <!-- JavaScript -->
    <script src="modulos.js"></script>
</body>
</html>
