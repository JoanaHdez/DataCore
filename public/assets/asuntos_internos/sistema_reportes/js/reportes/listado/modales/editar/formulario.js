/* =========================================================
   SISTEMA DE REPORTES - ASUNTOS INTERNOS
   Editar reporte - Formulario
========================================================= */

import {
    estadoPersonal,
    estadoUnidades,
    establecerPersonal,
    establecerUnidades,
} from './estado.js';

import {
    asignarValorEditar,
    asignarSelectSeguro,
    obtenerDatoFormulario,
    construirFolio,
    formatearFechaTabla,
    convertirFechaInput,
    leerDatasetArray,
    escaparHTML,
    obtenerClaseEstado,
} from './utilidades.js';

import {
    renderizarPersonalEditar,
    limpiarSelectorPersonal,
} from './personal.js';

import {
    renderizarUnidadesEditar,
    limpiarSelectorUnidad,
} from './unidades.js';

import {
    mostrarEvidenciaExistente,
    mostrarEvidenciaNueva,
    obtenerNuevasEvidencias,
} from './evidencia.js';


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


        /* HECHOS */

        fecha_hechos:
            '',

        hora_hechos:
            '',

        descripcion:
            '',


        /* UBICACIÓN */

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

        /* PERSONAL */

        personal:
            leerDatasetArray(
                fila.dataset.personal
            ),


        /* UNIDADES */

        unidades:
            leerDatasetArray(
                fila.dataset.unidades
            ),


        /* QUEJOSO */

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


        /* CLASIFICACIÓN */

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

        resolucion:
            celdas[7]
                ?.textContent
                .trim()
            || '',

        motivos:
            '',


        /* ADICIONAL */

        observaciones:
            '',


        /* EVIDENCIA */

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

    limpiarFormularioEditar(
        modal,
        formulario
    );


    /* =====================================================
       DATOS DEL REPORTE
    ===================================================== */

    asignarValorEditar(
        modal,
        '#editar-prefijo',
        reporte.prefijo
    );

    asignarValorEditar(
        modal,
        '#editar-numero-folio',
        reporte.numero_folio
    );

    asignarValorEditar(
        modal,
        '#editar-fecha-registro',
        reporte.fecha_registro
    );


    /* =====================================================
       IDENTIFICACIÓN
    ===================================================== */

    asignarValorEditar(
        modal,
        '#editar-folio-ip',
        reporte.folio_ip
    );

    asignarValorEditar(
        modal,
        '#editar-fecha-queja',
        reporte.fecha_queja
    );

    asignarValorEditar(
        modal,
        '#editar-fecha-acuerdo',
        reporte.fecha_acuerdo
    );

    asignarValorEditar(
        modal,
        '#editar-expediente',
        reporte.expediente
    );

    asignarValorEditar(
        modal,
        '#editar-nomenclatura',
        reporte.nomenclatura
    );

    asignarValorEditar(
        modal,
        '#editar-no-oficio',
        reporte.no_oficio
    );


    /* =====================================================
       HECHOS
    ===================================================== */

    asignarValorEditar(
        modal,
        '#editar-fecha-hechos',
        reporte.fecha_hechos
    );

    asignarValorEditar(
        modal,
        '#editar-hora-hechos',
        reporte.hora_hechos
    );

    asignarValorEditar(
        modal,
        '#editar-descripcion',
        reporte.descripcion
    );


    /* =====================================================
    UBICACIÓN
    ===================================================== */

    asignarValorEditar(
        modal,
        '#editar-calle',
        reporte.calle
    );

    asignarValorEditar(
        modal,
        '#editar-numero',
        reporte.numero
    );

    asignarValorEditar(
        modal,
        '#editar-colonia',
        reporte.colonia
    );

    asignarValorEditar(
        modal,
        '#editar-entre-calle',
        reporte.entre_calle
    );

    asignarValorEditar(
        modal,
        '#editar-y-calle',
        reporte.y_calle
    );

    asignarValorEditar(
        modal,
        '#editar-municipio',
        reporte.municipio
    );

    asignarValorEditar(
        modal,
        '#editar-estado',
        reporte.estado
    );

    asignarValorEditar(
        modal,
        '#editar-sector',
        reporte.sector
    );

    asignarValorEditar(
        modal,
        '#editar-cuadrante',
        reporte.cuadrante
    );

    asignarValorEditar(
        modal,
        '#editar-id-cuadra',
        reporte.id_cuadra
    );

    asignarValorEditar(
        modal,
        '#editar-latitud',
        reporte.latitud
    );

    asignarValorEditar(
        modal,
        '#editar-longitud',
        reporte.longitud
    );

    asignarValorEditar(
        modal,
        '#editar-origen-ubicacion',
        reporte.origen_ubicacion
    );


    /*
    * Coordenadas visibles.
    *
    * X = Longitud
    * Y = Latitud
    */

    asignarValorEditar(
        modal,
        '#editar-longitud-visible',
        reporte.longitud
    );

    asignarValorEditar(
        modal,
        '#editar-latitud-visible',
        reporte.latitud
    );


    const latitudEditar =
        String(
            reporte.latitud
            || ''
        ).trim();


    const longitudEditar =
        String(
            reporte.longitud
            || ''
        ).trim();


    const coordenadasEditar =
        latitudEditar && longitudEditar
            ? `${latitudEditar}, ${longitudEditar}`
            : '';


    asignarValorEditar(
        modal,
        '#editar-coordenadas',
        coordenadasEditar
    );

    /* =====================================================
       PERSONAL
    ===================================================== */

    establecerPersonal(
        Array.isArray(
            reporte.personal
        )
            ? reporte.personal
            : []
    );


    limpiarSelectorPersonal(
        modal
    );


    renderizarPersonalEditar(
        modal
    );


    /* =====================================================
       UNIDADES
    ===================================================== */

    establecerUnidades(
        Array.isArray(
            reporte.unidades
        )
            ? reporte.unidades
            : []
    );


    limpiarSelectorUnidad(
        modal
    );


    renderizarUnidadesEditar(
        modal
    );


    /* =====================================================
       QUEJOSO
    ===================================================== */

    asignarValorEditar(
        modal,
        '#editar-quejoso',
        reporte.quejoso
    );

    asignarValorEditar(
        modal,
        '#editar-edad',
        reporte.edad
    );

    asignarSelectSeguro(
        modal,
        '#editar-genero',
        reporte.genero
    );

    asignarValorEditar(
        modal,
        '#editar-telefono',
        reporte.telefono
    );

    asignarValorEditar(
        modal,
        '#editar-correo',
        reporte.correo
    );


    /* =====================================================
       CLASIFICACIÓN
    ===================================================== */

    asignarValorEditar(
        modal,
        '#editar-clasificacion',
        reporte.clasificacion
    );

    asignarValorEditar(
        modal,
        '#editar-inspector',
        reporte.inspector
    );

    asignarValorEditar(
        modal,
        '#editar-investigador',
        reporte.investigador
    );

    asignarValorEditar(
        modal,
        '#editar-quien-emite-resolucion',
        reporte.quien_emite_resolucion
    );

    asignarValorEditar(
        modal,
        '#editar-resolucion',
        reporte.resolucion
    );

    asignarValorEditar(
        modal,
        '#editar-motivos',
        reporte.motivos
    );


    /* =====================================================
       OBSERVACIONES
    ===================================================== */

    asignarValorEditar(
        modal,
        '#editar-observaciones',
        reporte.observaciones
    );


    /* =====================================================
       EVIDENCIA
    ===================================================== */

    mostrarEvidenciaExistente(
        modal,
        reporte.evidencias
    );


    mostrarEvidenciaNueva(
        modal,
        []
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

        'prefijo',
        'numero_folio',
        'fecha_registro',

        'folio_ip',
        'fecha_queja',
        'fecha_acuerdo',
        'expediente',
        'nomenclatura',
        'no_oficio',

        'fecha_hechos',
        'hora_hechos',
        'descripcion',

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

        'quejoso',
        'edad',
        'genero',
        'telefono',
        'correo',

        'clasificacion',
        'inspector',
        'investigador',
        'quien_emite_resolucion',
        'resolucion',
        'motivos',

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
        estadoPersonal.elementos.map(
            (persona) => ({
                ...persona,
            })
        );


    /* =====================================================
       UNIDADES
    ===================================================== */

    reporte.unidades =
        estadoUnidades.elementos.map(
            (unidad) => ({
                ...unidad,
            })
        );


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


    reporte.folio =
        construirFolio(
            reporte.prefijo,
            reporte.numero_folio
        );


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
        celdas.length < 8
    ) {
        return;
    }


    const folio =
        reporte.folio
        || construirFolio(
            reporte.prefijo,
            reporte.numero_folio
        );


    celdas[0].innerHTML =
        `<strong>${escaparHTML(
            folio
        )}</strong>`;


    celdas[1].textContent =
        formatearFechaTabla(
            reporte.fecha_queja
        );


    celdas[2].textContent =
        reporte.expediente
        || '';


    celdas[3].textContent =
        reporte.clasificacion
        || '';


    celdas[4].textContent =
        reporte.quejoso
        || '';


    /*
     * Por ahora el listado conserva
     * una sola columna de Área y Turno.
     *
     * Utilizamos la primera persona.
     */
    const primeraPersona =
        Array.isArray(
            reporte.personal
        )
        && reporte.personal.length > 0
            ? reporte.personal[0]
            : null;


    celdas[5].textContent =
        primeraPersona?.area
        || '';


    celdas[6].textContent =
        primeraPersona?.turno
        || '';


    actualizarEstadoFila(
        celdas[7],
        reporte.resolucion
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

    modal.querySelectorAll(
        'input, select, textarea'
    ).forEach(
        (campo) => {

            if (
                campo instanceof
                    HTMLInputElement
                && campo.type === 'file'
            ) {

                campo.value =
                    '';

                return;
            }


            campo.value =
                '';

        }
    );


    establecerPersonal(
        []
    );


    establecerUnidades(
        []
    );


    renderizarPersonalEditar(
        modal
    );


    renderizarUnidadesEditar(
        modal
    );


    limpiarSelectorPersonal(
        modal
    );


    limpiarSelectorUnidad(
        modal
    );


    const prefijo =
        modal.querySelector(
            '#editar-prefijo'
        );


    if (
        prefijo
        && !prefijo.value
    ) {

        prefijo.value =
            'QJ';

    }


    const inputEvidencia =
        formulario
            ?.querySelector(
                '#editar-evidencia-fotografica'
            );


    if (inputEvidencia) {

        inputEvidencia.value =
            '';

    }

}


/* =========================================================
   ESTADO VISUAL
========================================================= */

function actualizarEstadoFila(
    celda,
    estado
) {

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
        valor.split('-');


    return partes.length > 1
        ? partes[0]
        : 'QJ';

}


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
        valor.split('-');


    if (
        partes.length <= 1
    ) {
        return valor;
    }


    return partes
        .slice(1)
        .join('-');

}