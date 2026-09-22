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


    const folio =
        String(
            felicitacion.folio
            || ''
        )
            .trim()
            .toLocaleUpperCase(
                'es-MX'
            );


    if (inputFolio) {

        inputFolio.value =
            folio;

    }


    /* =====================================================
       FECHA DE REGISTRO
    ===================================================== */

    const inputFecha =
        modal.querySelector(
            '#editar-felicitacion-fecha'
        );


    const fechaRegistro =
        String(
            felicitacion.fecha_registro
            || ''
        ).trim();


    if (inputFecha) {

        inputFecha.value =
            fechaRegistro;

    }


    /* =====================================================
       NOMENCLATURA AUTOMÁTICA
    ===================================================== */

    const inputNomenclaturaVisual =
        modal.querySelector(
            '#editar-felicitacion-nomenclatura-visual'
        );


    const inputNomenclatura =
        modal.querySelector(
            '#editar-felicitacion-nomenclatura'
        );


    /* =====================================================
       EXTRAER NÚMERO DEL FOLIO

       FEL-15
       ↓
       15
    ===================================================== */

    const numeroFolio =
        folio
            .replace(
                /^FEL-/i,
                ''
            )
            .trim();


    /* =====================================================
       EXTRAER AÑO DE LA FECHA

       22/09/2026
       ↓
       2026
    ===================================================== */

    let anio =
        '';


    if (
        fechaRegistro !== ''
    ) {

        const partesFecha =
            fechaRegistro.split(
                '/'
            );


        if (
            partesFecha.length === 3
        ) {

            anio =
                String(
                    partesFecha[2]
                    || ''
                ).trim();

        }

    }


    /* =====================================================
       SOPORTE PARA FECHA ISO

       2026-09-22
       ↓
       2026

       Esto evita problemas si el backend devuelve
       la fecha en formato Y-m-d.
    ===================================================== */

    if (
        anio === ''
        && /^\d{4}-\d{2}-\d{2}$/.test(
            fechaRegistro
        )
    ) {

        anio =
            fechaRegistro.substring(
                0,
                4
            );

    }


    /* =====================================================
       CONSTRUIR NOMENCLATURA

       FEL-15 + 2026
       ↓
       CGSC/CAI/FEL/15/2026
    ===================================================== */

    const nomenclatura =
        numeroFolio !== ''
        && anio !== ''
            ? `CGSC/CAI/FEL/${numeroFolio}/${anio}`
            : '';


    /* =====================================================
       MOSTRAR NOMENCLATURA
    ===================================================== */

    if (
        inputNomenclaturaVisual
    ) {

        inputNomenclaturaVisual.value =
            nomenclatura;

    }


    /* =====================================================
       VALOR QUE SE ENVÍA AL BACKEND
    ===================================================== */

    if (
        inputNomenclatura
    ) {

        inputNomenclatura.value =
            nomenclatura;

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
            )
                .trim()
                .toLocaleUpperCase(
                    'es-MX'
                );

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
            )
                .trim()
                .toLocaleUpperCase(
                    'es-MX'
                );

    }

}