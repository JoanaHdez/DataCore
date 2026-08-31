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
       FECHAS
    ===================================================== */

    const fechaInicio =
        document.querySelector(
            '#dashboard-fecha-inicio'
        );

    const fechaFin =
        document.querySelector(
            '#dashboard-fecha-fin'
        );

    const periodo =
        document.querySelector(
            '#dashboard-periodo'
        );

    const tipoFecha =
        document.querySelector(
            '#dashboard-tipo-fecha'
        );


    /* =====================================================
       REPORTE
    ===================================================== */

    const estado =
        document.querySelector(
            '#dashboard-estado'
        );

    const seguimiento =
        document.querySelector(
            '#dashboard-seguimiento'
        );

    const evidencia =
        document.querySelector(
            '#dashboard-evidencia'
        );


    /* =====================================================
       PERSONAL INVOLUCRADO
    ===================================================== */

    const areaPersonal =
        document.querySelector(
            '#dashboard-area-personal'
        );

    const turno =
        document.querySelector(
            '#dashboard-turno'
        );

    const zona =
        document.querySelector(
            '#dashboard-zona'
        );


    /* =====================================================
       QUEJOSO
    ===================================================== */

    const genero =
        document.querySelector(
            '#dashboard-genero'
        );


    /* =====================================================
       UNIDAD
    ===================================================== */

    const unidad =
        document.querySelector(
            '#dashboard-unidad'
        );


    /* =====================================================
       AGRUPAR FILTROS ACTIVOS

       Zona ya forma parte de los filtros activos.

       Clasificación permanece pendiente porque todavía
       no existe un catálogo institucional definido.
    ===================================================== */

    const filtros = {

        fechaInicio,
        fechaFin,
        periodo,
        tipoFecha,

        estado,
        seguimiento,
        evidencia,

        areaPersonal,
        turno,
        zona,

        genero,

        unidad,

    };


    /* =====================================================
       RESTAURAR DESDE URL
    ===================================================== */

    restaurarFiltrosDesdeUrl(
        filtros
    );


    /* =====================================================
       PANEL AVANZADO
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
       PERIODO RÁPIDO
    ===================================================== */

    if (periodo) {

        periodo.addEventListener(
            'change',
            () => {

                aplicarPeriodoRapido(
                    periodo.value,
                    fechaInicio,
                    fechaFin
                );

            }
        );

    }


    /* =====================================================
       FECHAS MANUALES = PERSONALIZADO
    ===================================================== */

    if (fechaInicio) {

        fechaInicio.addEventListener(
            'change',
            () => {

                marcarPeriodoPersonalizado(
                    periodo
                );

            }
        );

    }


    if (fechaFin) {

        fechaFin.addEventListener(
            'change',
            () => {

                marcarPeriodoPersonalizado(
                    periodo
                );

            }
        );

    }


    /* =====================================================
       APLICAR
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
       LIMPIAR
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
       ABRIR PANEL SI EXISTEN FILTROS AVANZADOS ACTIVOS
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
       FECHAS
    ===================================================== */

    actualizarParametro(
        url,
        'fecha_inicio',
        filtros.fechaInicio?.value
    );

    actualizarParametro(
        url,
        'fecha_fin',
        filtros.fechaFin?.value
    );

    actualizarParametro(
        url,
        'periodo',
        filtros.periodo?.value
    );

    actualizarParametro(
        url,
        'tipo_fecha',
        filtros.tipoFecha?.value
    );


    /* =====================================================
       REPORTE
    ===================================================== */

    actualizarParametro(
        url,
        'estado_actual',
        filtros.estado?.value
    );

    actualizarParametro(
        url,
        'seguimiento',
        filtros.seguimiento?.value
    );

    actualizarParametro(
        url,
        'evidencia',
        filtros.evidencia?.value
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
        'turno',
        filtros.turno?.value
    );

    actualizarParametro(
        url,
        'zona',
        filtros.zona?.value
    );


    /* =====================================================
       QUEJOSO
    ===================================================== */

    actualizarParametro(
        url,
        'genero',
        filtros.genero?.value
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
       PARÁMETROS NO ACTIVOS

       Clasificación continúa pendiente.

       Resolución ya no forma parte de los filtros.

       Zona YA NO se elimina porque ahora es un filtro real.
    ===================================================== */

    const parametrosNoActivos = [
        'clasificacion',
        'resolucion',
    ];


    parametrosNoActivos.forEach(
        parametro => {

            url.searchParams.delete(
                parametro
            );

        }
    );


    /* =====================================================
       ELIMINAR PARÁMETROS ANTIGUOS
    ===================================================== */

    const parametrosAntiguos = [
        'sector',
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
   LIMPIAR FILTROS
========================================================= */

function limpiarFiltrosDashboard() {

    const url =
        new URL(
            window.location.href
        );


    /*
     * Incluimos tanto los filtros actuales como parámetros
     * antiguos para garantizar que el Dashboard vuelva
     * completamente a la consulta general.
     */

    const parametrosDashboard = [

        /* Actuales */
        'fecha_inicio',
        'fecha_fin',
        'periodo',
        'tipo_fecha',

        'estado_actual',
        'seguimiento',
        'evidencia',

        'area_personal',
        'turno',
        'zona',

        'genero',

        'unidad',

        /* Pendiente */
        'clasificacion',

        /* Ya retirado */
        'resolucion',

        /* Versión anterior */
        'sector',
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
       FECHAS
    ===================================================== */

    restaurarValor(
        filtros.fechaInicio,
        parametros.get(
            'fecha_inicio'
        )
    );

    restaurarValor(
        filtros.fechaFin,
        parametros.get(
            'fecha_fin'
        )
    );

    restaurarValor(
        filtros.periodo,
        parametros.get(
            'periodo'
        )
    );

    restaurarValor(
        filtros.tipoFecha,
        parametros.get(
            'tipo_fecha'
        )
    );


    /* =====================================================
       REPORTE
    ===================================================== */

    restaurarValor(
        filtros.estado,
        parametros.get(
            'estado_actual'
        )
    );

    restaurarValor(
        filtros.seguimiento,
        parametros.get(
            'seguimiento'
        )
    );

    restaurarValor(
        filtros.evidencia,
        parametros.get(
            'evidencia'
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
        filtros.turno,
        parametros.get(
            'turno'
        )
    );

    restaurarValor(
        filtros.zona,
        parametros.get(
            'zona'
        )
    );


    /* =====================================================
       QUEJOSO
    ===================================================== */

    restaurarValor(
        filtros.genero,
        parametros.get(
            'genero'
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


    /*
     * Para SELECT solamente restauramos el valor cuando
     * exista realmente una opción con ese valor.
     */

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
        'estado_actual',
        'seguimiento',
        'evidencia',
        'area_personal',
        'turno',
        'zona',
        'genero',
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
   PERIODO RÁPIDO
========================================================= */

function aplicarPeriodoRapido(
    periodo,
    fechaInicio,
    fechaFin
) {

    if (
        !fechaInicio
        || !fechaFin
    ) {
        return;
    }


    /* =====================================================
       PERSONALIZADO
    ===================================================== */

    if (
        periodo === 'personalizado'
    ) {
        return;
    }


    /* =====================================================
       TODO
    ===================================================== */

    if (
        periodo === 'todo'
    ) {

        fechaInicio.value = '';
        fechaFin.value = '';

        return;
    }


    const hoy =
        new Date();


    let inicio =
        new Date(
            hoy
        );


    let fin =
        new Date(
            hoy
        );


    /* =====================================================
       MES ACTUAL
    ===================================================== */

    if (
        periodo === 'actual'
    ) {

        inicio =
            new Date(
                hoy.getFullYear(),
                hoy.getMonth(),
                1
            );

        fin =
            new Date(
                hoy.getFullYear(),
                hoy.getMonth() + 1,
                0
            );

    }


    /* =====================================================
       MES ANTERIOR
    ===================================================== */

    else if (
        periodo === 'anterior'
    ) {

        inicio =
            new Date(
                hoy.getFullYear(),
                hoy.getMonth() - 1,
                1
            );

        fin =
            new Date(
                hoy.getFullYear(),
                hoy.getMonth(),
                0
            );

    }


    /* =====================================================
       ÚLTIMOS 3 MESES
    ===================================================== */

    else if (
        periodo === 'trimestre'
    ) {

        inicio =
            new Date(
                hoy.getFullYear(),
                hoy.getMonth() - 2,
                1
            );

        fin =
            new Date(
                hoy
            );

    }


    /* =====================================================
       ÚLTIMOS 6 MESES
    ===================================================== */

    else if (
        periodo === 'semestre'
    ) {

        inicio =
            new Date(
                hoy.getFullYear(),
                hoy.getMonth() - 5,
                1
            );

        fin =
            new Date(
                hoy
            );

    }


    /* =====================================================
       AÑO ACTUAL
    ===================================================== */

    else if (
        periodo === 'anio'
    ) {

        inicio =
            new Date(
                hoy.getFullYear(),
                0,
                1
            );

        fin =
            new Date(
                hoy.getFullYear(),
                11,
                31
            );

    }


    else {
        return;
    }


    fechaInicio.value =
        formatearFechaDashboard(
            inicio
        );


    fechaFin.value =
        formatearFechaDashboard(
            fin
        );

}


/* =========================================================
   FECHAS MANUALES = PERSONALIZADO
========================================================= */

function marcarPeriodoPersonalizado(
    periodo
) {

    if (!periodo) {
        return;
    }


    periodo.value =
        'personalizado';

}


/* =========================================================
   FORMATEAR FECHA YYYY-MM-DD
========================================================= */

function formatearFechaDashboard(
    fecha
) {

    const anio =
        fecha.getFullYear();


    const mes =
        String(
            fecha.getMonth() + 1
        ).padStart(
            2,
            '0'
        );


    const dia =
        String(
            fecha.getDate()
        ).padStart(
            2,
            '0'
        );


    return `${anio}-${mes}-${dia}`;

}


/* =========================================================
   ABRIR FILTROS AVANZADOS
========================================================= */

function abrirFiltrosAvanzados(
    boton,
    panel
) {

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