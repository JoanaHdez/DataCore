import {
    mostrarResultadoYRedirigir
} from '../notificaciones/resultado.js';

document.addEventListener('DOMContentLoaded', () => {
    inicializarFormularioPorPasos();
});


function inicializarFormularioPorPasos() {

    const formulario =
        document.querySelector('#form-nuevo-reporte');

    if (!formulario) {
        return;
    }


    const pasos =
        Array.from(
            formulario.querySelectorAll('.report-step')
        );

    const indicadores =
        Array.from(
            formulario.querySelectorAll(
                '[data-step-indicator]'
            )
        );

    const botonAnterior =
        formulario.querySelector('#btn-step-anterior');

    const botonSiguiente =
        formulario.querySelector('#btn-step-siguiente');

    const botonGuardar =
        formulario.querySelector('#btn-guardar-reporte');


    if (
        !pasos.length
        || !botonAnterior
        || !botonSiguiente
        || !botonGuardar
    ) {
        return;
    }


    let pasoActual = 1;

    const totalPasos =
        pasos.length;


    /*
     * Pasos completados correctamente.
     */
    const pasosCompletados =
        new Set();


    /* =========================================================
       MOSTRAR PASO
    ========================================================= */

    function mostrarPaso(numeroPaso) {

        pasoActual =
            numeroPaso;


        /*
         * Mostrar únicamente
         * el bloque correspondiente.
         */
        pasos.forEach((paso) => {

            const numero =
                Number(
                    paso.dataset.step
                );

            paso.classList.toggle(
                'report-step--active',
                numero === pasoActual
            );

        });


        /*
         * Actualizar indicador superior.
         */
        indicadores.forEach((indicador) => {

            const numero =
                Number(
                    indicador.dataset.stepIndicator
                );


            /*
             * Paso actual = azul.
             */
            indicador.classList.toggle(
                'report-steps__item--active',
                numero === pasoActual
            );


            /*
             * Paso completado = verde.
             */
            indicador.classList.toggle(
                'report-steps__item--completed',
                pasosCompletados.has(numero)
                && numero !== pasoActual
            );

        });


        /*
         * Botón Anterior.
         */
        botonAnterior.classList.toggle(
            'report-step-control--hidden',
            pasoActual === 1
        );


        /*
         * Botón Siguiente.
         */
        botonSiguiente.classList.toggle(
            'report-step-control--hidden',
            pasoActual === totalPasos
        );


        /*
         * Guardar solo aparece
         * en el último paso.
         */
        botonGuardar.classList.toggle(
            'report-step-control--hidden',
            pasoActual !== totalPasos
        );


        /*
         * Regresamos visualmente
         * al inicio del formulario.
         */
        formulario.scrollIntoView({
            behavior: 'smooth',
            block: 'start',
        });
    }


    /* =========================================================
       SIGUIENTE
    ========================================================= */

    botonSiguiente.addEventListener(
        'click',
        () => {

            if (
                pasoActual
                >= totalPasos
            ) {
                return;
            }


            const paso =
                pasos.find(
                    (elemento) =>
                        Number(
                            elemento.dataset.step
                        ) === pasoActual
                );


            if (!paso) {
                return;
            }


            /*
             * Validamos únicamente
             * el paso actual.
             */
            if (
                !validarPasoActual(
                    paso
                )
            ) {
                return;
            }


            /*
             * Marcamos el paso
             * como completado.
             */
            pasosCompletados.add(
                pasoActual
            );


            /*
             * Avanzamos.
             */
            mostrarPaso(
                pasoActual + 1
            );

        }
    );


    /* =========================================================
       ANTERIOR
    ========================================================= */

    botonAnterior.addEventListener(
        'click',
        () => {

            if (pasoActual <= 1) {
                return;
            }


            mostrarPaso(
                pasoActual - 1
            );

        }
    );


    /* =========================================================
       GUARDAR REPORTE
    ========================================================= */

    formulario.addEventListener(
        'submit',
        (evento) => {

            /*
             * TEMPORAL:
             *
             * Todavía no enviamos al backend.
             * Esto cambiará cuando conectemos BD.
             */
            evento.preventDefault();


            const ultimoPaso =
                pasos.find(
                    (elemento) =>
                        Number(
                            elemento.dataset.step
                        ) === totalPasos
                );


            if (!ultimoPaso) {
                return;
            }


            /*
             * Validamos el último paso.
             */
            if (
                !validarPasoActual(
                    ultimoPaso
                )
            ) {
                return;
            }


            /*
             * Marcamos el último paso
             * como completado.
             */
            pasosCompletados.add(
                totalPasos
            );


            /*
             * Actualizamos visualmente
             * el indicador final.
             */
            indicadores.forEach(
                (indicador) => {

                    const numero =
                        Number(
                            indicador
                                .dataset
                                .stepIndicator
                        );


                    if (
                        numero
                        === totalPasos
                    ) {

                        indicador.classList.remove(
                            'report-steps__item--active'
                        );

                        indicador.classList.add(
                            'report-steps__item--completed'
                        );

                    }

                }
            );


            /*
             * MODAL TEMPORAL DE PRUEBA.
             *
             * Cuando conectemos el backend,
             * se mostrará únicamente después
             * de recibir confirmación real.
             */
            mostrarResultadoYRedirigir({
    tipo: 'success',
    titulo: 'Reporte guardado',
    mensaje:
        'El reporte se registró correctamente.',
    url: '/asuntos-internos/reportes/listado',
    duracion: 2000,
});

        }
    );


    /*
     * Estado inicial.
     */
    mostrarPaso(1);
}


/* =========================================================
   VALIDAR PASO ACTUAL
========================================================= */

function validarPasoActual(
    paso
) {

    const camposObligatorios =
        Array.from(
            paso.querySelectorAll(
                `
                input[required],
                select[required],
                textarea[required]
                `
            )
        );


    for (
        const campo
        of camposObligatorios
    ) {

        /*
         * El navegador ignora
         * campos deshabilitados.
         */
        if (campo.disabled) {
            continue;
        }


        /*
         * Validación nativa.
         */
        if (!campo.checkValidity()) {

            campo.reportValidity();

            campo.focus();

            return false;
        }

    }


    return true;
}