/* =========================================================
   SISTEMA DE REPORTES - ASUNTOS INTERNOS
   NUEVO REPORTE
   DATOS DEL QUEJOSO
========================================================= */

document.addEventListener(
    'DOMContentLoaded',
    () => {

        inicializarCanalizacionQuejoso();

        inicializarQuejosoAnonimo();
    }
);


/* =========================================================
   CANALIZACIÓN
========================================================= */

function inicializarCanalizacionQuejoso() {

    const selector =
        document.querySelector(
            '#canalizacion-select'
        );


    const textoSelector =
        document.querySelector(
            '#canalizacion-select-texto'
        );


    const inputCanalizacion =
        document.querySelector(
            '#canalizacion'
        );


    const resultados =
        document.querySelector(
            '#canalizacion-resultados'
        );


    const opciones =
        document.querySelectorAll(
            '[data-canalizacion-opcion]'
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
        !selector
        || !textoSelector
        || !inputCanalizacion
        || !resultados
        || !contenedorOtro
        || !inputOtro
    ) {
        return;
    }


    /* =====================================================
       ABRIR / CERRAR
    ===================================================== */

    function abrirCatalogo() {

        resultados.hidden =
            false;


        selector.setAttribute(
            'aria-expanded',
            'true'
        );


        selector.classList.add(
            'canalizacion-select--activo'
        );
    }


    function cerrarCatalogo() {

        resultados.hidden =
            true;


        selector.setAttribute(
            'aria-expanded',
            'false'
        );


        selector.classList.remove(
            'canalizacion-select--activo'
        );
    }


    /* =====================================================
       SELECCIONAR
    ===================================================== */

    function seleccionarCanalizacion(
        valor
    ) {

        const nombre =
            String(
                valor
                || ''
            ).trim();


        inputCanalizacion.value =
            nombre;


        textoSelector.textContent =
            nombre !== ''
                ? nombre
                : 'Sin canalización';


        const esOtro =
            nombre === 'Otro';


        contenedorOtro.hidden =
            !esOtro;


        inputOtro.disabled =
            !esOtro;


        inputOtro.required =
            esOtro;


        if (
            !esOtro
        ) {

            inputOtro.value =
                '';
        }


        cerrarCatalogo();
    }


    /* =====================================================
       CLICK SELECTOR
    ===================================================== */

    selector.addEventListener(
        'click',
        () => {

            if (
                resultados.hidden
            ) {

                abrirCatalogo();

            } else {

                cerrarCatalogo();
            }
        }
    );


    /* =====================================================
       OPCIONES
    ===================================================== */

    opciones.forEach(
        (opcion) => {

            opcion.addEventListener(
                'click',
                () => {

                    seleccionarCanalizacion(
                        opcion.dataset.canalizacionNombre
                    );
                }
            );
        }
    );


    /* =====================================================
       CLICK FUERA
    ===================================================== */

    document.addEventListener(
        'click',
        (evento) => {

            if (
                selector.contains(
                    evento.target
                )
                || resultados.contains(
                    evento.target
                )
            ) {
                return;
            }


            cerrarCatalogo();
        }
    );


    /* =====================================================
       ESC
    ===================================================== */

    document.addEventListener(
        'keydown',
        (evento) => {

            if (
                evento.key === 'Escape'
            ) {

                cerrarCatalogo();
            }
        }
    );


    /* =====================================================
       ESTADO INICIAL
    ===================================================== */

    seleccionarCanalizacion(
        ''
    );
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

        document.querySelector(
            '#quejoso'
        ),

        document.querySelector(
            '#edad'
        ),

        document.querySelector(
            '#genero'
        ),

        document.querySelector(
            '#telefono'
        ),

        document.querySelector(
            '#correo'
        ),

        document.querySelector(
            '#buscar-canalizacion'
        ),

        document.querySelector(
            '#canalizacion'
        ),

        document.querySelector(
            '#canalizacion_otro'
        ),
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

                if (
                    !campo
                ) {
                    return;
                }


                campo.disabled =
                    esAnonimo;


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
                        campo.dataset.requiredOriginal
                        === '1'
                    ) {

                        campo.required =
                            true;
                    }


                    delete campo.dataset.requiredOriginal;
                }
            }
        );


        /* =================================================
           BOTÓN QUITAR CANALIZACIÓN
        ================================================= */

        const botonQuitar =
            document.querySelector(
                '#btn-quitar-canalizacion'
            );


        if (
            botonQuitar
        ) {

            botonQuitar.disabled =
                esAnonimo;
        }


        /* =================================================
           RESULTADOS
        ================================================= */

        const resultados =
            document.querySelector(
                '#canalizacion-resultados'
            );


        if (
            esAnonimo
            && resultados
        ) {

            resultados.hidden =
                true;
        }


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