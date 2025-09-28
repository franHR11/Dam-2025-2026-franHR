<!-- stilo específico  -->

<style>
    <?php include "HeaderInfAdmin.css"; ?>
</style>

<main id="Inferior">

    <div class="inf-toolbar">
        <div class="inf-toolbar-center">
            <div class="search-box">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" placeholder="Buscar..." aria-label="Buscar" />
            </div>
        </div>
        <div class="inf-toolbar-right">
            <div class="paginator" aria-label="Paginador">
                <button class="btn-page prev" title="Anterior" aria-label="Página anterior" disabled aria-disabled="true">
                    <i class="fa-solid fa-angle-left"></i>
                </button>
                <span class="current" title="Página actual">1</span>
                <span class="sep">/</span>
                <span class="total" title="Total de páginas">10</span>
                <button class="btn-page next" title="Siguiente" aria-label="Página siguiente">
                    <i class="fa-solid fa-angle-right"></i>
                </button>
            </div>
            <div class="view-toggle" role="group" aria-label="Cambiar vista">
                <button class="btn-view active" data-view="grid" title="Vista cuadrícula">
                    <i class="fa-solid fa-table-cells"></i>
                </button>
                <button class="btn-view" data-view="list" title="Vista lista">
                    <i class="fa-solid fa-list"></i>
                </button>
            </div>
        </div>
    </div>
</main>

<!-- JavaScript específico -->
<script>
    <?php include "HeaderInfAdmin.js"; ?>
</script>