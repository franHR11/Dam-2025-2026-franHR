<?php
// Verificación de sesión usando el componente existente
require_once '../../componentes/Auth/SessionManager.php';
SessionManager::checkSession(); // Protege la página
$currentUser = SessionManager::getUserInfo();
?>
<?php include '../../componentes/Head/Head.php'; ?>

<!-- Estilos específicos de la plantilla -->
<style>
    <?php include "../../escritorio/escritorio.css"; ?>
    <?php include "plantilla.css"; ?>
</style>

<main>
    <?php include '../../componentes/header-sup-admin/HeaderSupAdmin.php'; ?>
    <?php include '../../componentes/header-inf-admin/HeaderInfAdmin.php'; ?>

    <div id="escritorio">
        <?php include '../../componentes/Menu-Admin/MenuAdmin.php'; ?>

        <!-- Contenido específico de la plantilla -->
        <div id="plantilla-content">
            <!-- Barra de herramientas -->
            <div class="toolbar">
                <div class="row">
                    <div class="col-md-6">
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-primary" id="nuevo-btn">
                                <i class="fas fa-plus"></i>
                                Nuevo
                            </button>
                            <button type="button" class="btn btn-success" id="guardar-btn">
                                <i class="fas fa-save"></i>
                                Guardar
                            </button>
                            <button type="button" class="btn btn-info" id="actualizar-btn">
                                <i class="fas fa-sync"></i>
                                Actualizar
                            </button>
                        </div>
                    </div>
                    <div class="col-md-6 text-end">
                        <div class="search-box">
                            <input type="text" class="form-control" id="buscar-input" placeholder="Buscar...">
                            <button class="btn btn-outline-secondary" id="buscar-btn">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Área de contenido dinámico -->
            <div class="content-area">
                <div id="contenido-dinamico">
                    <!-- El contenido se cargará aquí dinámicamente -->
                    <div class="text-center py-5">
                        <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                        <h3 class="text-muted">Contenido en desarrollo</h3>
                        <p class="text-muted">Esta página está lista para ser personalizada.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modales -->
    <!-- Modal genérico -->
    <div id="modal-generico" class="modal fade" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-titulo">Título del Modal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="modal-cuerpo">
                    <!-- Contenido del modal -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="modal-confirmar">Confirmar</button>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Scripts específicos de la plantilla -->
<script src="plantilla.js"></script>

<script>
    // Inicializar la página cuando el DOM esté listo
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof PlantillaPage !== 'undefined') {
            window.plantillaPage = new PlantillaPage();
            window.plantillaPage.init();
        }
    });
</script>

<?php include '../../componentes/Footer/Footer.php'; ?>