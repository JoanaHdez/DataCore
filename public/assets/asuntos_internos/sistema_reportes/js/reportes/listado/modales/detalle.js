/* =========================================================
   UTILIDADES
========================================================= */

import {
    asignarTextoDetalle,
    escaparHtmlDetalle,
} from './detalle/utils/texto.js';


import {
    cargarDatosReporteDetalle,
} from './detalle/secciones/datosReporte.js';


import {
    cargarIdentificacionDetalle,
} from './detalle/secciones/identificacion.js';


import {
    cargarDatosHechosDetalle,
} from './detalle/secciones/datosHechos.js';


import {
    cargarUbicacionDetalle,
} from './detalle/secciones/ubicacion.js';



import {
    renderizarPersonalDetalle,
} from './detalle/secciones/personal.js';


import {
    renderizarUnidadesDetalle,
} from './detalle/secciones/unidades.js';


import {
    cargarQuejosoDetalle,
} from './detalle/secciones/quejoso.js';


import {
    cargarClasificacionDetalle,
} from './detalle/secciones/clasificacion.js';


import {
    renderizarEvidenciasDetalle,
    limpiarEvidenciaDetalle,
} from './detalle/secciones/evidencias.js';

import {
    cargarObservacionesDetalle,
} from './detalle/secciones/observaciones.js';


/* =========================================================
   SISTEMA DE REPORTES - ASUNTOS INTERNOS
   Listado - Detalle real del reporte
========================================================= */


document.addEventListener(
    'DOMContentLoaded',
    () => {

        inicializarDetalleReporte();

    }
);


/* =========================================================
   INICIALIZAR
========================================================= */

function inicializarDetalleReporte() {

    const modal =
        document.querySelector(
            '#modal-detalle-reporte'
        );


    if (!modal) {
        return;
    }


    /* =====================================================
       ABRIR
    ===================================================== */

    document.addEventListener(
        'click',
        async (evento) => {

            const boton =
                evento.target.closest(
                    '[data-accion="ver"]'
                );


            if (!boton) {
                return;
            }


            const idReporte =
                Number(
                    boton.dataset.idReporte
                    || 0
                );


            if (
                !Number.isInteger(idReporte)
                || idReporte <= 0
            ) {

                console.error(
                    'El reporte no contiene un id_reporte válido.'
                );

                return;
            }


            await abrirDetalleReporte(
                modal,
                idReporte
            );

        }
    );


    /* =====================================================
       NAVEGACIÓN Y CIERRE
    ===================================================== */

    modal.addEventListener(
        'click',
        (evento) => {

            const botonSeccion =
                evento.target.closest(
                    '[data-detalle-seccion]'
                );


            if (botonSeccion) {

                mostrarSeccionDetalle(
                    modal,
                    botonSeccion
                        .dataset
                        .detalleSeccion
                );


                return;
            }


            const cerrar =
                evento.target.closest(
                    '[data-cerrar-modal]'
                );


            if (!cerrar) {
                return;
            }


            cerrarDetalleReporte(
                modal
            );

        }
    );


    /* =====================================================
       ESCAPE
    ===================================================== */

    document.addEventListener(
        'keydown',
        (evento) => {

            if (
                evento.key === 'Escape'
                && modal.classList.contains(
                    'modal-reporte--visible'
                )
            ) {

                cerrarDetalleReporte(
                    modal
                );

            }

        }
    );

}


/* =========================================================
   ABRIR DETALLE
========================================================= */

async function abrirDetalleReporte(
    modal,
    idReporte
) {

    limpiarDetalleReporte(
        modal
    );


    mostrarSeccionDetalle(
        modal,
        'datos'
    );


    mostrarModalDetalle(
        modal
    );


    try {

        const datos =
            await consultarDetalleReporte(
                idReporte
            );


        if (
            !datos
            || datos.success !== true
            || !datos.reporte
        ) {

            throw new Error(
                datos?.message
                || 'No fue posible consultar el reporte.'
            );
        }


        cargarDetalleReporte(
            modal,
            datos
        );


    } catch (error) {

        console.error(
            'Error consultando detalle:',
            error
        );


        cerrarDetalleReporte(
            modal
        );


        window.alert(
            error.message
            || 'No fue posible consultar el detalle del reporte.'
        );

    }

}


/* =========================================================
   CONSULTAR BACKEND
========================================================= */

async function consultarDetalleReporte(
    idReporte
) {

    /* const baseUrl =
        document
            .querySelector('base')
            ?.href
        || `${window.location.origin}/`;


    const url =
        new URL(
            `asuntos-internos/reportes/detalle/${idReporte}`,
            baseUrl
        ); */

    const url =
        new URL(
            `DataCore/public/asuntos-internos/reportes/detalle/${idReporte}`,
            `${window.location.origin}/`
        );

    const respuesta =
        await fetch(
            url.toString(),
            {
                method: 'GET',

                headers: {
                    Accept:
                        'application/json',
                },

                credentials:
                    'same-origin',
            }
        );


    let datos = null;


    try {

        datos =
            await respuesta.json();

    } catch (error) {

        throw new Error(
            'El servidor devolvió una respuesta no válida.'
        );

    }


    if (
        !respuesta.ok
    ) {

        throw new Error(
            datos?.message
            || 'No fue posible consultar el reporte.'
        );

    }


    return datos;

}


/* =========================================================
   CARGAR DETALLE COMPLETO
========================================================= */

function cargarDetalleReporte(
    modal,
    datos
) {

    const reporte =
        datos.reporte
        || {};


    const personal =
        Array.isArray(
            datos.personal
        )
            ? datos.personal
            : [];


    const unidades =
        Array.isArray(
            datos.unidades
        )
            ? datos.unidades
            : [];


    const evidencias =
        Array.isArray(
            datos.evidencias
        )
            ? datos.evidencias
            : [];


    const sancion =
        datos.sancion
            && typeof datos.sancion === 'object'
            ? datos.sancion
            : null;


    const motivos =
        Array.isArray(
            datos.motivos
        )
            ? datos.motivos
            : [];


    const folio =
        String(
            reporte.folio
            || ''
        ).trim();


    /* =====================================================
       HEADER
    ===================================================== */

    asignarTextoDetalle(
        modal,
        '#detalle-meta-expediente',
        reporte.expediente
    );


    asignarTextoDetalle(
        modal,
        '#detalle-meta-estado',
        reporte.estado_actual
    );


    const titulo =
        modal.querySelector(
            '#modal-detalle-titulo'
        );


    if (titulo) {

        titulo.textContent =
            folio
                ? `Reporte ${folio}`
                : 'Reporte';

    }


    /* =====================================================
    DATOS DEL REPORTE
    ===================================================== */

    cargarDatosReporteDetalle(
        modal,
        reporte
    );


    /* =====================================================
    IDENTIFICACIÓN
    ===================================================== */

    cargarIdentificacionDetalle(
        modal,
        reporte
    );


    /* =====================================================
    DATOS DE LOS HECHOS
    ===================================================== */

    cargarDatosHechosDetalle(
        modal,
        reporte
    );


    /* =====================================================
    UBICACIÓN
    ===================================================== */

    cargarUbicacionDetalle(
        modal,
        reporte
    );


    /* =====================================================
       PERSONAL Y UNIDADES
    ===================================================== */

    renderizarPersonalDetalle(
        modal,
        personal
    );


    renderizarUnidadesDetalle(
        modal,
        unidades,
        reporte.modalidad_unidad
    );

    /* =====================================================
    QUEJOSO
    ===================================================== */

    cargarQuejosoDetalle(
        modal,
        reporte
    );


    /* =====================================================
    CLASIFICACIÓN Y SEGUIMIENTO
    ===================================================== */

    cargarClasificacionDetalle(
        modal,
        reporte,
        motivos
    );

    /* =====================================================
    OBSERVACIONES
    ===================================================== */

    cargarObservacionesDetalle(
        modal,
        reporte
    );


    /* =====================================================
       EVIDENCIAS
    ===================================================== */

    renderizarEvidenciasDetalle(
        modal,
        evidencias
    );

}


/* =========================================================
   SANCIÓN DISCIPLINARIA
========================================================= */

function renderizarSancionDetalle(
    modal,
    sancion
) {

    const campoSancion =
        modal.querySelector(
            '#detalle-sancion-disciplinaria'
        );


    const avisoOrigen =
        modal.querySelector(
            '#detalle-sancion-origen'
        );


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




/* =========================================================
   MOSTRAR MODAL
========================================================= */

function mostrarModalDetalle(
    modal
) {

    modal.classList.add(
        'modal-reporte--visible'
    );


    modal.setAttribute(
        'aria-hidden',
        'false'
    );


    document.body.classList.add(
        'modal-abierto'
    );

}


/* =========================================================
   CERRAR MODAL
========================================================= */

function cerrarDetalleReporte(
    modal
) {

    const elementoActivo =
        document.activeElement;


    if (
        elementoActivo
        && modal.contains(
            elementoActivo
        )
    ) {

        elementoActivo.blur();

    }


    modal.classList.remove(
        'modal-reporte--visible'
    );


    modal.setAttribute(
        'aria-hidden',
        'true'
    );


    document.body.classList.remove(
        'modal-abierto'
    );

}


/* =========================================================
   CAMBIAR SECCIÓN
========================================================= */

function mostrarSeccionDetalle(
    modal,
    seccion
) {

    const botones =
        modal.querySelectorAll(
            '[data-detalle-seccion]'
        );


    const paneles =
        modal.querySelectorAll(
            '[data-detalle-panel]'
        );


    botones.forEach(
        (boton) => {

            const activo =
                boton
                    .dataset
                    .detalleSeccion
                === seccion;


            boton.classList.toggle(
                'detalle-reporte-nav__item--active',
                activo
            );

        }
    );


    paneles.forEach(
        (panel) => {

            const activo =
                panel
                    .dataset
                    .detallePanel
                === seccion;


            panel.classList.toggle(
                'detalle-reporte-seccion--active',
                activo
            );

        }
    );


    const body =
        modal.querySelector(
            '.modal-reporte__body--detalle'
        );


    if (body) {

        body.scrollTo({
            top: 0,
            behavior: 'smooth',
        });

    }

}


/* =========================================================
   LIMPIAR
========================================================= */

function limpiarDetalleReporte(
    modal
) {

    modal
        .querySelectorAll(
            '.detalle-reporte-campo strong'
        )
        .forEach(
            (campo) => {

                campo.textContent =
                    '—';

            }
        );

    const avisoSancion =
        modal.querySelector(
            '#detalle-sancion-origen'
        );


    if (avisoSancion) {

        avisoSancion.hidden =
            true;

        avisoSancion.textContent =
            '';
    }

    const titulo =
        modal.querySelector(
            '#modal-detalle-titulo'
        );


    if (titulo) {

        titulo.textContent =
            'Reporte';

    }


    asignarTextoDetalle(
        modal,
        '#detalle-meta-expediente',
        ''
    );


    asignarTextoDetalle(
        modal,
        '#detalle-meta-estado',
        ''
    );


    renderizarPersonalDetalle(
        modal,
        []
    );


    renderizarUnidadesDetalle(
        modal,
        [],
        'CON_UNIDAD'
    );


    limpiarEvidenciaDetalle(
        modal
    );

}


/* =========================================================
   SISTEMA DE REPORTES - ASUNTOS INTERNOS
   Felicitaciones - Detalle
========================================================= */

document.addEventListener(
    'DOMContentLoaded',
    () => {
        inicializarDetalleFelicitacion();
    }
);


/* =========================================================
   INICIALIZAR
========================================================= */

function inicializarDetalleFelicitacion() {

    const modal =
        document.querySelector(
            '#modal-detalle-felicitacion'
        );


    if (!modal) {
        return;
    }


    /* =====================================================
       ABRIR
    ===================================================== */

    document.addEventListener(
        'click',
        async (evento) => {

            const boton =
                evento.target.closest(
                    '[data-accion-felicitacion="ver"]'
                );


            if (!boton) {
                return;
            }


            const idFelicitacion =
                Number(
                    boton.dataset.idFelicitacion
                    || 0
                );


            if (
                !Number.isInteger(
                    idFelicitacion
                )
                || idFelicitacion <= 0
            ) {

                console.error(
                    'La felicitación no contiene un id válido.'
                );

                return;
            }


            await abrirDetalleFelicitacion(
                modal,
                idFelicitacion
            );
        }
    );


    /* =====================================================
       NAVEGACIÓN Y CIERRE
    ===================================================== */

    modal.addEventListener(
        'click',
        (evento) => {

            /* =============================================
               CAMBIAR SECCIÓN
            ============================================== */

            const botonSeccion =
                evento.target.closest(
                    '[data-detalle-felicitacion-seccion]'
                );


            if (botonSeccion) {

                const seccion =
                    botonSeccion
                        .dataset
                        .detalleFelicitacionSeccion;


                mostrarSeccionDetalleFelicitacion(
                    modal,
                    seccion
                );


                return;
            }


            /* =============================================
               CERRAR
            ============================================== */

            const cerrar =
                evento.target.closest(
                    '[data-cerrar-detalle-felicitacion]'
                );


            if (!cerrar) {
                return;
            }


            cerrarDetalleFelicitacion(
                modal
            );
        }
    );


    /* =====================================================
       ESC
    ===================================================== */

    document.addEventListener(
        'keydown',
        (evento) => {

            if (
                evento.key === 'Escape'
                && !modal.hidden
            ) {

                cerrarDetalleFelicitacion(
                    modal
                );
            }
        }
    );
}


/* =========================================================
   ABRIR DETALLE
========================================================= */

async function abrirDetalleFelicitacion(
    modal,
    idFelicitacion
) {

    /* =====================================================
       LIMPIAR DATOS DEL REGISTRO ANTERIOR
    ===================================================== */

    limpiarDetalleFelicitacion(
        modal
    );


    /* =====================================================
       REGRESAR A LA PRIMERA SECCIÓN
    ===================================================== */

    mostrarSeccionDetalleFelicitacion(
        modal,
        'datos'
    );


    /* =====================================================
       MOSTRAR MODAL INMEDIATAMENTE

       No esperamos al backend para abrirlo.
    ===================================================== */

    mostrarModalDetalleFelicitacion(
        modal
    );


    try {

        /* =================================================
           CONSULTAR BACKEND
        ================================================= */

        const resultado =
            await consultarDetalleFelicitacion(
                idFelicitacion
            );


        /* =================================================
           VALIDAR RESPUESTA
        ================================================= */

        if (
            !resultado
            || resultado.success !== true
            || !resultado.felicitacion
        ) {

            throw new Error(
                resultado?.message
                || 'No fue posible consultar la felicitación.'
            );
        }


        /* =================================================
           CARGAR INFORMACIÓN
        ================================================= */

        cargarDetalleFelicitacion(
            modal,
            resultado
        );


    } catch (error) {

        console.error(
            'Error consultando detalle de felicitación:',
            error
        );


        /* =================================================
           CERRAR SI HUBO ERROR
        ================================================= */

        cerrarDetalleFelicitacion(
            modal
        );


        window.alert(
            error.message
            || 'No fue posible consultar el detalle de la felicitación.'
        );
    }
}


/* =========================================================
   CONSULTAR BACKEND
========================================================= */

async function consultarDetalleFelicitacion(
    idFelicitacion
) {

    const url =
        new URL(
            `DataCore/public/asuntos-internos/reportes/felicitaciones/detalle/${idFelicitacion}`,
            `${window.location.origin}/`
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


    let resultado =
        null;


    try {

        resultado =
            await respuesta.json();


    } catch (error) {

        throw new Error(
            'El servidor devolvió una respuesta no válida.'
        );
    }


    if (
        !respuesta.ok
    ) {

        throw new Error(
            resultado?.message
            || 'No fue posible consultar la felicitación.'
        );
    }


    return resultado;
}


/* =========================================================
   CARGAR DETALLE COMPLETO
========================================================= */

function cargarDetalleFelicitacion(
    modal,
    resultado
) {

    const felicitacion =
        resultado.felicitacion
        || {};


    const personal =
        Array.isArray(
            resultado.personal
        )
            ? resultado.personal
            : [];


    const unidades =
        Array.isArray(
            resultado.unidades
        )
            ? resultado.unidades
            : [];


    /* =====================================================
       TÍTULO
    ===================================================== */

    const folio =
        String(
            felicitacion.folio
            || ''
        ).trim();


    const tituloFolio =
        modal.querySelector(
            '#detalle-felicitacion-titulo-folio'
        );


    if (tituloFolio) {

        tituloFolio.textContent =
            folio !== ''
                ? ` ${folio}`
                : '';
    }


    /* =====================================================
       DATOS GENERALES
    ===================================================== */

    asignarTextoDetalleFelicitacion(
        modal,
        '#detalle-felicitacion-folio',
        felicitacion.folio
    );


    asignarTextoDetalleFelicitacion(
        modal,
        '#detalle-felicitacion-fecha',
        formatearFechaFelicitacion(
            felicitacion.fecha_registro
        )
    );


    asignarTextoDetalleFelicitacion(
        modal,
        '#detalle-felicitacion-felicitante',
        felicitacion.nombre_felicitante
    );


    /* =====================================================
       RAZÓN
    ===================================================== */

    asignarTextoDetalleFelicitacion(
        modal,
        '#detalle-felicitacion-razon',
        felicitacion.razon_felicitacion
    );


    /* =====================================================
       PERSONAL
    ===================================================== */

    renderizarPersonalDetalleFelicitacion(
        modal,
        personal
    );


    /* =====================================================
       UNIDADES
    ===================================================== */

    renderizarUnidadesDetalleFelicitacion(
        modal,
        unidades
    );
}


/* =========================================================
   PERSONAL
========================================================= */

function renderizarPersonalDetalleFelicitacion(
    modal,
    personal
) {

    const vacio =
        modal.querySelector(
            '#detalle-felicitacion-personal-vacio'
        );


    const wrapper =
        modal.querySelector(
            '#detalle-felicitacion-personal-tabla-wrapper'
        );


    const tbody =
        modal.querySelector(
            '#detalle-felicitacion-personal'
        );


    if (
        !vacio
        || !wrapper
        || !tbody
    ) {
        return;
    }


    tbody.innerHTML =
        '';


    if (
        !Array.isArray(
            personal
        )
        || personal.length === 0
    ) {

        vacio.hidden =
            false;


        wrapper.hidden =
            true;


        return;
    }


    personal.forEach(
        (persona) => {

            const fila =
                document.createElement(
                    'tr'
                );


            const nombre =
                String(
                    persona.nombre_snapshot
                    || persona.nombre
                    || ''
                )
                    .trim()
                    .toUpperCase();


            const nomina =
                String(
                    persona.perscod
                    || persona.nomina
                    || ''
                ).trim();


            const area =
                String(
                    persona.area_snapshot
                    || persona.area
                    || ''
                )
                    .trim()
                    .toUpperCase();


            const turno =
                String(
                    persona.turno_snapshot
                    || persona.turno
                    || ''
                )
                    .trim()
                    .toUpperCase();


            const foto =
                String(
                    persona.foto
                    || ''
                ).trim();


            const inicial =
                nombre
                    ? nombre.charAt(0)
                    : '?';


            const fotoHtml =
                foto
                    ? `
                        <div class="detalle-felicitacion-personal__foto">

                            <img
                                src="${escaparHtmlFelicitacion(foto)}"
                                alt=""
                                onerror="
                                    this.style.display='none';
                                    this.nextElementSibling.style.display='flex';
                                "
                            >

                            <span style="display:none;">
                                ${escaparHtmlFelicitacion(inicial)}
                            </span>

                        </div>
                    `
                    : `
                        <div class="detalle-felicitacion-personal__foto">

                            <span>
                                ${escaparHtmlFelicitacion(inicial)}
                            </span>

                        </div>
                    `;


            fila.innerHTML = `

                <td>
                    ${fotoHtml}
                </td>

                <td>
                    <strong class="detalle-felicitacion-personal__nombre">
                        ${escaparHtmlFelicitacion(
                nombre
                || '—'
            )}
                    </strong>
                </td>

                <td>
                    ${escaparHtmlFelicitacion(
                nomina
                || '—'
            )}
                </td>

                <td>
                    ${escaparHtmlFelicitacion(
                area
                || '—'
            )}
                </td>

                <td>
                    ${escaparHtmlFelicitacion(
                turno
                || '—'
            )}
                </td>

            `;


            tbody.appendChild(
                fila
            );
        }
    );


    vacio.hidden =
        true;


    wrapper.hidden =
        false;
}

/* =========================================================
   UNIDADES
========================================================= */

function renderizarUnidadesDetalleFelicitacion(
    modal,
    unidades
) {

    const contenedorUnidades =
        modal.querySelector(
            '#detalle-felicitacion-unidades-contenedor'
        );


    const contenedorSinUnidad =
        modal.querySelector(
            '#detalle-felicitacion-sin-unidad'
        );


    const tbody =
        modal.querySelector(
            '#detalle-felicitacion-unidades'
        );


    if (
        !contenedorUnidades
        || !contenedorSinUnidad
        || !tbody
    ) {
        return;
    }


    tbody.innerHTML =
        '';


    /* =====================================================
       SIN UNIDAD / OFICINA
    ===================================================== */

    if (
        !Array.isArray(
            unidades
        )
        || unidades.length === 0
    ) {

        contenedorUnidades.hidden =
            true;


        contenedorSinUnidad.hidden =
            false;


        return;
    }


    /* =====================================================
       CON UNIDAD
    ===================================================== */

    contenedorSinUnidad.hidden =
        true;


    contenedorUnidades.hidden =
        false;


    unidades.forEach(
        (unidad) => {

            const fila =
                document.createElement(
                    'tr'
                );


            const noEconomico =
                normalizarMayusculasFelicitacion(
                    unidad.no_economico
                    || unidad.no_economico_snapshot
                );


            const placas =
                normalizarMayusculasFelicitacion(
                    unidad.placas
                    || unidad.placas_snapshot
                );


            const marca =
                normalizarMayusculasFelicitacion(
                    unidad.marca
                    || unidad.marca_snapshot
                );


            const submarca =
                normalizarMayusculasFelicitacion(
                    unidad.submarca
                    || unidad.submarca_snapshot
                );


            const color =
                normalizarMayusculasFelicitacion(
                    unidad.color
                    || unidad.color_snapshot
                );


            const estatus =
                normalizarMayusculasFelicitacion(
                    unidad.estatus
                    || unidad.estatus_snapshot
                );


            const servicio =
                normalizarMayusculasFelicitacion(
                    unidad.servicio
                    || unidad.servicio_snapshot
                );


            const tipo =
                normalizarMayusculasFelicitacion(
                    unidad.tipo
                    || unidad.tipo_snapshot
                );


            const marcaSubmarca =
                [
                    marca,
                    submarca,
                ]
                    .filter(
                        Boolean
                    )
                    .join(
                        ' '
                    );


            fila.innerHTML = `

                <td>

                    <strong>
                        ${escaparHtmlFelicitacion(
                noEconomico
                || '—'
            )}
                    </strong>

                    <small
                        style="
                            display:block;
                            margin-top:3px;
                        "
                    >
                        Placas:
                        ${escaparHtmlFelicitacion(
                placas
                || '—'
            )}
                    </small>

                </td>


                <td>
                    ${escaparHtmlFelicitacion(
                marcaSubmarca
                || '—'
            )}
                </td>


                <td>
                    ${escaparHtmlFelicitacion(
                color
                || '—'
            )}
                </td>


                <td>
                    ${escaparHtmlFelicitacion(
                estatus
                || '—'
            )}
                </td>


                <td>
                    ${escaparHtmlFelicitacion(
                servicio
                || '—'
            )}
                </td>


                <td>
                    ${escaparHtmlFelicitacion(
                tipo
                || '—'
            )}
                </td>

            `;


            tbody.appendChild(
                fila
            );
        }
    );
}


/* =========================================================
   NAVEGACIÓN
========================================================= */

function mostrarSeccionDetalleFelicitacion(
    modal,
    seccion
) {

    const botones =
        modal.querySelectorAll(
            '[data-detalle-felicitacion-seccion]'
        );


    const paneles =
        modal.querySelectorAll(
            '[data-detalle-felicitacion-panel]'
        );


    /* =====================================================
       BOTONES
    ===================================================== */

    botones.forEach(
        (boton) => {

            const activo =
                boton
                    .dataset
                    .detalleFelicitacionSeccion
                === seccion;


            boton.classList.toggle(
                'detalle-felicitacion-nav__item--active',
                activo
            );
        }
    );


    /* =====================================================
       PANELES
    ===================================================== */

    paneles.forEach(
        (panel) => {

            const activo =
                panel
                    .dataset
                    .detalleFelicitacionPanel
                === seccion;


            panel.hidden =
                !activo;


            panel.classList.toggle(
                'detalle-felicitacion-panel--active',
                activo
            );
        }
    );


    /* =====================================================
       SCROLL ARRIBA
    ===================================================== */

    const body =
        modal.querySelector(
            '.modal-felicitacion__body'
        );


    if (body) {

        body.scrollTo({
            top:
                0,

            behavior:
                'smooth',
        });
    }
}


/* =========================================================
   MOSTRAR MODAL
========================================================= */

function mostrarModalDetalleFelicitacion(
    modal
) {

    modal.hidden =
        false;


    modal.setAttribute(
        'aria-hidden',
        'false'
    );


    document.body.classList.add(
        'modal-abierto'
    );
}


/* =========================================================
   CERRAR
========================================================= */

function cerrarDetalleFelicitacion(
    modal
) {

    const activo =
        document.activeElement;


    if (
        activo
        && modal.contains(
            activo
        )
    ) {

        activo.blur();
    }


    modal.hidden =
        true;


    modal.setAttribute(
        'aria-hidden',
        'true'
    );


    document.body.classList.remove(
        'modal-abierto'
    );


    /* =====================================================
       REGRESAR A PRIMERA PESTAÑA
    ===================================================== */

    mostrarSeccionDetalleFelicitacion(
        modal,
        'datos'
    );
}


/* =========================================================
   LIMPIAR
========================================================= */

function limpiarDetalleFelicitacion(
    modal
) {

    /* =====================================================
       TÍTULO
    ===================================================== */

    const tituloFolio =
        modal.querySelector(
            '#detalle-felicitacion-titulo-folio'
        );


    if (tituloFolio) {

        tituloFolio.textContent =
            '';
    }


    /* =====================================================
       TEXTOS
    ===================================================== */

    asignarTextoDetalleFelicitacion(
        modal,
        '#detalle-felicitacion-folio',
        ''
    );


    asignarTextoDetalleFelicitacion(
        modal,
        '#detalle-felicitacion-fecha',
        ''
    );


    asignarTextoDetalleFelicitacion(
        modal,
        '#detalle-felicitacion-felicitante',
        ''
    );


    asignarTextoDetalleFelicitacion(
        modal,
        '#detalle-felicitacion-razon',
        ''
    );


    /* =====================================================
       PERSONAL
    ===================================================== */

    renderizarPersonalDetalleFelicitacion(
        modal,
        []
    );


    /* =====================================================
       UNIDADES
    ===================================================== */

    renderizarUnidadesDetalleFelicitacion(
        modal,
        []
    );
}


/* =========================================================
   FECHA
========================================================= */

function formatearFechaFelicitacion(
    valor
) {

    const fecha =
        String(
            valor
            || ''
        ).trim();


    if (!fecha) {
        return '';
    }


    /* =====================================================
       YA VIENE DD/MM/YYYY
    ===================================================== */

    if (
        /^\d{2}\/\d{2}\/\d{4}$/.test(
            fecha
        )
    ) {

        return fecha;
    }


    /* =====================================================
       VIENE YYYY-MM-DD
    ===================================================== */

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
   ASIGNAR TEXTO
========================================================= */

function asignarTextoDetalleFelicitacion(
    modal,
    selector,
    valor
) {

    const elemento =
        modal.querySelector(
            selector
        );


    if (!elemento) {
        return;
    }


    const texto =
        String(
            valor
            ?? ''
        ).trim();


    elemento.textContent =
        texto !== ''
            ? texto
            : '—';
}


/* =========================================================
   MAYÚSCULAS
========================================================= */

function normalizarMayusculasFelicitacion(
    valor
) {

    return String(
        valor
        ?? ''
    )
        .trim()
        .toUpperCase();
}


/* =========================================================
   ESCAPAR HTML
========================================================= */

function escaparHtmlFelicitacion(
    valor
) {

    return String(
        valor
        ?? ''
    )
        .replaceAll(
            '&',
            '&amp;'
        )
        .replaceAll(
            '<',
            '&lt;'
        )
        .replaceAll(
            '>',
            '&gt;'
        )
        .replaceAll(
            '"',
            '&quot;'
        )
        .replaceAll(
            "'",
            '&#039;'
        );
}