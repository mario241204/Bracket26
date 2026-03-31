document.addEventListener('DOMContentLoaded', () => {

    // --- 0. Helper Functions (Escapado de HTML) ---
    const escapeHTML = (str) => {
        if (!str) return '';
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return String(str).replace(/[&<>"']/g, m => map[m]);
    };


    // --- 1. Tab Navigation Config ---
    const navButtons = document.querySelectorAll('.nav-btn');
    const tabContents = document.querySelectorAll('.tab-content');

    // Manejo de clicks en el Bottom Navigation
    navButtons.forEach(btn => {
        btn.addEventListener('click', (e) => {
            // Prevenir comportamiento default
            e.preventDefault();

            // Resetear todos los tabs
            navButtons.forEach(b => b.classList.remove('active'));
            tabContents.forEach(t => t.classList.remove('active'));

            // Activar tab seleccionado
            const currentBtn = e.currentTarget;
            currentBtn.classList.add('active');

            const targetId = currentBtn.getAttribute('data-target');
            const targetContent = document.getElementById(targetId);

            if (targetContent) {
                targetContent.classList.add('active');
            }
        });
    });

    // --- 2. Acordeón para Grupos (Delegación de eventos) ---
    // Delegamos en el contenedor para soportar DOM dinámico si luego conectamos al API
    const gruposContainer = document.getElementById('vista-grupos');

    if (gruposContainer) {
        gruposContainer.addEventListener('click', (e) => {
            // Buscamos si el click fue dentro de un .row-main
            const rowMain = e.target.closest('.row-main');

            if (rowMain) {
                const grupoRow = rowMain.parentElement;

                // Opcional: Cerrar cualquier otro acordeón abierto para mantener limpieza visual
                const allExpanded = document.querySelectorAll('.grupo-row.expanded');
                allExpanded.forEach(row => {
                    if (row !== grupoRow) {
                        row.classList.remove('expanded');
                    }
                });

                // Toglear el actual
                grupoRow.classList.toggle('expanded');
            }
        });
    }

    // --- 3. Tooltips de la Pirámide (Eliminatorias) ---
    const eliminatoriasContainer = document.getElementById('vista-eliminatorias');

    if (eliminatoriasContainer) {
        eliminatoriasContainer.addEventListener('click', (e) => {
            const vsBtn = e.target.closest('.vs-btn');

            if (vsBtn) {
                const wrapper = vsBtn.parentElement;

                // Cerrar cualquier otro tooltip abierto
                const allTooltips = document.querySelectorAll('.vs-btn-wrapper.mostrar');
                allTooltips.forEach(w => {
                    if (w !== wrapper) {
                        w.classList.remove('mostrar');
                    }
                });

                // Toggle al bocadillo actual
                wrapper.classList.toggle('mostrar');

                // Si se abrió, configuro un temporizador para cerrarlo solo
                if (wrapper.classList.contains('mostrar')) {
                    // Limpiar el timeout anterior si el usuario toca rápido
                    if (wrapper.dataset.timeoutId) {
                        clearTimeout(wrapper.dataset.timeoutId);
                    }

                    const timeoutId = setTimeout(() => {
                        wrapper.classList.remove('mostrar');
                    }, 3500);

                    wrapper.dataset.timeoutId = timeoutId;
                }
            }
        });
    }

    // Mejora UI: Ocultar tooltips automáticamente si el usuario toca en el fondo
    document.addEventListener('click', (e) => {
        // Si el toque no fue en un botón VS ni en su wrapper, cerramos todo
        if (!e.target.closest('.vs-btn-wrapper')) {
            document.querySelectorAll('.vs-btn-wrapper.mostrar').forEach(w => {
                w.classList.remove('mostrar');
                if (w.dataset.timeoutId) {
                    clearTimeout(w.dataset.timeoutId);
                }
            });
        }
    });

    // --- 4. Cargar Grupos desde API ---
    async function cargarGrupos() {
        try {
            const response = await fetch('api.php?action=get_grupos');
            if (!response.ok) throw new Error('Error en la respuesta de la red');

            const result = await response.json();
            if (result.status === 'success') {
                renderizarGrupos(result.data);
            }
        } catch (error) {
            console.error('Error al cargar grupos:', error);
            const gruposContainer = document.getElementById('vista-grupos');
            if (gruposContainer) {
                gruposContainer.innerHTML = '<p style="text-align:center; color: var(--text-secondary); padding: 20px;">Error cargando los grupos.</p>';
            }
        }
    }

    function renderizarGrupos(gruposData) {
        const vistaGrupos = document.getElementById('vista-grupos');
        if (!vistaGrupos) return;

        vistaGrupos.innerHTML = ''; // Limpiar mocks

        for (const [letraGrupo, equipos] of Object.entries(gruposData)) {
            // Contenedor de la tarjeta oscura
            const grupoContainer = document.createElement('div');
            grupoContainer.className = 'grupo-container';

            // Título del grupo
            const title = document.createElement('h2');
            title.className = 'grupo-title';
            title.textContent = `Grupo ${letraGrupo}`;
            grupoContainer.appendChild(title);

            // Tabla de equipos
            const table = document.createElement('div');
            table.className = 'grupo-table';

            equipos.forEach((equipo, index) => {
                const pos = index + 1; // 1, 2, 3, 4

                let partidosHtml = '';
                if (equipo.partidos && equipo.partidos.length > 0) {
                    equipo.partidos.forEach(partido => {
                        const dateObj = new Date(partido.fecha_hora.replace(' ', 'T'));
                        let textoFecha = 'Fecha pdte.';
                        if (!isNaN(dateObj)) {
                            const day = String(dateObj.getDate()).padStart(2, '0');
                            const month = String(dateObj.getMonth() + 1).padStart(2, '0');
                            const year = dateObj.getFullYear();
                            textoFecha = `${day}/${month}/${year}`;
                        }

                        partidosHtml += `<li class="partido-item">📅 <span class="p-date">${escapeHTML(textoFecha)}</span> | vs&nbsp;<span class="p-rival">${escapeHTML(partido.rival_nombre)}</span></li>`;
                    });
                } else {
                    partidosHtml = '<li class="partido-item">⏳ A la espera del calendario...</li>';
                }

                const row = document.createElement('div');
                row.className = 'grupo-row';
                row.dataset.id = equipo.id;

                row.innerHTML = `
                    <div class="row-main">
                        <span class="pos">${pos}</span>
                        <img src="${escapeHTML(equipo.bandera_url)}" alt="${escapeHTML(equipo.nombre)}" class="flag-img" loading="lazy">
                        <span class="name">${escapeHTML(equipo.nombre)}</span>
                        <span class="pts">${escapeHTML(equipo.puntos_totales)} pts</span>
                    </div>
                    <div class="row-details">
                        <div class="details-content">
                            <ul class="lista-partidos">
                                ${partidosHtml}
                            </ul>
                        </div>
                    </div>
                `;
                table.appendChild(row);
            });

            grupoContainer.appendChild(table);
            vistaGrupos.appendChild(grupoContainer);
        }
    }

    // Inicializar cargas
    cargarGrupos();

    // --- 5. Navegación Fases Eliminatorias ---
    const faseBtns = document.querySelectorAll('.fase-btn');
    const matchContainer = document.getElementById('fase-match-container');
    let eliminatoriasData = null; // Guardar datos cacheados en memoria

    if (faseBtns.length > 0 && matchContainer) {

        // Mapeo del data-fase del HTML al key que llega en el JSON
        const mapFases = {
            '32': { key: '1/16', gold: false },
            '16': { key: 'Octavos', gold: false },
            '8': { key: 'Cuartos', gold: false },
            '4': { key: 'Semis', gold: false },
            '2': { key: 'Final', gold: true }
        };

        const renderFase = (faseNum) => {
            if (!eliminatoriasData) return;

            const conf = mapFases[faseNum];
            if (!conf) return;

            matchContainer.innerHTML = '';

            const roundDiv = document.createElement('div');
            roundDiv.className = `eliminatorias-round round-${faseNum}`;

            const partidosDeEstaFase = eliminatoriasData[conf.key] || [];

            if (partidosDeEstaFase.length === 0) {
                roundDiv.innerHTML = '<p style="text-align:center; color: var(--text-secondary); grid-column: 1 / -1; margin-top: 40px; font-size: 14px;">Aún no hay cruces definidos para esta ronda.</p>';
            }

            partidosDeEstaFase.forEach(partido => {
                const node = document.createElement('div');
                let extraClass = '';
                if (conf.gold) extraClass += ' final-node';
                if (partido.estado === 'finalizado') extraClass += ' match-card-finished';

                node.className = 'match-node' + extraClass;

                const qClass = conf.gold ? 'team-q gold' : 'team-q';
                const btnClass = conf.gold ? 'vs-btn gold-btn' : 'vs-btn';

                // Determinar Inyección Visual de Bandera Local o Interrogante
                const htmlLocal = partido.local_bandera
                    ? `<img src="${escapeHTML(partido.local_bandera)}" alt="${escapeHTML(partido.local_nombre)}" class="flag-img" loading="lazy" style="margin:0; width:34px; height:34px;">`
                    : `<span class="${qClass}">?</span>`;

                // Determinar Inyección Visual de Bandera Visitante o Interrogante
                const htmlVisitante = partido.visitante_bandera
                    ? `<img src="${escapeHTML(partido.visitante_bandera)}" alt="${escapeHTML(partido.visitante_nombre)}" class="flag-img" loading="lazy" style="margin:0; width:34px; height:34px;">`
                    : `<span class="${qClass}">?</span>`;

                let tooltipInfo = '- : -';
                if (partido.estado === 'finalizado') {
                    tooltipInfo = `${escapeHTML(partido.goles_local)} - ${escapeHTML(partido.goles_visitante)}`;
                } else if (partido.estado === 'pendiente') {
                    tooltipInfo = 'Pendiente';
                }

                node.innerHTML = `
                    ${htmlLocal}
                    <div class="vs-btn-wrapper">
                        <button class="${btnClass}">VS</button>
                        <div class="bocadillo-resultado">${escapeHTML(tooltipInfo)}</div>
                    </div>
                    ${htmlVisitante}
                `;
                roundDiv.appendChild(node);
            });

            matchContainer.appendChild(roundDiv);
        };

        faseBtns.forEach(btn => {
            btn.addEventListener('click', (e) => {
                faseBtns.forEach(b => b.classList.remove('active'));
                const clickedBtn = e.currentTarget;
                clickedBtn.classList.add('active');

                // Mover el contenedor al inicio del padre para que el usuario que haya bajado lo vea desde arriba
                const vistaEliminatorias = document.getElementById('vista-eliminatorias');
                renderFase(clickedBtn.dataset.fase);

                if (window.scrollY > 150) {
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
            });
        });

        async function cargarEliminatorias() {
            try {
                const response = await fetch('api.php?action=get_eliminatorias');
                if (!response.ok) throw new Error('Error en la red');
                const result = await response.json();

                if (result.status === 'success') {
                    eliminatoriasData = result.data;
                    renderFase('32'); // Cargar Dieciseisavos por defecto cuando llegan los datos
                }
            } catch (error) {
                console.error('Error al cargar las eliminatorias:', error);
                matchContainer.innerHTML = '<p style="text-align:center; color: var(--text-secondary); margin-top:20px;">Error cargando los cruces del servidor.</p>';
            }
        }

        cargarEliminatorias();
    }

});

// --- 6. Registro de Service Worker para PWA ---
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('sw.js')
            .then(reg => console.log('[PWA] Service Worker registrado', reg))
            .catch(err => console.log('[PWA] Error al registrar SW', err));
    });
}

// --- 7. Firma de Desarrollador BRACKET26 ---
(function () {
    const asciiArt = `
    ██████╗ ██████╗  █████╗  ██████╗██╗  ██╗███████╗████████╗██████╗  ██████╗ 
    ██╔══██╗██╔══██╗██╔══██╗██╔════╝██║ ██╔╝██╔════╝╚══██╔══╝╚════██╗██╔════╝ 
    ██████╔╝██████╔╝███████║██║     █████╔╝ █████╗     ██║    █████╔╝███████╗ 
    ██╔══██╗██╔══██╗██╔══██║██║     ██╔═██╗ ██╔══╝     ██║   ██╔═══╝ ██╔═══██╗
    ██████╔╝██║  ██║██║  ██║╚██████╗██║  ██╗███████╗   ██║   ███████╗╚██████╔╝
    ╚═════╝ ╚═╝  ╚═╝╚═╝  ╚═╝ ╚═════╝╚═╝  ╚═╝╚══════╝   ╚═╝   ╚══════╝ ╚═════╝ 
    `;

    console.log("%c" + asciiArt, "color: #3b82f6; font-weight: bold; text-shadow: 0 0 10px rgba(59, 130, 246, 0.4);");
    console.log(
        "%cPWA DESARROLLADA POR: MARIO CÁRCEL NAVARRO%c",
        "background: #0f172a; color: #ffffff; padding: 15px 25px; border-radius: 10px; border: 2px solid #3b82f6; font-size: 14px; font-weight: bold; font-family: 'Outfit', sans-serif; display: block; margin: 10px 0;",
        "color: #fbbf24; font-weight: 900; letter-spacing: 1px;"
    );
})();
