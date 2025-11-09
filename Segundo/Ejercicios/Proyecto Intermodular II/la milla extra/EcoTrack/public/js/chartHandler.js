/**
 * ChartHandler.js - Manejo de gráficos Chart.js para EcoTrack
 */

class ChartHandler {
    constructor() {
        this.charts = new Map();
        this.defaultOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        font: {
                            family: 'Inter',
                            size: 12
                        },
                        padding: 15,
                        usePointStyle: true
                    }
                },
                tooltip: {
                    enabled: true,
                    backgroundColor: 'rgba(31, 41, 55, 0.95)',
                    titleFont: {
                        family: 'Inter',
                        size: 14,
                        weight: '600'
                    },
                    bodyFont: {
                        family: 'Inter',
                        size: 12
                    },
                    padding: 12,
                    cornerRadius: 8,
                    displayColors: true,
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            label += context.parsed.y ? context.parsed.y.toFixed(2) : context.parsed.x.toFixed(2);
                            return label + ' kg CO₂';
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        font: {
                            family: 'Inter',
                            size: 11
                        }
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        borderDash: [3, 3],
                        color: 'rgba(0, 0, 0, 0.05)'
                    },
                    ticks: {
                        font: {
                            family: 'Inter',
                            size: 11
                        },
                        callback: function(value) {
                            return value.toFixed(1) + ' kg';
                        }
                    }
                }
            },
            animation: {
                duration: 1000,
                easing: 'easeInOutQuart'
            }
        };
    }

    // Crear gráfico de evolución de CO2
    createEvolutionChart(canvasId, data) {
        const ctx = document.getElementById(canvasId);
        if (!ctx) return null;

        // Destruir gráfico existente
        if (this.charts.has(canvasId)) {
            this.charts.get(canvasId).destroy();
        }

        const chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.labels || [],
                datasets: [{
                    label: 'Huella de Carbono (kg CO₂/día)',
                    data: data.values || [],
                    borderColor: '#22c55e',
                    backgroundColor: 'rgba(34, 197, 94, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    pointBackgroundColor: '#22c55e',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: '#22c55e',
                    pointHoverBorderWidth: 3
                }]
            },
            options: {
                ...this.defaultOptions,
                scales: {
                    ...this.defaultOptions.scales,
                    y: {
                        ...this.defaultOptions.scales.y,
                        title: {
                            display: true,
                            text: 'CO₂ (kg/día)',
                            font: {
                                family: 'Inter',
                                size: 14,
                                weight: '600'
                            }
                        }
                    },
                    x: {
                        ...this.defaultOptions.scales.x,
                        title: {
                            display: true,
                            text: 'Período',
                            font: {
                                family: 'Inter',
                                size: 14,
                                weight: '600'
                            }
                        }
                    }
                }
            }
        });

        this.charts.set(canvasId, chart);
        return chart;
    }

    // Crear gráfico de barras comparativo
    createComparisonChart(canvasId, data) {
        const ctx = document.getElementById(canvasId);
        if (!ctx) return null;

        if (this.charts.has(canvasId)) {
            this.charts.get(canvasId).destroy();
        }

        const chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.labels || [],
                datasets: [
                    {
                        label: 'Tu Impacto',
                        data: data.userValues || [],
                        backgroundColor: '#22c55e',
                        borderColor: '#16a34a',
                        borderWidth: 2,
                        borderRadius: 6,
                        barThickness: 40
                    },
                    {
                        label: 'Promedio',
                        data: data.averageValues || [],
                        backgroundColor: '#94a3b8',
                        borderColor: '#64748b',
                        borderWidth: 2,
                        borderRadius: 6,
                        barThickness: 40
                    }
                ]
            },
            options: {
                ...this.defaultOptions,
                plugins: {
                    ...this.defaultOptions.plugins,
                    legend: {
                        ...this.defaultOptions.plugins.legend,
                        position: 'top'
                    }
                },
                scales: {
                    ...this.defaultOptions.scales,
                    y: {
                        ...this.defaultOptions.scales.y,
                        title: {
                            display: true,
                            text: 'CO₂ (kg/día)',
                            font: {
                                family: 'Inter',
                                size: 14,
                                weight: '600'
                            }
                        }
                    }
                }
            }
        });

        this.charts.set(canvasId, chart);
        return chart;
    }

    // Crear gráfico de pastel para distribución de impacto
    createDistributionChart(canvasId, data) {
        const ctx = document.getElementById(canvasId);
        if (!ctx) return null;

        if (this.charts.has(canvasId)) {
            this.charts.get(canvasId).destroy();
        }

        const colors = [
            '#22c55e', // Transporte
            '#3b82f6', // Energía
            '#f59e0b', // Dieta
            '#8b5cf6'  // Otros
        ];

        const chart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: data.labels || [],
                datasets: [{
                    data: data.values || [],
                    backgroundColor: colors,
                    borderColor: '#fff',
                    borderWidth: 2,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            font: {
                                family: 'Inter',
                                size: 12
                            },
                            padding: 15,
                            usePointStyle: true,
                            pointStyle: 'circle'
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((context.parsed / total) * 100).toFixed(1);
                                return context.label + ': ' + context.parsed.toFixed(2) + ' kg (' + percentage + '%)';
                            }
                        }
                    }
                },
                animation: {
                    animateRotate: true,
                    animateScale: true,
                    duration: 1000
                }
            }
        });

        this.charts.set(canvasId, chart);
        return chart;
    }

    // Crear gráfico de radar para nivel ecológico
    createRadarChart(canvasId, data) {
        const ctx = document.getElementById(canvasId);
        if (!ctx) return null;

        if (this.charts.has(canvasId)) {
            this.charts.get(canvasId).destroy();
        }

        const chart = new Chart(ctx, {
            type: 'radar',
            data: {
                labels: ['Transporte', 'Energía', 'Dieta', 'Reciclaje', 'Consumo'],
                datasets: [
                    {
                        label: 'Tu Nivel',
                        data: data.userValues || [],
                        borderColor: '#22c55e',
                        backgroundColor: 'rgba(34, 197, 94, 0.2)',
                        borderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    },
                    {
                        label: 'Nivel Ideal',
                        data: data.idealValues || [2, 2, 2, 5, 2],
                        borderColor: '#94a3b8',
                        backgroundColor: 'rgba(148, 163, 184, 0.1)',
                        borderWidth: 2,
                        borderDash: [5, 5],
                        pointRadius: 3,
                        pointHoverRadius: 5
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    r: {
                        beginAtZero: true,
                        max: 5,
                        ticks: {
                            stepSize: 1,
                            font: {
                                family: 'Inter',
                                size: 10
                            }
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        },
                        pointLabels: {
                            font: {
                                family: 'Inter',
                                size: 11,
                                weight: '500'
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            font: {
                                family: 'Inter',
                                size: 12
                            }
                        }
                    }
                }
            }
        });

        this.charts.set(canvasId, chart);
        return chart;
    }

    // Actualizar datos de un gráfico existente
    updateChart(canvasId, newData) {
        const chart = this.charts.get(canvasId);
        if (!chart) return false;

        chart.data.labels = newData.labels || chart.data.labels;
        chart.data.datasets.forEach((dataset, index) => {
            if (newData.datasets && newData.datasets[index]) {
                dataset.data = newData.datasets[index].data;
                dataset.label = newData.datasets[index].label || dataset.label;
            }
        });

        chart.update('active');
        return true;
    }

    // Destruir un gráfico específico
    destroyChart(canvasId) {
        const chart = this.charts.get(canvasId);
        if (chart) {
            chart.destroy();
            this.charts.delete(canvasId);
            return true;
        }
        return false;
    }

    // Destruir todos los gráficos
    destroyAllCharts() {
        this.charts.forEach((chart, canvasId) => {
            chart.destroy();
        });
        this.charts.clear();
    }

    // Obtener un gráfico específico
    getChart(canvasId) {
        return this.charts.get(canvasId);
    }

    // Exportar gráfico como imagen
    exportChart(canvasId, filename = 'chart.png') {
        const chart = this.charts.get(canvasId);
        if (!chart) return false;

        const url = chart.toBase64Image();
        const link = document.createElement('a');
        link.download = filename;
        link.href = url;
        link.click();

        return true;
    }

    // Inicializar todos los gráficos de la página
    initCharts() {
        // Gráfico de evolución
        const evolutionCanvas = document.getElementById('evolutionChart');
        if (evolutionCanvas) {
            const data = JSON.parse(evolutionCanvas.dataset.chartData || '{}');
            this.createEvolutionChart('evolutionChart', data);
        }

        // Gráfico de distribución
        const distributionCanvas = document.getElementById('distributionChart');
        if (distributionCanvas) {
            const data = JSON.parse(distributionCanvas.dataset.chartData || '{}');
            this.createDistributionChart('distributionChart', data);
        }

        // Gráfico de comparación
        const comparisonCanvas = document.getElementById('comparisonChart');
        if (comparisonCanvas) {
            const data = JSON.parse(comparisonCanvas.dataset.chartData || '{}');
            this.createComparisonChart('comparisonChart', data);
        }

        // Gráfico de radar
        const radarCanvas = document.getElementById('radarChart');
        if (radarCanvas) {
            const data = JSON.parse(radarCanvas.dataset.chartData || '{}');
            this.createRadarChart('radarChart', data);
        }
    }

    // Crear gráfico de progreso circular
    createCircularProgress(canvasId, value, maxValue = 100, color = '#22c55e') {
        const ctx = document.getElementById(canvasId);
        if (!ctx) return null;

        if (this.charts.has(canvasId)) {
            this.charts.get(canvasId).destroy();
        }

        const percentage = (value / maxValue) * 100;

        const chart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                datasets: [{
                    data: [percentage, 100 - percentage],
                    backgroundColor: [color, '#e5e7eb'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                rotation: -90,
                circumference: 180,
                cutout: '70%',
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        enabled: false
                    }
                },
                animation: {
                    animateRotate: true,
                    animateScale: false,
                    duration: 1500
                }
            }
        });

        this.charts.set(canvasId, chart);
        return chart;
    }

    // Animar contador numérico
    animateCounter(element, targetValue, duration = 2000) {
        const startValue = 0;
        const startTime = Date.now();

        const animate = () => {
            const currentTime = Date.now();
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);

            const easeOutQuart = 1 - Math.pow(1 - progress, 4);
            const currentValue = startValue + (targetValue - startValue) * easeOutQuart;

            element.textContent = currentValue.toFixed(1);

            if (progress < 1) {
                requestAnimationFrame(animate);
            }
        };

        animate();
    }
}

// Instancia global
const chartHandler = new ChartHandler();

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    chartHandler.initCharts();

    // Animar contadores con data-counter
    document.querySelectorAll('[data-counter]').forEach(element => {
        const targetValue = parseFloat(element.dataset.counter);
        chartHandler.animateCounter(element, targetValue);
    });
});

// Exponer globalmente
window.chartHandler = chartHandler;
window.ChartHandler = ChartHandler;
