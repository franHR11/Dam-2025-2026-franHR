document.querySelector('button').addEventListener('click', async function () {
    const username = document.getElementById('username').value.trim();
    const password = document.getElementById('password').value.trim();
    const errorMessage = document.getElementById('error-message');

    if (!username || !password) {
        errorMessage.textContent = 'Por favor, complete todos los campos.';
        return;
    }

    try {
        // Paso 1: Autenticar contra el backend
        const loginResponse = await fetch((window.CONFIG?.API_BASE_URL || '/api/') + 'login/login.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ username, password })
        });

        const loginData = await loginResponse.json();

        if (loginData.success) {
            // Paso 2: Crear la sesión en el frontend
            const sessionResponse = await fetch('../componentes/Auth/create_session.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(loginData)
            });

            const sessionData = await sessionResponse.json();

            if (sessionData.success) {
                window.location.href = '../escritorio/escritorio.php';
            } else {
                errorMessage.textContent = sessionData.message || 'Error al crear la sesión.';
            }
        } else {
            errorMessage.textContent = loginData.message || 'Error en el inicio de sesión.';
        }
    } catch (error) {
        errorMessage.textContent = 'Error de conexión con el servidor.';
        console.error('Error en la solicitud de login:', error);
    }
});

// Permitir envío con Enter
document.addEventListener('keypress', function (event) {
    if (event.key === 'Enter') {
        document.querySelector('button').click();
    }
});
