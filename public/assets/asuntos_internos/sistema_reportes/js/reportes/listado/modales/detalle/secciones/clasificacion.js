/* =========================================================
   SISTEMA DE REPORTES - ASUNTOS INTERNOS
   DETALLE DE QUEJAS
   SECCIÓN - CLASIFICACIÓN Y SEGUIMIENTO
========================================================= */

import {
    asignarTextoDetalle,
    escaparHtmlDetalle,
} from '../utils/texto.js';


/* =========================================================
   CARGAR CLASIFICACIÓN
========================================================= */

export function cargarClasificacionDetalle(
    modal,
    reporte,
    motivos = []
) {

    if (
        !modal
        || !reporte
    ) {
        return;
    }


    /* =====================================================
       DATOS GENERALES
    ===================================================== */

    asignarTextoDetalle(
        modal,
        '#detalle-clasificacion',
        reporte.clasificacion
    );


    asignarTextoDetalle(
        modal,
        '#detalle-inspector',
        reporte.inspector
    );


    asignarTextoDetalle(
        modal,
        '#detalle-investigador',
        reporte.investigador
    );


    asignarTextoDetalle(
        modal,
        '#detalle-estado-actual',
        reporte.estado_actual
    );


    asignarTextoDetalle(
        modal,
        '#detalle-quien-emite-resolucion',
        reporte.quien_emite_resolucion
    );


    asignarTextoDetalle(
        modal,
        '#detalle-resolucion',
        reporte.resolucion
    );


    /* =====================================================
       SITUACIÓN DE LA SANCIÓN
    ===================================================== */

    renderizarSituacionSancion(
        modal,
        reporte,
        motivos
    );


    /* =====================================================
       MOTIVOS
    ===================================================== */

    renderizarMotivosDetalle(
        modal,
        motivos
    );

    actualizarTotalHorasArrestoDetalle(
        modal,
        motivos
    );
}


/* =========================================================
   SITUACIÓN DE LA SANCIÓN
========================================================= */

function renderizarSituacionSancion(
    modal,
    reporte,
    motivos = []
) {

    const origenEstado =
        String(
            reporte.origen_estado
            ?? ''
        ).trim();


    const estadoActual =
        String(
            reporte.estado_actual
            ?? ''
        ).trim();


    const estaFinalizado =
        estadoActual === 'Finalizado';


    const tieneMotivos =
        Array.isArray(
            motivos
        )
        && motivos.length > 0;


    let texto =
        'Sin motivos relacionados';


    /* =====================================================
       REPORTE FINALIZADO
    ===================================================== */

    if (estaFinalizado) {

        switch (origenEstado) {

            case 'sin_sancion':

                texto =
                    'Finalizado sin sanción';

                break;


            case 'baja_voluntaria':

                texto =
                    'Finalizado por baja voluntaria';

                break;


            case 'desistimiento':

                texto =
                    'Finalizado por desistimiento';

                break;


            case 'seguimiento':

                texto =
                    'Finalizado por seguimiento';

                break;


            case 'manual':

                texto =
                    'Finalizado manualmente';

                break;


            default:

                texto =
                    'Finalizado';

                break;
        }


        asignarTextoDetalle(
            modal,
            '#detalle-situacion-sancion',
            texto
        );


        return;
    }


    /* =====================================================
       REPORTE NO FINALIZADO
    ===================================================== */

    if (
        origenEstado === 'sin_sancion'
        || Number(
            reporte.sin_sanciones
            ?? 0
        ) === 1
    ) {

        texto =
            'Sin sanción asignada';

    } else if (
        origenEstado === 'seguimiento'
    ) {

        texto =
            'En seguimiento';

    } else if (
        origenEstado === 'manual'
    ) {

        texto =
            'Estado actualizado manualmente';

    } else if (tieneMotivos) {

        texto =
            'Con motivos relacionados';

    } else {

        texto =
            'Sin motivos relacionados';
    }


    asignarTextoDetalle(
        modal,
        '#detalle-situacion-sancion',
        texto
    );
}


/* =========================================================
   TOTAL DE HORAS DE ARRESTO
========================================================= */

function actualizarTotalHorasArrestoDetalle(
    modal,
    motivos = []
) {

    if (!modal) {
        return;
    }


    const campoTotal =
        modal.querySelector(
            '#detalle-total-horas-arresto'
        );


    if (!campoTotal) {
        return;
    }


    let totalHoras =
        0;


    if (
        Array.isArray(
            motivos
        )
    ) {

        motivos.forEach(
            (motivo) => {

                const sancion =
                    String(
                        motivo.sancion
                        ?? motivo.tipo_sancion
                        ?? ''
                    )
                        .trim()
                        .toUpperCase();


                const coincidencia =
                    sancion.match(
                        /^ARRESTO\s+POR\s+(\d+)\s+HORAS$/
                    );


                if (!coincidencia) {
                    return;
                }


                const horas =
                    Number(
                        coincidencia[1]
                    );


                if (
                    Number.isFinite(
                        horas
                    )
                    && horas > 0
                ) {

                    totalHoras +=
                        horas;
                }
            }
        );
    }


    campoTotal.textContent =
        String(
            totalHoras
        );
}


/* =========================================================
   MOTIVOS
========================================================= */

function renderizarMotivosDetalle(
    modal,
    motivos
) {

    const vacio =
        modal.querySelector(
            '#detalle-motivos-vacio'
        );


    const wrapper =
        modal.querySelector(
            '#detalle-motivos-wrapper'
        );


    const body =
        modal.querySelector(
            '#detalle-motivos-body'
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
        !Array.isArray(
            motivos
        )
        || motivos.length === 0
    ) {

        vacio.hidden =
            false;


        wrapper.hidden =
            true;


        return;
    }


    motivos.forEach(
        (motivo, indice) => {

            const fila =
                document.createElement(
                    'tr'
                );


            const numero =
                motivo.id_motivo
                ?? indice + 1;


            const textoMotivo =
                String(
                    motivo.motivo
                    ?? ''
                ).trim();


            const sancion =
                String(
                    motivo.sancion
                    ?? motivo.tipo_sancion
                    ?? ''
                ).trim();


            const folioSancion =
                String(
                    motivo.folio_sancion
                    ?? ''
                ).trim();


            fila.innerHTML = `

                <td>
                    ${escaparHtmlDetalle(
                numero
            )}
                </td>


                <td>
                    ${escaparHtmlDetalle(
                textoMotivo
                || '—'
            )}
                </td>


                <td>
                    ${escaparHtmlDetalle(
                sancion
                || 'Sin sanción'
            )}
                </td>


                <td>
                    ${escaparHtmlDetalle(
                folioSancion
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