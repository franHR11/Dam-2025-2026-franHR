document.addEventListener('DOMContentLoaded', function() {
    const logoutButton = document.getElementById('logout-btn'); // Asume que tu botón de logout tiene este ID

    if (logoutButton) {
        logoutButton.addEventListener('click', function(e) {
            e.preventDefault();
            
            const apiUrl = window.API_BASE_URL + 'logout.php'; // URL del endpoint de logout

            fetch(apiUrl, {
                method: 'POST',
                credentials: 'include', // Importante para enviar cookies de sesión
                headers: {
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Redirigir al login si el logout fue exitoso
                    window.location.href = window.LOGIN_URL;
                } else {
                    // Manejar el caso en que el logout falla
                    console.error('Error al cerrar sesión:', data.message);
                    alert('No se pudo cerrar la sesión. Por favor, inténtalo de nuevo.');
                }
            })
            .catch(error => {
                console.error('Error en la petición de logout:', error);
                alert('Ocurrió un error de red. No se pudo cerrar la sesión.');
            });
        });
    }
});