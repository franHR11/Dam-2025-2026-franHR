<?php
require_once '../../componentes/Auth/SessionManager.php';
SessionManager::checkSession();
?>
<?php include '../../componentes/Head/Head.php'; ?>

<style>
    <?php include "../../escritorio/escritorio.css"; ?>
    <?php include "css/facturacion.css"; ?>
</style>

<main>
    <?php include '../../componentes/header-sup-admin/HeaderSupAdmin.php'; ?>
    <?php include '../../componentes/header-inf-admin/HeaderInfAdmin.php'; ?>

    <div id="escritorio">
        <?php include '../../componentes/Menu-Admin/MenuAdmin.php'; ?>

        <div id="facturacion-content" class="main-content">
            <div class="toolbar">
                <div class="row g-3 align-items-center">
                    <div class="col-lg-3">
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-primary" id="nueva-factura-btn">
                                <i class="fas fa-file-circle-plus"></i> Nueva factura
                            </button>
                            <button type="button" class="btn btn-outline-secondary dropdown-toggle"
                                data-bs-toggle="dropdown">
                                <i class="fas fa-plus"></i> Más
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#" id="nueva-rectificativa-btn">
                                        <i class="fas fa-file-excel"></i> Factura rectificativa
                                    </a></li>
                                <li><a class="dropdown-item" href="#" id="nueva-proforma-btn">
                                        <i class="fas fa-file-alt"></i> Proforma
                                    </a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item" href="#" id="desde-presupuesto-btn">
                                        <i class="fas fa-arrow-right"></i> Desde presupuesto
                                    </a></li>
                            </ul>
                            <button type="button" class="btn btn-outline-secondary" id="exportar-facturas-btn">
                                <i class="fas fa-file-export"></i> Exportar
                            </button>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input type="text" class="form-control" id="buscar-factura"
                                placeholder="Buscar por número o cliente...">
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <select class="form-select" id="filtro-tipo">
                            <option value="">Todos los tipos</option>
                            <option value="venta">Venta</option>
                            <option value="compra">Compra</option>
                            <option value="rectificativa">Rectificativa</option>
                            <option value="proforma">Proforma</option>
                        </select>
                    </div>
                    <div class="col-lg-2">
                        <select class="form-select" id="filtro-estado">
                            <option value="">Todos los estados</option>
                            <option value="borrador">Borrador</option>
                            <option value="pendiente">Pendiente</option>
                            <option value="pagada">Pagada</option>
                            <option value="vencida">Vencida</option>
                            <option value="cancelada">Cancelada</option>
                            <option value="cobrada">Cobrada</option>
                        </select>
                    </div>
                    <div class="col-lg-2">
                        <select class="form-select" id="filtro-cliente">
                            <option value="">Todos los clientes</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th style="width: 50px;">ID</th>
                            <th style="width: 140px;">Número</th>
                            <th>Tipo</th>
                            <th>Cliente/Proveedor</th>
                            <th style="width: 150px;">Fecha</th>
                            <th style="width: 140px;">Vencimiento</th>
                            <th style="width: 140px;">Total</th>
                            <th style="width: 140px;">Estado</th>
                            <th style="width: 180px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="facturas-tbody"></tbody>
                </table>
            </div>

            <div id="no-facturas" class="text-center py-5" style="display:none;">
                <i class="fas fa-file-invoice-dollar fa-3x text-muted mb-3"></i>
                <h4 class="text-muted">Todavía no hay facturas</h4>
                <p class="text-muted">Crea tu primera factura con el botón "Nueva factura".</p>
            </div>
        </div>
    </div>

    <!-- Modal: Crear/Editar factura -->
    <div class="modal fade" id="modal-factura" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-factura-titulo">Nueva factura</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <ul class="nav nav-tabs" id="factura-tabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="datos-tab" data-bs-toggle="tab"
                                data-bs-target="#datos-content" type="button" role="tab">
                                <i class="fas fa-info-circle"></i> Datos Generales
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="lineas-tab" data-bs-toggle="tab"
                                data-bs-target="#lineas-content" type="button" role="tab">
                                <i class="fas fa-list"></i> Líneas
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="pagos-tab" data-bs-toggle="tab" data-bs-target="#pagos-content"
                                type="button" role="tab">
                                <i class="fas fa-credit-card"></i> Pagos
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="observaciones-tab" data-bs-toggle="tab"
                                data-bs-target="#observaciones-content" type="button" role="tab">
                                <i class="fas fa-sticky-note"></i> Observaciones
                            </button>
                        </li>
                    </ul>

                    <form id="form-factura">
                        <input type="hidden" id="factura-id">

                        <div class="tab-content" id="factura-tab-content">
                            <!-- Pestaña Datos Generales -->
                            <div class="tab-pane fade show active" id="datos-content" role="tabpanel">
                                <div class="row g-3 mb-3 mt-2">
                                    <div class="col-md-2">
                                        <label class="form-label">Tipo *</label>
                                        <select class="form-select" id="tipo_factura" required>
                                            <option value="venta">Venta</option>
                                            <option value="compra">Compra</option>
                                            <option value="rectificativa">Rectificativa</option>
                                            <option value="proforma">Proforma</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Serie</label>
                                        <input type="text" class="form-control" id="numero_serie" value="FAC"
                                            maxlength="10">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Número</label>
                                        <input type="text" class="form-control" id="numero_factura"
                                            placeholder="Automático" readonly>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Ejercicio</label>
                                        <input type="number" class="form-control" id="ejercicio" required>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Fecha *</label>
                                        <input type="date" class="form-control" id="fecha" required>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Vencimiento *</label>
                                        <input type="date" class="form-control" id="fecha_vencimiento" required>
                                    </div>
                                </div>

                                <div class="row g-3 mb-3">
                                    <div class="col-md-4">
                                        <label class="form-label">Cliente/Proveedor *</label>
                                        <div class="input-group">
                                            <select class="form-select" id="tipo_tercero">
                                                <option value="cliente">Cliente</option>
                                                <option value="proveedor">Proveedor</option>
                                            </select>
                                            <select class="form-select" id="cliente_id" required></select>
                                            <select class="form-select" id="proveedor_id" style="display:none;"
                                                required></select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Estado</label>
                                        <select class="form-select" id="estado">
                                            <option value="borrador">Borrador</option>
                                            <option value="pendiente">Pendiente</option>
                                            <option value="pagada">Pagada</option>
                                            <option value="vencida">Vencida</option>
                                            <option value="cancelada">Cancelada</option>
                                            <option value="cobrada">Cobrada</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Moneda</label>
                                        <input type="text" class="form-control" id="moneda" value="EUR" maxlength="3">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Forma de pago</label>
                                        <select class="form-select" id="forma_pago">
                                            <option value="transferencia">Transferencia</option>
                                            <option value="contado">Contado</option>
                                            <option value="tarjeta">Tarjeta</option>
                                            <option value="cheque">Cheque</option>
                                            <option value="paypal">PayPal</option>
                                            <option value="efectivo">Efectivo</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Cuenta</label>
                                        <input type="text" class="form-control" id="numero_cuenta" placeholder="IBAN">
                                    </div>
                                </div>

                                <div class="row g-3 mb-3" id="rectificativa-fields" style="display:none;">
                                    <div class="col-md-6">
                                        <label class="form-label">Factura rectificada</label>
                                        <select class="form-select" id="factura_rectificada_id"></select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Motivo de rectificación</label>
                                        <textarea class="form-control" id="motivo_rectificacion" rows="2"></textarea>
                                    </div>
                                </div>

                                <div class="row g-3 mb-3">
                                    <div class="col-md-4">
                                        <label class="form-label">Dirección envío</label>
                                        <select class="form-select" id="direccion_envio_id">
                                            <option value="">Dirección fiscal</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Contacto</label>
                                        <select class="form-select" id="contacto_id">
                                            <option value="">Contacto principal</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Presupuesto origen</label>
                                        <select class="form-select" id="presupuesto_id">
                                            <option value="">Sin presupuesto</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Pestaña Líneas -->
                            <div class="tab-pane fade" id="lineas-content" role="tabpanel">
                                <div class="d-flex justify-content-between align-items-center mb-3 mt-2">
                                    <small class="text-muted">Añade productos o servicios que componen la
                                        factura.</small>
                                    <button type="button" class="btn btn-outline-primary btn-sm" id="agregar-linea-btn">
                                        <i class="fas fa-plus"></i> Añadir línea
                                    </button>
                                </div>
                                <div class="table-responsive">
                                    <table class="table lineas-table">
                                        <thead>
                                            <tr>
                                                <th style="width: 150px;">Producto</th>
                                                <th>Descripción</th>
                                                <th style="width: 90px;">Cant.</th>
                                                <th style="width: 110px;">Precio</th>
                                                <th style="width: 90px;">Dto. %</th>
                                                <th style="width: 90px;">IVA %</th>
                                                <th style="width: 90px;">IRPF %</th>
                                                <th style="width: 120px;">Total línea</th>
                                                <th style="width: 60px;"></th>
                                            </tr>
                                        </thead>
                                        <tbody id="lineas-tbody"></tbody>
                                    </table>
                                </div>

                                <div class="row justify-content-end mt-3">
                                    <div class="col-md-4">
                                        <div class="totales-box">
                                            <div class="d-flex justify-content-between">
                                                <span>Base imponible</span>
                                                <span class="valor" id="total-base">0,00 €</span>
                                            </div>
                                            <div class="d-flex justify-content-between">
                                                <span>Descuento</span>
                                                <span class="valor" id="total-descuento">0,00 €</span>
                                            </div>
                                            <div class="d-flex justify-content-between">
                                                <span>Base descuento</span>
                                                <span class="valor" id="total-base-descuento">0,00 €</span>
                                            </div>
                                            <div class="d-flex justify-content-between">
                                                <span>IVA</span>
                                                <span class="valor" id="total-iva">0,00 €</span>
                                            </div>
                                            <div class="d-flex justify-content-between">
                                                <span>IRPF</span>
                                                <span class="valor" id="total-irpf">0,00 €</span>
                                            </div>
                                            <hr class="border-light">
                                            <div class="d-flex justify-content-between">
                                                <span>Total</span>
                                                <span class="valor" id="total-general">0,00 €</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Pestaña Pagos -->
                            <div class="tab-pane fade" id="pagos-content" role="tabpanel">
                                <div class="row g-3 mb-3 mt-2">
                                    <div class="col-md-3">
                                        <label class="form-label">Fecha pago</label>
                                        <input type="datetime-local" class="form-control" id="fecha_pago">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Importe pagado</label>
                                        <input type="number" step="0.01" class="form-control" id="importe_pagado"
                                            value="0">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Método pago</label>
                                        <input type="text" class="form-control" id="metodo_pago">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Referencia</label>
                                        <input type="text" class="form-control" id="referencia_pago">
                                    </div>
                                </div>

                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i>
                                    El estado de la factura se actualizará automáticamente según los pagos registrados.
                                </div>
                            </div>

                            <!-- Pestaña Observaciones -->
                            <div class="tab-pane fade" id="observaciones-content" role="tabpanel">
                                <div class="mt-2">
                                    <label class="form-label">Términos y condiciones</label>
                                    <textarea class="form-control" id="terminos_condiciones" rows="4"></textarea>
                                </div>
                                <div class="mt-3">
                                    <label class="form-label">Notas internas</label>
                                    <textarea class="form-control" id="notas_internas" rows="4"></textarea>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="guardar-factura-btn">
                        <i class="fas fa-floppy-disk"></i> Guardar factura
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: Detalles -->
    <div class="modal fade" id="modal-detalles" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detalle de la factura</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body" id="detalles-factura-content"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: Eliminar -->
    <div class="modal fade" id="modal-eliminar" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Eliminar factura</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <p>¿Deseas eliminar la factura <strong id="factura-eliminar-numero"></strong>?</p>
                    <div class="alert alert-warning mb-0">
                        <i class="fas fa-triangle-exclamation"></i> Esta acción no se puede deshacer.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="confirmar-eliminar">
                        <i class="fas fa-trash"></i> Eliminar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: Seleccionar Presupuesto -->
    <div class="modal fade" id="modal-seleccionar-presupuesto" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Seleccionar Presupuesto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Número</th>
                                    <th>Cliente</th>
                                    <th>Fecha</th>
                                    <th>Total</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody id="presupuestos-tbody"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/facturacion.js"></script>