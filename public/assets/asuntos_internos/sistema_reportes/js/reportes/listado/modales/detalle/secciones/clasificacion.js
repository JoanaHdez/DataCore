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
        reporte
    );


    /* =====================================================
       MOTIVOS
    ===================================================== */

    renderizarMotivosDetalle(
        modal,
        motivos
    );
}


/* =========================================================
   SITUACIÓN DE LA SANCIÓN
========================================================= */

function renderizarSituacionSancion(
    modal,
    reporte
) {

    const esBajaVoluntaria =
        Number(
            reporte.baja_voluntaria
            ?? 0
        ) === 1;


    const esSinSanciones =
        Number(
            reporte.sin_sanciones
            ?? 0
        ) === 1;


    let texto =
        'Con seguimiento de sanción';


    if (
        esBajaVoluntaria
    ) {

        texto =
            'Baja voluntaria';

    } else if (
        esSinSanciones
    ) {

        texto =
            'Sin sanciones';
    }


    asignarTextoDetalle(
        modal,
        '#detalle-situacion-sancion',
        texto
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