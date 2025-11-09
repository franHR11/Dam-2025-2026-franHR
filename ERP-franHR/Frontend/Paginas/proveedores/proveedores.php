<?php
// Verificación de sesión usando el componente existente
require_once '../../componentes/Auth/SessionManager.php';
SessionManager::checkSession();
?>
<?php include '../../componentes/Head/Head.php'; ?>

<style>
    <?php include "../../escritorio/escritorio.css"; ?>
    <?php include "css/proveedores.css"; ?>
</style>

<main>
    <?php include '../../componentes/header-sup-admin/HeaderSupAdmin.php'; ?>
    <?php include '../../componentes/header-inf-admin/HeaderInfAdmin.php'; ?>

    <div id="escritorio">
        <?php include '../../componentes/Menu-Admin/MenuAdmin.php'; ?>

        <div id="proveedores-content" class="main-content">
            <!-- Toolbar -->
            <div class="toolbar">
                <div class="row g-3 align-items-center">
                    <div class="col-md-4">
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-primary" id="nuevo-proveedor-btn">
                                <i class="fas fa-plus"></i> Nuevo proveedor
                            </button>
                            <button type="button" class="btn btn-info" id="gestionar-contactos-btn">
                                <i class="fas fa-address-book"></i> Contactos
                            </button>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input type="text" class="form-control" id="buscar-proveedor" placeholder="Buscar proveedores..." />
                        </div>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select" id="filtro-tipo">
                            <option value="">Todos los tipos</option>
                            <option value="material">Material</option>
                            <option value="servicio">Servicio</option>
                            <option value="transporte">Transporte</option>
                            <option value="seguro">Seguro</option>
                            <option value="suministro">Suministro</option>
                            <option value="tecnologia">Tecnología</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select" id="filtro-estado">
                            <option value="">Todos los estados</option>
                            <option value="activo">Activos</option>
                            <option value="inactivo">Inactivos</option>
                            <option value="bloqueado">Bloqueados</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Área de contenido -->
            <div class="content-area p-3">
                <!-- Tabla de proveedores -->
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th style="width: 40px;">
                                    <input type="checkbox" id="seleccionar-todos" />
                                </th>
                                <th style="width: 120px;">Código</th>
                                <th>Nombre / Razón Social</th>
                                <th style="width: 250px;">Contacto</th>
                                <th style="width: 120px;">Tipo</th>
                                <th style="width: 120px;">Estado</th>
                                <th style="width: 150px;">Importes</th>
                                <th style="width: 180px;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="proveedores-tbody"></tbody>
                    </table>
                </div>

                <!-- Mensaje sin proveedores -->
                <div id="no-proveedores" class="text-center py-5" style="display: none;">
                    <i class="fas fa-truck fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">No hay proveedores disponibles</h4>
                    <p class="text-muted">Crea un nuevo proveedor con el botón "Nuevo proveedor".</p>
                </div>

                <!-- Paginación -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="small text-muted">
                        Mostrando <span id="proveedores-desde">0</span>–<span id="proveedores-hasta">0</span> de <span id="proveedores-total">0</span> proveedores
                    </div>
                    <nav>
                        <ul id="pagination" class="pagination pagination-sm mb-0"></ul>
                    </nav>
                </div>
            </div>
        </div>

        <!-- Modal: Crear/Editar Proveedor -->
        <div class="modal fade" id="modal-proveedor" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Nuevo Proveedor</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="form-proveedor">
                            <input type="hidden" id="id" name="id">

                            <!-- Tabs -->
                            <ul class="nav nav-tabs mb-3" id="proveedorTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="datos-tab" data-bs-toggle="tab" data-bs-target="#datos" type="button" role="tab">Datos Generales</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="direccion-tab" data-bs-toggle="tab" data-bs-target="#direccion" type="button" role="tab">Dirección y Contacto</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="comercial-tab" data-bs-toggle="tab" data-bs-target="#comercial" type="button" role="tab">Datos Comerciales</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="config-tab" data-bs-toggle="tab" data-bs-target="#config" type="button" role="tab">Configuración</button>
                                </li>
                            </ul>

                            <div class="tab-content" id="proveedorTabContent">
                                <!-- Tab: Datos Generales -->
                                <div class="tab-pane fade show active" id="datos" role="tabpanel">
                                    <div class="row g-3">
                                        <div class="col-md-3">
                                            <label for="codigo" class="form-label">Código</label>
                                            <input type="text" class="form-control" id="codigo" name="codigo" placeholder="Auto-generado" readonly>
                                        </div>
                                        <div class="col-md-5">
                                            <label for="nombre_comercial" class="form-label">Nombre Comercial *</label>
                                            <input type="text" class="form-control" id="nombre_comercial" name="nombre_comercial" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="razon_social" class="form-label">Razón Social</label>
                                            <input type="text" class="form-control" id="razon_social" name="razon_social">
                                        </div>
                                        <div class="col-md-3">
                                            <label for="nif_cif" class="form-label">NIF/CIF</label>
                                            <input type="text" class="form-control" id="nif_cif" name="nif_cif" placeholder="12345678Z o B12345678">
                                        </div>
                                        <div class="col-md-3">
                                            <label for="tipo_proveedor" class="form-label">Tipo Proveedor *</label>
                                            <select class="form-select" id="tipo_proveedor" name="tipo_proveedor" required>
                                                <option value="">Seleccionar...</option>
                                                <option value="material">Material</option>
                                                <option value="servicio">Servicio</option>
                                                <option value="transporte">Transporte</option>
                                                <option value="seguro">Seguro</option>
                                                <option value="suministro">Suministro</option>
                                                <option value="tecnologia">Tecnología</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label for="web" class="form-label">Sitio Web</label>
                                            <input type="url" class="form-control" id="web" name="web" placeholder="https://www.ejemplo.com">
                                        </div>
                                        <div class="col-md-3">
                                            <label for="observaciones" class="form-label">Observaciones</label>
                                            <textarea class="form-control" id="observaciones" name="observaciones" rows="1"></textarea>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tab: Dirección y Contacto -->
                                <div class="tab-pane fade" id="direccion" role="tabpanel">
                                    <div class="row g-3">
                                        <div class="col-md-8">
                                            <label for="direccion" class="form-label">Dirección</label>
                                            <input type="text" class="form-control" id="direccion" name="direccion">
                                        </div>
                                        <div class="col-md-2">
                                            <label for="codigo_postal" class="form-label">Código Postal</label>
                                            <input type="text" class="form-control" id="codigo_postal" name="codigo_postal">
                                        </div>
                                        <div class="col-md-2">
                                            <label for="ciudad" class="form-label">Ciudad</label>
                                            <input type="text" class="form-control" id="ciudad" name="ciudad">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="provincia" class="form-label">Provincia</label>
                                            <input type="text" class="form-control" id="provincia" name="provincia">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="pais" class="form-label">País</label>
                                            <input type="text" class="form-control" id="pais" name="pais" value="España">
                                        </div>
                                        <div class="col-md-2">
                                            <label for="telefono" class="form-label">Teléfono</label>
                                            <input type="tel" class="form-control" id="telefono" name="telefono">
                                        </div>
                                        <div class="col-md-2">
                                            <label for="telefono2" class="form-label">Teléfono 2</label>
                                            <input type="tel" class="form-control" id="telefono2" name="telefono2">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="email" class="form-label">Email</label>
                                            <input type="email" class="form-control" id="email" name="email">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="contacto_principal" class="form-label">Contacto Principal</label>
                                            <input type="text" class="form-control" id="contacto_principal" name="contacto_principal">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="cargo_contacto" class="form-label">Cargo Contacto</label>
                                            <input type="text" class="form-control" id="cargo_contacto" name="cargo_contacto">
                                        </div>
                                    </div>
                                </div>

                                <!-- Tab: Datos Comerciales -->
                                <div class="tab-pane fade" id="comercial" role="tabpanel">
                                    <div class="row g-3">
                                        <div class="col-md-3">
                                            <label for="forma_pago" class="form-label">Forma de Pago</label>
                                            <select class="form-select" id="forma_pago" name="forma_pago">
                                                <option value="contado">Contado</option>
                                                <option value="transferencia" selected>Transferencia</option>
                                                <option value="tarjeta">Tarjeta</option>
                                                <option value="cheque">Cheque</option>
                                                <option value="paypal">PayPal</option>
                                                <option value="efectivo">Efectivo</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label for="dias_pago" class="form-label">Días de Pago</label>
                                            <input type="number" class="form-control" id="dias_pago" name="dias_pago" value="30" min="0">
                                        </div>
                                        <div class="col-md-3">
                                            <label for="cuenta_bancaria" class="form-label">Cuenta Bancaria</label>
                                            <input type="text" class="form-control" id="cuenta_bancaria" name="cuenta_bancaria" placeholder="ESXX XXXX XXXX XXXX XXXX XXXX">
                                        </div>
                                        <div class="col-md-2">
                                            <label for="swift_bic" class="form-label">SWIFT/BIC</label>
                                            <input type="text" class="form-control" id="swift_bic" name="swift_bic">
                                        </div>
                                        <div class="col-md-2">
                                            <label for="descuento_comercial" class="form-label">Descuento %</label>
                                            <input type="number" class="form-control" id="descuento_comercial" name="descuento_comercial" value="0" min="0" max="100" step="0.01">
                                        </div>
                                    </div>
                                </div>

                                <!-- Tab: Configuración -->
                                <div class="tab-pane fade" id="config" role="tabpanel">
                                    <div class="row g-3">
                                        <div class="col-md-3">
                                            <div class="form-check form-switch mt-4">
                                                <input class="form-check-input" type="checkbox" id="activo" name="activo" checked>
                                                <label class="form-check-label" for="activo">
                                                    Proveedor Activo
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-check form-switch mt-4">
                                                <input class="form-check-input" type="checkbox" id="bloqueado" name="bloqueado">
                                                <label class="form-check-label" for="bloqueado">
                                                    Proveedor Bloqueado
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-check form-switch mt-4">
                                                <input class="form-check-input" type="checkbox" id="es_proveedor_urgente" name="es_proveedor_urgente">
                                                <label class="form-check-label" for="es_proveedor_urgente">
                                                    Proveedor Urgente
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary" form="form-proveedor">
                            <i class="fas fa-save"></i> Guardar Proveedor
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Loading Overlay -->
        <div id="loading-overlay" class="position-fixed top-0 start-0 w-100 h-100 d-flex justify-content-center align-items-center" style="display: none; background-color: rgba(0,0,0,0.5); z-index: 9999;">
            <div class="text-center text-white">
                <div class="spinner-border mb-2" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
                <div class="loading-text">Cargando...</div>
            </div>
        </div>
    </div>
</main>

<!-- Scripts -->
<script>
// Configuración específica para esta página
// La API funciona en localhost/api/ desde cualquier ubicación
window.API_BASE_URL = '/api/';
</script>
<script src="../../comun/config.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/proveedores.js"></script>
</body>
</html>
