<?php
$page_title = 'Historial';
?>

<div class="history-container">
    <div class="history-header">
        <h1 class="history-title">
            <i class="fas fa-history"></i>
            Mi Historial Ecológico
        </h1>
        <p class="history-subtitle">Registro completo de tus cálculos y progreso</p>
    </div>

    <div class="history-stats">
        <div class="stat-card stat-card--primary">
            <div class="stat-card__icon">
                <i class="fas fa-calculator"></i>
            </div>
            <div class="stat-card__content">
                <div class="stat-card__value"><?php echo $stats['total_calculations'] ?? 0; ?></div>
                <div class="stat-card__label">Total de Cálculos</div>
            </div>
        </div>

        <div class="stat-card stat-card--success">
            <div class="stat-card__icon">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="stat-card__content">
                <div class="stat-card__value"><?php echo round($stats['avg_co2'] ?? 0, 2); ?></div>
                <div class="stat-card__label">Promedio CO₂ (kg/día)</div>
            </div>
        </div>

        <div class="stat-card stat-card--warning">
            <div class="stat-card__icon">
                <i class="fas fa-trophy"></i>
            </div>
            <div class="stat-card__content">
                <div class="stat-card__value"><?php echo round($stats['min_co2'] ?? 0, 2); ?></div>
                <div class="stat-card__label">Mejor Registro (kg CO₂)</div>
            </div>
        </div>

        <div class="stat-card stat-card--info">
            <div class="stat-card__icon">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div class="stat-card__content">
                <div class="stat-card__value"><?php echo count($monthly_stats); ?></div>
                <div class="stat-card__label">Meses Activos</div>
            </div>
        </div>
    </div>

    <div class="history-content">
        <div class="history-section">
            <div class="section-header">
                <h2 class="section-title">
                    <i class="fas fa-chart-bar"></i>
                    Evolución Mensual
                </h2>
                <div class="section-actions">
                    <button class="btn btn--outline btn--small" onclick="exportHistory()">
                        <i class="fas fa-download"></i>
                        Exportar Datos
                    </button>
                </div>
            </div>

            <?php if (!empty($monthly_stats)): ?>
                <div class="chart-container">
                    <canvas id="historyChart" width="400" height="200"></canvas>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-chart-line"></i>
                    <h3>Sin datos para mostrar</h3>
                    <p>Comienza a registrar tus hábitos para ver tu evolución</p>
                    <a href="index.php?page=habit_form" class="btn btn--primary">
                        <i class="fas fa-plus-circle"></i>
                        Primer Cálculo
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <div class="history-section">
            <div class="section-header">
                <h2 class="section-title">
                    <i class="fas fa-list"></i>
                    Registro Detallado
                </h2>
                <div class="section-filters">
                    <select class="form-input form-input--small" id="monthFilter" onchange="filterHistory()">
                        <option value="">Todos los meses</option>
                        <?php foreach ($monthly_stats as $stat): ?>
                            <option value="<?php echo $stat['month']; ?>">
                                <?php echo date('F Y', strtotime($stat['month'] . '-01')); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <select class="form-input form-input--small" id="sortOrder" onchange="sortHistory()">
                        <option value="desc">Más reciente primero</option>
                        <option value="asc">Más antiguo primero</option>
                    </select>
                </div>
            </div>

            <?php if (!empty($eco_scores)): ?>
                <div class="history-table-container">
                    <table class="history-table">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>CO₂ (kg/día)</th>
                                <th>Nivel</th>
                                <th>Consejo Principal</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="historyTableBody">
                            <?php foreach ($eco_scores as $score): ?>
                                <tr data-date="<?php echo date('Y-m', strtotime($score['created_at'])); ?>">
                                    <td>
                                        <div class="date-cell">
                                            <i class="fas fa-calendar"></i>
                                            <?php echo date('d/m/Y', strtotime($score['created_at'])); ?>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="co2-value <?php echo $score['co2_kg'] <= 3 ? 'excellent' : ($score['co2_kg'] <= 5 ? 'good' : ($score['co2_kg'] <= 7 ? 'fair' : 'poor')); ?>">
                                            <?php echo number_format($score['co2_kg'], 2); ?> kg
                                        </span>
                                    </td>
                                    <td>
                                        <?php
                                        $level = '';
                                        if ($score['co2_kg'] <= 3) {
                                            $level = '<span class="level-badge level-badge--excellent">Eco Héroe</span>';
                                        } elseif ($score['co2_kg'] <= 5) {
                                            $level = '<span class="level-badge level-badge--good">Eco Consciente</span>';
                                        } elseif ($score['co2_kg'] <= 7) {
                                            $level = '<span class="level-badge level-badge--fair">Eco Aprendiz</span>';
                                        } else {
                                            $level = '<span class="level-badge level-badge--poor">Eco Principiante</span>';
                                        }
                                        echo $level;
                                        ?>
                                    </td>
                                    <td>
                                        <div class="advice-cell">
                                            <?php echo substr(htmlspecialchars($score['advice']), 0, 80) . '...'; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="btn btn--ghost btn--small" onclick="viewDetails(<?php echo $score['id']; ?>)">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn--ghost btn--small" onclick="shareResult(<?php echo $score['id']; ?>)">
                                                <i class="fas fa-share"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-list"></i>
                    <h3>No hay registros</h3>
                    <p>Aún no has realizado ningún cálculo de huella ecológica</p>
                    <a href="index.php?page=habit_form" class="btn btn--primary">
                        <i class="fas fa-plus-circle"></i>
                        Realizar Primer Cálculo
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <div class="history-actions">
            <a href="index.php?page=dashboard" class="btn btn--outline">
                <i class="fas fa-tachometer-alt"></i>
                Volver al Dashboard
            </a>
            <a href="index.php?page=habit_form" class="btn btn--primary">
                <i class="fas fa-plus-circle"></i>
                Nuevo Cálculo
            </a>
            <a href="index.php?action=export" class="btn btn--secondary">
                <i class="fas fa-download"></i>
                Exportar Todo
            </a>
        </div>
    </div>
</div>

<!-- Modal de detalles -->
<div id="detailsModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Detalles del Cálculo</h3>
            <button class="modal-close" onclick="closeModal()">&times;</button>
        </div>
        <div class="modal-body" id="modalBody">
            <!-- Contenido dinámico -->
        </div>
    </div>
</div>

<script>
// Datos para el gráfico
const historyData = {
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
const historyChartConfig = {
    type: 'line',
    data: historyData,
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
        }
    }
};

// Inicializar gráfico
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('historyChart');
    if (ctx && typeof Chart !== 'undefined') {
        new Chart(ctx, historyChartConfig);
    }
});

// Funciones de filtrado y ordenación
function filterHistory() {
    const monthFilter = document.getElementById('monthFilter').value;
    const rows = document.querySelectorAll('#historyTableBody tr');

    rows.forEach(row => {
        if (!monthFilter || row.dataset.date === monthFilter) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

function sortHistory() {
    const sortOrder = document.getElementById('sortOrder').value;
    const tbody = document.getElementById('historyTableBody');
    const rows = Array.from(tbody.querySelectorAll('tr'));

    rows.sort((a, b) => {
        const dateA = new Date(a.querySelector('td:first-child').textContent.trim());
        const dateB = new Date(b.querySelector('td:first-child').textContent.trim());

        return sortOrder === 'asc' ? dateA - dateB : dateB - dateA;
    });

    tbody.innerHTML = '';
    rows.forEach(row => tbody.appendChild(row));
}

function exportHistory() {
    window.location.href = 'index.php?action=export';
}

function viewDetails(id) {
    // Aquí podrías hacer una llamada AJAX para obtener detalles completos
    const modalBody = document.getElementById('modalBody');
    modalBody.innerHTML = `
        <div class="detail-item">
            <strong>ID:</strong> ${id}
        </div>
        <div class="detail-item">
            <strong>Fecha:</strong> ${new Date().toLocaleDateString()}
        </div>
        <div class="detail-item">
            <strong>Estado:</strong> <span class="level-badge level-badge--good">Eco Consciente</span>
        </div>
        <div class="detail-item">
            <strong>Recomendación:</strong> Continúa con tus buenos hábitos ecológicos.
        </div>
    `;

    document.getElementById('detailsModal').style.display = 'block';
}

function shareResult(id) {
    if (navigator.share) {
        navigator.share({
            title: 'Mi Huella Ecológica',
            text: '¡He calculado mi huella ecológica con EcoTrack!',
            url: window.location.href
        });
    } else {
        // Fallback: copiar al portapapeles
        const dummy = document.createElement('input');
        document.body.appendChild(dummy);
        dummy.value = window.location.href;
        dummy.select();
        document.execCommand('copy');
        document.body.removeChild(dummy);

        if (window.utils && window.utils.showNotification) {
            window.utils.showNotification('Enlace copiado al portapapeles', 'success');
        }
    }
}

function closeModal() {
    document.getElementById('detailsModal').style.display = 'none';
}

// Cerrar modal al hacer clic fuera
window.onclick = function(event) {
    const modal = document.getElementById('detailsModal');
    if (event.target === modal) {
        modal.style.display = 'none';
    }
}
</script>

<style>
.history-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem;
}

.history-header {
    text-align: center;
    margin-bottom: 3rem;
}

.history-title {
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
}

.history-title i {
    color: var(--primary-color);
}

.history-subtitle {
    font-size: 1.125rem;
    color: var(--text-secondary);
}

.history-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 3rem;
}

.history-content {
    margin-bottom: 3rem;
}

.history-section {
    background: var(--bg-primary);
    border: 1px solid var(--border-color);
    border-radius: var(--radius-xl);
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: var(--shadow);
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
    gap: 1rem;
}

.section-title {
    font-size: 1.5rem;
    font-weight: 600;
    color: var(--text-primary);
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.section-title i {
    color: var(--primary-color);
}

.section-filters {
    display: flex;
    gap: 1rem;
}

.form-input--small {
    padding: 0.5rem;
    font-size: 0.875rem;
}

.chart-container {
    position: relative;
    height: 300px;
    margin-bottom: 1rem;
}

.history-table-container {
    overflow-x: auto;
}

.history-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 1rem;
}

.history-table th,
.history-table td {
    padding: 1rem;
    text-align: left;
    border-bottom: 1px solid var(--border-light);
}

.history-table th {
    font-weight: 600;
    color: var(--text-primary);
    background: var(--bg-secondary);
}

.history-table tbody tr:hover {
    background: var(--bg-secondary);
}

.date-cell {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--text-secondary);
}

.co2-value {
    font-weight: 600;
    padding: 0.25rem 0.75rem;
    border-radius: var(--radius);
    font-size: 0.875rem;
}

.co2-value.excellent { background: #d1fae5; color: #065f46; }
.co2-value.good { background: #dbeafe; color: #1e40af; }
.co2-value.fair { background: #fed7aa; color: #92400e; }
.co2-value.poor { background: #fee2e2; color: #991b1b; }

.level-badge {
    padding: 0.25rem 0.75rem;
    border-radius: var(--radius);
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
}

.level-badge--excellent { background: #d1fae5; color: #065f46; }
.level-badge--good { background: #dbeafe; color: #1e40af; }
.level-badge--fair { background: #fed7aa; color: #92400e; }
.level-badge--poor { background: #fee2e2; color: #991b1b; }

.advice-cell {
    max-width: 200px;
    font-size: 0.875rem;
    color: var(--text-secondary);
    line-height: 1.4;
}

.action-buttons {
    display: flex;
    gap: 0.5rem;
}

.history-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
}

/* Modal */
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
}

.modal-content {
    background-color: var(--bg-primary);
    margin: 10% auto;
    padding: 0;
    border-radius: var(--radius-lg);
    width: 90%;
    max-width: 500px;
    box-shadow: var(--shadow-xl);
}

.modal-header {
    padding: 1.5rem;
    border-bottom: 1px solid var(--border-color);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-header h3 {
    margin: 0;
    color: var(--text-primary);
}

.modal-close {
    background: none;
    border: none;
    font-size: 1.5rem;
    cursor: pointer;
    color: var(--text-secondary);
}

.modal-close:hover {
    color: var(--text-primary);
}

.modal-body {
    padding: 1.5rem;
}

.detail-item {
    margin-bottom: 1rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid var(--border-light);
}

.detail-item:last-child {
    border-bottom: none;
}

@media (max-width: 768px) {
    .history-container {
        padding: 1rem;
    }

    .history-title {
        font-size: 2rem;
    }

    .history-stats {
        grid-template-columns: 1fr;
    }

    .section-header {
        flex-direction: column;
        align-items: flex-start;
    }

    .section-filters {
        width: 100%;
        flex-direction: column;
    }

    .history-table-container {
        font-size: 0.875rem;
    }

    .history-table th,
    .history-table td {
        padding: 0.75rem 0.5rem;
    }

    .advice-cell {
        max-width: 150px;
    }

    .history-actions {
        flex-direction: column;
        align-items: center;
    }

    .history-actions .btn {
        width: 100%;
        max-width: 250px;
        justify-content: center;
    }
}

@media (max-width: 480px) {
    .action-buttons {
        flex-direction: column;
    }
}
</style>
