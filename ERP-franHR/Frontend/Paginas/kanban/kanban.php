<?php
// Verificación de sesión
require_once '../../componentes/Auth/SessionManager.php';
SessionManager::checkSession(); // Protege la página
$currentUser = SessionManager::getUserInfo();
?>
<?php include '../../componentes/Head/Head.php'; ?>

<!-- Estilos de layout y específicos del kanban -->
<style>
    <?php include "../../escritorio/escritorio.css"; ?><?php include "kanban.css"; ?>
</style>

<main>
    <?php include '../../componentes/header-sup-admin/HeaderSupAdmin.php'; ?>
    <?php include '../../componentes/header-inf-admin/HeaderInfAdmin.php'; ?>

    <div id="escritorio">
        <?php include '../../componentes/Menu-Admin/MenuAdmin.php'; ?>

        <!-- Contenido principal del Kanban -->

        <div id="kanban-content">
            <div id="kanban-header">
                <h1>Tablero Kanban</h1>
                <div class="header-buttons">
                    <button id="add-column-btn" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Agregar Columna
                    </button>
                    <button id="save-board-btn" class="btn btn-success">
                        <i class="fas fa-save"></i> Guardar Tablero
                    </button>
                </div>
            </div>

            <div id="kanban-board">
                <!-- Las columnas se cargarán dinámicamente aquí -->
            </div>

            <div id="empty-board" class="empty-board" style="display: none;">
                <div class="empty-icon">
                    <i class="fas fa-columns"></i>
                </div>
                <h3>¡Comienza tu proyecto!</h3>
                <p>Crea tu primera columna para organizar tus tareas</p>
                <button id="create-first-column" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Crear Primera Columna
                </button>
            </div>
        </div>
    </div>

    <!-- Modal para crear/editar columnas -->
    <div id="column-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="column-modal-title">Crear Nueva Columna</h3>
                <button class="modal-close" id="close-column-modal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="column-form">
                    <div class="form-group">
                        <label for="column-name">Nombre de la Columna:</label>
                        <input type="text" id="column-name" name="column-name" placeholder="Ej: Por Hacer, En Proceso, Completado" required>
                    </div>
                    <div class="form-group">
                        <label for="column-color">Color de la Columna:</label>
                        <div class="color-picker">
                            <input type="color" id="column-color" name="column-color" value="#875A7B">
                            <div class="color-presets">
                                <div class="color-preset" data-color="#875A7B" style="background: #875A7B;"></div>
                                <div class="color-preset" data-color="#28a745" style="background: #28a745;"></div>
                                <div class="color-preset" data-color="#dc3545" style="background: #dc3545;"></div>
                                <div class="color-preset" data-color="#ffc107" style="background: #ffc107;"></div>
                                <div class="color-preset" data-color="#17a2b8" style="background: #17a2b8;"></div>
                                <div class="color-preset" data-color="#6f42c1" style="background: #6f42c1;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="column-limit">Límite de Tarjetas (opcional):</label>
                        <input type="number" id="column-limit" name="column-limit" min="1" placeholder="Sin límite">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" id="cancel-column">Cancelar</button>
                <button type="submit" form="column-form" class="btn-primary" id="save-column">Guardar Columna</button>
            </div>
        </div>
    </div>

    <!-- Modal para crear/editar tarjetas -->
    <div id="card-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="card-modal-title">Crear Nueva Tarjeta</h3>
                <button class="modal-close" id="close-card-modal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="card-form">
                    <div class="form-group">
                        <label for="card-title">Título de la Tarjeta:</label>
                        <input type="text" id="card-title" name="card-title" placeholder="Título de la tarea" required>
                    </div>
                    <div class="form-group">
                        <label for="card-description">Descripción:</label>
                        <textarea id="card-description" name="card-description" rows="3" placeholder="Descripción detallada de la tarea"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="card-priority">Prioridad:</label>
                        <select id="card-priority" name="card-priority">
                            <option value="low">Baja</option>
                            <option value="medium" selected>Media</option>
                            <option value="high">Alta</option>
                            <option value="urgent">Urgente</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="card-assignee">Asignado a:</label>
                        <select id="card-assignee" name="card-assignee">
                            <option value="">Sin asignar</option>
                            <!-- Opciones de usuarios se cargan dinámicamente -->
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="card-due-date">Fecha límite:</label>
                        <input type="date" id="card-due-date" name="card-due-date">
                    </div>
                    <div class="form-group">
                        <label for="card-tags">Etiquetas:</label>
                        <input type="text" id="card-tags" name="card-tags" placeholder="Separadas por comas: frontend, urgente, bug">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" id="cancel-card">Cancelar</button>
                <button type="submit" form="card-form" class="btn-primary" id="save-card">Guardar Tarjeta</button>
            </div>
        </div>
    </div>

    <!-- Modal de confirmación para eliminar -->
    <div id="confirm-modal" class="modal">
        <div class="modal-content modal-small">
            <div class="modal-header">
                <h3>Confirmar Eliminación</h3>
                <button class="modal-close" id="close-confirm-modal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <p id="confirm-message">¿Estás seguro de que deseas eliminar este elemento?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" id="cancel-delete">Cancelar</button>
                <button type="button" class="btn-danger" id="confirm-delete">Eliminar</button>
            </div>
        </div>
    </div>
</main>

<!-- JavaScript específico del kanban -->
<script>
    <?php include "kanban.js"; ?>
</script>

<?php include '../../componentes/Footer/Footer.php'; ?>