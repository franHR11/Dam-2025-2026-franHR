/**
 * UI Library - Componentes Reutilizables
 * Librería de componentes de interfaz de usuario con HTML, CSS y JavaScript vanilla
 */

// Clase base para todos los componentes
class BaseComponent {
    constructor(options = {}) {
        this.container = options.container;
        this.element = null;
        this.options = { ...this.getDefaultOptions(), ...options };
        this.events = {};

        if (this.container) {
            this.init();
        }
    }

    getDefaultOptions() {
        return {
            theme: 'default',
            responsive: true,
            animate: true
        };
    }

    init() {
        this.createElement();
        this.bindEvents();
        this.render();
    }

    createElement() {
        const container = document.querySelector(this.container);
        if (!container) {
            throw new Error(`Container ${this.container} not found`);
        }

        this.element = document.createElement('div');
        this.element.className = `ui-component ui-${this.constructor.name.toLowerCase()}`;
        container.appendChild(this.element);
    }

    bindEvents() {
        // Override en clases hijas
    }

    render() {
        // Override en clases hijas
    }

    on(event, callback) {
        if (!this.events[event]) {
            this.events[event] = [];
        }
        this.events[event].push(callback);
    }

    emit(event, data) {
        if (this.events[event]) {
            this.events[event].forEach(callback => callback(data));
        }
    }

    destroy() {
        if (this.element && this.element.parentNode) {
            this.element.parentNode.removeChild(this.element);
        }
    }

    showLoading() {
        this.emit('loading', true);
    }

    hideLoading() {
        this.emit('loading', false);
    }
}

// Componente de Tabla
class TableComponent extends BaseComponent {
    getDefaultOptions() {
        return {
            ...super.getDefaultOptions(),
            data: [],
            columns: [],
            pagination: true,
            pageSize: 10,
            sortable: true,
            searchable: true,
            selectable: false,
            exportable: true
        };
    }

    render() {
        const { data, columns, pagination, searchable, selectable, exportable } = this.options;

        let html = '<div class="ui-table-container">';

        // Header con búsqueda y exportación
        if (searchable || exportable) {
            html += '<div class="ui-table-header">';
            html += '<div class="ui-table-controls">';

            if (searchable) {
                html += '<input type="text" class="ui-input ui-search-input" placeholder="Buscar...">';
            }

            if (exportable) {
                html += '<button class="ui-btn ui-btn-secondary ui-export-btn">Exportar CSV</button>';
            }

            html += '</div></div>';
        }

        // Tabla
        html += '<div class="ui-table-wrapper"><table class="ui-table">';

        // Encabezado
        html += '<thead><tr>';
        if (selectable) {
            html += '<th><input type="checkbox" class="ui-select-all"></th>';
        }
        columns.forEach(col => {
            const sortable = this.options.sortable ? ' sortable' : '';
            html += `<th class="${sortable}" data-key="${col.key}">${col.label}</th>`;
        });
        html += '</tr></thead>';

        // Cuerpo
        html += '<tbody>';
        const paginatedData = this.getPaginatedData();
        if (paginatedData.length === 0) {
            const colspan = columns.length + (selectable ? 1 : 0);
            html += `<tr><td colspan="${colspan}" class="ui-no-data">No hay datos disponibles</td></tr>`;
        } else {
            paginatedData.forEach(row => {
                html += '<tr>';
                if (selectable) {
                    html += '<td><input type="checkbox" class="ui-row-select" data-id="' + row.id + '"></td>';
                }
                columns.forEach(col => {
                    const value = row[col.key] || '';
                    html += `<td>${this.formatCellValue(value, col)}</td>`;
                });
                html += '</tr>';
            });
        }
        html += '</tbody></table></div>';

        // Paginación
        if (pagination && data.length > this.options.pageSize) {
            html += this.renderPagination();
        }

        html += '</div>';

        this.element.innerHTML = html;
        this.bindTableEvents();
    }

    formatCellValue(value, column) {
        if (column.formatter && typeof column.formatter === 'function') {
            return column.formatter(value);
        }

        if (value === null || value === undefined) {
            return '-';
        }

        return String(value);
    }

    getPaginatedData() {
        if (!this.options.pagination) {
            return this.filteredData || this.options.data;
        }

        const data = this.filteredData || this.options.data;
        const currentPage = this.currentPage || 1;
        const pageSize = this.options.pageSize;

        const start = (currentPage - 1) * pageSize;
        const end = start + pageSize;

        return data.slice(start, end);
    }

    renderPagination() {
        const data = this.filteredData || this.options.data;
        const totalPages = Math.ceil(data.length / this.options.pageSize);
        const currentPage = this.currentPage || 1;

        let html = '<div class="ui-pagination">';
        html += `<span class="ui-pagination-info">Mostrando ${((currentPage - 1) * this.options.pageSize) + 1}-${Math.min(currentPage * this.options.pageSize, data.length)} de ${data.length} resultados</span>`;

        html += '<div class="ui-pagination-controls">';

        // Anterior
        html += `<button class="ui-btn ui-btn-outline ui-pagination-prev" ${currentPage === 1 ? 'disabled' : ''}>Anterior</button>`;

        // Páginas
        for (let i = 1; i <= totalPages; i++) {
            if (i === 1 || i === totalPages || (i >= currentPage - 1 && i <= currentPage + 1)) {
                html += `<button class="ui-btn ${i === currentPage ? 'ui-btn-primary' : 'ui-btn-outline'} ui-pagination-page" data-page="${i}">${i}</button>`;
            } else if (i === currentPage - 2 || i === currentPage + 2) {
                html += '<span>...</span>';
            }
        }

        // Siguiente
        html += `<button class="ui-btn ui-btn-outline ui-pagination-next" ${currentPage === totalPages ? 'disabled' : ''}>Siguiente</button>`;

        html += '</div></div>';

        return html;
    }

    bindTableEvents() {
        const table = this.element.querySelector('.ui-table');

        // Búsqueda
        const searchInput = this.element.querySelector('.ui-search-input');
        if (searchInput) {
            searchInput.addEventListener('input', (e) => {
                this.filterData(e.target.value);
            });
        }

        // Ordenamiento
        if (this.options.sortable) {
            table.querySelectorAll('th.sortable').forEach(th => {
                th.addEventListener('click', () => {
                    this.sortData(th.dataset.key);
                });
            });
        }

        // Paginación
        this.element.querySelectorAll('.ui-pagination-page').forEach(btn => {
            btn.addEventListener('click', () => {
                this.goToPage(parseInt(btn.dataset.page));
            });
        });

        this.element.querySelector('.ui-pagination-prev')?.addEventListener('click', () => {
            this.previousPage();
        });

        this.element.querySelector('.ui-pagination-next')?.addEventListener('click', () => {
            this.nextPage();
        });

        // Exportación
        this.element.querySelector('.ui-export-btn')?.addEventListener('click', () => {
            this.exportToCSV();
        });

        // Selección
        if (this.options.selectable) {
            const selectAll = this.element.querySelector('.ui-select-all');
            if (selectAll) {
                selectAll.addEventListener('change', (e) => {
                    this.toggleSelectAll(e.target.checked);
                });
            }

            this.element.querySelectorAll('.ui-row-select').forEach(checkbox => {
                checkbox.addEventListener('change', () => {
                    this.updateSelectedRows();
                });
            });
        }
    }

    filterData(searchTerm) {
        if (!searchTerm) {
            this.filteredData = null;
        } else {
            const term = searchTerm.toLowerCase();
            this.filteredData = this.options.data.filter(row => {
                return this.options.columns.some(col => {
                    const value = row[col.key];
                    return value && String(value).toLowerCase().includes(term);
                });
            });
        }
        this.currentPage = 1;
        this.render();
    }

    sortData(key) {
        const data = this.filteredData || this.options.data;
        const column = this.options.columns.find(col => col.key === key);

        if (this.sortKey === key) {
            this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            this.sortKey = key;
            this.sortDirection = 'asc';
        }

        data.sort((a, b) => {
            let aVal = a[key];
            let bVal = b[key];

            if (column.type === 'number') {
                aVal = parseFloat(aVal) || 0;
                bVal = parseFloat(bVal) || 0;
            } else {
                aVal = String(aVal || '').toLowerCase();
                bVal = String(bVal || '').toLowerCase();
            }

            if (this.sortDirection === 'asc') {
                return aVal > bVal ? 1 : aVal < bVal ? -1 : 0;
            } else {
                return aVal < bVal ? 1 : aVal > bVal ? -1 : 0;
            }
        });

        this.render();
    }

    goToPage(page) {
        this.currentPage = page;
        this.render();
    }

    nextPage() {
        const data = this.filteredData || this.options.data;
        const totalPages = Math.ceil(data.length / this.options.pageSize);
        if (this.currentPage < totalPages) {
            this.goToPage(this.currentPage + 1);
        }
    }

    previousPage() {
        if (this.currentPage > 1) {
            this.goToPage(this.currentPage - 1);
        }
    }

    exportToCSV() {
        const data = this.filteredData || this.options.data;
        if (data.length === 0) return;

        const headers = this.options.columns.map(col => col.label).join(',');
        const rows = data.map(row => {
            return this.options.columns.map(col => {
                let value = row[col.key] || '';
                value = String(value).replace(/"/g, '""');
                if (value.includes(',') || value.includes('"')) {
                    value = `"${value}"`;
                }
                return value;
            }).join(',');
        });

        const csv = [headers, ...rows].join('\n');
        const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        const url = URL.createObjectURL(blob);

        link.setAttribute('href', url);
        link.setAttribute('download', `table_export_${Date.now()}.csv`);
        link.style.visibility = 'hidden';

        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    toggleSelectAll(checked) {
        this.element.querySelectorAll('.ui-row-select').forEach(checkbox => {
            checkbox.checked = checked;
        });
        this.updateSelectedRows();
    }

    updateSelectedRows() {
        const selected = [];
        this.element.querySelectorAll('.ui-row-select:checked').forEach(checkbox => {
            const id = checkbox.dataset.id;
            const row = this.options.data.find(item => item.id == id);
            if (row) selected.push(row);
        });
        this.emit('selection', selected);
    }

    updateData(newData) {
        this.options.data = newData;
        this.filteredData = null;
        this.currentPage = 1;
        this.render();
    }
}

// Componente de Gráficas
class ChartComponent extends BaseComponent {
    getDefaultOptions() {
        return {
            ...super.getDefaultOptions(),
            type: 'line', // line, bar, pie
            data: {
                labels: [],
                datasets: []
            },
            options: {
                responsive: true,
                title: '',
                legend: true,
                colors: ['#3498db', '#e74c3c', '#2ecc71', '#f39c12', '#9b59b6', '#1abc9c']
            }
        };
    }

    render() {
        const { type, data, options } = this.options;

        let html = `<div class="ui-chart-container">`;

        if (options.title) {
            html += `<h3 class="ui-chart-title">${options.title}</h3>`;
        }

        html += `<div class="ui-chart-wrapper">`;
        html += `<canvas id="${this.element.id || 'chart_' + Math.random().toString(36).substr(2, 9)}" class="ui-chart"></canvas>`;
        html += `</div>`;

        if (options.legend && type !== 'pie') {
            html += '<div class="ui-chart-legend"></div>';
        }

        html += '</div>';

        this.element.innerHTML = html;

        // Esperar a que el DOM se actualice para dibujar el canvas
        setTimeout(() => {
            this.drawChart();
        }, 0);
    }

    drawChart() {
        const canvas = this.element.querySelector('.ui-chart');
        if (!canvas) return;

        const ctx = canvas.getContext('2d');
        const { type, data, options } = this.options;

        // Set canvas size
        const rect = canvas.getBoundingClientRect();
        canvas.width = rect.width;
        canvas.height = rect.height;

        switch (type) {
            case 'line':
                this.drawLineChart(ctx, data, options);
                break;
            case 'bar':
                this.drawBarChart(ctx, data, options);
                break;
            case 'pie':
                this.drawPieChart(ctx, data, options);
                break;
        }
    }

    drawLineChart(ctx, data, options) {
        const width = ctx.canvas.width;
        const height = ctx.canvas.height;
        const padding = 40;

        ctx.clearRect(0, 0, width, height);

        if (!data.labels.length || !data.datasets.length) return;

        const chartWidth = width - padding * 2;
        const chartHeight = height - padding * 2;

        // Find max value
        let maxValue = 0;
        data.datasets.forEach(dataset => {
            dataset.data.forEach(value => {
                maxValue = Math.max(maxValue, parseFloat(value) || 0);
            });
        });

        // Draw axes
        ctx.strokeStyle = '#ddd';
        ctx.lineWidth = 1;
        ctx.beginPath();
        ctx.moveTo(padding, padding);
        ctx.lineTo(padding, height - padding);
        ctx.lineTo(width - padding, height - padding);
        ctx.stroke();

        // Draw grid lines
        ctx.strokeStyle = '#f0f0f0';
        for (let i = 0; i <= 5; i++) {
            const y = padding + (chartHeight / 5) * i;
            ctx.beginPath();
            ctx.moveTo(padding, y);
            ctx.lineTo(width - padding, y);
            ctx.stroke();

            // Y-axis labels
            ctx.fillStyle = '#666';
            ctx.font = '12px sans-serif';
            ctx.textAlign = 'right';
            const value = Math.round(maxValue - (maxValue / 5) * i);
            ctx.fillText(value, padding - 10, y + 4);
        }

        // Draw lines
        const xStep = chartWidth / (data.labels.length - 1);

        data.datasets.forEach((dataset, datasetIndex) => {
            ctx.strokeStyle = options.colors[datasetIndex % options.colors.length];
            ctx.lineWidth = 2;
            ctx.beginPath();

            dataset.data.forEach((value, index) => {
                const x = padding + xStep * index;
                const y = height - padding - (parseFloat(value) || 0) / maxValue * chartHeight;

                if (index === 0) {
                    ctx.moveTo(x, y);
                } else {
                    ctx.lineTo(x, y);
                }
            });

            ctx.stroke();

            // Draw points
            dataset.data.forEach((value, index) => {
                const x = padding + xStep * index;
                const y = height - padding - (parseFloat(value) || 0) / maxValue * chartHeight;

                ctx.fillStyle = options.colors[datasetIndex % options.colors.length];
                ctx.beginPath();
                ctx.arc(x, y, 4, 0, Math.PI * 2);
                ctx.fill();
            });
        });

        // X-axis labels
        ctx.fillStyle = '#666';
        ctx.font = '12px sans-serif';
        ctx.textAlign = 'center';
        data.labels.forEach((label, index) => {
            const x = padding + xStep * index;
            ctx.fillText(label, x, height - padding + 20);
        });
    }

    drawBarChart(ctx, data, options) {
        const width = ctx.canvas.width;
        const height = ctx.canvas.height;
        const padding = 40;

        ctx.clearRect(0, 0, width, height);

        if (!data.labels.length || !data.datasets.length) return;

        const chartWidth = width - padding * 2;
        const chartHeight = height - padding * 2;

        // Find max value
        let maxValue = 0;
        data.datasets.forEach(dataset => {
            dataset.data.forEach(value => {
                maxValue = Math.max(maxValue, parseFloat(value) || 0);
            });
        });

        // Draw axes
        ctx.strokeStyle = '#ddd';
        ctx.lineWidth = 1;
        ctx.beginPath();
        ctx.moveTo(padding, padding);
        ctx.lineTo(padding, height - padding);
        ctx.lineTo(width - padding, height - padding);
        ctx.stroke();

        // Draw grid lines
        ctx.strokeStyle = '#f0f0f0';
        for (let i = 0; i <= 5; i++) {
            const y = padding + (chartHeight / 5) * i;
            ctx.beginPath();
            ctx.moveTo(padding, y);
            ctx.lineTo(width - padding, y);
            ctx.stroke();

            // Y-axis labels
            ctx.fillStyle = '#666';
            ctx.font = '12px sans-serif';
            ctx.textAlign = 'right';
            const value = Math.round(maxValue - (maxValue / 5) * i);
            ctx.fillText(value, padding - 10, y + 4);
        }

        // Draw bars
        const barGroupWidth = chartWidth / data.labels.length;
        const barWidth = barGroupWidth / data.datasets.length * 0.8;
        const barSpacing = barGroupWidth / data.datasets.length * 0.2;

        data.datasets.forEach((dataset, datasetIndex) => {
            ctx.fillStyle = options.colors[datasetIndex % options.colors.length];

            dataset.data.forEach((value, index) => {
                const x = padding + barGroupWidth * index + barSpacing / 2 + (barWidth + barSpacing) * datasetIndex;
                const barHeight = (parseFloat(value) || 0) / maxValue * chartHeight;
                const y = height - padding - barHeight;

                ctx.fillRect(x, y, barWidth, barHeight);
            });
        });

        // X-axis labels
        ctx.fillStyle = '#666';
        ctx.font = '12px sans-serif';
        ctx.textAlign = 'center';
        data.labels.forEach((label, index) => {
            const x = padding + barGroupWidth * index + barGroupWidth / 2;
            ctx.fillText(label, x, height - padding + 20);
        });
    }

    drawPieChart(ctx, data, options) {
        const width = ctx.canvas.width;
        const height = ctx.canvas.height;
        const centerX = width / 2;
        const centerY = height / 2;
        const radius = Math.min(width, height) / 2 - 40;

        ctx.clearRect(0, 0, width, height);

        if (!data.datasets.length || !data.datasets[0].data.length) return;

        const dataset = data.datasets[0];
        const total = dataset.data.reduce((sum, value) => sum + (parseFloat(value) || 0), 0);

        if (total === 0) return;

        let currentAngle = -Math.PI / 2;

        dataset.data.forEach((value, index) => {
            const sliceAngle = (parseFloat(value) || 0) / total * Math.PI * 2;

            // Draw slice
            ctx.fillStyle = options.colors[index % options.colors.length];
            ctx.beginPath();
            ctx.moveTo(centerX, centerY);
            ctx.arc(centerX, centerY, radius, currentAngle, currentAngle + sliceAngle);
            ctx.closePath();
            ctx.fill();

            // Draw label
            const labelAngle = currentAngle + sliceAngle / 2;
            const labelX = centerX + Math.cos(labelAngle) * (radius * 0.7);
            const labelY = centerY + Math.sin(labelAngle) * (radius * 0.7);

            ctx.fillStyle = 'white';
            ctx.font = 'bold 14px sans-serif';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            const percentage = Math.round(((parseFloat(value) || 0) / total) * 100);
            ctx.fillText(`${percentage}%`, labelX, labelY);

            currentAngle += sliceAngle;
        });

        // Draw legend
        const legendX = 20;
        let legendY = 20;

        ctx.font = '12px sans-serif';
        ctx.textAlign = 'left';

        dataset.data.forEach((value, index) => {
            // Color box
            ctx.fillStyle = options.colors[index % options.colors.length];
            ctx.fillRect(legendX, legendY, 12, 12);

            // Label
            ctx.fillStyle = '#333';
            const label = data.labels[index] || `Item ${index + 1}`;
            const percentage = Math.round(((parseFloat(value) || 0) / total) * 100);
            ctx.fillText(`${label}: ${percentage}%`, legendX + 20, legendY + 10);

            legendY += 20;
        });
    }

    updateData(newData) {
        this.options.data = newData;
        this.render();
    }

    resize() {
        this.drawChart();
    }
}

// Componente de Formularios
class FormComponent extends BaseComponent {
    getDefaultOptions() {
        return {
            ...super.getDefaultOptions(),
            fields: [],
            onSubmit: null,
            submitText: 'Enviar',
            resetText: 'Limpiar',
            showReset: true,
            validateOnChange: true,
            showErrors: true
        };
    }

    render() {
        const { fields, submitText, resetText, showReset } = this.options;

        let html = `<form class="ui-form" novalidate>`;

        fields.forEach((field, index) => {
            html += this.renderField(field, index);
        });

        html += '<div class="ui-form-actions">';
        html += `<button type="submit" class="ui-btn ui-btn-primary ui-submit-btn">${submitText}</button>`;

        if (showReset) {
            html += `<button type="reset" class="ui-btn ui-btn-outline ui-reset-btn">${resetText}</button>`;
        }

        html += '</div></form>';

        this.element.innerHTML = html;
        this.bindFormEvents();
    }

    renderField(field, index) {
        const fieldId = `field_${index}`;
        let html = '<div class="ui-form-field">';

        // Label
        const required = field.required ? ' required' : '';
        html += `<label for="${fieldId}" class="ui-label${required}">${field.label}</label>`;

        // Input container
        html += '<div class="ui-input-container">';

        switch (field.type) {
            case 'select':
                html += this.renderSelect(field, fieldId);
                break;
            case 'textarea':
                html += this.renderTextarea(field, fieldId);
                break;
            case 'checkbox':
                html += this.renderCheckbox(field, fieldId);
                break;
            case 'radio':
                html += this.renderRadio(field, fieldId);
                break;
            case 'date':
            case 'email':
            case 'password':
            case 'number':
            case 'tel':
            case 'text':
            default:
                html += this.renderInput(field, fieldId);
        }

        html += '</div>';

        // Error message container
        if (this.options.showErrors) {
            html += `<div class="ui-error" id="${fieldId}_error"></div>`;
        }

        html += '</div>';

        return html;
    }

    renderInput(field, fieldId) {
        const attributes = [];

        if (field.placeholder) attributes.push(`placeholder="${field.placeholder}"`);
        if (field.required) attributes.push('required');
        if (field.min) attributes.push(`min="${field.min}"`);
        if (field.max) attributes.push(`max="${field.max}"`);
        if (field.minLength) attributes.push(`minlength="${field.minLength}"`);
        if (field.maxLength) attributes.push(`maxlength="${field.maxLength}"`);
        if (field.pattern) attributes.push(`pattern="${field.pattern}"`);
        if (field.disabled) attributes.push('disabled');
        if (field.readonly) attributes.push('readonly');

        return `<input type="${field.type}" id="${fieldId}" name="${field.name}" class="ui-input" ${attributes.join(' ')}>`;
    }

    renderSelect(field, fieldId) {
        const attributes = [];
        if (field.required) attributes.push('required');
        if (field.disabled) attributes.push('disabled');

        let html = `<select id="${fieldId}" name="${field.name}" class="ui-select" ${attributes.join(' ')}>`;

        if (field.placeholder) {
            html += `<option value="">${field.placeholder}</option>`;
        }

        if (field.options) {
            field.options.forEach(option => {
                const value = typeof option === 'string' ? option : option.value;
                const label = typeof option === 'string' ? option : option.label;
                const selected = option.selected ? 'selected' : '';
                html += `<option value="${value}" ${selected}>${label}</option>`;
            });
        }

        html += '</select>';
        return html;
    }

    renderTextarea(field, fieldId) {
        const attributes = [];

        if (field.placeholder) attributes.push(`placeholder="${field.placeholder}"`);
        if (field.required) attributes.push('required');
        if (field.minLength) attributes.push(`minlength="${field.minLength}"`);
        if (field.maxLength) attributes.push(`maxlength="${field.maxLength}"`);
        if (field.rows) attributes.push(`rows="${field.rows}"`);
        if (field.disabled) attributes.push('disabled');
        if (field.readonly) attributes.push('readonly');

        return `<textarea id="${fieldId}" name="${field.name}" class="ui-textarea" ${attributes.join(' ')}></textarea>`;
    }

    renderCheckbox(field, fieldId) {
        const attributes = [];
        if (field.required) attributes.push('required');
        if (field.disabled) attributes.push('disabled');
        if (field.checked) attributes.push('checked');

        return `<label class="ui-checkbox-label">
            <input type="checkbox" id="${fieldId}" name="${field.name}" class="ui-checkbox" ${attributes.join(' ')}>
            <span class="ui-checkbox-text">${field.text || field.label}</span>
        </label>`;
    }

    renderRadio(field, fieldId) {
        let html = '<div class="ui-radio-group">';

        if (field.options) {
            field.options.forEach((option, index) => {
                const optionId = `${fieldId}_${index}`;
                const value = typeof option === 'string' ? option : option.value;
                const label = typeof option === 'string' ? option : option.label;
                const checked = option.selected || option.checked ? 'checked' : '';
                const required = index === 0 && field.required ? 'required' : '';

                html += `<label class="ui-radio-label">
                    <input type="radio" id="${optionId}" name="${field.name}" value="${value}" class="ui-radio" ${checked} ${required}>
                    <span class="ui-radio-text">${label}</span>
                </label>`;
            });
        }

        html += '</div>';
        return html;
    }

    bindFormEvents() {
        const form = this.element.querySelector('.ui-form');

        // Form submission
        form.addEventListener('submit', (e) => {
            e.preventDefault();
            this.handleSubmit();
        });

        // Form reset
        form.addEventListener('reset', () => {
            setTimeout(() => {
                this.clearErrors();
                this.emit('reset');
            }, 0);
        });

        // Field validation on change
        if (this.options.validateOnChange) {
            this.options.fields.forEach((field, index) => {
                const fieldElement = this.element.querySelector(`#field_${index}`);
                if (fieldElement) {
                    fieldElement.addEventListener('blur', () => {
                        this.validateField(field, index);
                    });

                    fieldElement.addEventListener('input', () => {
                        this.clearFieldError(index);
                    });
                }
            });
        }
    }

    validateField(field, index) {
        const fieldElement = this.element.querySelector(`#field_${index}`);
        if (!fieldElement) return true;

        let isValid = true;
        let errorMessage = '';

        // Required validation
        if (field.required && !fieldElement.value.trim()) {
            isValid = false;
            errorMessage = `${field.label} es obligatorio`;
        }

        // Type validation
        if (isValid && fieldElement.value) {
            switch (field.type) {
                case 'email':
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailRegex.test(fieldElement.value)) {
                        isValid = false;
                        errorMessage = 'Ingrese un correo electrónico válido';
                    }
                    break;

                case 'number':
                    const numValue = parseFloat(fieldElement.value);
                    if (isNaN(numValue)) {
                        isValid = false;
                        errorMessage = 'Ingrese un número válido';
                    } else if (field.min !== undefined && numValue < field.min) {
                        isValid = false;
                        errorMessage = `El valor debe ser mayor o igual a ${field.min}`;
                    } else if (field.max !== undefined && numValue > field.max) {
                        isValid = false;
                        errorMessage = `El valor debe ser menor o igual a ${field.max}`;
                    }
                    break;

                case 'tel':
                    const phoneRegex = /^[+]?[\d\s\-()]{7,}$/;
                    if (!phoneRegex.test(fieldElement.value)) {
                        isValid = false;
                        errorMessage = 'Ingrese un número de teléfono válido';
                    }
                    break;
            }
        }

        // Length validation
        if (isValid && fieldElement.value) {
            if (field.minLength && fieldElement.value.length < field.minLength) {
                isValid = false;
                errorMessage = `El campo debe tener al menos ${field.minLength} caracteres`;
            } else if (field.maxLength && fieldElement.value.length > field.maxLength) {
                isValid = false;
                errorMessage = `El campo debe tener máximo ${field.maxLength} caracteres`;
            }
        }

        // Pattern validation
        if (isValid && field.pattern && fieldElement.value) {
            const regex = new RegExp(field.pattern);
            if (!regex.test(fieldElement.value)) {
                isValid = false;
                errorMessage = field.patternMessage || 'El formato del campo es incorrecto';
            }
        }

        // Custom validation
        if (isValid && field.validation && typeof field.validation === 'function') {
            const customValidation = field.validation(fieldElement.value);
            if (customValidation !== true) {
                isValid = false;
                errorMessage = typeof customValidation === 'string' ? customValidation : 'Error de validación';
            }
        }

        // Update UI
        if (isValid) {
            this.clearFieldError(index);
        } else {
            this.showFieldError(index, errorMessage);
        }

        return isValid;
    }

    validateForm() {
        let isValid = true;

        this.options.fields.forEach((field, index) => {
            if (!this.validateField(field, index)) {
                isValid = false;
            }
        });

        return isValid;
    }

    showFieldError(index, message) {
        const fieldElement = this.element.querySelector(`#field_${index}`);
        const errorElement = this.element.querySelector(`#field_${index}_error`);

        if (fieldElement) {
            fieldElement.classList.add('error');
        }

        if (errorElement && this.options.showErrors) {
            errorElement.textContent = message;
        }
    }

    clearFieldError(index) {
        const fieldElement = this.element.querySelector(`#field_${index}`);
        const errorElement = this.element.querySelector(`#field_${index}_error`);

        if (fieldElement) {
            fieldElement.classList.remove('error');
        }

        if (errorElement) {
            errorElement.textContent = '';
        }
    }

    clearErrors() {
        this.options.fields.forEach((_, index) => {
            this.clearFieldError(index);
        });
    }

    handleSubmit() {
        if (this.validateForm()) {
            const formData = this.getFormData();
            this.emit('submit', formData);

            if (this.options.onSubmit && typeof this.options.onSubmit === 'function') {
                this.options.onSubmit(formData);
            }
        } else {
            this.emit('validationError');
        }
    }

    getFormData() {
        const form = this.element.querySelector('.ui-form');
        const formData = new FormData(form);
        const data = {};

        for (let [key, value] of formData.entries()) {
            if (data[key]) {
                // Handle multiple values (checkboxes, radio groups)
                if (Array.isArray(data[key])) {
                    data[key].push(value);
                } else {
                    data[key] = [data[key], value];
                }
            } else {
                data[key] = value;
            }
        }

        return data;
    }

    reset() {
        const form = this.element.querySelector('.ui-form');
        if (form) {
            form.reset();
            this.clearErrors();
        }
    }

    setFieldValue(fieldName, value) {
        const field = this.options.fields.find(f => f.name === fieldName);
        if (!field) return;

        const index = this.options.fields.indexOf(field);
        const fieldElement = this.element.querySelector(`#field_${index}`);

        if (fieldElement) {
            if (field.type === 'checkbox' || field.type === 'radio') {
                fieldElement.checked = Boolean(value);
            } else {
                fieldElement.value = value;
            }
        }
    }

    getFieldValue(fieldName) {
        const field = this.options.fields.find(f => f.name === fieldName);
        if (!field) return null;

        const index = this.options.fields.indexOf(field);
        const fieldElement = this.element.querySelector(`#field_${index}`);

        if (fieldElement) {
            if (field.type === 'checkbox' || field.type === 'radio') {
                return fieldElement.checked;
            } else {
                return fieldElement.value;
            }
        }

        return null;
    }
}

// Componente de Informes
class ReportComponent extends BaseComponent {
    getDefaultOptions() {
        return {
            ...super.getDefaultOptions(),
            title: '',
            sections: [],
            layout: 'vertical', // vertical, horizontal
            exportable: true,
            template: 'default'
        };
    }

    render() {
        const { title, sections, layout, exportable } = this.options;

        let html = `<div class="ui-report">`;

        // Header
        html += '<div class="ui-report-header">';
        html += `<h2 class="ui-report-title">${title}</h2>`;
        html += '<div class="ui-report-actions">';

        if (exportable) {
            html += '<button class="ui-btn ui-btn-secondary ui-print-btn">Imprimir</button>';
            html += '<button class="ui-btn ui-btn-outline ui-export-pdf-btn">Exportar PDF</button>';
        }

        html += '</div></div>';

        // Content
        html += `<div class="ui-report-content ui-report-layout-${layout}">`;

        sections.forEach((section, index) => {
            html += this.renderSection(section, index);
        });

        html += '</div>';

        // Footer
        html += '<div class="ui-report-footer">';
        html += `<p>Generado el ${new Date().toLocaleDateString('es-ES')}</p>`;
        html += '</div>';

        html += '</div>';

        this.element.innerHTML = html;
        this.bindReportEvents();
        this.initSections();
    }

    renderSection(section, index) {
        let html = `<div class="ui-report-section" data-section="${index}">`;

        if (section.title) {
            html += `<h3 class="ui-section-title">${section.title}</h3>`;
        }

        if (section.description) {
            html += `<p class="ui-section-description">${section.description}</p>`;
        }

        html += '<div class="ui-section-content">';

        switch (section.type) {
            case 'table':
                html += `<div class="ui-section-table"></div>`;
                break;
            case 'chart':
                html += `<div class="ui-section-chart"></div>`;
                break;
            case 'text':
                html += `<div class="ui-section-text">${section.content || ''}</div>`;
                break;
            case 'summary':
                html += `<div class="ui-section-summary"></div>`;
                break;
            default:
                html += `<div class="ui-section-custom">${section.content || ''}</div>`;
        }

        html += '</div>';
        html += '</div>';

        return html;
    }

    initSections() {
        this.options.sections.forEach((section, index) => {
            const sectionElement = this.element.querySelector(`[data-section="${index}"]`);
            if (!sectionElement) return;

            switch (section.type) {
                case 'table':
                    this.initTableSection(sectionElement, section);
                    break;
                case 'chart':
                    this.initChartSection(sectionElement, section);
                    break;
                case 'summary':
                    this.initSummarySection(sectionElement, section);
                    break;
            }
        });
    }

    initTableSection(sectionElement, section) {
        const tableContainer = sectionElement.querySelector('.ui-section-table');

        this.tableComponents = this.tableComponents || [];
        const tableComponent = new TableComponent({
            container: tableContainer,
            data: section.data,
            columns: section.columns,
            pagination: section.pagination !== false,
            pageSize: section.pageSize || 10,
            sortable: section.sortable !== false,
            searchable: section.searchable !== false,
            exportable: false // El reporte maneja la exportación
        });

        this.tableComponents.push(tableComponent);
    }

    initChartSection(sectionElement, section) {
        const chartContainer = sectionElement.querySelector('.ui-section-chart');

        this.chartComponents = this.chartComponents || [];
        const chartComponent = new ChartComponent({
            container: chartContainer,
            type: section.chartType || 'line',
            data: section.data,
            options: {
                ...section.options,
                title: '', // El título se maneja a nivel de sección
                responsive: true
            }
        });

        this.chartComponents.push(chartComponent);
    }

    initSummarySection(sectionElement, section) {
        const summaryContainer = sectionElement.querySelector('.ui-section-summary');

        let html = '<div class="ui-summary-cards">';

        if (section.metrics) {
            section.metrics.forEach(metric => {
                html += `
                    <div class="ui-summary-card">
                        <div class="ui-summary-label">${metric.label}</div>
                        <div class="ui-summary-value">${this.formatMetricValue(metric.value, metric.format)}</div>
                        ${metric.change ? `<div class="ui-summary-change ${metric.change >= 0 ? 'positive' : 'negative'}">
                            ${metric.change >= 0 ? '↑' : '↓'} ${Math.abs(metric.change)}%
                        </div>` : ''}
                    </div>
                `;
            });
        }

        html += '</div>';
        summaryContainer.innerHTML = html;
    }

    formatMetricValue(value, format = 'number') {
        switch (format) {
            case 'currency':
                return new Intl.NumberFormat('es-ES', {
                    style: 'currency',
                    currency: 'EUR'
                }).format(value);
            case 'percentage':
                return `${value}%`;
            case 'decimal':
                return new Intl.NumberFormat('es-ES', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }).format(value);
            default:
                return new Intl.NumberFormat('es-ES').format(value);
        }
    }

    bindReportEvents() {
        // Print button
        const printBtn = this.element.querySelector('.ui-print-btn');
        if (printBtn) {
            printBtn.addEventListener('click', () => {
                this.print();
            });
        }

        // Export PDF button (simplified implementation)
        const exportPdfBtn = this.element.querySelector('.ui-export-pdf-btn');
        if (exportPdfBtn) {
            exportPdfBtn.addEventListener('click', () => {
                this.exportToPDF();
            });
        }
    }

    print() {
        const originalContent = document.body.innerHTML;
        const reportContent = this.element.innerHTML;

        document.body.innerHTML = `
            <div class="ui-report-print">
                ${reportContent}
            </div>
        `;

        window.print();

        document.body.innerHTML = originalContent;

        // Re-initialize the report after printing
        setTimeout(() => {
            this.render();
        }, 100);
    }

    exportToPDF() {
        // Simplified PDF export using print functionality
        // In a real implementation, you would use a library like jsPDF
        alert('Funcionalidad de exportación PDF disponible con librerías adicionales como jsPDF');
    }

    updateSection(index, newData) {
        const section = this.options.sections[index];
        if (!section) return;

        if (section.type === 'table' && this.tableComponents && this.tableComponents[index]) {
            this.tableComponents[index].updateData(newData);
        } else if (section.type === 'chart' && this.chartComponents && this.chartComponents[index]) {
            this.chartComponents[index].updateData(newData);
        } else if (section.type === 'summary') {
            section.metrics = newData;
            this.initSections();
        }
    }

    addSection(section) {
        this.options.sections.push(section);
        this.render();
    }

    removeSection(index) {
        this.options.sections.splice(index, 1);
        this.render();
    }
}

// Exportar componentes para uso global
window.UILibrary = {
    TableComponent,
    ChartComponent,
    FormComponent,
    ReportComponent,
    BaseComponent
};

// Auto-inicialización si se encuentra el atributo data-ui-component
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-ui-component]').forEach(element => {
        const componentName = element.dataset.uiComponent;
        const options = JSON.parse(element.dataset.options || '{}');
        options.container = element;

        if (window.UILibrary[componentName]) {
            new window.UILibrary[componentName](options);
        }
    });
});
