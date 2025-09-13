document.querySelector('button').onclick = async function () {
    // Obtener el elemento para mostrar errores
    const errorElement = document.getElementById('error-message');

    // Limpiar mensaje de error anterior
    errorElement.innerText = '';

    // Obtener los valores de los campos
    let username = document.getElementById('username').value;
    let password = document.getElementById('password').value;

    // Validar que los campos no estén vacíos
    if (!username.trim() || !password.trim()) {
        errorElement.innerText = 'Por favor, complete todos los campos';
        return;
    }

    // Crear objeto con los datos
    let objeto = {
        username: username,
        password: password
    };

    // Deshabilitar el botón durante la petición
    const button = document.querySelector('button');
    button.disabled = true;
    button.textContent = 'Iniciando sesión...';

    try {
        // Enviar petición al backend
        const response = await fetch(window.API_BASE_URL + '/login/login.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(objeto)
        });

        const data = await response.json();

        if (data.success) {
            // Login exitoso
            
            console.log('Usuario logueado:', data.user);

            // Redirigir a la página de escritorio
            window.location.href = '../escritorio/index.html';

        } else {
            // Error en el login
            errorElement.innerText = data.message;
        }

    } catch (error) {
        console.error('Error en la petición:', error);
        errorElement.innerText = 'Error de conexión. Por favor, intente nuevamente.';
    } finally {
        // Rehabilitar el botón
        button.disabled = false;
        button.textContent = 'Iniciar sesión';
    }
};

// Permitir envío con Enter
document.addEventListener('keypress', function(event) {
    if (event.key === 'Enter') {
        document.querySelector('button').click();
    }
});
