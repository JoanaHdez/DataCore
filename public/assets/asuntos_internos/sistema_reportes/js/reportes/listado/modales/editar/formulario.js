/* =========================================================
   SISTEMA DE REPORTES - ASUNTOS INTERNOS
   Editar reporte - Formulario
========================================================= */

import {
    obtenerDatoFormulario,
    construirFolio,
    formatearFechaTabla,
    convertirFechaInput,
    leerDatasetArray,
    escaparHTML,
    obtenerClaseEstado,
} from './utilidades.js';

import {
    cargarPersonalEditar,
    obtenerPersonalEditar,
    limpiarPersonalEditar,
} from './personal.js';


import {
    cargarUnidadesEditar,
    obtenerUnidadesEditar,
    limpiarUnidadesEditar,
} from './unidades.js';


import {
    cargarEvidenciaEditar,
    limpiarEvidenciaEditar,
    obtenerNuevasEvidencias,
} from './evidencia.js';


import {
    cargarQuejosoEditar,
} from './quejoso.js';


import {
    cargarIdentificacionEditar,
} from './identificacion.js';


import {
    cargarDatosHechosEditar,
} from './datosHechos.js';


import {
    cargarUbicacionEditar,
} from './ubicacion.js';


import {
    cargarClasificacionEditar,
} from './clasificacion.js';

import {
    cargarSancionesEditar,
} from './sanciones.js';


import {
    cargarMotivosEditar,
    limpiarMotivosEditar,
    obtenerMotivosEditar,
} from './motivos.js';


import {
    cargarObservacionesEditar,
    limpiarObservacionesEditar,
} from './observaciones.js';


import {
    cargarDatosReporteEditar,
    limpiarDatosReporteEditar,
} from './datosReporte.js';


import {
    cargarDireccionNotificacionEditar,
} from './direccionNotificacion.js';

/* =========================================================
   CREAR REPORTE TEMPORAL DESDE FILA
========================================================= */

export function crearReporteTemporalDesdeFila(
    fila
) {

    const celdas =
        fila.querySelectorAll(
            'td'
        );


    const folio =
        celdas[0]
            ?.textContent
            .trim()
        || '';


    return {

        folio,


        prefijo:
            obtenerPrefijoTemporal(
                folio
            ),


        numero_folio:
            obtenerNumeroTemporal(
                folio
            ),


        fecha_registro:
            '',


        folio_ip:
            '',


        fecha_queja:
            convertirFechaInput(
                celdas[1]
                    ?.textContent
                    .trim()
                || ''
            ),


        fecha_acuerdo:
            '',


        expediente:
            celdas[2]
                ?.textContent
                .trim()
            || '',


        nomenclatura:
            '',


        no_oficio:
            '',


        /* =====================================================
           HECHOS
        ===================================================== */

        fecha_hechos:
            '',


        hora_hechos:
            '',


        descripcion:
            '',


        /* =====================================================
           UBICACIÓN
        ===================================================== */

        calle:
            '',


        numero:
            '',


        colonia:
            '',


        entre_calle:
            '',


        y_calle:
            '',


        municipio:
            '',


        estado:
            '',


        sector:
            '',


        cuadrante:
            '',


        id_cuadra:
            '',


        latitud:
            '',


        longitud:
            '',


        origen_ubicacion:
            '',


        /* =====================================================
           PERSONAL
        ===================================================== */

        personal:
            leerDatasetArray(
                fila.dataset.personal
            ),


        /* =====================================================
           UNIDADES
        ===================================================== */

        unidades:
            leerDatasetArray(
                fila.dataset.unidades
            ),


        /* =====================================================
           QUEJOSO
        ===================================================== */

        quejoso:
            celdas[4]
                ?.textContent
                .trim()
            || '',


        edad:
            '',


        genero:
            '',


        telefono:
            '',


        correo:
            '',


        /* =====================================================
           CLASIFICACIÓN
        ===================================================== */

        clasificacion:
            celdas[3]
                ?.textContent
                .trim()
            || '',


        inspector:
            '',


        investigador:
            '',


        quien_emite_resolucion:
            '',


        estado_actual:
            celdas[5]
                ?.textContent
                .trim()
            || '',


        resolucion:
            '',


        motivos:
            [],


        /* =====================================================
           ADICIONAL
        ===================================================== */

        observaciones:
            '',


        /* =====================================================
           EVIDENCIA
        ===================================================== */

        evidencias:
            [],
    };
}


/* =========================================================
   CARGAR REPORTE EN FORMULARIO
========================================================= */

export function cargarReporteEnFormulario(
    modal,
    formulario,
    reporte
) {

    if (
        !modal
        || !formulario
        || !reporte
    ) {
        return;
    }


    /* =====================================================
       LIMPIAR ESTADO ANTERIOR
    ===================================================== */

    limpiarFormularioEditar(
        modal,
        formulario
    );


    /* =====================================================
       DATOS DEL REPORTE
    ===================================================== */

    cargarDatosReporteEditar(
        modal,
        reporte
    );


    /* =====================================================
       IDENTIFICACIÓN
    ===================================================== */

    cargarIdentificacionEditar(
        modal,
        reporte
    );


    /* =====================================================
       DATOS DE LOS HECHOS
    ===================================================== */

    cargarDatosHechosEditar(
        modal,
        reporte
    );


    /* =====================================================
       UBICACIÓN DE LOS HECHOS
    ===================================================== */

    cargarUbicacionEditar(
        modal,
        reporte
    );


    /* =====================================================
       PERSONAL
    ===================================================== */

    cargarPersonalEditar(
        modal,
        reporte
    );


    /* =====================================================
       UNIDADES
    ===================================================== */

    cargarUnidadesEditar(
        modal,
        reporte
    );


    /* =====================================================
       QUEJOSO
    ===================================================== */

    cargarQuejosoEditar(
        modal,
        reporte
    );


    /* =====================================================
       DIRECCIÓN PARA NOTIFICACIÓN
    ===================================================== */

    cargarDireccionNotificacionEditar(
        modal,
        reporte.direccion_notificacion
    );


    /* =====================================================
       CLASIFICACIÓN Y SEGUIMIENTO
    ===================================================== */

    cargarClasificacionEditar(
        modal,
        reporte
    );


    /* =====================================================
       SITUACIÓN DE LA SANCIÓN
    ===================================================== */

    cargarSancionesEditar(
        modal,
        reporte
    );


    /* =====================================================
       MOTIVOS
    ===================================================== */

    cargarMotivosEditar(
        modal,
        reporte.motivos
    );


    /* =====================================================
       OBSERVACIONES
    ===================================================== */

    cargarObservacionesEditar(
        modal,
        reporte
    );


    /* =====================================================
       EVIDENCIA
    ===================================================== */

    cargarEvidenciaEditar(
        modal,
        reporte
    );
}


/* =========================================================
   OBTENER REPORTE DESDE FORMULARIO
========================================================= */

export function obtenerReporteDesdeFormulario(
    formulario,
    reporteAnterior
) {

    const datos =
        new FormData(
            formulario
        );


    const reporte = {
        ...reporteAnterior,
    };


    const campos = [

        /* =====================================================
           DATOS DEL REPORTE
        ===================================================== */

        'folio',
        'fecha_registro',


        /* =====================================================
           IDENTIFICACIÓN
        ===================================================== */

        'folio_ip',
        'fecha_queja',
        'fecha_acuerdo',
        'expediente',
        'nomenclatura',
        'no_oficio',


        /* =====================================================
           HECHOS
        ===================================================== */

        'fecha_hechos',
        'hora_hechos',
        'descripcion',


        /* =====================================================
           UBICACIÓN
        ===================================================== */

        'calle',
        'numero',
        'colonia',
        'entre_calle',
        'y_calle',
        'municipio',
        'estado',
        'sector',
        'cuadrante',
        'id_cuadra',
        'latitud',
        'longitud',
        'origen_ubicacion',
        'modalidad_unidad',


        /* =====================================================
           QUEJOSO
        ===================================================== */

        'quejoso',
        'edad',
        'genero',
        'telefono',
        'correo',


        /* =====================================================
           CLASIFICACIÓN
        ===================================================== */

        'clasificacion',
        'inspector',
        'investigador',
        'quien_emite_resolucion',
        'resolucion',
        'motivos',


        /* =====================================================
           OBSERVACIONES
        ===================================================== */

        'observaciones',
    ];


    campos.forEach(
        (campo) => {

            if (
                !datos.has(
                    campo
                )
            ) {
                return;
            }


            reporte[campo] =
                obtenerDatoFormulario(
                    datos,
                    campo
                );
        }
    );


    /* =====================================================
       PERSONAL
    ===================================================== */

    reporte.personal =
        obtenerPersonalEditar();


    /* =====================================================
       UNIDADES
    ===================================================== */

    reporte.unidades =
        obtenerUnidadesEditar();


    /* =====================================================
       SITUACIÓN DE LA SANCIÓN
    ===================================================== */

    reporte.sin_sanciones =
        datos.get(
            'sin_sanciones'
        ) === '1'
            ? 1
            : 0;


    reporte.baja_voluntaria =
        datos.get(
            'baja_voluntaria'
        ) === '1'
            ? 1
            : 0;


    /* =====================================================
       MOTIVOS
    ===================================================== */

    reporte.motivos =
        obtenerMotivosEditar();


    /* =====================================================
       EVIDENCIA
    ===================================================== */

    const nuevas =
        obtenerNuevasEvidencias(
            datos
        );


    if (
        nuevas.length > 0
    ) {

        const existentes =
            Array.isArray(
                reporte.evidencias
            )
                ? reporte.evidencias
                : [];


        reporte.evidencias = [
            ...existentes,
            ...nuevas,
        ];
    }


    return reporte;
}


/* =========================================================
   ACTUALIZAR FILA DEL LISTADO
========================================================= */

export function actualizarFilaDesdeReporte(
    fila,
    reporte
) {

    const celdas =
        fila.querySelectorAll(
            'td'
        );


    if (
        celdas.length < 7
    ) {
        return;
    }


    const folio =
        reporte.folio
        || construirFolio(
            reporte.prefijo,
            reporte.numero_folio
        );


    /* =====================================================
       FOLIO
    ===================================================== */

    celdas[0].innerHTML =
        `<strong>${escaparHTML(
            folio
        )}</strong>`;


    /* =====================================================
       FECHA
    ===================================================== */

    celdas[1].textContent =
        formatearFechaTabla(
            reporte.fecha_queja
        );


    /* =====================================================
       EXPEDIENTE
    ===================================================== */

    celdas[2].textContent =
        reporte.expediente
        || '';


    /* =====================================================
       CLASIFICACIÓN
    ===================================================== */

    celdas[3].textContent =
        reporte.clasificacion
        || '';


    /* =====================================================
       QUEJOSO
    ===================================================== */

    celdas[4].textContent =
        reporte.quejoso
        || '';


    /* =====================================================
       ESTADO
    ===================================================== */

    actualizarEstadoFila(
        celdas[5],
        reporte.estado_actual
    );


    /* =====================================================
       DATOS PARA DETALLE / EDICIÓN
    ===================================================== */

    fila.dataset.personal =
        JSON.stringify(
            Array.isArray(
                reporte.personal
            )
                ? reporte.personal
                : []
        );


    fila.dataset.unidades =
        JSON.stringify(
            Array.isArray(
                reporte.unidades
            )
                ? reporte.unidades
                : []
        );


    /* =====================================================
       ACTUALIZAR FOLIO DE BOTONES
    ===================================================== */

    fila.querySelectorAll(
        '[data-folio]'
    ).forEach(
        (boton) => {

            boton.dataset.folio =
                folio;
        }
    );
}


/* =========================================================
   LIMPIAR FORMULARIO
========================================================= */

export function limpiarFormularioEditar(
    modal,
    formulario
) {

    if (
        !modal
        || !formulario
    ) {
        return;
    }


    /* =====================================================
       CAMPOS GENERALES
    ===================================================== */

    modal.querySelectorAll(
        'input, select, textarea'
    ).forEach(
        (campo) => {

            /* =================================================
               ARCHIVOS
            ================================================= */

            if (
                campo instanceof
                HTMLInputElement
                && campo.type === 'file'
            ) {

                campo.value =
                    '';

                return;
            }


            /* =================================================
               RADIO BUTTONS
            ================================================= */

            if (
                campo instanceof
                HTMLInputElement
                && campo.type === 'radio'
            ) {

                /*
                 * No modificamos .value porque contiene
                 * valores como:
                 *
                 * CON_UNIDAD
                 * SIN_UNIDAD_OFICINA
                 * 0
                 * 1
                 */

                campo.checked =
                    false;

                return;
            }


            /* =================================================
               CHECKBOXES
            ================================================= */

            if (
                campo instanceof
                HTMLInputElement
                && campo.type === 'checkbox'
            ) {

                campo.checked =
                    false;

                return;
            }


            /* =================================================
               RESTO DE CAMPOS
            ================================================= */

            campo.value =
                '';
        }
    );

    /* =====================================================
    DATOS DEL REPORTE
    ===================================================== */

    limpiarDatosReporteEditar(
        modal
    );

    /* =====================================================
       PERSONAL
    ===================================================== */

    limpiarPersonalEditar(
        modal
    );


    /* =====================================================
       UNIDADES
    ===================================================== */

    limpiarUnidadesEditar(
        modal
    );


    /* =====================================================
       MOTIVOS
    ===================================================== */

    limpiarMotivosEditar(
        modal
    );


    /* =====================================================
       OBSERVACIONES
    ===================================================== */

    limpiarObservacionesEditar(
        modal
    );


    /* =====================================================
       EVIDENCIA
    ===================================================== */

    limpiarEvidenciaEditar(
        modal,
        formulario
    );
}


/* =========================================================
   ESTADO VISUAL
========================================================= */

function actualizarEstadoFila(
    celda,
    estado
) {

    if (!celda) {
        return;
    }


    celda.innerHTML =
        '';


    const etiqueta =
        document.createElement(
            'span'
        );


    etiqueta.className =
        `reportes-tabla__estado ${obtenerClaseEstado(
            estado
        )}`;


    etiqueta.textContent =
        estado
        || 'Pendiente';


    celda.appendChild(
        etiqueta
    );
}


/* =========================================================
   UTILIDADES LOCALES DE FOLIO
========================================================= */

function obtenerPrefijoTemporal(
    folio
) {

    const valor =
        String(
            folio || ''
        ).trim();


    if (!valor) {

        return 'QJ';
    }


    const partes =
        valor.split(
            '-'
        );


    return partes.length > 1
        ? partes[0]
        : 'QJ';
}


/* =========================================================
   OBTENER NÚMERO TEMPORAL
========================================================= */

function obtenerNumeroTemporal(
    folio
) {

    const valor =
        String(
            folio || ''
        ).trim();


    if (!valor) {

        return '';
    }


    const partes =
        valor.split(
            '-'
        );


    if (
        partes.length <= 1
    ) {

        return valor;
    }


    return partes
        .slice(
            1
        )
        .join(
            '-'
        );
}


