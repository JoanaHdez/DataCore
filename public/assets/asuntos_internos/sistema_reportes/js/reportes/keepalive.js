/* =========================================================
   SISTEMA DE REPORTES - ACTIVIDAD DE SESION
========================================================= */

const INTERVALO_ACTIVIDAD_MS = 15 * 1000;
const LIMITE_INACTIVIDAD_MS = 12 * 60 * 60 * 1000;

const RUTA_KEEPALIVE = 'asuntos-internos/reportes/ping';

let intervaloKeepaliveId = null;
let ultimaActividadUsuario = Date.now();
let ultimoEnvioActividad = 0;
let enviandoKeepalive = false;
let sesionExpirada = false;

function obtenerUrlKeepalive() {
    const rutaActual = window.location.pathname;
    const marcadorModulo = '/asuntos-internos/reportes';

    const indiceModulo = rutaActual.indexOf(
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

function obtenerUrlLogin() {
    const url = obtenerUrlKeepalive();

    return url.toString().replace(/\/ping$/, '');
}

function registrarActividadUsuario() {
    if (sesionExpirada || document.hidden) {
        return;
    }

    ultimaActividadUsuario = Date.now();
}

function expirarSesionEnNavegador() {
    if (sesionExpirada) {
        return;
    }

    sesionExpirada = true;

    if (intervaloKeepaliveId !== null) {
        clearInterval(intervaloKeepaliveId);
        intervaloKeepaliveId = null;
    }

    /*
     * No redirigimos automáticamente mientras el usuario
     * pueda tener información sin guardar.
     *
     * El servidor ya protegerá las operaciones mediante
     * ReportesAuthFilter.
     */
    console.warn(
        'La sesión de SistemaReportes ha expirado por inactividad.'
    );
}

async function enviarKeepalive() {

    if (
        sesionExpirada
        || enviandoKeepalive
        || document.hidden
    ) {
        return;
    }

    const ahora = Date.now();

    /*
     * No enviar peticiones cuando el usuario
     * lleva 12 horas sin actividad.
     */
    if (
        ahora - ultimaActividadUsuario >=
        LIMITE_INACTIVIDAD_MS
    ) {
        expirarSesionEnNavegador();
        return;
    }

    /*
     * Evitar peticiones demasiado frecuentes.
     */
    if (
        ahora - ultimoEnvioActividad <
        INTERVALO_ACTIVIDAD_MS
    ) {
        return;
    }

    enviandoKeepalive = true;

    try {
        const respuesta = await fetch(
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

        if (respuesta.status === 401) {
            expirarSesionEnNavegador();
            return;
        }

        if (respuesta.ok) {
            ultimoEnvioActividad = Date.now();
            
        }
    } catch (error) {
        // Un error de red no debe cerrar la sesión automáticamente.
    } finally {
        enviandoKeepalive = false;
    }
}

function comprobarActividad() {
    if (sesionExpirada) {
        return;
    }

    if (
        Date.now() - ultimaActividadUsuario >=
        LIMITE_INACTIVIDAD_MS
    ) {
        expirarSesionEnNavegador();
        return;
    }

    /*
     * Solo enviar actividad si hubo una interacción
     * reciente del usuario.
     */
    if (
        Date.now() - ultimaActividadUsuario <=
        INTERVALO_ACTIVIDAD_MS
    ) {
        enviarKeepalive();
    }
}

function inicializarKeepalive() {
    if (
        typeof window === 'undefined'
        || !document.querySelector('.report-header')
        || intervaloKeepaliveId !== null
    ) {
        return;
    }

    const eventosActividad = [
        'pointerdown',
        'keydown',
        'input',
        'change',
        'scroll',
    ];

    eventosActividad.forEach(evento => {
        document.addEventListener(
            evento,
            registrarActividadUsuario,
            { passive: true }
        );
    });

    /*
     * Al volver a una pestaña, comprobar primero
     * si ya se superó el límite de inactividad.
     */
    document.addEventListener(
        'visibilitychange',
        () => {
            if (!document.hidden) {
                comprobarActividad();
            }
        }
    );

    intervaloKeepaliveId = window.setInterval(
        comprobarActividad,
        1000
    );
}

if (document.readyState === 'loading') {
    document.addEventListener(
        'DOMContentLoaded',
        inicializarKeepalive,
        { once: true }
    );
} else {
    inicializarKeepalive();
}

export {
    inicializarKeepalive,
};