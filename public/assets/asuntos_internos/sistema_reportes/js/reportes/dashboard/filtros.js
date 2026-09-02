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
       FECHA DE REGISTRO
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
       FECHA DE LA QUEJA
    ===================================================== */

    const fechaQuejaInicio =
        document.querySelector(
            '#dashboard-fecha-queja-inicio'
        );

    const fechaQuejaFin =
        document.querySelector(
            '#dashboard-fecha-queja-fin'
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

    const sector =
        document.querySelector(
            '#dashboard-sector'
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

        fechaQuejaInicio,
        fechaQuejaFin,

        estado,
        seguimiento,

        areaPersonal,
        turno,
        sector,

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
       APLICAR
    ===================================================== */

    botonAplicar.addEventListener(
    'click',
    () => {

        aplicarFiltrosDashboard(
            filtros
        );

    }
);

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
       ABRIR PANEL SI HAY FILTROS AVANZADOS ACTIVOS
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
       FECHA DE REGISTRO
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
       FECHA DE LA QUEJA
    ===================================================== */

    actualizarParametro(
        url,
        'fecha_queja_inicio',
        filtros.fechaQuejaInicio?.value
    );

    actualizarParametro(
        url,
        'fecha_queja_fin',
        filtros.fechaQuejaFin?.value
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
        'sector',
        filtros.sector?.value
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
       ELIMINAR PARÁMETROS RETIRADOS / ANTIGUOS

       Esto evita que una URL anterior conserve filtros
       que ya no existen en la interfaz.
    ===================================================== */

    const parametrosRetirados = [

        /* Filtros retirados */
        'fecha_inicio',
        'fecha_fin',
        'periodo',
        'tipo_fecha',
        'evidencia',
        'genero',
        'zona',

        /* Pendiente */
        'clasificacion',

        /* Filtros retirados anteriormente */
        'resolucion',
        'cuadrante',
        'colonia',
        'antiguedad',
        'inspector',
        'investigador',
        'emite_resolucion',

    ];


    parametrosRetirados.forEach(
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
     * Se eliminan tanto los filtros actuales como
     * cualquier parámetro perteneciente a versiones
     * anteriores del Dashboard.
     */

    const parametrosDashboard = [

        /* Actuales */
        'fecha_registro_inicio',
        'fecha_registro_fin',
        'fecha_queja_inicio',
        'fecha_queja_fin',

        'estado_actual',
        'seguimiento',

        'area_personal',
        'turno',
        'sector',

        'unidad',

        /* Retirados */
        'fecha_inicio',
        'fecha_fin',
        'periodo',
        'tipo_fecha',
        'evidencia',
        'genero',
        'zona',

        /* Pendiente */
        'clasificacion',

        /* Versiones anteriores */
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
       FECHA DE REGISTRO
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
       FECHA DE LA QUEJA
    ===================================================== */

    restaurarValor(
        filtros.fechaQuejaInicio,
        parametros.get(
            'fecha_queja_inicio'
        )
    );

    restaurarValor(
        filtros.fechaQuejaFin,
        parametros.get(
            'fecha_queja_fin'
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
        filtros.sector,
        parametros.get(
            'sector'
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
     * Para SELECT solamente restauramos el valor
     * cuando exista realmente una opción con ese valor.
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
        'area_personal',
        'turno',
        'sector',
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