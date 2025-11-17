<?php
require_once '../../componentes/Auth/SessionManager.php';
SessionManager::checkSession();
?>
<?php include '../../componentes/Head/Head.php'; ?>

<style>
    <?php include "../../escritorio/escritorio.css"; ?>
    <?php include "css/configuracion.css"; ?>
</style>

<main id="configuracion">
    <?php include '../../componentes/header-sup-admin/HeaderSupAdmin.php'; ?>
    <?php include '../../componentes/header-inf-admin/HeaderInfAdmin.php'; ?>

    <div id="escritorio">
        <?php include '../../componentes/Menu-Admin/MenuAdmin.php'; ?>

        <div id="configuracion-content" class="main-content">
            <div class="toolbar">
                <div class="row g-3 align-items-center">
                    <div class="col-md-4">
                        <h1 class="page-title">
                            <i class="fas fa-cogs"></i> Configuración del sistema
                        </h1>
                        <p class="text-muted small mb-0">Gestiona módulos, permisos y parámetros globales.</p>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input type="text" class="form-control" id="buscar-modulo" placeholder="Buscar módulo por nombre o clave...">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select" id="filtro-estado">
                            <option value="">Todos los estados</option>
                            <option value="activo">Activos</option>
                            <option value="inactivo">Inactivos</option>
                            <option value="no_instalado">No instalados</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select" id="filtro-categoria">
                            <option value="">Todas las categorías</option>
                            <option value="sistema">Sistema</option>
                            <option value="crm">CRM</option>
                            <option value="ventas">Ventas</option>
                            <option value="compras">Compras</option>
                            <option value="inventario">Inventario</option>
                            <option value="contabilidad">Contabilidad</option>
                            <option value="rrhh">RRHH</option>
                            <option value="produccion">Producción</option>
                        </select>
                    </div>
                </div>
            </div>

            <div id="configuracion-alert" class="alert" role="alert" style="display:none;"></div>

            <div class="stats-grid" id="configuracion-stats">
                <div class="stat-card">
                    <div class="stat-icon bg-primary text-white"><i class="fas fa-layer-group"></i></div>
                    <div>
                        <p class="label">Módulos totales</p>
                        <h3 id="stat-total-modulos">0</h3>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon bg-success text-white"><i class="fas fa-download"></i></div>
                    <div>
                        <p class="label">Instalados</p>
                        <h3 id="stat-modulos-instalados">0</h3>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon bg-teal text-white"><i class="fas fa-check-circle"></i></div>
                    <div>
                        <p class="label">Activos</p>
                        <h3 id="stat-modulos-activos">0</h3>
                    </div>
                </div>
            </div>

            <div class="configuracion-layout">
                <div class="modulos-panel">
                    <div class="panel-header">
                        <h5><i class="fas fa-layer-group"></i> Módulos del sistema</h5>
                        <span class="badge bg-secondary" id="contador-modulos">0 módulos</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table align-middle" id="tabla-modulos">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Categoría</th>
                                    <th>Versión</th>
                                    <th>Estado</th>
                                    <th>Orden menú</th>
                                    <th style="width: 60px;">&nbsp;</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                    <div id="modulos-empty" class="empty-state" style="display:none;">
                        <i class="fas fa-folder-open"></i>
                        <p>No se encontraron módulos con los filtros aplicados.</p>
                    </div>
                </div>

                <div class="detalle-panel" id="detalle-modulo">
                    <div class="panel-header">
                        <div>
                            <h5 id="detalle-nombre">Selecciona un módulo</h5>
                            <p class="text-muted" id="detalle-descripcion">Haz clic en un módulo para ver sus configuraciones.</p>
                        </div>
                        <div class="detalle-estado">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="toggle-activo" disabled>
                                <label class="form-check-label" for="toggle-activo">Activo</label>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="toggle-instalado" disabled>
                                <label class="form-check-label" for="toggle-instalado">Instalado</label>
                            </div>
                        </div>
                    </div>

                    <div class="detalle-meta">
                        <div>
                            <p class="label">Nombre técnico</p>
                            <span id="detalle-tecnico">-</span>
                        </div>
                        <div>
                            <p class="label">Categoría</p>
                            <span id="detalle-categoria">-</span>
                        </div>
                        <div>
                            <p class="label">Versión</p>
                            <span id="detalle-version">-</span>
                        </div>
                        <div>
                            <p class="label">Orden en menú</p>
                            <input type="number" min="1" class="form-control" id="input-menu-order" placeholder="-" disabled>
                        </div>
                    </div>

                    <div class="detalle-seccion">
                        <div class="seccion-header">
                            <h6><i class="fas fa-wrench"></i> Configuraciones del módulo</h6>
                            <span class="badge bg-light text-dark" id="contador-configs">0</span>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm" id="tabla-configuraciones">
                                <thead>
                                    <tr>
                                        <th>Clave</th>
                                        <th>Valor</th>
                                        <th>Tipo</th>
                                        <th style="width: 110px;">&nbsp;</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                        <div class="empty-state" id="configs-empty" style="display:none;">
                            <p>No hay configuraciones declaradas para este módulo.</p>
                        </div>
                    </div>

                    <div class="detalle-seccion">
                        <div class="seccion-header">
                            <h6><i class="fas fa-user-shield"></i> Permisos por rol</h6>
                        </div>
                        <div id="permisos-container" class="permisos-grid">
                            <p class="text-muted">Selecciona un módulo para ver y editar permisos.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include '../../componentes/Footer/Footer.php'; ?>
</main>

<div class="modal fade" id="modal-configuracion" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-configuracion-title">Editar configuración</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <form id="form-configuracion">
                    <input type="hidden" id="config-id">
                    <div class="mb-3">
                        <label class="form-label">Clave</label>
                        <input type="text" class="form-control" id="config-clave" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Valor</label>
                        <div id="config-input-wrapper"></div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Descripción</label>
                        <p class="small text-muted" id="config-descripcion">-</p>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btn-guardar-configuracion">
                    <i class="fas fa-save"></i> Guardar cambios
                </button>
            </div>
        </div>
    </div>
</div>

<script src="../../comun/config.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/configuracion.js"></script>
