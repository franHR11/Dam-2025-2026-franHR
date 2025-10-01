// Registro global de scripts dinámicos ya cargados para evitar duplicados
window.__loadedDynamicScripts = window.__loadedDynamicScripts || new Set();

// Función para cargar contenido dinámicamente
function loadPage(url) {
    const contentArea = document.getElementById('content-area');
    if (!contentArea) {
        console.error('No se encontró el área de contenido');
        return;
    }

    // Mostrar indicador de carga
    contentArea.innerHTML = '<div class="loading">Cargando...</div>';

    fetch(url)
        .then(response => response.text())
        .then(html => {
            contentArea.innerHTML = html;
            // Si se trata del módulo Kanban, forzar re-inicialización para re-vincular eventos y renderizar
            try {
                const hasKanban = !!document.getElementById('kanban-board') || !!document.getElementById('kanban-content');
                if (hasKanban) {
                    if (typeof window.initKanban === 'function') {
                        window.initKanban();
                    } else if (window.kanban) {
                        if (typeof window.kanban.setupEventListeners === 'function') window.kanban.setupEventListeners();
                        if (typeof window.kanban.renderBoard === 'function') window.kanban.renderBoard();
                    }
                }
            } catch (e) {
                console.warn('Reinicialización de Kanban fallida:', e);
            }
            // Ejecutar scripts que puedan estar en el contenido cargado, manejando src y verificando sintaxis
            const scripts = contentArea.querySelectorAll('script');
            scripts.forEach(script => {
                // Clonar atributos relevantes
                const type = script.getAttribute('type') || '';
                const hasSrc = !!script.getAttribute('src');

                if (hasSrc) {
                    const srcAttr = script.getAttribute('src');
                    // Resolver URL absoluta basada en el documento actual
                    let absoluteSrc;
                    try {
                        absoluteSrc = new URL(srcAttr, document.baseURI).href;
                    } catch (e) {
                        absoluteSrc = srcAttr; // fallback
                    }

                    // Evitar cargar el mismo script src más de una vez
                    if (window.__loadedDynamicScripts.has(absoluteSrc)) {
                        script.remove();
                        return;
                    }

                    const newScript = document.createElement('script');
                    newScript.src = srcAttr;
                    if (type) newScript.type = type;
                    if (script.defer) newScript.defer = true;
                    if (script.async) newScript.async = true;
                    document.head.appendChild(newScript);
                    window.__loadedDynamicScripts.add(absoluteSrc);
                } else {
                    const code = (script.textContent || '').trim();
                    if (!code) {
                        // No hay contenido ejecutable
                        script.remove();
                        return;
                    }
                    // Validar sintaxis antes de inyectar para evitar SyntaxError al appendChild
                    try {
                        // new Function lanza si hay error de sintaxis
                        // No ejecuta el código, solo valida que es parseable
                        // eslint-disable-next-line no-new-func
                        new Function(code);
                    } catch (e) {
                        console.error('Script dinámico con error de sintaxis, se omite:', e);
                        script.remove();
                        return;
                    }

                    // Generar una firma simple del código para evitar re-ejecución
                    const codeSignature = 'inline:' + code.length + ':' + code.slice(0, 200);
                    if (window.__loadedDynamicScripts.has(codeSignature)) {
                        script.remove();
                        return;
                    }

                    const newScript = document.createElement('script');
                    if (type) newScript.type = type;
                    newScript.textContent = code;
                    document.head.appendChild(newScript);
                    window.__loadedDynamicScripts.add(codeSignature);
                }
                // Evitar doble ejecución
                script.remove();
            });
        })
        .catch(error => {
            console.error('Error al cargar la página:', error);
            contentArea.innerHTML = '<div class="error">Error al cargar el contenido</div>';
        });
}

// Cargar categorías del menú desde API (requiere API_BASE_URL en CONFIG)
(function () {
    const apiBase = window.CONFIG?.API_BASE_URL;
    if (!apiBase) {
        console.error('API_BASE_URL no configurada en CONFIG. No se puede cargar el menú.');
        return;
    }

    fetch(apiBase + 'componentes/listado-de-modulos/listadoModulos.php?ruta=categorias', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json'
        }
    })
        .then(function (respuesta) {
            return respuesta.json();
        })
        .then(function (data) {
            if (data.success) {
                let listadoCard = document.getElementById('menu');
                listadoCard.innerHTML = ''; // Limpiar contenido existente
                data.data.forEach(function (listadoMenu) {
                    let nav = document.createElement('nav');
                    // Determinar la URL correcta según el tipo de enlace
                    let targetUrl = listadoMenu.enlace;

                    // Si es el enlace de home/escritorio, usar la versión de contenido dinámico
                    if (listadoMenu.enlace.includes('listadoModulos.php')) {
                        targetUrl = '../componentes/listadoModulos/listadoModulos-content.php';
                    }
                    // Si es el enlace de kanban, corregir la ruta relativa
                    else if (listadoMenu.enlace.includes('kanban-content.php')) {
                        targetUrl = '../Paginas/kanban/kanban-content.php';
                    }

                    nav.innerHTML = `
                    <ul>
            <li>
                <a href="#" onclick="loadPage('${targetUrl}'); return false;">
                        <i class="${listadoMenu.icono}"></i>
                        <span>${listadoMenu.nombre}</span>
                    </a>
            </li>
            </ul>
                `;
                    listadoCard.appendChild(nav);
                });
            } else {
                console.error('Error al obtener las categorias:', data.message);
            }
        })
        .catch(function (err) {
            console.error('Error solicitando categorías del menú:', err);
        });
})();
