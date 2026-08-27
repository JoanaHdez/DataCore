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

    const totalPasos = pasos.length;


    /*
     * Mostrar paso correspondiente
     */
    function mostrarPaso(numeroPaso) {

        pasoActual = numeroPaso;


        /*
         * Secciones
         */
        pasos.forEach((paso) => {

            const numero =
                Number(paso.dataset.step);

            paso.classList.toggle(
                'report-step--active',
                numero === pasoActual
            );

        });


        /*
         * Indicadores superiores
         */
        indicadores.forEach((indicador) => {

            const numero =
                Number(
                    indicador.dataset.stepIndicator
                );

            indicador.classList.toggle(
                'report-steps__item--active',
                numero === pasoActual
            );

        });


        botonAnterior.classList.toggle(
            'report-step-control--hidden',
            pasoActual === 1
        );

        botonSiguiente.classList.toggle(
            'report-step-control--hidden',
            pasoActual === totalPasos
        );

        botonGuardar.classList.toggle(
            'report-step-control--hidden',
            pasoActual !== totalPasos
        );

        /*
         * Subimos al inicio del formulario
         * al cambiar de paso.
         */
        formulario.scrollIntoView({
            behavior: 'smooth',
            block: 'start',
        });
    }


    /*
     * SIGUIENTE
     */
    botonSiguiente.addEventListener(
        'click',
        () => {

            if (pasoActual >= totalPasos) {
                return;
            }


            /*
             * Obtenemos el bloque actualmente visible.
             */
            const paso =
                pasos.find(
                    (elemento) =>
                        Number(elemento.dataset.step)
                        === pasoActual
                );


            if (!paso) {
                return;
            }


            /*
             * Antes de avanzar validamos únicamente
             * los campos obligatorios de este paso.
             */
            if (!validarPasoActual(paso)) {
                return;
            }


            /*
             * Todo correcto.
             */
            mostrarPaso(
                pasoActual + 1
            );
        }
    );


    /*
     * ANTERIOR
     */
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


    /*
     * Estado inicial
     */
    mostrarPaso(1);
}

/* =========================================================
   VALIDAR PASO ACTUAL
========================================================= */

function validarPasoActual(paso) {

    const camposObligatorios =
        Array.from(
            paso.querySelectorAll(
                'input[required], select[required], textarea[required]'
            )
        );


    for (const campo of camposObligatorios) {

        /*
         * Ignoramos campos deshabilitados.
         */
        if (campo.disabled) {
            continue;
        }


        /*
         * Utilizamos la validación nativa
         * del navegador.
         */
        if (!campo.checkValidity()) {

            /*
             * Mostramos el mensaje nativo.
             */
            campo.reportValidity();


            /*
             * Llevamos el foco al campo
             * que necesita atención.
             */
            campo.focus();


            return false;
        }
    }


    return true;
}