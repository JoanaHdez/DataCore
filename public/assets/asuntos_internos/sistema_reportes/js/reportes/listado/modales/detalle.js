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

    asignarTextoDetalle(
        modal,
        '#detalle-prefijo',
        obtenerPrefijoFolio(
            folio
        )
    );


    asignarTextoDetalle(
        modal,
        '#detalle-numero-folio',
        obtenerNumeroFolio(
            folio
        )
    );


    asignarTextoDetalle(
        modal,
        '#detalle-fecha-registro',
        formatearFechaDetalle(
            reporte.fecha_registro
        )
    );


    /* =====================================================
       IDENTIFICACIÓN
    ===================================================== */

    asignarTextoDetalle(
        modal,
        '#detalle-folio-ip',
        reporte.folio_ip
    );


    asignarTextoDetalle(
        modal,
        '#detalle-fecha-queja',
        formatearFechaDetalle(
            reporte.fecha_queja
        )
    );


    asignarTextoDetalle(
        modal,
        '#detalle-fecha-acuerdo',
        formatearFechaDetalle(
            reporte.fecha_acuerdo
        )
    );


    asignarTextoDetalle(
        modal,
        '#detalle-expediente',
        reporte.expediente
    );


    asignarTextoDetalle(
        modal,
        '#detalle-nomenclatura',
        reporte.nomenclatura
    );


    asignarTextoDetalle(
        modal,
        '#detalle-no-oficio',
        reporte.numero_oficio
    );


    /* =====================================================
       DATOS DE LOS HECHOS
    ===================================================== */

    asignarTextoDetalle(
        modal,
        '#detalle-fecha-hechos',
        formatearFechaDetalle(
            reporte.fecha_hechos
        )
    );


    asignarTextoDetalle(
        modal,
        '#detalle-hora-hechos',
        formatearHoraDetalle(
            reporte.hora_hechos
        )
    );


    asignarTextoDetalle(
        modal,
        '#detalle-descripcion',
        reporte.descripcion_hechos
    );


    /* =====================================================
       UBICACIÓN
    ===================================================== */

    asignarTextoDetalle(
        modal,
        '#detalle-calle',
        reporte.calle
    );


    asignarTextoDetalle(
        modal,
        '#detalle-numero',
        reporte.numero_exterior
    );


    asignarTextoDetalle(
        modal,
        '#detalle-colonia',
        reporte.colonia
    );


    asignarTextoDetalle(
        modal,
        '#detalle-entre-calle',
        reporte.entre_calle
    );


    asignarTextoDetalle(
        modal,
        '#detalle-y-calle',
        reporte.y_calle
    );


    asignarTextoDetalle(
        modal,
        '#detalle-municipio',
        reporte.municipio
    );


    asignarTextoDetalle(
        modal,
        '#detalle-estado',
        reporte.estado
    );


    asignarTextoDetalle(
        modal,
        '#detalle-sector',
        reporte.sector
    );


    asignarTextoDetalle(
        modal,
        '#detalle-cuadrante',
        reporte.cuadrante
    );


    asignarTextoDetalle(
        modal,
        '#detalle-id-cuadra',
        reporte.id_cuadra
    );


    asignarTextoDetalle(
        modal,
        '#detalle-latitud',
        reporte.latitud
    );


    asignarTextoDetalle(
        modal,
        '#detalle-longitud',
        reporte.longitud
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

    asignarTextoDetalle(
        modal,
        '#detalle-quejoso',
        reporte.nombre_quejoso
    );


    asignarTextoDetalle(
        modal,
        '#detalle-edad',
        reporte.edad_quejoso
    );


    asignarTextoDetalle(
        modal,
        '#detalle-genero',
        reporte.genero_quejoso
    );


    asignarTextoDetalle(
        modal,
        '#detalle-telefono',
        reporte.telefono_quejoso
    );


    asignarTextoDetalle(
        modal,
        '#detalle-correo',
        reporte.correo_quejoso
    );


    /* =====================================================
       CLASIFICACIÓN
    ===================================================== */

    asignarTextoDetalle(
        modal,
        '#detalle-clasificacion',
        reporte.clasificacion
    );


    asignarTextoDetalle(
        modal,
        '#detalle-inspector',
        reporte.inspector
    );


    asignarTextoDetalle(
        modal,
        '#detalle-investigador',
        reporte.investigador
    );


    renderizarSancionDetalle(
        modal,
        sancion
    );

    asignarTextoDetalle(
        modal,
        '#detalle-quien-emite-resolucion',
        reporte.quien_emite_resolucion
    );


    asignarTextoDetalle(
        modal,
        '#detalle-resolucion',
        reporte.resolucion
    );


    asignarTextoDetalle(
        modal,
        '#detalle-motivos',
        reporte.motivos
    );


    /* =====================================================
       OBSERVACIONES
    ===================================================== */

    asignarTextoDetalle(
        modal,
        '#detalle-observaciones',
        reporte.observaciones
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
   PERSONAL
========================================================= */

function renderizarPersonalDetalle(
    modal,
    personal
) {

    const vacio =
        modal.querySelector(
            '#detalle-personal-vacio'
        );


    const wrapper =
        modal.querySelector(
            '#detalle-personal-tabla-wrapper'
        );


    const body =
        modal.querySelector(
            '#detalle-personal-body'
        );


    if (
        !vacio
        || !wrapper
        || !body
    ) {
        return;
    }


    body.innerHTML =
        '';


    if (
        !Array.isArray(personal)
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
                    persona.nombre
                    || ''
                )
                    .trim()
                    .toUpperCase();


            const nomina =
                String(
                    persona.nomina
                    || ''
                ).trim();


            const area =
                String(
                    persona.area
                    || ''
                )
                    .trim()
                    .toUpperCase();


            const turno =
                String(
                    persona.turno
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
                        <div class="detalle-personal__foto">

                            <img
                                src="${escaparHtmlDetalle(foto)}"
                                alt=""
                                onerror="
                                    this.style.display='none';
                                    this.nextElementSibling.style.display='flex';
                                "
                            >

                            <span style="display:none;">
                                ${escaparHtmlDetalle(inicial)}
                            </span>

                        </div>
                    `
                    : `
                        <div class="detalle-personal__foto">

                            <span>
                                ${escaparHtmlDetalle(inicial)}
                            </span>

                        </div>
                    `;


            fila.innerHTML = `

                <td>
                    ${fotoHtml}
                </td>

                <td>
                    <strong>
                        ${escaparHtmlDetalle(
                nombre || '—'
            )}
                    </strong>
                </td>

                <td>
                    ${escaparHtmlDetalle(
                nomina || '—'
            )}
                </td>

                <td>
                    ${escaparHtmlDetalle(
                area || '—'
            )}
                </td>

                <td>
                    ${escaparHtmlDetalle(
                turno || '—'
            )}
                </td>
            `;


            body.appendChild(
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

function renderizarUnidadesDetalle(
    modal,
    unidades,
    modalidadUnidad = 'CON_UNIDAD'
) {

    const vacio =
        modal.querySelector(
            '#detalle-unidades-vacio'
        );


    const sinUnidad =
        modal.querySelector(
            '#detalle-unidades-sin-unidad'
        );


    const wrapper =
        modal.querySelector(
            '#detalle-unidades-tabla-wrapper'
        );


    const body =
        modal.querySelector(
            '#detalle-unidades-body'
        );


    if (
        !vacio
        || !sinUnidad
        || !wrapper
        || !body
    ) {
        return;
    }


    body.innerHTML =
        '';


    const modalidad =
        String(
            modalidadUnidad
            || 'CON_UNIDAD'
        )
            .trim()
            .toUpperCase();


    /* =====================================================
       SIN UNIDAD / OFICINA
    ===================================================== */

    if (
        modalidad ===
        'SIN_UNIDAD_OFICINA'
    ) {

        sinUnidad.hidden =
            false;

        vacio.hidden =
            true;

        wrapper.hidden =
            true;

        return;
    }


    /* =====================================================
       CON UNIDAD
    ===================================================== */

    sinUnidad.hidden =
        true;


    if (
        !Array.isArray(unidades)
        || unidades.length === 0
    ) {

        vacio.hidden =
            false;

        wrapper.hidden =
            true;

        return;
    }


    unidades.forEach(
        (unidad) => {

            const fila =
                document.createElement(
                    'tr'
                );


            const noEconomico =
                mayusculas(
                    unidad.no_economico
                );


            const placas =
                mayusculas(
                    unidad.placas
                );


            const marca =
                mayusculas(
                    unidad.marca
                );


            const submarca =
                mayusculas(
                    unidad.submarca
                );


            const color =
                mayusculas(
                    unidad.color
                );


            const estatus =
                mayusculas(
                    unidad.estatus
                );


            const servicio =
                mayusculas(
                    unidad.servicio
                );


            const tipo =
                mayusculas(
                    unidad.tipo
                );


            const marcaSubmarca =
                [
                    marca,
                    submarca,
                ]
                    .filter(Boolean)
                    .join(' ');


            fila.innerHTML = `

                <td>

                    <strong>
                        ${escaparHtmlDetalle(
                            noEconomico || '—'
                        )}
                    </strong>

                    <small class="detalle-unidades__placas">
                        Placas:
                        ${escaparHtmlDetalle(
                            placas || '—'
                        )}
                    </small>

                </td>

                <td>
                    ${escaparHtmlDetalle(
                        marcaSubmarca || '—'
                    )}
                </td>

                <td>
                    ${escaparHtmlDetalle(
                        color || '—'
                    )}
                </td>

                <td>
                    ${escaparHtmlDetalle(
                        estatus || '—'
                    )}
                </td>

                <td>
                    ${escaparHtmlDetalle(
                        servicio || '—'
                    )}
                </td>

                <td>
                    ${escaparHtmlDetalle(
                        tipo || '—'
                    )}
                </td>
            `;


            body.appendChild(
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
   EVIDENCIAS
========================================================= */

function renderizarEvidenciasDetalle(
    modal,
    evidencias
) {

    const lista =
        modal.querySelector(
            '#detalle-evidencia-lista'
        );


    if (!lista) {
        return;
    }


    lista.innerHTML =
        '';


    if (
        !Array.isArray(evidencias)
        || evidencias.length === 0
    ) {

        limpiarEvidenciaDetalle(
            modal
        );

        return;
    }


    /*   const baseUrl =
          document
              .querySelector('base')
              ?.href
          || `${window.location.origin}/`;
   */

    evidencias.forEach(
        (evidencia, indice) => {

            const idEvidencia =
                Number(
                    evidencia.id_evidencia
                    || 0
                );


            if (
                !Number.isInteger(idEvidencia)
                || idEvidencia <= 0
            ) {
                return;
            }


            const nombre =
                String(
                    evidencia.nombre_original
                    || evidencia.nombre_archivo
                    || `Evidencia ${indice + 1}`
                ).trim();


            const tipo =
                String(
                    evidencia.mime_type
                    || evidencia.extension
                    || 'Imagen'
                ).trim();


            /* const urlImagen =
                new URL(
                    `asuntos-internos/reportes/evidencia/${idEvidencia}`,
                    baseUrl
                ).toString(); */

            const urlImagen =
                new URL(
                    `DataCore/public/asuntos-internos/reportes/evidencia/${idEvidencia}`,
                    `${window.location.origin}/`
                ).toString();


            const item =
                document.createElement(
                    'div'
                );


            item.className =
                'detalle-evidencia__item';


            item.innerHTML = `

                <button
                    type="button"
                    class="detalle-evidencia__preview"
                    title="Ver evidencia"
                >

                    <img
                        src="${escaparHtmlDetalle(urlImagen)}"
                        alt="${escaparHtmlDetalle(nombre)}"
                        loading="lazy"
                    >

                </button>


                <div class="detalle-evidencia__contenido">

                    <strong
                        class="detalle-evidencia__nombre"
                        title="${escaparHtmlDetalle(nombre)}"
                    >
                        ${escaparHtmlDetalle(nombre)}
                    </strong>


                    <span class="detalle-evidencia__tipo">
                        ${escaparHtmlDetalle(tipo)}
                    </span>


                    <button
                        type="button"
                        class="detalle-evidencia__ver"
                    >
                        Ver imagen
                    </button>

                </div>


                <div class="detalle-evidencia__numero">
                    ${String(indice + 1).padStart(2, '0')}
                </div>

            `;


            const botonPreview =
                item.querySelector(
                    '.detalle-evidencia__preview'
                );


            const botonVer =
                item.querySelector(
                    '.detalle-evidencia__ver'
                );


            const abrirImagen = () => {

                window.open(
                    urlImagen,
                    '_blank',
                    'noopener,noreferrer'
                );

            };


            if (botonPreview) {

                botonPreview.addEventListener(
                    'click',
                    abrirImagen
                );
            }


            if (botonVer) {

                botonVer.addEventListener(
                    'click',
                    abrirImagen
                );
            }


            lista.appendChild(
                item
            );

        }
    );

}


/* =========================================================
   LIMPIAR EVIDENCIA
========================================================= */

function limpiarEvidenciaDetalle(
    modal
) {

    const lista =
        modal.querySelector(
            '#detalle-evidencia-lista'
        );


    if (!lista) {
        return;
    }


    lista.innerHTML = `

        <div class="detalle-evidencia__vacio">

            <div class="detalle-evidencia__vacio-icono">
                +
            </div>

            <div>

                <strong>
                    Sin evidencia
                </strong>

                <span>
                    No hay evidencia fotográfica registrada.
                </span>

            </div>

        </div>

    `;

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
   FECHA
========================================================= */

function formatearFechaDetalle(
    valor
) {

    const fecha =
        String(
            valor || ''
        ).trim();


    if (!fecha) {
        return '';
    }


    const coincidencia =
        fecha.match(
            /^(\d{4})-(\d{2})-(\d{2})$/
        );


    if (!coincidencia) {
        return fecha;
    }


    return `${coincidencia[3]}/${coincidencia[2]}/${coincidencia[1]}`;

}


/* =========================================================
   HORA
========================================================= */

function formatearHoraDetalle(
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
        ? hora.substring(0, 5)
        : hora;

}


/* =========================================================
   FOLIO
========================================================= */

function obtenerPrefijoFolio(
    folio
) {

    const texto =
        String(
            folio || ''
        ).trim();


    if (!texto) {
        return 'QJ';
    }


    const partes =
        texto.split('-');


    return partes.length > 1
        ? partes[0]
        : 'QJ';

}


function obtenerNumeroFolio(
    folio
) {

    const texto =
        String(
            folio || ''
        ).trim();


    if (!texto) {
        return '';
    }


    const partes =
        texto.split('-');


    return partes.length > 1
        ? partes.slice(1).join('-')
        : texto;

}


/* =========================================================
   MAYÚSCULAS
========================================================= */

function mayusculas(
    valor
) {

    return String(
        valor ?? ''
    )
        .trim()
        .toUpperCase();

}


/* =========================================================
   ASIGNAR TEXTO
========================================================= */

function asignarTextoDetalle(
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
            valor ?? ''
        ).trim();


    elemento.textContent =
        texto || '—';

}


/* =========================================================
   ESCAPAR HTML
========================================================= */

function escaparHtmlDetalle(
    valor
) {

    return String(
        valor ?? ''
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