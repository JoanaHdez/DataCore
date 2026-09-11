/* =========================================================
   SISTEMA DE REPORTES - ASUNTOS INTERNOS
   DETALLE DE QUEJAS
   UTILIDADES DE FECHA Y HORA
========================================================= */


/* =========================================================
   FECHA
========================================================= */

export function formatearFechaDetalle(
    valor
) {

    const fecha =
        String(
            valor || ''
        ).trim();


    if (!fecha) {
        return '';
    }


    /*
     * Si ya viene en formato DD/MM/YYYY,
     * lo conservamos.
     */

    if (
        /^\d{2}\/\d{2}\/\d{4}$/.test(
            fecha
        )
    ) {
        return fecha;
    }


    /*
     * Formato de BD:
     *
     * YYYY-MM-DD
     */

    const coincidencia =
        fecha.match(
            /^(\d{4})-(\d{2})-(\d{2})$/
        );


    if (!coincidencia) {
        return fecha;
    }


    return (
        `${coincidencia[3]}/`
        + `${coincidencia[2]}/`
        + `${coincidencia[1]}`
    );
}


/* =========================================================
   HORA
========================================================= */

export function formatearHoraDetalle(
    valor
) {

    const hora =
        String(
            valor || ''
        ).trim();


    if (!hora) {
        return '';
    }


    return hora.length >= 5
        ? hora.substring(
            0,
            5
        )
        : hora;
}