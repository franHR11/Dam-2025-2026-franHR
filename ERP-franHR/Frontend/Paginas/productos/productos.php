<?php
// Verificación de sesión usando el componente existente
require_once '../../componentes/Auth/SessionManager.php';
SessionManager::checkSession();
?>
<?php include '../../componentes/Head/Head.php'; ?>

<style>
    <?php include "../../escritorio/escritorio.css"; ?>
    <?php include "css/productos.css"; ?>
</style>

<main>
    <?php include '../../componentes/header-sup-admin/HeaderSupAdmin.php'; ?>
    <?php include '../../componentes/header-inf-admin/HeaderInfAdmin.php'; ?>

    <div id="escritorio">
        <?php include '../../componentes/Menu-Admin/MenuAdmin.php'; ?>

        <div id="productos-content" class="main-content">
            <!-- Toolbar -->
            <div class="toolbar">
                <div class="row g-3 align-items-center">
                    <div class="col-md-4">
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-primary" id="nuevo-producto-btn">
                                <i class="fas fa-plus"></i> Nuevo producto
                            </button>
                            <button type="button" class="btn btn-info" id="gestionar-categorias-btn" onclick="window.location.href='../categorias/categorias.php'">
                                <i class="fas fa-tags"></i> Categorías
                            </button>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input type="text" class="form-control" id="buscar-producto" placeholder="Buscar productos..." />
                        </div>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select" id="filtro-categoria">
                            <option value="">Todas las categorías</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select" id="filtro-stock">
                            <option value="">Todo stock</option>
                            <option value="bajo">Stock bajo</option>
                            <option value="agotado">Agotado</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Área de contenido -->
            <div class="content-area p-3">
                <!-- Tabla de productos -->
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th style="width: 40px;">
                                    <input type="checkbox" id="seleccionar-todos" />
                                </th>
                                <th style="width: 120px;">Código</th>
                                <th style="width: 90px;">Imagen</th>
                                <th>Nombre</th>
                                <th style="width: 180px;">Categoría / Tipo</th>
                                <th style="width: 150px;">Precio</th>
                                <th style="width: 160px;">Stock</th>
                                <th style="width: 180px;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="productos-tbody"></tbody>
                    </table>
                </div>

                <!-- Mensaje sin productos -->
                <div id="no-productos" class="text-center py-5" style="display: none;">
                    <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">No hay productos disponibles</h4>
                    <p class="text-muted">Crea un nuevo producto con el botón "Nuevo producto".</p>
                </div>

                <!-- Paginación -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="small text-muted">
                        Mostrando <span id="productos-desde">0</span>–<span id="productos-hasta">0</span> de <span id="productos-total">0</span> productos
                    </div>
                    <nav>
                        <ul id="pagination" class="pagination pagination-sm mb-0"></ul>
                    </nav>
                </div>
            </div>
        </div>

        <!-- Modal: Crear/Editar Producto -->
        <div class="modal fade" id="modal-producto" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modal-producto-titulo">Producto</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <form id="form-producto">
                            <div class="row g-3">
                                <!-- Columna izquierda: Información general -->
                                <div class="col-md-8">
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label class="form-label">Código *</label>
                                            <input type="text" class="form-control" name="codigo" id="codigo" required />
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Código de barras</label>
                                            <input type="text" class="form-control" name="codigo_barras" id="codigo-barras" />
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Categoría</label>
                                            <select class="form-select" id="categoria" name="categoria_id"></select>
                                        </div>

                                        <div class="col-md-8">
                                            <label class="form-label">Nombre *</label>
                                            <input type="text" class="form-control" name="nombre" id="nombre" required />
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Proveedor</label>
                                            <select class="form-select" id="proveedor" name="proveedor_principal_id"></select>
                                        </div>

                                        <div class="col-md-12">
                                            <label class="form-label">Descripción</label>
                                            <textarea class="form-control" name="descripcion" id="descripcion" rows="3"></textarea>
                                        </div>

                                        <div class="col-md-4">
                                            <label class="form-label">Tipo de producto</label>
                                            <select class="form-select" name="tipo_producto" id="tipo-producto">
                                                <option value="producto">Producto simple</option>
                                                <option value="servicio">Servicio</option>
                                                <option value="kit">Producto compuesto</option>
                                                <option value="kit">Pack</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Unidad de medida</label>
                                            <input type="text" class="form-control" name="unidad_medida" id="unidad-medida" placeholder="ud, kg, l, etc." />
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">IVA (%)</label>
                                            <input type="number" step="0.01" class="form-control" name="iva_tipo" id="iva-tipo" value="21" />
                                        </div>

                                        <div class="col-md-4">
                                            <label class="form-label">Precio coste</label>
                                            <input type="number" step="0.01" class="form-control" name="precio_coste" id="precio-coste" />
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Margen (%)</label>
                                            <input type="number" step="0.01" class="form-control" name="margen" id="margen" />
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Precio venta *</label>
                                            <input type="number" step="0.01" class="form-control" name="precio_venta" id="precio-venta" required />
                                        </div>

                                        <div class="col-md-4">
                                            <label class="form-label">Stock actual *</label>
                                            <input type="number" step="0.01" class="form-control" name="stock_actual" id="stock-actual" required />
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Stock mínimo</label>
                                            <input type="number" step="0.01" class="form-control" name="stock_minimo" id="stock-minimo" />
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Stock máximo</label>
                                            <input type="number" step="0.01" class="form-control" name="stock_maximo" id="stock-maximo" />
                                        </div>

                                        <div class="col-md-6">
                                            <div id="alerta-stock-bajo" class="alert alert-warning" style="display: none;">
                                                Stock bajo respecto al mínimo configurado.
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div id="alerta-stock-agotado" class="alert alert-danger" style="display: none;">
                                                Stock agotado.
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label">Imagen</label>
                                            <input type="file" class="form-control" id="imagen" name="imagen" accept="image/*" />
                                            <div id="vista-previa-imagen" class="mt-2"></div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Tags</label>
                                            <input type="text" class="form-control" name="tags" id="tags" placeholder="etiquetas separadas por comas" />
                                        </div>

                                        <div class="col-md-12">
                                            <label class="form-label">Observaciones</label>
                                            <textarea class="form-control" name="observaciones" id="observaciones" rows="2"></textarea>
                                        </div>
                                    </div>
                                </div>

                                <!-- Columna derecha: Configuración -->
                                <div class="col-md-4">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="form-check form-switch mb-2">
                                                <input class="form-check-input" type="checkbox" id="activo" name="activo" checked />
                                                <label class="form-check-label" for="activo">Activo</label>
                                            </div>
                                            <div class="form-check form-switch mb-2">
                                                <input class="form-check-input" type="checkbox" id="venta-online" name="es_venta_online" checked />
                                                <label class="form-check-label" for="venta-online">Disponible para venta online</label>
                                            </div>
                                            <div class="form-check form-switch mb-2">
                                                <input class="form-check-input" type="checkbox" id="control-stock" name="control_stock" checked />
                                                <label class="form-check-label" for="control-stock">Controlar stock</label>
                                            </div>
                                            <div class="form-check form-switch mb-2">
                                                <input class="form-check-input" type="checkbox" id="requiere-receta" name="requiere_receta" />
                                                <label class="form-check-label" for="requiere-receta">Requiere receta</label>
                                            </div>
                                            <div class="form-check form-switch mb-2">
                                                <input class="form-check-input" type="checkbox" id="fecha-caducidad" name="fecha_caducidad" />
                                                <label class="form-check-label" for="fecha-caducidad">Control de fecha de caducidad</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary" id="guardar-producto">
                            <i class="fas fa-save"></i> Guardar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal: Confirmar eliminación -->
        <div class="modal fade" id="modal-eliminar" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Eliminar producto</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        ¿Está seguro de que desea eliminar este producto?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-danger" id="confirmar-eliminar">Eliminar</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal: Detalles del producto -->
        <div class="modal fade" id="modal-detalles" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Detalles del producto</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body" id="detalles-contenido">
                        <!-- Contenido dinámico cargado por JS -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button type="button" class="btn btn-primary" id="editar-desde-detalles">
                            <i class="fas fa-edit"></i> Editar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include '../../componentes/Footer/Footer.php'; ?>
</main>

<!-- Scripts -->
<script src="../../comun/config.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- Script de Productos -->
<script src="js/productos.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    if (typeof ProductosPage !== 'undefined') {
      window.productosPage = new ProductosPage();
    }
  });
  </script>