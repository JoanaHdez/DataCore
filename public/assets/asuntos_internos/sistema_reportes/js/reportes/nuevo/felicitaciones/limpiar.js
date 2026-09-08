/* =========================================================
   SISTEMA DE REPORTES - ASUNTOS INTERNOS
   Felicitaciones - Limpiar sección
========================================================= */

document.addEventListener(
    'DOMContentLoaded',
    () => {
        inicializarLimpiarFelicitacion();
    }
);


/* =========================================================
   INICIALIZAR
========================================================= */

function inicializarLimpiarFelicitacion() {

    const botones =
        document.querySelectorAll(
            '[data-limpiar-felicitacion-paso]'
        );


    if (
        botones.length === 0
    ) {
        return;
    }


    botones.forEach(
        (boton) => {

            boton.addEventListener(
                'click',
                () => {

                    const paso =
                        Number(
                            boton.dataset
                                .limpiarFelicitacionPaso
                            || 0
                        );


                    if (
                        paso === 1
                    ) {

                        limpiarPasoUnoFelicitacion();

                        return;
                    }


                    if (
                        paso === 2
                    ) {

                        limpiarPasoDosFelicitacion();
                    }
                }
            );
        }
    );
}


/* =========================================================
   PASO 1
========================================================= */

function limpiarPasoUnoFelicitacion() {

    const nombre =
        document.querySelector(
            '#nombre_felicitante'
        );


    const razon =
        document.querySelector(
            '#razon_felicitacion'
        );


    if (nombre) {

        nombre.value =
            '';
    }


    if (razon) {

        razon.value =
            '';
    }


    nombre?.focus();
}


/* =========================================================
   PASO 2
========================================================= */

function limpiarPasoDosFelicitacion() {

    /* =====================================================
       BUSCADOR DE PERSONAL
    ===================================================== */

    const buscadorPersonal =
        document.querySelector(
            '#felicitacion-oficial'
        );


    if (buscadorPersonal) {

        buscadorPersonal.value =
            '';
    }


    /* =====================================================
       RESULTADOS DE PERSONAL
    ===================================================== */

    const resultadosPersonal =
        document.querySelector(
            '#felicitacion-personal-resultados'
        );


    if (resultadosPersonal) {

        resultadosPersonal.innerHTML =
            '';

        resultadosPersonal.hidden =
            true;
    }


    /* =====================================================
       PERSONA SELECCIONADA
    ===================================================== */

    const personaSeleccionada =
        document.querySelector(
            '#felicitacion-personal-seleccionado'
        );


    if (personaSeleccionada) {

        personaSeleccionada.hidden =
            true;
    }


    const camposPersonal = [

        '#felicitacion-personal-plantilla-id',
        '#felicitacion-personal-perscod',
        '#felicitacion-personal-nombre',
        '#felicitacion-personal-area',
        '#felicitacion-personal-turno',
        '#felicitacion-personal-alias',

    ];


    camposPersonal.forEach(
        (selector) => {

            const campo =
                document.querySelector(
                    selector
                );


            if (campo) {

                campo.value =
                    '';
            }
        }
    );


    /* =====================================================
       FOTO
    ===================================================== */

    const foto =
        document.querySelector(
            '#felicitacion-personal-foto'
        );


    const fallback =
        document.querySelector(
            '#felicitacion-personal-foto-fallback'
        );


    if (foto) {

        foto.src =
            '';

        foto.hidden =
            true;
    }


    if (fallback) {

        fallback.textContent =
            '—';

        fallback.hidden =
            false;
    }


    /* =====================================================
       PERSONAL AGREGADO
    ===================================================== */

    const personalAgregado =
        document.querySelector(
            '#felicitacion-personal-agregado'
        );


    const personalBody =
        document.querySelector(
            '#felicitacion-personal-agregado-body'
        );


    const personalHidden =
        document.querySelector(
            '#felicitacion-personal-hidden-inputs'
        );


    if (personalBody) {

        personalBody.innerHTML =
            '';
    }


    if (personalHidden) {

        personalHidden.innerHTML =
            '';
    }


    if (personalAgregado) {

        personalAgregado.hidden =
            true;

        personalAgregado.dataset.totalPersonal =
            '0';
    }


    /* =====================================================
       UNIDADES
    ===================================================== */

    document.dispatchEvent(
        new CustomEvent(
            'limpiarUnidadesFelicitacion'
        )
    );
}