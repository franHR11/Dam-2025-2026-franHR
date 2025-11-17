<?php
// Protección de sesión
require_once '../../componentes/Auth/SessionManager.php';
SessionManager::checkSession();
?>
<?php include '../../componentes/Head/Head.php'; ?>

<style>
    <?php include "../../escritorio/escritorio.css"; ?>
    <?php include "css/usuarios.css"; ?>
</style>

<main>
    <?php include '../../componentes/header-sup-admin/HeaderSupAdmin.php'; ?>
    <?php include '../../componentes/header-inf-admin/HeaderInfAdmin.php'; ?>

    <div id="escritorio">
        <?php include '../../componentes/Menu-Admin/MenuAdmin.php'; ?>

        <div id="usuarios-content" class="main-content">
            <!-- Toolbar -->
            <div class="toolbar">
                <div class="row g-3 align-items-center">
                    <div class="col-xl-4">
                        <div class="btn-group">
                            <button class="btn btn-primary" id="nuevo-usuario-btn">
                                <i class="fas fa-user-plus"></i> Nuevo usuario
                            </button>
                            <button class="btn btn-success" id="importar-usuarios-btn">
                                <i class="fas fa-file-import"></i> Importar
                            </button>
                            <button class="btn btn-info" id="exportar-usuarios-btn">
                                <i class="fas fa-file-export"></i> Exportar
                            </button>
                        </div>
                    </div>
                    <div class="col-xl-4">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input type="text" class="form-control" id="buscar-usuario" placeholder="Buscar por usuario, nombre o email">
                        </div>
                    </div>
                    <div class="col-xl-2">
                        <select class="form-select" id="filtro-rol">
                            <option value="">Todos los roles</option>
                            <option value="admin">Administrador</option>
                            <option value="gerente">Gerente</option>
                            <option value="usuario">Usuario</option>
                        </select>
                    </div>
                    <div class="col-xl-2">
                        <select class="form-select" id="filtro-estado">
                            <option value="">Todos</option>
                            <option value="activos">Activos</option>
                            <option value="inactivos">Inactivos</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Tabla -->
            <div class="content-area p-3">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th style="width: 60px;">#</th>
                                <th>Usuario</th>
                                <th>Nombre completo</th>
                                <th>Email</th>
                                <th>Rol</th>
                                <th>Último acceso</th>
                                <th>Estado</th>
                                <th style="width: 160px;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="usuarios-tbody"></tbody>
                    </table>
                </div>

                <div id="no-usuarios" class="text-center py-5" style="display: none;">
                    <i class="fas fa-user-lock fa-3x mb-3"></i>
                    <h4 class="text-muted">No hay usuarios disponibles</h4>
                    <p class="text-muted">Crea un nuevo usuario con el botón "Nuevo usuario".</p>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="small text-muted">
                        Mostrando <span id="usuarios-desde">0</span>–<span id="usuarios-hasta">0</span> de <span id="usuarios-total">0</span> usuarios
                    </div>
                    <nav>
                        <ul id="pagination" class="pagination pagination-sm mb-0"></ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Crear/Editar -->
    <div class="modal fade" id="modal-usuario" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-usuario-title">Nuevo usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <form id="form-usuario">
                        <input type="hidden" id="usuario-id">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Usuario *</label>
                                <input type="text" class="form-control" id="usuario" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Correo electrónico *</label>
                                <input type="email" class="form-control" id="email" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nombre</label>
                                <input type="text" class="form-control" id="nombre">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Apellidos</label>
                                <input type="text" class="form-control" id="apellidos">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Teléfono</label>
                                <input type="text" class="form-control" id="telefono">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Rol *</label>
                                <select class="form-select" id="rol" required>
                                    <option value="">Seleccionar rol</option>
                                    <option value="admin">Administrador</option>
                                    <option value="gerente">Gerente</option>
                                    <option value="usuario">Usuario</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Contraseña <small class="text-muted" id="password-hint">(obligatoria)</small></label>
                                <input type="password" class="form-control" id="contrasena" placeholder="********">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Estado</label>
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" id="activo" checked>
                                    <label class="form-check-label" for="activo">Activo</label>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="guardar-usuario">
                        <i class="fas fa-save"></i> Guardar cambios
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Detalles -->
    <div class="modal fade" id="modal-detalles" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detalles del usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body" id="detalles-usuario-content">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Confirmación -->
    <div class="modal fade" id="modal-confirmar" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-confirmar-title">Confirmar acción</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <p id="modal-confirmar-message"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="confirmar-accion">
                        <i class="fas fa-check"></i> Confirmar
                    </button>
                </div>
            </div>
        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/usuarios.js"></script>
