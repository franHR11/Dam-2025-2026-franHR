<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
?>
<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <h1 class="auth-title">
                <i class="fas fa-leaf"></i>
                Únete a EcoTrack
            </h1>
            <p class="auth-subtitle">Crea tu cuenta y empieza a reducir tu huella ecológica</p>
        </div>

        <form class="auth-form" action="index.php?action=register" method="POST">
            <div class="form-group">
                <label for="name" class="form-label">
                    <i class="fas fa-user"></i>
                    Nombre Completo
                </label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    class="form-input"
                    placeholder="Tu nombre completo"
                    required
                    autocomplete="name"
                    minlength="2"
                    maxlength="100"
                >
            </div>

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
                    maxlength="120"
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
                        placeholder="Mínimo 6 caracteres"
                        required
                        autocomplete="new-password"
                        minlength="6"
                        maxlength="255"
                    >
                    <button type="button" class="password-toggle" onclick="togglePassword('password')">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                <div class="password-strength" id="passwordStrength"></div>
            </div>

            <div class="form-group">
                <label for="confirm_password" class="form-label">
                    <i class="fas fa-lock"></i>
                    Confirmar Contraseña
                </label>
                <div class="password-input">
                    <input
                        type="password"
                        id="confirm_password"
                        name="confirm_password"
                        class="form-input"
                        placeholder="Repite tu contraseña"
                        required
                        autocomplete="new-password"
                    >
                    <button type="button" class="password-toggle" onclick="togglePassword('confirm_password')">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>

            <div class="form-options">
                <label class="checkbox-label">
                    <input type="checkbox" name="terms" class="checkbox-input" required>
                    <span class="checkbox-text">
                        Acepto los <a href="index.php?page=terms" target="_blank">términos y condiciones</a>
                        y la <a href="index.php?page=privacy" target="_blank">política de privacidad</a>
                    </span>
                </label>
            </div>

            <div class="form-options">
                <label class="checkbox-label">
                    <input type="checkbox" name="newsletter" class="checkbox-input">
                    <span class="checkbox-text">
                        Quiero recibir consejos ecológicos por correo electrónico
                    </span>
                </label>
            </div>

            <button type="submit" class="btn btn--primary btn--full btn--large">
                <i class="fas fa-user-plus"></i>
                Crear Cuenta
            </button>
        </form>

        <div class="auth-divider">
            <span>o regístrate con</span>
        </div>

        <div class="social-login">
            <button class="btn btn--social btn--google">
                <i class="fab fa-google"></i>
                Google
            </button>
            <button class="btn btn--social btn--facebook">
                <i class="fab fa-facebook-f"></i>
                Facebook
            </button>
        </div>

        <div class="auth-footer">
            <p>¿Ya tienes una cuenta? <a href="index.php?page=login" class="auth-link">Inicia sesión</a></p>
        </div>
    </div>

    <div class="auth-info">
        <div class="auth-info-content">
            <h2>Beneficios de tu cuenta EcoTrack</h2>
            <ul class="auth-features">
                <li><i class="fas fa-chart-line"></i> Historial completo de tu huella de carbono</li>
                <li><i class="fas fa-trophy"></i> Logros y recompensas personalizadas</li>
                <li><i class="fas fa-bell"></i> Recordatorios y consejos semanales</li>
                <li><i class="fas fa-download"></i> Exporta tus datos en CSV</li>
                <li><i class="fas fa-users"></i> Compara con otros usuarios</li>
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

// Validación de fuerza de contraseña
function checkPasswordStrength(password) {
    let strength = 0;
    const strengthDiv = document.getElementById('passwordStrength');

    if (password.length >= 6) strength++;
    if (password.length >= 10) strength++;
    if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
    if (/[0-9]/.test(password)) strength++;
    if (/[^a-zA-Z0-9]/.test(password)) strength++;

    const strengthTexts = ['', 'Débil', 'Regular', 'Buena', 'Fuerte', 'Muy Fuerte'];
    const strengthColors = ['', '#ff4444', '#ff8844', '#ffdd44', '#88dd44', '#44dd44'];

    strengthDiv.textContent = strengthTexts[strength];
    strengthDiv.style.color = strengthColors[strength];

    return strength;
}

document.getElementById('password').addEventListener('input', function() {
    checkPasswordStrength(this.value);
});

// Validación del formulario
document.querySelector('.auth-form').addEventListener('submit', function(e) {
    const name = document.getElementById('name').value.trim();
    const email = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    const terms = document.querySelector('input[name="terms"]').checked;

    if (!name || name.length < 2) {
        e.preventDefault();
        alert('Por favor, introduce tu nombre completo (mínimo 2 caracteres)');
        return;
    }

    if (!email || !email.includes('@') || !email.includes('.')) {
        e.preventDefault();
        alert('Por favor, introduce un correo electrónico válido');
        return;
    }

    if (!password || password.length < 6) {
        e.preventDefault();
        alert('La contraseña debe tener al menos 6 caracteres');
        return;
    }

    if (password !== confirmPassword) {
        e.preventDefault();
        alert('Las contraseñas no coinciden');
        return;
    }

    if (!terms) {
        e.preventDefault();
        alert('Debes aceptar los términos y condiciones para registrarte');
        return;
    }

    // Mostrar indicador de carga
    const submitButton = this.querySelector('button[type="submit"]');
    const originalText = submitButton.innerHTML;
    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creando cuenta...';
    submitButton.disabled = true;

    // Restaurar botón después de 5 segundos en caso de error
    setTimeout(() => {
        submitButton.innerHTML = originalText;
        submitButton.disabled = false;
    }, 5000);
});

// Autocompletar confirmación de contraseña
document.getElementById('password').addEventListener('input', function() {
    const confirmField = document.getElementById('confirm_password');
    if (confirmField.value && confirmField.value !== this.value) {
        confirmField.setCustomValidity('Las contraseñas no coinciden');
    } else {
        confirmField.setCustomValidity('');
    }
});

document.getElementById('confirm_password').addEventListener('input', function() {
    const password = document.getElementById('password').value;
    if (this.value !== password) {
        this.setCustomValidity('Las contraseñas no coinciden');
    } else {
        this.setCustomValidity('');
    }
});
</script>
<?php } ?>
