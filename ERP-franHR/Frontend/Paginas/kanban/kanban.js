// ===== CLASE PRINCIPAL DEL KANBAN =====
class KanbanBoard {
    constructor() {
        this.api = new ApiService();
        this.currentTableroId = 1; // ID del tablero por defecto
        this.columns = [];
        this.cards = [];
        this.usuarios = [];
        this.currentEditingColumn = null;
        this.currentEditingCard = null;
        this.draggedCard = null;
        this.init();
    }

    // Inicialización del Kanban
    async init() {
        this.setupEventListeners();
        await this.loadUsuarios();
        await this.loadTableroData();
        this.renderBoard();
    }

    // Configurar event listeners
    setupEventListeners() {
        // Botones principales
        document.getElementById('add-column-btn').addEventListener('click', () => this.openColumnModal());
        document.getElementById('save-board-btn').addEventListener('click', () => this.saveBoard());

        // Modales - Columnas
        document.getElementById('close-column-modal').addEventListener('click', () => this.closeColumnModal());
        document.getElementById('cancel-column').addEventListener('click', () => this.closeColumnModal());
        document.getElementById('column-form').addEventListener('submit', (e) => this.handleColumnSubmit(e));

        // Modales - Tarjetas
        document.getElementById('close-card-modal').addEventListener('click', () => this.closeCardModal());
        document.getElementById('cancel-card').addEventListener('click', () => this.closeCardModal());
        document.getElementById('card-form').addEventListener('submit', (e) => this.handleCardSubmit(e));

        // Modal de confirmación
        document.getElementById('close-confirm-modal').addEventListener('click', () => this.closeConfirmModal());
        document.getElementById('cancel-delete').addEventListener('click', () => this.closeConfirmModal());
        document.getElementById('confirm-delete').addEventListener('click', () => this.handleConfirmDelete());

        // Color presets
        document.querySelectorAll('.color-preset').forEach(preset => {
            preset.addEventListener('click', (e) => this.selectColorPreset(e));
        });

        // Cerrar modales al hacer clic fuera
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('modal')) {
                this.closeAllModals();
            }
        });

        // Tecla ESC para cerrar modales
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                this.closeAllModals();
            }
        });
    }

    // Cargar usuarios disponibles
    async loadUsuarios() {
        try {
            const response = await this.api.getUsuarios();
            if (response.success) {
                this.usuarios = response.data;
            }
        } catch (error) {
            console.error('Error cargando usuarios:', error);
            this.showNotification('Error cargando usuarios', 'error');
        }
    }

    // Cargar datos del tablero desde la API
    async loadTableroData() {
        try {
            const response = await this.api.getTablero(this.currentTableroId);
            if (response.success) {
                const tableroData = response.data;
                this.columns = tableroData.columnas || [];
                
                // Extraer tarjetas de las columnas (están anidadas)
                this.cards = [];
                if (this.columns.length > 0) {
                    this.columns.forEach(column => {
                        if (column.tarjetas && Array.isArray(column.tarjetas)) {
                            this.cards.push(...column.tarjetas);
                        }
                    });
                }
                
                // Debug: Verificar datos cargados
                console.log('Datos del tablero cargados:', tableroData);
                console.log('Columnas:', this.columns);
                console.log('Tarjetas extraídas:', this.cards);
                
                this.renderBoard();
            } else {
                // Si no existe el tablero, crear uno por defecto
                await this.createDefaultTablero();
            }
        } catch (error) {
            console.error('Error cargando tablero:', error);
            // Si hay error, crear tablero por defecto
            await this.createDefaultTablero();
        }
    }

    // Crear tablero por defecto si no existe
    async createDefaultTablero() {
        try {
            const tableroData = {
                nombre: 'Mi Tablero Kanban',
                descripcion: 'Tablero principal para gestión de tareas',
                usuario_propietario: 1 // Usuario por defecto
            };

            const response = await this.api.createTablero(tableroData);
            if (response.success) {
                this.currentTableroId = response.data.id;
                // Cargar las columnas por defecto que se crearon automáticamente
                await this.loadTableroData();
            }
        } catch (error) {
            console.error('Error creando tablero por defecto:', error);
            this.showNotification('Error inicializando tablero', 'error');
            // Fallback a datos hardcodeados
            this.loadDefaultColumns();
        }
    }

    // Cargar datos por defecto (fallback cuando falla la API)
    loadDefaultColumns() {
        this.columns = [
            {
                id: 'col-1',
                name: 'Por Hacer',
                color: '#e74c3c',
                limit: null
            },
            {
                id: 'col-2', 
                name: 'En Progreso',
                color: '#f39c12',
                limit: 3
            },
            {
                id: 'col-3',
                name: 'Completado',
                color: '#27ae60',
                limit: null
            }
        ];

        this.cards = [
            {
                id: 'card-1',
                columnId: 'col-1',
                title: 'Configurar proyecto',
                description: 'Configurar el entorno de desarrollo inicial',
                priority: 'high',
                assignee: 'Desarrollador',
                dueDate: '',
                tags: ['setup', 'inicial']
            },
            {
                id: 'card-2',
                columnId: 'col-2',
                title: 'Diseñar interfaz',
                description: 'Crear mockups y diseños de la interfaz de usuario',
                priority: 'medium',
                assignee: 'Diseñador',
                dueDate: '',
                tags: ['diseño', 'ui']
            },
            {
                id: 'card-3',
                columnId: 'col-3',
                title: 'Documentación inicial',
                description: 'Crear documentación básica del proyecto',
                priority: 'low',
                assignee: 'Analista',
                dueDate: '',
                tags: ['docs']
            }
        ];

        this.renderBoard();
        this.showNotification('Cargados datos por defecto (modo offline)', 'info');
    }

    // Renderizar todo el tablero
    renderBoard() {
        const board = document.getElementById('kanban-board');
        
        if (this.columns.length === 0) {
            board.innerHTML = this.getEmptyBoardHTML();
            return;
        }

        board.innerHTML = '';
        this.columns.forEach(column => {
            const columnElement = this.createColumnElement(column);
            board.appendChild(columnElement);
        });
    }

    // HTML para tablero vacío
    getEmptyBoardHTML() {
        return `
            <div class="empty-board">
                <i class="fas fa-columns"></i>
                <h3>¡Comienza tu tablero Kanban!</h3>
                <p>Crea tu primera columna para organizar tus tareas de manera visual y eficiente.</p>
                <button class="btn-primary" onclick="kanban.openColumnModal()">
                    <i class="fas fa-plus"></i> Crear Primera Columna
                </button>
            </div>
        `;
    }

    // Crear elemento de columna
    createColumnElement(column) {
        // Usar el ID de la base de datos
        const columnId = column.Identificador || column.id;
        const columnCards = this.cards.filter(card => 
            (card.columna_id && card.columna_id == columnId) || 
            (card.columnId && card.columnId == columnId)
        );
        
        // Debug: Verificar filtrado de tarjetas
        console.log(`Columna ${columnId} (${column.nombre || column.name}):`);
        console.log('  Todas las tarjetas:', this.cards);
        console.log('  Tarjetas filtradas:', columnCards);
        console.log('  Criterio de filtro: columna_id == ' + columnId);
        
        const columnDiv = document.createElement('div');
        columnDiv.className = 'kanban-column';
        columnDiv.dataset.columnId = columnId;

        const columnName = column.nombre || column.name;
        const columnColor = column.color || '#875A7B';

        columnDiv.innerHTML = `
            <div class="column-header" style="border-bottom-color: ${columnColor};">
                <div class="column-title">
                    <h3>${columnName}</h3>
                    <span class="column-count">${columnCards.length}</span>
                </div>
                <div class="column-actions">
                    <button class="column-btn" onclick="kanban.editColumn('${columnId}')" title="Editar columna">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="column-btn" onclick="kanban.deleteColumn('${columnId}')" title="Eliminar columna">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
            <div class="column-content" data-column-id="${columnId}">
                ${columnCards.map(card => this.createCardHTML(card)).join('')}
            </div>
            <div class="column-footer">
                <button class="add-card-btn" onclick="kanban.openCardModal('${columnId}')">
                    <i class="fas fa-plus"></i> Añadir tarjeta
                </button>
            </div>
        `;

        this.setupColumnDragAndDrop(columnDiv);
        return columnDiv;
    }

    // Crear HTML de tarjeta
    createCardHTML(card) {
        // Compatibilidad con ambos formatos de datos
        const cardId = card.Identificador || card.id;
        const cardTitle = card.titulo || card.title;
        const cardDescription = card.descripcion || card.description;
        // Normalizar prioridad (DB numérica 1-4 -> clases string)
        const priorityNum = (typeof card.prioridad === 'number') ? card.prioridad : null;
        const priorityMap = {1: 'low', 2: 'medium', 3: 'high', 4: 'urgent'};
        const cardPriority = priorityNum ? (priorityMap[priorityNum] || 'medium') : (card.priority || card.prioridad || 'medium');
        // Mostrar nombre de asignado si está disponible
        const cardAssigneeName = card.asignado_nombre || card.assignee || null;
        const cardDueDate = card.fecha_vencimiento || card.dueDate;
        const cardTags = card.etiquetas || card.tags || [];

        const dueDate = cardDueDate ? new Date(cardDueDate) : null;
        const isOverdue = dueDate && dueDate < new Date();
        const formattedDate = dueDate ? dueDate.toLocaleDateString('es-ES') : '';

        // Manejar etiquetas como string o array
        let tags = '';
        if (Array.isArray(cardTags)) {
            tags = cardTags.map(tag => `<span class="card-tag">${tag}</span>`).join('');
        } else if (typeof cardTags === 'string' && cardTags) {
            const tagArray = cardTags.split(',').map(tag => tag.trim()).filter(Boolean);
            tags = tagArray.map(tag => `<span class="card-tag">${tag}</span>`).join('');
        }

        return `
            <div class="kanban-card" draggable="true" data-card-id="${cardId}">
                <div class="card-priority ${cardPriority}"></div>
                <div class="card-header">
                    <h4 class="card-title">${cardTitle}</h4>
                    <div class="card-actions">
                        <button class="card-btn" onclick="kanban.editCard('${cardId}')" title="Editar">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="card-btn" onclick="kanban.deleteCard('${cardId}')" title="Eliminar">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
                ${cardDescription ? `<p class="card-description">${cardDescription}</p>` : ''}
                <div class="card-meta">
                    ${cardAssigneeName ? `
                        <div class="card-assignee">
                            <i class="fas fa-user"></i>
                            <span>${cardAssigneeName}</span>
                        </div>
                    ` : ''}
                    ${cardDueDate ? `
                        <div class="card-due-date ${isOverdue ? 'overdue' : ''}">
                            <i class="fas fa-calendar"></i>
                            <span>${formattedDate}</span>
                        </div>
                    ` : ''}
                </div>
                ${tags ? `<div class="card-tags">${tags}</div>` : ''}
            </div>
        `;
    }

    // Configurar drag and drop para columnas
    setupColumnDragAndDrop(columnElement) {
        const columnContent = columnElement.querySelector('.column-content');
        
        // Eventos para las tarjetas
        columnElement.addEventListener('dragstart', (e) => {
            if (e.target.classList.contains('kanban-card')) {
                this.handleDragStart(e);
            }
        });

        columnElement.addEventListener('dragend', (e) => {
            if (e.target.classList.contains('kanban-card')) {
                this.handleDragEnd(e);
            }
        });

        // Eventos para las columnas (drop zones)
        columnContent.addEventListener('dragover', (e) => this.handleDragOver(e));
        columnContent.addEventListener('dragenter', (e) => this.handleDragEnter(e));
        columnContent.addEventListener('dragleave', (e) => this.handleDragLeave(e));
        columnContent.addEventListener('drop', (e) => this.handleDrop(e));
    }

    // Manejar inicio de arrastre
    handleDragStart(e) {
        this.draggedCard = e.target;
        e.target.classList.add('dragging');
        e.dataTransfer.effectAllowed = 'move';
        e.dataTransfer.setData('text/html', e.target.outerHTML);
    }

    // Manejar fin de arrastre
    handleDragEnd(e) {
        e.target.classList.remove('dragging');
        document.querySelectorAll('.column-content').forEach(col => {
            col.classList.remove('drag-over');
        });
    }

    // Manejar arrastre sobre columna
    handleDragOver(e) {
        e.preventDefault();
        e.dataTransfer.dropEffect = 'move';
    }

    // Manejar entrada a columna
    handleDragEnter(e) {
        e.preventDefault();
        if (e.target.classList.contains('column-content')) {
            e.target.classList.add('drag-over');
        }
    }

    // Manejar salida de columna
    handleDragLeave(e) {
        if (e.target.classList.contains('column-content')) {
            e.target.classList.remove('drag-over');
        }
    }

    // Manejar soltar tarjeta
    async handleDrop(e) {
        e.preventDefault();
        const columnContent = e.target.closest('.column-content');
        
        if (columnContent && this.draggedCard) {
            const newColumnId = columnContent.dataset.columnId;
            const cardId = this.draggedCard.dataset.cardId;
            
            // Buscar la tarjeta con compatibilidad de formatos
            const card = this.cards.find(c => 
                (c.Identificador && c.Identificador == cardId) || 
                (c.id && c.id == cardId)
            );
            
            if (!card) {
                columnContent.classList.remove('drag-over');
                this.draggedCard = null;
                return;
            }

            const currentColumn = card.columna_id || card.columnId;
            
            // Si la tarjeta ya está en la columna destino, no hacer nada
            if (currentColumn == newColumnId) {
                columnContent.classList.remove('drag-over');
                this.draggedCard = null;
                return;
            }

            try {
                // Calcular nueva posición como último lugar de la columna destino
                const cardsInTarget = this.cards.filter(c => String(c.columna_id || c.columnId) === String(newColumnId));
                const nuevaPosicion = (cardsInTarget?.length || 0) + 1;

                // Preparar payload completo preservando campos existentes
                const payload = {
                    titulo: card.titulo || card.title || card.nombre || '',
                    descripcion: card.descripcion || card.description || '',
                    asignado_a: card.asignado_a || card.assigned_to || card.asignadoA || null,
                    columna_id: newColumnId,
                    posicion: nuevaPosicion
                };

                const response = await this.api.updateTarjeta(cardId, payload);
                if (response.success) {
                    this.showNotification('Tarjeta movida exitosamente', 'success');
                    await this.loadTableroData();
                    this.renderBoard();
                }
            } catch (error) {
                console.error('Error moviendo tarjeta:', error);
                this.showNotification('Error moviendo tarjeta: ' + error.message, 'error');
            }
        }
        
        columnContent.classList.remove('drag-over');
        this.draggedCard = null;
    }

    // ===== GESTIÓN DE COLUMNAS =====
    
    openColumnModal(columnId = null) {
        this.currentEditingColumn = columnId;
        const modal = document.getElementById('column-modal');
        const title = document.getElementById('column-modal-title');
        const form = document.getElementById('column-form');
        
        if (columnId) {
            const column = this.columns.find(c => 
                (c.Identificador && c.Identificador == columnId) || 
                (c.id && c.id == columnId)
            );
            
            if (column) {
                title.textContent = 'Editar Columna';
                // Usar las propiedades correctas de la API
                document.getElementById('column-name').value = column.nombre || column.name || '';
                document.getElementById('column-color').value = column.color || '#875A7B';
                document.getElementById('column-limit').value = column.limite_tarjetas || column.limit || '';
            } else {
                console.error('Columna no encontrada:', columnId);
                return;
            }
        } else {
            title.textContent = 'Crear Nueva Columna';
            form.reset();
            document.getElementById('column-color').value = '#875A7B';
        }
        
        modal.classList.add('show');
    }

    closeColumnModal() {
        document.getElementById('column-modal').classList.remove('show');
        this.currentEditingColumn = null;
    }

    async handleColumnSubmit(e) {
        e.preventDefault();
        
        const formData = new FormData(e.target);
        const columnData = {
            nombre: formData.get('column-name'),
            color: formData.get('column-color'),
            limite_tarjetas: formData.get('column-limit') || null,
            tablero_id: this.currentTableroId
        };

        // Calcular posición: si es edición, usar índice actual; si es creación, usar longitud + 1
        const existingIndex = this.currentEditingColumn ? this.columns.findIndex(c => 
            (c.Identificador && c.Identificador == this.currentEditingColumn) || 
            (c.id && c.id == this.currentEditingColumn)
        ) : -1;
        columnData.posicion = existingIndex >= 0 ? (existingIndex + 1) : (this.columns.length + 1);

        try {
            if (this.currentEditingColumn) {
                // Actualizar columna existente
                const response = await this.api.updateColumna(this.currentEditingColumn, columnData);
                if (response.success) {
                    this.showNotification('Columna actualizada exitosamente', 'success');
                    await this.loadTableroData();
                    this.renderBoard();
                }
            } else {
                // Crear nueva columna
                const response = await this.api.createColumna(columnData);
                if (response.success) {
                    this.showNotification('Columna creada exitosamente', 'success');
                    await this.loadTableroData();
                    this.renderBoard();
                }
            }
            this.closeColumnModal();
        } catch (error) {
            console.error('Error guardando columna:', error);
            this.showNotification('Error guardando columna: ' + error.message, 'error');
        }
    }

    createColumn(columnData) {
        const newColumn = {
            id: 'col-' + Date.now(),
            ...columnData
        };
        
        this.columns.push(newColumn);
        this.renderBoard();
        this.closeColumnModal();
        this.showNotification('Columna creada exitosamente', 'success');
    }

    updateColumn(columnId, columnData) {
        const columnIndex = this.columns.findIndex(c => c.id === columnId);
        if (columnIndex !== -1) {
            this.columns[columnIndex] = { ...this.columns[columnIndex], ...columnData };
            this.renderBoard();
            this.closeColumnModal();
            this.showNotification('Columna actualizada exitosamente', 'success');
        }
    }

    editColumn(columnId) {
        const column = this.columns.find(col => 
            (col.Identificador && col.Identificador == columnId) || 
            (col.id && col.id == columnId)
        );
        if (column) {
            this.openColumnModal(columnId);
        }
    }

    async deleteColumn(columnId) {
        const column = this.columns.find(col => 
            (col.Identificador && col.Identificador == columnId) || 
            (col.id && col.id == columnId)
        );
        
        if (!column) return;

        const columnName = column.nombre || column.name;
        this.showConfirmModal(
            `¿Estás seguro de que deseas eliminar la columna "${columnName}"? Esta acción no se puede deshacer.`,
            async () => {
                try {
                    const response = await this.api.deleteColumna(columnId);
                    if (response.success) {
                        this.showNotification('Columna eliminada exitosamente', 'success');
                        await this.loadTableroData();
                        this.renderBoard();
                    }
                } catch (error) {
                    console.error('Error eliminando columna:', error);
                    this.showNotification('Error eliminando columna: ' + error.message, 'error');
                }
            }
        );
    }

    // ===== GESTIÓN DE TARJETAS =====
    
    openCardModal(columnId = null, cardId = null) {
        this.currentEditingCard = cardId;
        const modal = document.getElementById('card-modal');
        const title = document.getElementById('card-modal-title');
        const form = document.getElementById('card-form');
        const assigneeSelect = document.getElementById('card-assignee');
        
        // Poblar opciones del select de usuarios
        assigneeSelect.innerHTML = '<option value="">Sin asignar</option>' +
            (this.usuarios || []).map(u => {
                const id = u.id || u.Identificador;
                const nombre = u.nombre || u.nombrecompleto || u.usuario || '';
                return `<option value="${id}">${nombre}</option>`;
            }).join('');
        
        if (cardId) {
            const card = this.cards.find(c => 
                (c.Identificador && c.Identificador == cardId) || 
                (c.id && c.id == cardId)
            );
            
            if (card) {
                title.textContent = 'Editar Tarjeta';
                document.getElementById('card-title').value = card.titulo || card.title || '';
                document.getElementById('card-description').value = card.descripcion || card.description || '';
                const priorityNum = (typeof card.prioridad === 'number') ? card.prioridad : null;
                const priorityMap = {1: 'low', 2: 'medium', 3: 'high', 4: 'urgent'};
                document.getElementById('card-priority').value = priorityNum ? (priorityMap[priorityNum] || 'medium') : (card.priority || card.prioridad || 'medium');
                // Seleccionar el asignado en el select si existe
                const assignId = card.asignado_a || card.assigneeId || null;
                if (assignId) {
                    assigneeSelect.value = assignId;
                } else if (card.asignado_nombre) {
                    // Intentar seleccionar por nombre si no hay id
                    const idx = Array.from(assigneeSelect.options).find(opt => opt.text === card.asignado_nombre);
                    if (idx) assigneeSelect.value = idx.value;
                }
                document.getElementById('card-due-date').value = card.fecha_vencimiento || card.dueDate || '';
                let tags = '';
                if (card.etiquetas) {
                    if (Array.isArray(card.etiquetas)) {
                        tags = card.etiquetas.join(', ');
                    } else if (typeof card.etiquetas === 'string') {
                        tags = card.etiquetas;
                    }
                } else if (card.tags && Array.isArray(card.tags)) {
                    tags = card.tags.join(', ');
                }
                document.getElementById('card-tags').value = tags;
            } else {
                console.error('Tarjeta no encontrada:', cardId);
                return;
            }
        } else {
            title.textContent = 'Crear Nueva Tarjeta';
            form.reset();
            document.getElementById('card-priority').value = 'medium';
        }
        
        if (columnId) {
            form.dataset.targetColumn = columnId;
        }
        
        modal.classList.add('show');
    }

    closeCardModal() {
        document.getElementById('card-modal').classList.remove('show');
        this.currentEditingCard = null;
    }

    async handleCardSubmit(e) {
        e.preventDefault();
        
        const formData = new FormData(e.target);
        const assigneeValue = formData.get('card-assignee') || '';
        let asignadoId = null;
        let asignadoNombre = null;
        if (assigneeValue) {
            asignadoId = parseInt(assigneeValue, 10);
            const user = (this.usuarios || []).find(u => (u.id || u.Identificador) == asignadoId);
            if (user) {
                asignadoNombre = user.nombre || user.nombrecompleto || user.usuario || null;
            }
        }

        const priorityStr = (formData.get('card-priority') || 'medium').toLowerCase();
        const priorityMapToInt = { low: 1, medium: 2, high: 3, urgent: 4 };
        const prioridadInt = priorityMapToInt[priorityStr] || 2;

        const cardData = {
            titulo: formData.get('card-title'),
            descripcion: formData.get('card-description'),
            prioridad: prioridadInt,
            fecha_vencimiento: formData.get('card-due-date') || null,
            etiquetas: formData.get('card-tags') || '',
            asignado_a: asignadoId,
            asignado_nombre: asignadoNombre,
            columna_id: this.currentColumnId
        };

        if (!this.currentEditingCard) {
            cardData.creado_por = 1; // TODO: usar usuario actual
        }

        try {
            if (this.currentEditingCard) {
                const existingCard = this.cards.find(c => 
                    (c.Identificador && c.Identificador == this.currentEditingCard) || 
                    (c.id && c.id == this.currentEditingCard)
                );
                const currentColumnId = existingCard?.columna_id ?? existingCard?.columnId ?? cardData.columna_id;
                let posicion = existingCard?.orden ?? existingCard?.posicion;
                if (posicion == null) {
                    const cardsInColumn = this.cards.filter(c => (c.columna_id ?? c.columnId) == currentColumnId);
                    const idx = cardsInColumn.findIndex(c => (c.Identificador ?? c.id) == this.currentEditingCard);
                    posicion = idx >= 0 ? (idx + 1) : ((cardsInColumn.length || 0) + 1);
                }
                cardData.posicion = posicion;
                cardData.columna_id = currentColumnId;

                const response = await this.api.updateTarjeta(this.currentEditingCard, cardData);
                if (response.success) {
                    this.showNotification('Tarjeta actualizada exitosamente', 'success');
                    await this.loadTableroData();
                    this.renderBoard();
                }
            } else {
                const targetColumn = e.target.dataset.targetColumn;
                cardData.columna_id = targetColumn;
                const response = await this.api.createTarjeta(cardData);
                if (response.success) {
                    this.showNotification('Tarjeta creada exitosamente', 'success');
                    await this.loadTableroData();
                    this.renderBoard();
                }
            }
            this.closeCardModal();
        } catch (error) {
            console.error('Error guardando tarjeta:', error);
            this.showNotification('Error guardando tarjeta: ' + error.message, 'error');
        }
    }

    createCard(columnId, cardData) {
        const newCard = {
            id: 'card-' + Date.now(),
            columnId: columnId,
            ...cardData
        };
        
        this.cards.push(newCard);
        this.renderBoard();
        this.closeCardModal();
        this.showNotification('Tarjeta creada exitosamente', 'success');
    }

    updateCard(cardId, cardData) {
        const cardIndex = this.cards.findIndex(c => c.id === cardId);
        if (cardIndex !== -1) {
            this.cards[cardIndex] = { ...this.cards[cardIndex], ...cardData };
            this.renderBoard();
            this.closeCardModal();
            this.showNotification('Tarjeta actualizada exitosamente', 'success');
        }
    }

    editCard(cardId) {
        const card = this.cards.find(c => 
            (c.Identificador && c.Identificador == cardId) || 
            (c.id && c.id == cardId)
        );
        if (card) {
            this.openCardModal(null, cardId);
        }
    }

    async deleteCard(cardId) {
        const card = this.cards.find(c => 
            (c.Identificador && c.Identificador == cardId) || 
            (c.id && c.id == cardId)
        );
        
        if (!card) return;

        const cardTitle = card.titulo || card.title;
        this.showConfirmModal(
            `¿Estás seguro de que deseas eliminar la tarjeta "${cardTitle}"? Esta acción no se puede deshacer.`,
            async () => {
                try {
                    const response = await this.api.deleteTarjeta(cardId);
                    if (response.success) {
                        this.showNotification('Tarjeta eliminada exitosamente', 'success');
                        await this.loadTableroData();
                        this.renderBoard();
                    }
                } catch (error) {
                    console.error('Error eliminando tarjeta:', error);
                    this.showNotification('Error eliminando tarjeta: ' + error.message, 'error');
                }
            }
        );
    }

    // ===== UTILIDADES =====
    
    selectColorPreset(e) {
        const color = e.target.dataset.color;
        document.getElementById('column-color').value = color;
        
        // Actualizar selección visual
        document.querySelectorAll('.color-preset').forEach(preset => {
            preset.classList.remove('active');
        });
        e.target.classList.add('active');
    }

    showConfirmModal(message, onConfirm) {
        document.getElementById('confirm-message').textContent = message;
        document.getElementById('confirm-modal').classList.add('show');
        
        // Guardar callback
        this.confirmCallback = onConfirm;
    }

    closeConfirmModal() {
        document.getElementById('confirm-modal').classList.remove('show');
        this.confirmCallback = null;
    }

    handleConfirmDelete() {
        if (this.confirmCallback) {
            this.confirmCallback();
        }
        this.closeConfirmModal();
    }

    closeAllModals() {
        document.querySelectorAll('.modal').forEach(modal => {
            modal.classList.remove('show');
        });
        this.currentEditingColumn = null;
        this.currentEditingCard = null;
        this.confirmCallback = null;
    }

    showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }

    async saveBoard() {
        try {
            // Guardar el tablero actual en la base de datos
            const tableroData = {
                nombre: `Tablero ${this.currentTableroId}`,
                descripcion: 'Tablero Kanban actualizado automáticamente'
            };
            
            await this.api.updateTablero(this.currentTableroId, tableroData);
            
            // También guardar en localStorage como respaldo
            const boardData = {
                columns: this.columns,
                cards: this.cards,
                timestamp: new Date().toISOString()
            };
            localStorage.setItem('kanban-board', JSON.stringify(boardData));
            
            this.showNotification('Tablero guardado exitosamente', 'success');
        } catch (error) {
            console.error('Error guardando tablero:', error);
            // En caso de error, al menos guardar en localStorage
            const boardData = {
                columns: this.columns,
                cards: this.cards,
                timestamp: new Date().toISOString()
            };
            localStorage.setItem('kanban-board', JSON.stringify(boardData));
            this.showNotification('Error guardando en servidor, guardado localmente', 'warning');
        }
    }

    loadBoard() {
        const savedData = localStorage.getItem('kanban-board');
        if (savedData) {
            const boardData = JSON.parse(savedData);
            this.columns = boardData.columns || [];
            this.cards = boardData.cards || [];
            this.renderBoard();
            this.showNotification('Tablero cargado exitosamente', 'success');
        }
    }
}

// ===== INICIALIZACIÓN =====
let kanban;

document.addEventListener('DOMContentLoaded', function() {
    kanban = new KanbanBoard();
    
    // Intentar cargar datos guardados
    const savedData = localStorage.getItem('kanban-board');
    if (savedData) {
        kanban.loadBoard();
    }
});

// Guardar automáticamente cada 30 segundos
setInterval(() => {
    if (kanban) {
        kanban.saveBoard();
    }
}, 30000);

// ===== SERVICIO DE API =====
class ApiService {
    constructor() {
        this.baseUrl = window.CONFIG?.API_BASE_URL || '/api/';
        this.apiEndpoint = this.baseUrl + 'paginas/kandan.php';
    }

    async request(method, ruta, data = null) {
        const config = {
            method: method,
            headers: {
                'Content-Type': 'application/json',
            },
            credentials: 'include' // Para incluir cookies de sesión
        };

        let url = `${this.apiEndpoint}?ruta=${ruta}`;

        if (method === 'GET' && data) {
            const params = new URLSearchParams(data);
            url += `&${params.toString()}`;
        } else if (data && method !== 'GET') {
            config.body = JSON.stringify(data);
        }

        try {
            const response = await fetch(url, config);
            const result = await response.json();

            if (!response.ok) {
                throw new Error(result.message || `Error ${response.status}`);
            }

            return result;
        } catch (error) {
            console.error('Error en API:', error);
            throw error;
        }
    }

    // ===== MÉTODOS PARA TABLEROS =====
    async getTableros(usuarioId = null) {
        const params = usuarioId ? { usuario_id: usuarioId } : {};
        return await this.request('GET', 'tableros', params);
    }

    async getTablero(tableroId) {
        return await this.request('GET', 'tablero', { id: tableroId });
    }

    async createTablero(data) {
        return await this.request('POST', 'tablero', data);
    }

    async updateTablero(tableroId, data) {
        return await this.request('PUT', 'tablero', { id: tableroId, ...data });
    }

    async deleteTablero(tableroId) {
        return await this.request('DELETE', 'tablero', { id: tableroId });
    }

    // ===== MÉTODOS PARA COLUMNAS =====
    async getColumnas(tableroId) {
        return await this.request('GET', 'columnas', { tablero_id: tableroId });
    }

    async createColumna(data) {
        return await this.request('POST', 'columnas', data);
    }

    async updateColumna(columnaId, data) {
        return await this.request('PUT', 'columnas', { id: columnaId, ...data });
    }

    async deleteColumna(columnaId) {
        return await this.request('DELETE', 'columnas', { id: columnaId });
    }

    // ===== MÉTODOS PARA TARJETAS =====
    async getTarjetas(columnaId) {
        return await this.request('GET', 'tarjetas', { columna_id: columnaId });
    }

    async getTarjeta(tarjetaId) {
        return await this.request('GET', 'tarjeta', { id: tarjetaId });
    }

    async createTarjeta(data) {
        return await this.request('POST', 'tarjetas', data);
    }

    async updateTarjeta(tarjetaId, data) {
        return await this.request('PUT', 'tarjetas', { id: tarjetaId, ...data });
    }

    async deleteTarjeta(tarjetaId) {
        return await this.request('DELETE', 'tarjetas', { id: tarjetaId });
    }

    async moverTarjeta(tarjetaId, nuevaColumnaId, nuevaPosicion, extraData = {}) {
        const payload = { id: tarjetaId, columna_id: nuevaColumnaId, posicion: nuevaPosicion, ...extraData };
        return await this.request('PUT', 'tarjetas', payload);
    }

    // ===== MÉTODOS PARA COMENTARIOS =====
    async getComentarios(tarjetaId) {
        return await this.request('GET', 'comentarios', { tarjeta_id: tarjetaId });
    }

    async createComentario(data) {
        return await this.request('POST', 'comentario', data);
    }

    async deleteComentario(comentarioId) {
        return await this.request('DELETE', 'comentario', { id: comentarioId });
    }

    // ===== MÉTODOS PARA USUARIOS =====
    async getUsuarios() {
        return await this.request('GET', 'usuarios');
    }
}