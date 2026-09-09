/* =========================================================
   SISTEMA DE REPORTES - ASUNTOS INTERNOS
   FELICITACIONES - EDITAR
   GUARDAR CAMBIOS
========================================================= */
import {
    mostrarResultado,
    mostrarResultadoYRedirigir,
} from '../../../notificaciones/resultado.js';


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

                mostrarResultado({
                    tipo:
                        'error',

                    titulo:
                        'No fue posible continuar',

                    mensaje:
                        'No fue posible identificar la felicitación que deseas actualizar.',
                });

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

                mostrarResultado({
                    tipo:
                        'warning',

                    titulo:
                        'Campo obligatorio',

                    mensaje:
                        'El nombre de la persona que da la felicitación es obligatorio.',
                });


                inputFelicitante?.focus();

                return;
            }


            if (
                razonFelicitacion === ''
            ) {

                mostrarResultado({
                    tipo:
                        'warning',

                    titulo:
                        'Campo obligatorio',

                    mensaje:
                        'La razón de la felicitación es obligatoria.',
                });


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

                mostrarResultado({
                    tipo:
                        'warning',

                    titulo:
                        'Selecciona una opción',

                    mensaje:
                        'Selecciona el tipo de asignación de unidad.',
                });

                return;
            }


            /* =================================================
               PERSONAL ACTUAL
            ================================================= */

            const filasPersonal =
                modal.querySelectorAll(
                    '#editar-felicitacion-personal tr[data-plantilla-id]'
                );


            if (
                filasPersonal.length === 0
            ) {

                mostrarResultado({
                    tipo:
                        'warning',

                    titulo:
                        'Personal requerido',

                    mensaje:
                        'Debes agregar al menos una persona a la felicitación.',
                });

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

                mostrarResultado({
                    tipo:
                        'warning',

                    titulo:
                        'Unidad requerida',

                    mensaje:
                        'Debes agregar al menos una unidad o seleccionar "Sin unidad / Oficina".',
                });

                return;
            }


            /* =================================================
               FORMDATA
            ================================================= */

            const datos =
                new FormData(
                    formulario
                );


            /* =================================================
               ELIMINAR PERSONAL DINÁMICO ANTERIOR
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

                mostrarResultadoYRedirigir({
                    tipo:
                        'success',

                    titulo:
                        'Felicitación actualizada',

                    mensaje:
                        resultado.message
                        || 'La felicitación fue actualizada correctamente.',

                    url:
                        window.location.href,

                    duracion:
                        1500,
                });


            } catch (error) {

                console.error(
                    'Error actualizando felicitación:',
                    error
                );


                mostrarResultado({
                    tipo:
                        'error',

                    titulo:
                        'No fue posible actualizar',

                    mensaje:
                        error.message
                        || 'No fue posible actualizar la felicitación.',
                });


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