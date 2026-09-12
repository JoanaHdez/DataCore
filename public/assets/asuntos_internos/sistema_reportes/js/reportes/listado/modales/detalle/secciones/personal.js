/* =========================================================
   SISTEMA DE REPORTES - ASUNTOS INTERNOS
   DETALLE DE QUEJAS
   SECCIÓN - PERSONAL INVOLUCRADO
========================================================= */

import {
    escaparHtmlDetalle,
} from '../utils/texto.js';


/* =========================================================
   RENDERIZAR PERSONAL
========================================================= */

export function renderizarPersonalDetalle(
    modal,
    personal
) {

    if (!modal) {
        return;
    }


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


    /* =====================================================
       SIN PERSONAL
    ===================================================== */

    if (
        !Array.isArray(
            personal
        )
        || personal.length === 0
    ) {

        vacio.hidden =
            false;


        wrapper.hidden =
            true;


        return;
    }


    /* =====================================================
       PERSONAL REGISTRADO
    ===================================================== */

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
                ).trim();


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


            const foto =
                String(
                    persona.foto
                    || ''
                ).trim();


            const inicial =
                nombre
                    ? nombre.charAt(0)
                    : '?';


            /* =================================================
               FOTO
            ================================================= */

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


            /* =================================================
               FILA
            ================================================= */

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