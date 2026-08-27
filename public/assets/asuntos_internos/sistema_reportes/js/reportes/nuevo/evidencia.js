/* =========================================================
   SISTEMA DE REPORTES - ASUNTOS INTERNOS
   Nuevo reporte - Evidencia fotográfica
========================================================= */

document.addEventListener('DOMContentLoaded', () => {
    inicializarEvidenciaFotografica();
});


function inicializarEvidenciaFotografica() {

    const input =
        document.querySelector(
            '#evidencia_fotografica'
        );

    const lista =
        document.querySelector(
            '#evidencia-lista-archivos'
        );


    if (!input || !lista) {
        return;
    }


    /*
     * Aquí conservamos TODOS los archivos
     * que el usuario vaya agregando.
     */
    let archivosSeleccionados = [];


    /* =====================================================
       SELECCIONAR ARCHIVOS
    ===================================================== */

    input.addEventListener('change', () => {

        const nuevosArchivos =
            Array.from(
                input.files || []
            );


        if (nuevosArchivos.length === 0) {
            return;
        }


        /*
         * Agregamos únicamente archivos
         * que todavía no estén seleccionados.
         */
        nuevosArchivos.forEach((archivo) => {

            const yaExiste =
                archivosSeleccionados.some(
                    (existente) =>
                        archivoEsIgual(
                            existente,
                            archivo
                        )
                );


            if (!yaExiste) {

                archivosSeleccionados.push(
                    archivo
                );

            }

        });


        /*
         * Actualizamos el FileList real
         * del input.
         */
        sincronizarInputArchivos(
            input,
            archivosSeleccionados
        );


        /*
         * Pintamos nuevamente la lista.
         */
        renderizarArchivos(
            lista,
            archivosSeleccionados,
            (indice) => {

                eliminarArchivo(
                    indice
                );

            }
        );

    });


    /* =====================================================
       ELIMINAR ARCHIVO
    ===================================================== */

    function eliminarArchivo(
        indice
    ) {

        archivosSeleccionados.splice(
            indice,
            1
        );


        sincronizarInputArchivos(
            input,
            archivosSeleccionados
        );


        renderizarArchivos(
            lista,
            archivosSeleccionados,
            eliminarArchivo
        );

    }

}


/* =========================================================
   COMPARAR ARCHIVOS

   Evita agregar dos veces exactamente
   el mismo archivo.
========================================================= */

function archivoEsIgual(
    archivoA,
    archivoB
) {

    return (
        archivoA.name === archivoB.name
        && archivoA.size === archivoB.size
        && archivoA.lastModified
        === archivoB.lastModified
    );

}


/* =========================================================
   SINCRONIZAR INPUT

   Reconstruimos input.files con todos
   los archivos acumulados.
========================================================= */

function sincronizarInputArchivos(
    input,
    archivos
) {

    const transferencia =
        new DataTransfer();


    archivos.forEach((archivo) => {

        transferencia.items.add(
            archivo
        );

    });


    input.files =
        transferencia.files;

}


/* =========================================================
   RENDERIZAR LISTA
========================================================= */

function renderizarArchivos(
    lista,
    archivos,
    onEliminar
) {

    lista.innerHTML = '';


    if (archivos.length === 0) {

        return;

    }


    archivos.forEach(
        (archivo, indice) => {

            const elemento =
                document.createElement(
                    'div'
                );


            elemento.className =
                'report-evidence__file';


            /* =============================================
               INFORMACIÓN
            ============================================= */

            const informacion =
                document.createElement(
                    'div'
                );


            informacion.className =
                'report-evidence__file-info';


            const nombre =
                document.createElement(
                    'strong'
                );


            nombre.textContent =
                archivo.name;


            const peso =
                document.createElement(
                    'span'
                );


            peso.textContent =
                formatearPesoArchivo(
                    archivo.size
                );


            informacion.appendChild(
                nombre
            );

            informacion.appendChild(
                peso
            );


            /* =============================================
               BOTÓN ELIMINAR
            ============================================= */

            const botonEliminar =
                document.createElement(
                    'button'
                );


            botonEliminar.type =
                'button';


            botonEliminar.className =
                'report-evidence__remove';


            botonEliminar.setAttribute(
                'aria-label',
                `Eliminar ${archivo.name}`
            );


            botonEliminar.innerHTML =
                '&times;';


            botonEliminar.addEventListener(
                'click',
                () => {

                    onEliminar(
                        indice
                    );

                }
            );


            /* =============================================
               ARMAR
            ============================================= */

            elemento.appendChild(
                informacion
            );

            elemento.appendChild(
                botonEliminar
            );


            lista.appendChild(
                elemento
            );

        }
    );

}


/* =========================================================
   FORMATEAR PESO
========================================================= */

function formatearPesoArchivo(
    bytes
) {

    if (!bytes) {
        return '0 KB';
    }


    const kilobytes =
        bytes / 1024;


    if (kilobytes < 1024) {

        return (
            `${kilobytes.toFixed(1)} KB`
        );

    }


    const megabytes =
        kilobytes / 1024;


    return (
        `${megabytes.toFixed(1)} MB`
    );

}