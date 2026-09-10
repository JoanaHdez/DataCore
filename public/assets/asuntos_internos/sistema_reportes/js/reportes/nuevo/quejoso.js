document.addEventListener(
    'DOMContentLoaded',
    () => {

        inicializarCanalizacionQuejoso();

        inicializarQuejosoAnonimo();
    }
);

/* =========================================================
   SISTEMA DE REPORTES - ASUNTOS INTERNOS
   NUEVO REPORTE
   DATOS DEL QUEJOSO
========================================================= */

document.addEventListener(
    'DOMContentLoaded',
    () => {
        inicializarCanalizacionQuejoso();
    }
);


/* =========================================================
   CANALIZACIÓN
========================================================= */

function inicializarCanalizacionQuejoso() {

    const select =
        document.querySelector(
            '#canalizacion'
        );


    const contenedorOtro =
        document.querySelector(
            '#canalizacion-otro-contenedor'
        );


    const inputOtro =
        document.querySelector(
            '#canalizacion_otro'
        );


    if (
        !select
        || !contenedorOtro
        || !inputOtro
    ) {
        return;
    }


    /* =====================================================
       ACTUALIZAR ESTADO
    ===================================================== */

    function actualizarCanalizacion() {

        const esOtro =
            select.value === 'OTRO';


        contenedorOtro.hidden =
            !esOtro;


        inputOtro.disabled =
            !esOtro;


        inputOtro.required =
            esOtro;


        /* =================================================
           SI DEJA DE SER "OTRO"
           LIMPIAR VALOR
        ================================================= */

        if (
            !esOtro
        ) {

            inputOtro.value =
                '';
        }
    }


    /* =====================================================
       CAMBIO
    ===================================================== */

    select.addEventListener(
        'change',
        actualizarCanalizacion
    );


    /* =====================================================
       ESTADO INICIAL
    ===================================================== */

    actualizarCanalizacion();
}

/* =========================================================
   QUEJOSO ANÓNIMO
========================================================= */

function inicializarQuejosoAnonimo() {

    const radioAnonimo =
        document.querySelector(
            '#quejoso-anonimo'
        );


    const radioNoAnonimo =
        document.querySelector(
            '#quejoso-no-anonimo'
        );


    const contenedorNumero =
        document.querySelector(
            '#numero-anonimo-contenedor'
        );


    const inputNumero =
        document.querySelector(
            '#numero_anonimo'
        );


    const camposQuejoso = [

        document.querySelector('#quejoso'),

        document.querySelector('#edad'),

        document.querySelector('#genero'),

        document.querySelector('#telefono'),

        document.querySelector('#correo'),

        document.querySelector('#canalizacion'),

        document.querySelector('#canalizacion_otro'),
    ];


    if (
        !radioAnonimo
        || !radioNoAnonimo
        || !contenedorNumero
        || !inputNumero
    ) {
        return;
    }


    /* =====================================================
       ACTUALIZAR ESTADO
    ===================================================== */

    function actualizarEstadoAnonimo() {

        const esAnonimo =
            radioAnonimo.checked;


        /* =================================================
           NÚMERO ANÓNIMO
        ================================================= */

        contenedorNumero.hidden =
            !esAnonimo;


        inputNumero.disabled =
            !esAnonimo;


        inputNumero.required =
            esAnonimo;


        /* =================================================
           CAMPOS DEL QUEJOSO
        ================================================= */

        camposQuejoso.forEach(
            (campo) => {

                if (!campo) {
                    return;
                }


                campo.disabled =
                    esAnonimo;


                /*
                 * Los campos obligatorios dejan de serlo
                 * mientras la queja sea anónima.
                 */

                if (
                    esAnonimo
                ) {

                    campo.dataset.requiredOriginal =
                        campo.required
                            ? '1'
                            : '0';


                    campo.required =
                        false;

                } else {

                    if (
                        campo.dataset.requiredOriginal === '1'
                    ) {

                        campo.required =
                            true;
                    }


                    delete campo.dataset.requiredOriginal;
                }
            }
        );


        /* =================================================
           SI DEJA DE SER ANÓNIMO
        ================================================= */

        if (
            !esAnonimo
        ) {

            inputNumero.value =
                '';
        }
    }


    /* =====================================================
       EVENTOS
    ===================================================== */

    radioAnonimo.addEventListener(
        'change',
        actualizarEstadoAnonimo
    );


    radioNoAnonimo.addEventListener(
        'change',
        actualizarEstadoAnonimo
    );


    /* =====================================================
       ESTADO INICIAL
    ===================================================== */

    actualizarEstadoAnonimo();
}