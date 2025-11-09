<?php
// Verificación de sesión usando el componente existente
require_once '../../componentes/Auth/SessionManager.php';
SessionManager::checkSession();
?>
<?php include '../../componentes/Head/Head.php'; ?>

<style>
    <?php include "../../escritorio/escritorio.css"; ?>
    <?php include "css/clientes.css"; ?>
</style>

<main>
    <?php include '../../componentes/header-sup-admin/HeaderSupAdmin.php'; ?>
    <?php include '../../componentes/header-inf-admin/HeaderInfAdmin.php'; ?>

    <div id="escritorio">
        <?php include '../../componentes/Menu-Admin/MenuAdmin.php'; ?>

        <div id="clientes-content" class="main-content">
            <!-- Toolbar -->
            <div class="toolbar">
                <div class="row g-3 align-items-center">
                    <div class="col-md-4">
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-primary" id="nuevo-cliente-btn">
                                <i class="fas fa-plus"></i> Nuevo cliente
                            </button>
                            <button type="button" class="btn btn-success" id="importar-clientes-btn">
                                <i class="fas fa-file-import"></i> Importar
                            </button>
                            <button type="button" class="btn btn-info" id="exportar-clientes-btn">
                                <i class="fas fa-file-export"></i> Exportar
                            </button>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input type="text" class="form-control" id="buscar-cliente" placeholder="Buscar clientes..." />
                        </div>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select" id="filtro-tipo">
                            <option value="">Todos los tipos</option>
                            <option value="particular">Particular</option>
                            <option value="empresa">Empresa</option>
                            <option value="autonomo">Autónomo</option>
                            <option value="ong">ONG</option>
                            <option value="publico">Público</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select" id="filtro-estado">
                            <option value="">Todos los estados</option>
                            <option value="activos">Activos</option>
                            <option value="bloqueados">Bloqueados</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Área de contenido -->
            <div class="content-area p-3">
                <!-- Tabla de clientes -->
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th style="width: 40px;">
                                    <input type="checkbox" id="seleccionar-todos" />
                                </th>
                                <th style="width: 120px;">Código</th>
                                <th>Nombre Comercial</th>
                                <th style="width: 150px;">NIF/CIF</th>
                                <th style="width: 120px;">Tipo</th>
                                <th style="width: 120px;">Teléfono</th>
                                <th style="width: 180px;">Email</th>
                                <th style="width: 100px;">Estado</th>
                                <th style="width: 180px;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="clientes-tbody"></tbody>
                    </table>
                </div>

                <!-- Mensaje sin clientes -->
                <div id="no-clientes" class="text-center py-5" style="display: none;">
                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">No hay clientes disponibles</h4>
                    <p class="text-muted">Crea un nuevo cliente con el botón "Nuevo cliente".</p>
                </div>

                <!-- Paginación -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="small text-muted">
                        Mostrando <span id="clientes-desde">0</span>–<span id="clientes-hasta">0</span> de <span id="clientes-total">0</span> clientes
                    </div>
                    <nav>
                        <ul id="pagination" class="pagination pagination-sm mb-0"></ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: Crear/Editar Cliente -->
    <div class="modal fade" id="modal-cliente" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-cliente-title">Nuevo Cliente</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="form-cliente">
                        <input type="hidden" id="cliente-id" />

                        <!-- Tabs para organizar la información -->
                        <ul class="nav nav-tabs mb-3" id="clienteTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="datos-tab" data-bs-toggle="tab" data-bs-target="#datos" type="button">Datos Generales</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="contacto-tab" data-bs-toggle="tab" data-bs-target="#contacto" type="button">Contacto</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="facturacion-tab" data-bs-toggle="tab" data-bs-target="#facturacion" type="button">Facturación</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="observaciones-tab" data-bs-toggle="tab" data-bs-target="#observaciones" type="button">Observaciones</button>
                            </li>
                        </ul>

                        <div class="tab-content" id="clienteTabsContent">
                            <!-- Pestaña Datos Generales -->
                            <div class="tab-pane fade show active" id="datos" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="codigo" class="form-label">Código *</label>
                                        <input type="text" class="form-control" id="codigo" required />
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="tipo_cliente" class="form-label">Tipo Cliente *</label>
                                        <select class="form-select" id="tipo_cliente" required>
                                            <option value="">Seleccionar tipo</option>
                                            <option value="particular">Particular</option>
                                            <option value="empresa">Empresa</option>
                                            <option value="autonomo">Autónomo</option>
                                            <option value="ong">ONG</option>
                                            <option value="publico">Público</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="nombre_comercial" class="form-label">Nombre Comercial *</label>
                                        <input type="text" class="form-control" id="nombre_comercial" required />
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="razon_social" class="form-label">Razón Social</label>
                                        <input type="text" class="form-control" id="razon_social" />
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="nif_cif" class="form-label">NIF/CIF</label>
                                        <input type="text" class="form-control" id="nif_cif" />
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="web" class="form-label">Web</label>
                                        <input type="url" class="form-control" id="web" />
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label for="direccion" class="form-label">Dirección</label>
                                        <input type="text" class="form-control" id="direccion" />
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3 mb-3">
                                        <label for="codigo_postal" class="form-label">C.P.</label>
                                        <input type="text" class="form-control" id="codigo_postal" />
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="ciudad" class="form-label">Ciudad</label>
                                        <input type="text" class="form-control" id="ciudad" />
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="provincia" class="form-label">Provincia</label>
                                        <input type="text" class="form-control" id="provincia" />
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="pais" class="form-label">País</label>
                                        <input type="text" class="form-control" id="pais" value="España" />
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3 mb-3">
                                        <label for="activo" class="form-label">Estado</label>
                                        <div class="form-check form-switch mt-2">
                                            <input class="form-check-input" type="checkbox" id="activo" checked>
                                            <label class="form-check-label" for="activo">Activo</label>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="bloqueado" class="form-label">Bloqueado</label>
                                        <div class="form-check form-switch mt-2">
                                            <input class="form-check-input" type="checkbox" id="bloqueado">
                                            <label class="form-check-label" for="bloqueado">Bloqueado por impago</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Pestaña Contacto -->
                            <div class="tab-pane fade" id="contacto" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="contacto_principal" class="form-label">Contacto Principal</label>
                                        <input type="text" class="form-control" id="contacto_principal" />
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="cargo_contacto" class="form-label">Cargo</label>
                                        <input type="text" class="form-control" id="cargo_contacto" />
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="telefono" class="form-label">Teléfono</label>
                                        <input type="tel" class="form-control" id="telefono" />
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="telefono2" class="form-label">Teléfono 2</label>
                                        <input type="tel" class="form-control" id="telefono2" />
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email" />
                                    </div>
                                </div>
                            </div>

                            <!-- Pestaña Facturación -->
                            <div class="tab-pane fade" id="facturacion" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="forma_pago" class="form-label">Forma de Pago</label>
                                        <select class="form-select" id="forma_pago">
                                            <option value="contado">Contado</option>
                                            <option value="transferencia">Transferencia</option>
                                            <option value="tarjeta">Tarjeta</option>
                                            <option value="cheque">Cheque</option>
                                            <option value="paypal">PayPal</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="dias_credito" class="form-label">Días de Crédito</label>
                                        <input type="number" class="form-control" id="dias_credito" value="0" min="0" />
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="limite_credito" class="form-label">Límite de Crédito (€)</label>
                                        <input type="number" class="form-control" id="limite_credito" value="0" step="0.01" min="0" />
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="saldo_pendiente" class="form-label">Saldo Pendiente (€)</label>
                                        <input type="number" class="form-control" id="saldo_pendiente" value="0" step="0.01" min="0" readonly />
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="importe_acumulado" class="form-label">Importe Acumulado (€)</label>
                                        <input type="number" class="form-control" id="importe_acumulado" value="0" step="0.01" min="0" readonly />
                                    </div>
                                </div>
                            </div>

                            <!-- Pestaña Observaciones -->
                            <div class="tab-pane fade" id="observaciones" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label for="observaciones" class="form-label">Observaciones</label>
                                        <textarea class="form-control" id="observaciones" rows="8"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="guardar-cliente">
                        <i class="fas fa-save"></i> Guardar Cliente
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: Ver Detalles Cliente -->
    <div class="modal fade" id="modal-detalles" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detalles del Cliente</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="detalles-cliente-content">
                    <!-- Los detalles se cargarán dinámicamente -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" id="editar-desde-detalles">
                        <i class="fas fa-edit"></i> Editar Cliente
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: Eliminar Cliente -->
    <div class="modal fade" id="modal-eliminar" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirmar Eliminación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>¿Está seguro que desea eliminar el cliente <strong id="nombre-cliente-eliminar"></strong>?</p>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i> Esta acción no se puede deshacer.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="confirmar-eliminar">
                        <i class="fas fa-trash"></i> Eliminar
                    </button>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/clientes.js"></script>
