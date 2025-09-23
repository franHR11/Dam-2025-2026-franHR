<!-- stilo específico  -->

<style>
    <?php include "listadoModulos.css"; ?>
</style>


<main id="listadoModulos">


    <div id="listadoCard">
        <?php for ($i = 0; $i < 20; $i++) { ?>
            <article>
                <div class="logo">
                    <i class="logo fas fa-home"></i>
                </div>
                <div class="texto">
                    <h2>Modulo 1</h2>
                    <p>Descripcion modulo 1</p>
                    <button>Instalar</button>
                </div>
            </article>
        <?php } ?>
    </div>



</main>

<!-- JavaScript específico  -->
<script>
    <?php include "listadoModulos.js"; ?>
</script>