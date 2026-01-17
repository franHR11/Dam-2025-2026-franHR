/**
 * Editor de Diagramas Interactivo
 * Desarrollado por Francisco José para la asignatura de Desarrollo de Interfaces.
 */

const canvas = document.getElementById('editorCanvas');
const ctx = canvas.getContext('2d');
const modeDisplay = document.getElementById('currentMode');

// Estado de la aplicación
let shapes = [];
let connections = [];
let currentMode = 'select'; // 'select', 'rect', 'pill', 'circle', 'connect'
let selectedShape = null;
let isDragging = false;
let startShape = null; // Para crear conexiones
let dragOffsetX, dragOffsetY;

// Ajustar canvas al tamaño de la pantalla
function resizeCanvas() {
    canvas.width = canvas.parentElement.clientWidth;
    canvas.height = canvas.parentElement.clientHeight;
    draw();
}
window.addEventListener('resize', resizeCanvas);
resizeCanvas();

// --- CLASES ---

class Shape {
    constructor(x, y, type) {
        this.id = Date.now() + Math.random(); // Identificador único
        this.x = x;
        this.y = y;
        this.width = 100;
        this.height = 60;
        this.type = type; // 'rect', 'pill', 'circle'
        this.color = '#ffffff';
        this.borderColor = '#334155';
    }

    draw(ctx, isSelected) {
        ctx.beginPath();
        ctx.fillStyle = this.color;
        ctx.strokeStyle = isSelected ? '#4f46e5' : this.borderColor;
        ctx.lineWidth = isSelected ? 3 : 2;

        if (this.type === 'rect') {
            ctx.rect(this.x, this.y, this.width, this.height);
        } else if (this.type === 'pill') {
            const r = this.height / 2;
            ctx.roundRect(this.x, this.y, this.width, this.height, r);
        } else if (this.type === 'circle') {
            // Hacemos que sea un círculo basado en la anchura
            const r = this.width / 2;
            ctx.arc(this.x + r, this.y + r, r, 0, Math.PI * 2);
        }

        ctx.fill();
        ctx.stroke();
    }

    contains(mx, my) {
        if (this.type === 'circle') {
            const r = this.width / 2;
            const dx = mx - (this.x + r);
            const dy = my - (this.y + r);
            return (dx * dx + dy * dy) <= (r * r);
        }
        return mx >= this.x && mx <= this.x + this.width &&
            my >= this.y && my <= this.y + this.height;
    }

    getCenter() {
        if (this.type === 'circle') {
            const r = this.width / 2;
            return { x: this.x + r, y: this.y + r };
        }
        return { x: this.x + this.width / 2, y: this.y + this.height / 2 };
    }
}

class Connection {
    constructor(fromId, toId) {
        this.fromId = fromId;
        this.toId = toId;
    }

    draw(ctx) {
        const fromShape = shapes.find(s => s.id === this.fromId);
        const toShape = shapes.find(s => s.id === this.toId);
        if (!fromShape || !toShape) return;

        const start = fromShape.getCenter();
        const end = toShape.getCenter();

        // Dibujar línea
        ctx.beginPath();
        ctx.moveTo(start.x, start.y);
        ctx.lineTo(end.x, end.y);
        ctx.strokeStyle = '#64748b';
        ctx.lineWidth = 2;
        ctx.stroke();

        // Dibujar flecha
        const angle = Math.atan2(end.y - start.y, end.x - start.x);
        const headLength = 10;

        ctx.beginPath();
        ctx.moveTo(end.x, end.y);
        ctx.lineTo(end.x - headLength * Math.cos(angle - Math.PI / 6), end.y - headLength * Math.sin(angle - Math.PI / 6));
        ctx.moveTo(end.x, end.y);
        ctx.lineTo(end.x - headLength * Math.cos(angle + Math.PI / 6), end.y - headLength * Math.sin(angle + Math.PI / 6));
        ctx.stroke();
    }
}

// --- LÓGICA DE HERRAMIENTAS ---

window.setMode = (mode) => {
    currentMode = mode;
    modeDisplay.textContent = mode.charAt(0).toUpperCase() + mode.slice(1);

    // Actualizar botones visualmente
    document.querySelectorAll('.tool-btn').forEach(btn => btn.classList.remove('active'));
    const btnMap = {
        'rect': 'Rectángulo', 'pill': 'Píldora', 'circle': 'Círculo',
        'connect': 'Conectar', 'select': 'Mover'
    };
    // Forma simple de buscar el botón activo (podría mejorarse con IDs)
    document.querySelectorAll('.tool-btn').forEach(btn => {
        if (btn.textContent.includes(btnMap[mode])) {
            btn.classList.add('active');
        }
    });

    // Resetear selecciones temporales
    if (mode !== 'select') selectedShape = null;
    startShape = null;
    draw();
};

window.deleteSelected = () => {
    if (selectedShape) {
        // Borrar conexiones asociadas
        connections = connections.filter(c => c.fromId !== selectedShape.id && c.toId !== selectedShape.id);
        // Borrar forma
        shapes = shapes.filter(s => s.id !== selectedShape.id);
        selectedShape = null;
        draw();
    }
};

// --- LOOP DE DIBUJO ---

function draw() {
    ctx.clearRect(0, 0, canvas.width, canvas.height); // Limpiar

    // Dibujar conexiones primero (para que queden detrás)
    connections.forEach(c => c.draw(ctx));

    // Dibujar línea temporal si estamos conectando
    if (currentMode === 'connect' && startShape) {
        // Obtenemos coordenadas mouse (necesitaríamos guardarlas globalmente en mousemove, 
        // pero para simplificar, redibujaremos en el evento mousemove)
    }

    // Dibujar formas
    shapes.forEach(s => s.draw(ctx, selectedShape && s.id === selectedShape.id));
}

// --- EVENTOS DEL MOUSE ---

// Función auxiliar para coordenadas relativas al canvas
function getMousePos(evt) {
    const rect = canvas.getBoundingClientRect();
    return {
        x: evt.clientX - rect.left,
        y: evt.clientY - rect.top
    };
}

canvas.addEventListener('mousedown', (e) => {
    const { x, y } = getMousePos(e);

    // Buscar si hicimos clic en una forma (de arriba a abajo en el stack)
    const clickedShape = shapes.slice().reverse().find(s => s.contains(x, y));

    if (currentMode === 'select') {
        selectedShape = clickedShape || null;
        if (selectedShape) {
            isDragging = true;
            dragOffsetX = x - selectedShape.x;
            dragOffsetY = y - selectedShape.y;
        }
    } else if (currentMode === 'connect') {
        if (clickedShape) {
            startShape = clickedShape;
        }
    } else {
        // Modos de creación (rect, pill, circle)
        const newShape = new Shape(x - 50, y - 30, currentMode); // Centrado aprox
        shapes.push(newShape);
        selectedShape = newShape;
        setMode('select'); // Volver a modo selección automáticamente
    }
    draw();
});

canvas.addEventListener('mousemove', (e) => {
    const { x, y } = getMousePos(e);

    if (currentMode === 'select' && isDragging && selectedShape) {
        selectedShape.x = x - dragOffsetX;
        selectedShape.y = y - dragOffsetY;
        draw();
    } else if (currentMode === 'connect' && startShape) {
        // Redibujar todo + línea elástica
        draw();

        const start = startShape.getCenter();
        ctx.beginPath();
        ctx.moveTo(start.x, start.y);
        ctx.lineTo(x, y);
        ctx.strokeStyle = '#94a3b8';
        ctx.setLineDash([5, 5]); // Línea punteada para preview
        ctx.stroke();
        ctx.setLineDash([]);
    }
});

canvas.addEventListener('mouseup', (e) => {
    const { x, y } = getMousePos(e);

    if (currentMode === 'select') {
        isDragging = false;
    } else if (currentMode === 'connect' && startShape) {
        const endShape = shapes.slice().reverse().find(s => s.contains(x, y));
        if (endShape && endShape.id !== startShape.id) {
            // Crear conexión
            connections.push(new Connection(startShape.id, endShape.id));
        }
        startShape = null;
        draw();
    }
});

// --- PERSISTENCIA (JSON) ---

window.saveDiagram = () => {
    const data = {
        shapes: shapes,
        connections: connections
    };
    const json = JSON.stringify(data, null, 2);
    const blob = new Blob([json], { type: 'application/json' });
    const url = URL.createObjectURL(blob);

    const a = document.createElement('a');
    a.href = url;
    a.download = 'diagrama.json';
    a.click();
    URL.revokeObjectURL(url);
};

window.loadDiagram = (input) => {
    const file = input.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = (e) => {
        try {
            const data = JSON.parse(e.target.result);

            // Reconstruir objetos Shapes
            shapes = data.shapes.map(sData => {
                const s = new Shape(sData.x, sData.y, sData.type);
                s.id = sData.id; // Mantener ID para conexiones
                s.width = sData.width;
                s.height = sData.height;
                return s;
            });

            // Reconstruir conexiones
            connections = data.connections.map(cData => new Connection(cData.fromId, cData.toId));

            draw();
            alert('Diagrama cargado correctamente.');
        } catch (err) {
            alert('Error al cargar el archivo JSON.');
            console.error(err);
        }
    };
    reader.readAsText(file);
    input.value = ''; // Resetear input
};
