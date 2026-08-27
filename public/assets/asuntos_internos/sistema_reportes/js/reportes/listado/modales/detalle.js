document.addEventListener('DOMContentLoaded', () => {
    inicializarDetalleReporte();
});


function inicializarDetalleReporte() {

    const modal =
        document.querySelector(
            '#modal-detalle-reporte'
        );

    if (!modal) {
        return;
    }


    /* =========================================================
       ABRIR DETALLE
    ========================================================= */

    document.addEventListener('click', (evento) => {

        const boton =
            evento.target.closest(
                '[data-accion="ver"]'
            );

        if (!boton) {
            return;
        }


        const fila =
            boton.closest('tr');


        if (!fila) {
            return;
        }


        abrirDetalleReporte(
            modal,
            fila
        );

    });


    /* =========================================================
       NAVEGACIÓN ENTRE SECCIONES
    ========================================================= */

    modal.addEventListener('click', (evento) => {

        const botonSeccion =
            evento.target.closest(
                '[data-detalle-seccion]'
            );


        if (botonSeccion) {

            const seccion =
                botonSeccion.dataset.detalleSeccion;


            mostrarSeccionDetalle(
                modal,
                seccion
            );


            return;
        }


        /* =====================================================
           CERRAR MODAL
        ===================================================== */

        const cerrar =
            evento.target.closest(
                '[data-cerrar-modal]'
            );


        if (!cerrar) {
            return;
        }


        cerrarDetalleReporte(
            modal
        );

    });


    /* =========================================================
       CERRAR CON ESCAPE
    ========================================================= */

    document.addEventListener('keydown', (evento) => {

        if (
            evento.key === 'Escape'
            && modal.classList.contains(
                'modal-reporte--visible'
            )
        ) {

            cerrarDetalleReporte(
                modal
            );

        }

    });

}


/* =========================================================
   ABRIR DETALLE
========================================================= */

function abrirDetalleReporte(
    modal,
    fila
) {

    const celdas =
        fila.querySelectorAll('td');


    if (celdas.length < 8) {
        return;
    }


    /* =====================================================
       DATOS DISPONIBLES ACTUALMENTE EN LA TABLA
    ===================================================== */

    const folio =
        celdas[0].textContent.trim();

    const fechaQueja =
        celdas[1].textContent.trim();

    const expediente =
        celdas[2].textContent.trim();

    const clasificacion =
        celdas[3].textContent.trim();

    const quejoso =
        celdas[4].textContent.trim();

    const area =
        celdas[5].textContent.trim();

    const turno =
        celdas[6].textContent.trim();

    const estado =
        celdas[7].textContent.trim();


    /* =====================================================
       HEADER
    ===================================================== */

    asignarTexto(
        '#detalle-meta-expediente',
        expediente
    );

    asignarTexto(
        '#detalle-meta-estado',
        estado
    );


    const titulo =
        modal.querySelector(
            '#modal-detalle-titulo'
        );


    if (titulo) {

        titulo.textContent =
            `Reporte ${folio}`;

    }


    /* =====================================================
       DATOS DEL REPORTE
    ===================================================== */

    asignarTexto(
        '#detalle-prefijo',
        obtenerPrefijoFolio(
            folio
        )
    );


    asignarTexto(
        '#detalle-numero-folio',
        obtenerNumeroFolio(
            folio
        )
    );


    asignarTexto(
        '#detalle-fecha-queja',
        fechaQueja
    );


    asignarTexto(
        '#detalle-expediente',
        expediente
    );


    /* =====================================================
       PERSONAL
    ===================================================== */

    asignarTexto(
        '#detalle-area',
        area
    );


    asignarTexto(
        '#detalle-turno',
        turno
    );


    /* =====================================================
       QUEJOSO
    ===================================================== */

    asignarTexto(
        '#detalle-quejoso',
        quejoso
    );


    /* =====================================================
       CLASIFICACIÓN
    ===================================================== */

    asignarTexto(
        '#detalle-clasificacion',
        clasificacion
    );


    /*
     * Los demás campos todavía no existen
     * en la tabla temporal.
     *
     * Se mantienen como "—" hasta
     * conectar el detalle con la BD.
     */


    /* =====================================================
       SIEMPRE ABRIR EN "DATOS"
    ===================================================== */

    mostrarSeccionDetalle(
        modal,
        'datos'
    );


    /* =====================================================
       MOSTRAR MODAL
    ===================================================== */

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
   CAMBIAR SECCIÓN
========================================================= */

function mostrarSeccionDetalle(
    modal,
    seccion
) {

    const botones =
        modal.querySelectorAll(
            '[data-detalle-seccion]'
        );


    const paneles =
        modal.querySelectorAll(
            '[data-detalle-panel]'
        );


    /* =====================================================
       BOTONES
    ===================================================== */

    botones.forEach((boton) => {

        const esActivo =
            boton.dataset.detalleSeccion
            === seccion;


        boton.classList.toggle(
            'detalle-reporte-nav__item--active',
            esActivo
        );

    });


    /* =====================================================
       PANELES
    ===================================================== */

    paneles.forEach((panel) => {

        const esActivo =
            panel.dataset.detallePanel
            === seccion;


        panel.classList.toggle(
            'detalle-reporte-seccion--active',
            esActivo
        );

    });


    /* =====================================================
       REGRESAR SCROLL DEL BODY AL INICIO
    ===================================================== */

    const body =
        modal.querySelector(
            '.modal-reporte__body--detalle'
        );


    if (body) {

        body.scrollTo({
            top: 0,
            behavior: 'smooth',
        });

    }

}


/* =========================================================
   CERRAR MODAL
========================================================= */

function cerrarDetalleReporte(
    modal
) {

    const elementoActivo =
        document.activeElement;


    if (
        elementoActivo
        && modal.contains(elementoActivo)
    ) {

        elementoActivo.blur();

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
   OBTENER PREFIJO DEL FOLIO
========================================================= */

function obtenerPrefijoFolio(
    folio
) {

    if (!folio) {
        return 'QJ';
    }


    const partes =
        folio.split('-');


    if (partes.length > 1) {
        return partes[0];
    }


    return 'QJ';

}


/* =========================================================
   OBTENER NÚMERO DEL FOLIO
========================================================= */

function obtenerNumeroFolio(
    folio
) {

    if (!folio) {
        return '';
    }


    const partes =
        folio.split('-');


    /*
     * Temporalmente usamos todo lo
     * que exista después del prefijo.
     *
     * Cuando conectemos BD se utilizará
     * directamente numero_folio.
     */
    if (partes.length > 1) {

        return partes
            .slice(1)
            .join('-');

    }


    return folio;

}


/* =========================================================
   ASIGNAR TEXTO
========================================================= */

function asignarTexto(
    selector,
    valor
) {

    const elemento =
        document.querySelector(
            selector
        );


    if (!elemento) {
        return;
    }


    elemento.textContent =
        valor?.trim()
        || '—';

}