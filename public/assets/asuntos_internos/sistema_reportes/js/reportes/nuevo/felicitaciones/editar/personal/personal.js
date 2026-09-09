/* =========================================================
   SISTEMA DE REPORTES - ASUNTOS INTERNOS
   FELICITACIONES - EDITAR
   PERSONAL
========================================================= */

import {
    escaparHtml,
    obtenerInicialApellido,
    obtenerFotoPersonal,
} from '../utilidades.js';

/* =========================================================
   CARGAR PERSONAL
========================================================= */

export function cargarPersonalEditar(
    modal,
    personal
) {

    const tbody =
        modal.querySelector(
            '#editar-felicitacion-personal'
        );


    const contenedorInputs =
        modal.querySelector(
            '#editar-felicitacion-personal-inputs'
        );


    if (!tbody) {
        return;
    }


    tbody.innerHTML =
        '';


    if (contenedorInputs) {

        contenedorInputs.innerHTML =
            '';
    }


    /* =====================================================
       SIN PERSONAL
    ===================================================== */

    if (
        !Array.isArray(personal)
        || personal.length === 0
    ) {

        tbody.innerHTML = `
            <tr>
                <td colspan="6">
                    Sin personal relacionado
                </td>
            </tr>
        `;


        return;
    }


    /* =====================================================
       PERSONAL REGISTRADO
    ===================================================== */

    personal.forEach(
        (persona, indice) => {

            /* =================================================
               IDENTIFICADORES
            ================================================= */

            const plantillaId =
                Number(
                    persona.plantilla_id
                    || persona.id
                    || 0
                );


            const perscod =
                String(
                    persona.perscod
                    || ''
                ).trim();


            /* =================================================
               DATOS
            ================================================= */

            const nombre =
                String(
                    persona.nombre_snapshot
                    || persona.nombre
                    || ''
                )
                    .trim()
                    .toUpperCase();


            const nomina =
                String(
                    persona.nomina_snapshot
                    || persona.nomina
                    || perscod
                    || ''
                ).trim();


            const area =
                String(
                    persona.area_snapshot
                    || persona.area
                    || ''
                )
                    .trim()
                    .toUpperCase();


            const turno =
                String(
                    persona.turno_snapshot
                    || persona.turno
                    || ''
                )
                    .trim()
                    .toUpperCase();


            const alias =
                String(
                    persona.alias_snapshot
                    || persona.alias
                    || ''
                ).trim();


            const foto =
                String(
                    persona.foto
                    || ''
                ).trim();


            /* =================================================
               INICIAL DEL APELLIDO
            ================================================= */

            const inicial =
                obtenerInicialApellido(
                    nombre
                );


            /* =================================================
               FILA
            ================================================= */

            const fila =
                document.createElement(
                    'tr'
                );


            fila.dataset.plantillaId =
                String(
                    plantillaId
                );


            fila.dataset.perscod =
                perscod;


            fila.dataset.alias =
                alias;


            /* =================================================
               FOTO
            ================================================= */

            const fotoHtml = `

                <div class="modal-felicitacion-personal-editar__foto">

                    <img
                        alt=""
                        data-foto-personal-felicitacion
                        hidden
                    >

                    <span
                        data-fallback-personal-felicitacion
                    >
                        ${escaparHtml(
                            inicial
                        )}
                    </span>

                </div>
            `;


            /* =================================================
               CONTENIDO DE FILA
            ================================================= */

            fila.innerHTML = `

                <!-- FOTO -->

                <td>

                    ${fotoHtml}

                </td>


                <!-- NOMBRE -->

                <td>

                    <span class="modal-felicitacion-personal-editar__nombre">

                        ${escaparHtml(
                            nombre
                            || '—'
                        )}

                    </span>

                </td>


                <!-- NÓMINA -->

                <td>

                    ${escaparHtml(
                        nomina
                        || '—'
                    )}

                </td>


                <!-- ÁREA -->

                <td>

                    ${escaparHtml(
                        area
                        || '—'
                    )}

                </td>


                <!-- TURNO -->

                <td>

                    <input
                        type="text"
                        class="modal-felicitacion-personal-editar__turno"
                        value="${escaparHtml(turno)}"
                        data-turno-personal-felicitacion
                        autocomplete="off"
                    >

                </td>


                <!-- ACCIONES -->

                <td>

                    <button
                        type="button"
                        class="button--remove"
                        data-quitar-personal-felicitacion
                    >
                        Quitar
                    </button>

                </td>

            `;


            /* =================================================
               ELEMENTOS DE FOTO
            ================================================= */

            const imagen =
                fila.querySelector(
                    '[data-foto-personal-felicitacion]'
                );


            const fallback =
                fila.querySelector(
                    '[data-fallback-personal-felicitacion]'
                );


            /* =================================================
               CARGAR FOTO
            ================================================= */

            if (
                imagen
                && fallback
            ) {

                imagen.hidden =
                    true;


                imagen.removeAttribute(
                    'src'
                );


                fallback.hidden =
                    false;


                if (
                    foto !== ''
                ) {

                    imagen.onload =
                        () => {

                            imagen.hidden =
                                false;


                            fallback.hidden =
                                true;
                        };


                    imagen.onerror =
                        () => {

                            imagen.hidden =
                                true;


                            imagen.removeAttribute(
                                'src'
                            );


                            fallback.hidden =
                                false;
                        };


                    imagen.src =
                        foto;
                }
            }


            /* =================================================
               AGREGAR FILA
            ================================================= */

            tbody.appendChild(
                fila
            );


            /* =================================================
               RECONSTRUIR INPUTS DINÁMICOS

               Esto permite que el personal que ya estaba
               registrado también forme parte del submit.
            ================================================= */

            if (contenedorInputs) {

                contenedorInputs.insertAdjacentHTML(
                    'beforeend',
                    `
                        <input
                            type="hidden"
                            name="personal[${indice}][plantilla_id]"
                            value="${escaparHtml(plantillaId)}"
                        >

                        <input
                            type="hidden"
                            name="personal[${indice}][perscod]"
                            value="${escaparHtml(perscod)}"
                        >

                        <input
                            type="hidden"
                            name="personal[${indice}][turno]"
                            value="${escaparHtml(turno)}"
                        >

                        <input
                            type="hidden"
                            name="personal[${indice}][alias]"
                            value="${escaparHtml(alias)}"
                        >
                    `
                );
            }
        }
    );
}