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

        <div id="informes-content" class="main-content">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">Informes y Estadísticas de Facturación</h2>
                <div class="btn-group">
                    <button type="button" class="btn btn-outline-secondary" id="exportar-informe-btn">
                        <i class="fas fa-download"></i> Exportar
                    </button>
                    <button type="button" class="btn btn-outline-secondary" id="imprimir-informe-btn">
                        <i class="fas fa-print"></i> Imprimir
                    </button>
                </div>
            </div>

            <!-- Filtros -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Ejercicio</label>
                            <select class="form-select" id="ejercicio-select">
                                <!-- Se llenará dinámicamente -->
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Tipo</label>
                            <select class="form-select" id="tipo-select">
                                <option value="venta">Ventas</option>
                                <option value="compra">Compras</option>
                                <option value="todos">Todos</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Período</label>
                            <select class="form-select" id="periodo-select">
                                <option value="mes">Último mes</option>
                                <option value="trimestre">Último trimestre</option>
                                <option value="año">Año completo</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Acciones</label>
                            <button type="button" class="btn btn-primary w-100" id="actualizar-btn">
                                <i class="fas fa-sync"></i> Actualizar
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tarjetas de estadísticas -->
            <div class="row mb-4">
                <!-- Tarjeta de Resumen -->
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title">Resumen del Periodo</h5>
                            <div class="row text-center">
                                <div class="col-6">
                                    <h3 class="text-primary" id="total-generadas">0</h3>
                                    <small class="text-muted">Facturas Generadas</small>
                                </div>
                                <div class="col-6">
                                    <h3 class="text-success" id="total-pagadas">0</h3>
                                    <small class="text-muted">Facturas Pagadas</small>
                                </div>
                            </div>
                            <hr>
                            <div class="row text-center">
                                <div class="col-6">
                                    <h4 class="text-warning" id="total-pendientes">0</h4>
                                    <small class="text-muted">Pendientes</small>
                                </div>
                                <div class="col-6">
                                    <h4 class="text-danger" id="total-vencidas">0</h4>
                                    <small class="text-muted">Vencidas</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tarjeta de Importes -->
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title">Importes</h5>
                            <div class="mb-3">
                                <label class="form-label">Total Facturado</label>
                                <h2 class="text-primary" id="importe-total">0,00 €</h2>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Total Pendiente</label>
                                <h4 class="text-warning" id="importe-pendiente">0,00 €</h4>
                            </div>
                            <div>
                                <label class="form-label">Promedio por Factura</label>
                                <h4 class="text-info" id="importe-promedio">0,00 €</h4>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tarjeta de Estados -->
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title">Facturas por Estado</h5>
                            <canvas id="estados-chart" width="200" height="200"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Tarjeta de Tipos -->
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title">Facturas por Tipo</h5>
                            <canvas id="tipos-chart" width="200" height="200"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gráficos y Tablas -->
            <div class="row">
                <!-- Evolución Mensual -->
                <div class="col-lg-8 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Evolución Mensual</h5>
                            <canvas id="evolucion-chart" height="100"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Top Clientes -->
                <div class="col-lg-4 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Top Clientes</h5>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Cliente</th>
                                            <th class="text-end">Facturas</th>
                                            <th class="text-end">Importe</th>
                                        </tr>
                                    </thead>
                                    <tbody id="top-clientes-tbody">
                                        <!-- Se llenará dinámicamente -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabla de Top Productos -->
            <div class="row">
                <div class="col-lg-6 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Top Productos Facturados</h5>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Producto</th>
                                            <th class="text-end">Unidades</th>
                                            <th class="text-end">Veces</th>
                                            <th class="text-end">Importe</th>
                                        </tr>
                                    </thead>
                                    <tbody id="top-productos-tbody">
                                        <!-- Se llenará dinámicamente -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ingresos Mensuales -->
                <div class="col-lg-6 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Ingresos Mensuales</h5>
                            <canvas id="ingresos-chart" height="150"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Loading indicator -->
            <div id="loading-indicator" class="text-center py-5" style="display: none;">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
                <p class="mt-2">Cargando estadísticas...</p>
            </div>
        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="js/informes.js"></script>