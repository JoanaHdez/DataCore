import {
    mostrarResultadoYRedirigir
} from '../notificaciones/resultado.js';


document.addEventListener(
    'DOMContentLoaded',
    () => {

        inicializarFormularioPorPasos();

    }
);


/* =========================================================
   INICIALIZAR
========================================================= */

function inicializarFormularioPorPasos() {

    const formulario =
        document.querySelector(
            '#form-nuevo-reporte'
        );


    if (!formulario) {
        return;
    }


    const pasos =
        Array.from(
            formulario.querySelectorAll(
                '.report-step'
            )
        );


    const indicadores =
        Array.from(
            formulario.querySelectorAll(
                '[data-step-indicator]'
            )
        );


    const botonAnterior =
        formulario.querySelector(
            '#btn-step-anterior'
        );


    const botonSiguiente =
        formulario.querySelector(
            '#btn-step-siguiente'
        );


    const botonGuardar =
        formulario.querySelector(
            '#btn-guardar-reporte'
        );


    if (
        !pasos.length
        || !botonAnterior
        || !botonSiguiente
        || !botonGuardar
    ) {
        return;
    }


    let pasoActual = 1;

    let guardando = false;


    const totalPasos =
        pasos.length;


    const pasosCompletados =
        new Set();


    /* =====================================================
       MOSTRAR PASO
    ===================================================== */

    function mostrarPaso(
        numeroPaso
    ) {

        pasoActual =
            numeroPaso;


        /* =================================================
           CONTENIDO
        ================================================= */

        pasos.forEach(
            (paso) => {

                const numero =
                    Number(
                        paso.dataset.step
                    );


                paso.classList.toggle(
                    'report-step--active',
                    numero === pasoActual
                );

            }
        );


        /* =================================================
           INDICADORES
        ================================================= */

        indicadores.forEach(
            (indicador) => {

                const numero =
                    Number(
                        indicador
                            .dataset
                            .stepIndicator
                    );


                indicador.classList.toggle(
                    'report-steps__item--active',
                    numero === pasoActual
                );


                indicador.classList.toggle(
                    'report-steps__item--completed',
                    pasosCompletados.has(
                        numero
                    )
                    && numero !== pasoActual
                );

            }
        );


        /* =================================================
           ANTERIOR
        ================================================= */

        botonAnterior.classList.toggle(
            'report-step-control--hidden',
            pasoActual === 1
        );


        /* =================================================
           SIGUIENTE
        ================================================= */

        botonSiguiente.classList.toggle(
            'report-step-control--hidden',
            pasoActual === totalPasos
        );


        /* =================================================
           GUARDAR
        ================================================= */

        botonGuardar.classList.toggle(
            'report-step-control--hidden',
            pasoActual !== totalPasos
        );


        /* =================================================
           SCROLL
        ================================================= */

        formulario.scrollIntoView({
            behavior: 'smooth',
            block: 'start',
        });

    }

    /* =====================================================
       LIMPIAR VALIDACIÓN DE FOLIO AL MODIFICARLO
    ===================================================== */

    const inputNumeroFolio =
        formulario.querySelector(
            '#numero_folio'
        );


    const mensajeFolio =
        formulario.querySelector(
            '#folio-validacion-mensaje'
        );


    if (inputNumeroFolio) {

        inputNumeroFolio.addEventListener(
            'input',
            () => {

                /*
                 * Al cambiar el folio eliminamos cualquier
                 * error personalizado de la consulta anterior.
                 */
                inputNumeroFolio.setCustomValidity(
                    ''
                );


                if (mensajeFolio) {

                    mensajeFolio.textContent =
                        'Captura únicamente el número correspondiente al folio.';

                }

            }
        );

    }

    /* =====================================================
       SIGUIENTE
    ===================================================== */

    botonSiguiente.addEventListener(
        'click',
        async () => {

            if (
                guardando
                || pasoActual >= totalPasos
            ) {
                return;
            }


            const paso =
                obtenerPaso(
                    pasos,
                    pasoActual
                );


            if (!paso) {
                return;
            }


            /*
             * Validaciones normales del paso.
             */
            if (
                !validarPasoActual(
                    paso
                )
            ) {
                return;
            }


            /*
             * Validaciones especiales de campos
             * dinámicos.
             */
            if (
                !validarRelacionesDelPaso(
                    pasoActual,
                    formulario
                )
            ) {
                return;
            }


            /*
             * PASO 1:
             * Validar que el folio no exista
             * previamente en la base de datos.
             */
            if (
                pasoActual === 1
            ) {

                const folioDisponible =
                    await validarFolioDisponible();


                if (!folioDisponible) {
                    return;
                }

            }


            pasosCompletados.add(
                pasoActual
            );


            mostrarPaso(
                pasoActual + 1
            );

        }
    );


    /* =====================================================
       ANTERIOR
    ===================================================== */

    botonAnterior.addEventListener(
        'click',
        () => {

            if (
                guardando
                || pasoActual <= 1
            ) {
                return;
            }


            mostrarPaso(
                pasoActual - 1
            );

        }
    );


    /* =====================================================
       GUARDAR REPORTE
    ===================================================== */

    formulario.addEventListener(
        'submit',
        async (evento) => {

            evento.preventDefault();


            if (guardando) {
                return;
            }


            /*
             * Antes del envío volvemos a validar
             * TODOS los pasos.
             *
             * Esto es importante porque el usuario
             * puede regresar a un paso anterior y
             * modificar un campo después de haberlo
             * validado.
             */
            const validacion =
                validarFormularioCompleto(
                    pasos,
                    formulario
                );


            if (!validacion.valido) {

                mostrarPaso(
                    validacion.paso
                );

                return;
            }


            guardando =
                true;


            establecerEstadoGuardando(
                botonGuardar,
                true
            );


            try {

                /* =============================================
                   FORM DATA
                ============================================= */

                const datos =
                    new FormData(
                        formulario
                    );


                /* =============================================
                   ENDPOINT
                ============================================= */

                const url =
                    construirUrlGuardar();


                /* =============================================
                   REQUEST
                ============================================= */

                const respuesta =
                    await fetch(
                        url,
                        {
                            method: 'POST',

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


                /* =============================================
                   RESPUESTA
                ============================================= */

                let resultado = null;


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
                    || !resultado?.success
                ) {

                    throw new Error(
                        resultado?.message
                        || 'No fue posible guardar el reporte.'
                    );

                }


                /* =============================================
                   MARCAR TODOS COMO COMPLETADOS
                ============================================= */

                for (
                    let numero = 1;
                    numero <= totalPasos;
                    numero++
                ) {

                    pasosCompletados.add(
                        numero
                    );

                }


                actualizarIndicadoresGuardados(
                    indicadores,
                    pasosCompletados
                );


                /* =============================================
                   ÉXITO
                ============================================= */

                mostrarResultadoYRedirigir({

                    tipo:
                        'success',

                    titulo:
                        'Reporte guardado',

                    mensaje:
                        resultado.message
                        || 'El reporte se registró correctamente.',

                    /* url:
                        '/asuntos-internos/reportes/listado', */

                        url:
    '/DataCore/public/asuntos-internos/reportes/listado',

                    duracion:
                        2000,

                });


            } catch (error) {

                console.error(
                    'Error guardando reporte:',
                    error
                );


                mostrarResultadoYRedirigir({

                    tipo:
                        'error',

                    titulo:
                        'No fue posible guardar',

                    mensaje:
                        error.message
                        || 'Ocurrió un error al registrar el reporte.',

                    duracion:
                        3000,

                });


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


    /* =====================================================
       ESTADO INICIAL
    ===================================================== */

    mostrarPaso(
        1
    );

}


/* =========================================================
   OBTENER PASO
========================================================= */

function obtenerPaso(
    pasos,
    numeroPaso
) {

    return pasos.find(
        (elemento) => {

            return Number(
                elemento.dataset.step
            ) === numeroPaso;

        }
    )
        || null;

}


/* =========================================================
   VALIDAR FORMULARIO COMPLETO
========================================================= */

function validarFormularioCompleto(
    pasos,
    formulario
) {

    for (
        const paso
        of pasos
    ) {

        const numeroPaso =
            Number(
                paso.dataset.step
            );


        /*
         * Primero validamos los campos
         * HTML requeridos.
         */
        if (
            !validarPasoActual(
                paso,
                false
            )
        ) {

            mostrarPrimerCampoInvalido(
                paso
            );


            return {
                valido: false,
                paso: numeroPaso,
            };

        }


        /*
         * Después validamos las relaciones
         * dinámicas.
         */
        if (
            !validarRelacionesDelPaso(
                numeroPaso,
                formulario
            )
        ) {

            return {
                valido: false,
                paso: numeroPaso,
            };

        }

    }


    return {
        valido: true,
        paso: null,
    };

}


/* =========================================================
   VALIDAR PASO ACTUAL
========================================================= */

function validarPasoActual(
    paso,
    mostrarError = true
) {

    const camposValidables =
        Array.from(
            paso.querySelectorAll(
                `
                input[required]:not([data-validacion-dinamica]),
                input[type="email"]:not([data-validacion-dinamica]),
                select[required]:not([data-validacion-dinamica]),
                textarea[required]:not([data-validacion-dinamica])
                `
            )
        );


    for (
        const campo
        of camposValidables
    ) {

        /*
         * Los campos deshabilitados
         * no participan en la validación.
         */
        if (campo.disabled) {
            continue;
        }


        if (
            !campo.checkValidity()
        ) {

            if (mostrarError) {

                campo.reportValidity();

                campo.focus();

            }


            return false;
        }

    }


    return true;

}


/* =========================================================
   MOSTRAR PRIMER CAMPO INVÁLIDO
========================================================= */

function mostrarPrimerCampoInvalido(
    paso
) {

    /*
     * Esperamos a que mostrarPaso()
     * haga visible la sección.
     */
    window.setTimeout(
        () => {

            const campo =
                paso.querySelector(
                    `
                    input:invalid,
                    select:invalid,
                    textarea:invalid
                    `
                );


            if (!campo) {
                return;
            }


            campo.reportValidity();

            campo.focus();

        },
        100
    );

}


/* =========================================================
   VALIDACIONES DINÁMICAS
========================================================= */

function validarRelacionesDelPaso(
    numeroPaso,
    formulario
) {

    /*
     * PASO 3:
     * Personal y unidades.
     *
     * Los registros dinámicos se representan
     * mediante los hidden inputs generados por
     * personal.js y unidades.js.
     */
    if (
        numeroPaso !== 3
    ) {
        return true;
    }


    /* =====================================================
       PERSONAL-------------
    ===================================================== */

    const personal =
        formulario.querySelectorAll(
            `
            #personal-hidden-inputs
            input[name^="personal["],

            #editar-personal-hidden-inputs
            input[name^="personal["]
            `
        );


    /*
     * Buscamos directamente cualquier input
     * personal[...] dentro del formulario para
     * no depender del nombre exacto del contenedor.
     */
    const personalReal =
        formulario.querySelectorAll(
            'input[name^="personal["]'
        );


    if (
        personalReal.length === 0
    ) {

        mostrarResultadoYRedirigir({

            tipo:
                'error',

            titulo:
                'Personal requerido',

            mensaje:
                'Agrega al menos una persona relacionada con el reporte.',

            duracion:
                3000,

        });


        return false;
    }


    /*
     * La variable anterior solo se conserva
     * para dejar explícita la intención.
     */
    void personal;


    /* =====================================================
       UNIDADES----------
    ===================================================== */

    const unidades =
        formulario.querySelectorAll(
            'input[name^="unidades["]'
        );


    /*
     * De acuerdo con el diseño actual, la unidad
     * también está marcada como requerida.
     */
    if (
        unidades.length === 0
    ) {

        mostrarResultadoYRedirigir({

            tipo:
                'error',

            titulo:
                'Unidad requerida',

            mensaje:
                'Agrega al menos una unidad relacionada con el reporte.',

            duracion:
                3000,

        });


        return false;
    }


    return true;

}


/* =========================================================
   URL GUARDAR
========================================================= */

/* function construirUrlGuardar() {

    const base =
        document
            .querySelector('base')
            ?.href;


    if (base) {

        return new URL(
            'asuntos-internos/reportes/guardar',
            base
        ).toString();

    }


    return `${window.location.origin
        }/asuntos-internos/reportes/guardar`;

} */

function construirUrlGuardar() {

    return new URL(
        'DataCore/public/asuntos-internos/reportes/guardar',
        `${window.location.origin}/`
    ).toString();

}

/* =========================================================
   ESTADO BOTÓN GUARDAR
========================================================= */

function establecerEstadoGuardando(
    boton,
    guardando
) {

    boton.disabled =
        guardando;


    if (guardando) {

        boton.dataset.textoOriginal =
            boton.textContent;


        boton.textContent =
            'Guardando...';


        return;
    }


    boton.textContent =
        boton.dataset.textoOriginal
        || 'Guardar reporte';


    delete boton.dataset.textoOriginal;

}


/* =========================================================
   INDICADORES COMPLETADOS
========================================================= */

function actualizarIndicadoresGuardados(
    indicadores,
    pasosCompletados
) {

    indicadores.forEach(
        (indicador) => {

            const numero =
                Number(
                    indicador
                        .dataset
                        .stepIndicator
                );


            indicador.classList.remove(
                'report-steps__item--active'
            );


            indicador.classList.toggle(
                'report-steps__item--completed',
                pasosCompletados.has(
                    numero
                )
            );

        }
    );

}

async function validarFolioDisponible() {

    const input =
        document.querySelector(
            '#numero_folio'
        );


    const mensaje =
        document.querySelector(
            '#folio-validacion-mensaje'
        );


    if (!input) {
        return true;
    }


    const numero =
        String(
            input.value
            || ''
        ).trim();


    if (numero === '') {
        return false;
    }


    const folio =
        `QJ-${numero}`;


    try {

        /* const url =
            new URL(
                'asuntos-internos/reportes/validar-folio',
                `${window.location.origin}/`
            ); */

const url =
    new URL(
        'DataCore/public/asuntos-internos/reportes/validar-folio',
        `${window.location.origin}/`
    );


        url.searchParams.set(
            'folio',
            folio
        );


        const respuesta =
            await fetch(
                url.toString(),
                {
                    headers: {
                        Accept:
                            'application/json',
                    },

                    credentials:
                        'same-origin',
                }
            );


        const datos =
            await respuesta.json();


        if (
            !respuesta.ok
            || datos.success !== true
        ) {

            throw new Error(
                datos.message
                || 'No fue posible validar el folio.'
            );
        }


        if (datos.existe) {

            input.setCustomValidity(
                'Este folio ya está registrado.'
            );


            if (mensaje) {

                mensaje.textContent =
                    `El folio ${folio} ya está registrado.`;

            }


            input.reportValidity();

            input.focus();


            return false;
        }


        input.setCustomValidity(
            ''
        );


        if (mensaje) {

            mensaje.textContent =
                `El folio ${folio} está disponible.`;

        }


        return true;


    } catch (error) {

        console.error(
            'Error validando folio:',
            error
        );


        input.setCustomValidity(
            'No fue posible validar el folio.'
        );


        input.reportValidity();


        return false;
    }
}