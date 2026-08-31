document.addEventListener('DOMContentLoaded', () => {
    inicializarSanciones();
});


function inicializarSanciones() {

    const sancion =
        document.querySelector(
            '#sancion_disciplinaria'
        );

    const contenedorOtro =
        document.querySelector(
            '#campo-sancion-otro'
        );

    const sancionOtro =
        document.querySelector(
            '#sancion_otro'
        );


    if (
        !sancion
        || !contenedorOtro
        || !sancionOtro
    ) {
        return;
    }


    function actualizarCampoOtro() {

        const esOtro =
            sancion.value === 'Otro';


        contenedorOtro.hidden =
            !esOtro;


        sancionOtro.disabled =
            !esOtro;


        sancionOtro.required =
            esOtro;


        /*
         * Si cambia de "Otro" a Arresto,
         * Amonestación o Sin sanción,
         * eliminamos cualquier texto anterior.
         */
        if (!esOtro) {

            sancionOtro.value =
                '';
        }
    }


    sancion.addEventListener(
        'change',
        actualizarCampoOtro
    );


    /*
     * También se ejecuta al cargar.
     *
     * Esto será importante posteriormente cuando
     * carguemos un reporte existente para editarlo.
     */
    actualizarCampoOtro();
}