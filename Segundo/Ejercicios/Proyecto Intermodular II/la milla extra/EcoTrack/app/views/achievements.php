<?php
$page_title = 'Logros';
?>

<div class="achievements-container">
    <div class="achievements-header">
        <h1 class="achievements-title">
            <i class="fas fa-trophy"></i>
            Mis Logros Ecológicos
        </h1>
        <p class="achievements-subtitle">Celebrando tus avances hacia la sostenibilidad</p>
    </div>

    <div class="achievements-stats">
        <div class="stat-card stat-card--primary">
            <div class="stat-card__icon">
                <i class="fas fa-medal"></i>
            </div>
            <div class="stat-card__content">
                <div class="stat-card__value"><?php echo count($achievements); ?></div>
                <div class="stat-card__label">Logros Desbloqueados</div>
            </div>
        </div>

        <div class="stat-card stat-card--success">
            <div class="stat-card__icon">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="stat-card__content">
                <div class="stat-card__value"><?php echo $stats['total_calculations'] ?? 0; ?></div>
                <div class="stat-card__label">Cálculos Totales</div>
            </div>
        </div>

        <div class="stat-card stat-card--warning">
            <div class="stat-card__icon">
                <i class="fas fa-leaf"></i>
            </div>
            <div class="stat-card__content">
                <div class="stat-card__value"><?php echo round($stats['avg_co2'] ?? 0, 2); ?></div>
                <div class="stat-card__label">kg CO₂/día (Promedio)</div>
            </div>
        </div>
    </div>

    <div class="achievements-content">
        <div class="achievements-section">
            <h2 class="section-title">
                <i class="fas fa-unlock"></i>
                Logros Desbloqueados
            </h2>

            <?php if (!empty($achievements)): ?>
                <div class="achievements-grid">
                    <?php foreach ($achievements as $achievement): ?>
                        <div class="achievement-card achievement-card--unlocked">
                            <div class="achievement-card__icon" style="background: <?php echo isset($achievement['badge_color']) ? $achievement['badge_color'] : '#22c55e'; ?>">
                                <i class="<?php echo htmlspecialchars($achievement['icon']); ?>"></i>
                            </div>
                            <div class="achievement-card__content">
                                <h3 class="achievement-card__title"><?php echo htmlspecialchars($achievement['name']); ?></h3>
                                <p class="achievement-card__description"><?php echo htmlspecialchars($achievement['description']); ?></p>
                                <div class="achievement-card__date">
                                    <i class="fas fa-calendar"></i>
                                    Desbloqueado el <?php echo date('d/m/Y', strtotime($achievement['unlocked_at'])); ?>
                                </div>
                            </div>
                            <div class="achievement-card__badge">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-lock"></i>
                    <h3>Aún no tienes logros</h3>
                    <p>Comienza a registrar tus hábitos ecológicos para desbloquear logros</p>
                    <a href="index.php?page=habit_form" class="btn btn--primary">
                        <i class="fas fa-plus-circle"></i>
                        Primer Cálculo
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <div class="achievements-section">
            <h2 class="section-title">
                <i class="fas fa-lock"></i>
                Logros Pendientes
            </h2>

            <div class="achievements-grid">
                <div class="achievement-card achievement-card--locked">
                    <div class="achievement-card__icon">
                        <i class="fas fa-question"></i>
                    </div>
                    <div class="achievement-card__content">
                        <h3 class="achievement-card__title">Explorador Ecológico</h3>
                        <p class="achievement-card__description">Prueba todos los tipos de transporte</p>
                        <div class="achievement-card__progress">
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: 40%; background-color: var(--info-color);"></div>
                            </div>
                            <span class="progress-text">2/5 completado</span>
                        </div>
                    </div>
                    <div class="achievement-card__badge">
                        <i class="fas fa-lock"></i>
                    </div>
                </div>

                <div class="achievement-card achievement-card--locked">
                    <div class="achievement-card__icon">
                        <i class="fas fa-question"></i>
                    </div>
                    <div class="achievement-card__content">
                        <h3 class="achievement-card__title">Maestro de la Conservación</h3>
                        <p class="achievement-card__description">Mantén tu huella por debajo de 2 kg durante un mes</p>
                        <div class="achievement-card__progress">
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: 25%; background-color: var(--warning-color);"></div>
                            </div>
                            <span class="progress-text">7/30 días</span>
                        </div>
                    </div>
                    <div class="achievement-card__badge">
                        <i class="fas fa-lock"></i>
                    </div>
                </div>

                <div class="achievement-card achievement-card--locked">
                    <div class="achievement-card__icon">
                        <i class="fas fa-question"></i>
                    </div>
                    <div class="achievement-card__content">
                        <h3 class="achievement-card__title">Líder Comunitario</h3>
                        <p class="achievement-card__description">Comparte tus consejos con 5 amigos</p>
                        <div class="achievement-card__progress">
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: 0%; background-color: var(--error-color);"></div>
                            </div>
                            <span class="progress-text">0/5 amigos</span>
                        </div>
                    </div>
                    <div class="achievement-card__badge">
                        <i class="fas fa-lock"></i>
                    </div>
                </div>

                <div class="achievement-card achievement-card--locked">
                    <div class="achievement-card__icon">
                        <i class="fas fa-question"></i>
                    </div>
                    <div class="achievement-card__content">
                        <h3 class="achievement-card__title">Científico del Clima</h3>
                        <p class="achievement-card__description">Analiza 100 conjuntos de datos diferentes</p>
                        <div class="achievement-card__progress">
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: <?php echo min(100, ($stats['total_calculations'] ?? 0)); ?>%; background-color: var(--primary-color);"></div>
                            </div>
                            <span class="progress-text"><?php echo $stats['total_calculations'] ?? 0; ?>/100 análisis</span>
                        </div>
                    </div>
                    <div class="achievement-card__badge">
                        <i class="fas fa-lock"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="achievements-actions">
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
                Exportar Datos
            </a>
        </div>
    </div>
</div>

<style>
.achievements-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem;
}

.achievements-header {
    text-align: center;
    margin-bottom: 3rem;
}

.achievements-title {
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
}

.achievements-title i {
    color: var(--warning-color);
}

.achievements-subtitle {
    font-size: 1.125rem;
    color: var(--text-secondary);
}

.achievements-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 3rem;
}

.stat-card {
    background: var(--bg-primary);
    border: 1px solid var(--border-color);
    border-radius: var(--radius-lg);
    padding: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    box-shadow: var(--shadow);
    transition: var(--transition);
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
}

.stat-card__icon {
    width: 3rem;
    height: 3rem;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.25rem;
}

.stat-card--primary .stat-card__icon { background: var(--primary-color); }
.stat-card--success .stat-card__icon { background: var(--success-color); }
.stat-card--warning .stat-card__icon { background: var(--warning-color); }

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

.achievements-content {
    margin-bottom: 3rem;
}

.achievements-section {
    margin-bottom: 3rem;
}

.section-title {
    font-size: 1.5rem;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.section-title i {
    color: var(--primary-color);
}

.achievements-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
}

.achievement-card {
    background: var(--bg-primary);
    border: 2px solid var(--border-color);
    border-radius: var(--radius-lg);
    padding: 1.5rem;
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    position: relative;
    transition: var(--transition);
}

.achievement-card--unlocked {
    border-color: var(--success-color);
    box-shadow: var(--shadow);
}

.achievement-card--unlocked:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
}

.achievement-card--locked {
    opacity: 0.7;
    border-color: var(--border-light);
}

.achievement-card__icon {
    width: 3rem;
    height: 3rem;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.25rem;
    flex-shrink: 0;
}

.achievement-card--unlocked .achievement-card__icon {
    background: linear-gradient(135deg, var(--success-color), #059669);
}

.achievement-card--locked .achievement-card__icon {
    background: var(--bg-light);
    color: var(--text-light);
}

.achievement-card__content {
    flex: 1;
}

.achievement-card__title {
    font-size: 1.125rem;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 0.5rem;
}

.achievement-card__description {
    font-size: 0.875rem;
    color: var(--text-secondary);
    line-height: 1.5;
    margin-bottom: 1rem;
}

.achievement-card__date {
    font-size: 0.75rem;
    color: var(--text-light);
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.achievement-card__progress {
    margin-top: 0.75rem;
}

.progress-bar {
    height: 0.25rem;
    background: var(--bg-light);
    border-radius: var(--radius-sm);
    overflow: hidden;
    margin-bottom: 0.5rem;
}

.progress-fill {
    height: 100%;
    border-radius: var(--radius-sm);
    transition: width 0.3s ease;
}

.progress-text {
    font-size: 0.75rem;
    color: var(--text-secondary);
    font-weight: 500;
}

.achievement-card__badge {
    position: absolute;
    top: 1rem;
    right: 1rem;
}

.achievement-card--unlocked .achievement-card__badge {
    color: var(--success-color);
    font-size: 1.25rem;
}

.achievement-card--locked .achievement-card__badge {
    color: var(--text-light);
    font-size: 1rem;
}

.empty-state {
    text-align: center;
    padding: 3rem 2rem;
    background: var(--bg-secondary);
    border-radius: var(--radius-lg);
    border: 2px dashed var(--border-color);
}

.empty-state i {
    font-size: 4rem;
    color: var(--text-light);
    margin-bottom: 1.5rem;
}

.empty-state h3 {
    font-size: 1.5rem;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 0.75rem;
}

.empty-state p {
    font-size: 1rem;
    color: var(--text-secondary);
    margin-bottom: 2rem;
}

.achievements-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
}

@media (max-width: 768px) {
    .achievements-container {
        padding: 1rem;
    }

    .achievements-title {
        font-size: 2rem;
    }

    .achievements-stats {
        grid-template-columns: 1fr;
    }

    .achievements-grid {
        grid-template-columns: 1fr;
    }

    .achievements-actions {
        flex-direction: column;
        align-items: center;
    }

    .achievements-actions .btn {
        width: 100%;
        max-width: 250px;
        justify-content: center;
    }
}

@media (max-width: 480px) {
    .achievement-card {
        padding: 1rem;
    }

    .achievement-card__icon {
        width: 2.5rem;
        height: 2.5rem;
        font-size: 1rem;
    }

    .achievement-card__title {
        font-size: 1rem;
    }

    .achievements-title {
        font-size: 1.75rem;
    }
}
</style>
