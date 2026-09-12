/* =========================================================
   SISTEMA DE REPORTES - ASUNTOS INTERNOS
   DETALLE DE QUEJAS
   SECCIÓN - UNIDADES INVOLUCRADAS
========================================================= */

import {
    mayusculas,
    escaparHtmlDetalle,
} from '../utils/texto.js';


/* =========================================================
   RENDERIZAR UNIDADES
========================================================= */

export function renderizarUnidadesDetalle(
    modal,
    unidades,
    modalidadUnidad = 'CON_UNIDAD'
) {

    if (!modal) {
        return;
    }


    const vacio =
        modal.querySelector(
            '#detalle-unidades-vacio'
        );


    const sinUnidad =
        modal.querySelector(
            '#detalle-unidades-sin-unidad'
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
        || !sinUnidad
        || !wrapper
        || !body
    ) {
        return;
    }


    body.innerHTML =
        '';


    const modalidad =
        String(
            modalidadUnidad
            || 'CON_UNIDAD'
        )
            .trim()
            .toUpperCase();


    /* =====================================================
       SIN UNIDAD / OFICINA
    ===================================================== */

    if (
        modalidad ===
        'SIN_UNIDAD_OFICINA'
    ) {

        sinUnidad.hidden =
            false;


        vacio.hidden =
            true;


        wrapper.hidden =
            true;


        return;
    }


    /* =====================================================
       CON UNIDAD
    ===================================================== */

    sinUnidad.hidden =
        true;


    if (
        !Array.isArray(
            unidades
        )
        || unidades.length === 0
    ) {

        vacio.hidden =
            false;


        wrapper.hidden =
            true;


        return;
    }


    /* =====================================================
       RENDERIZAR REGISTROS
    ===================================================== */

    unidades.forEach(
        (unidad) => {

            const fila =
                document.createElement(
                    'tr'
                );


            const noEconomico =
                mayusculas(
                    unidad.no_economico
                );


            const placas =
                mayusculas(
                    unidad.placas
                );


            const marca =
                mayusculas(
                    unidad.marca
                );


            const submarca =
                mayusculas(
                    unidad.submarca
                );


            const color =
                mayusculas(
                    unidad.color
                );


            const estatus =
                mayusculas(
                    unidad.estatus
                );


            const servicio =
                mayusculas(
                    unidad.servicio
                );


            const tipo =
                mayusculas(
                    unidad.tipo
                );


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
                            noEconomico || '—'
                        )}
                    </strong>

                    <small class="detalle-unidades__placas">
                        Placas:
                        ${escaparHtmlDetalle(
                            placas || '—'
                        )}
                    </small>

                </td>


                <td>
                    ${escaparHtmlDetalle(
                        marcaSubmarca || '—'
                    )}
                </td>


                <td>
                    ${escaparHtmlDetalle(
                        color || '—'
                    )}
                </td>


                <td>
                    ${escaparHtmlDetalle(
                        estatus || '—'
                    )}
                </td>


                <td>
                    ${escaparHtmlDetalle(
                        servicio || '—'
                    )}
                </td>


                <td>
                    ${escaparHtmlDetalle(
                        tipo || '—'
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