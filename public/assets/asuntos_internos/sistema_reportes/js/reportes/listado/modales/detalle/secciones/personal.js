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


            /* =================================================
               NOMBRE
            ================================================= */

            const nombre =
                String(
                    persona.nombre
                    ?? persona.nombre_snapshot
                    ?? ''
                )
                    .trim()
                    .toUpperCase();


            /* =================================================
               NÓMINA
            ================================================= */

            const nomina =
                String(
                    persona.nomina
                    ?? persona.perscod
                    ?? ''
                ).trim();


            /* =================================================
               ÁREA
            ================================================= */

            const area =
                String(
                    persona.area
                    ?? persona.area_snapshot
                    ?? ''
                )
                    .trim()
                    .toUpperCase();


            /* =================================================
               TURNO
            ================================================= */

            const turno =
                String(
                    persona.turno
                    ?? persona.turno_snapshot
                    ?? ''
                )
                    .trim()
                    .toUpperCase();


            /* =================================================
               ALIAS
            ================================================= */

            const alias =
                String(
                    persona.alias
                    ?? persona.alias_snapshot
                    ?? ''
                )
                    .trim()
                    .toUpperCase();


            /* =================================================
               FOTO
            ================================================= */

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
               FOTO HTML
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

                <!-- FOTO -->
                <td>
                    ${fotoHtml}
                </td>


                <!-- NOMBRE -->
                <td>
                    <strong>
                        ${escaparHtmlDetalle(
                            nombre || '—'
                        )}
                    </strong>
                </td>


                <!-- NÓMINA -->
                <td>
                    ${escaparHtmlDetalle(
                        nomina || '—'
                    )}
                </td>


                <!-- ÁREA -->
                <td>
                    ${escaparHtmlDetalle(
                        area || '—'
                    )}
                </td>


                <!-- TURNO -->
                <td>
                    ${escaparHtmlDetalle(
                        turno || '—'
                    )}
                </td>


                <!-- ALIAS -->
                <td>
                    ${escaparHtmlDetalle(
                        alias || '—'
                    )}
                </td>

            `;


            body.appendChild(
                fila
            );
        }
    );


    /* =====================================================
       MOSTRAR TABLA
    ===================================================== */

    vacio.hidden =
        true;


    wrapper.hidden =
        false;
}