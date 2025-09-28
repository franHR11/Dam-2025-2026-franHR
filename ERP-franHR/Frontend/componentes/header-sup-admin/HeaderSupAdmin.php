<!-- stilo específico  -->

<style>
    <?php include "HeaderSupAdmin.css"; ?>
</style>

<header id="superior" class="odoo-header">
    <div class="header-left">
        <button id="apps-menu-btn" class="apps-menu-btn" title="Menú de aplicaciones">
            <i class="fas fa-th"></i>
        </button>

    </div>

    <div class="titulo-aplicaciones">
        <h3>Aplicaciones</h3>
        <h5>Aplicaciones</h5>
    </div>

    <div class="header-center">
        <h1 class="header-title">Pcpro ERP - franHR</h1>

    </div>

    <div class="header-right">
        <!-- Notificaciones -->
        <div class="notifications-menu">
            <button id="notifications-btn" class="header-btn" title="Notificaciones">
                <i class="fas fa-bell"></i>
                <span class="notification-badge">3</span>
            </button>

            <div id="notifications-dropdown" class="notifications-dropdown">
                <div class="notifications-header">
                    <h4>Notificaciones</h4>
                    <button id="mark-all-read" class="mark-all-read">Marcar todas como leídas</button>
                </div>
                <div class="notifications-list">
                    <div class="notification-item unread" data-id="1">
                        <div class="notification-icon">
                            <i class="fas fa-user-plus"></i>
                        </div>
                        <div class="notification-content">
                            <div class="notification-title">Nuevo usuario registrado</div>
                            <div class="notification-message">Francisco Josè se ha registrado en el sistema</div>
                            <div class="notification-time">Hace 5 min</div>
                        </div>
                        <button class="notification-close" data-id="1">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="notification-item unread" data-id="2">
                        <div class="notification-icon">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="notification-content">
                            <div class="notification-title">Error en el sistema</div>
                            <div class="notification-message">Se ha producido un error en el módulo de ventas</div>
                            <div class="notification-time">Hace 15 min</div>
                        </div>
                        <button class="notification-close" data-id="2">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="notification-item" data-id="3">
                        <div class="notification-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="notification-content">
                            <div class="notification-title">Copia de seguridad completada</div>
                            <div class="notification-message">La copia de seguridad diaria se completó exitosamente</div>
                            <div class="notification-time">Hace 1 hora</div>
                        </div>
                        <button class="notification-close" data-id="3">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="notifications-footer">
                    <a href="#" class="view-all-notifications">Ver todas las notificaciones</a>
                </div>
            </div>
        </div>

        <!-- Chat -->
        <button id="chat-btn" class="header-btn" title="Chat">
            <i class="fas fa-comments"></i>
        </button>

        <!-- Usuario -->
        <div class="user-menu">
            <button id="user-menu-btn" class="user-btn" title="Usuario">
                <i class="fas fa-user"></i>
                <span class="user-name"><?php echo htmlspecialchars($currentUser['username'] ?? 'Usuario'); ?></span>
                <i class="fas fa-chevron-down"></i>
            </button>

            <div id="user-dropdown" class="user-dropdown">
                <a href="#" class="dropdown-item">
                    <i class="fas fa-user"></i>
                    Mi Perfil
                </a>
                <a href="#" class="dropdown-item">
                    <i class="fas fa-cog"></i>
                    Configuración
                </a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item" id="logout-btn">
                    <i class="fas fa-sign-out-alt"></i>
                    Cerrar Sesión
                </a>
            </div>
        </div>
    </div>
</header>

<!-- JavaScript específico -->
<script src="../../config.php"></script>
<script>
    <?php include "HeaderSupAdmin.js"; ?>
</script>