/* =========================================================
   SISTEMA DE REPORTES - ASUNTOS INTERNOS
   FELICITACIONES - EDITAR
   CARGA DE DATOS
========================================================= */


/* =========================================================
   CARGAR DATOS GENERALES
========================================================= */

export function cargarDatosGeneralesEditar(
    modal,
    felicitacion
) {

    if (
        !modal
        || !felicitacion
    ) {
        return;
    }


    /* =====================================================
       FOLIO
    ===================================================== */

    const inputFolio =
        modal.querySelector(
            '#editar-felicitacion-folio'
        );


    if (inputFolio) {

        inputFolio.value =
            String(
                felicitacion.folio
                || ''
            ).trim();
    }


    /* =====================================================
       FECHA DE REGISTRO
    ===================================================== */

    const inputFecha =
        modal.querySelector(
            '#editar-felicitacion-fecha'
        );


    if (inputFecha) {

        inputFecha.value =
            String(
                felicitacion.fecha_registro
                || ''
            ).trim();
    }


    /* =====================================================
       NOMENCLATURA
    ===================================================== */

    const inputNomenclaturaParte =
        modal.querySelector(
            '#editar-felicitacion-nomenclatura-parte'
        );


    const inputNomenclaturaCompleta =
        modal.querySelector(
            '#editar-felicitacion-nomenclatura'
        );


    const PREFIJO_NOMENCLATURA =
        'CGSC/CAI/FEL/';


    const nomenclaturaGuardada =
        String(
            felicitacion.nomenclatura
            || ''
        ).trim();


    let parteVariable =
        nomenclaturaGuardada;


    if (
        nomenclaturaGuardada.startsWith(
            PREFIJO_NOMENCLATURA
        )
    ) {

        parteVariable =
            nomenclaturaGuardada
                .substring(
                    PREFIJO_NOMENCLATURA.length
                );
    }


    if (inputNomenclaturaParte) {

        inputNomenclaturaParte.value =
            parteVariable;
    }


    if (inputNomenclaturaCompleta) {

        inputNomenclaturaCompleta.value =
            nomenclaturaGuardada;
    }


    /* =====================================================
       ACTUALIZAR NOMENCLATURA AL ESCRIBIR
    ===================================================== */

    function actualizarNomenclatura() {

        if (
            !inputNomenclaturaParte
            || !inputNomenclaturaCompleta
        ) {
            return;
        }


        let parte =
            String(
                inputNomenclaturaParte.value
                || ''
            );


        /*
         * Evitar doble diagonal al inicio.
         *
         * No usamos trim() aquí para permitir
         * que el usuario capture espacios normalmente.
         */

        parte =
            parte.replace(
                /^\/+/,
                ''
            );


        inputNomenclaturaParte.value =
            parte;


        inputNomenclaturaCompleta.value =
            parte.trim() !== ''
                ? `${PREFIJO_NOMENCLATURA}${parte}`
                : '';
    }


    /* =====================================================
       EVITAR LISTENERS DUPLICADOS
    ===================================================== */

    if (
        inputNomenclaturaParte
        && inputNomenclaturaParte.dataset
            .nomenclaturaInicializada
        !== '1'
    ) {

        inputNomenclaturaParte.dataset
            .nomenclaturaInicializada =
            '1';


        inputNomenclaturaParte.addEventListener(
            'input',
            actualizarNomenclatura
        );


        inputNomenclaturaParte.addEventListener(
            'change',
            actualizarNomenclatura
        );
    }


    /* =====================================================
       NOMBRE DE LA PERSONA QUE FELICITA
    ===================================================== */

    const inputFelicitante =
        modal.querySelector(
            '#editar-felicitacion-felicitante'
        );


    if (inputFelicitante) {

        inputFelicitante.value =
            String(
                felicitacion.nombre_felicitante
                || ''
            ).trim();
    }


    /* =====================================================
       RAZÓN DE LA FELICITACIÓN
    ===================================================== */

    const inputRazon =
        modal.querySelector(
            '#editar-felicitacion-razon'
        );


    if (inputRazon) {

        inputRazon.value =
            String(
                felicitacion.razon_felicitacion
                || ''
            ).trim();
    }
}