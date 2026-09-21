
/* =========================================================
   CONSULTAR
========================================================= */

export async function consultarSeguimientos(
    idReporte
) {

    const baseUrl =
        obtenerBaseUrl();


    const url =
        new URL(
            `asuntos-internos/reportes/seguimientos/${idReporte}`,
            baseUrl
        );


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


    const datos =
        await obtenerJsonRespuesta(
            respuesta
        );


    if (!respuesta.ok) {

        throw new Error(
            datos?.message
            || 'No fue posible consultar el seguimiento.'
        );
    }


    return datos;
}

/* =========================================================
   REGISTRAR
========================================================= */

export async function registrarSeguimiento(
    idReporte,
    datos
) {

    const baseUrl =
        obtenerBaseUrl();


    const url =
        new URL(
            `asuntos-internos/reportes/seguimientos/${idReporte}`,
            baseUrl
        );


    const respuesta =
        await fetch(
            url.toString(),
            {
                method:
                    'POST',

                headers: {
                    Accept:
                        'application/json',
                },

                credentials:
                    'same-origin',

                body:
                    datos,
            }
        );


    const resultado =
        await obtenerJsonRespuesta(
            respuesta
        );


    if (!respuesta.ok) {

        throw new Error(
            resultado?.message
            || 'No fue posible registrar el seguimiento.'
        );
    }

    return resultado;
}


/* =========================================================
   ACTUALIZAR SEGUIMIENTO
========================================================= */

export async function actualizarSeguimientoBackend(
    idSeguimiento,
    datos
) {

    const baseUrl =
        obtenerBaseUrl();


    const url =
        new URL(
            `asuntos-internos/reportes/seguimientos/${idSeguimiento}`,
            baseUrl
        );


    const respuesta =
        await fetch(
            url.toString(),
            {
                method:
                    'PUT',

                headers: {

                    Accept:
                        'application/json',

                    'Content-Type':
                        'application/x-www-form-urlencoded; charset=UTF-8',

                },

                credentials:
                    'same-origin',

                body:
                    datos.toString(),
            }
        );


    const resultado =
        await obtenerJsonRespuesta(
            respuesta
        );


    if (!respuesta.ok) {

        throw new Error(
            resultado?.message
            || 'No fue posible actualizar el seguimiento.'
        );
    }


    return resultado;
}


/* =========================================================
   JSON
========================================================= */

async function obtenerJsonRespuesta(
    respuesta
) {

    try {

        return await respuesta.json();

    } catch (error) {

        throw new Error(
            'El servidor devolvió una respuesta no válida.'
        );
    }
}


/* =========================================================
   BASE URL
========================================================= */

function obtenerBaseUrl() {

    return `${window.location.origin}/DataCore/public/`;

}