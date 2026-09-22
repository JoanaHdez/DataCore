import {
    mostrarResultadoYRedirigir
} from '../../notificaciones/resultado.js';


/* =========================================================
   FELICITACIONES

   GUARDAR FORMULARIO
========================================================= */

document.addEventListener(
    'DOMContentLoaded',
    () => {

        inicializarFormularioFelicitacion();

    }
);


/* =========================================================
   INICIALIZAR
========================================================= */

function inicializarFormularioFelicitacion() {

    const formulario =
        document.querySelector(
            '#form-nueva-felicitacion'
        );


    const botonGuardar =
        document.querySelector(
            '#btn-guardar-felicitacion'
        );


    if (
        !formulario
        || !botonGuardar
    ) {

        return;
    }


    /* =====================================================
       MAYÚSCULAS AUTOMÁTICAS
    ===================================================== */

    inicializarMayusculasFelicitacion(
        formulario
    );


    let guardando =
        false;


    /* =====================================================
       NOMENCLATURA
    ===================================================== */

    inicializarNomenclaturaFelicitacion(
        formulario
    );


    /* =====================================================
       SUBMIT
    ===================================================== */

    formulario.addEventListener(
        'submit',
        async (evento) => {

            evento.preventDefault();


            if (
                guardando
            ) {

                return;
            }


            /* =====================================================
               VALIDACIÓN HTML
            ===================================================== */

            if (
                !formulario.checkValidity()
            ) {

                formulario.reportValidity();

                return;
            }


            /* =====================================================
               VALIDAR PERSONAL
            ===================================================== */

            const personal =
                formulario.querySelectorAll(
                    'input[name^="personal["]'
                );


            if (
                personal.length === 0
            ) {

                alert(
                    'Debes agregar al menos una persona a la felicitación.'
                );

                return;
            }


            /* =====================================================
               VALIDAR UNIDAD
            ===================================================== */

            const modalidadConUnidad =
                formulario.querySelector(
                    '#felicitacion-modalidad-con-unidad'
                );


            if (
                modalidadConUnidad?.checked
            ) {

                const unidades =
                    formulario.querySelectorAll(
                        'input[name^="unidades["][name$="[parque_vehicular_id]"]'
                    );


                if (
                    unidades.length === 0
                ) {

                    alert(
                        'Debes agregar al menos una unidad o seleccionar "Sin unidad / Oficina".'
                    );

                    return;
                }
            }


            guardando =
                true;


            establecerEstadoGuardando(
                botonGuardar,
                true
            );


            try {

                /* =================================================
                   FORM DATA
                ================================================= */

                const datos =
                    new FormData(
                        formulario
                    );


                /* =================================================
                   ENDPOINT
                ================================================= */

                const url =
                    new URL(
                        'DataCore/public/asuntos-internos/reportes/felicitaciones/guardar',
                        `${window.location.origin}/`
                    );


                /* =================================================
                   REQUEST
                ================================================= */

                const respuesta =
                    await fetch(
                        url.toString(),
                        {
                            method:
                                'POST',

                            body:
                                datos,

                            headers: {
                                Accept:
                                    'application/json',
                            },

                            credentials:
                                'same-origin',
                        }
                    );


                /* =================================================
                   RESPUESTA
                ================================================= */

                let resultado =
                    null;


                try {

                    resultado =
                        await respuesta.json();

                } catch (error) {

                    throw new Error(
                        'El servidor devolvió una respuesta no válida.'
                    );

                }


                if (
                    !respuesta.ok
                    || resultado?.success !== true
                ) {

                    throw new Error(
                        resultado?.message
                        || 'No fue posible guardar la felicitación.'
                    );

                }


                /* =================================================
                   ÉXITO
                ================================================= */

                mostrarResultadoYRedirigir({

                    tipo:
                        'success',

                    titulo:
                        'Felicitación guardada',

                    mensaje:
                        `La felicitación se registró correctamente con el folio ${resultado.folio ?? ''}.`,

                    url:
                        '/DataCore/public/asuntos-internos/reportes/felicitaciones',

                    duracion:
                        2000,

                });


            } catch (error) {

                console.error(
                    'Error guardando felicitación:',
                    error
                );


                alert(
                    error.message
                    || 'Ocurrió un error al guardar la felicitación.'
                );


            } finally {

                guardando =
                    false;


                establecerEstadoGuardando(
                    botonGuardar,
                    false
                );

            }

        }
    );

}


/* =========================================================
   MAYÚSCULAS AUTOMÁTICAS
   NUEVA FELICITACIÓN
========================================================= */

function inicializarMayusculasFelicitacion(
    formulario
) {

    if (!formulario) {
        return;
    }


    /* =====================================================
       EVITAR LISTENER DUPLICADO
    ===================================================== */

    if (
        formulario.dataset
            .mayusculasInicializadas
        === '1'
    ) {

        return;
    }


    formulario.dataset
        .mayusculasInicializadas =
        '1';


    /* =====================================================
       CONVERTIR MIENTRAS SE ESCRIBE
    ===================================================== */

    formulario.addEventListener(
        'input',
        (evento) => {

            const campo =
                evento.target;


            if (
                !(
                    campo instanceof HTMLInputElement
                )
                && !(
                    campo instanceof HTMLTextAreaElement
                )
            ) {

                return;
            }


            /* =================================================
               TIPOS QUE NO DEBEN MODIFICARSE
            ================================================= */

            if (
                campo instanceof HTMLInputElement
            ) {

                const tiposIgnorados =
                    [
                        'checkbox',
                        'radio',
                        'file',
                        'hidden',
                        'number',
                        'range',
                        'date',
                        'datetime-local',
                        'time',
                        'month',
                        'week',
                        'email',
                        'password',
                        'url',
                    ];


                if (
                    tiposIgnorados.includes(
                        campo.type
                    )
                ) {

                    return;
                }
            }


            /* =================================================
               EXCLUSIÓN MANUAL

               Si algún campo debe conservar minúsculas:
               data-sin-mayusculas="1"
            ================================================= */

            if (
                campo.dataset
                    .sinMayusculas
                === '1'
            ) {

                return;
            }


            /* =================================================
               VALOR ACTUAL
            ================================================= */

            const valorActual =
                String(
                    campo.value
                    || ''
                );


            const valorMayusculas =
                valorActual
                    .toLocaleUpperCase(
                        'es-MX'
                    );


            if (
                valorActual
                === valorMayusculas
            ) {

                return;
            }


            /* =================================================
               POSICIÓN DEL CURSOR
            ================================================= */

            const inicioSeleccion =
                campo.selectionStart;


            const finSeleccion =
                campo.selectionEnd;


            /* =================================================
               ASIGNAR MAYÚSCULAS
            ================================================= */

            campo.value =
                valorMayusculas;


            /* =================================================
               RESTAURAR CURSOR
            ================================================= */

            if (
                inicioSeleccion !== null
                && finSeleccion !== null
            ) {

                try {

                    campo.setSelectionRange(
                        inicioSeleccion,
                        finSeleccion
                    );

                } catch (error) {

                    /*
                     * Algunos tipos de input no permiten
                     * controlar manualmente la selección.
                     */

                }

            }

        }
    );

}


/* =========================================================
   INICIALIZAR NOMENCLATURA MANUAL
========================================================= */

function inicializarNomenclaturaFelicitacion(
    formulario
) {

    if (!formulario) {

        return;
    }


    const inputParte =
        formulario.querySelector(
            '#felicitacion-nomenclatura-parte'
        );


    const inputCompleto =
        formulario.querySelector(
            '#felicitacion-nomenclatura'
        );


    if (
        !inputParte
        || !inputCompleto
    ) {

        return;
    }


    const PREFIJO =
        'CGSC/CAI/FEL/';


    /* =====================================================
       ACTUALIZAR NOMENCLATURA COMPLETA
    ===================================================== */

    function actualizarNomenclatura() {

        let parte =
            String(
                inputParte.value
                || ''
            );


        /*
         * Evitar diagonal inicial duplicada.
         */

        parte =
            parte.replace(
                /^\/+/,
                ''
            );


        /* =================================================
           MAYÚSCULAS
        ================================================= */

        parte =
            parte.toLocaleUpperCase(
                'es-MX'
            );


        inputParte.value =
            parte;


        const parteConContenido =
            parte.trim();


        inputCompleto.value =
            parteConContenido !== ''
                ? `${PREFIJO}${parte}`
                : '';

    }


    /* =====================================================
       EVITAR LISTENERS DUPLICADOS
    ===================================================== */

    if (
        inputParte.dataset
            .nomenclaturaInicializada
        !== '1'
    ) {

        inputParte.dataset
            .nomenclaturaInicializada =
            '1';


        inputParte.addEventListener(
            'input',
            actualizarNomenclatura
        );


        inputParte.addEventListener(
            'change',
            actualizarNomenclatura
        );

    }


    /* =====================================================
       ESTADO INICIAL
    ===================================================== */

    actualizarNomenclatura();

}


/* =========================================================
   ESTADO BOTÓN
========================================================= */

function establecerEstadoGuardando(
    boton,
    guardando
) {

    boton.disabled =
        guardando;


    if (
        guardando
    ) {

        boton.dataset.textoOriginal =
            boton.textContent;


        boton.textContent =
            'Guardando...';


        return;
    }


    boton.textContent =
        boton.dataset.textoOriginal
        || 'Guardar felicitación';


    delete boton.dataset.textoOriginal;

}