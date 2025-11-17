class UsuariosPage {
    constructor() {
        this.apiBase = '../../api/usuarios/';
        this.usuarios = [];
        this.currentPage = 1;
        this.itemsPerPage = 10;
        this.currentUsuario = null;
        this.pendingAction = null;
        this.modales = {
            form: null,
            detalles: null,
            confirmar: null,
        };
        this.elements = {};
        this.init();
    }

    init() {
        this.cacheDom();
        this.setupModals();
        this.bindEvents();
        this.loadUsuarios();
    }

    cacheDom() {
        this.elements = {
            tbody: document.getElementById('usuarios-tbody'),
            noUsuarios: document.getElementById('no-usuarios'),
            pagination: document.getElementById('pagination'),
            desde: document.getElementById('usuarios-desde'),
            hasta: document.getElementById('usuarios-hasta'),
            total: document.getElementById('usuarios-total'),
            filtros: {
                busqueda: document.getElementById('buscar-usuario'),
                rol: document.getElementById('filtro-rol'),
                estado: document.getElementById('filtro-estado'),
            },
            form: {
                id: document.getElementById('usuario-id'),
                usuario: document.getElementById('usuario'),
                email: document.getElementById('email'),
                nombre: document.getElementById('nombre'),
                apellidos: document.getElementById('apellidos'),
                telefono: document.getElementById('telefono'),
                rol: document.getElementById('rol'),
                contrasena: document.getElementById('contrasena'),
                activo: document.getElementById('activo'),
                passwordHint: document.getElementById('password-hint'),
            },
            detallesContent: document.getElementById('detalles-usuario-content'),
            confirmarTitle: document.getElementById('modal-confirmar-title'),
            confirmarMessage: document.getElementById('modal-confirmar-message'),
            actionButton: document.getElementById('confirmar-accion'),
            guardarBtn: document.getElementById('guardar-usuario'),
            nuevoBtn: document.getElementById('nuevo-usuario-btn'),
            importarBtn: document.getElementById('importar-usuarios-btn'),
            exportarBtn: document.getElementById('exportar-usuarios-btn'),
        };
    }

    setupModals() {
        this.modales.form = new bootstrap.Modal(document.getElementById('modal-usuario'));
        this.modales.detalles = new bootstrap.Modal(document.getElementById('modal-detalles'));
        this.modales.confirmar = new bootstrap.Modal(document.getElementById('modal-confirmar'));
    }

    bindEvents() {
        this.elements.nuevoBtn?.addEventListener('click', () => this.nuevoUsuario());
        this.elements.guardarBtn?.addEventListener('click', () => this.guardarUsuario());
        this.elements.actionButton?.addEventListener('click', () => {
            if (typeof this.pendingAction === 'function') {
                this.pendingAction();
            }
        });

        this.elements.filtros.busqueda?.addEventListener('input', this.debounce(() => {
            this.currentPage = 1;
            this.loadUsuarios();
        }, 400));

        ['rol', 'estado'].forEach((filtro) => {
            this.elements.filtros[filtro]?.addEventListener('change', () => {
                this.currentPage = 1;
                this.loadUsuarios();
            });
        });

        this.elements.importarBtn?.addEventListener('click', () => this.showNotification('Funcionalidad de importar próximamente', 'info'));
        this.elements.exportarBtn?.addEventListener('click', () => this.exportarUsuarios());
    }

    async loadUsuarios() {
        try {
            const params = new URLSearchParams();
            const busqueda = this.elements.filtros.busqueda?.value.trim();
            const rol = this.elements.filtros.rol?.value;
            const estado = this.elements.filtros.estado?.value;

            if (busqueda) params.append('busqueda', busqueda);
            if (rol) params.append('rol', rol);
            if (estado) params.append('estado', estado);

            const response = await fetch(`${this.apiBase}obtener_usuarios.php?${params.toString()}`);
            const data = await response.json();

            if (data.ok) {
                this.usuarios = data.usuarios || [];
                this.renderTabla();
            } else {
                throw new Error(data.error || 'No se pudieron cargar los usuarios');
            }
        } catch (error) {
            console.error('Error cargando usuarios:', error);
            this.showNotification(error.message, 'danger');
        }
    }

    getUsuariosPaginados() {
        const start = (this.currentPage - 1) * this.itemsPerPage;
        return this.usuarios.slice(start, start + this.itemsPerPage);
    }

    renderTabla() {
        if (!this.elements.tbody) return;

        const usuariosPaginados = this.getUsuariosPaginados();

        if (usuariosPaginados.length === 0) {
            this.elements.tbody.innerHTML = '';
            this.elements.noUsuarios.style.display = 'block';
        } else {
            this.elements.noUsuarios.style.display = 'none';
            this.elements.tbody.innerHTML = usuariosPaginados
                .map((usuario, index) => this.renderFila(usuario, index))
                .join('');
        }

        const total = this.usuarios.length;
        const desde = total === 0 ? 0 : (this.currentPage - 1) * this.itemsPerPage + 1;
        const hasta = Math.min(this.currentPage * this.itemsPerPage, total);

        if (this.elements.desde) this.elements.desde.textContent = desde;
        if (this.elements.hasta) this.elements.hasta.textContent = hasta;
        if (this.elements.total) this.elements.total.textContent = total;

        this.renderPaginacion(total);
    }

    renderFila(usuario, index) {
        const contador = (this.currentPage - 1) * this.itemsPerPage + index + 1;
        const nombreCompleto = usuario.nombre_completo?.trim() || `${usuario.nombre || ''} ${usuario.apellidos || ''}`.trim() || '—';
        const rolBadge = this.getRolBadge(usuario.rol);
        const estadoBadge = this.getEstadoBadge(usuario.activo);
        const ultimoAcceso = usuario.fecha_ultimo_login ? this.formatDate(usuario.fecha_ultimo_login) : '—';

        return `
            <tr>
                <td>${contador}</td>
                <td><strong>${usuario.usuario}</strong></td>
                <td>${nombreCompleto}</td>
                <td>${usuario.email || '—'}</td>
                <td>${rolBadge}</td>
                <td>${ultimoAcceso}</td>
                <td>${estadoBadge}</td>
                <td>
                    <button class="btn btn-action btn-ver" title="Ver" onclick="usuariosPage.verDetalles(${usuario.id})">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-action btn-editar" title="Editar" onclick="usuariosPage.editarUsuario(${usuario.id})">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-action btn-eliminar" title="Eliminar" onclick="usuariosPage.confirmarEliminacion(${usuario.id})">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
    }

    renderPaginacion(totalItems) {
        if (!this.elements.pagination) return;
        const totalPages = Math.ceil(totalItems / this.itemsPerPage) || 1;
        let html = '';

        const createItem = (page, label, disabled = false, active = false) => `
            <li class="page-item ${disabled ? 'disabled' : ''} ${active ? 'active' : ''}">
                <a class="page-link" href="#" onclick="usuariosPage.cambiarPagina(${page}); return false;">${label}</a>
            </li>`;

        html += createItem(Math.max(1, this.currentPage - 1), '<i class="fas fa-chevron-left"></i>', this.currentPage === 1);

        for (let i = 1; i <= totalPages; i++) {
            if (i === 1 || i === totalPages || (i >= this.currentPage - 2 && i <= this.currentPage + 2)) {
                html += createItem(i, i, false, i === this.currentPage);
            } else if (i === this.currentPage - 3 || i === this.currentPage + 3) {
                html += '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
        }

        html += createItem(Math.min(totalPages, this.currentPage + 1), '<i class="fas fa-chevron-right"></i>', this.currentPage === totalPages);
        this.elements.pagination.innerHTML = html;
    }

    cambiarPagina(page) {
        const totalPages = Math.ceil(this.usuarios.length / this.itemsPerPage) || 1;
        if (page >= 1 && page <= totalPages) {
            this.currentPage = page;
            this.renderTabla();
        }
    }

    nuevoUsuario() {
        this.currentUsuario = null;
        this.resetFormulario();
        this.elements.form.passwordHint.textContent = '(obligatoria)';
        document.getElementById('modal-usuario-title').textContent = 'Nuevo usuario';
        this.modales.form.show();
    }

    editarUsuario(id) {
        const usuario = this.usuarios.find((u) => Number(u.id) === Number(id));
        if (!usuario) return;

        this.currentUsuario = usuario;
        this.loadFormulario(usuario);
        this.elements.form.passwordHint.textContent = '(dejar vacío para mantener)';
        document.getElementById('modal-usuario-title').textContent = 'Editar usuario';
        this.modales.form.show();
    }

    loadFormulario(usuario) {
        const { form } = this.elements;
        form.id.value = usuario.id || '';
        form.usuario.value = usuario.usuario || '';
        form.email.value = usuario.email || '';
        form.nombre.value = usuario.nombre || '';
        form.apellidos.value = usuario.apellidos || '';
        form.telefono.value = usuario.telefono || '';
        form.rol.value = usuario.rol || '';
        form.contrasena.value = '';
        form.activo.checked = Number(usuario.activo) === 1;
    }

    resetFormulario() {
        const { form } = this.elements;
        Object.values(form).forEach((el) => {
            if (!el || el === form.passwordHint) return;
            if (el.type === 'checkbox') {
                el.checked = true;
            } else {
                el.value = '';
            }
        });
        form.activo.checked = true;
    }

    async guardarUsuario() {
        const { form } = this.elements;
        const payload = {
            usuario: form.usuario.value.trim(),
            email: form.email.value.trim(),
            nombre: form.nombre.value.trim(),
            apellidos: form.apellidos.value.trim(),
            telefono: form.telefono.value.trim(),
            rol: form.rol.value,
            activo: form.activo.checked ? 1 : 0,
        };

        const contrasena = form.contrasena.value.trim();
        const esNuevo = !form.id.value;

        if (esNuevo && !contrasena) {
            this.showNotification('La contraseña es obligatoria para nuevos usuarios', 'warning');
            form.contrasena.focus();
            return;
        }

        if (contrasena) {
            payload.contrasena = contrasena;
        }

        if (!payload.usuario || !payload.email || !payload.rol) {
            this.showNotification('Usuario, email y rol son obligatorios', 'warning');
            return;
        }

        let url = `${this.apiBase}guardar_usuario.php`;
        if (!esNuevo) {
            url = `${this.apiBase}actualizar_usuario.php`;
            payload.id = form.id.value;
        }

        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload),
            });
            const data = await response.json();

            if (data.ok) {
                this.showNotification(data.mensaje || 'Usuario guardado correctamente');
                this.modales.form.hide();
                this.loadUsuarios();
            } else {
                throw new Error(data.error || 'No se pudo guardar');
            }
        } catch (error) {
            console.error('Error guardando usuario:', error);
            this.showNotification(error.message, 'danger');
        }
    }

    verDetalles(id) {
        const usuario = this.usuarios.find((u) => Number(u.id) === Number(id));
        if (!usuario || !this.elements.detallesContent) return;

        const campos = [
            { label: 'Usuario', value: usuario.usuario },
            { label: 'Nombre completo', value: usuario.nombre_completo || `${usuario.nombre || ''} ${usuario.apellidos || ''}`.trim() },
            { label: 'Email', value: usuario.email },
            { label: 'Teléfono', value: usuario.telefono },
            { label: 'Rol', value: usuario.rol },
            { label: 'Estado', value: Number(usuario.activo) === 1 ? 'Activo' : 'Inactivo' },
            { label: 'Fecha creación', value: this.formatDate(usuario.fecha_creacion, true) },
            { label: 'Último login', value: usuario.fecha_ultimo_login ? this.formatDate(usuario.fecha_ultimo_login, true) : '—' },
        ];

        this.elements.detallesContent.innerHTML = `
            <div class="row g-3">
                ${campos
                    .map((campo) => `
                        <div class="col-md-6">
                            <p class="mb-1 text-muted">${campo.label}</p>
                            <h6>${campo.value || '—'}</h6>
                        </div>
                    `)
                    .join('')}
            </div>
        `;

        this.modales.detalles.show();
    }

    confirmarEliminacion(id) {
        const usuario = this.usuarios.find((u) => Number(u.id) === Number(id));
        if (!usuario) return;

        this.elements.confirmarTitle.textContent = 'Eliminar usuario';
        this.elements.confirmarMessage.innerHTML = `¿Seguro que deseas eliminar al usuario <strong>${usuario.usuario}</strong>?`;
        this.pendingAction = () => this.eliminarUsuario(id);
        this.modales.confirmar.show();
    }

    async eliminarUsuario(id) {
        try {
            const response = await fetch(`${this.apiBase}eliminar_usuario.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id }),
            });
            const data = await response.json();

            if (data.ok) {
                this.showNotification(data.mensaje || 'Usuario eliminado', 'success');
                this.modales.confirmar.hide();
                this.loadUsuarios();
            } else {
                throw new Error(data.error || 'No se pudo eliminar');
            }
        } catch (error) {
            console.error('Error eliminando usuario:', error);
            this.showNotification(error.message, 'danger');
        }
    }

    exportarUsuarios() {
        if (!this.usuarios.length) {
            this.showNotification('No hay datos para exportar', 'info');
            return;
        }

        const headers = ['Usuario', 'Nombre', 'Apellidos', 'Email', 'Teléfono', 'Rol', 'Estado'];
        const rows = this.usuarios.map((u) => [
            u.usuario,
            u.nombre || '',
            u.apellidos || '',
            u.email || '',
            u.telefono || '',
            u.rol,
            Number(u.activo) === 1 ? 'Activo' : 'Inactivo',
        ]);

        const csvContent = [headers, ...rows]
            .map((row) => row.map((cell) => `"${(cell || '').toString().replace(/"/g, '""')}"`).join(','))
            .join('\n');

        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const url = URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        link.download = `usuarios_${new Date().toISOString().slice(0, 10)}.csv`;
        link.click();
        URL.revokeObjectURL(url);
    }

    getRolBadge(rol) {
        if (!rol) return '<span class="badge-rol">—</span>';
        return `<span class="badge-rol ${rol}">${rol}</span>`;
    }

    getEstadoBadge(activo) {
        return Number(activo) === 1
            ? '<span class="badge-estado badge-activo">Activo</span>'
            : '<span class="badge-estado badge-inactivo">Inactivo</span>';
    }

    formatDate(dateString, includeTime = false) {
        if (!dateString) return '';
        const date = new Date(dateString.replace(' ', 'T'));
        if (Number.isNaN(date.getTime())) return dateString;
        const options = includeTime
            ? { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' }
            : { year: 'numeric', month: 'short', day: 'numeric' };
        return date.toLocaleDateString('es-ES', options);
    }

    showNotification(message, type = 'success') {
        const alert = document.createElement('div');
        alert.className = `alert alert-${type}`;
        alert.textContent = message;
        alert.style.position = 'fixed';
        alert.style.top = '20px';
        alert.style.right = '20px';
        alert.style.zIndex = 1056;
        document.body.appendChild(alert);
        setTimeout(() => alert.remove(), 3500);
    }

    debounce(fn, delay = 300) {
        let timer;
        return (...args) => {
            clearTimeout(timer);
            timer = setTimeout(() => fn.apply(this, args), delay);
        };
    }
}

let usuariosPage;
document.addEventListener('DOMContentLoaded', () => {
    usuariosPage = new UsuariosPage();
    window.usuariosPage = usuariosPage;
});
