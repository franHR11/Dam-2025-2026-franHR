<?php
// Verificación de sesión usando el componente existente
require_once '../../componentes/Auth/SessionManager.php';
SessionManager::checkSession();
?>
<?php include '../../componentes/Head/Head.php'; ?>

<style>
    <?php include "../../escritorio/escritorio.css"; ?>
    <?php include "css/categorias.css"; ?>
</style>

<main>
    <?php include '../../componentes/header-sup-admin/HeaderSupAdmin.php'; ?>
    <?php include '../../componentes/header-inf-admin/HeaderInfAdmin.php'; ?>

    <div id="escritorio">
        <?php include '../../componentes/Menu-Admin/MenuAdmin.php'; ?>

        <div id="categorias-content" class="main-content">
            <!-- Toolbar -->
            <div class="toolbar">
                <div class="row g-3 align-items-center">
                    <div class="col-md-4">
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-primary" id="nueva-categoria-btn">
                                <i class="fas fa-plus"></i> Nueva categoría
                            </button>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input type="text" class="form-control" id="buscar-categoria" placeholder="Buscar categorías..." />
                        </div>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select" id="filtro-estado">
                            <option value="">Todas</option>
                            <option value="1">Activas</option>
                            <option value="0">Inactivas</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select" id="filtro-padre">
                            <option value="">Todas las categorías</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Contenido principal -->
            <div class="content-area p-4">
                <div class="table-responsive">
                    <table class="table table-hover" id="tabla-categorias">
                        <thead>
                            <tr>
                                <th>Imagen</th>
                                <th>Nombre</th>
                                <th>Descripción</th>
                                <th>Categoría Padre</th>
                                <th>Productos</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="categorias-tbody">
                            <!-- Las categorías se cargarán dinámicamente -->
                        </tbody>
                    </table>
                </div>

                <!-- Mensaje cuando no hay categorías -->
                <div id="sin-categorias" class="no-categorias" style="display: none;">
                    <i class="fas fa-folder-open"></i>
                    <h4 class="text-muted">No hay categorías disponibles</h4>
                    <p class="text-muted">Crea una nueva categoría con el botón "Nueva categoría".</p>
                </div>

                <!-- Paginación -->
                <nav aria-label="Paginación de categorías" class="mt-4">
                    <ul class="pagination justify-content-center" id="paginacion-categorias">
                        <!-- Los botones de paginación se generarán dinámicamente -->
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</main>

<!-- Modal para crear/editar categoría -->
<div class="modal fade" id="modal-categoria" tabindex="-1" aria-labelledby="modal-categoria-label" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-categoria-titulo">Nueva Categoría</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="form-categoria">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="categoria-nombre" class="form-label">Nombre <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="categoria-nombre" required maxlength="100">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="categoria-padre" class="form-label">Categoría Padre</label>
                                <select class="form-select" id="categoria-padre">
                                    <option value="">Ninguna (categoría principal)</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="categoria-descripcion" class="form-label">Descripción</label>
                        <textarea class="form-control" id="categoria-descripcion" rows="3" maxlength="500"></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="categoria-imagen" class="form-label">Imagen de la categoría</label>
                                <input type="file" class="form-control" id="categoria-imagen" accept="image/*">
                                <div class="form-text">Formatos permitidos: JPG, PNG, GIF. Máximo 2MB.</div>
                                <div id="vista-previa-imagen" class="mt-2"></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="categoria-activo" class="form-label">Estado</label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="categoria-activo" checked>
                                    <label class="form-check-label" for="categoria-activo">
                                        Categoría activa
                                    </label>
                                </div>
                                <div class="form-text">Las categorías inactivas no se mostrarán en los listados.</div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="eliminar-categoria" style="display: none;">
                    <i class="fas fa-trash"></i> Eliminar
                </button>
                <button type="button" class="btn btn-primary" id="guardar-categoria">
                    <i class="fas fa-save"></i> Guardar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmación de eliminación -->
<div class="modal fade" id="modal-confirmar-eliminacion" tabindex="-1" aria-labelledby="modal-confirmar-label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-confirmar-label">Confirmar Eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>¿Está seguro que desea eliminar esta categoría?</p>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Atención:</strong> Esta acción no se puede deshacer. Los productos asociados a esta categoría quedarán sin categoría asignada.
                </div>
                <div id="productos-asociados-info"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="confirmar-eliminar-categoria">
                    <i class="fas fa-trash"></i> Eliminar Categoría
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/categorias.js"></script>
</main>

</body>
</html>
