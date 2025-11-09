<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
?>
<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <h1 class="auth-title">
                <i class="fas fa-leaf"></i>
                Bienvenido a EcoTrack
            </h1>
            <p class="auth-subtitle">Inicia sesión para calcular tu huella ecológica</p>
        </div>

        <form class="auth-form" action="index.php?action=login" method="POST">
            <div class="form-group">
                <label for="email" class="form-label">
                    <i class="fas fa-envelope"></i>
                    Correo Electrónico
                </label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    class="form-input"
                    placeholder="tu@email.com"
                    required
                    autocomplete="email"
                >
            </div>

            <div class="form-group">
                <label for="password" class="form-label">
                    <i class="fas fa-lock"></i>
                    Contraseña
                </label>
                <div class="password-input">
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="form-input"
                        placeholder="••••••••"
                        required
                        autocomplete="current-password"
                    >
                    <button type="button" class="password-toggle" onclick="togglePassword('password')">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>

            <div class="form-options">
                <label class="checkbox-label">
                    <input type="checkbox" name="remember" class="checkbox-input">
                    <span class="checkbox-text">Recordarme</span>
                </label>
                <a href="index.php?page=forgot-password" class="forgot-link">
                    ¿Olvidaste tu contraseña?
                </a>
            </div>

            <button type="submit" class="btn btn--primary btn--full btn--large">
                <i class="fas fa-sign-in-alt"></i>
                Iniciar Sesión
            </button>
        </form>

        <div class="auth-divider">
            <span>o</span>
        </div>

        <div class="social-login">
            <button class="btn btn--social btn--google">
                <i class="fab fa-google"></i>
                Continuar con Google
            </button>
            <button class="btn btn--social btn--facebook">
                <i class="fab fa-facebook-f"></i>
                Continuar con Facebook
            </button>
        </div>

        <div class="auth-footer">
            <p>¿No tienes una cuenta? <a href="index.php?page=register" class="auth-link">Regístrate gratis</a></p>
        </div>
    </div>

    <div class="auth-info">
        <div class="auth-info-content">
            <h2>¿Por qué registrarte?</h2>
            <ul class="auth-features">
                <li><i class="fas fa-check"></i> Seguimiento personalizado de tu huella ecológica</li>
                <li><i class="fas fa-chart-line"></i> Gráficos y estadísticas detalladas</li>
                <li><i class="fas fa-trophy"></i> Sistema de logros y recompensas</li>
                <li><i class="fas fa-users"></i> Acceso a la comunidad verde</li>
                <li><i class="fas fa-mobile-alt"></i> Disponible en todos tus dispositivos</li>
            </ul>
        </div>
    </div>
</div>

<script>
function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const button = input.nextElementSibling;
    const icon = button.querySelector('i');

    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

// Validación del formulario
document.querySelector('.auth-form').addEventListener('submit', function(e) {
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;

    if (!email || !password) {
        e.preventDefault();
        alert('Por favor, completa todos los campos');
        return;
    }

    if (!email.includes('@')) {
        e.preventDefault();
        alert('Por favor, introduce un email válido');
        return;
    }
});
</script>
<?php } ?>
