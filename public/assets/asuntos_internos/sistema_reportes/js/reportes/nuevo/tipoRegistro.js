document.addEventListener(
    'DOMContentLoaded',
    () => {
        inicializarSelectorTipoRegistro();
    }
);


/* =========================================================
   SELECTOR DE TIPO DE REGISTRO
========================================================= */

function inicializarSelectorTipoRegistro() {

    const modal =
        document.querySelector(
            '#modal-tipo-registro'
        );


    const contenedorQueja =
        document.querySelector(
            '#contenedor-registro-queja'
        );


    const contenedorFelicitacion =
        document.querySelector(
            '#contenedor-registro-felicitacion'
        );


    const botones =
        document.querySelectorAll(
            '[data-tipo-registro]'
        );


    if (
        !modal
        || !contenedorQueja
        || !contenedorFelicitacion
        || botones.length === 0
    ) {
        return;
    }


    botones.forEach(
        (boton) => {

            boton.addEventListener(
                'click',
                async () => {

                    const tipoRegistro =
                        String(
                            boton.dataset.tipoRegistro
                            || ''
                        )
                            .trim()
                            .toUpperCase();


                    await seleccionarTipoRegistro(
                        tipoRegistro,
                        modal,
                        contenedorQueja,
                        contenedorFelicitacion
                    );
                }
            );
        }
    );
}


/* =========================================================
   SELECCIONAR TIPO
========================================================= */

async function seleccionarTipoRegistro(
    tipoRegistro,
    modal,
    contenedorQueja,
    contenedorFelicitacion
) {

    /* =====================================================
       OCULTAR FORMULARIOS
    ===================================================== */

    contenedorQueja.hidden =
        true;


    contenedorFelicitacion.hidden =
        true;


    /* =====================================================
       MOSTRAR FORMULARIO CORRESPONDIENTE
    ===================================================== */

    switch (
        tipoRegistro
    ) {

        /* =================================================
           QUEJA
        ================================================= */

        case 'QUEJA':

            contenedorQueja.hidden =
                false;

            break;


        /* =================================================
           FELICITACIÓN
        ================================================= */

        case 'FELICITACION':

            contenedorFelicitacion.hidden =
                false;


            await cargarPrevisualizacionFelicitacion();

            break;


        default:

            console.warn(
                'Tipo de registro no reconocido:',
                tipoRegistro
            );

            return;
    }


    /* =====================================================
       CERRAR MODAL
    ===================================================== */

    modal.hidden =
        true;


    modal.setAttribute(
        'aria-hidden',
        'true'
    );
}


/* =========================================================
   PREVISUALIZAR FOLIO DE FELICITACIÓN
========================================================= */

async function cargarPrevisualizacionFelicitacion() {

    const inputFolio =
        document.querySelector(
            '#felicitacion-folio-visual'
        );


    const inputNomenclatura =
        document.querySelector(
            '#felicitacion-nomenclatura'
        );


    if (
        !inputFolio
        || !inputNomenclatura
    ) {

        console.error(
            'No se encontraron los campos de folio de felicitación.'
        );

        return;
    }


    /* =====================================================
       ESTADO DE CARGA
    ===================================================== */

    inputFolio.value =
        'Consultando...';


    inputNomenclatura.value =
        'Consultando...';


    try {

        /* =================================================
           URL
        ================================================= */

        const url =
            new URL(
                'DataCore/public/asuntos-internos/reportes/previsualizar-folio',
                `${window.location.origin}/`
            );


        url.searchParams.set(
            'tipo_registro',
            'FELICITACION'
        );


        /* =================================================
           CONSULTAR BACKEND
        ================================================= */

        const respuesta =
            await fetch(
                url.toString(),
                {
                    method:
                        'GET',

                    headers: {
                        Accept:
                            'application/json',
                    },

                    credentials:
                        'same-origin',
                }
            );


        /* =================================================
           RESPUESTA JSON
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


        console.log(
            'Previsualización FEL:',
            resultado
        );


        if (
            !respuesta.ok
            || resultado?.success !== true
        ) {

            throw new Error(
                resultado?.message
                || 'No fue posible consultar el siguiente folio.'
            );
        }


        /* =================================================
           VALORES
        ================================================= */

        const folio =
            String(
                resultado.folio
                || ''
            ).trim();


        const nomenclatura =
            String(
                resultado.nomenclatura
                || ''
            ).trim();


        if (
            folio === ''
            || nomenclatura === ''
        ) {

            throw new Error(
                'El servidor no devolvió los datos del folio.'
            );
        }


        /* =================================================
           MOSTRAR
        ================================================= */

        inputFolio.value =
            folio;


        inputNomenclatura.value =
            nomenclatura;


    } catch (error) {

        console.error(
            'Error previsualizando folio de felicitación:',
            error
        );


        inputFolio.value =
            'FEL- — No disponible';


        inputNomenclatura.value =
            'CGSC/CAI/FEL/ — No disponible';
    }
}