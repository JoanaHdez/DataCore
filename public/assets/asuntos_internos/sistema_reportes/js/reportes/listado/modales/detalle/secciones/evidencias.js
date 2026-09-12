/* =========================================================
   SISTEMA DE REPORTES - ASUNTOS INTERNOS
   DETALLE DE QUEJAS
   SECCIÓN - EVIDENCIAS
========================================================= */

import {
    escaparHtmlDetalle,
} from '../utils/texto.js';


/* =========================================================
   RENDERIZAR EVIDENCIAS
========================================================= */

export function renderizarEvidenciasDetalle(
    modal,
    evidencias
) {

    if (!modal) {
        return;
    }


    const lista =
        modal.querySelector(
            '#detalle-evidencia-lista'
        );


    if (!lista) {
        return;
    }


    lista.innerHTML =
        '';


    /* =====================================================
       SIN EVIDENCIAS
    ===================================================== */

    if (
        !Array.isArray(
            evidencias
        )
        || evidencias.length === 0
    ) {

        limpiarEvidenciaDetalle(
            modal
        );

        return;
    }


    /* =====================================================
       EVIDENCIAS
    ===================================================== */

    evidencias.forEach(
        (evidencia, indice) => {

            const idEvidencia =
                Number(
                    evidencia.id_evidencia
                    || 0
                );


            if (
                !Number.isInteger(
                    idEvidencia
                )
                || idEvidencia <= 0
            ) {
                return;
            }


            /* =================================================
               NOMBRE
            ================================================= */

            const nombre =
                String(
                    evidencia.nombre_original
                    || evidencia.nombre_archivo
                    || `Evidencia ${indice + 1}`
                ).trim();


            /* =================================================
               TIPO
            ================================================= */

            const tipo =
                String(
                    evidencia.mime_type
                    || evidencia.extension
                    || 'Imagen'
                ).trim();


            /* =================================================
               URL
            ================================================= */

            const urlImagen =
                new URL(
                    `DataCore/public/asuntos-internos/reportes/evidencia/${idEvidencia}`,
                    `${window.location.origin}/`
                ).toString();


            /* =================================================
               TARJETA
            ================================================= */

            const item =
                document.createElement(
                    'div'
                );


            item.className =
                'detalle-evidencia__item';


            item.innerHTML = `

                <button
                    type="button"
                    class="detalle-evidencia__preview"
                    title="Ver evidencia"
                >

                    <img
                        src="${escaparHtmlDetalle(
                            urlImagen
                        )}"
                        alt="${escaparHtmlDetalle(
                            nombre
                        )}"
                        loading="lazy"
                    >

                </button>


                <div class="detalle-evidencia__contenido">

                    <strong
                        class="detalle-evidencia__nombre"
                        title="${escaparHtmlDetalle(
                            nombre
                        )}"
                    >
                        ${escaparHtmlDetalle(
                            nombre
                        )}
                    </strong>


                    <span class="detalle-evidencia__tipo">
                        ${escaparHtmlDetalle(
                            tipo
                        )}
                    </span>


                    <button
                        type="button"
                        class="detalle-evidencia__ver"
                    >
                        Ver imagen
                    </button>

                </div>


                <div class="detalle-evidencia__numero">

                    ${
                        String(
                            indice + 1
                        ).padStart(
                            2,
                            '0'
                        )
                    }

                </div>

            `;


            /* =================================================
               BOTONES
            ================================================= */

            const botonPreview =
                item.querySelector(
                    '.detalle-evidencia__preview'
                );


            const botonVer =
                item.querySelector(
                    '.detalle-evidencia__ver'
                );


            /* =================================================
               ABRIR EVIDENCIA
            ================================================= */

            const abrirImagen =
                () => {

                    window.open(
                        urlImagen,
                        '_blank',
                        'noopener,noreferrer'
                    );
                };


            if (botonPreview) {

                botonPreview.addEventListener(
                    'click',
                    abrirImagen
                );
            }


            if (botonVer) {

                botonVer.addEventListener(
                    'click',
                    abrirImagen
                );
            }


            /* =================================================
               AGREGAR
            ================================================= */

            lista.appendChild(
                item
            );
        }
    );
}


/* =========================================================
   LIMPIAR EVIDENCIAS
========================================================= */

export function limpiarEvidenciaDetalle(
    modal
) {

    if (!modal) {
        return;
    }


    const lista =
        modal.querySelector(
            '#detalle-evidencia-lista'
        );


    if (!lista) {
        return;
    }


    lista.innerHTML = `

        <div class="detalle-evidencia__vacio">

            <div class="detalle-evidencia__vacio-icono">
                +
            </div>


            <div>

                <strong>
                    Sin evidencia
                </strong>

                <span>
                    No hay evidencia fotográfica registrada.
                </span>

            </div>

        </div>

    `;
}