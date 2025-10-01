<?php
// Verificación de sesión
require_once '../componentes/Auth/SessionManager.php';
SessionManager::checkSession(); // Protege la página
$currentUser = SessionManager::getUserInfo();
?>
<?php include '../componentes/Head/Head.php'; ?>

<!-- stilo específico  -->

<style>
    <?php include "escritorio.css"; ?>
</style>
<main>
    <?php include '../componentes/header-sup-admin/HeaderSupAdmin.php'; ?>
    <?php include '../componentes/header-inf-admin/HeaderInfAdmin.php'; ?>

    <div id="escritorio">
        <?php include '../componentes/Menu-Admin/MenuAdmin.php'; ?>

        <!-- Área de contenido dinámico -->
        <div id="content-area">
            <?php include '../componentes/listadoModulos/listadoModulos.php'; ?>
        </div>
    </div>

</main>

<!-- JavaScript específico  -->
<script>
    <?php include "javascript.js"; ?>
</script>



<?php include '../componentes/Footer/Footer.php'; ?>