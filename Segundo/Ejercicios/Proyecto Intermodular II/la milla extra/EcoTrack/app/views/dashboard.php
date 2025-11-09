<div class="dashboard">
    <div class="dashboard__header">
        <h1 class="dashboard__title">
            <i class="fas fa-leaf"></i>
            ¡Hola, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!
        </h1>
        <p class="dashboard__subtitle">Tu panel de control ecológico</p>
    </div>

    <div class="dashboard__stats">
        <div class="stats-grid">
            <div class="stat-card stat-card--primary">
                <div class="stat-card__icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="stat-card__content">
                    <div class="stat-card__value"><?php echo round($stats['avg_co2'] ?? 0, 2); ?></div>
                    <div class="stat-card__label">kg CO₂/día (Promedio)</div>
                </div>
            </div>

            <div class="stat-card stat-card--success">
                <div class="stat-card__icon">
                    <i class="fas fa-calculator"></i>
                </div>
                <div class="stat-card__content">
                    <div class="stat-card__value"><?php echo $stats['total_calculations'] ?? 0; ?></div>
                    <div class="stat-card__label">Cálculos Realizados</div>
                </div>
            </div>

            <div class="stat-card stat-card--warning">
                <div class="stat-card__icon">
                    <i class="fas fa-trophy"></i>
                </div>
                <div class="stat-card__content">
                    <div class="stat-card__value"><?php echo count($achievements); ?></div>
                    <div class="stat-card__label">Logros Desbloqueados</div>
                </div>
            </div>

            <div class="stat-card stat-card--info">
                <div class="stat-card__icon">
                    <i class="fas fa-star"></i>
                </div>
                <div class="stat-card__content">
                    <div class="stat-card__value"><?php echo htmlspecialchars($eco_level['level']); ?></div>
                    <div class="stat-card__label">Nivel Ecológico</div>
                </div>
            </div>
        </div>
    </div>

    <div class="dashboard__content">
        <div class="dashboard__main">
            <div class="card">
                <div class="card__header">
                    <h2 class="card__title">
                        <i class="fas fa-chart-area"></i>
                        Evolución Mensual
                    </h2>
                    <div class="card__actions">
                        <button class="btn btn--outline btn--small" onclick="exportData()">
                            <i class="fas fa-download"></i>
                            Exportar
                        </button>
                    </div>
                </div>
                <div class="card__body">
                    <div class="chart-container">
                        <canvas id="evolutionChart" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card__header">
                    <h2 class="card__title">
                        <i class="fas fa-leaf"></i>
                        Consejos Personalizados
                    </h2>
                </div>
                <div class="card__body">
                    <div class="tips-container">
                        <?php if (!empty($recent_scores)): ?>
                            <?php $latest = $recent_scores[0]; ?>
                            <div class="tip tip--<?php echo $latest['co2_kg'] <= 5 ? 'success' : 'warning'; ?>">
                                <div class="tip__icon">
                                    <i class="fas fa-lightbulb"></i>
                                </div>
                                <div class="tip__content">
                                    <p><?php echo htmlspecialchars($latest['advice']); ?></p>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="tip tip--info">
                                <div class="tip__icon">
                                    <i class="fas fa-info-circle"></i>
                                </div>
                                <div class="tip__content">
                                    <p>Comienza registrando tus hábitos para recibir consejos personalizados.</p>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card__header">
                    <h2 class="card__title">
                        <i class="fas fa-history"></i>
                        Actividad Reciente
                    </h2>
                </div>
                <div class="card__body">
                    <div class="activity-list">
                        <?php if (!empty($recent_scores)): ?>
                            <?php foreach (array_slice($recent_scores, 0, 5) as $score): ?>
                                <div class="activity-item">
                                    <div class="activity__icon">
                                        <i class="fas fa-chart-bar"></i>
                                    </div>
                                    <div class="activity__content">
                                        <div class="activity__title">
                                            Cálculo de <?php echo round($score['co2_kg'], 2); ?> kg CO₂
                                        </div>
                                        <div class="activity__date">
                                            <?php echo date('d/m/Y H:i', strtotime($score['created_at'])); ?>
                                        </div>
                                    </div>
                                    <div class="activity__badge badge--<?php echo $score['co2_kg'] <= 3 ? 'success' : ($score['co2_kg'] <= 5 ? 'warning' : 'danger'); ?>">
                                        <?php echo $score['co2_kg'] <= 3 ? 'Excelente' : ($score['co2_kg'] <= 5 ? 'Bueno' : 'Mejorable'); ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="fas fa-chart-line"></i>
                                <p>Aún no tienes cálculos registrados</p>
                                <a href="index.php?page=habit_form" class="btn btn--primary btn--small">
                                    <i class="fas fa-plus"></i>
                                    Primer Cálculo
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="dashboard__sidebar">
            <div class="card">
                <div class="card__header">
                    <h2 class="card__title">
                        <i class="fas fa-medal"></i>
                        Nivel Ecológico
                    </h2>
                </div>
                <div class="card__body">
                    <div class="level-indicator">
                        <div class="level-icon" style="color: <?php echo $eco_level['color']; ?>">
                            <?php echo $eco_level['icon']; ?>
                        </div>
                        <div class="level-info">
                            <div class="level-name"><?php echo htmlspecialchars($eco_level['level']); ?></div>
                            <div class="level-description"><?php echo htmlspecialchars($eco_level['description']); ?></div>
                        </div>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-bar__fill" style="width: <?php echo min(100, max(0, 100 - (($stats['avg_co2'] ?? 0) / 10) * 100)); ?>%; background-color: <?php echo $eco_level['color']; ?>"></div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card__header">
                    <h2 class="card__title">
                        <i class="fas fa-trophy"></i>
                        Logros Recientes
                    </h2>
                </div>
                <div class="card__body">
                    <div class="achievements-list">
                        <?php if (!empty($achievements)): ?>
                            <?php foreach (array_slice($achievements, 0, 3) as $achievement): ?>
                                <div class="achievement-item">
                                    <div class="achievement__icon" style="color: <?php echo isset($achievement['badge_color']) ? $achievement['badge_color'] : '#22c55e'; ?>">
                                        <i class="<?php echo htmlspecialchars($achievement['icon']); ?>"></i>
                                    </div>
                                    <div class="achievement__content">
                                        <div class="achievement__name"><?php echo htmlspecialchars($achievement['name']); ?></div>
                                        <div class="achievement__date"><?php echo date('d/m/Y', strtotime($achievement['unlocked_at'])); ?></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="empty-state empty-state--small">
                                <i class="fas fa-lock"></i>
                                <p>Completa más cálculos para desbloquear logros</p>
                            </div>
                        <?php endif; ?>
                    </div>
                    <?php if (!empty($achievements)): ?>
                        <div class="card__footer">
                            <a href="index.php?page=achievements" class="btn btn--outline btn--small btn--full">
                                Ver Todos los Logros
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card">
                <div class="card__header">
                    <h2 class="card__title">
                        <i class="fas fa-bolt"></i>
                        Acciones Rápidas
                    </h2>
                </div>
                <div class="card__body">
                    <div class="quick-actions">
                        <a href="index.php?page=habit_form" class="btn btn--primary btn--full">
                            <i class="fas fa-plus-circle"></i>
                            Nuevo Cálculo
                        </a>
                        <a href="index.php?page=history" class="btn btn--outline btn--full">
                            <i class="fas fa-history"></i>
                            Ver Historial
                        </a>
                        <a href="index.php?page=compare" class="btn btn--outline btn--full">
                            <i class="fas fa-balance-scale"></i>
                            Comparar Datos
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Datos para el gráfico de evolución
const chartData = {
    labels: <?php echo json_encode(array_reverse(array_column($monthly_stats, 'month'))); ?>,
    datasets: [{
        label: 'Promedio CO₂ (kg/día)',
        data: <?php echo json_encode(array_reverse(array_column($monthly_stats, 'avg_co2'))); ?>,
        borderColor: '#22c55e',
        backgroundColor: 'rgba(34, 197, 94, 0.1)',
        borderWidth: 2,
        fill: true,
        tension: 0.4
    }]
};

// Configuración del gráfico
const chartConfig = {
    type: 'line',
    data: chartData,
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: true,
                position: 'top'
            },
            tooltip: {
                mode: 'index',
                intersect: false,
                callbacks: {
                    label: function(context) {
                        return context.dataset.label + ': ' + context.parsed.y.toFixed(2) + ' kg CO₂';
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                title: {
                    display: true,
                    text: 'CO₂ (kg/día)'
                }
            },
            x: {
                title: {
                    display: true,
                    text: 'Mes'
                }
            }
        },
        interaction: {
            mode: 'nearest',
            axis: 'x',
            intersect: false
        }
    }
};

// Inicializar gráfico cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('evolutionChart');
    if (ctx) {
        new Chart(ctx, chartConfig);
    }
});

// Función para exportar datos
function exportData() {
    window.location.href = 'index.php?action=export';
}
</script>

<style>
.dashboard {
    padding: 2rem 0;
}

.dashboard__header {
    text-align: center;
    margin-bottom: 3rem;
}

.dashboard__title {
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 0.5rem;
}

.dashboard__title i {
    color: var(--primary-color);
    margin-right: 0.5rem;
}

.dashboard__subtitle {
    font-size: 1.125rem;
    color: var(--text-secondary);
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 3rem;
}

.stat-card {
    background: var(--bg-primary);
    border-radius: var(--radius-lg);
    padding: 1.5rem;
    box-shadow: var(--shadow);
    border: 1px solid var(--border-color);
    display: flex;
    align-items: center;
    gap: 1rem;
    transition: var(--transition);
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
}

.stat-card--primary { border-left: 4px solid var(--primary-color); }
.stat-card--success { border-left: 4px solid var(--success-color); }
.stat-card--warning { border-left: 4px solid var(--warning-color); }
.stat-card--info { border-left: 4px solid var(--info-color); }

.stat-card__icon {
    width: 3rem;
    height: 3rem;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    color: white;
}

.stat-card--primary .stat-card__icon { background: var(--primary-color); }
.stat-card--success .stat-card__icon { background: var(--success-color); }
.stat-card--warning .stat-card__icon { background: var(--warning-color); }
.stat-card--info .stat-card__icon { background: var(--info-color); }

.stat-card__value {
    font-size: 2rem;
    font-weight: 700;
    color: var(--text-primary);
    line-height: 1;
}

.stat-card__label {
    font-size: 0.875rem;
    color: var(--text-secondary);
    margin-top: 0.25rem;
}

.dashboard__content {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 2rem;
}

.chart-container {
    position: relative;
    height: 300px;
}

.tips-container {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.tip {
    background: var(--bg-secondary);
    border-radius: var(--radius);
    padding: 1rem;
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    border-left: 4px solid;
}

.tip--success { border-left-color: var(--success-color); }
.tip--warning { border-left-color: var(--warning-color); }
.tip--info { border-left-color: var(--info-color); }

.tip__icon {
    color: var(--text-secondary);
    margin-top: 0.125rem;
}

.activity-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.activity-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: var(--bg-secondary);
    border-radius: var(--radius);
}

.activity__icon {
    width: 2.5rem;
    height: 2.5rem;
    background: var(--primary-color);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.activity__content {
    flex: 1;
}

.activity__title {
    font-weight: 600;
    color: var(--text-primary);
}

.activity__date {
    font-size: 0.875rem;
    color: var(--text-secondary);
}

.badge--success { background: var(--success-color); }
.badge--warning { background: var(--warning-color); }
.badge--danger { background: var(--error-color); }

.badge {
    padding: 0.25rem 0.75rem;
    border-radius: var(--radius);
    font-size: 0.75rem;
    font-weight: 600;
    color: white;
}

.level-indicator {
    text-align: center;
    margin-bottom: 1.5rem;
}

.level-icon {
    font-size: 3rem;
    margin-bottom: 1rem;
}

.level-name {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 0.25rem;
}

.level-description {
    font-size: 0.875rem;
    color: var(--text-secondary);
}

.progress-bar {
    height: 0.5rem;
    background: var(--bg-light);
    border-radius: var(--radius);
    overflow: hidden;
}

.progress-bar__fill {
    height: 100%;
    transition: width 1s ease;
}

.achievements-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.achievement-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem;
    background: var(--bg-secondary);
    border-radius: var(--radius);
}

.achievement__icon {
    font-size: 1.25rem;
}

.achievement__name {
    font-weight: 600;
    color: var(--text-primary);
}

.achievement__date {
    font-size: 0.75rem;
    color: var(--text-secondary);
}

.quick-actions {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.empty-state {
    text-align: center;
    padding: 2rem;
    color: var(--text-secondary);
}

.empty-state i {
    font-size: 3rem;
    margin-bottom: 1rem;
    opacity: 0.5;
}

.empty-state--small {
    padding: 1.5rem;
}

.empty-state--small i {
    font-size: 2rem;
}

@media (max-width: 1024px) {
    .dashboard__content {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .stats-grid {
        grid-template-columns: 1fr;
    }

    .dashboard__title {
        font-size: 2rem;
    }
}
</style>
