<!-- Contenido dinámico de listadoModulos sin headers ni scripts -->
<style>
    <?php include "listadoModulos.css"; ?>
</style>

<?php
// Conexión a la base de datos usando la configuración existente
require_once '../../api/config.php';

try {
    $pdo = getConnection();
    if ($pdo) {
        // Obtener aplicaciones de la base de datos
        $stmt = $pdo->prepare("SELECT nombre, icono, descripcion FROM aplicaciones ORDER BY identificador");
        $stmt->execute();
        $modulos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $modulos = [];
    }
} catch (PDOException $e) {
    $modulos = [];
    error_log("Error de conexión: " . $e->getMessage());
}
?>

<main id="listadoModulos">
    <div id="listadoCard">
        <?php if (!empty($modulos)): ?>
            <?php foreach ($modulos as $modulo): ?>
                <article>
                    <div class="logo">
                        <i class="logo <?php echo htmlspecialchars($modulo['icono']); ?>"></i>
                    </div>
                    <div class="texto">
                        <h3><?php echo htmlspecialchars($modulo['nombre']); ?></h3>
                        <p><?php echo htmlspecialchars($modulo['descripcion'] ?? 'Descripción del módulo ' . $modulo['nombre']); ?></p>
                        <button>Instalar</button>
                    </div>
                </article>
            <?php endforeach; ?>
        <?php else: ?>
            <!-- Fallback en caso de error de conexión -->
            <?php for ($i = 0; $i < 10; $i++) { ?>
                <article>
                    <div class="logo">
                        <i class="logo <?php echo htmlspecialchars($modulo['icono']); ?>"></i>
                    </div>
                    <div class="texto">
                        <h2>Modulo <?php echo $i + 1; ?></h2>
                        <p>Descripción modulo <?php echo $i + 1; ?></p>
                        <button>Instalar</button>
                    </div>
                </article>
            <?php } ?>
        <?php endif; ?>
    </div>
</main>