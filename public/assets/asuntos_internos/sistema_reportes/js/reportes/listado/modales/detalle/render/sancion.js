/* =========================================================
   SISTEMA DE REPORTES - ASUNTOS INTERNOS
   DETALLE DE QUEJAS
   RENDER - SANCIÓN DISCIPLINARIA
========================================================= */


/* =========================================================
   RENDERIZAR SANCIÓN
========================================================= */

export function renderizarSancionDetalle(
    modal,
    sancion
) {

    if (!modal) {
        return;
    }


    const campoSancion =
        modal.querySelector(
            '#detalle-sancion-disciplinaria'
        );


    const avisoOrigen =
        modal.querySelector(
            '#detalle-sancion-origen'
        );


    /* =====================================================
       TEXTO DE SANCIÓN
    ===================================================== */

    if (campoSancion) {

        let texto =
            'Sin sanción registrada';


        if (
            sancion
            && typeof sancion === 'object'
        ) {

            const textoBackend =
                String(
                    sancion.texto
                    || ''
                ).trim();


            const tipo =
                String(
                    sancion.tipo
                    || ''
                ).trim();


            const descripcionOtro =
                String(
                    sancion.descripcion_otro
                    || ''
                ).trim();


            /*
             * El backend ya entrega "texto",
             * pero mantenemos respaldo por seguridad.
             */

            if (textoBackend) {

                texto =
                    textoBackend;

            } else if (
                tipo === 'Otro'
                && descripcionOtro
            ) {

                texto =
                    descripcionOtro;

            } else if (tipo) {

                texto =
                    tipo;
            }
        }


        campoSancion.textContent =
            texto;
    }


    /* =====================================================
       AVISO DE ORIGEN
    ===================================================== */

    if (!avisoOrigen) {
        return;
    }


    /*
     * El aviso solamente aparece cuando
     * la sanción vigente proviene de Seguimiento.
     */

    const desdeSeguimiento =
        sancion
        && sancion.actualizada_desde_seguimiento === true;


    if (!desdeSeguimiento) {

        avisoOrigen.hidden =
            true;


        avisoOrigen.textContent =
            '';


        return;
    }


    const fecha =
        String(
            sancion.fecha_actualizacion
            || ''
        ).trim();


    avisoOrigen.textContent =
        fecha
            ? `Actualizada desde seguimiento el ${fecha}`
            : 'Actualizada desde seguimiento';


    avisoOrigen.hidden =
        false;
}