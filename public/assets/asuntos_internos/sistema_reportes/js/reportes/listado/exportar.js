/* =========================================================
   SISTEMA DE REPORTES - ASUNTOS INTERNOS
   Listado - Exportar Excel completo
========================================================= */

document.addEventListener(
    'DOMContentLoaded',
    () => {

        inicializarExportacionListado();

    }
);


/* =========================================================
   INICIALIZAR
========================================================= */

function inicializarExportacionListado() {

    const boton =
        document.querySelector(
            '#btn-exportar-reportes'
        );


    if (!boton) {
        return;
    }


    boton.addEventListener(
        'click',
        async () => {

            const textoOriginal =
                boton.textContent;


            try {

                /* =================================================
                   ESTADO DEL BOTÓN
                ================================================= */

                boton.disabled =
                    true;


                boton.textContent =
                    'Generando...';


                /* =================================================
                   SOLICITUD
                ================================================= */

                const respuesta =
                    await fetch(
                        construirUrlExportacion(),
                        {
                            method:
                                'POST',

                            headers: {
                                Accept:
                                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/json',
                            },

                            credentials:
                                'same-origin',
                        }
                    );


                /* =================================================
                   ERROR
                ================================================= */

                if (!respuesta.ok) {

                    let mensaje =
                        'No fue posible generar el archivo de Excel.';


                    try {

                        const resultado =
                            await respuesta.json();


                        if (
                            resultado?.message
                        ) {

                            mensaje =
                                resultado.message;

                        }

                    } catch (error) {

                        /*
                         * La respuesta no era JSON.
                         */

                    }


                    throw new Error(
                        mensaje
                    );

                }


                /* =================================================
                   ARCHIVO
                ================================================= */

                const archivo =
                    await respuesta.blob();


                if (
                    !archivo
                    || archivo.size === 0
                ) {

                    throw new Error(
                        'El archivo generado está vacío.'
                    );

                }


                const disposition =
                    respuesta.headers.get(
                        'Content-Disposition'
                    );


                const nombre =
                    obtenerNombreArchivo(
                        disposition
                    );


                descargarArchivo(
                    archivo,
                    nombre
                );


            } catch (error) {

                console.error(
                    'Error exportando listado:',
                    error
                );


                window.alert(
                    error.message
                    || 'No fue posible generar el archivo de Excel.'
                );


            } finally {

                /* =================================================
                   RESTAURAR BOTÓN
                ================================================= */

                boton.disabled =
                    false;


                boton.textContent =
                    textoOriginal;

            }

        }
    );

}


/* =========================================================
   URL DE EXPORTACIÓN
========================================================= */

function construirUrlExportacion() {

    const base =
        document
            .querySelector(
                'base'
            )
            ?.href;


    if (base) {

        return new URL(
            'asuntos-internos/reportes/listado/exportar',
            base
        ).toString();

    }


    return (
        `${window.location.origin}`
        + '/asuntos-internos/reportes/listado/exportar'
    );

}


/* =========================================================
   DESCARGAR ARCHIVO
========================================================= */

function descargarArchivo(
    archivo,
    nombre
) {

    const url =
        window.URL.createObjectURL(
            archivo
        );


    const enlace =
        document.createElement(
            'a'
        );


    enlace.href =
        url;


    enlace.download =
        nombre;


    document.body.appendChild(
        enlace
    );


    enlace.click();


    enlace.remove();


    window.URL.revokeObjectURL(
        url
    );

}


/* =========================================================
   OBTENER NOMBRE DEL ARCHIVO
========================================================= */

function obtenerNombreArchivo(
    contentDisposition
) {

    const predeterminado =
        'reportes_asuntos_internos.xlsx';


    if (!contentDisposition) {
        return predeterminado;
    }


    /*
     * filename*=UTF-8''archivo.xlsx
     */
    const utf8 =
        contentDisposition.match(
            /filename\*=UTF-8''([^;]+)/i
        );


    if (
        utf8
        && utf8[1]
    ) {

        try {

            return decodeURIComponent(
                utf8[1]
            );

        } catch (error) {

            return utf8[1];

        }

    }


    /*
     * filename="archivo.xlsx"
     */
    const normal =
        contentDisposition.match(
            /filename="?([^";]+)"?/i
        );


    if (
        normal
        && normal[1]
    ) {

        return normal[1]
            .trim();

    }


    return predeterminado;

}