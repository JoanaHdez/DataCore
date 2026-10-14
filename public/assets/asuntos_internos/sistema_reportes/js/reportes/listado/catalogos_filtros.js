/* =========================================================
   SISTEMA DE REPORTES - FILTROS CON CATALOGO VISUAL
========================================================= */

document.addEventListener(
    'DOMContentLoaded',
    () => {
        inicializarCatalogosFiltrosReportes();
    }
);

function inicializarCatalogosFiltrosReportes() {
    const selects =
        document.querySelectorAll(
            '.reportes-filtros__select'
        );

    selects.forEach(
        select => {
            inicializarCatalogoFiltroReportes(
                select
            );
        }
    );

    document.addEventListener(
        'click',
        evento => {
            if (
                evento.target.closest(
                    '.reportes-filtros-catalogo'
                )
            ) {
                return;
            }

            cerrarCatalogosFiltroReportes();
        }
    );

    document.addEventListener(
        'keydown',
        evento => {
            if (evento.key === 'Escape') {
                cerrarCatalogosFiltroReportes();
            }
        }
    );

    [
        'filtrosReportesLimpiados',
        'filtrosFelicitacionesLimpiados',
    ].forEach(
        nombreEvento => {
            document.addEventListener(
                nombreEvento,
                sincronizarCatalogosFiltroReportes
            );
        }
    );
}

function inicializarCatalogoFiltroReportes(
    select
) {
    if (
        !select
        || select.dataset.filtroCatalogoInicializado === 'true'
    ) {
        return;
    }

    const campo =
        select.closest(
            '.reportes-filtros__field'
        );

    const etiqueta =
        campo?.querySelector(
            'label'
        );

    const nombre =
        etiqueta?.textContent?.trim()
        || 'Filtro';

    const catalogo =
        document.createElement(
            'div'
        );

    catalogo.className =
        'reportes-filtros-catalogo';

    const boton =
        document.createElement(
            'button'
        );

    boton.type =
        'button';

    boton.className =
        'reportes-filtros-catalogo__selector';

    boton.setAttribute(
        'aria-expanded',
        'false'
    );

    boton.innerHTML = `
        <span class="reportes-filtros-catalogo__contenido">
            <span class="reportes-filtros-catalogo__avatar" aria-hidden="true"></span>
            <span class="reportes-filtros-catalogo__datos">
                <strong></strong>
                <small></small>
            </span>
        </span>
        <svg class="reportes-filtros-catalogo__flecha" viewBox="0 0 20 20" aria-hidden="true">
            <path d="M5 7.5L10 12.5L15 7.5"></path>
        </svg>
    `;

    const menu =
        document.createElement(
            'div'
        );

    menu.className =
        'reportes-filtros-catalogo__resultados';

    menu.hidden =
        true;

    select.parentNode.insertBefore(
        catalogo,
        select
    );

    catalogo.appendChild(
        select
    );

    catalogo.appendChild(
        boton
    );

    catalogo.appendChild(
        menu
    );

    select.classList.add(
        'reportes-filtros__select--visual'
    );

    select.dataset.filtroCatalogoInicializado =
        'true';

    catalogo.dataset.filtroCatalogoNombre =
        nombre;

    reconstruirOpcionesCatalogoFiltroReportes(
        catalogo
    );

    sincronizarCatalogoFiltroReportes(
        catalogo
    );

    boton.addEventListener(
        'click',
        evento => {
            evento.stopPropagation();

            const abierto =
                boton.getAttribute(
                    'aria-expanded'
                ) === 'true';

            cerrarCatalogosFiltroReportes(
                catalogo
            );

            if (abierto) {
                cerrarCatalogoFiltroReportes(
                    catalogo
                );
                return;
            }

            abrirCatalogoFiltroReportes(
                catalogo
            );
        }
    );

    boton.addEventListener(
        'keydown',
        evento => {
            if (
                evento.key !== 'Enter'
                && evento.key !== ' '
                && evento.key !== 'Spacebar'
            ) {
                return;
            }

            evento.preventDefault();
            boton.click();
        }
    );

    select.addEventListener(
        'change',
        () => {
            sincronizarCatalogoFiltroReportes(
                catalogo
            );
        }
    );

    const observador =
        new MutationObserver(
            () => {
                reconstruirOpcionesCatalogoFiltroReportes(
                    catalogo
                );

                sincronizarCatalogoFiltroReportes(
                    catalogo
                );
            }
        );

    observador.observe(
        select,
        {
            childList: true,
            subtree: true,
            attributes: true,
            attributeFilter: [
                'value',
                'selected',
                'disabled',
            ],
        }
    );
}

function reconstruirOpcionesCatalogoFiltroReportes(
    catalogo
) {
    const select =
        catalogo.querySelector(
            'select'
        );

    const menu =
        catalogo.querySelector(
            '.reportes-filtros-catalogo__resultados'
        );

    if (
        !select
        || !menu
    ) {
        return;
    }

    menu.textContent =
        '';

    Array.from(
        select.options
    ).forEach(
        option => {
            const item =
                document.createElement(
                    'button'
                );

            item.type =
                'button';

            item.className =
                'reportes-filtros-catalogo__item';

            item.dataset.value =
                option.value;

            item.innerHTML = `
                <span class="reportes-filtros-catalogo__avatar" aria-hidden="true"></span>
                <span class="reportes-filtros-catalogo__datos">
                    <strong></strong>
                    <small></small>
                </span>
            `;

            item.querySelector(
                '.reportes-filtros-catalogo__avatar'
            ).textContent =
                obtenerInicialCatalogoFiltroReportes(
                    option.textContent
                );

            item.querySelector(
                'strong'
            ).textContent =
                limpiarTextoCatalogoFiltroReportes(
                    option.textContent
                );

            item.querySelector(
                'small'
            ).textContent =
                option.value === ''
                    ? 'Sin filtro aplicado'
                    : catalogo.dataset.filtroCatalogoNombre
                    || 'Filtro';

            item.addEventListener(
                'click',
                evento => {
                    evento.stopPropagation();

                    select.value =
                        option.value;

                    sincronizarCatalogoFiltroReportes(
                        catalogo
                    );

                    cerrarCatalogoFiltroReportes(
                        catalogo
                    );

                    select.dispatchEvent(
                        new Event(
                            'change',
                            {
                                bubbles: true,
                            }
                        )
                    );
                }
            );

            menu.appendChild(
                item
            );
        }
    );
}

function sincronizarCatalogosFiltroReportes() {
    document
        .querySelectorAll(
            '.reportes-filtros-catalogo'
        )
        .forEach(
            catalogo => {
                sincronizarCatalogoFiltroReportes(
                    catalogo
                );
            }
        );
}

function sincronizarCatalogoFiltroReportes(
    catalogo
) {
    const select =
        catalogo.querySelector(
            'select'
        );

    const boton =
        catalogo.querySelector(
            '.reportes-filtros-catalogo__selector'
        );

    const texto =
        boton?.querySelector(
            'strong'
        );

    const descripcion =
        boton?.querySelector(
            'small'
        );

    const avatar =
        boton?.querySelector(
            '.reportes-filtros-catalogo__avatar'
        );

    const items =
        catalogo.querySelectorAll(
            '.reportes-filtros-catalogo__item'
        );

    if (
        !select
        || !boton
    ) {
        return;
    }

    const option =
        select.selectedOptions[0]
        || select.options[0]
        || null;

    const valor =
        option?.value
        ?? '';

    const etiqueta =
        limpiarTextoCatalogoFiltroReportes(
            option?.textContent
            || ''
        );

    if (texto) {
        texto.textContent =
            etiqueta;
    }

    if (descripcion) {
        descripcion.textContent =
            valor === ''
                ? 'Sin filtro aplicado'
                : catalogo.dataset.filtroCatalogoNombre
                || 'Filtro';
    }

    if (avatar) {
        avatar.textContent =
            obtenerInicialCatalogoFiltroReportes(
                etiqueta
            );
    }

    items.forEach(
        item => {
            const activo =
                item.dataset.value === valor;

            item.classList.toggle(
                'reportes-filtros-catalogo__item--activo',
                activo
            );

            item.setAttribute(
                'aria-selected',
                activo ? 'true' : 'false'
            );
        }
    );
}

function abrirCatalogoFiltroReportes(
    catalogo
) {
    const boton =
        catalogo.querySelector(
            '.reportes-filtros-catalogo__selector'
        );

    const menu =
        catalogo.querySelector(
            '.reportes-filtros-catalogo__resultados'
        );

    if (
        !boton
        || !menu
    ) {
        return;
    }

    menu.hidden =
        false;

    boton.setAttribute(
        'aria-expanded',
        'true'
    );

    ajustarAlturaCatalogoFiltroReportes(
        catalogo
    );
}

function cerrarCatalogoFiltroReportes(
    catalogo
) {
    const boton =
        catalogo.querySelector(
            '.reportes-filtros-catalogo__selector'
        );

    const menu =
        catalogo.querySelector(
            '.reportes-filtros-catalogo__resultados'
        );

    if (
        !boton
        || !menu
    ) {
        return;
    }

    menu.hidden =
        true;

    boton.setAttribute(
        'aria-expanded',
        'false'
    );
}

function cerrarCatalogosFiltroReportes(
    excepcion = null
) {
    document
        .querySelectorAll(
            '.reportes-filtros-catalogo'
        )
        .forEach(
            catalogo => {
                if (
                    excepcion
                    && catalogo === excepcion
                ) {
                    return;
                }

                cerrarCatalogoFiltroReportes(
                    catalogo
                );
            }
        );
}

function ajustarAlturaCatalogoFiltroReportes(
    catalogo
) {
    const menu =
        catalogo.querySelector(
            '.reportes-filtros-catalogo__resultados'
        );

    if (!menu) {
        return;
    }

    const items =
        Array.from(
            menu.querySelectorAll(
                '.reportes-filtros-catalogo__item'
            )
        );

    if (items.length <= 4) {
        menu.style.maxHeight =
            '';
        return;
    }

    const alto =
        items
            .slice(
                0,
                4
            )
            .reduce(
                (total, item) =>
                    total
                    + item.getBoundingClientRect().height,
                0
            );

    menu.style.maxHeight =
        `${Math.ceil(alto + 14)}px`;
}

function limpiarTextoCatalogoFiltroReportes(
    texto
) {
    return String(
        texto
        || ''
    )
        .replace(
            /\s+/g,
            ' '
        )
        .trim();
}

function obtenerInicialCatalogoFiltroReportes(
    texto
) {
    const limpio =
        limpiarTextoCatalogoFiltroReportes(
            texto
        );

    if (limpio === '') {
        return '—';
    }

    return limpio
        .slice(
            0,
            1
        )
        .toLocaleUpperCase(
            'es-MX'
        );
}
