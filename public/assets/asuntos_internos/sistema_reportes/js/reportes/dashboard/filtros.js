document.addEventListener(
    'DOMContentLoaded',
    () => {

        inicializarFiltrosDashboard();

    }
);


/* =========================================================
   FILTROS DEL DASHBOARD
========================================================= */

function inicializarFiltrosDashboard() {

    /* =====================================================
       CONTROLES GENERALES
    ===================================================== */

    const botonMasFiltros =
        document.querySelector(
            '#dashboard-mas-filtros'
        );


    const panelAvanzado =
        document.querySelector(
            '#dashboard-filtros-avanzados'
        );


    const botonAplicar =
        document.querySelector(
            '#dashboard-aplicar-filtros'
        );


    const botonLimpiar =
        document.querySelector(
            '#dashboard-limpiar-filtros'
        );


    /* =====================================================
       PERIODO
    ===================================================== */

    const fechaRegistroInicio =
        document.querySelector(
            '#dashboard-fecha-registro-inicio'
        );


    const fechaRegistroFin =
        document.querySelector(
            '#dashboard-fecha-registro-fin'
        );


    /* =====================================================
       TIPO
    ===================================================== */

    const tipo =
        document.querySelector(
            '#dashboard-tipo'
        );


    /* =====================================================
       REPORTE
    ===================================================== */

    const estado =
        document.querySelector(
            '#dashboard-estado'
        );


    const clasificacion =
        document.querySelector(
            '#dashboard-clasificacion'
        );


    const seguimiento =
        document.querySelector(
            '#dashboard-seguimiento'
        );


    const esAnonimo =
        document.querySelector(
            '#dashboard-anonima'
        );


    /* =====================================================
       UBICACIÓN OPERATIVA
    ===================================================== */

    const zona =
        document.querySelector(
            '#dashboard-zona'
        );


    const sector =
        document.querySelector(
            '#dashboard-sector'
        );


    const turno =
        document.querySelector(
            '#dashboard-turno'
        );


    /* =====================================================
       PERSONAL INVOLUCRADO
    ===================================================== */

    const areaPersonal =
        document.querySelector(
            '#dashboard-area-personal'
        );


    const personal =
        document.querySelector(
            '#dashboard-personal'
        );


    /* =====================================================
       UNIDAD
    ===================================================== */

    const unidad =
        document.querySelector(
            '#dashboard-unidad'
        );


    /* =====================================================
       AGRUPAR FILTROS
    ===================================================== */

    const filtros = {

        fechaRegistroInicio,
        fechaRegistroFin,

        tipo,

        estado,
        clasificacion,
        seguimiento,
        esAnonimo,

        zona,
        sector,
        turno,

        areaPersonal,
        personal,

        unidad,

    };


    /* =====================================================
       RESTAURAR FILTROS DESDE URL
    ===================================================== */

    restaurarFiltrosDesdeUrl(
        filtros
    );

    inicializarCatalogosDashboard();

    /* =====================================================
       PANEL AVANZADO

       En escritorio permanece visible por CSS.

       Este comportamiento se conserva porque en tablet /
       móvil seguimos utilizando "Más filtros".
    ===================================================== */

    if (
        botonMasFiltros
        && panelAvanzado
    ) {

        botonMasFiltros.addEventListener(
            'click',
            () => {

                const estaAbierto =
                    botonMasFiltros.getAttribute(
                        'aria-expanded'
                    ) === 'true';


                if (estaAbierto) {

                    cerrarFiltrosAvanzados(
                        botonMasFiltros,
                        panelAvanzado
                    );

                    return;
                }


                abrirFiltrosAvanzados(
                    botonMasFiltros,
                    panelAvanzado
                );

            }
        );

    }


    /* =====================================================
       APLICAR FILTROS
    ===================================================== */

    if (botonAplicar) {

        botonAplicar.addEventListener(
            'click',
            () => {

                aplicarFiltrosDashboard(
                    filtros
                );

            }
        );

    }


    /* =====================================================
       LIMPIAR FILTROS
    ===================================================== */

    if (botonLimpiar) {

        botonLimpiar.addEventListener(
            'click',
            () => {

                limpiarFiltrosDashboard();

            }
        );

    }


    /* =====================================================
       ABRIR PANEL EN MÓVIL SI EXISTEN FILTROS AVANZADOS
       ACTIVOS EN LA URL
    ===================================================== */

    if (
        botonMasFiltros
        && panelAvanzado
        && existenFiltrosAvanzadosActivos()
    ) {

        abrirFiltrosAvanzados(
            botonMasFiltros,
            panelAvanzado
        );

    }

}


/* =========================================================
   APLICAR FILTROS
========================================================= */

function aplicarFiltrosDashboard(
    filtros
) {

    const url =
        new URL(
            window.location.href
        );


    /* =====================================================
       PERIODO
    ===================================================== */

    actualizarParametro(
        url,
        'fecha_registro_inicio',
        filtros.fechaRegistroInicio?.value
    );


    actualizarParametro(
        url,
        'fecha_registro_fin',
        filtros.fechaRegistroFin?.value
    );


    /* =====================================================
       TIPO
    ===================================================== */

    actualizarParametro(
        url,
        'tipo',
        filtros.tipo?.value
    );


    /* =====================================================
       REPORTE
    ===================================================== */

    actualizarParametro(
        url,
        'estado',
        filtros.estado?.value
    );


    actualizarParametro(
        url,
        'clasificacion',
        filtros.clasificacion?.value
    );


    actualizarParametro(
        url,
        'seguimiento',
        filtros.seguimiento?.value
    );


    actualizarParametro(
        url,
        'es_anonimo',
        filtros.esAnonimo?.value
    );


    /* =====================================================
       UBICACIÓN OPERATIVA
    ===================================================== */

    actualizarParametro(
        url,
        'zona',
        filtros.zona?.value
    );


    actualizarParametro(
        url,
        'sector',
        filtros.sector?.value
    );


    actualizarParametro(
        url,
        'turno',
        filtros.turno?.value
    );


    /* =====================================================
       PERSONAL INVOLUCRADO
    ===================================================== */

    actualizarParametro(
        url,
        'area_personal',
        filtros.areaPersonal?.value
    );


    actualizarParametro(
        url,
        'personal',
        filtros.personal?.value
    );


    /* =====================================================
       UNIDAD
    ===================================================== */

    actualizarParametro(
        url,
        'unidad',
        filtros.unidad?.value
    );


    /* =====================================================
       ELIMINAR PARÁMETROS ANTIGUOS

       Evita que URLs guardadas de versiones anteriores
       sigan afectando el Dashboard.
    ===================================================== */

    eliminarParametrosAntiguos(
        url
    );


    /* =====================================================
       RECARGAR DASHBOARD
    ===================================================== */

    window.location.href =
        url.toString();

}


/* =========================================================
   ACTUALIZAR PARÁMETRO
========================================================= */

function actualizarParametro(
    url,
    nombre,
    valor
) {

    const valorLimpio =
        String(
            valor ?? ''
        ).trim();


    if (valorLimpio !== '') {

        url.searchParams.set(
            nombre,
            valorLimpio
        );

        return;
    }


    url.searchParams.delete(
        nombre
    );

}


/* =========================================================
   ELIMINAR PARÁMETROS ANTIGUOS
========================================================= */

function eliminarParametrosAntiguos(
    url
) {

    const parametrosAntiguos = [

        /* =============================================
           FECHAS RETIRADAS
        ============================================== */

        'fecha_queja_inicio',
        'fecha_queja_fin',

        'fecha_inicio',
        'fecha_fin',

        'periodo',
        'tipo_fecha',


        /* =============================================
           NOMBRE ANTERIOR DE ESTADO
        ============================================== */

        'estado_actual',


        /* =============================================
           FILTROS RETIRADOS
        ============================================== */

        'evidencia',
        'genero',
        'resolucion',
        'cuadrante',
        'colonia',
        'antiguedad',
        'inspector',
        'investigador',
        'emite_resolucion',

    ];


    parametrosAntiguos.forEach(
        parametro => {

            url.searchParams.delete(
                parametro
            );

        }
    );

}


/* =========================================================
   LIMPIAR FILTROS
========================================================= */

function limpiarFiltrosDashboard() {

    const url =
        new URL(
            window.location.href
        );


    /* =====================================================
       FILTROS V1 ACTUALES
    ===================================================== */

    const parametrosDashboard = [

        'fecha_registro_inicio',
        'fecha_registro_fin',

        'tipo',

        'estado',
        'clasificacion',
        'seguimiento',
        'es_anonimo',

        'zona',
        'sector',
        'turno',

        'area_personal',
        'personal',

        'unidad',


        /* =============================================
           VERSIONES ANTERIORES
        ============================================== */

        'fecha_queja_inicio',
        'fecha_queja_fin',

        'fecha_inicio',
        'fecha_fin',

        'periodo',
        'tipo_fecha',

        'estado_actual',

        'evidencia',
        'genero',

        'resolucion',
        'cuadrante',
        'colonia',
        'antiguedad',

        'inspector',
        'investigador',
        'emite_resolucion',

    ];


    parametrosDashboard.forEach(
        parametro => {

            url.searchParams.delete(
                parametro
            );

        }
    );


    window.location.href =
        url.toString();

}


/* =========================================================
   RESTAURAR FILTROS DESDE URL
========================================================= */

function restaurarFiltrosDesdeUrl(
    filtros
) {

    const parametros =
        new URLSearchParams(
            window.location.search
        );


    /* =====================================================
       PERIODO
    ===================================================== */

    restaurarValor(
        filtros.fechaRegistroInicio,
        parametros.get(
            'fecha_registro_inicio'
        )
    );


    restaurarValor(
        filtros.fechaRegistroFin,
        parametros.get(
            'fecha_registro_fin'
        )
    );


    /* =====================================================
       TIPO
    ===================================================== */

    restaurarValor(
        filtros.tipo,
        parametros.get(
            'tipo'
        )
    );


    /* =====================================================
       REPORTE
    ===================================================== */

    restaurarValor(
        filtros.estado,
        parametros.get(
            'estado'
        )
    );


    restaurarValor(
        filtros.clasificacion,
        parametros.get(
            'clasificacion'
        )
    );


    restaurarValor(
        filtros.seguimiento,
        parametros.get(
            'seguimiento'
        )
    );


    restaurarValor(
        filtros.esAnonimo,
        parametros.get(
            'es_anonimo'
        )
    );


    /* =====================================================
       UBICACIÓN OPERATIVA
    ===================================================== */

    restaurarValor(
        filtros.zona,
        parametros.get(
            'zona'
        )
    );


    restaurarValor(
        filtros.sector,
        parametros.get(
            'sector'
        )
    );


    restaurarValor(
        filtros.turno,
        parametros.get(
            'turno'
        )
    );


    /* =====================================================
       PERSONAL INVOLUCRADO
    ===================================================== */

    restaurarValor(
        filtros.areaPersonal,
        parametros.get(
            'area_personal'
        )
    );


    restaurarValor(
        filtros.personal,
        parametros.get(
            'personal'
        )
    );


    /* =====================================================
       UNIDAD
    ===================================================== */

    restaurarValor(
        filtros.unidad,
        parametros.get(
            'unidad'
        )
    );

}


/* =========================================================
   RESTAURAR VALOR
========================================================= */

function restaurarValor(
    elemento,
    valor
) {

    if (
        !elemento
        || valor === null
        || valor === ''
    ) {

        return;
    }


    /* =====================================================
       SELECT

       Solo restaurar cuando realmente exista la opción.
    ===================================================== */

    if (
        elemento.tagName === 'SELECT'
    ) {

        const existeOpcion =
            Array.from(
                elemento.options
            ).some(
                opcion =>
                    opcion.value === valor
            );


        if (!existeOpcion) {

            return;
        }

    }


    elemento.value =
        valor;

}


/* =========================================================
   VERIFICAR FILTROS AVANZADOS ACTIVOS
========================================================= */

function existenFiltrosAvanzadosActivos() {

    const parametros =
        new URLSearchParams(
            window.location.search
        );


    const filtrosAvanzados = [

        'estado',
        'clasificacion',
        'seguimiento',
        'es_anonimo',

        'zona',
        'sector',
        'turno',

        'area_personal',
        'personal',

        'unidad',

    ];


    return filtrosAvanzados.some(
        nombre => {

            const valor =
                parametros.get(
                    nombre
                );


            return (
                valor !== null
                && valor.trim() !== ''
            );

        }
    );

}


/* =========================================================
   ABRIR FILTROS AVANZADOS
========================================================= */

function abrirFiltrosAvanzados(
    boton,
    panel
) {

    if (
        !boton
        || !panel
    ) {

        return;
    }


    panel.hidden =
        false;


    boton.setAttribute(
        'aria-expanded',
        'true'
    );


    const texto =
        boton.querySelector(
            'span'
        );


    if (texto) {

        texto.textContent =
            'Menos filtros';

    }

}


/* =========================================================
   CERRAR FILTROS AVANZADOS
========================================================= */

function cerrarFiltrosAvanzados(
    boton,
    panel
) {

    if (
        !boton
        || !panel
    ) {

        return;
    }


    panel.hidden =
        true;


    boton.setAttribute(
        'aria-expanded',
        'false'
    );


    const texto =
        boton.querySelector(
            'span'
        );


    if (texto) {

        texto.textContent =
            'Más filtros';

    }

}

/* =========================================================
   CATÁLOGOS VISUALES DEL DASHBOARD
========================================================= */

function inicializarCatalogosDashboard() {

    const catalogos =
        document.querySelectorAll(
            '[data-dashboard-catalogo]'
        );


    catalogos.forEach(
        catalogo => {

            inicializarCatalogoDashboard(
                catalogo
            );

        }
    );


    /* =====================================================
       CERRAR AL HACER CLIC FUERA
    ===================================================== */

    document.addEventListener(
        'click',
        evento => {

            if (
                evento.target.closest(
                    '[data-dashboard-catalogo]'
                )
            ) {

                return;
            }


            cerrarTodosCatalogosDashboard();

        }
    );


    /* =====================================================
       CERRAR CON ESCAPE
    ===================================================== */

    document.addEventListener(
        'keydown',
        evento => {

            if (
                evento.key !== 'Escape'
            ) {

                return;
            }


            cerrarTodosCatalogosDashboard();

        }
    );

}


/* =========================================================
   INICIALIZAR UN CATÁLOGO
========================================================= */

function inicializarCatalogoDashboard(
    catalogo
) {

    if (!catalogo) {

        return;
    }


    const selector =
        catalogo.querySelector(
            '[data-dashboard-catalogo-selector]'
        );


    const resultados =
        catalogo.querySelector(
            '[data-dashboard-catalogo-resultados]'
        );


    const input =
        catalogo.querySelector(
            '[data-dashboard-catalogo-valor]'
        );


    const opciones =
        catalogo.querySelectorAll(
            '[data-dashboard-catalogo-opcion]'
        );


    if (
        !selector
        || !resultados
        || !input
    ) {

        return;
    }


    /* =====================================================
       SINCRONIZAR VALOR RESTAURADO DESDE URL
    ===================================================== */

    sincronizarCatalogoDashboard(
        catalogo
    );


    /* =====================================================
       ABRIR / CERRAR
    ===================================================== */

    selector.addEventListener(
        'click',
        evento => {

            evento.stopPropagation();


            const estaAbierto =
                selector.getAttribute(
                    'aria-expanded'
                ) === 'true';


            cerrarTodosCatalogosDashboard(
                catalogo
            );


            if (estaAbierto) {

                cerrarCatalogoDashboard(
                    catalogo
                );

                return;
            }


            abrirCatalogoDashboard(
                catalogo
            );

        }
    );


    /* =====================================================
       SELECCIONAR OPCIÓN
    ===================================================== */

    opciones.forEach(
        opcion => {

            opcion.addEventListener(
                'click',
                evento => {

                    evento.stopPropagation();


                    seleccionarOpcionCatalogoDashboard(
                        catalogo,
                        opcion
                    );

                }
            );

        }
    );

}


/* =========================================================
   ABRIR CATÁLOGO
========================================================= */

function abrirCatalogoDashboard(
    catalogo
) {

    const selector =
        catalogo.querySelector(
            '[data-dashboard-catalogo-selector]'
        );


    const resultados =
        catalogo.querySelector(
            '[data-dashboard-catalogo-resultados]'
        );


    if (
        !selector
        || !resultados
    ) {

        return;
    }


    resultados.hidden =
        false;


    selector.setAttribute(
        'aria-expanded',
        'true'
    );

}


/* =========================================================
   CERRAR CATÁLOGO
========================================================= */

function cerrarCatalogoDashboard(
    catalogo
) {

    const selector =
        catalogo.querySelector(
            '[data-dashboard-catalogo-selector]'
        );


    const resultados =
        catalogo.querySelector(
            '[data-dashboard-catalogo-resultados]'
        );


    if (
        !selector
        || !resultados
    ) {

        return;
    }


    resultados.hidden =
        true;


    selector.setAttribute(
        'aria-expanded',
        'false'
    );

}


/* =========================================================
   CERRAR TODOS LOS CATÁLOGOS
========================================================= */

function cerrarTodosCatalogosDashboard(
    excepcion = null
) {

    const catalogos =
        document.querySelectorAll(
            '[data-dashboard-catalogo]'
        );


    catalogos.forEach(
        catalogo => {

            if (
                excepcion
                && catalogo === excepcion
            ) {

                return;
            }


            cerrarCatalogoDashboard(
                catalogo
            );

        }
    );

}


/* =========================================================
   SELECCIONAR OPCIÓN
========================================================= */

function seleccionarOpcionCatalogoDashboard(
    catalogo,
    opcion
) {

    const input =
        catalogo.querySelector(
            '[data-dashboard-catalogo-valor]'
        );


    if (
        !input
        || !opcion
    ) {

        return;
    }


    input.value =
        opcion.dataset.value
        ?? '';


    actualizarPresentacionCatalogoDashboard(
        catalogo,
        opcion
    );


    marcarOpcionActivaCatalogoDashboard(
        catalogo,
        opcion
    );


    cerrarCatalogoDashboard(
        catalogo
    );


    /* =====================================================
       EVENTO CHANGE

       Esto permitirá que otros filtros dependientes puedan
       reaccionar en el futuro sin acoplarse al catálogo.
    ===================================================== */

    input.dispatchEvent(
        new Event(
            'change',
            {
                bubbles: true,
            }
        )
    );

}


/* =========================================================
   SINCRONIZAR CATÁLOGO CON EL INPUT
========================================================= */

function sincronizarCatalogoDashboard(
    catalogo
) {

    const input =
        catalogo.querySelector(
            '[data-dashboard-catalogo-valor]'
        );


    const opciones =
        Array.from(
            catalogo.querySelectorAll(
                '[data-dashboard-catalogo-opcion]'
            )
        );


    if (!input) {

        return;
    }


    let opcionSeleccionada =
        opciones.find(
            opcion =>
                String(
                    opcion.dataset.value
                    ?? ''
                ) === String(
                    input.value
                    ?? ''
                )
        );


    if (!opcionSeleccionada) {

        opcionSeleccionada =
            opciones[0]
            ?? null;

    }


    if (!opcionSeleccionada) {

        return;
    }


    actualizarPresentacionCatalogoDashboard(
        catalogo,
        opcionSeleccionada
    );


    marcarOpcionActivaCatalogoDashboard(
        catalogo,
        opcionSeleccionada
    );

}


/* =========================================================
   ACTUALIZAR PRESENTACIÓN
========================================================= */

function actualizarPresentacionCatalogoDashboard(
    catalogo,
    opcion
) {

    const texto =
        catalogo.querySelector(
            '[data-dashboard-catalogo-texto]'
        );


    const descripcion =
        catalogo.querySelector(
            '[data-dashboard-catalogo-descripcion]'
        );


    const avatar =
        catalogo.querySelector(
            '[data-dashboard-catalogo-avatar]'
        );


    if (texto) {

        texto.textContent =
            opcion.dataset.texto
            ?? '';

    }


    if (descripcion) {

        descripcion.textContent =
            opcion.dataset.descripcion
            ?? '';

    }


    if (avatar) {

        avatar.textContent =
            opcion.dataset.avatar
            ?? '';

    }

}


/* =========================================================
   MARCAR OPCIÓN ACTIVA
========================================================= */

function marcarOpcionActivaCatalogoDashboard(
    catalogo,
    opcionSeleccionada
) {

    const opciones =
        catalogo.querySelectorAll(
            '[data-dashboard-catalogo-opcion]'
        );


    opciones.forEach(
        opcion => {

            const estaActiva =
                opcion === opcionSeleccionada;


            opcion.classList.toggle(
                'dashboard-catalogo__item--activo',
                estaActiva
            );


            opcion.setAttribute(
                'aria-selected',
                estaActiva
                    ? 'true'
                    : 'false'
            );

        }
    );

}