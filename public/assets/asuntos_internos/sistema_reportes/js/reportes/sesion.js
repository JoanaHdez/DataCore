/* =========================================================
   SISTEMA DE REPORTES - ASUNTOS INTERNOS
   Manejo compartido de sesion expirada
========================================================= */

import {
    mostrarResultadoYRedirigir,
} from './notificaciones/resultado.js';


const fetchOriginal =
    window.fetch.bind(
        window
    );


let manejandoSesionExpirada =
    false;


window.fetch = async function fetchReportes(
    recurso,
    opciones
) {

    const recursoPreparado =
        prepararRecurso(
            recurso,
            opciones
        );


    const respuesta =
        await fetchOriginal(
            recursoPreparado.recurso,
            recursoPreparado.opciones
        );


    if (
        respuesta.status !== 401
    ) {
        return respuesta;
    }


    const datos =
        await leerJsonSeguro(
            respuesta
        );


    if (
        datos?.session_expired !== true
    ) {
        return respuesta;
    }


    manejarSesionExpirada(
        datos
    );


    return new Promise(
        () => {}
    );
};


function prepararRecurso(
    recurso,
    opciones
) {

    const url =
        obtenerUrlRecurso(
            recurso
        );


    if (
        !url
        || url.origin !== window.location.origin
        || !url.pathname.includes(
            '/asuntos-internos/reportes'
        )
    ) {

        return {
            recurso,
            opciones,
        };
    }


    const opcionesPreparadas = {
        ...(opciones || {}),
    };


    const headers =
        new Headers(
            opcionesPreparadas.headers
            || {}
        );


    if (
        !headers.has(
            'X-Requested-With'
        )
    ) {

        headers.set(
            'X-Requested-With',
            'XMLHttpRequest'
        );
    }


    opcionesPreparadas.headers =
        headers;


    return {
        recurso,
        opciones:
            opcionesPreparadas,
    };
}


function obtenerUrlRecurso(
    recurso
) {

    try {

        if (
            recurso instanceof Request
        ) {

            return new URL(
                recurso.url
            );
        }


        return new URL(
            String(
                recurso
            ),
            window.location.href
        );

    } catch (error) {

        return null;
    }
}


async function leerJsonSeguro(
    respuesta
) {

    try {

        return await respuesta
            .clone()
            .json();

    } catch (error) {

        return null;
    }
}


function manejarSesionExpirada(
    datos
) {

    if (
        manejandoSesionExpirada
    ) {
        return;
    }


    manejandoSesionExpirada =
        true;


    const urlLogin =
        datos?.redirect
        || construirUrlLogin();


    mostrarResultadoYRedirigir({
        tipo:
            'warning',

        titulo:
            'Sesión expirada',

        mensaje:
            datos?.message
            || 'Tu sesión ha expirado por inactividad.',

        url:
            urlLogin,

        duracion:
            3000,
    });
}


function construirUrlLogin() {

    const rutaActual =
        window.location.pathname;


    const marcador =
        '/asuntos-internos/reportes';


    const indice =
        rutaActual.indexOf(
            marcador
        );


    if (
        indice >= 0
    ) {

        return `${window.location.origin}${rutaActual.slice(
            0,
            indice
        )}${marcador}`;
    }


    return new URL(
        'asuntos-internos/reportes',
        window.location.origin
    ).toString();
}
