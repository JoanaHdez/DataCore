/* =========================================================
   SISTEMA DE REPORTES - ASUNTOS INTERNOS
   Editar reporte - Estado compartido
========================================================= */


/* =========================================================
   REPORTES TEMPORALES
========================================================= */

export const reportesTemporales =
    new Map();


/* =========================================================
   PERSONAL
========================================================= */

export const estadoPersonal = {

    elementos: [],

    seleccionado: null,

    temporizadorBusqueda: null,

    controladorBusqueda: null,

};


/* =========================================================
   UNIDADES
========================================================= */

export const estadoUnidades = {

    elementos: [],

    seleccionada: null,

    temporizadorBusqueda: null,

    controladorBusqueda: null,

};


/* =========================================================
   REPORTE ACTUAL
========================================================= */

export const estadoEdicion = {

    filaActual: null,

    folioActual: '',

};


/* =========================================================
   ACTUALIZAR REPORTE ACTUAL
========================================================= */

export function establecerReporteActual(
    fila,
    folio
) {

    estadoEdicion.filaActual =
        fila || null;

    estadoEdicion.folioActual =
        String(
            folio || ''
        ).trim();

}


/* =========================================================
   LIMPIAR REPORTE ACTUAL
========================================================= */

export function limpiarReporteActual() {

    estadoEdicion.filaActual =
        null;

    estadoEdicion.folioActual =
        '';

}


/* =========================================================
   PERSONAL
========================================================= */

export function establecerPersonal(
    personal
) {

    estadoPersonal.elementos =
        Array.isArray(
            personal
        )
            ? personal.map(
                (persona) => ({
                    ...persona,
                })
            )
            : [];

}


export function agregarPersonal(
    persona
) {

    estadoPersonal.elementos.push({
        ...persona,
    });

}


export function eliminarPersonal(
    indice
) {

    if (
        !Number.isInteger(
            indice
        )
        || !estadoPersonal.elementos[indice]
    ) {
        return false;
    }


    estadoPersonal.elementos.splice(
        indice,
        1
    );


    return true;

}


export function establecerPersonaSeleccionada(
    persona
) {

    estadoPersonal.seleccionado =
        persona
            ? {
                ...persona,
            }
            : null;

}


/* =========================================================
   UNIDADES
========================================================= */

export function establecerUnidades(
    unidades
) {

    estadoUnidades.elementos =
        Array.isArray(
            unidades
        )
            ? unidades.map(
                (unidad) => ({
                    ...unidad,
                })
            )
            : [];

}


export function agregarUnidad(
    unidad
) {

    estadoUnidades.elementos.push({
        ...unidad,
    });

}


export function eliminarUnidad(
    indice
) {

    if (
        !Number.isInteger(
            indice
        )
        || !estadoUnidades.elementos[indice]
    ) {
        return false;
    }


    estadoUnidades.elementos.splice(
        indice,
        1
    );


    return true;

}


export function establecerUnidadSeleccionada(
    unidad
) {

    estadoUnidades.seleccionada =
        unidad
            ? {
                ...unidad,
            }
            : null;

}


/* =========================================================
   LIMPIAR RELACIONES
========================================================= */

export function limpiarRelacionesEdicion() {

    estadoPersonal.elementos =
        [];

    estadoPersonal.seleccionado =
        null;

    estadoPersonal.temporizadorBusqueda =
        null;

    estadoPersonal.controladorBusqueda =
        null;


    estadoUnidades.elementos =
        [];

    estadoUnidades.seleccionada =
        null;

    estadoUnidades.temporizadorBusqueda =
        null;

    estadoUnidades.controladorBusqueda =
        null;

}