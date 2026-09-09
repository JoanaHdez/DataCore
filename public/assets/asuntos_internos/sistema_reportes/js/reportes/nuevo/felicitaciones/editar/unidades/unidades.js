/* =========================================================
   SISTEMA DE REPORTES - ASUNTOS INTERNOS
   FELICITACIONES - EDITAR
   UNIDADES
========================================================= */

import {
    escaparHtml,
} from '../utilidades.js';

/* =========================================================
   CARGAR UNIDADES
========================================================= */

export function cargarUnidadesEditar(
    modal,
    unidades
) {

    const radioConUnidad =
        modal.querySelector(
            '#editar-felicitacion-con-unidad'
        );


    const radioSinUnidad =
        modal.querySelector(
            '#editar-felicitacion-sin-unidad'
        );


    const contenidoConUnidad =
        modal.querySelector(
            '#editar-felicitacion-con-unidad-contenido'
        );


    const contenidoSinUnidad =
        modal.querySelector(
            '#editar-felicitacion-sin-unidad-contenido'
        );


    const contenedorUnidadesAgregadas =
        modal.querySelector(
            '#editar-felicitacion-unidades-agregadas'
        );


    const tbody =
        modal.querySelector(
            '#editar-felicitacion-unidades'
        );


    const contenedorInputs =
        modal.querySelector(
            '#editar-felicitacion-unidades-inputs'
        );


    if (
        !radioConUnidad
        || !radioSinUnidad
        || !contenidoConUnidad
        || !contenidoSinUnidad
        || !contenedorUnidadesAgregadas
        || !tbody
    ) {
        return;
    }


    /* =====================================================
       LIMPIAR
    ===================================================== */

    tbody.innerHTML =
        '';


    if (contenedorInputs) {

        contenedorInputs.innerHTML =
            '';
    }


    /* =====================================================
       SIN UNIDADES
    ===================================================== */

    if (
        !Array.isArray(unidades)
        || unidades.length === 0
    ) {

        radioConUnidad.checked =
            false;


        radioSinUnidad.checked =
            true;


        /* =================================================
           OCULTAR CONTENIDO CON UNIDAD
        ================================================= */

        contenidoConUnidad.hidden =
            true;


        /* =================================================
           MOSTRAR ESTADO SIN UNIDAD
        ================================================= */

        contenidoSinUnidad.hidden =
            false;


        /* =================================================
           OCULTAR TABLA
        ================================================= */

        contenedorUnidadesAgregadas.hidden =
            true;


        return;
    }


    /* =====================================================
       CON UNIDADES
    ===================================================== */

    radioConUnidad.checked =
        true;


    radioSinUnidad.checked =
        false;


    contenidoConUnidad.hidden =
        false;


    contenidoSinUnidad.hidden =
        true;


    contenedorUnidadesAgregadas.hidden =
        false;


    /* =====================================================
       RECORRER UNIDADES
    ===================================================== */

    unidades.forEach(
        (unidad, indice) => {

            const idFelicitacionUnidad =
                Number(
                    unidad.id_felicitacion_unidad
                    || 0
                );


            const parqueVehicularId =
                Number(
                    unidad.parque_vehicular_id
                    || unidad.id
                    || 0
                );


            const numeroEconomico =
                String(
                    unidad.no_economico
                    || ''
                ).trim();


            const placas =
                String(
                    unidad.placas
                    || ''
                ).trim();


            const marca =
                String(
                    unidad.marca
                    || ''
                ).trim();


            const submarca =
                String(
                    unidad.submarca
                    || ''
                ).trim();


            const color =
                String(
                    unidad.color
                    || ''
                ).trim();


            const estatus =
                String(
                    unidad.estatus
                    || ''
                ).trim();


            const servicio =
                String(
                    unidad.servicio
                    || ''
                ).trim();


            const tipo =
                String(
                    unidad.tipo
                    || ''
                ).trim();


            const marcaSubmarca =
                [
                    marca,
                    submarca
                ]
                    .filter(
                        (valor) =>
                            valor !== ''
                    )
                    .join(
                        ' '
                    );


            /* =================================================
               FILA
            ================================================= */

            const fila =
                document.createElement(
                    'tr'
                );


            fila.dataset.idFelicitacionUnidad =
                String(
                    idFelicitacionUnidad
                );


            fila.dataset.parqueVehicularId =
                String(
                    parqueVehicularId
                );


            fila.innerHTML = `

                <!-- UNIDAD -->

                <td>

                    <strong class="modal-felicitacion-unidades-editar__unidad-nombre">

                        ${escaparHtml(
                            numeroEconomico
                            || '—'
                        )}

                    </strong>


                    <span class="modal-felicitacion-unidades-editar__unidad-detalle">

                        Placas:
                        ${escaparHtml(
                            placas
                            || 'SIN PLACAS'
                        )}

                    </span>

                </td>


                <!-- MARCA / SUBMARCA -->

                <td>

                    ${escaparHtml(
                        marcaSubmarca
                        || '—'
                    )}

                </td>


                <!-- COLOR -->

                <td>

                    ${escaparHtml(
                        color
                        || '—'
                    )}

                </td>


                <!-- ESTATUS -->

                <td>

                    ${escaparHtml(
                        estatus
                        || '—'
                    )}

                </td>


                <!-- SERVICIO -->

                <td>

                    ${escaparHtml(
                        servicio
                        || '—'
                    )}

                </td>


                <!-- TIPO -->

                <td>

                    ${escaparHtml(
                        tipo
                        || '—'
                    )}

                </td>


                <!-- ACCIONES -->

                <td>

                    <button
                        type="button"
                        class="button--remove"
                        data-quitar-unidad-felicitacion
                    >
                        Quitar
                    </button>

                </td>

            `;


            tbody.appendChild(
                fila
            );


            /* =================================================
               RECONSTRUIR INPUT DINÁMICO

               Esto permite que las unidades ya registradas
               también formen parte del submit.
            ================================================= */

            if (
                contenedorInputs
                && parqueVehicularId > 0
            ) {

                contenedorInputs.insertAdjacentHTML(
                    'beforeend',
                    `
                        <input
                            type="hidden"
                            name="unidades[${indice}][parque_vehicular_id]"
                            value="${escaparHtml(parqueVehicularId)}"
                        >
                    `
                );
            }
        }
    );
}