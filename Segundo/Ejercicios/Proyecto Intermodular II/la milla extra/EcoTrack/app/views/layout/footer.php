        </div>
    </main>

    <footer class="footer">
        <div class="container">
            <div class="footer__content">
                <div class="footer__section">
                    <h3 class="footer__title">
                        <i class="fas fa-leaf"></i>
                        EcoTrack
                    </h3>
                    <p class="footer__description">
                        Calcula y reduce tu huella ecológica para un futuro más sostenible.
                    </p>
                    <div class="footer__social">
                        <a href="#" class="footer__social-link" aria-label="Facebook">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="footer__social-link" aria-label="Twitter">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="footer__social-link" aria-label="Instagram">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="footer__social-link" aria-label="LinkedIn">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                    </div>
                </div>

                <div class="footer__section">
                    <h4 class="footer__subtitle">Enlaces Rápidos</h4>
                    <ul class="footer__links">
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <li><a href="index.php?page=dashboard">Dashboard</a></li>
                            <li><a href="index.php?page=habit_form">Nuevo Hábito</a></li>
                            <li><a href="index.php?page=history">Historial</a></li>
                            <li><a href="index.php?page=achievements">Logros</a></li>
                        <?php else: ?>
                            <li><a href="index.php?page=login">Iniciar Sesión</a></li>
                            <li><a href="index.php?page=register">Registrarse</a></li>
                        <?php endif; ?>
                    </ul>
                </div>

                <div class="footer__section">
                    <h4 class="footer__subtitle">Recursos</h4>
                    <ul class="footer__links">
                        <li><a href="index.php?page=about">Acerca de</a></li>
                        <li><a href="index.php?page=help">Ayuda</a></li>
                        <li><a href="index.php?page=privacy">Privacidad</a></li>
                        <li><a href="index.php?page=terms">Términos</a></li>
                    </ul>
                </div>

                <div class="footer__section">
                    <h4 class="footer__subtitle">Newsletter</h4>
                    <p class="footer__description">
                        Suscríbete para recibir consejos ecológicos semanales.
                    </p>
                    <form class="footer__newsletter" action="index.php?page=newsletter" method="POST">
                        <input type="email" name="email" placeholder="Tu correo electrónico" required>
                        <button type="submit" class="btn btn--primary btn--small">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </form>
                </div>
            </div>

            <div class="footer__bottom">
                <div class="footer__copyright">
                    <p>&copy; <?php echo date('Y'); ?> EcoTrack. Todos los derechos reservados.</p>
                </div>
                <div class="footer__made-by">
                    <p>Hecho con <i class="fas fa-heart" style="color: #e74c3c;"></i> por Fran</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
    <script src="./public/js/main.js"></script>
    <script src="./public/js/api.js"></script>
    <script src="./public/js/chartHandler.js"></script>
    <script src="./public/js/helpers/validator.js"></script>

    <script>
        // Inicialización
        document.addEventListener('DOMContentLoaded', function() {
            // Menú móvil
            const navbarToggle = document.getElementById('navbarToggle');
            const navbarMenu = document.querySelector('.navbar__menu');

            if (navbarToggle) {
                navbarToggle.addEventListener('click', function() {
                    navbarMenu.classList.toggle('navbar__menu--active');
                    navbarToggle.classList.toggle('navbar__toggle--active');
                });
            }

            // Dropdowns
            const dropdowns = document.querySelectorAll('.navbar__dropdown');
            dropdowns.forEach(dropdown => {
                const link = dropdown.querySelector('.navbar__link');
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    dropdown.classList.toggle('navbar__dropdown--active');
                });
            });

            // Cerrar dropdowns al hacer clic fuera
            document.addEventListener('click', function(e) {
                if (!e.target.closest('.navbar__dropdown')) {
                    dropdowns.forEach(dropdown => {
                        dropdown.classList.remove('navbar__dropdown--active');
                    });
                }
            });
        });
    </script>
</body>
</html>
