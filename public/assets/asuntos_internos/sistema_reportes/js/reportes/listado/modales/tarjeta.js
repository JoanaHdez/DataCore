/* =========================================================
   SISTEMA DE REPORTES - ASUNTOS INTERNOS
   Listado - Tarjeta informativa
========================================================= */

document.addEventListener(
    'DOMContentLoaded',
    () => {

        inicializarTarjetaReporte();

    }
);


/* =========================================================
   INICIALIZAR
========================================================= */

function inicializarTarjetaReporte() {

    const modal =
        document.querySelector(
            '#modal-tarjeta-reporte'
        );


    if (!modal) {
        return;
    }


    /* =====================================================
       ABRIR TARJETA
    ===================================================== */

    document.addEventListener(
        'click',
        async (evento) => {

            const boton =
                evento.target.closest(
                    '[data-accion="tarjeta"]'
                );


            if (!boton) {
                return;
            }


            const fila =
                boton.closest(
                    'tr'
                );


            if (!fila) {
                return;
            }


            const idReporte =
                Number(
                    boton.dataset.idReporte
                    || fila.dataset.idReporte
                    || 0
                );


            if (
                !Number.isInteger(idReporte)
                || idReporte <= 0
            ) {

                console.error(
                    'No fue posible identificar el reporte.'
                );

                return;
            }


            boton.disabled =
                true;


            try {

                /* =================================================
                   CONSULTAR DATOS
                ================================================= */

                const [
                    detalle,
                    seguimiento,
                ] =
                    await Promise.all([
                        consultarDetalleTarjeta(
                            idReporte
                        ),

                        consultarSeguimientoTarjeta(
                            idReporte
                        ),
                    ]);


                if (
                    !detalle
                    || detalle.success !== true
                    || !detalle.reporte
                ) {

                    throw new Error(
                        detalle?.message
                        || 'No fue posible consultar el reporte.'
                    );
                }


                /* =================================================
                   CARGAR TARJETA
                ================================================= */

                cargarDatosTarjeta(
                    modal,
                    detalle,
                    seguimiento
                );


                abrirModalTarjeta(
                    modal
                );


            } catch (error) {

                console.error(
                    'Error cargando tarjeta:',
                    error
                );


                window.alert(
                    error.message
                    || 'No fue posible cargar la tarjeta.'
                );


            } finally {

                boton.disabled =
                    false;

            }

        }
    );


    /* =====================================================
       CERRAR TARJETA
    ===================================================== */

    modal.addEventListener(
        'click',
        (evento) => {

            const cerrar =
                evento.target.closest(
                    '[data-cerrar-modal-tarjeta]'
                );


            if (!cerrar) {
                return;
            }


            cerrarModalTarjeta(
                modal
            );

        }
    );


    /* =====================================================
       ESCAPE
    ===================================================== */

    document.addEventListener(
        'keydown',
        (evento) => {

            if (
                evento.key === 'Escape'
                && modal.classList.contains(
                    'modal-reporte--visible'
                )
            ) {

                cerrarModalTarjeta(
                    modal
                );

            }

        }
    );

}


/* =========================================================
   CONSULTAR DETALLE
========================================================= */

async function consultarDetalleTarjeta(
    idReporte
) {

    const baseUrl =
        obtenerBaseUrlTarjeta();


    const url =
        new URL(
            `asuntos-internos/reportes/detalle/${idReporte}`,
            baseUrl
        );


    const respuesta =
        await fetch(
            url.toString(),
            {
                method:
                    'GET',

                headers: {
                    Accept:
                        'application/json',
                },

                credentials:
                    'same-origin',
            }
        );


    const datos =
        await obtenerJsonTarjeta(
            respuesta
        );


    if (!respuesta.ok) {

        throw new Error(
            datos?.message
            || 'No fue posible consultar el reporte.'
        );

    }


    return datos;

}


/* =========================================================
   CONSULTAR SEGUIMIENTOS
========================================================= */

async function consultarSeguimientoTarjeta(
    idReporte
) {

    const baseUrl =
        obtenerBaseUrlTarjeta();


    const url =
        new URL(
            `asuntos-internos/reportes/seguimientos/${idReporte}`,
            baseUrl
        );


    const respuesta =
        await fetch(
            url.toString(),
            {
                method:
                    'GET',

                headers: {
                    Accept:
                        'application/json',
                },

                credentials:
                    'same-origin',
            }
        );


    const datos =
        await obtenerJsonTarjeta(
            respuesta
        );


    if (!respuesta.ok) {

        throw new Error(
            datos?.message
            || 'No fue posible consultar los seguimientos.'
        );

    }


    return datos;

}


/* =========================================================
   CARGAR DATOS
========================================================= */

function cargarDatosTarjeta(
    modal,
    detalle,
    seguimiento
) {

    const reporte =
        detalle.reporte
        || {};


    const personal =
        Array.isArray(
            detalle.personal
        )
            ? detalle.personal
            : [];


    /* =====================================================
       DATOS PRINCIPALES
    ===================================================== */

    asignarTextoTarjeta(
        modal,
        '#tarjeta-folio',
        reporte.folio
    );


    asignarTextoTarjeta(
        modal,
        '#tarjeta-fecha-queja',
        formatearFechaTarjeta(
            reporte.fecha_queja
        )
    );


    asignarTextoTarjeta(
        modal,
        '#tarjeta-expediente',
        reporte.expediente
    );


    asignarTextoTarjeta(
        modal,
        '#tarjeta-clasificacion',
        reporte.clasificacion
    );


    asignarTextoTarjeta(
        modal,
        '#tarjeta-quejoso',
        reporte.nombre_quejoso
    );


    /* =====================================================
       PERSONAL
    ===================================================== */

    const areas =
        obtenerValoresUnicos(
            personal,
            'area'
        );


    const turnos =
        obtenerValoresUnicos(
            personal,
            'turno'
        );


    asignarTextoTarjeta(
        modal,
        '#tarjeta-area',
        areas.length
            ? areas.join(', ')
            : ''
    );


    asignarTextoTarjeta(
        modal,
        '#tarjeta-turno',
        turnos.length
            ? turnos.join(', ')
            : ''
    );


    /* =====================================================
       ESTADO
    ===================================================== */

    asignarTextoTarjeta(
        modal,
        '#tarjeta-resolucion',
        reporte.estado_actual
        || reporte.resolucion
    );


    /* =====================================================
       ÚLTIMO SEGUIMIENTO
    ===================================================== */

    cargarUltimoSeguimientoTarjeta(
        modal,
        seguimiento?.seguimientos
        || []
    );


    /* =====================================================
       TÍTULO
    ===================================================== */

    const titulo =
        modal.querySelector(
            '#modal-tarjeta-titulo'
        );


    if (titulo) {

        const folio =
            String(
                reporte.folio
                || ''
            ).trim();


        titulo.textContent =
            folio
                ? `Tarjeta ${folio}`
                : 'Tarjeta informativa';

    }

}


/* =========================================================
   ÚLTIMO SEGUIMIENTO
========================================================= */

function cargarUltimoSeguimientoTarjeta(
    modal,
    seguimientos
) {

    const contenedor =
        modal.querySelector(
            '#tarjeta-ultimo-seguimiento'
        );


    if (!contenedor) {
        return;
    }


    contenedor.innerHTML =
        '';


    if (
        !Array.isArray(seguimientos)
        || seguimientos.length === 0
    ) {

        const mensaje =
            document.createElement(
                'span'
            );


        mensaje.textContent =
            'Sin seguimientos registrados.';


        contenedor.appendChild(
            mensaje
        );


        return;
    }


    /*
     * El backend ya devuelve el historial
     * ordenado del más reciente al más antiguo.
     */

    const ultimo =
        seguimientos[0];


    /* =====================================================
       ENCABEZADO
    ===================================================== */

    const encabezado =
        document.createElement(
            'strong'
        );


    encabezado.textContent =
        `${
            ultimo.tipo
            || 'Seguimiento'
        } · ${
            formatearFechaTarjeta(
                ultimo.fecha
            )
        }`;


    /* =====================================================
       ESTADO
    ===================================================== */

    const estado =
        document.createElement(
            'span'
        );


    estado.textContent =
        `Estado: ${
            ultimo.estado_resultante
            || ultimo.estado
            || 'Pendiente'
        }`;


    /* =====================================================
       OBSERVACIONES
    ===================================================== */

    const observaciones =
        document.createElement(
            'p'
        );


    observaciones.textContent =
        ultimo.observaciones
        || '—';


    contenedor.appendChild(
        encabezado
    );


    contenedor.appendChild(
        estado
    );


    contenedor.appendChild(
        observaciones
    );

}


/* =========================================================
   VALORES ÚNICOS
========================================================= */

function obtenerValoresUnicos(
    elementos,
    propiedad
) {

    if (
        !Array.isArray(elementos)
    ) {
        return [];
    }


    const valores =
        [];


    elementos.forEach(
        (elemento) => {

            const valor =
                String(
                    elemento?.[propiedad]
                    || ''
                )
                    .trim()
                    .toUpperCase();


            if (
                valor !== ''
                && !valores.includes(
                    valor
                )
            ) {

                valores.push(
                    valor
                );

            }

        }
    );


    return valores;

}


/* =========================================================
   ABRIR MODAL
========================================================= */

function abrirModalTarjeta(
    modal
) {

    modal.classList.add(
        'modal-reporte--visible'
    );


    modal.setAttribute(
        'aria-hidden',
        'false'
    );


    document.body.classList.add(
        'modal-abierto'
    );

}


/* =========================================================
   CERRAR MODAL
========================================================= */

function cerrarModalTarjeta(
    modal
) {

    const activo =
        document.activeElement;


    if (
        activo
        && modal.contains(
            activo
        )
    ) {

        activo.blur();

    }


    modal.classList.remove(
        'modal-reporte--visible'
    );


    modal.setAttribute(
        'aria-hidden',
        'true'
    );


    document.body.classList.remove(
        'modal-abierto'
    );

}


/* =========================================================
   FORMATEAR FECHA
========================================================= */

function formatearFechaTarjeta(
    fecha
) {

    const valor =
        String(
            fecha
            || ''
        ).trim();


    if (!valor) {
        return '—';
    }


    const coincidencia =
        valor.match(
            /^(\d{4})-(\d{2})-(\d{2})/
        );


    if (!coincidencia) {
        return valor;
    }


    return `${coincidencia[3]}/${coincidencia[2]}/${coincidencia[1]}`;

}


/* =========================================================
   ASIGNAR TEXTO
========================================================= */

function asignarTextoTarjeta(
    modal,
    selector,
    valor
) {

    const elemento =
        modal.querySelector(
            selector
        );


    if (!elemento) {
        return;
    }


    const texto =
        String(
            valor
            ?? ''
        ).trim();


    elemento.textContent =
        texto || '—';

}


/* =========================================================
   JSON
========================================================= */

async function obtenerJsonTarjeta(
    respuesta
) {

    try {

        return await respuesta.json();

    } catch (error) {

        throw new Error(
            'El servidor devolvió una respuesta no válida.'
        );

    }

}


/* =========================================================
   BASE URL
========================================================= */

function obtenerBaseUrlTarjeta() {

    return (
        document
            .querySelector('base')
            ?.href
        || `${window.location.origin}/`
    );

}