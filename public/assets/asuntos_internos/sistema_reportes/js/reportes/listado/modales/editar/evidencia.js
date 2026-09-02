/* =========================================================
   SISTEMA DE REPORTES - ASUNTOS INTERNOS
   Editar reporte - Evidencia
========================================================= */


/* =========================================================
   ESTADO LOCAL
========================================================= */

let evidenciasExistentes = [];

let evidenciasEliminadas = new Set();


/* =========================================================
   INICIALIZAR
========================================================= */

export function inicializarEditarEvidencia(
    modal,
    formulario
) {

    if (
        !modal
        || !formulario
    ) {
        return;
    }


    const input =
        formulario.querySelector(
            '#editar-evidencia-fotografica'
        );


    if (!input) {
        return;
    }


    input.addEventListener(
        'change',
        () => {

            mostrarEvidenciaNueva(
                modal,
                input.files
            );

        }
    );

}


/* =========================================================
   EVIDENCIA EXISTENTE
========================================================= */

export function mostrarEvidenciaExistente(
    modal,
    evidencias
) {

    const contenedor =
        modal.querySelector(
            '#editar-evidencia-existente'
        );


    if (!contenedor) {
        return;
    }


    /*
     * Reiniciamos estado cada vez que se carga
     * un reporte distinto.
     */

    evidenciasExistentes =
        Array.isArray(
            evidencias
        )
            ? evidencias.map(
                (evidencia) => ({
                    ...evidencia,
                })
            )
            : [];


    evidenciasEliminadas =
        new Set();


    renderizarEvidenciasExistentes(
        modal
    );

}


/* =========================================================
   RENDERIZAR EVIDENCIAS EXISTENTES
========================================================= */

function renderizarEvidenciasExistentes(
    modal
) {

    const contenedor =
        modal.querySelector(
            '#editar-evidencia-existente'
        );


    if (!contenedor) {
        return;
    }


    contenedor.innerHTML =
        '';


    const visibles =
        evidenciasExistentes.filter(
            (evidencia) => {

                const id =
                    Number(
                        evidencia.id_evidencia
                        || 0
                    );


                return !evidenciasEliminadas.has(
                    id
                );

            }
        );


    if (
        visibles.length === 0
    ) {

        contenedor.innerHTML = `
            <div class="editar-evidencia__vacio">
                Sin evidencia registrada
            </div>
        `;


        return;
    }


    /* const baseUrl =
        document
            .querySelector('base')
            ?.href
        || `${window.location.origin}/`; */


    visibles.forEach(
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
                'editar-evidencia__item editar-evidencia__item--existente';


            item.innerHTML = `

                <button
                    type="button"
                    class="editar-evidencia__preview"
                    title="Ver imagen"
                >

                    <img
                        src="${escaparHtmlEvidencia(urlImagen)}"
                        alt="${escaparHtmlEvidencia(nombre)}"
                        loading="lazy"
                    >

                </button>


                <div class="editar-evidencia__contenido">

                    <strong
                        class="editar-evidencia__nombre"
                        title="${escaparHtmlEvidencia(nombre)}"
                    >
                        ${escaparHtmlEvidencia(nombre)}
                    </strong>


                    <span class="editar-evidencia__tipo">
                        ${escaparHtmlEvidencia(tipo)}
                    </span>


                    <div class="editar-evidencia__acciones">

                        <button
                            type="button"
                            class="editar-evidencia__accion editar-evidencia__accion--ver"
                        >
                            Ver imagen
                        </button>


                        <button
                            type="button"
                            class="editar-evidencia__accion editar-evidencia__accion--quitar"
                            data-id-evidencia="${idEvidencia}"
                        >
                            Quitar
                        </button>

                    </div>

                </div>

            `;


            const botonPreview =
                item.querySelector(
                    '.editar-evidencia__preview'
                );


            const botonVer =
                item.querySelector(
                    '.editar-evidencia__accion--ver'
                );


            const botonQuitar =
                item.querySelector(
                    '.editar-evidencia__accion--quitar'
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


            if (botonQuitar) {

                botonQuitar.addEventListener(
                    'click',
                    () => {

                        marcarEvidenciaParaEliminar(
                            modal,
                            idEvidencia
                        );

                    }
                );

            }


            contenedor.appendChild(
                item
            );

        }
    );

}


/* =========================================================
   MARCAR EVIDENCIA PARA ELIMINAR
========================================================= */

function marcarEvidenciaParaEliminar(
    modal,
    idEvidencia
) {

    if (
        !Number.isInteger(idEvidencia)
        || idEvidencia <= 0
    ) {
        return;
    }


    evidenciasEliminadas.add(
        idEvidencia
    );


    renderizarEvidenciasExistentes(
        modal
    );

}


/* =========================================================
   EVIDENCIAS A ELIMINAR
========================================================= */

export function obtenerEvidenciasEliminadas() {

    return Array.from(
        evidenciasEliminadas
    );

}


/* =========================================================
   EVIDENCIAS EXISTENTES CONSERVADAS
========================================================= */

export function obtenerEvidenciasConservadas() {

    return evidenciasExistentes.filter(
        (evidencia) => {

            const id =
                Number(
                    evidencia.id_evidencia
                    || 0
                );


            return !evidenciasEliminadas.has(
                id
            );

        }
    );

}


/* =========================================================
   EVIDENCIA NUEVA
========================================================= */

export function mostrarEvidenciaNueva(
    modal,
    archivos
) {

    const contenedor =
        modal.querySelector(
            '#editar-evidencia-nueva'
        );


    if (!contenedor) {
        return;
    }


    contenedor.innerHTML =
        '';


    const lista =
        Array.from(
            archivos || []
        );


    if (
        lista.length === 0
    ) {

        contenedor.innerHTML = `
            <div class="editar-evidencia__vacio">
                No se han seleccionado archivos nuevos
            </div>
        `;


        return;
    }


    lista.forEach(
        (archivo, indice) => {

            const item =
                document.createElement(
                    'div'
                );


            item.className =
                'editar-evidencia__item editar-evidencia__item--nueva';


            const urlTemporal =
                URL.createObjectURL(
                    archivo
                );


            const nombre =
                archivo.name
                || `Imagen ${indice + 1}`;


            const tipo =
                archivo.type
                || 'Imagen';


            item.innerHTML = `

                <button
                    type="button"
                    class="editar-evidencia__preview"
                    title="Ver imagen"
                >

                    <img
                        src="${escaparHtmlEvidencia(urlTemporal)}"
                        alt="${escaparHtmlEvidencia(nombre)}"
                    >

                </button>


                <div class="editar-evidencia__contenido">

                    <strong
                        class="editar-evidencia__nombre"
                        title="${escaparHtmlEvidencia(nombre)}"
                    >
                        ${escaparHtmlEvidencia(nombre)}
                    </strong>


                    <span class="editar-evidencia__tipo">
                        ${escaparHtmlEvidencia(tipo)}
                    </span>


                    <span class="editar-evidencia__nueva-etiqueta">
                        Nueva
                    </span>

                </div>

            `;


            const botonPreview =
                item.querySelector(
                    '.editar-evidencia__preview'
                );


            if (botonPreview) {

                botonPreview.addEventListener(
                    'click',
                    () => {

                        window.open(
                            urlTemporal,
                            '_blank',
                            'noopener,noreferrer'
                        );

                    }
                );

            }


            contenedor.appendChild(
                item
            );

        }
    );

}


/* =========================================================
   OBTENER NUEVAS EVIDENCIAS
========================================================= */

export function obtenerNuevasEvidencias(
    datos
) {

    if (
        !(datos instanceof FormData)
    ) {
        return [];
    }


    const archivos =
        datos.getAll(
            'evidencia_fotografica[]'
        );


    return archivos
        .filter(
            (archivo) => {

                return (
                    archivo instanceof File
                    && archivo.size > 0
                );

            }
        )
        .map(
            (archivo) => ({

                nombre:
                    archivo.name,

                archivo:
                    archivo.name,

                mime_type:
                    archivo.type,

                tamano_bytes:
                    archivo.size,

                temporal:
                    true,

            })
        );

}


/* =========================================================
   LIMPIAR EVIDENCIA NUEVA
========================================================= */

export function limpiarEvidenciaNueva(
    modal,
    formulario = null
) {

    const input =
        formulario
            ?.querySelector(
                '#editar-evidencia-fotografica'
            )
        || modal.querySelector(
            '#editar-evidencia-fotografica'
        );


    if (input) {

        input.value =
            '';

    }


    mostrarEvidenciaNueva(
        modal,
        []
    );

}


/* =========================================================
   LIMPIAR ESTADO
========================================================= */

export function limpiarEstadoEvidencias() {

    evidenciasExistentes =
        [];


    evidenciasEliminadas =
        new Set();

}


/* =========================================================
   ESCAPAR HTML
========================================================= */

function escaparHtmlEvidencia(
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