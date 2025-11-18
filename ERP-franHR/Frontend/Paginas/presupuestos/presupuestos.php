<?php
require_once '../../componentes/Auth/SessionManager.php';
SessionManager::checkSession();
?>
<?php include '../../componentes/Head/Head.php'; ?>

<style>
    <?php include "../../escritorio/escritorio.css"; ?>
    <?php include "css/presupuestos.css"; ?>
</style>

<main>
    <?php include '../../componentes/header-sup-admin/HeaderSupAdmin.php'; ?>
    <?php include '../../componentes/header-inf-admin/HeaderInfAdmin.php'; ?>

    <div id="escritorio">
        <?php include '../../componentes/Menu-Admin/MenuAdmin.php'; ?>

        <div id="presupuestos-content" class="main-content">
            <div class="toolbar">
                <div class="row g-3 align-items-center">
                    <div class="col-lg-4">
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-primary" id="nuevo-presupuesto-btn">
                                <i class="fas fa-file-circle-plus"></i> Nuevo presupuesto
                            </button>
                            <button type="button" class="btn btn-outline-secondary" id="exportar-presupuestos-btn">
                                <i class="fas fa-file-export"></i> Exportar
                            </button>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input type="text" class="form-control" id="buscar-presupuesto"
                                placeholder="Buscar por número o cliente...">
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <select class="form-select" id="filtro-estado">
                            <option value="">Todos los estados</option>
                            <option value="borrador">Borrador</option>
                            <option value="enviado">Enviado</option>
                            <option value="aceptado">Aceptado</option>
                            <option value="rechazado">Rechazado</option>
                            <option value="cancelado">Cancelado</option>
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
                            <th>Cliente</th>
                            <th style="width: 150px;">Fecha</th>
                            <th style="width: 140px;">Total</th>
                            <th style="width: 140px;">Estado</th>
                            <th style="width: 140px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="presupuestos-tbody"></tbody>
                </table>
            </div>

            <div id="no-presupuestos" class="text-center py-5" style="display:none;">
                <i class="fas fa-file-circle-exclamation fa-3x text-muted mb-3"></i>
                <h4 class="text-muted">Todavía no hay presupuestos</h4>
                <p class="text-muted">Crea tu primer presupuesto con el botón "Nuevo presupuesto".</p>
            </div>
        </div>
    </div>

    <!-- Modal: Crear/Editar presupuesto -->
    <div class="modal fade" id="modal-presupuesto" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-presupuesto-titulo">Nuevo presupuesto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <form id="form-presupuesto">
                        <input type="hidden" id="presupuesto-id">
                        <div class="row g-3 mb-3">
                            <div class="col-md-3">
                                <label class="form-label">Número</label>
                                <input type="text" class="form-control" id="numero_presupuesto" placeholder="Automático"
                                    readonly>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Cliente *</label>
                                <select class="form-select" id="cliente_id" required></select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Estado</label>
                                <select class="form-select" id="estado">
                                    <option value="borrador">Borrador</option>
                                    <option value="enviado">Enviado</option>
                                    <option value="aceptado">Aceptado</option>
                                    <option value="rechazado">Rechazado</option>
                                    <option value="cancelado">Cancelado</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Fecha</label>
                                <input type="date" class="form-control" id="fecha" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Válido hasta</label>
                                <input type="date" class="form-control" id="fecha_valido_hasta">
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-2">
                                <label class="form-label">Ejercicio</label>
                                <input type="number" class="form-control" id="ejercicio">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Moneda</label>
                                <input type="text" class="form-control" id="moneda" value="EUR">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Forma de pago</label>
                                <select class="form-select" id="forma_pago">
                                    <option value="transferencia">Transferencia</option>
                                    <option value="contado">Contado</option>
                                    <option value="tarjeta">Tarjeta</option>
                                    <option value="cheque">Cheque</option>
                                    <option value="paypal">PayPal</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Plazo de entrega</label>
                                <input type="text" class="form-control" id="plazo_entrega">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Garantía</label>
                                <input type="text" class="form-control" id="garantia">
                            </div>
                        </div>

                        <h6 class="mt-4">Líneas del presupuesto</h6>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <small class="text-muted">Añade productos o servicios que componen el presupuesto.</small>
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
                                        <span>IVA</span>
                                        <span class="valor" id="total-iva">0,00 €</span>
                                    </div>
                                    <hr class="border-secondary">
                                    <div class="d-flex justify-content-between">
                                        <span>Total</span>
                                        <span class="valor" id="total-general">0,00 €</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <label class="form-label">Términos y condiciones</label>
                            <textarea class="form-control" id="terminos_condiciones" rows="3"></textarea>
                        </div>
                        <div class="mt-3">
                            <label class="form-label">Notas internas</label>
                            <textarea class="form-control" id="notas_internas" rows="3"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="guardar-presupuesto-btn">
                        <i class="fas fa-floppy-disk"></i> Guardar presupuesto
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: Detalles -->
    <div class="modal fade" id="modal-detalles" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detalle del presupuesto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body" id="detalles-presupuesto-content"></div>
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
                    <h5 class="modal-title">Eliminar presupuesto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <p>¿Deseas eliminar el presupuesto <strong id="presupuesto-eliminar-numero"></strong>?</p>
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
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/presupuestos.js"></script>