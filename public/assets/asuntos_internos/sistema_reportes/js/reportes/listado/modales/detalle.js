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

    document.addEventListener(
        'click',
        (evento) => {

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

        }
    );


    /* =====================================================
       NAVEGACIÓN + CIERRE
    ===================================================== */

    modal.addEventListener(
        'click',
        (evento) => {

            /*
             * Navegación entre las 5 secciones.
             */
            const botonSeccion =
                evento.target.closest(
                    '[data-detalle-seccion]'
                );


            if (botonSeccion) {

                const seccion =
                    botonSeccion
                        .dataset
                        .detalleSeccion;


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

        }
    );


    /* =====================================================
       CERRAR CON ESCAPE
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

                cerrarDetalleReporte(
                    modal
                );

            }

        }
    );

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
        celdas[0]
            .textContent
            .trim();

    const fechaQueja =
        celdas[1]
            .textContent
            .trim();

    const expediente =
        celdas[2]
            .textContent
            .trim();

    const clasificacion =
        celdas[3]
            .textContent
            .trim();

    const quejoso =
        celdas[4]
            .textContent
            .trim();

    const area =
        celdas[5]
            .textContent
            .trim();

    const turno =
        celdas[6]
            .textContent
            .trim();

    const estado =
        celdas[7]
            .textContent
            .trim();


    /*
     * Área y turno todavía están disponibles
     * en la tabla actual.
     *
     * Los mantenemos por ahora porque otras
     * partes temporales del listado pueden
     * seguir utilizándolos.
     */
    void area;
    void turno;


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
       PERSONAL Y UNIDADES
    ===================================================== */

    /*
     * TEMPORAL:
     *
     * El listado todavía no está conectado a la BD.
     *
     * Cuando conectemos el detalle real del reporte,
     * estos arrays vendrán del backend.
     *
     * Por ahora, si la fila contiene:
     *
     * data-personal="[...]"
     * data-unidades="[...]"
     *
     * se podrán renderizar desde aquí.
     */

    const personal =
        obtenerPersonalTemporal(
            fila
        );


    const unidades =
        obtenerUnidadesTemporales(
            fila
        );


    renderizarPersonalDetalle(
        modal,
        personal
    );


    renderizarUnidadesDetalle(
        modal,
        unidades
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

    botones.forEach(
        (boton) => {

            const esActivo =
                boton
                    .dataset
                    .detalleSeccion
                === seccion;


            boton.classList.toggle(
                'detalle-reporte-nav__item--active',
                esActivo
            );

        }
    );


    /* =====================================================
       PANELES
    ===================================================== */

    paneles.forEach(
        (panel) => {

            const esActivo =
                panel
                    .dataset
                    .detallePanel
                === seccion;


            panel.classList.toggle(
                'detalle-reporte-seccion--active',
                esActivo
            );

        }
    );


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


    campos.forEach(
        (campo) => {

            campo.textContent =
                '—';

        }
    );


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


    /*
     * Limpiar personal.
     */
    renderizarPersonalDetalle(
        modal,
        []
    );


    /*
     * Limpiar unidades.
     */
    renderizarUnidadesDetalle(
        modal,
        []
    );


    /*
     * Limpiar evidencia.
     */
    limpiarEvidenciaDetalle(
        modal
    );

}


/* =========================================================
   PERSONAL - DATOS TEMPORALES
========================================================= */

function obtenerPersonalTemporal(
    fila
) {

    const contenido =
        fila.dataset.personal;


    if (!contenido) {
        return [];
    }


    try {

        const personal =
            JSON.parse(
                contenido
            );


        return Array.isArray(
            personal
        )
            ? personal
            : [];


    } catch (error) {

        console.error(
            'No fue posible leer el personal del reporte:',
            error
        );


        return [];
    }

}


/* =========================================================
   UNIDADES - DATOS TEMPORALES
========================================================= */

function obtenerUnidadesTemporales(
    fila
) {

    const contenido =
        fila.dataset.unidades;


    if (!contenido) {
        return [];
    }


    try {

        const unidades =
            JSON.parse(
                contenido
            );


        return Array.isArray(
            unidades
        )
            ? unidades
            : [];


    } catch (error) {

        console.error(
            'No fue posible leer las unidades del reporte:',
            error
        );


        return [];
    }

}


/* =========================================================
   RENDERIZAR PERSONAL
========================================================= */

function renderizarPersonalDetalle(
    modal,
    personal
) {

    const vacio =
        modal.querySelector(
            '#detalle-personal-vacio'
        );


    const wrapper =
        modal.querySelector(
            '#detalle-personal-tabla-wrapper'
        );


    const body =
        modal.querySelector(
            '#detalle-personal-body'
        );


    if (
        !vacio
        || !wrapper
        || !body
    ) {
        return;
    }


    body.innerHTML =
        '';


    if (
        !Array.isArray(personal)
        || personal.length === 0
    ) {

        vacio.hidden =
            false;

        wrapper.hidden =
            true;

        return;
    }


    personal.forEach(
        (persona) => {

            const fila =
                document.createElement(
                    'tr'
                );


            const nombre =
                String(
                    persona.nombre
                    || ''
                )
                    .trim()
                    .toUpperCase();


            const nomina =
                String(
                    persona.nomina
                    || ''
                )
                    .trim();


            const area =
                String(
                    persona.area
                    || ''
                )
                    .trim()
                    .toUpperCase();


            const turno =
                String(
                    persona.turno
                    || ''
                )
                    .trim()
                    .toUpperCase();


            const inicial =
                nombre
                    ? nombre.charAt(0)
                    : '?';


            /*
             * Si posteriormente el backend manda
             * la foto en Base64 o una URL válida,
             * este mismo render la mostrará.
             */
            const foto =
                String(
                    persona.foto
                    || ''
                ).trim();


            const fotoHtml =
                foto
                    ? `
                        <div class="detalle-personal__foto">

                            <img
                                src="${escaparHtmlDetalle(foto)}"
                                alt=""
                                onerror="
                                    this.style.display='none';
                                    this.nextElementSibling.style.display='flex';
                                "
                            >

                            <span style="display:none;">
                                ${escaparHtmlDetalle(inicial)}
                            </span>

                        </div>
                    `
                    : `
                        <div class="detalle-personal__foto">

                            <span>
                                ${escaparHtmlDetalle(inicial)}
                            </span>

                        </div>
                    `;


            fila.innerHTML = `

                <td>
                    ${fotoHtml}
                </td>

                <td>

                    <strong>
                        ${escaparHtmlDetalle(
                            nombre || '—'
                        )}
                    </strong>

                </td>

                <td>
                    ${escaparHtmlDetalle(
                        nomina || '—'
                    )}
                </td>

                <td>
                    ${escaparHtmlDetalle(
                        area || '—'
                    )}
                </td>

                <td>
                    ${escaparHtmlDetalle(
                        turno || '—'
                    )}
                </td>
            `;


            body.appendChild(
                fila
            );

        }
    );


    vacio.hidden =
        true;

    wrapper.hidden =
        false;

}


/* =========================================================
   RENDERIZAR UNIDADES
========================================================= */

function renderizarUnidadesDetalle(
    modal,
    unidades
) {

    const vacio =
        modal.querySelector(
            '#detalle-unidades-vacio'
        );


    const wrapper =
        modal.querySelector(
            '#detalle-unidades-tabla-wrapper'
        );


    const body =
        modal.querySelector(
            '#detalle-unidades-body'
        );


    if (
        !vacio
        || !wrapper
        || !body
    ) {
        return;
    }


    body.innerHTML =
        '';


    if (
        !Array.isArray(unidades)
        || unidades.length === 0
    ) {

        vacio.hidden =
            false;

        wrapper.hidden =
            true;

        return;
    }


    unidades.forEach(
        (unidad) => {

            const fila =
                document.createElement(
                    'tr'
                );


            const noEconomico =
                String(
                    unidad.no_economico
                    || ''
                )
                    .trim()
                    .toUpperCase();


            const placas =
                String(
                    unidad.placas
                    || ''
                )
                    .trim()
                    .toUpperCase();


            const marca =
                String(
                    unidad.marca
                    || ''
                )
                    .trim()
                    .toUpperCase();


            const submarca =
                String(
                    unidad.submarca
                    || ''
                )
                    .trim()
                    .toUpperCase();


            const color =
                String(
                    unidad.color
                    || ''
                )
                    .trim()
                    .toUpperCase();


            const estatus =
                String(
                    unidad.estatus
                    || ''
                )
                    .trim()
                    .toUpperCase();


            const servicio =
                String(
                    unidad.servicio
                    || ''
                )
                    .trim()
                    .toUpperCase();


            const tipo =
                String(
                    unidad.tipo
                    || ''
                )
                    .trim()
                    .toUpperCase();


            const origen =
                String(
                    unidad.origen
                    || ''
                )
                    .trim()
                    .toUpperCase();


            const marcaSubmarca =
                [
                    marca,
                    submarca,
                ]
                    .filter(Boolean)
                    .join(' ');


            fila.innerHTML = `

                <td>

                    <strong>
                        ${escaparHtmlDetalle(
                            noEconomico
                            || '—'
                        )}
                    </strong>

                    <small class="detalle-unidades__placas">
                        Placas:
                        ${escaparHtmlDetalle(
                            placas
                            || '—'
                        )}
                    </small>

                </td>

                <td>
                    ${escaparHtmlDetalle(
                        marcaSubmarca
                        || '—'
                    )}
                </td>

                <td>
                    ${escaparHtmlDetalle(
                        color
                        || '—'
                    )}
                </td>

                <td>
                    ${escaparHtmlDetalle(
                        estatus
                        || '—'
                    )}
                </td>

                <td>
                    ${escaparHtmlDetalle(
                        servicio
                        || '—'
                    )}
                </td>

                <td>
                    ${escaparHtmlDetalle(
                        tipo
                        || '—'
                    )}
                </td>

                <td>
                    ${escaparHtmlDetalle(
                        origen
                        || '—'
                    )}
                </td>
            `;


            body.appendChild(
                fila
            );

        }
    );


    vacio.hidden =
        true;

    wrapper.hidden =
        false;

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
        && modal.contains(
            elementoActivo
        )
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


    if (
        partes.length > 1
    ) {

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
    if (
        partes.length > 1
    ) {

        return partes
            .slice(1)
            .join('-');

    }


    return folio;

}


/* =========================================================
   ESCAPAR HTML
========================================================= */

function escaparHtmlDetalle(
    valor
) {

    return String(
        valor ?? ''
    )
        .replaceAll(
            '&',
            '&amp;'
        )
        .replaceAll(
            '<',
            '&lt;'
        )
        .replaceAll(
            '>',
            '&gt;'
        )
        .replaceAll(
            '"',
            '&quot;'
        )
        .replaceAll(
            "'",
            '&#039;'
        );

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