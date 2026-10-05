/* =========================================================
   SISTEMA DE REPORTES - KEEPALIVE DE SESION
========================================================= */

const INTERVALO_KEEPALIVE_MS = 10 * 60 * 1000;
const RUTA_KEEPALIVE = 'asuntos-internos/reportes/ping';

function obtenerUrlKeepalive() {
    const rutaActual =
        window.location.pathname;

    const marcadorModulo =
        '/asuntos-internos/reportes';

    const indiceModulo =
        rutaActual.indexOf(
            marcadorModulo
        );

    if (indiceModulo >= 0) {
        return new URL(
            `${rutaActual.slice(0, indiceModulo)}/${RUTA_KEEPALIVE}`,
            window.location.origin
        );
    }

    return new URL(
        RUTA_KEEPALIVE,
        `${window.location.origin}/`
    );
}

async function enviarKeepalive() {
    try {
        await fetch(
            obtenerUrlKeepalive().toString(),
            {
                method: 'GET',
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
                cache: 'no-store',
            }
        );
    } catch (error) {
        // El keepalive no debe interrumpir al usuario ni reintentar login.
    }
}

if (
    typeof window !== 'undefined'
    && document.querySelector('.report-header')
) {
    window.setInterval(
        enviarKeepalive,
        INTERVALO_KEEPALIVE_MS
    );
}
