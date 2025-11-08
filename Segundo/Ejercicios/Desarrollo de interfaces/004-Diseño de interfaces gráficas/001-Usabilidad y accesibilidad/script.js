// Función para buscar en los artículos
document.getElementById('search-btn').addEventListener('click', function() {
    const query = document.getElementById('search').value.toLowerCase();
    const articles = document.querySelectorAll('#results article');
    articles.forEach(article => {
        if (article.textContent.toLowerCase().includes(query)) {
            article.style.display = 'block';
        } else {
            article.style.display = 'none';
        }
    });
});

// Validación simple del formulario
document.getElementById('contact-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const name = document.getElementById('name').value;
    const email = document.getElementById('email').value;
    if (name && email) {
        alert('Formulario enviado. Gracias por contactar, ' + name + '.');
    } else {
        alert('Por favor, completa todos los campos.');
    }
});
