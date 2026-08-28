document.addEventListener('DOMContentLoaded', () => {
    inicializarDetalleReporte();
});


/* =========================================================
   INICIALIZAR DETALLE
========================================================= */

function inicializarDetalleReporte() {

    const modal =
        document.querySelector(
            '#modal-detalle-reporte'
        );

    if (!modal) {
        return;
    }


    /* =====================================================
       ABRIR DETALLE
    ===================================================== */

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


    /* =====================================================
       NAVEGACIÓN + CIERRE
    ===================================================== */

    modal.addEventListener('click', (evento) => {

        /*
         * Navegación entre las 5 secciones.
         */
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


        /*
         * Cerrar modal.
         */
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


    /* =====================================================
       CERRAR CON ESCAPE
    ===================================================== */

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
       LIMPIAR INFORMACIÓN ANTERIOR
    ===================================================== */

    limpiarDetalleReporte(
        modal
    );


    /* =====================================================
       DATOS TEMPORALES DISPONIBLES EN LA TABLA
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
       HEADER DEL MODAL
    ===================================================== */

    asignarTextoDetalle(
        modal,
        '#detalle-meta-expediente',
        expediente
    );


    asignarTextoDetalle(
        modal,
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
       PASO 1
       DATOS DEL REPORTE
    ===================================================== */

    asignarTextoDetalle(
        modal,
        '#detalle-prefijo',
        obtenerPrefijoFolio(
            folio
        )
    );


    asignarTextoDetalle(
        modal,
        '#detalle-numero-folio',
        obtenerNumeroFolio(
            folio
        )
    );


    /*
     * Fecha de registro:
     * todavía no viene en la tabla.
     */
    asignarTextoDetalle(
        modal,
        '#detalle-fecha-registro',
        ''
    );


    /* =====================================================
       IDENTIFICACIÓN
    ===================================================== */

    asignarTextoDetalle(
        modal,
        '#detalle-folio-ip',
        ''
    );


    asignarTextoDetalle(
        modal,
        '#detalle-fecha-queja',
        fechaQueja
    );


    asignarTextoDetalle(
        modal,
        '#detalle-fecha-acuerdo',
        ''
    );


    asignarTextoDetalle(
        modal,
        '#detalle-expediente',
        expediente
    );


    asignarTextoDetalle(
        modal,
        '#detalle-nomenclatura',
        ''
    );


    asignarTextoDetalle(
        modal,
        '#detalle-no-oficio',
        ''
    );


    /* =====================================================
       PASO 2
       DATOS DE LOS HECHOS
    ===================================================== */

    asignarTextoDetalle(
        modal,
        '#detalle-fecha-hechos',
        ''
    );


    asignarTextoDetalle(
        modal,
        '#detalle-hora-hechos',
        ''
    );


    asignarTextoDetalle(
        modal,
        '#detalle-descripcion',
        ''
    );


    /* =====================================================
       UBICACIÓN
    ===================================================== */

    asignarTextoDetalle(
        modal,
        '#detalle-calle',
        ''
    );

    asignarTextoDetalle(
        modal,
        '#detalle-numero',
        ''
    );

    asignarTextoDetalle(
        modal,
        '#detalle-colonia',
        ''
    );

    asignarTextoDetalle(
        modal,
        '#detalle-entre-calle',
        ''
    );

    asignarTextoDetalle(
        modal,
        '#detalle-y-calle',
        ''
    );

    asignarTextoDetalle(
        modal,
        '#detalle-municipio',
        ''
    );

    asignarTextoDetalle(
        modal,
        '#detalle-estado',
        ''
    );

    asignarTextoDetalle(
        modal,
        '#detalle-sector',
        ''
    );

    asignarTextoDetalle(
        modal,
        '#detalle-cuadrante',
        ''
    );

    asignarTextoDetalle(
        modal,
        '#detalle-latitud',
        ''
    );

    asignarTextoDetalle(
        modal,
        '#detalle-longitud',
        ''
    );


    /* =====================================================
       PASO 3
       PERSONAL
    ===================================================== */

    asignarTextoDetalle(
        modal,
        '#detalle-oficial',
        ''
    );


    asignarTextoDetalle(
        modal,
        '#detalle-area',
        area
    );


    asignarTextoDetalle(
        modal,
        '#detalle-turno',
        turno
    );


    /* =====================================================
       UNIDAD
    ===================================================== */

    asignarTextoDetalle(
        modal,
        '#detalle-unidad',
        ''
    );

    asignarTextoDetalle(
        modal,
        '#detalle-unidad-marca',
        ''
    );

    asignarTextoDetalle(
        modal,
        '#detalle-unidad-submarca',
        ''
    );

    asignarTextoDetalle(
        modal,
        '#detalle-unidad-color',
        ''
    );

    asignarTextoDetalle(
        modal,
        '#detalle-unidad-estatus',
        ''
    );

    asignarTextoDetalle(
        modal,
        '#detalle-unidad-servicio-adscripcion',
        ''
    );

    asignarTextoDetalle(
        modal,
        '#detalle-unidad-tipo-vehiculo',
        ''
    );

    asignarTextoDetalle(
        modal,
        '#detalle-unidad-origen',
        ''
    );


    /* =====================================================
       PASO 4
       QUEJOSO
    ===================================================== */

    asignarTextoDetalle(
        modal,
        '#detalle-quejoso',
        quejoso
    );


    asignarTextoDetalle(
        modal,
        '#detalle-edad',
        ''
    );


    asignarTextoDetalle(
        modal,
        '#detalle-genero',
        ''
    );


    asignarTextoDetalle(
        modal,
        '#detalle-telefono',
        ''
    );


    asignarTextoDetalle(
        modal,
        '#detalle-correo',
        ''
    );


    /* =====================================================
       PASO 5
       CLASIFICACIÓN Y SEGUIMIENTO
    ===================================================== */

    asignarTextoDetalle(
        modal,
        '#detalle-clasificacion',
        clasificacion
    );


    asignarTextoDetalle(
        modal,
        '#detalle-inspector',
        ''
    );


    asignarTextoDetalle(
        modal,
        '#detalle-investigador',
        ''
    );


    asignarTextoDetalle(
        modal,
        '#detalle-quien-emite-resolucion',
        ''
    );


    asignarTextoDetalle(
        modal,
        '#detalle-resolucion',
        estado
    );


    asignarTextoDetalle(
        modal,
        '#detalle-motivos',
        ''
    );


    /* =====================================================
       OBSERVACIONES
    ===================================================== */

    asignarTextoDetalle(
        modal,
        '#detalle-observaciones',
        ''
    );


    /* =====================================================
       EVIDENCIA
    ===================================================== */

    limpiarEvidenciaDetalle(
        modal
    );


    /* =====================================================
       SIEMPRE ABRIR EN PRIMERA PESTAÑA
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
       REINICIAR SCROLL INTERNO
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
   LIMPIAR DETALLE
========================================================= */

function limpiarDetalleReporte(
    modal
) {

    const campos =
        modal.querySelectorAll(
            '.detalle-reporte-campo strong'
        );


    campos.forEach((campo) => {

        campo.textContent =
            '—';

    });


    /*
     * Prefijo fijo.
     */
    const prefijo =
        modal.querySelector(
            '#detalle-prefijo'
        );


    if (prefijo) {

        prefijo.textContent =
            'QJ';

    }


    limpiarEvidenciaDetalle(
        modal
    );

}


/* =========================================================
   LIMPIAR EVIDENCIA
========================================================= */

function limpiarEvidenciaDetalle(
    modal
) {

    const lista =
        modal.querySelector(
            '#detalle-evidencia-lista'
        );


    if (!lista) {
        return;
    }


    lista.innerHTML = `
        <div class="detalle-reporte-evidencia__vacio">
            No hay evidencia fotográfica registrada.
        </div>
    `;

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
     * TEMPORAL.
     *
     * Cuando conectemos la BD utilizaremos
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

function asignarTextoDetalle(
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
            valor ?? ''
        ).trim();


    elemento.textContent =
        texto || '—';

}