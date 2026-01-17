<?php
// Configuración de la gráfica (simulando que viene de PHP)
// Aunque sea un archivo PHP, generamos JavaScript para la vista.
?>
<script>
// Configuración de las opciones de la gráfica
const pieOptions = {
    animation: {
        duration: 0 // Desactivar animación para actualizaciones suaves
    },
    responsive: true,
    scales: {
        y: {
            beginAtZero: true,
            max: 100
        }
    }
};

// Inicialización de la gráfica usando Chart.js
const ctx = document.getElementById('serverChart').getContext('2d');
const serverChart = new Chart(ctx, {
    type: 'line', // Usamos línea para ver la evolución temporal
    data: {
        labels: [], // Las etiquetas serán las marcas de tiempo
        datasets: [{
            label: 'Uso de CPU (%)',
            data: [],
            borderColor: 'rgba(255, 99, 132, 1)',
            borderWidth: 2,
            fill: false
        }, {
            label: 'Uso de RAM (%)',
            data: [],
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 2,
            fill: false
        }]
    },
    options: pieOptions // Usamos la variable requerida por el enunciado
});
</script>
