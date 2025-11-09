<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
?>
<div class="habit-form-container">
    <div class="habit-form-card">
        <div class="habit-form-header">
            <h1 class="habit-form-title">
                <i class="fas fa-leaf"></i>
                Registrar Hábitos Ecológicos
            </h1>
            <p class="habit-form-subtitle">
                Ingresa tus hábitos diarios para calcular tu huella de carbono
            </p>
        </div>

        <form class="habit-form" action="index.php?page=habit_form" method="POST" id="habitForm">
            <div class="form-section">
                <h2 class="form-section-title">
                    <i class="fas fa-route"></i>
                    Transporte
                </h2>
                <div class="form-group">
                    <label class="form-label">¿Qué medio de transporte usas principalmente?</label>
                    <div class="radio-group">
                        <div class="radio-option">
                            <input type="radio" id="transport_coche" name="transport" value="coche" required>
                            <label for="transport_coche" class="radio-label">
                                <i class="fas fa-car"></i>
                                <span>Coche</span>
                            </label>
                        </div>
                        <div class="radio-option">
                            <input type="radio" id="transport_moto" name="transport" value="moto" required>
                            <label for="transport_moto" class="radio-label">
                                <i class="fas fa-motorcycle"></i>
                                <span>Moto</span>
                            </label>
                        </div>
                        <div class="radio-option">
                            <input type="radio" id="transport_public" name="transport" value="transporte público" required>
                            <label for="transport_public" class="radio-label">
                                <i class="fas fa-bus"></i>
                                <span>Transporte Público</span>
                            </label>
                        </div>
                        <div class="radio-option">
                            <input type="radio" id="transport_bike" name="transport" value="bicicleta" required>
                            <label for="transport_bike" class="radio-label">
                                <i class="fas fa-bicycle"></i>
                                <span>Bicicleta</span>
                            </label>
                        </div>
                        <div class="radio-option">
                            <input type="radio" id="transport_walk" name="transport" value="a pie" required>
                            <label for="transport_walk" class="radio-label">
                                <i class="fas fa-walking"></i>
                                <span>A Pie</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h2 class="form-section-title">
                    <i class="fas fa-bolt"></i>
                    Consumo Energético
                </h2>
                <div class="form-group">
                    <label for="energy_use" class="form-label">
                        Consumo eléctrico mensual (kWh)
                        <span class="form-hint">Revisa tu factura de luz</span>
                    </label>
                    <div class="input-with-unit">
                        <input
                            type="number"
                            id="energy_use"
                            name="energy_use"
                            class="form-input"
                            placeholder="Ej: 300"
                            min="0"
                            step="0.01"
                            required
                        >
                        <span class="input-unit">kWh/mes</span>
                    </div>
                    <div class="energy-examples">
                        <span class="examples-label">Referencias:</span>
                        <button type="button" class="example-btn" onclick="setEnergy(150)">Bajo (150kWh)</button>
                        <button type="button" class="example-btn" onclick="setEnergy(300)">Medio (300kWh)</button>
                        <button type="button" class="example-btn" onclick="setEnergy(500)">Alto (500kWh)</button>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h2 class="form-section-title">
                    <i class="fas fa-utensils"></i>
                    Tipo de Dieta
                </h2>
                <div class="form-group">
                    <div class="diet-cards">
                        <div class="diet-card">
                            <input type="radio" id="diet_vegetariana" name="diet_type" value="vegetariana" required>
                            <label for="diet_vegetariana" class="diet-card-label">
                                <div class="diet-icon">🥗</div>
                                <div class="diet-info">
                                    <h4>Vegetariana</h4>
                                    <p>Basada en vegetales y productos lácteos</p>
                                </div>
                            </label>
                        </div>
                        <div class="diet-card">
                            <input type="radio" id="diet_mixta" name="diet_type" value="mixta" required>
                            <label for="diet_mixta" class="diet-card-label">
                                <div class="diet-icon">🍽️</div>
                                <div class="diet-info">
                                    <h4>Mixta</h4>
                                    <p>Combinación de vegetales y carne ocasional</p>
                                </div>
                            </label>
                        </div>
                        <div class="diet-card">
                            <input type="radio" id="diet_carnivora" name="diet_type" value="carnívora" required>
                            <label for="diet_carnivora" class="diet-card-label">
                                <div class="diet-icon">🥩</div>
                                <div class="diet-info">
                                    <h4>Carnívora</h4>
                                    <p>Alto consumo de carne y productos animales</p>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h2 class="form-section-title">
                    <i class="fas fa-recycle"></i>
                    Hábitos de Reciclaje
                </h2>
                <div class="form-group">
                    <div class="recycling-options">
                        <label class="checkbox-option">
                            <input type="checkbox" name="recycling" value="1" id="recycling_check">
                            <span class="checkbox-custom"></span>
                            <span class="checkbox-label">
                                <i class="fas fa-recycle"></i>
                                Reciclo regularmente papel, plástico, vidrio y orgánicos
                            </span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="form-preview">
                <h3 class="preview-title">
                    <i class="fas fa-chart-pie"></i>
                    Vista Previa del Impacto
                </h3>
                <div class="impact-preview" id="impactPreview">
                    <div class="impact-item">
                        <span class="impact-label">Transporte:</span>
                        <span class="impact-value" id="transportImpact">--</span>
                    </div>
                    <div class="impact-item">
                        <span class="impact-label">Energía:</span>
                        <span class="impact-value" id="energyImpact">--</span>
                    </div>
                    <div class="impact-item">
                        <span class="impact-label">Dieta:</span>
                        <span class="impact-value" id="dietImpact">--</span>
                    </div>
                    <div class="impact-total">
                        <span class="total-label">Total Estimado:</span>
                        <span class="total-value" id="totalImpact">-- kg CO₂/día</span>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="button" class="btn btn--outline" onclick="resetForm()">
                    <i class="fas fa-undo"></i>
                    Limpiar
                </button>
                <button type="submit" class="btn btn--primary btn--large">
                    <i class="fas fa-calculator"></i>
                    Calcular Huella Ecológica
                </button>
            </div>
        </form>
    </div>

    <div class="habit-form-info">
        <div class="info-card">
            <h3 class="info-title">
                <i class="fas fa-info-circle"></i>
                ¿Cómo funciona el cálculo?
            </h3>
            <div class="info-content">
                <p>Nuestro algoritmo calcula tu huella de carbono basándose en factores científicos:</p>
                <ul class="info-list">
                    <li>
                        <strong>Transporte:</strong>
                        Diferentes medios tienen impactos variables en emisiones de CO₂
                    </li>
                    <li>
                        <strong>Energía:</strong>
                        Se convierte tu consumo mensual a impacto diario (factor 0.233 kg CO₂/kWh)
                    </li>
                    <li>
                        <strong>Dieta:</strong>
                        La producción de alimentos tiene diferentes niveles de emisión
                    </li>
                    <li>
                        <strong>Reciclaje:</strong>
                        Reduce tu impacto en 0.5 kg CO₂ diarios
                    </li>
                </ul>
            </div>
        </div>

        <div class="info-card">
            <h3 class="info-title">
                <i class="fas fa-lightbulb"></i>
                Tips para Reducir tu Impacto
            </h3>
            <div class="tips-grid">
                <div class="tip-item">
                    <i class="fas fa-bicycle"></i>
                    <p>Usa bicicleta o transporte público para desplazamientos cortos</p>
                </div>
                <div class="tip-item">
                    <i class="fas fa-lightbulb"></i>
                    <p>Ahorra energía desconectando aparatos que no usas</p>
                </div>
                <div class="tip-item">
                    <i class="fas fa-carrot"></i>
                    <p>Incluye más días vegetarianos en tu dieta</p>
                </div>
                <div class="tip-item">
                    <i class="fas fa-recycle"></i>
                    <p>Clasifica correctamente tus residuos para reciclar</p>
                </div>
            </div>
        </div>

        <div class="info-card">
            <h3 class="info-title">
                <i class="fas fa-leaf"></i>
                Referencias Globales
            </h3>
            <div class="references">
                <div class="reference-item">
                    <span class="reference-label">Promedio español:</span>
                    <span class="reference-value">7.5 kg CO₂/día</span>
                </div>
                <div class="reference-item">
                    <span class="reference-label">Recomendado ONU:</span>
                    <span class="reference-value">4.0 kg CO₂/día</span>
                </div>
                <div class="reference-item">
                    <span class="reference-label">Objetivo sostenible:</span>
                    <span class="reference-value">2.0 kg CO₂/día</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Datos para el cálculo
const CO2_DATA = {
    transport: {
        'coche': 4.6,
        'moto': 2.0,
        'transporte público': 1.0,
        'bicicleta': 0.0,
        'a pie': 0.0
    },
    diet: {
        'vegetariana': 0.8,
        'mixta': 1.5,
        'carnívora': 3.0
    }
};

// Función para calcular impacto
function calculateImpact() {
    const transport = document.querySelector('input[name="transport"]:checked')?.value || 0;
    const energy = parseFloat(document.getElementById('energy_use').value) || 0;
    const diet = document.querySelector('input[name="diet_type"]:checked')?.value || 0;
    const recycling = document.getElementById('recycling_check').checked;

    let total = 0;
    let transportCO2 = 0;
    let energyCO2 = 0;
    let dietCO2 = 0;

    if (transport) {
        transportCO2 = CO2_DATA.transport[transport] || 0;
        total += transportCO2;
        document.getElementById('transportImpact').textContent = `${transportCO2.toFixed(1)} kg CO₂/día`;
    } else {
        document.getElementById('transportImpact').textContent = '--';
    }

    if (energy > 0) {
        energyCO2 = (energy / 30) * 0.233;
        total += energyCO2;
        document.getElementById('energyImpact').textContent = `${energyCO2.toFixed(1)} kg CO₂/día`;
    } else {
        document.getElementById('energyImpact').textContent = '--';
    }

    if (diet) {
        dietCO2 = CO2_DATA.diet[diet] || 0;
        total += dietCO2;
        document.getElementById('dietImpact').textContent = `${dietCO2.toFixed(1)} kg CO₂/día`;
    } else {
        document.getElementById('dietImpact').textContent = '--';
    }

    if (recycling) {
        total -= 0.5;
    }

    total = Math.max(total, 0);
    document.getElementById('totalImpact').textContent = `${total.toFixed(1)} kg CO₂/día`;

    // Color según el nivel
    const totalElement = document.getElementById('totalImpact');
    if (total <= 3) {
        totalElement.style.color = 'var(--success-color)';
    } else if (total <= 5) {
        totalElement.style.color = 'var(--warning-color)';
    } else {
        totalElement.style.color = 'var(--error-color)';
    }
}

// Función para establecer energía
function setEnergy(value) {
    document.getElementById('energy_use').value = value;
    calculateImpact();
}

// Función para resetear formulario
function resetForm() {
    document.getElementById('habitForm').reset();
    document.getElementById('transportImpact').textContent = '--';
    document.getElementById('energyImpact').textContent = '--';
    document.getElementById('dietImpact').textContent = '--';
    document.getElementById('totalImpact').textContent = '-- kg CO₂/día';
}

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    // Calcular cuando cambie cualquier campo
    const inputs = document.querySelectorAll('input[name="transport"], input[name="diet_type"], #energy_use, #recycling_check');
    inputs.forEach(input => {
        input.addEventListener('change', calculateImpact);
        input.addEventListener('input', calculateImpact);
    });

    // Validación del formulario
    document.getElementById('habitForm').addEventListener('submit', function(e) {
        const transport = document.querySelector('input[name="transport"]:checked');
        const energy = document.getElementById('energy_use').value;
        const diet = document.querySelector('input[name="diet_type"]:checked');

        if (!transport) {
            e.preventDefault();
            alert('Por favor, selecciona un medio de transporte');
            return;
        }

        if (!energy || energy < 0) {
            e.preventDefault();
            alert('Por favor, introduce un consumo de energía válido');
            return;
        }

        if (!diet) {
            e.preventDefault();
            alert('Por favor, selecciona un tipo de dieta');
            return;
        }

        // Mostrar indicador de carga
        const submitButton = this.querySelector('button[type="submit"]');
        const originalText = submitButton.innerHTML;
        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Calculando...';
        submitButton.disabled = true;
    });
});
</script>

<style>
.habit-form-container {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 2rem;
    margin-bottom: 2rem;
}

.habit-form-card {
    background: var(--bg-primary);
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-lg);
    border: 1px solid var(--border-color);
    overflow: hidden;
}

.habit-form-header {
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    color: white;
    padding: 2rem;
    text-align: center;
}

.habit-form-title {
    font-size: 1.75rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.habit-form-title i {
    margin-right: 0.5rem;
}

.habit-form-subtitle {
    opacity: 0.9;
    font-size: 1rem;
}

.habit-form {
    padding: 2rem;
}

.form-section {
    margin-bottom: 2rem;
}

.form-section-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.form-section-title i {
    color: var(--primary-color);
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-label {
    display: block;
    font-weight: 500;
    color: var(--text-primary);
    margin-bottom: 0.5rem;
}

.form-hint {
    font-size: 0.875rem;
    color: var(--text-secondary);
    margin-left: 0.5rem;
}

.radio-group {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 1rem;
}

.radio-option {
    position: relative;
}

.radio-option input[type="radio"] {
    position: absolute;
    opacity: 0;
}

.radio-label {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 1.5rem 1rem;
    border: 2px solid var(--border-color);
    border-radius: var(--radius-lg);
    cursor: pointer;
    transition: var(--transition);
    text-align: center;
}

.radio-label:hover {
    border-color: var(--primary-light);
    background: var(--bg-secondary);
}

.radio-option input[type="radio"]:checked + .radio-label {
    border-color: var(--primary-color);
    background: var(--primary-light);
    color: var(--primary-dark);
}

.radio-label i {
    font-size: 2rem;
    margin-bottom: 0.5rem;
    color: var(--primary-color);
}

.radio-label span {
    font-size: 0.875rem;
    font-weight: 500;
}

.input-with-unit {
    position: relative;
    display: flex;
    align-items: stretch;
}

.form-input {
    flex: 1;
    padding: 0.75rem 1rem;
    border: 2px solid var(--border-color);
    border-radius: var(--radius);
    font-size: 1rem;
    transition: var(--transition);
}

.form-input:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.1);
}

.input-unit {
    padding: 0.75rem 1rem;
    background: var(--bg-secondary);
    border: 2px solid var(--border-color);
    border-left: none;
    border-radius: 0 var(--radius) var(--radius) 0;
    font-weight: 500;
    color: var(--text-secondary);
}

.energy-examples {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-top: 0.75rem;
    flex-wrap: wrap;
}

.examples-label {
    font-size: 0.875rem;
    color: var(--text-secondary);
    font-weight: 500;
}

.example-btn {
    padding: 0.25rem 0.75rem;
    background: var(--bg-secondary);
    border: 1px solid var(--border-color);
    border-radius: var(--radius);
    font-size: 0.875rem;
    color: var(--text-primary);
    cursor: pointer;
    transition: var(--transition);
}

.example-btn:hover {
    background: var(--primary-light);
    border-color: var(--primary-color);
    color: var(--primary-dark);
}

.diet-cards {
    display: grid;
    gap: 1rem;
}

.diet-card {
    position: relative;
}

.diet-card input[type="radio"] {
    position: absolute;
    opacity: 0;
}

.diet-card-label {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.25rem;
    border: 2px solid var(--border-color);
    border-radius: var(--radius-lg);
    cursor: pointer;
    transition: var(--transition);
    background: var(--bg-primary);
}

.diet-card-label:hover {
    border-color: var(--primary-light);
    background: var(--bg-secondary);
}

.diet-card input[type="radio"]:checked + .diet-card-label {
    border-color: var(--primary-color);
    background: var(--primary-light);
}

.diet-icon {
    font-size: 2.5rem;
    text-align: center;
    min-width: 3rem;
}

.diet-info h4 {
    font-size: 1.125rem;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 0.25rem;
}

.diet-info p {
    font-size: 0.875rem;
    color: var(--text-secondary);
    line-height: 1.4;
}

.checkbox-option {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    padding: 1.25rem;
    background: var(--bg-secondary);
    border: 2px solid var(--border-color);
    border-radius: var(--radius-lg);
    cursor: pointer;
    transition: var(--transition);
}

.checkbox-option:hover {
    border-color: var(--primary-light);
    background: var(--bg-light);
}

.checkbox-option input[type="checkbox"] {
    display: none;
}

.checkbox-custom {
    width: 1.25rem;
    height: 1.25rem;
    border: 2px solid var(--border-color);
    border-radius: var(--radius-sm);
    background: var(--bg-primary);
    position: relative;
    transition: var(--transition);
    flex-shrink: 0;
    margin-top: 0.125rem;
}

.checkbox-option input[type="checkbox"]:checked + .checkbox-custom {
    background: var(--primary-color);
    border-color: var(--primary-color);
}

.checkbox-option input[type="checkbox"]:checked + .checkbox-custom::after {
    content: '✓';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: white;
    font-size: 0.75rem;
    font-weight: bold;
}

.checkbox-label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 500;
    color: var(--text-primary);
}

.form-preview {
    background: var(--bg-secondary);
    border: 2px solid var(--border-color);
    border-radius: var(--radius-lg);
    padding: 1.5rem;
    margin: 2rem 0;
}

.preview-title {
    font-size: 1.125rem;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.preview-title i {
    color: var(--primary-color);
}

.impact-preview {
    display: grid;
    gap: 0.75rem;
}

.impact-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem 0;
    border-bottom: 1px solid var(--border-light);
}

.impact-label {
    font-weight: 500;
    color: var(--text-primary);
}

.impact-value {
    font-weight: 600;
    color: var(--text-secondary);
}

.impact-total {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem 0;
    margin-top: 0.5rem;
    border-top: 2px solid var(--border-color);
    font-size: 1.125rem;
}

.total-label {
    font-weight: 600;
    color: var(--text-primary);
}

.total-value {
    font-weight: 700;
    color: var(--text-primary);
}

.form-actions {
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
    align-items: center;
}

.habit-form-info {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.info-card {
    background: var(--bg-primary);
    border: 1px solid var(--border-color);
    border-radius: var(--radius-lg);
    padding: 1.5rem;
    box-shadow: var(--shadow);
}

.info-title {
    font-size: 1.125rem;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.info-title i {
    color: var(--primary-color);
}

.info-content p {
    color: var(--text-secondary);
    line-height: 1.6;
    margin-bottom: 1rem;
}

.info-list {
    list-style: none;
}

.info-list li {
    margin-bottom: 0.75rem;
    color: var(--text-secondary);
    line-height: 1.5;
}

.info-list li strong {
    color: var(--text-primary);
}

.tips-grid {
    display: grid;
    gap: 1rem;
}

.tip-item {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    padding: 0.75rem;
    background: var(--bg-secondary);
    border-radius: var(--radius);
}

.tip-item i {
    color: var(--primary-color);
    margin-top: 0.125rem;
}

.tip-item p {
    font-size: 0.875rem;
    color: var(--text-secondary);
    line-height: 1.4;
}

.references {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.reference-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem 0;
    border-bottom: 1px solid var(--border-light);
}

.reference-label {
    font-size: 0.875rem;
    color: var(--text-secondary);
}

.reference-value {
    font-weight: 600;
    color: var(--text-primary);
}

@media (max-width: 1024px) {
    .habit-form-container {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .radio-group {
        grid-template-columns: repeat(2, 1fr);
    }

    .diet-cards {
        gap: 0.75rem;
    }

    .diet-card-label {
        padding: 1rem;
        gap: 0.75rem;
    }

    .diet-icon {
        font-size: 2rem;
        min-width: 2.5rem;
    }

    .energy-examples {
        flex-direction: column;
        align-items: flex-start;
    }

    .form-actions {
        flex-direction: column;
    }

    .form-actions .btn {
        width: 100%;
        justify-content: center;
    }
}
</style>
<?php } ?>
