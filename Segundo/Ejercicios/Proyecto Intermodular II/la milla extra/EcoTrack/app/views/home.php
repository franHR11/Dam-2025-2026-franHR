<?php $page_title = 'Inicio'; ?>
<?php include __DIR__ . '/layout/header.php'; ?>

<section class="hero">
    <div class="hero__content">
        <h1 class="hero__title">
            <i class="fas fa-leaf"></i>
            Calcula tu Huella Ecológica
        </h1>
        <p class="hero__description">
            Descubre el impacto ambiental de tus hábitos diarios y aprende a reducirlo con EcoTrack,
            tu calculadora personal de sostenibilidad.
        </p>
        <div class="hero__actions">
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="index.php?page=habit_form" class="btn btn--primary btn--large">
                    <i class="fas fa-plus-circle"></i>
                    Nuevo Cálculo
                </a>
                <a href="index.php?page=dashboard" class="btn btn--secondary btn--large">
                    <i class="fas fa-tachometer-alt"></i>
                    Mi Dashboard
                </a>
            <?php else: ?>
                <a href="index.php?page=register" class="btn btn--primary btn--large">
                    <i class="fas fa-user-plus"></i>
                    Comenzar Ahora
                </a>
                <a href="index.php?page=login" class="btn btn--outline btn--large">
                    <i class="fas fa-sign-in-alt"></i>
                    Iniciar Sesión
                </a>
            <?php endif; ?>
        </div>
    </div>
    <div class="hero__image">
        <img src="/EcoTrack/public/img/hero-image.png" alt="Sostenibilidad ambiental" loading="lazy">
    </div>
</section>

<section class="features">
    <div class="container">
        <h2 class="section__title">¿Por qué EcoTrack?</h2>
        <div class="features__grid">
            <div class="feature__card">
                <div class="feature__icon">
                    <i class="fas fa-calculator"></i>
                </div>
                <h3 class="feature__title">Cálculo Preciso</h3>
                <p class="feature__description">
                    Algoritmos científicos para calcular tu huella de carbono basados en transporte,
                    energía, dieta y reciclaje.
                </p>
            </div>

            <div class="feature__card">
                <div class="feature__icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <h3 class="feature__title">Seguimiento Visual</h3>
                <p class="feature__description">
                    Gráficos interactivos y estadísticas detalladas para monitorear tu progreso
                    y visualizar tu impacto ambiental.
                </p>
            </div>

            <div class="feature__card">
                <div class="feature__icon">
                    <i class="fas fa-lightbulb"></i>
                </div>
                <h3 class="feature__title">Consejos Personalizados</h3>
                <p class="feature__description">
                    Recomendaciones adaptadas a tus hábitos específicos para ayudarte a reducir
                    tu huella de carbono efectivamente.
                </p>
            </div>

            <div class="feature__card">
                <div class="feature__icon">
                    <i class="fas fa-trophy"></i>
                </div>
                <h3 class="feature__title">Gamificación</h3>
                <p class="feature__description">
                    Sistema de logros y recompensas que motiva tus mejores prácticas ecológicas
                    y celebra tus progresos.
                </p>
            </div>

            <div class="feature__card">
                <div class="feature__icon">
                    <i class="fas fa-mobile-alt"></i>
                </div>
                <h3 class="feature__title">100% Digital</h3>
                <p class="feature__description">
                    Reduce el papel accediendo a toda tu información desde cualquier dispositivo,
                    en cualquier momento y lugar.
                </p>
            </div>

            <div class="feature__card">
                <div class="feature__icon">
                    <i class="fas fa-users"></i>
                </div>
                <h3 class="feature__title">Comunidad Verde</h3>
                <p class="feature__description">
                    Comparte tus logros, compara tus resultados y aprende de una comunidad
                    comprometida con la sostenibilidad.
                </p>
            </div>
        </div>
    </div>
</section>

<section class="stats">
    <div class="container">
        <div class="stats__grid">
            <div class="stat__item">
                <div class="stat__number">
                    <span class="stat__counter" data-target="2500">0</span>+
                </div>
                <div class="stat__label">Usuarios Activos</div>
            </div>

            <div class="stat__item">
                <div class="stat__number">
                    <span class="stat__counter" data-target="15000">0</span>+
                </div>
                <div class="stat__label">Cálculos Realizados</div>
            </div>

            <div class="stat__item">
                <div class="stat__number">
                    <span class="stat__counter" data-target="25">0</span>%
                </div>
                <div class="stat__label">Reducción Promedio</div>
            </div>

            <div class="stat__item">
                <div class="stat__number">
                    <span class="stat__counter" data-target="4.8">0</span>⭐
                </div>
                <div class="stat__label">Valoración Media</div>
            </div>
        </div>
    </div>
</section>

<section class="testimonials">
    <div class="container">
        <h2 class="section__title">Lo que dicen nuestros usuarios</h2>
        <div class="testimonials__grid">
            <div class="testimonial__card">
                <div class="testimonial__content">
                    <p>
                        "EcoTrack me ha abierto los ojos sobre mi impacto ambiental. En solo 2 meses
                        he reducido mi huella de carbono en un 35% gracias a los consejos personalizados."
                    </p>
                </div>
                <div class="testimonial__author">
                    <div class="testimonial__avatar">
                        <i class="fas fa-user-circle"></i>
                    </div>
                    <div class="testimonial__info">
                        <div class="testimonial__name">María González</div>
                        <div class="testimonial__role">Profesora</div>
                    </div>
                </div>
            </div>

            <div class="testimonial__card">
                <div class="testimonial__content">
                    <p>
                        "La gamificación hace que cuidar el planeta sea divertido. Me encanta desbloquear
                        nuevos logros y ver mi progreso en los gráficos. ¡Recomendado 100%!"
                    </p>
                </div>
                <div class="testimonial__author">
                    <div class="testimonial__avatar">
                        <i class="fas fa-user-circle"></i>
                    </div>
                    <div class="testimonial__info">
                        <div class="testimonial__name">Carlos Rodríguez</div>
                        <div class="testimonial__role">Ingeniero</div>
                    </div>
                </div>
            </div>

            <div class="testimonial__card">
                <div class="testimonial__content">
                    <p>
                        "Simple, intuitivo y efectivo. Me ayuda a mantenerme consciente de mis hábitos
                        diarios y a tomar mejores decisiones para el medio ambiente."
                    </p>
                </div>
                <div class="testimonial__author">
                    <div class="testimonial__avatar">
                        <i class="fas fa-user-circle"></i>
                    </div>
                    <div class="testimonial__info">
                        <div class="testimonial__name">Ana Martínez</div>
                        <div class="testimonial__role">Estudiante</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="cta">
    <div class="container">
        <div class="cta__content">
            <h2 class="cta__title">¿Listo para cambiar el mundo?</h2>
            <p class="cta__description">
                Únete a miles de personas que ya están reduciendo su impacto ambiental
                y construyendo un futuro más sostenible.
            </p>
            <div class="cta__actions">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="index.php?page=habit_form" class="btn btn--primary btn--large">
                        <i class="fas fa-play"></i>
                        Empezar Cálculo
                    </a>
                <?php else: ?>
                    <a href="index.php?page=register" class="btn btn--primary btn--large">
                        <i class="fas fa-rocket"></i>
                        Crear Cuenta Gratis
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<script>
// Animación de contadores
document.addEventListener('DOMContentLoaded', function() {
    const counters = document.querySelectorAll('.stat__counter');
    const speed = 200;

    counters.forEach(counter => {
        const animate = () => {
            const value = +counter.getAttribute('data-target');
            const data = +counter.innerText;
            const time = value / speed;

            if (data < value) {
                counter.innerText = Math.ceil(data + time);
                setTimeout(animate, 1);
            } else {
                counter.innerText = value;
            }
        }

        // Iniciar animación cuando el elemento sea visible
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    animate();
                    observer.unobserve(entry.target);
                }
            });
        });

        observer.observe(counter);
    });
});
</script>

<?php include __DIR__ . '/layout/footer.php'; ?>
