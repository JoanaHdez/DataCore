/* =========================================================
   SISTEMA DE REPORTES - ASUNTOS INTERNOS
   Felicitaciones - Pasos
========================================================= */

document.addEventListener(
    'DOMContentLoaded',
    () => {
        inicializarPasosFelicitacion();
    }
);


/* =========================================================
   INICIALIZAR
========================================================= */

function inicializarPasosFelicitacion() {

    const formulario =
        document.querySelector(
            '#form-nueva-felicitacion'
        );


    const botonSiguiente =
        document.querySelector(
            '#btn-siguiente-felicitacion'
        );


    const botonAnterior =
        document.querySelector(
            '#btn-anterior-felicitacion'
        );


    const paneles =
        document.querySelectorAll(
            '[data-felicitacion-paso]'
        );


    const indicadores =
        document.querySelectorAll(
            '[data-felicitacion-step-indicator]'
        );


    if (
        !formulario
        || !botonSiguiente
        || !botonAnterior
        || paneles.length === 0
        || indicadores.length === 0
    ) {
        return;
    }


    let pasoActual =
        1;


    /* =====================================================
       SIGUIENTE
    ===================================================== */

    botonSiguiente.addEventListener(
        'click',
        () => {

            if (
                !validarPasoUnoFelicitacion()
            ) {
                return;
            }


            pasoActual =
                2;


            mostrarPasoFelicitacion(
                pasoActual,
                paneles,
                indicadores
            );
        }
    );


    /* =====================================================
       ANTERIOR
    ===================================================== */

    botonAnterior.addEventListener(
        'click',
        () => {

            pasoActual =
                1;


            mostrarPasoFelicitacion(
                pasoActual,
                paneles,
                indicadores
            );
        }
    );


    /* =====================================================
       ESTADO INICIAL
    ===================================================== */

    mostrarPasoFelicitacion(
        pasoActual,
        paneles,
        indicadores
    );
}


/* =========================================================
   MOSTRAR PASO
========================================================= */

function mostrarPasoFelicitacion(
    paso,
    paneles,
    indicadores
) {

    /* =====================================================
       PANELES
    ===================================================== */

    paneles.forEach(
        (panel) => {

            const numeroPaso =
                Number(
                    panel.dataset.felicitacionPaso
                    || 0
                );


            panel.hidden =
                numeroPaso !== paso;
        }
    );


    /* =====================================================
       INDICADORES
    ===================================================== */

    indicadores.forEach(
        (indicador) => {

            const numeroPaso =
                Number(
                    indicador.dataset
                        .felicitacionStepIndicator
                    || 0
                );


            /* =============================================
               ACTIVO
            ============================================== */

            indicador.classList.toggle(
                'report-steps__item--active',
                numeroPaso === paso
            );


            /* =============================================
               COMPLETADO
            ============================================== */

            indicador.classList.toggle(
                'report-steps__item--completed',
                numeroPaso < paso
            );
        }
    );


    /* =====================================================
       SUBIR AL INICIO DEL FORMULARIO
    ===================================================== */

    const formulario =
        document.querySelector(
            '#form-nueva-felicitacion'
        );


    formulario?.scrollIntoView({
        behavior:
            'smooth',

        block:
            'start',
    });
}


/* =========================================================
   VALIDAR PASO 1
========================================================= */

function validarPasoUnoFelicitacion() {

    const nombreFelicitante =
        document.querySelector(
            '#nombre_felicitante'
        );


    const razonFelicitacion =
        document.querySelector(
            '#razon_felicitacion'
        );


    if (
        !nombreFelicitante
        || !razonFelicitacion
    ) {
        return false;
    }


    /* =====================================================
       NOMBRE
    ===================================================== */

    if (
        nombreFelicitante.value
            .trim() === ''
    ) {

        nombreFelicitante.reportValidity();

        nombreFelicitante.focus();

        return false;
    }


    /* =====================================================
       RAZÓN
    ===================================================== */

    if (
        razonFelicitacion.value
            .trim() === ''
    ) {

        razonFelicitacion.reportValidity();

        razonFelicitacion.focus();

        return false;
    }


    return true;
}