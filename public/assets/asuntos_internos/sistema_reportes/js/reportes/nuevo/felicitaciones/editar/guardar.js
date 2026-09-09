/* =========================================================
   SISTEMA DE REPORTES - ASUNTOS INTERNOS
   FELICITACIONES - EDITAR
   GUARDAR CAMBIOS
========================================================= */


/* =========================================================
   INICIALIZAR GUARDADO
========================================================= */

export function inicializarGuardadoEditar(
    modal
) {

    const formulario =
        modal.querySelector(
            '#form-editar-felicitacion'
        );


    const botonGuardar =
        modal.querySelector(
            '#btn-actualizar-felicitacion'
        );


    if (
        !formulario
        || !botonGuardar
    ) {

        console.error(
            'No se encontró el formulario de edición de felicitaciones.'
        );

        return;
    }


    /* =====================================================
       SUBMIT
    ===================================================== */

    formulario.addEventListener(
        'submit',
        async (evento) => {

            evento.preventDefault();


            /* =================================================
               ID
            ================================================= */

            const inputId =
                modal.querySelector(
                    '#editar-felicitacion-id'
                );


            const idFelicitacion =
                Number(
                    inputId?.value
                    || 0
                );


            if (
                idFelicitacion <= 0
            ) {

                alert(
                    'No fue posible identificar la felicitación que deseas actualizar.'
                );

                return;
            }


            /* =================================================
               DATOS GENERALES
            ================================================= */

            const inputFelicitante =
                modal.querySelector(
                    '#editar-felicitacion-felicitante'
                );


            const inputRazon =
                modal.querySelector(
                    '#editar-felicitacion-razon'
                );


            const nombreFelicitante =
                String(
                    inputFelicitante?.value
                    || ''
                ).trim();


            const razonFelicitacion =
                String(
                    inputRazon?.value
                    || ''
                ).trim();


            if (
                nombreFelicitante === ''
            ) {

                alert(
                    'El nombre de la persona que da la felicitación es obligatorio.'
                );


                inputFelicitante?.focus();

                return;
            }


            if (
                razonFelicitacion === ''
            ) {

                alert(
                    'La razón de la felicitación es obligatoria.'
                );


                inputRazon?.focus();

                return;
            }


            /* =================================================
               MODALIDAD
            ================================================= */

            const radioModalidad =
                modal.querySelector(
                    'input[name="modalidad_unidad_editar"]:checked'
                );


            const modalidad =
                String(
                    radioModalidad?.value
                    || ''
                ).trim();


            if (
                modalidad !== 'CON_UNIDAD'
                && modalidad !== 'SIN_UNIDAD_OFICINA'
            ) {

                alert(
                    'Selecciona el tipo de asignación de unidad.'
                );

                return;
            }


            /* =================================================
               PERSONAL ACTUAL

               Se toma directamente de las filas para enviar
               exactamente el estado actual del modal.
            ================================================= */

            const filasPersonal =
                modal.querySelectorAll(
                    '#editar-felicitacion-personal tr[data-plantilla-id]'
                );


            if (
                filasPersonal.length === 0
            ) {

                alert(
                    'Debes agregar al menos una persona a la felicitación.'
                );

                return;
            }


            /* =================================================
               UNIDADES ACTUALES
            ================================================= */

            const filasUnidades =
                modal.querySelectorAll(
                    '#editar-felicitacion-unidades tr[data-parque-vehicular-id]'
                );


            if (
                modalidad === 'CON_UNIDAD'
                && filasUnidades.length === 0
            ) {

                alert(
                    'Debes agregar al menos una unidad o seleccionar "Sin unidad / Oficina".'
                );

                return;
            }


            /* =================================================
               FORMDATA

               Incluye automáticamente:
               - CSRF
               - id_felicitacion
               - fecha_registro
               - nombre_felicitante
               - razon_felicitacion
               - modalidad_unidad_editar
            ================================================= */

            const datos =
                new FormData(
                    formulario
                );


            /* =================================================
               ELIMINAR PERSONAL DINÁMICO ANTERIOR

               Lo reconstruiremos directamente desde la tabla.
            ================================================= */

            Array.from(
                datos.keys()
            )
                .filter(
                    (clave) =>
                        clave.startsWith(
                            'personal['
                        )
                )
                .forEach(
                    (clave) => {

                        datos.delete(
                            clave
                        );
                    }
                );


            /* =================================================
               RECONSTRUIR PERSONAL
            ================================================= */

            filasPersonal.forEach(
                (fila, indice) => {

                    const plantillaId =
                        String(
                            fila.dataset.plantillaId
                            || ''
                        ).trim();


                    const perscod =
                        String(
                            fila.dataset.perscod
                            || ''
                        ).trim();


                    const alias =
                        String(
                            fila.dataset.alias
                            || ''
                        ).trim();


                    const inputTurno =
                        fila.querySelector(
                            '[data-turno-personal-felicitacion]'
                        );


                    const turno =
                        String(
                            inputTurno?.value
                            || ''
                        ).trim();


                    datos.append(
                        `personal[${indice}][plantilla_id]`,
                        plantillaId
                    );


                    datos.append(
                        `personal[${indice}][perscod]`,
                        perscod
                    );


                    datos.append(
                        `personal[${indice}][turno]`,
                        turno
                    );


                    datos.append(
                        `personal[${indice}][alias]`,
                        alias
                    );
                }
            );


            /* =================================================
               ELIMINAR UNIDADES DINÁMICAS ANTERIORES
            ================================================= */

            Array.from(
                datos.keys()
            )
                .filter(
                    (clave) =>
                        clave.startsWith(
                            'unidades['
                        )
                )
                .forEach(
                    (clave) => {

                        datos.delete(
                            clave
                        );
                    }
                );


            /* =================================================
               RECONSTRUIR UNIDADES

               Si seleccionó SIN_UNIDAD_OFICINA no enviamos
               ninguna unidad. El Service eliminará las
               relaciones anteriores.
            ================================================= */

            if (
                modalidad === 'CON_UNIDAD'
            ) {

                filasUnidades.forEach(
                    (fila, indice) => {

                        const parqueVehicularId =
                            String(
                                fila.dataset.parqueVehicularId
                                || ''
                            ).trim();


                        datos.append(
                            `unidades[${indice}][parque_vehicular_id]`,
                            parqueVehicularId
                        );
                    }
                );
            }


            /* =================================================
               BLOQUEAR BOTÓN
            ================================================= */

            const textoOriginal =
                botonGuardar.textContent;


            botonGuardar.disabled =
                true;


            botonGuardar.textContent =
                'Guardando...';


            /* =================================================
               ENVIAR
            ================================================= */

            try {

                const url =
                    new URL(
                        `DataCore/public/asuntos-internos/reportes/felicitaciones/actualizar/${idFelicitacion}`,
                        `${window.location.origin}/`
                    );


                const respuesta =
                    await fetch(
                        url.toString(),
                        {
                            method:
                                'POST',

                            headers: {
                                Accept:
                                    'application/json',
                            },

                            body:
                                datos,

                            credentials:
                                'same-origin',
                        }
                    );


                /* =============================================
                   RESPUESTA
                ============================================== */

                const resultado =
                    await respuesta.json();


                if (
                    !respuesta.ok
                    || resultado?.success !== true
                ) {

                    throw new Error(
                        resultado?.message
                        || 'No fue posible actualizar la felicitación.'
                    );
                }


                /* =============================================
                   ÉXITO
                ============================================== */

                alert(
                    resultado.message
                    || 'La felicitación fue actualizada correctamente.'
                );


                /*
                 * Recargamos para que:
                 * - listado
                 * - filtros
                 * - detalles
                 * - siguiente edición
                 *
                 * utilicen inmediatamente la información
                 * actualizada de la base de datos.
                 */

                window.location.reload();


            } catch (error) {

                console.error(
                    'Error actualizando felicitación:',
                    error
                );


                alert(
                    error.message
                    || 'No fue posible actualizar la felicitación.'
                );


                /* =============================================
                   REACTIVAR BOTÓN
                ============================================== */

                botonGuardar.disabled =
                    false;


                botonGuardar.textContent =
                    textoOriginal;
            }
        }
    );
}