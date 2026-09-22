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

/* =========================================================
   PREVISUALIZAR FOLIO DE FELICITACIÓN
========================================================= */

async function cargarPrevisualizacionFelicitacion() {

    const inputFolio =
        document.querySelector(
            '#felicitacion-folio-visual'
        );


    const inputNomenclaturaVisual =
        document.querySelector(
            '#felicitacion-nomenclatura-visual'
        );


    const inputNomenclatura =
        document.querySelector(
            '#felicitacion-nomenclatura'
        );


    if (
        !inputFolio
        || !inputNomenclaturaVisual
        || !inputNomenclatura
    ) {

        console.error(
            'No se encontraron los campos de identificación de la felicitación.'
        );

        return;
    }


    /* =====================================================
       ESTADO DE CARGA
    ===================================================== */

    inputFolio.value =
        'Consultando...';


    inputNomenclaturaVisual.value =
        'Consultando...';


    inputNomenclatura.value =
        '';


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
            'clave_folio',
            'FEL'
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
                || 'No fue posible consultar el siguiente folio.'
            );

        }


        /* =================================================
           FOLIO
        ================================================= */

        const folio =
            String(
                resultado.folio
                || ''
            ).trim();


        if (
            folio === ''
        ) {

            throw new Error(
                'El servidor no devolvió el folio.'
            );

        }


        /* =================================================
           EXTRAER NÚMERO DEL FOLIO

           FEL-15 → 15
        ================================================= */

        const numeroFolio =
            folio
                .replace(
                    /^FEL-/i,
                    ''
                )
                .trim();


        /* =================================================
           OBTENER AÑO DE LA FECHA DE REGISTRO
        ================================================= */

        const inputFecha =
            document.querySelector(
                '#felicitacion-fecha-registro'
            );


        const fechaRegistro =
            String(
                inputFecha?.value
                || ''
            ).trim();


        const partesFecha =
            fechaRegistro.split(
                '/'
            );


        const anio =
            partesFecha.length === 3
                ? partesFecha[2]
                : '';


        if (
            numeroFolio === ''
            || anio === ''
        ) {

            throw new Error(
                'No fue posible generar la nomenclatura de la felicitación.'
            );

        }


        /* =================================================
           NOMENCLATURA AUTOMÁTICA

           FEL-15 + 22/09/2026
           ↓
           CGSC/CAI/FEL/15/2026
        ================================================= */

        const nomenclatura =
            `CGSC/CAI/FEL/${numeroFolio}/${anio}`;


        /* =================================================
           MOSTRAR
        ================================================= */

        inputFolio.value =
            folio;


        inputNomenclaturaVisual.value =
            nomenclatura;


        /* =================================================
           VALOR QUE SE ENVÍA AL BACKEND
        ================================================= */

        inputNomenclatura.value =
            nomenclatura;


    } catch (error) {

        console.error(
            'Error previsualizando folio de felicitación:',
            error
        );


        inputFolio.value =
            'FEL- — No disponible';


        inputNomenclaturaVisual.value =
            'CGSC/CAI/FEL/ — No disponible';


        inputNomenclatura.value =
            '';

    }

}