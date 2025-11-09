<?php
// Evitar cualquier salida antes de headers
ob_start();

// Configuración de headers
header('Content-Type: text/html; charset=UTF-8');

// Verificación de sesión
$rutaSessionManager = __DIR__ . '/../Auth/SessionManager.php';
$usuarioAutenticado = false;
$currentUser = null;

if (file_exists($rutaSessionManager)) {
    require_once $rutaSessionManager;

    // Verificar si hay sesión activa
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Verificar autenticación
    if (isset($_SESSION['user_id']) || isset($_SESSION['username'])) {
        $usuarioAutenticado = true;
        $currentUser = [
            'id' => $_SESSION['user_id'] ?? null,
            'usuario' => $_SESSION['username'] ?? 'admin',
            'rol' => $_SESSION['user_rol'] ?? 'admin'
        ];
    }
} else {
    // Si no existe SessionManager, usar datos por defecto para desarrollo
    $usuarioAutenticado = true;
    $currentUser = [
        'id' => 1,
        'usuario' => 'admin',
        'rol' => 'admin'
    ];
}

// Si no está autenticado, redirigir al login
if (!$usuarioAutenticado) {
    header('Location: /Login/login.php');
    exit();
}

// Incluir configuración de base de datos
$rutaConfig = __DIR__ . '/../../api/config.php';
$conexionExitosa = false;

if (file_exists($rutaConfig)) {
    require_once $rutaConfig;

    // Intentar obtener conexión
    try {
        if (function_exists('getConnection')) {
            $pdo = getConnection();
            $conexionExitosa = ($pdo !== null);
        }
    } catch (Exception $e) {
        error_log("Error de conexión: " . $e->getMessage());
        $conexionExitosa = false;
    }
}

// Obtener módulos desde la base de datos
$modulos = [];
$mensaje = '';

if ($conexionExitosa) {
    try {
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
                mc.menu_order as menu_order,
                CASE
                    WHEN m.instalado = 1 AND m.activo = 1 THEN 'activo'
                    WHEN m.instalado = 1 AND m.activo = 0 THEN 'inactivo'
                    WHEN m.instalado = 0 THEN 'no-instalado'
                END as estado,
                CASE
                    WHEN m.instalado = 1 AND m.activo = 1 THEN 1
                    WHEN m.instalado = 1 AND m.activo = 0 THEN 1
                    ELSE 0
                END as disponible
            FROM modulos m
            INNER JOIN (
                SELECT LOWER(TRIM(nombre_tecnico)) AS nt, MAX(id) AS latest_id
                FROM modulos
                GROUP BY LOWER(TRIM(nombre_tecnico))
            ) lm ON LOWER(TRIM(m.nombre_tecnico)) = lm.nt AND m.id = lm.latest_id
            LEFT JOIN (
                SELECT 
                    modulo_id,
                    CAST(MIN(valor) AS UNSIGNED) AS menu_order
                FROM modulo_configuracion
                WHERE clave = 'menu_order'
                GROUP BY modulo_id
            ) mc ON m.id = mc.modulo_id
            ORDER BY
                CASE WHEN m.instalado = 0 THEN 2 ELSE 1 END,
                CAST(COALESCE(mc.menu_order, '999') AS UNSIGNED) ASC,
                m.nombre ASC
        ";

        $stmt = $pdo->query($sql);
        $modulosDB = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Procesar dependencias si existen
        foreach ($modulosDB as &$modulo) {
            $dependencias = $modulo['dependencias'] ?? '';
            $modulo['dependencias'] = $dependencias ? json_decode($dependencias, true) : [];
            $modulo['menu_order'] = $modulo['menu_order'] ?: 999;
        }

        // Deduplicación defensiva por nombre_tecnico (por si la BD contiene variantes)
        $dedup = [];
        foreach ($modulosDB as $m) {
            $nt = isset($m['nombre_tecnico']) ? $m['nombre_tecnico'] : '';
            // Normalizar espacios y caso (incluye tabs/CR/LF/NBSP)
            $nt = str_replace([chr(160), "\t", "\r", "\n"], ' ', $nt);
            $nt = preg_replace('/\s+/u', ' ', $nt);
            $nt = trim($nt);
            $key = function_exists('mb_strtolower') ? mb_strtolower($nt, 'UTF-8') : strtolower($nt);

            // Mantener el registro con id más reciente
            if (!isset($dedup[$key]) || (int)$m['id'] > (int)$dedup[$key]['id']) {
                $dedup[$key] = $m;
            }
        }

        $modulos = array_values($dedup);
    } catch (PDOException $e) {
        error_log("Error al cargar módulos: " . $e->getMessage());
        $mensaje = 'Error al cargar los módulos: ' . $e->getMessage();
    } catch (Exception $e) {
        error_log("Error general al cargar módulos: " . $e->getMessage());
        $mensaje = 'Error general: ' . $e->getMessage();
    }
}

// Si no hay conexión o no hay módulos, mostrar módulos de ejemplo
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
            'disponible' => 1,
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
            'disponible' => 1,
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
            'disponible' => 1,
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
            'disponible' => 1,
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
            'disponible' => 1,
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
            'disponible' => 1,
            'dependencias' => []
        ]
    ];

    if (empty($mensaje)) {
        $mensaje = $conexionExitosa ? 'No hay módulos configurados en el sistema' : 'Usando datos de ejemplo (sin conexión a la base de datos)';
    }
}

// Limpiar buffer de salida y mostrar contenido
ob_end_clean();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Módulos del Sistema - ERP</title>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Estilos específicos -->
    <style>
        <?php include "listadoModulos.css"; ?>
    </style>
</head>
<body>
    <main id="listadoModulos">
        <header class="modulos-header">
            <div class="header-content">
                <h1><i class="fas fa-th-large"></i> Módulos del Sistema</h1>
                <p>Gestiona los módulos disponibles para tu ERP. Instala, activa o desactiva según tus necesidades.</p>
                <?php if ($mensaje): ?>
                    <div class="info-message">
                        <i class="fas fa-info-circle"></i>
                        <span><?php echo htmlspecialchars($mensaje); ?></span>
                    </div>
                <?php endif; ?>
            </div>
            <div class="header-stats">
                <div class="stat-item">
                    <span class="stat-number"><?php echo count($modulos); ?></span>
                    <span class="stat-label">Total Módulos</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number"><?php echo count(array_filter($modulos, fn($m) => $m['estado'] === 'activo')); ?></span>
                    <span class="stat-label">Activos</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number"><?php echo count(array_filter($modulos, fn($m) => $m['estado'] === 'inactivo')); ?></span>
                    <span class="stat-label">Inactivos</span>
                </div>
            </div>
        </header>

        <div class="modulos-container">
            <div class="modulos-filtros">
                <button class="filtro-btn active" data-filtro="todos">
                    <i class="fas fa-th"></i> Todos
                </button>
                <button class="filtro-btn" data-filtro="activos">
                    <i class="fas fa-check-circle"></i> Activos
                </button>
                <button class="filtro-btn" data-filtro="inactivos">
                    <i class="fas fa-pause-circle"></i> Inactivos
                </button>
                <button class="filtro-btn" data-filtro="no-instalados">
                    <i class="fas fa-download"></i> Por Instalar
                </button>
            </div>

            <div id="listadoCard" class="modulos-grid">
                <?php if (empty($modulos)): ?>
                    <div class="no-modulos">
                        <i class="fas fa-inbox"></i>
                        <h3>No hay módulos disponibles</h3>
                        <p>No se encontraron módulos en el sistema.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($modulos as $modulo): ?>
                        <?php
                        // Asegurarnos de que todas las claves existan
                        $modulo = array_merge([
                            'id' => 0,
                            'nombre' => '',
                            'nombre_tecnico' => '',
                            'descripcion' => '',
                            'version' => '1.0.0',
                            'icono' => 'fas fa-cube',
                            'categoria' => 'sistema',
                            'estado' => 'no-instalado',
                            'autor' => null,
                            'fecha_instalacion' => null,
                            'fecha_activacion' => null,
                            'dependencias' => []
                        ], $modulo);
                        ?>
                        <article class="modulo-card estado-<?php echo $modulo['estado']; ?>"
                                 data-modulo-id="<?php echo $modulo['id']; ?>"
                                 data-estado="<?php echo $modulo['estado']; ?>"
                                 data-nombre-tecnico="<?php echo $modulo['nombre_tecnico']; ?>">

                            <div class="modulo-header">
                                <div class="modulo-icono">
                                    <i class="<?php echo htmlspecialchars($modulo['icono']); ?>"></i>
                                </div>
                                <div class="modulo-info">
                                    <h3><?php echo htmlspecialchars($modulo['nombre']); ?></h3>
                                    <span class="modulo-version">v<?php echo htmlspecialchars($modulo['version']); ?></span>
                                    <span class="modulo-tecnico"><?php echo htmlspecialchars($modulo['nombre_tecnico']); ?></span>
                                </div>
                                <div class="modulo-estado">
                                    <i class="<?php echo $modulo['estado'] === 'activo' ? 'fas fa-check-circle' :
                                                ($modulo['estado'] === 'inactivo' ? 'fas fa-pause-circle' : 'fas fa-times-circle'); ?>"></i>
                                    <span><?php echo ucfirst(str_replace('-', ' ', $modulo['estado'])); ?></span>
                                </div>
                            </div>

                            <div class="modulo-body">
                                <p class="modulo-descripcion"><?php echo htmlspecialchars($modulo['descripcion']); ?></p>

                                <div class="modulo-meta">
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

                                <?php if (!empty($modulo['dependencias'])): ?>
                                    <div class="modulo-dependencias">
                                        <strong>Dependencias:</strong>
                                        <div class="dependencias-list">
                                            <?php foreach ($modulo['dependencias'] as $dep): ?>
                                                <span class="dependencia-tag"><?php echo htmlspecialchars($dep); ?></span>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <?php if ($modulo['fecha_instalacion']): ?>
                                    <div class="modulo-fechas">
                                        <small>
                                            <i class="fas fa-calendar"></i>
                                            Instalado: <?php echo date('d/m/Y', strtotime($modulo['fecha_instalacion'])); ?>
                                        </small>
                                        <?php if ($modulo['fecha_activacion']): ?>
                                            <small>
                                                <i class="fas fa-power-off"></i>
                                                Activado: <?php echo date('d/m/Y', strtotime($modulo['fecha_activacion'])); ?>
                                            </small>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="modulo-actions">
                                <?php if ($modulo['estado'] === 'no-instalado'): ?>
                                    <button class="btn btn-primary" onclick="modulosDashboard.instalarModulo(<?php echo $modulo['id']; ?>)">
                                        <i class="fas fa-download"></i> Instalar
                                    </button>
                                <?php elseif ($modulo['estado'] === 'inactivo'): ?>
                                    <button class="btn btn-success" onclick="modulosDashboard.activarModulo(<?php echo $modulo['id']; ?>)">
                                        <i class="fas fa-play"></i> Activar
                                    </button>
                                    <button class="btn btn-danger" onclick="modulosDashboard.desinstalarModulo(<?php echo $modulo['id']; ?>)">
                                        <i class="fas fa-trash"></i> Desinstalar
                                    </button>
                                <?php elseif ($modulo['estado'] === 'activo'): ?>
                                    <?php if ($modulo['nombre_tecnico'] !== 'dashboard'): ?>
                                        <button class="btn btn-warning" onclick="modulosDashboard.desactivarModulo(<?php echo $modulo['id']; ?>)">
                                            <i class="fas fa-pause"></i> Desactivar
                                        </button>
                                        <button class="btn btn-danger" onclick="modulosDashboard.desinstalarModulo(<?php echo $modulo['id']; ?>)">
                                            <i class="fas fa-trash"></i> Desinstalar
                                        </button>
                                    <?php else: ?>
                                        <button class="btn btn-secondary" disabled>
                                            <i class="fas fa-lock"></i> Módulo del Sistema
                                        </button>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </article>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Modal de confirmación -->
        <div class="modal" id="modal-confirmacion" style="display: none;">
            <div class="modal-contenido">
                <div class="modal-header">
                    <h3 id="modal-titulo">Confirmar Acción</h3>
                    <button class="modal-cerrar" onclick="modulosDashboard.cerrarModal()">&times;</button>
                </div>
                <div class="modal-cuerpo">
                    <p id="modal-mensaje">¿Estás seguro de realizar esta acción?</p>
                    <div id="modal-detalles" class="modal-detalles"></div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" onclick="modulosDashboard.cerrarModal()">Cancelar</button>
                    <button class="btn btn-primary" id="modal-confirmar">Confirmar</button>
                </div>
            </div>
        </div>

        <!-- Modal de progreso -->
        <div class="modal" id="modal-progreso" style="display: none;">
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
    </main>

    <!-- JavaScript específico -->
    <script>
        <?php include "listadoModulos.js"; ?>
    </script>
</body>
</html>
