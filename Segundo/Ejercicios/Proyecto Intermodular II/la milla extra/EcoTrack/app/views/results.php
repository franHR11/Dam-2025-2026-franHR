<?php
$page_title = 'Resultados';
include __DIR__ . '/layout/header.php';
?>

<div class="results-container">
    <div class="results-header">
        <h1 class="results-title">
            <i class="fas fa-chart-pie"></i>
            Tu Huella Ecológica
        </h1>
        <p class="results-subtitle">Aquí están los resultados de tu cálculo</p>
    </div>

    <div class="results-content">
        <div class="result-card">
            <div class="result-score">
                <div class="score-circle">
                    <div class="score-value"><?php echo number_format($result['co2_kg'], 1); ?></div>
                    <div class="score-unit">kg CO₂/día</div>
                </div>
                <div class="score-level <?php echo $result['co2_kg'] <= 3 ? 'excellent' : ($result['co2_kg'] <= 5 ? 'good' : ($result['co2_kg'] <= 7 ? 'fair' : 'poor')); ?>">
                    <?php
                    if ($result['co2_kg'] <= 3) {
                        echo '<i class="fas fa-star"></i> Eco Héroe';
                    } elseif ($result['co2_kg'] <= 5) {
                        echo '<i class="fas fa-leaf"></i> Eco Consciente';
                    } elseif ($result['co2_kg'] <= 7) {
                        echo '<i class="fas fa-seedling"></i> Eco Aprendiz';
                    } else {
                        echo '<i class="fas fa-exclamation-triangle"></i> Eco Principiante';
                    }
                    ?>
                </div>
            </div>

            <div class="result-comparison">
                <h3 class="comparison-title">Comparación</h3>
                <div class="comparison-bars">
                    <div class="comparison-item">
                        <span class="comparison-label">Tu impacto</span>
                        <div class="comparison-bar">
                            <div class="comparison-fill comparison-fill--user" style="width: <?php echo min(100, ($result['co2_kg'] / 10) * 100); ?>%;"></div>
                        </div>
                        <span class="comparison-value"><?php echo number_format($result['co2_kg'], 1); ?> kg</span>
                    </div>
                    <div class="comparison-item">
                        <span class="comparison-label">Promedio español</span>
                        <div class="comparison-bar">
                            <div class="comparison-fill comparison-fill--average" style="width: 75%;"></div>
                        </div>
                        <span class="comparison-value">7.5 kg</span>
                    </div>
                    <div class="comparison-item">
                        <span class="comparison-label">Recomendado ONU</span>
                        <div class="comparison-bar">
                            <div class="comparison-fill comparison-fill--recommended" style="width: 40%;"></div>
                        </div>
                        <span class="comparison-value">4.0 kg</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="advice-card">
            <div class="advice-header">
                <h3 class="advice-title">
                    <i class="fas fa-lightbulb"></i>
                    Consejos Personalizados
                </h3>
            </div>
            <div class="advice-content">
                <div class="advice-text">
                    <p><?php echo htmlspecialchars($result['advice']); ?></p>
                </div>
                <div class="advice-actions">
                    <div class="advice-tips">
                        <div class="tip-item">
                            <i class="fas fa-bicycle"></i>
                            <span>Usa más bicicleta o transporte público</span>
                        </div>
                        <div class="tip-item">
                            <i class="fas fa-lightbulb"></i>
                            <span>Ahorra energía desconectando aparatos</span>
                        </div>
                        <div class="tip-item">
                            <i class="fas fa-carrot"></i>
                            <span>Incluye más días vegetarianos</span>
                        </div>
                        <div class="tip-item">
                            <i class="fas fa-recycle"></i>
                            <span>Mejora tus hábitos de reciclaje</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="impact-breakdown">
            <h3 class="breakdown-title">Desglose de tu Impacto</h3>
            <div class="breakdown-chart">
                <canvas id="distributionChart" width="400" height="200"></canvas>
            </div>
        </div>

        <div class="results-actions">
            <div class="action-buttons">
                <a href="index.php?page=habit_form" class="btn btn--primary">
                    <i class="fas fa-redo"></i>
                    Nuevo Cálculo
                </a>
                <a href="index.php?page=dashboard" class="btn btn--secondary">
                    <i class="fas fa-tachometer-alt"></i>
                    Ver Dashboard
                </a>
                <a href="index.php?page=compare" class="btn btn--outline">
                    <i class="fas fa-balance-scale"></i>
                    Comparar Datos
                </a>
            </div>

            <?php if ($comparison['better_than_average']): ?>
                <div class="success-message">
                    <i class="fas fa-check-circle"></i>
                    <span>¡Felicidades! Tu huella es <strong><?php echo $comparison['percentage_difference']; ?>%</strong> menor que el promedio.</span>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// Datos para el gráfico de distribución
const distributionData = {
    labels: ['Transporte', 'Energía', 'Dieta', 'Otros'],
    values: [
        <?php echo max(1, $result['co2_kg'] * 0.4); ?>,
        <?php echo max(1, $result['co2_kg'] * 0.3); ?>,
        <?php echo max(1, $result['co2_kg'] * 0.2); ?>,
        <?php echo max(1, $result['co2_kg'] * 0.1); ?>
    ]
};

// Configuración del gráfico de pastel
const distributionConfig = {
    type: 'doughnut',
    data: {
        labels: distributionData.labels,
        datasets: [{
            data: distributionData.values,
            backgroundColor: [
                '#22c55e',
                '#3b82f6',
                '#f59e0b',
                '#8b5cf6'
            ],
            borderColor: '#ffffff',
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
                    usePointStyle: true
                }
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                        const percentage = ((context.parsed / total) * 100).toFixed(1);
                        return context.label + ': ' + context.parsed.toFixed(1) + ' kg (' + percentage + '%)';
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
};

// Inicializar gráfico cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('distributionChart');
    if (ctx && window.chartHandler) {
        window.chartHandler.createDistributionChart('distributionChart', {
            labels: distributionData.labels,
            values: distributionData.values
        });
    } else if (ctx && typeof Chart !== 'undefined') {
        new Chart(ctx, distributionConfig);
    }
});

// Animación del contador
function animateScore() {
    const scoreElement = document.querySelector('.score-value');
    const targetValue = <?php echo $result['co2_kg']; ?>;
    const duration = 2000;
    const start = 0;
    const increment = targetValue / (duration / 16);
    let current = start;

    const timer = setInterval(() => {
        current += increment;
        if (current >= targetValue) {
            current = targetValue;
            clearInterval(timer);
        }
        scoreElement.textContent = current.toFixed(1);
    }, 16);
}

// Iniciar animación
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(animateScore, 500);
});
</script>

<style>
.results-container {
    max-width: 800px;
    margin: 0 auto;
    padding: 2rem;
}

.results-header {
    text-align: center;
    margin-bottom: 3rem;
}

.results-title {
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
}

.results-title i {
    color: var(--primary-color);
}

.results-subtitle {
    font-size: 1.125rem;
    color: var(--text-secondary);
}

.result-card {
    background: var(--bg-primary);
    border: 1px solid var(--border-color);
    border-radius: var(--radius-xl);
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: var(--shadow-lg);
}

.result-score {
    text-align: center;
    margin-bottom: 2rem;
}

.score-circle {
    width: 150px;
    height: 150px;
    margin: 0 auto 1.5rem;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: white;
    box-shadow: 0 10px 30px rgba(34, 197, 94, 0.3);
    position: relative;
    overflow: hidden;
}

.score-circle::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.1), transparent);
    transform: rotate(45deg);
    animation: shine 3s infinite;
}

@keyframes shine {
    0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
    100% { transform: translateX(100%) translateY(100%) rotate(45deg); }
}

.score-value {
    font-size: 2.5rem;
    font-weight: 800;
    line-height: 1;
}

.score-unit {
    font-size: 0.875rem;
    opacity: 0.9;
    margin-top: 0.25rem;
}

.score-level {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    border-radius: 2rem;
    font-weight: 600;
    font-size: 1.125rem;
}

.score-level.excellent {
    background: linear-gradient(135deg, var(--success-color), #059669);
    color: white;
}

.score-level.good {
    background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
    color: white;
}

.score-level.fair {
    background: linear-gradient(135deg, var(--warning-color), #d97706);
    color: white;
}

.score-level.poor {
    background: linear-gradient(135deg, var(--error-color), #dc2626);
    color: white;
}

.result-comparison {
    border-top: 1px solid var(--border-light);
    padding-top: 2rem;
}

.comparison-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 1.5rem;
    text-align: center;
}

.comparison-bars {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.comparison-item {
    display: grid;
    grid-template-columns: 120px 1fr 60px;
    align-items: center;
    gap: 1rem;
}

.comparison-label {
    font-size: 0.875rem;
    font-weight: 500;
    color: var(--text-primary);
}

.comparison-bar {
    height: 1rem;
    background: var(--bg-light);
    border-radius: var(--radius);
    overflow: hidden;
    position: relative;
}

.comparison-fill {
    height: 100%;
    border-radius: var(--radius);
    transition: width 1s ease;
    position: relative;
}

.comparison-fill--user {
    background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
}

.comparison-fill--average {
    background: linear-gradient(90deg, #64748b, #475569);
}

.comparison-fill--recommended {
    background: linear-gradient(90deg, var(--success-color), #059669);
}

.comparison-value {
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--text-primary);
    text-align: right;
}

.advice-card {
    background: linear-gradient(135deg, var(--bg-secondary), var(--bg-light));
    border: 1px solid var(--border-color);
    border-radius: var(--radius-xl);
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: var(--shadow);
}

.advice-header {
    margin-bottom: 1.5rem;
}

.advice-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--text-primary);
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.advice-title i {
    color: var(--warning-color);
}

.advice-text {
    background: white;
    padding: 1.5rem;
    border-radius: var(--radius-lg);
    border-left: 4px solid var(--warning-color);
    margin-bottom: 1.5rem;
}

.advice-text p {
    color: var(--text-primary);
    line-height: 1.6;
    margin: 0;
}

.advice-tips {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
}

.tip-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem;
    background: white;
    border-radius: var(--radius);
    border: 1px solid var(--border-light);
    transition: var(--transition);
}

.tip-item:hover {
    border-color: var(--primary-color);
    transform: translateY(-2px);
    box-shadow: var(--shadow);
}

.tip-item i {
    color: var(--primary-color);
    font-size: 1.125rem;
}

.tip-item span {
    font-size: 0.875rem;
    color: var(--text-secondary);
    line-height: 1.4;
}

.impact-breakdown {
    background: var(--bg-primary);
    border: 1px solid var(--border-color);
    border-radius: var(--radius-xl);
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: var(--shadow);
}

.breakdown-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 1.5rem;
    text-align: center;
}

.breakdown-chart {
    position: relative;
    height: 300px;
}

.results-actions {
    text-align: center;
}

.action-buttons {
    display: flex;
    gap: 1rem;
    justify-content: center;
    margin-bottom: 2rem;
    flex-wrap: wrap;
}

.success-message {
    display: inline-flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem 1.5rem;
    background: linear-gradient(135deg, #d1fae5, #a7f3d0);
    border: 1px solid #6ee7b7;
    border-radius: var(--radius-lg);
    color: #065f46;
    font-weight: 500;
    animation: slideUp 0.5s ease;
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.success-message i {
    color: #10b981;
    font-size: 1.25rem;
}

@media (max-width: 768px) {
    .results-container {
        padding: 1rem;
    }

    .results-title {
        font-size: 2rem;
        flex-direction: column;
        gap: 0.5rem;
    }

    .score-circle {
        width: 120px;
        height: 120px;
    }

    .score-value {
        font-size: 2rem;
    }

    .comparison-item {
        grid-template-columns: 1fr;
        gap: 0.5rem;
        text-align: center;
    }

    .action-buttons {
        flex-direction: column;
        align-items: center;
    }

    .action-buttons .btn {
        width: 100%;
        max-width: 250px;
    }

    .advice-tips {
        grid-template-columns: 1fr;
    }

    .breakdown-chart {
        height: 250px;
    }
}

@media (max-width: 480px) {
    .result-card,
    .advice-card,
    .impact-breakdown {
        padding: 1.5rem;
    }

    .score-circle {
        width: 100px;
        height: 100px;
    }

    .score-value {
        font-size: 1.75rem;
    }

    .score-unit {
        font-size: 0.75rem;
    }

    .score-level {
        padding: 0.5rem 1rem;
        font-size: 1rem;
    }
}
</style>

<?php include __DIR__ . '/layout/footer.php'; ?>
